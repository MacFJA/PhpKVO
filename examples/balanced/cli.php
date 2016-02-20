<?php
use MacFJA\PhpKVO\examples\balanced\SelfBalanced;

require_once __DIR__.DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR .
    'vendor' . DIRECTORY_SEPARATOR . 'autoload.php';

require __DIR__ . DIRECTORY_SEPARATOR . 'SelfBalanced.php';

$balanced = new SelfBalanced(0, 100, 100);

/**
 * @param SelfBalanced $object
 */
function status($object) {
    echo vsprintf('Used: %d, Left: %d, Total: %d', array($object->getUsed(), $object->getLeft(), $object->getTotal()));
    echo PHP_EOL;
}

$count = 10;
while ($count --> 0) {
    $value = floor(rand(0, 100));
    echo PHP_EOL.'Set used to: '.$value.PHP_EOL;
    $balanced->setValueForKey('used', $value);
    status($balanced);
    sleep(1);
}

$count = 10;
while ($count --> 0) {
    $value = floor(rand(0, 100));
    echo PHP_EOL.'Set left to: '.$value.PHP_EOL;
    $balanced->setValueForKey('left', $value);
    status($balanced);
    sleep(1);
}

$count = 10;
while ($count --> 0) {
    $value = floor(rand(0, 100));
    echo PHP_EOL.'Set total to: '.$value.PHP_EOL;
    $balanced->setValueForKey('total', $value);
    status($balanced);
    sleep(1);
}