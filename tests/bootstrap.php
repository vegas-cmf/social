<?php
//Test Suite bootstrap
include __DIR__ . "/../vendor/autoload.php";

define('TESTS_ROOT_DIR', dirname(__FILE__));

$di = new \Phalcon\DI\FactoryDefault();

\Phalcon\DI::setDefault($di);
