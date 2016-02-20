<?php

namespace MacFJA\PhpKVO\Test;

use MacFJA\PhpKVO\Proxy;
use MacFJA\PhpKVO\Spl\Observer;
use MacFJA\PhpKVO\Test\fixtures\KVOClass;
use MacFJA\PhpKVO\Test\fixtures\NonKvo;

class KVOTest extends \PHPUnit_Framework_TestCase
{
    public function testProxy()
    {
        $object = new NonKvo();
        $object->setProperty(10);

        self::assertEquals(10, $object->getProperty());

        $proxy = new Proxy($object);

        $mock = self::getMockBuilder('MacFJA\PhpKVO\Listener')
            ->disableOriginalConstructor()
            ->getMock();

        $mock->expects(self::once())->method('observeValueForKeyPath')
            ->with('property', $proxy, array(
                Observer::CHANGE_PRIOR => false,
                Observer::CHANGE_SOURCE => Observer::SOURCE_SETTER,
                Observer::CHANGE_NEW => 20,
                Observer::CHANGE_REQUESTED => 20
            ));

        $proxy->addObserverForKey($mock, 'property', Observer::OPTION_NEW);
        $proxy->setProperty(20);
    }

    public function testInitialOption()
    {
        $object = new NonKvo();
        $object->setProperty(10);

        self::assertEquals(10, $object->getProperty());

        $proxy = new Proxy($object);

        $mock = self::getMockBuilder('MacFJA\PhpKVO\Listener')
            ->disableOriginalConstructor()
            ->getMock();

        $mock->expects(self::once())->method('observeValueForKeyPath')
            ->with('property', $proxy, array(
                Observer::CHANGE_PRIOR => false,
                Observer::CHANGE_SOURCE => Observer::SOURCE_INITIAL,
                Observer::CHANGE_NEW => 10
            ));

        $proxy->addObserverForKey($mock, 'property', Observer::OPTION_INITIAL|Observer::OPTION_NEW);
    }

    public function testProxyProperty()
    {
        $object = new NonKvo();
        $object->public = 10;

        self::assertEquals(10, $object->public);

        $proxy = new Proxy($object);

        $mock = self::getMockBuilder('MacFJA\PhpKVO\Listener')
            ->disableOriginalConstructor()
            ->getMock();

        $mock->expects(self::once())->method('observeValueForKeyPath')
            ->with('public', $proxy, array(
                Observer::CHANGE_PRIOR => false,
                Observer::CHANGE_SOURCE => Observer::SOURCE_PROPERTY,
                Observer::CHANGE_NEW => 15,
                Observer::CHANGE_REQUESTED => 15,
                Observer::CHANGE_OLD => 10
            ));

        $proxy->addObserverForKey($mock, 'public', Observer::OPTION_OLD|Observer::OPTION_NEW);
        $proxy->public = 15;
    }

    public function testProxyNameConvention()
    {
        $object = new NonKvo();
        $object->setxPosition(10);

        self::assertEquals(10, $object->getxPosition());

        $proxy = new Proxy($object);

        $mock = self::getMockBuilder('MacFJA\PhpKVO\Listener')
            ->disableOriginalConstructor()
            ->getMock();

        $mock->expects(self::once())->method('observeValueForKeyPath')
            ->with('xPosition', $proxy, array(
                Observer::CHANGE_PRIOR => false,
                Observer::CHANGE_SOURCE => Observer::SOURCE_SETTER,
                Observer::CHANGE_NEW => 15,
                Observer::CHANGE_REQUESTED => 15,
                Observer::CHANGE_OLD => 10
            ));

        $proxy->addObserverForKey($mock, 'xPosition', Observer::OPTION_OLD|Observer::OPTION_NEW);
        $proxy->setxPosition(15);
    }

    public function testProxyShortVar()
    {
        $object = new NonKvo();
        $object->setY(7);

        self::assertEquals(7, $object->getY());

        $proxy = new Proxy($object);

        $mock = self::getMockBuilder('MacFJA\PhpKVO\Listener')
            ->disableOriginalConstructor()
            ->getMock();

        $mock->expects(self::once())->method('observeValueForKeyPath')
            ->with('y', $proxy, array(
                Observer::CHANGE_PRIOR => false,
                Observer::CHANGE_SOURCE => Observer::SOURCE_SETTER,
                Observer::CHANGE_NEW => 999,
                Observer::CHANGE_REQUESTED => 999,
                Observer::CHANGE_OLD => 7
            ));

        $proxy->addObserverForKey($mock, 'y', Observer::OPTION_OLD|Observer::OPTION_NEW);
        $proxy->setY(999);
    }

    public function testImplementation()
    {
        $object = new KVOClass();

        $mock = self::getMockBuilder('MacFJA\PhpKVO\Listener')
            ->disableOriginalConstructor()
            ->getMock();

        $mock->expects(self::once())->method('observeValueForKeyPath')
            ->with('value', $object, array(
                Observer::CHANGE_PRIOR => false,
                Observer::CHANGE_SOURCE => Observer::SOURCE_CUSTOM,
            ));

        $object->addObserverForKey($mock, 'value');
        $object->doSomething();
    }

    public function testKvcImplementation()
    {
        $object = new KVOClass();

        $mock = self::getMockBuilder('MacFJA\PhpKVO\Listener')
            ->disableOriginalConstructor()
            ->getMock();

        $mock2 = self::getMockBuilder('MacFJA\PhpKVO\Listener')
            ->disableOriginalConstructor()
            ->getMock();

        $mock->expects(self::once())->method('observeValueForKeyPath')
            ->with('progress', $object, array(
                Observer::CHANGE_PRIOR => false,
                Observer::CHANGE_SOURCE => Observer::SOURCE_KVC,
            ));

        $mock2->expects(self::once())->method('observeValueForKeyPath')
            ->with('protected', $object, array(
                Observer::CHANGE_PRIOR => false,
                Observer::CHANGE_SOURCE => Observer::SOURCE_KVC,
            ));

        $object->addObserverForKey($mock, 'progress');
        $object->addObserverForKey($mock2, 'protected');
        $object->setValueForKey('progress', 200);
        $object->setValueForKey('protected', 345);
    }
}
