<?php

namespace PhpKafka\AvscJsonConverter\Converter;

use PhpKafka\AvscJsonConverter\Avro\Avro;

class AvscToJson implements ConverterInterface
{
    /** @var array<string,mixed> $options */
    private array $options;

    /**
     * @param string $avscSchema
     * @param array<string,mixed> $options
     * @return string
     */
    public function convert(string $avscSchema, array $options): string
    {
        $this->options = $options;

        /** @var mixed[] $avscArray */
        $avscArray = json_decode($avscSchema, true, JSON_THROW_ON_ERROR);
        $jsonArray = $this->convertAvro($avscArray);

        /** @var string $rawJson */
        $rawJson = json_encode($jsonArray);
        $json = $this->fixAvroTypes($rawJson);

        return $json;
    }

    /**
     * @param mixed[] $avscArray
     * @return mixed[]
     */
    private function convertAvro(array $avscArray): array
    {
        $jsonArray = [];

        foreach ($avscArray as $key => $value) {
            if ('type' === $key && 'record' === $value) {
                $jsonArray[$key] = 'object';
            }
            if ('type' === $key && 'array' === $value) {
                $jsonArray[$key] = $value;

                /** @var string|mixed[] $items */
                $items = $avscArray['items'];

                if (
                    true === $this->isBasicType($items)
                    || (true === is_array($items) && true === $this->isBasicTypeArray($items))
                ) {
                    $jsonArray['items'] = $items;
                } elseif (
                    true === is_array($items)
                    && true === isset($items['type'])
                    && 'record' === $items['type']
                ) {
                    $jsonArray['items'] = $this->convertAvro($items);
                } elseif (true === is_array($items)) {
                    $jsonArray['items'] = $this->getAnyOf($items);
                }
            }
            if ('name' === $key && true === is_string($value)) {
                $jsonArray['title'] = $this->snakeToPascal($value);
            }
            if ('fields' === $key && true === is_array($value)) {
                $jsonArray['properties'] = $this->convertAvroFieldsToJsonFields($value);
                $requiredFields = $this->getRequiredFields($value);

                if ([] !== $requiredFields) {
                    $jsonArray['required'] = $requiredFields;
                }
            }
        }

        return $jsonArray;
    }

    /**
     * @param mixed[] $avroFields
     * @return mixed[]
     */
    private function convertAvroFieldsToJsonFields(array $avroFields): array
    {
        $fields = [];

        foreach ($avroFields as $field) {
            /** @var string|mixed[] $fieldType */
            $fieldType = $field['type'];

            if (
                true === $this->isBasicType($fieldType)
                || (true === is_array($fieldType) && true === $this->isBasicTypeArray($fieldType))
            ) {
                $fields[$field['name']] = [
                    'type' => $field['type'],
                    'description' => $field['doc']
                ];
            } elseif (true === is_array($fieldType)) {
                $fields[$field['name']] = $this->getAnyOf($fieldType);
            }
        }

        return $fields;
    }

    /**
     * @param mixed[] $avroFields
     * @return mixed[]
     */
    private function getRequiredFields(array $avroFields): array
    {
        $requiredFields = [];

        foreach ($avroFields as $field) {
            if (
                true === $this->options['markNoDefaultAsRequired']
                && true === is_array($field)
                && false === array_key_exists('default', $field)
            ) {
                $requiredFields[] = $field['name'];
            } elseif (false === $this->options['markNoDefaultAsRequired']) {
                $requiredFields[] = $field['name'];
            }
        }

        return $requiredFields;
    }

    /**
     * @param mixed[] $types
     * @return mixed[]
     */
    private function getAnyOf(array $types)
    {
        $anyOf = [];

        foreach ($types as $type) {
            if (false === is_string($type) && false === is_array($type)) {
                continue;
            }
            $anyOf['anyOf'][] = $this->getAnyOfType($type);
        }

        return $anyOf;
    }

    /**
     * @param string|mixed[] $type
     * @return mixed[]|string[]
     */
    private function getAnyOfType($type)
    {
        if (true === is_string($type)) {
            return ['type' => $type];
        } else {
            return $this->convertAvro($type);
        }
    }

    /**
     * @param mixed[] $fieldTypes
     * @return bool
     */
    private function isBasicTypeArray(array $fieldTypes): bool
    {
        foreach ($fieldTypes as $type) {
            if (false === is_string($type) && false === is_array($type)) {
                continue;
            }
            if (false === $this->isBasicType($type)) {
                return false;
            }
        }

        return true;
    }

    /**
     * @param string|mixed[] $type
     * @return bool
     */
    private function isBasicType($type)
    {
        if (false === is_string($type)) {
            return false;
        }

        return isset(Avro::BASIC_TYPES[$type]);
    }

    private function snakeToPascal(string $input): string
    {
        return str_replace(' ', '', ucwords(str_replace('_', ' ', $input)));
    }

    private function fixAvroTypes(string $rawJson): string
    {
        $json = str_replace('"int"', '"integer"', $rawJson);
        $json = str_replace('"long"', '"number"', $json);
        $json = str_replace('"float"', '"number"', $json);
        $json = str_replace('"double"', '"number"', $json);
        $json = str_replace('"bytes"', '"string"', $json);

        return $json;
    }
}
