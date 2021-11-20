<?php

declare(strict_types=1);

namespace PhpKafka\AvscJsonConverter\ServiceProvider;

use PhpKafka\AvscJsonConverter\Command\ConvertAvscToJsonCommand;
use PhpKafka\AvscJsonConverter\Command\ConvertSingleAvscToJsonCommand;
use PhpKafka\AvscJsonConverter\Converter\AvscToJson;
use Pimple\Container;
use Pimple\ServiceProviderInterface;

class CommandServiceProvider implements ServiceProviderInterface
{
    public function register(Container $container)
    {
        $container['console.commands'] = function () use ($container): array {
            $commands = [];

            $commands[ConvertAvscToJsonCommand::class] = new ConvertAvscToJsonCommand(
                $container[AvscToJson::class]
            );

            $commands[ConvertSingleAvscToJsonCommand::class] = new ConvertSingleAvscToJsonCommand(
                $container[AvscToJson::class]
            );

            return $commands;
        };
    }
}