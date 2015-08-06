<?php
namespace Xiag\DoctrineMappingTest\Command;

use Doctrine\MongoDB\Connection;
use Doctrine\ODM\MongoDB\Configuration;
use Doctrine\ODM\MongoDB\DocumentManager;
use Doctrine\ODM\MongoDB\Mapping\Driver\SimplifiedXmlDriver;
use JMS\Serializer\SerializerBuilder;
use JMS\Serializer\SerializerInterface;
use JMS\Serializer\Naming\IdenticalPropertyNamingStrategy;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Helper\TableCell;
use Symfony\Component\Console\Helper\TableSeparator;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Xiag\DoctrineMappingTest\Entity;

/**
 */
class TestCommand extends Command
{
    /**
     * @var SerializerInterface
     */
    private $serializer;
    /**
     * @var DocumentManager
     */
    private $entityManager;
    /**
     * @var array
     */
    private $mongoLogs = [];

    /**
     * @inheritdoc
     */
    protected function configure()
    {
        $this->setName('xiag:test');
    }

    /**
     * @inheritdoc
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->serializer = $this->createSerializer();
        $this->entityManager = $this->createEntityManager();

        // document
        $document = (new Entity\Question())
            ->setId('q1')
            ->setText('question with image document')
            ->setImage(
                (new Entity\Image())
                    ->setId('document')
                    ->setOffset(
                        (new Entity\ImageOffset())
                            ->setTop(200)
                            ->setRight(200)
                            ->setBottom(200)
                            ->setLeft(200)
                    )
                    ->setFiles([
                        (new Entity\ImageFile())->setRef('file-1'),
                        (new Entity\ImageFile())->setRef('file-2'),
                        (new Entity\ImageFile())->setRef('file-3'),
                    ])
            );
        $this->entityManager->persist($document);


        // embedded
        $embedded = (new Entity\Question())
            ->setId('q2')
            ->setText('question with image embedded')
            ->setImage(
                (new Entity\QuestionImage())
                    ->setId('embedded')
                    ->setOffset(
                        (new Entity\ImageOffset())
                            ->setTop(300)
                            ->setRight(300)
                            ->setBottom(300)
                            ->setLeft(300)
                    )
                    ->setFiles([
                        (new Entity\ImageFile())->setRef('file-4'),
                        (new Entity\ImageFile())->setRef('file-5'),
                        (new Entity\ImageFile())->setRef('file-6'),
                    ])
            );
        $this->entityManager->persist($embedded);


        $table = new Table($output);
        $table->setHeaders([
            'document',
            'embedded',
        ]);

        // before flush
        $table->addRow([new TableCell('new', ['colspan' => 2])]);
        $table->addRow(new TableSeparator());
        $table->addRow([
            $this->serializeData($document),
            $this->serializeData($embedded),
        ]);
        $table->addRow(new TableSeparator());


        // after flush
        $this->entityManager->flush();

        $table->addRow([new TableCell('saved', ['colspan' => 2])]);
        $table->addRow(new TableSeparator());
        $table->addRow([
            $this->serializeData($document),
            $this->serializeData($embedded),
        ]);
        $table->addRow(new TableSeparator());


        // reloaded (no real db query)
        $document = $this->entityManager->getRepository(Entity\Question::class)->find($document->getId());
        $embedded = $this->entityManager->getRepository(Entity\Question::class)->find($embedded->getId());

        $table->addRow([new TableCell('reloaded', ['colspan' => 2])]);
        $table->addRow(new TableSeparator());
        $table->addRow([
            $this->serializeData($document),
            $this->serializeData($embedded),
        ]);
        $table->addRow(new TableSeparator());


        // detach documents
        $this->entityManager->detach($document);
        $this->entityManager->detach($embedded);

        $table->addRow([new TableCell('detached', ['colspan' => 2])]);
        $table->addRow(new TableSeparator());
        $table->addRow([
            $this->serializeData($document),
            $this->serializeData($embedded),
        ]);
        $table->addRow(new TableSeparator());


        // real reload
        $document = $this->entityManager->getRepository(Entity\Question::class)->find($document->getId());
        $embedded = $this->entityManager->getRepository(Entity\Question::class)->find($embedded->getId());

        $table->addRow([new TableCell('real reloaded', ['colspan' => 2])]);
        $table->addRow(new TableSeparator());
        $table->addRow([
            $this->serializeData($document),
            $this->serializeData($embedded),
        ]);

        $table->render();

        $table = new Table($output);
        $table->setHeaders(['MongoDb query']);
        foreach ($this->mongoLogs as $query) {
            $table->addRow([$this->serializeData($query)]);
            $table->addRow(new TableSeparator());
        }
        $table->render();
    }

    /**
     * @return SerializerInterface
     */
    private function createSerializer()
    {
        return SerializerBuilder::create()
            ->addDefaultHandlers()
            ->setPropertyNamingStrategy(new IdenticalPropertyNamingStrategy())
            ->addDefaultSerializationVisitors()
            ->addDefaultDeserializationVisitors()
            ->addMetadataDir(__DIR__ . '/../Resources/config/serializer', 'Xiag\DoctrineMappingTest\Entity')
            ->setCacheDir(sys_get_temp_dir())
            ->setDebug(true)
            ->build();
    }

    /**
     * @return DocumentManager
     */
    private function createEntityManager()
    {
        $config = new Configuration();
        $config->setProxyDir(sys_get_temp_dir());
        $config->setHydratorDir(sys_get_temp_dir());
        $config->setProxyNamespace('__Xiag__\DoctrineMappingTest\Proxy');
        $config->setHydratorNamespace('__Xiag__\DoctrineMappingTest\Hydrator');
        $config->setMetadataDriverImpl(
            new SimplifiedXmlDriver(
                [realpath(__DIR__.'/../Resources/config/doctrine') => 'Xiag\DoctrineMappingTest\Entity'],
                '.mongodb.xml'
            )
        );
        $config->setLoggerCallable(function ($query) {
            $this->mongoLogs[] = $query;
        });

        return DocumentManager::create(new Connection(null, [], $config), $config);
    }

    /**
     * @param mixed $data
     * @return string
     */
    private function serializeData($data)
    {
        $json = $this->serializer->serialize($data, 'json');
        return json_encode(json_decode($json), JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
    }
}
