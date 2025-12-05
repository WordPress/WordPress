# Matomo/Ini

Read and write INI configurations.

[![Build Status](https://travis-ci.com/matomo-org/component-ini.svg?branch=master)](https://travis-ci.com/matomo-org/component-ini)
[![Latest Version](https://img.shields.io/github/release/matomo-org/component-ini.svg?style=flat-square)](https://packagist.org/packages/matomo/component-ini)
[![](https://img.shields.io/packagist/dm/matomo/ini.svg?style=flat-square)](https://packagist.org/packages/matomo/ini)

## Installation

```json
composer require matomo/ini
```

## Why?

PHP provides a `parse_ini_file()` function to read INI files.

This component provides the following benefits over the built-in function:

- allows to write INI files
- classes can be used with dependency injection and mocked in unit tests
- throws exceptions instead of PHP errors
- better type supports:
  - parses boolean values (`true`/`false`, `on`/`off`, `yes`/`no`) to real PHP booleans ([instead of strings `"1"` and `""`](http://3v4l.org/JuvOT))
  - parses null to PHP `null` ([instead of an empty string](http://3v4l.org/KSoj2))
- works even if `parse_ini_file()` or `parse_ini_string()` is disabled in `php.ini` by falling back on an alternate implementation (can happen on some shared hosts)

## Usage

### Read

```php
$reader = new IniReader();

// Read a string
$array = $reader->readString($string);

// Read a file
$array = $reader->readFile('config.ini');
```

#### Troubleshooting

**unexpected BOOL_TRUE in Unknown on line X**

The PHP default implementation of read_ini_file does not allow bool-ish values as keys in when reading ini files.

Data like `yes = "Yes"` results in the following error:

```
Syntax error in INI configuration: syntax error, unexpected BOOL_TRUE in Unknown on line 6
```

To prevent from that error, please switch to the custom ini reader implementation by using:

```php
$reader = new IniReader();
$reader->setUseNativeFunction(false);
```


### Write

```php
$writer = new IniWriter();

// Write to a string
$string = $writer->writeToString($array);

// Write to a file
$writer->writeToFile('config.ini', $array);
```

## License

The Ini component is released under the [LGPL v3.0](http://choosealicense.com/licenses/lgpl-3.0/).

## Contributing

To run the unit tests:

```
vendor/bin/phpunit
```

To run the performance tests:

```
php vendor/bin/phpbench run tests/PerformanceTest --report=default
```
