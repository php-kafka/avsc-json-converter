<?php

declare(strict_types=1);

namespace PhpKafka\AvscJsonConverter\Converter;

interface ConverterInterface
{
    /**
     * @param string $avscSchema
     * @param array<string,mixed> $options
     * @return string
     */
    public function convert(string $avscSchema, array $options): string;
}
