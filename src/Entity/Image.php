<?php
namespace Xiag\DoctrineMappingTest\Entity;

use Doctrine\Common\Collections\ArrayCollection;

/**
 */
class Image
{
    /**
     * @var string
     */
    private $id;
    /**
     * @var ImageOffset
     */
    private $offset;
    /**
     * @var ImageFile[]
     */
    private $files;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->files = new ArrayCollection();
    }

    /**
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param string $id
     * @return $this
     */
    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return ImageOffset
     */
    public function getOffset()
    {
        return $this->offset;
    }

    /**
     * @param ImageOffset $offset
     * @return $this
     */
    public function setOffset($offset)
    {
        $this->offset = $offset;
        return $this;
    }

    /**
     * @return ImageFile[]
     */
    public function getFiles()
    {
        return $this->files;
    }

    /**
     * @param ImageFile[] $files
     * @return $this
     */
    public function setFiles($files)
    {
        $this->files = $files;
        return $this;
    }
}
