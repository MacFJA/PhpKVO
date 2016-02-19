<?php
/**
 * Created by PhpStorm.
 * User: dev
 * Date: 14/02/2016
 * Time: 19:06
 */

namespace MacFJA\PhpKVO\Test\fixtures;


class NonKvo
{
    protected $property;
    protected $xPosition;
    protected $y;

    /**
     * @return mixed
     */
    public function getY()
    {
        return $this->y;
    }

    /**
     * @param mixed $y
     */
    public function setY($y)
    {
        $this->y = $y;
    }

    /**
     * @return mixed
     */
    public function getxPosition()
    {
        return $this->xPosition;
    }

    /**
     * @param mixed $xPosition
     */
    public function setxPosition($xPosition)
    {
        $this->xPosition = $xPosition;
    }
    public $public;

    /**
     * @return mixed
     */
    public function getProperty()
    {
        return $this->property;
    }

    /**
     * @param mixed $property
     */
    public function setProperty($property)
    {
        $this->property = $property;
    }


}