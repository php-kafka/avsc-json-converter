<?php

declare(strict_types=1);

namespace PhpKafka\AvscJsonConverter\ServiceProvider;

use PhpKafka\AvscJsonConverter\Command\ConvertAvscToJsonCommand;
use PhpKafka\AvscJsonConverter\Command\ConvertSingleAvscToJsonCommand;
use PhpKafka\AvscJsonConverter\Converter\AvscToJson;
use PhpKafka\AvscJsonConverter\Converter\ConverterInterface;
use Pimple\Container;
use Pimple\ServiceProviderInterface;

class CommandServiceProvider implements ServiceProviderInterface
{
    public function register(Container $container): void
    {
        $container['console.commands'] = function () use ($container): array {
            /** @var ConverterInterface $converter */
            $converter = $container[AvscToJson::class];

            $commands = [];

            $commands[ConvertAvscToJsonCommand::class] = new ConvertAvscToJsonCommand($converter);
            $commands[ConvertSingleAvscToJsonCommand::class] = new ConvertSingleAvscToJsonCommand($converter);

            return $commands;
        };
    }
}
