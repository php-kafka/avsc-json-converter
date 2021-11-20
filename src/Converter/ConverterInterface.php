<?php

declare(strict_types=1);

namespace PhpKafka\AvscJsonConverter\Converter;

interface ConverterInterface
{
    public function convert(string $avscSchema, array $options): string;
}
