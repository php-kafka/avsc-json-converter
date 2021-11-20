#!/usr/bin/env php
<?php

declare(strict_types=1);

namespace PhpKafka\AvscJsonConverter;

if (is_file(__DIR__ . '/../../../autoload.php') === true) {
    include_once __DIR__ . '/../../../autoload.php';
} else {
    include_once __DIR__ . '/../vendor/autoload.php';
}

use Symfony\Component\Console\Application;
use Symfony\Component\Console\Command\Command;

$container = AppContainer::init();

/** @var array<Command> $commands */
$commands = $container['console.commands'];
$console = new Application('Avsc-JSON Schema Converter');
$console->addCommands($commands);
$console->run();
