<?php

declare(strict_types=1);

namespace PhpKafka\AvscJsonConverter;

use PhpKafka\AvscJsonConverter\ServiceProvider\CommandServiceProvider;
use PhpKafka\AvscJsonConverter\ServiceProvider\ConverterServiceProvider;
use Pimple\Container;

class AppContainer
{
    /**
     * @return Container
     */
    public static function init(): Container
    {
        $container = new Container();

        $container
            ->register(new ConverterServiceProvider())
            ->register(new CommandServiceProvider())
        ;


        return $container;
    }
}
