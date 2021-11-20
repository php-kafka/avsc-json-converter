<?php

declare(strict_types=1);

namespace PhpKafka\AvscJsonConverter\ServiceProvider;

use PhpKafka\AvscJsonConverter\Converter\AvscToJson;
use PhpKafka\AvscJsonConverter\Converter\ConverterInterface;
use Pimple\Container;
use Pimple\ServiceProviderInterface;

class ConverterServiceProvider implements ServiceProviderInterface
{
    public function register(Container $container)
    {
        $container[AvscToJson::class] = function () use ($container): ConverterInterface {
            return new AvscToJson();
        };
    }
}