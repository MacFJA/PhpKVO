<?php
/**
 * Created by PhpStorm.
 * User: dev
 * Date: 14/02/2016
 * Time: 17:39
 */

namespace MacFJA\PhpKVO\Test\Spl\Test;


use MacFJA\PhpKVO\Spl\Observer;
use MacFJA\PhpKVO\Spl\Subject;
use MacFJA\PhpKVO\Test\fixtures\NullListener;
use MacFJA\PhpKVO\Test\fixtures\NullObservable;
use Symfony\Component\Yaml\Yaml;

class SubjectTest extends \PHPUnit_Framework_TestCase
{
    protected function getSubject($payload, $prior, $source)
    {
        $subject = new Subject(new NullObservable());
        $subject->setPayload(
            $payload['key'],
            $payload['old'],
            $payload['requested'],
            $payload['new']
        );
        $subject->setSource($source);
        $subject->setPrior($prior);

        return $subject;
    }

    /**
     * @dataProvider dataProvider
     *
     * @param $expected
     * @param $payload
     * @param $prior
     * @param $source
     */
    public function testGetter($expected, $payload, $prior, $source)
    {
        $subject = $this->getSubject($payload, $prior, $source);
        self::assertEquals($expected['key'], $subject->getKey());
        self::assertEquals($expected['source'], $subject->getSource());
        self::assertEquals($expected['new'], $subject->getNewValue());
        self::assertEquals($expected['old'], $subject->getOldValue());
        self::assertEquals($expected['requested'], $subject->getRequestedValue());
        self::assertEquals($expected['prior'], $subject->isPrior());
    }

    public function testResetPayload()
    {
        $subject = $this->getSubject(array('new' => 'n', 'old' => 'o', 'requested' => 'r', 'key' => 'k'), false, 's');
        self::assertEquals('k', $subject->getKey());
        self::assertEquals('n', $subject->getNewValue());
        self::assertEquals('o', $subject->getOldValue());
        self::assertEquals('r', $subject->getRequestedValue());

        $subject->resetPayload();

        self::assertNull($subject->getKey());
        self::assertNull($subject->getNewValue());
        self::assertNull($subject->getOldValue());
        self::assertNull($subject->getRequestedValue());
    }

    public function testNotify1()
    {
        $observerMock = self::getMockBuilder('MacFJA\PhpKVO\Spl\Observer')
            ->setConstructorArgs(array(new NullListener(), 'key'))
            ->getMock();
        $observerMock->method('getKey')->willReturn('key');

        $subject = new Subject(new NullObservable());
        $subject->attach($observerMock);
        $subject->setPayload('k');
        $subject->setPrior(false);
        $observerMock->expects(self::never())->method('update');
        $subject->notify();


        $subject->setPayload('key');
        $subject->setPrior(true);
        $observerMock->expects(self::never())->method('update');
        $subject->notify();


        $observerMock->method('getOptions')->willReturn(Observer::OPTION_PRIOR);
        $subject->setPayload('key');
        $subject->setPrior(false);
        $observerMock->expects(self::never())->method('update');
        $subject->notify();
    }

    public function testNotify2()
    {
        $observerMock = self::getMockBuilder('MacFJA\PhpKVO\Spl\Observer')
            ->setConstructorArgs(array(new NullListener(), 'key'))
            ->getMock();
        $observerMock->method('getKey')->willReturn('key');

        $subject = new Subject(new NullObservable());
        $subject->attach($observerMock);

        $subject->setPayload('key');
        $subject->setPrior(false);
        $observerMock->expects(self::once())->method('update');
        $subject->notify();
    }

    public function testDetach()
    {
        $observerMock = self::getMockBuilder('MacFJA\PhpKVO\Spl\Observer')
            ->setConstructorArgs(array(new NullListener(), 'key'))
            ->getMock();
        $observerMock->method('getKey')->willReturn('key');

        $subject = new Subject(new NullObservable());
        $subject->attach($observerMock);

        $subject->setPayload('key');
        $subject->setPrior(false);
        $observerMock->expects(self::once())->method('update');
        $subject->notify();
    }

    public function dataProvider($name)
    {
        return Yaml::parse(file_get_contents(__DIR__.DIRECTORY_SEPARATOR.'subject.dataset.yml'));
    }
}
