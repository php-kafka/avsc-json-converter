<?php

namespace PhpKafka\AvscJsonConverter\Avro;

class Avro
{
    public const FILE_EXTENSION = 'avsc';
    public const LONELIEST_NUMBER = 1;
    public const BASIC_TYPES = [
        'null' => self::LONELIEST_NUMBER,
        'boolean' => self::LONELIEST_NUMBER,
        'int' => self::LONELIEST_NUMBER,
        'long' => self::LONELIEST_NUMBER,
        'float' => self::LONELIEST_NUMBER,
        'double' => self::LONELIEST_NUMBER,
        'bytes' => self::LONELIEST_NUMBER,
        'string' => self::LONELIEST_NUMBER,
        'enum' => self::LONELIEST_NUMBER,
        'array' => self::LONELIEST_NUMBER,
        'map' => self::LONELIEST_NUMBER,
        'fixed' => self::LONELIEST_NUMBER,
    ];
}
