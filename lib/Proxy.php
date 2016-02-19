<?php

namespace MacFJA\PhpKVO;

use Eloquent\Pops\Exception\InvalidTypeException;
use Eloquent\Pops\ProxyObject;
use MacFJA\PhpKVO\Spl\Observer;
use MacFJA\ValueProvider\GuessProvider;

/**
 * Class Proxy.
 *
 * Proxy to add KVC/KVO to any object.
 *
 * @package MacFJA\PhpKVO
 * @author  MacFJA
 * @license MIT
 */
class Proxy extends ProxyObject implements Observable
{
    use ObservableTrait;

    /**
     * The list of method=>property to catch
     *
     * @var array
     */
    protected $setterMethods = array();
    /**
     * The reflection of the watched object
     *
     * @var \ReflectionObject
     */
    protected $reflection;

    /**
     * Construct a new traversable proxy.
     *
     * @param mixed        $value       The value to wrap.
     * @param boolean|null $isRecursive True if the wrapped value should be recursively proxied.
     *
     * @throws InvalidTypeException If the supplied value is not the correct type.
     */
    public function __construct($value, $isRecursive = null)
    {
        parent::__construct($value, $isRecursive);
        $this->reflection = new \ReflectionObject($this->popsValue());
        $this->initSetterMethods();
    }

    /**
     * Call a method on the wrapped object with support for by-reference
     * arguments.
     *
     * @param string $method    The name of the method to call.
     * @param array  $arguments The arguments.
     *
     * @return mixed The result of the method call.
     */
    public function popsCall($method, array &$arguments)
    {
        if ($this->isSetterMethod($method)) {
            $property     = $this->setterMethods[$method];
            $oldValue     = $this->getValue($property);
            $initialValue = reset($arguments);

            $this->willChangeValueForKey($property, Observer::SOURCE_SETTER, $oldValue, $initialValue);
            $return   = parent::popsCall($method, $arguments);
            $newValue = $this->getValue($property);

            $this->didChangeValueForKey($property, Observer::SOURCE_SETTER, $oldValue, $initialValue, $newValue);

            return $return;
        }
        return parent::popsCall($method, $arguments);
    }

    /**
     * Set the value of a property on the wrapped object.
     *
     * @param string $property The property name.
     * @param mixed  $value    The new value.
     *
     * @return void
     */
    public function __set($property, $value)
    {
        $oldValue = $this->getValue($property);

        $this->willChangeValueForKey($property, Observer::SOURCE_PROPERTY, $oldValue, $value);
        parent::__set($property, $value);
        $this->didChangeValueForKey(
            $property,
            Observer::SOURCE_PROPERTY,
            $oldValue,
            $value,
            $this->getValue($property)
        );
    }

    /**
     * Test if the method is a registered property setter
     *
     * @param string $method The method name to test
     *
     * @return bool
     */
    protected function isSetterMethod($method)
    {
        return array_key_exists($method, $this->setterMethods);
    }

    /**
     * Add a new method to catch
     *
     * @param string $method   The method name
     * @param string $property The property name
     *
     * @return void
     */
    public function addSetterMethod($method, $property)
    {
        $this->setterMethods[$method] = $property;
    }

    /**
     * Populate the list of the setter methods by reading the list of properties and the methods.
     * The getter as tester against the "JavaBeans(TM) API specification" (version 1.01-A, August 8, 1997)
     *
     * @return void
     */
    protected function initSetterMethods()
    {
        /*
         * Build the setter method list according to the:
         * "JavaBeans(TM) API specification" (version 1.01-A, August 8, 1997) chapter 8.8
         */
        foreach ($this->reflection->getProperties() as $property) {
            $prefix = 'set';

            $capitalization = strtoupper(substr($property->getName(), 0, 1)) . substr($property->getName(), 1);
            if (strlen($property->getName()) === 1) {
                $capitalization = strtoupper($property->getName());
            } elseif (substr($property->getName(), 1, 1) !== strtolower(substr($property->getName(), 1, 1))) {
                /*
                 * The case handle both $URL, and $xPosition case.
                 * The getter for $xPosition is setxPosition because of the implementation of
                 * "java.beans.Introspector.decapitalize".
                 * {@see http://stackoverflow.com/a/16146215}
                 * {@see http://dertompson.com/2013/04/29/java-bean-getterssetters/}
                 */
                $capitalization = $property->getName();
            }

            if ($this->reflection->hasMethod($prefix . $capitalization)) {
                $this->addSetterMethod($prefix . $capitalization, $property->getName());
            }
        }
    }

    /**
     * Get the current value of property of the observed object
     *
     * @param string $property The property name
     *
     * @return mixed
     */
    protected function getValue($property)
    {
        $provider = new GuessProvider();
        return $provider->getValue($this->popsValue(), $property);
    }
}
