# PhpKVO

### [What is KVO](#what) &nbsp; [Installation](#installation) &nbsp; [Usage](#usage) &nbsp; [API](#api)


## What is KVO ?<a id="what"></a>

KVO (_Key Value Observing_) is a design pattern which allows an object to get notified about changes.  
It allow you keep your object synchronized each other without creating a hard link thanks to Subject/Observer design pattern.  
KVC (_Key Value Coding_) and KVO (_Key Value Observing_) are heavily used in Cocoa Framework (Objective-C)

## Installation<a id="installation"></a>

The simplest way to install the library is to use [Composer](http://getcomposer.org/):

```bash
composer require macfja/php-kvo
```

## Usage<a id="usage"></a>

```php
class Downloader extends AbstractObservable
{
  protected $progress;
  public function getProgress()
  {
    return $this->progress;
  }
  protected function receiveCallback($newProgress)
  {
    $this->setValueForKey('progress', $newProgress);
    // ... do something with the data
  }
  public function download()
  {
    // ... start the download
  }
}

class ProgressDisplay implements Listener
{
  public function observeValueForKeyPath($keyPath, $object, $change, &$context)
  {
    if ($keyPath == 'progress') {
      echo sprintf('Download in progress (%d%%)%s', $change[Observer::CHANGE_NEW], PHP_EOL);
    }
  }
}

$downloader = new Downloader();
$progress = new ProgressDisplay();
$downloader->addObserverForKey($progress, 'progress', Observer::OPTION_NEW|Observer::OPTION_INITIAL);
$downloader->download()
```

---

A complete example can be found in the directory [example](example).

## API<a id="api"></a>

### API of `Observable` interface<a id="api_observable"></a>

Implemented in `AbstractObservable`, `Proxy`.  
A trait for quick implementation is available: `ObservableTrait`

#### API of `Observable::addObserverForKey` method

This method allow you to subscribe to key value changes notification.

| Type       | Variable    | Description |
|------------|-------------|-------------|
| [`Listener`](#api_listener) | `$listener` | The object that subscribe to the changes notification. |
|string      | `$key`      | The key to listen. |
|int\|`0`         | `$options` _optional_ | The list of options. (More information below) |
|mixed\|`null` | `&$context` _optional_ | The context: data to send with the notification. This value passed by reference. |

#### API, The `Observable::addObserverForKey` options list.

- **`Observer::OPTION_NEW`**, Indicates that the change array should provide the new attribute value, if applicable.

- **`Observer::OPTION_OLD`**, Indicates that the change array should contain the old attribute value, if applicable.

- **`Observer::OPTION_INITIAL`**, If specified, a notification should be sent to the observer immediately, before the observer registration method even returns.

  The change array in the notification will always contain an `Observer::CHANGE_NEW` entry if `Observer::OPTION_NEW` is also specified but will never contain an `Observer::CHANGE_OLD` or `Observer::CHANGE_REQUESTED` entry.  
  (In an initial notification the current value of the observed property may be old, but it's new to the observer.)

- **`Observer::OPTION_PRIOR`**, Whether separate notifications should be sent to the observer before and after each change, instead of a single notification after the change.

  The change array in a notification sent before a change always contains an `Observer::CHANGE_PRIOR` entry whose value is `true`, but never contains an `Observer::CHANGE_NEW` entry. When this option is specified the change array in a notification sent after a change contains the same entries that it would contain if this option were not specified. You can use this option when the observer's own key-value observing-compliance requires it to invoke the `willChangeValueForKey` method for one of its own properties, and the value of that property depends on the value of the observed object's property.

#### API of `Observable::willChangeValueForKey` method

This method trigger a notification for all [`Listener`](#api_listener) that registered for a key with the option `Observer::OPTION_PRIOR`.  
This method should be call **before** the key value change.

| Type        | Variable          | Description |
|-------------|-------------------|-------------|
| string      | `$key`            | The key that is changed. |
| string      | `$source`         | The source/type of change. (More information below). |
| null\|mixed | `$oldValue` _optional_ | The current value of the key. |
| null\|mixed | `$requestedValue` _optional_ | The requested new value of the key. |

#### API of `Observable::didChangeValueForKey` method

This method trigger a notification for all [`Listener`](#api_listener) that registered for a key without the option `Observer::OPTION_PRIOR`.  
This method should be call **after** the key value change.

| Type        | Variable          | Description |
|-------------|-------------------|-------------|
| string      | `$key`            | The key that is changed. |
| string      | `$source`         | The source/type of change. (More information below). |
| mixed\|`null` | `$oldValue` _optional_ | The value of the key before the change. |
| mixed\|`null` | `$requestedValue` _optional_ | The requested new value of the key. |
| mixed\|`null` | `$newValue` _optional_ | The current value of the key. |

#### API of `Observer::setValueForKey` method

This method change the value of a key and handle the call of methods `Observable::willChangeValueForKey` and `Observable::didChangeValueForKey`.

| Type   | Variable | Description        |
|--------|----------|--------------------|
| string | `$key`   | The key to change. |
| mixed  | `$value` | The new value.     |

#### API, the `Observable::willChangeValueForKey` and `Observable::didChangeValueForKey` source value

- **`Observer::SOURCE_SETTER`**, If the value of the key was changed from a _setter_ method. (Used by [`Proxy`](#api_proxy) class)
- **`Observer::SOURCE_PROPERTY`**, If the value of the key was changed from a direct property change (public class property). (Used by [`Proxy`](#api_proxy) class)
- **`Observer::SOURCE_CUSTOM`**, If the value was change without a setter, or from a direct property access.

Note: You can use your own source type, it's just a string.

Note: **`Observer::SOURCE_INITIAL`**, If the [`Listener`](#api_listener) has the option `Observer::OPTION_INITIAL`, then when registered, this source is used

### API of `Listener` interface<a id="api_listener"></a>

#### API of `Listener::observeValueForKeyPath` method

This method is call every time an observed key is modified.

| Type   | Variable    | Description |
|--------|-------------|-------------|
| string | `$keyPath`  | The modified key. |
| object | `$key`      | The modified object. |
| array  | `$change`   | The changed data. (More information below) |
| mixed  | `&$context` | The changed context (provided on subscription). |

#### API, The `Listener::observeValueForKeyPath` change array

- **`Observer::CHANGE_NEW`**, If the `Observer::OPTION_NEW` was specified when the observer was registered, the value of this key is the new value for the attribute.

- **`Observer::CHANGE_OLD`**, If the `Observer::OPTION_OLD` was specified when the observer was registered, the value of this key is the value before the attribute was changed.

- **`Observer::CHANGE_PRIOR`**, If the option `Observer::OPTION_PRIOR` was specified when the observer was registered this notification is sent prior to a change.

  The change array contains an `Observer::CHANGE_PRIOR` entry whose value is `true` if the nofication is before the change, or `false` if the notification is after.