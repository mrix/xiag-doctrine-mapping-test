<?php
namespace Xiag\DoctrineMappingTest\Entity;

/**
 */
class ImageFile
{
    /**
     * @var string
     */
    private $ref;

    /**
     * @return string
     */
    public function getRef()
    {
        return $this->ref;
    }

    /**
     * @param string $ref
     * @return $this
     */
    public function setRef($ref)
    {
        $this->ref = $ref;
        return $this;
    }
}
