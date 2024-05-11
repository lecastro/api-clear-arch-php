<?php

use Domain\Example;

require_once __DIR__ . '/../vendor/autoload.php';

$example = new Example;

var_dump($example->say());
