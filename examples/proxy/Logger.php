<?php
/**
 * Created by PhpStorm.
 * User: dev
 * Date: 20/02/2016
 * Time: 22:55
 */

namespace MacFJA\PhpKVO\examples\proxy;


use MacFJA\PhpKVO\Listener;
use MacFJA\PhpKVO\Spl\Observer;

class Logger implements Listener
{

    /**
     * Notification receiver method.
     *
     * When a value for key change and the object that implement this interface have subscribe to it,
     * this method is called.
     *
     * The `$change` contains several information about the change.
     * Structure:
     * ```php
     * Array(
     *    // The source of change: for setter, for property change, etc.
     *    MacFJA\PhpKVO\Spl\Observer::CHANGE_SOURCE => String,
     *    // The new value of the key
     *    MacFJA\PhpKVO\Spl\Observer::CHANGE_NEW => mixed,
     *    // The value of the key before the change
     *    MacFJA\PhpKVO\Spl\Observer::CHANGE_OLD => mixed,
     *    // The initial new value (in case of setter transformation)
     *    MacFJA\PhpKVO\Spl\Observer::CHANGE_REQUESTED => mixed,
     *    // `true` if the notification if before the effective change/call
     *    MacFJA\PhpKVO\Spl\Observer::CHANGE_PRIOR => boolean
     * )
     * ```
     * If _MacFJA\PhpKVO\Spl\Observer::CHANGE_PRIOR_ is `true` then the value of
     * _MacFJA\PhpKVO\Spl\Observer::CHANGE_NEW_ is always null
     *
     * @param string $keyPath The modified key
     * @param object $object The modified object
     * @param array $change The changed data
     * @param mixed $context The changed context (provided on subscription)
     *
     * @return void
     */
    public function observeValueForKeyPath($keyPath, $object, $change, &$context)
    {
        echo sprintf('[LOG] $%s = %s'. PHP_EOL, $keyPath, var_export($change[Observer::CHANGE_REQUESTED], true));
    }
}