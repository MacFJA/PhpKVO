<?php
/**
 * Created by PhpStorm.
 * User: dev
 * Date: 14/02/2016
 * Time: 22:38
 */

namespace MacFJA\PhpKVO\Test\fixtures;


use MacFJA\PhpKVO\AbstractObservable;
use MacFJA\PhpKVO\Spl\Observer;

class KVOClass extends AbstractObservable
{
    protected $value = 10;
    protected $progress = 2;
    protected $protected = 9;

    /**
     * @return int
     */
    public function getProtected()
    {
        return $this->protected;
    }

    /**
     * @return int
     */
    public function getProgress()
    {
        return $this->progress;
    }

    /**
     * @param int $progress
     */
    public function setProgress($progress)
    {
        $this->progress = $progress;
    }

    public function doSomething()
    {
        $this->willChangeValueForKey('value', Observer::SOURCE_CUSTOM);
        $this->value = 15;
        $this->didChangeValueForKey('value', Observer::SOURCE_CUSTOM);
    }

}