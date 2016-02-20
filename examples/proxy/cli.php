<?php

use MacFJA\PhpKVO\examples\proxy\FakeDb;
use MacFJA\PhpKVO\examples\proxy\Logger;
use MacFJA\PhpKVO\Proxy;
use MacFJA\PhpKVO\Spl\Observer;

require_once __DIR__.DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR .
    'vendor' . DIRECTORY_SEPARATOR . 'autoload.php';

require __DIR__ . DIRECTORY_SEPARATOR . 'FakeDb.php';
require __DIR__ . DIRECTORY_SEPARATOR . 'Logger.php';

$fakeDb = new Proxy(new FakeDb());
$logger = new Logger();

$fakeDb->addObserverForKey($logger, 'sql', Observer::OPTION_PRIOR|Observer::OPTION_NEW);
$fakeDb->addSetterMethod('query', 'sql');

$fakeDb->query('SELECT name FROM users WHERE role = \'admin\'');
$fakeDb->query('SELECT * FROM messages WHERE username = \'Administrator\'');