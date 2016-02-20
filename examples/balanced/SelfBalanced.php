<?php
/**
 * Created by PhpStorm.
 * User: dev
 * Date: 19/02/2016
 * Time: 21:26
 */

namespace MacFJA\PhpKVO\examples\balanced;


use MacFJA\PhpKVO\AbstractObservable;
use MacFJA\PhpKVO\Listener;
use MacFJA\PhpKVO\Spl\Observer;

class SelfBalanced extends AbstractObservable implements Listener
{
    protected $used = 0;
    protected $left = 100;
    protected $total = 100;

    /**
     * SelfBalanced constructor.
     * @param int $used
     * @param int $left
     * @param int $total
     */
    public function __construct($used, $left, $total)
    {
        $this->used = $used;
        $this->left = $left;
        $this->total = $total;

        $this->addObserverForKey($this, 'used');
        $this->addObserverForKey($this, 'left');
        $this->addObserverForKey($this, 'total');
    }

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
        if ($keyPath === 'used') {
            $this->left = $this->total - $this->used;
        } elseif ($keyPath === 'left') {
            $this->total = $this->left + $this->used;
        } elseif ($keyPath === 'total') {
            if ($this->total < $this->used) {
                $this->used = $this->total;
                $this->left = 0;
            } else {
                $this->didChangeValueForKey('used', Observer::SOURCE_CUSTOM);
            }
        }
    }



    /**
     * @return int
     */
    public function getUsed()
    {
        return $this->used;
    }

    /**
     * @return int
     */
    public function getLeft()
    {
        return $this->left;
    }

    /**
     * @return int
     */
    public function getTotal()
    {
        return $this->total;
    }
}