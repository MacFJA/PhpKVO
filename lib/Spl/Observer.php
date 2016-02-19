<?php

namespace MacFJA\PhpKVO\Spl;

use MacFJA\PhpKVO\Listener;
use SplSubject;

/**
 * Class Observer.
 *
 * The observer of the Subject/Observer couple.
 * Contains all constants about notification.
 *
 * @package MacFJA\PhpKVO\Spl
 * @author  MacFJA
 * @license MIT
 */
class Observer implements \SplObserver
{
    /**
     * Indicated that the new value must be provided if applicable
     */
    const OPTION_NEW = 0x01;
    /**
     * Indicated that the old value must be provided if applicable
     */
    const OPTION_OLD = 0x02;
    /**
     * Indicate that an event must be send when the observer is register
     */
    const OPTION_INITIAL = 0x04;
    /**
     * Indicated that the notification must be before the change
     */
    const OPTION_PRIOR = 0x08;

    /**
     * Change array key for the source/type of change
     */
    const CHANGE_SOURCE = 'source';
    /**
     * Change array key for the new value
     */
    const CHANGE_NEW = 'new';
    /**
     * Change array key for the old value
     */
    const CHANGE_OLD = 'old';
    /**
     * Change array key for the requested new value
     */
    const CHANGE_REQUESTED = 'requested';
    /**
     * Change array key that indicate if the notification if before or after change
     */
    const CHANGE_PRIOR = 'prior';

    /**
     * Source/type of change constant for setter change
     */
    const SOURCE_SETTER = 'setter';
    /**
     * Source/type of change constant for property direct access change
     */
    const SOURCE_PROPERTY = 'property';
    /**
     * Source/type of change constant for customer change
     */
    const SOURCE_CUSTOM = 'custom';
    /**
     * Source/type of change constant for customer change
     */
    const SOURCE_INITIAL = 'initial';
    /**
     * Indicate that the change was made by the call of the method `setValueForKey`
     * {@see MacFJA\PhpKVO\Observable}
     */
    const SOURCE_KVC = 'kvc';

    /**
     * The observer options
     *
     * @var int
     */
    protected $options = 0;

    /**
     * Get the options of the observer
     *
     * @return int
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * The context of the event.
     * The content is always set/use be reference
     *
     * @var mixed|null
     */
    protected $context;
    /**
     * The Object that subscribe to the change notification
     *
     * @var Listener
     */
    protected $listener;
    /**
     * The key to listen
     *
     * @var string
     */
    protected $key;

    /**
     * Get the listened key.
     *
     * @return string
     */
    public function getKey()
    {
        return $this->key;
    }

    /**
     * Observer constructor.
     *
     * @param Listener   $listener The object that subscribe to notification
     * @param string     $key      The key to observer
     * @param int        $options  The observer options
     * @param mixed|null $context  The notification context
     */
    public function __construct(Listener $listener, $key, $options = 0, $context = null)
    {
        $this->options  = $options;
        $this->context  = &$context;
        $this->listener = $listener;
        $this->key      = $key;
    }


    /**
     * (PHP 5 &gt;= 5.1.0)<br/>
     * Receive update from subject
     *
     * {@link http://php.net/manual/en/splobserver.update.php}
     *
     * @param SplSubject $subject <p>
     * The <b>SplSubject</b> notifying the observer of an update.
     * </p>
     *
     * @return void
     */
    public function update(SplSubject $subject)
    {
        if ($subject instanceof Subject) {
            $change = array(
                self::CHANGE_PRIOR => $subject->isPrior(),
                self::CHANGE_SOURCE => $subject->getSource()
            );

            if ($this->options & self::OPTION_NEW) {
                if (!$subject->isPrior()) {
                    $change[self::CHANGE_NEW] = $subject->getNewValue();
                }
                if ($subject->getSource() !== self::SOURCE_INITIAL) {
                    $change[self::CHANGE_REQUESTED] = $subject->getRequestedValue();
                }
            }
            if ($this->options & self::OPTION_OLD && $subject->getSource() !== self::SOURCE_INITIAL) {
                $change[self::CHANGE_OLD] = $subject->getOldValue();
            }

            $this->listener->observeValueForKeyPath($this->key, $subject->getObject(), $change, $this->context);
        }
    }
}
