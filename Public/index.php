<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

use DomacinskiBurek\System\Loader;

require dirname(__DIR__) . '/vendor/autoload.php';

(new Loader())->run();