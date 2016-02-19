<?php
/**
 * Created by PhpStorm.
 * User: dev
 * Date: 14/02/2016
 * Time: 17:14
 */

namespace MacFJA\PhpKVO\Test\Spl;


use MacFJA\PhpKVO\Spl\Observer;
use MacFJA\PhpKVO\Spl\Subject;
use MacFJA\PhpKVO\Test\fixtures\NullListener;
use MacFJA\PhpKVO\Test\fixtures\NullObservable;

class ObserverTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider dataProvider
     *
     * @param $expected
     * @param $input
     */
    public function testOptions($expected, $input)
    {
        $observer = new Observer(new NullListener(), '', $input);

        self::assertEquals($expected, $observer->getOptions());
    }

    /**
     * @dataProvider dataProvider
     *
     * @param $expected
     * @param $input
     */
    public function testKey($expected, $input)
    {
        $observer = new Observer(new NullListener(), $input);

        self::assertEquals($expected, $observer->getKey());
    }

    public function testUpdate()
    {
        $subject = new Subject(new NullObservable());

        $observer = new Observer(new NullListener(), 'key');
        $observer->update($subject);

        $observer = new Observer(new NullListener(), 'key', Observer::OPTION_NEW);
        $observer->update($subject);

        $subject->setPrior(false);
        $observer->update($subject);

        $observer = new Observer(new NullListener(), 'key', Observer::OPTION_NEW|Observer::OPTION_OLD);
        $observer->update($subject);
    }

    public function dataProvider($name)
    {
        if ($name === 'testOptions') {
            $new     = Observer::OPTION_NEW;
            $old     = Observer::OPTION_OLD;
            $initial = Observer::OPTION_INITIAL;
            $prior   = Observer::OPTION_PRIOR;
            return array(
                array(0,  0),
                array(1,  $new),
                array(2,  $old),
                array(3,  $new|$old),
                array(4,  $initial),
                array(5,  $initial|$new),
                array(6,  $initial|$old),
                array(7,  $initial|$new|$old),
                array(8,  $prior),
                array(9,  $prior|$new),
                array(10, $prior|$old),
                array(11, $prior|$new|$old),
                array(12, $prior|$initial),
                array(13, $prior|$initial|$new),
                array(14, $prior|$initial|$old),
                array(15, $prior|$initial|$new|$old),
            );
        } elseif ($name === 'testKey') {
            $keys = array('key', 'key.option', 'lower', 'UPPER', 'CamelCase');
            $dataset = array();

            foreach ($keys as $key) {
                $dataset[] = array($key, $key);
            }
            return $dataset;
        }

        return array();
    }
}
