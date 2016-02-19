<?php

namespace MacFJA\PhpKVO\Spl;

use MacFJA\PhpKVO\Observable;
use SplObserver;

/**
 * Class Subject.
 *
 * The Subject of the Subject/Observer couple.
 * It contains all information about the current notification.
 *
 * @package MacFJA\PhpKVO\Spl
 * @author  MacFJA
 * @license MIT
 */
class Subject implements \SplSubject
{
    /**
     * List of all observers that listen to the object changes.
     *
     * @var Observer[]
     */
    protected $observers = array();
    /**
     * The object that change.
     *
     * @var Observable
     */
    protected $object;
    /**
     * Indicate if the notification if before the change, or after it
     *
     * @var bool
     */
    protected $prior = true;
    /**
     * The source/type of change.
     *
     * Indication after which kind of action the value change.
     * Ex: from the setter, from a property access, etc.
     *
     * {@see MacFJA\PhpKVO\Spl\Observer constant}
     *
     * @var string
     */
    protected $source = Observer::SOURCE_SETTER;
    /**
     * The key that will/did change
     *
     * @var string
     */
    protected $key;
    /**
     * The value before the change
     *
     * @var mixed
     */
    protected $oldValue;
    /**
     * The new value before the change.
     * It contains the value before teh actual change, in case of setter or external change during the new value
     * assignment
     *
     * @var mixed
     */
    protected $requestedValue;
    /**
     * The value after the change
     *
     * @var mixed
     */
    protected $newValue;

    /**
     * Subject constructor.
     *
     * @param Observable $object The object to observe/listen
     */
    public function __construct(Observable $object)
    {
        $this->object = $object;
    }

    /**
     * The the "in edit" key name
     *
     * @return mixed
     */
    public function getKey()
    {
        return $this->key;
    }

    /**
     * The value of the key before the change
     *
     * @return mixed
     */
    public function getOldValue()
    {
        return $this->oldValue;
    }

    /**
     * The new value of the key before the actual change
     *
     * @return mixed
     */
    public function getRequestedValue()
    {
        return $this->requestedValue;
    }

    /**
     * The value after the change.
     * In case of _prior_ notification, this value always return `null`
     *
     * @return mixed|null
     */
    public function getNewValue()
    {
        if ($this->prior) {
            return null;
        }
        return $this->newValue;
    }

    /**
     * Reset the notification payload. (key, old, request and new value)
     *
     * @return void
     */
    public function resetPayload()
    {
        $this->key            = null;
        $this->oldValue       = null;
        $this->requestedValue = null;
        $this->newValue       = null;
    }

    /**
     * Set the payload (content of the notification)
     *
     * @param string $key            The key that is changed
     * @param mixed  $oldValue       The value before change
     * @param mixed  $requestedValue The "new" value before change
     * @param mixed  $newValue       The value after change
     *
     * @return void
     */
    public function setPayload($key, $oldValue = null, $requestedValue = null, $newValue = null)
    {
        $this->key            = $key;
        $this->oldValue       = $oldValue;
        $this->requestedValue = $requestedValue;
        $this->newValue       = $newValue;
    }

    /**
     * Get the source/type of change.
     *
     * @return string
     */
    public function getSource()
    {
        return $this->source;
    }

    /**
     * Set the source/type of change.
     *
     * @param string $source The source of the change
     *
     * @return void
     */
    public function setSource($source)
    {
        $this->source = $source;
    }

    /**
     * Indicate if the notification if before (`true`) or after (`false`) the change
     *
     * @return boolean
     */
    public function isPrior()
    {
        return $this->prior;
    }

    /**
     * Set the prior information.
     *
     * @param boolean $prior If before change set to `true`.
     *
     * @return void
     */
    public function setPrior($prior)
    {
        $this->prior = $prior;
    }

    /**
     * Get the observed object
     *
     * @return mixed
     */
    public function getObject()
    {
        return $this->object;
    }

    /**
     * (PHP 5 &gt;= 5.1.0)<br/>
     * Attach an SplObserver
     *
     * {@link http://php.net/manual/en/splsubject.attach.php}
     *
     * @param SplObserver $observer <p>
     * The <b>SplObserver</b> to attach.
     * </p>
     *
     * @return void
     */
    public function attach(SplObserver $observer)
    {
        $this->observers[] = $observer;
    }

    /**
     * (PHP 5 &gt;= 5.1.0)<br/>
     * Detach an observer
     *
     * {@codeCoverageIgnore}
     * {@link http://php.net/manual/en/splsubject.detach.php}
     *
     * @param SplObserver $observer <p>
     * The <b>SplObserver</b> to detach.
     * </p>
     *
     * @return void
     */
    public function detach(SplObserver $observer)
    {
        $key = array_search($observer, $this->observers, true);
        if ($key) {
            unset($this->observers[$key]);
        }
    }

    /**
     * (PHP 5 &gt;= 5.1.0)<br/>
     * Notify an observer
     *
     * {@link http://php.net/manual/en/splsubject.notify.php}
     *
     * @return void
     */
    public function notify()
    {
        foreach ($this->observers as $value) {
            if ($value->getKey() !== $this->getKey()) {
                continue;
            }
            if (($value->getOptions() & Observer::OPTION_PRIOR) === 0 && $this->isPrior()) {
                continue;
            }
            if (($value->getOptions() & Observer::OPTION_PRIOR) !== 0 && !$this->isPrior()) {
                continue;
            }

            $value->update($this);
        }
    }
}
