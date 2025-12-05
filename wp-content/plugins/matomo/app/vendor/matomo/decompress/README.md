# Matomo/Decompress

Component providing several adapters to decompress files.

[![Build Status](https://travis-ci.org/matomo-org/component-decompress.svg?branch=master)](https://travis-ci.org/matomo-org/component-decompress)

It supports the following compression formats:

- Zip
- Gzip
- Bzip
- Tar (gzip or bzip)

With the following adapters:

- `PclZip`, based on the [PclZip library](http://www.phpconcept.net/pclzip/)
- `ZipArchive`, based on PHP's [Zip extension](http://fr.php.net/manual/en/book.zip.php)
- `Gzip`, based on PHP's native Gzip functions
- `Bzip`, based on PHP's native Bzip functions
- `Tar`, based on the [Archive_Tar library](https://github.com/pear/Archive_Tar) from PEAR

## Installation

With Composer:

```json
{
    "require": {
        "matomo/decompress": "*"
    }
}
```

## Usage

All adapters have the same API as they implement `Matomo\Decompress\DecompressInterface`:

```php
$extractor = new \Matomo\Decompress\Gzip('file.gz');

$extractedFiles = $extractor->extract('some/directory');

if ($extractedFiles === 0) {
    echo $extractor->errorInfo();
}
```

## License

The Decompress component is released under the [LGPL v3.0](http://choosealicense.com/licenses/lgpl-3.0/).
