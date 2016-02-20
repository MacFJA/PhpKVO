<?php

namespace MacFJA\PhpKVO;

use MacFJA\PhpKVO\Spl\Observer;
use MacFJA\PhpKVO\Spl\Subject;
use MacFJA\ValueProvider\ChainProvider;
use MacFJA\ValueProvider\GuessProvider;
use MacFJA\ValueProvider\ProviderInterface;

/**
 * Trait ObservableTrait
 *
 * The trait to implement all needed methods to have KVC/KVO.
 * Don't forget to implement the interface!
 *
 * @package MacFJA\PhpKVO
 * @author  MacFJA
 * @license MIT
 */
trait ObservableTrait
{
    /**
     * The subject that will send notification to all observer
     *
     * @var Subject
     */
    private $subject;
    /**
     * The value getter/setter
     *
     * @var ProviderInterface
     */
    private $provider;

    /**
     * Add an observer on the value change of a key.
     *
     * @param Listener   $listener The object that subscribe to the changes notification
     * @param string     $key      The key to listen
     * @param int        $options  The list of options ({@see MacFJA\PhpKVO\Spl\Observer::OPTION_*})
     * @param mixed|null $context  The context (data to send with the notification)
     *
     * @return mixed
     */
    public function addObserverForKey(Listener $listener, $key, $options = 0, &$context = null)
    {
        $this->ensureKvoIsReady();
        $observer = new Observer($listener, $key, $options, $context);
        if (($options & Observer::OPTION_INITIAL) !== 0) {
            $provider       = new GuessProvider();
            $initialSubject = clone $this->subject;
            $initialSubject->setPayload($key, null, null, $provider->getValue($initialSubject->getObject(), $key));
            $initialSubject->setPrior(false);
            $initialSubject->setSource(Observer::SOURCE_INITIAL);
            $observer->update($initialSubject);
        }
        $this->subject->attach($observer);
    }

    /**
     * Send a notification about a key, **BEFORE** the change.
     *
     * @param string     $key            The key that is changed
     * @param string     $source         The source/type of change ({@see MacFJA\PhpKVO\Spl\Observer::SOURCE_*})
     * @param null|mixed $oldValue       The current value of the key
     * @param null|mixed $requestedValue The requested new value of the key
     *
     * @return void
     */
    public function willChangeValueForKey($key, $source, $oldValue = null, $requestedValue = null)
    {
        $this->ensureKvoIsReady();
        $this->subject->setPrior(true);
        $this->subject->setSource($source);
        $this->subject->setPayload($key, $oldValue, $requestedValue, null);
        $this->subject->notify();
    }

    /**
     * Send a notification about a key, **AFTER** the change.
     *
     * @param string     $key            The key that is changed
     * @param string     $source         The source/type of change ({@see MacFJA\PhpKVO\Spl\Observer::SOURCE_*})
     * @param null|mixed $oldValue       The value of the key before the change
     * @param null|mixed $requestedValue The requested new value of the key
     * @param null|mixed $newValue       The current value of the key
     *
     * @return void
     */
    public function didChangeValueForKey($key, $source, $oldValue = null, $requestedValue = null, $newValue = null)
    {
        $this->ensureKvoIsReady();
        $this->subject->setPrior(false);
        $this->subject->setSource($source);
        $this->subject->setPayload($key, $oldValue, $requestedValue, $newValue);
        $this->subject->notify();
    }

    /**
     * Initialize the subject of subject/observer couple
     *
     * @return void
     */
    protected function ensureKvoIsReady()
    {
        if (null === $this->subject) {
            $this->subject = new Subject($this);
        }
        if (null === $this->provider) {
            $this->provider = new ChainProvider();
            $this->provider->setProviders(array(
                'MacFJA\ValueProvider\MutatorProvider',
                'MacFJA\ValueProvider\PropertyProvider'
            ));
        }
    }

    /**
     * Set a new value and send notifications.
     * The method will try to set the value with the setter, with direct property access or through Reflection.
     *
     * @param string $key   The key to change
     * @param mixed  $value The new value
     *
     * @return void
     */
    public function setValueForKey($key, $value)
    {
        $oldValue     = $this->provider->getValue($this, $key);
        $initialValue = $value;

        $this->willChangeValueForKey($key, Observer::SOURCE_KVC, $oldValue, $initialValue);

        try {
            $this->provider->setValue($this, $key, $value);
        } catch (\InvalidArgumentException $e) {
            // Try the in scope access
            $this->{$key} = $value;
        }

        $newValue = $this->provider->getValue($this, $key);

        $this->didChangeValueForKey($key, Observer::SOURCE_KVC, $oldValue, $initialValue, $newValue);
    }
}
