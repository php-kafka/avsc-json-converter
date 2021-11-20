<?php

namespace PhpKafka\AvscJsonConverter\Converter;

use PhpKafka\AvscJsonConverter\Avro\Avro;

class AvscToJson implements ConverterInterface
{
    private array $options;

    public function convert(string $avscSchema, array $options): string
    {
        $this->options = $options;
        $avscArray = json_decode($avscSchema, true);
        $jsonArray = $this->convertAvro($avscArray);
        $rawJson = json_encode($jsonArray);
        $json = $this->fixAvroTypes($rawJson);

        return $json;
    }

    private function convertAvro(array $avscArray): array
    {
        $jsonArray = [];

        foreach ($avscArray as $key => $value) {
            if ('type' === $key && 'record' === $value) {
                $jsonArray[$key] = 'object';
            }
            if ('type' === $key && 'array' === $value) {
                $jsonArray[$key] = $value;

                if (
                    true === $this->isBasicType($avscArray['items'])
                    || (true === is_array($avscArray['items']) && true === $this->isBasicTypeArray($avscArray['items']))
                ) {
                    $jsonArray['items'] = $avscArray['items'];
                } elseif (true === isset($avscArray['items']['type']) && 'record' === $avscArray['items']['type']) {
                    $jsonArray['items'] = $this->convertAvro($avscArray['items']);
                } else {
                    $jsonArray['items'] = $this->getAnyOf($avscArray['items']);
                }
            }
            if ('name' === $key) {
                $jsonArray['title'] = $this->snakeToPascal($value);
            }
            if ('fields' === $key) {
                $jsonArray['properties'] = $this->convertAvroFieldsToJsonFields($value);
                $requiredFields = $this->getRequiredFields($value);

                if ([] !== $requiredFields) {
                    $jsonArray['required'] = $requiredFields;
                }
            }
        }

        return $jsonArray;
    }

    private function convertAvroFieldsToJsonFields(array $avroFields): array
    {
        $fields = [];

        foreach ($avroFields as $field) {
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

    private function getRequiredFields(array $avroFields): array
    {
        $requiredFields = [];

        foreach ($avroFields as $field) {
            if (
                true === $this->options['markNoDefaultAsRequired'] && false === array_key_exists('default', $field)
            ) {
                $requiredFields[] = $field['name'];
            } elseif (false === $this->options['markNoDefaultAsRequired']) {
                $requiredFields[] = $field['name'];
            }
        }

        return $requiredFields;
    }

    private function getAnyOf(array $types)
    {
        $anyOf = [];

        foreach ($types as $type) {
            $anyOf['anyOf'][] = $this->getAnyOfType($type);
        }

        return $anyOf;
    }

    private function getAnyOfType($type)
    {
        if (true === is_string($type)) {
            return ['type' => $type];
        } else {
            return $this->convertAvro($type);
        }
    }

    private function isBasicTypeArray(array $fieldTypes): bool
    {
        foreach ($fieldTypes as $type) {
            if (false === $this->isBasicType($type)) {
                return false;
            }
        }

        return true;
    }

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

    private function fixAvroTypes(string $rawJson)
    {
        $json = str_replace('int', 'integer', $rawJson);
        $json = str_replace('long', 'number', $json);
        $json = str_replace('float', 'number', $json);
        $json = str_replace('double', 'number', $json);
        $json = str_replace('bytes', 'string', $json);
        return $json;
    }
}
