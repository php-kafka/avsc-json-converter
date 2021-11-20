<?php

declare(strict_types=1);

namespace PhpKafka\AvscJsonConverter\ServiceProvider;

use PhpKafka\AvscJsonConverter\Converter\AvscToJson;
use PhpKafka\AvscJsonConverter\Converter\ConverterInterface;
use Pimple\Container;
use Pimple\ServiceProviderInterface;

class ConverterServiceProvider implements ServiceProviderInterface
{
    public function register(Container $container): void
    {
        $container[AvscToJson::class] = function (): ConverterInterface {
            return new AvscToJson();
        };
    }
}
