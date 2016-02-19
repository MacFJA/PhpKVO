<?php

namespace MacFJA\PhpKVO;

/**
 * Interface Observable.
 *
 * The list of function that an observable (KVC/KVO) object must implement.
 *
 * @package MacFJA\PhpKVO
 * @author  MacFJA
 * @license MIT
 */
interface Observable
{
    /**
     * Add an observer on the value change of a key.
     *
     * @param Listener $listener The object that subscribe to the changes notification
     * @param string   $key      The key to listen
     * @param int      $option   The list of options ({@see MacFJA\PhpKVO\Spl\Observer::OPTION_*})
     * @param mixed    $context  The context (data to send with the notification)
     *
     * @return mixed
     */
    public function addObserverForKey(Listener $listener, $key, $option = 0, &$context = null);

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
    public function willChangeValueForKey($key, $source, $oldValue = null, $requestedValue = null);

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
    public function didChangeValueForKey($key, $source, $oldValue = null, $requestedValue = null, $newValue = null);

    /**
     * Set a new value and send notifications.
     * The method will try to set the value with the setter, with direct property access or through Reflection.
     *
     * @param string $key   The key to change
     * @param mixed  $value The new value
     *
     * @return void
     */
    public function setValueForKey($key, $value);
}
