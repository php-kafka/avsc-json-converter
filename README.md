# Avsc Json Schema converter
Converts an Avsc schema into a JSON schema

## Installation
```bash
composer require php-kafka/avsc-json-converter
```

## Usage
Convert a folder with avsc files into json schema:
```bash
./bin/console convert:avsc-to-json avscFolder jsonOutputFolder
```
### Options
- `--convertOnlyValueSchema` only convert avsc files that end with `value.avsc`
- `--noDefaultAsRequired` only mark fields with no defaults as required instead of all

## Known issues
This library is very experimental and has the following open [issues / tasks](https://github.com/php-kafka/avsc-json-converter/issues)