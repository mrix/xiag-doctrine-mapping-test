<?php
namespace Xiag\DoctrineMappingTest\Entity;

/**
 */
class ImageOffset
{
    /**
     * @var int
     */
    private $top;
    /**
     * @var int
     */
    private $right;
    /**
     * @var int
     */
    private $bottom;
    /**
     * @var int
     */
    private $left;

    /**
     * @return int
     */
    public function getTop()
    {
        return $this->top;
    }

    /**
     * @param int $top
     * @return $this
     */
    public function setTop($top)
    {
        $this->top = $top;
        return $this;
    }

    /**
     * @return int
     */
    public function getRight()
    {
        return $this->right;
    }

    /**
     * @param int $right
     * @return $this
     */
    public function setRight($right)
    {
        $this->right = $right;
        return $this;
    }

    /**
     * @return int
     */
    public function getBottom()
    {
        return $this->bottom;
    }

    /**
     * @param int $bottom
     * @return $this
     */
    public function setBottom($bottom)
    {
        $this->bottom = $bottom;
        return $this;
    }

    /**
     * @return int
     */
    public function getLeft()
    {
        return $this->left;
    }

    /**
     * @param int $left
     * @return $this
     */
    public function setLeft($left)
    {
        $this->left = $left;
        return $this;
    }
}
