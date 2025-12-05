# MaxMind DB Reader PHP API #

## Description ##

This is the PHP API for reading MaxMind DB files. MaxMind DB is a binary file
format that stores data indexed by IP address subnets (IPv4 or IPv6).

## Installation (Composer) ##

We recommend installing this package with [Composer](https://getcomposer.org/).

### Download Composer ###

To download Composer, run in the root directory of your project:

```bash
curl -sS https://getcomposer.org/installer | php
```

You should now have the file `composer.phar` in your project directory.

### Install Dependencies ###

Run in your project root:

```
php composer.phar require maxmind-db/reader:~1.0
```

You should now have the files `composer.json` and `composer.lock` as well as
the directory `vendor` in your project directory. If you use a version control
system, `composer.json` should be added to it.

### Require Autoloader ###

After installing the dependencies, you need to require the Composer autoloader
from your code:

```php
require 'vendor/autoload.php';
```

## Installation (Standalone) ##

If you don't want to use Composer for some reason, a custom
`autoload.php` is provided for you in the project root. To use the
library, simply include that file,

```php
require('/path/to/MaxMind-DB-Reader-php/autoload.php');
```

and then instantiate the reader class normally:

```php
use MaxMind\Db\Reader;
$reader = new Reader('example.mmdb');
```

## Installation (RPM)

RPMs are available in the [official Fedora repository](https://apps.fedoraproject.org/packages/php-maxminddb).

To install on Fedora, run:

```bash
dnf install php-maxminddb
```

To install on CentOS or RHEL 7, first [enable the EPEL repository](https://fedoraproject.org/wiki/EPEL)
and then run:

```bash
yum install php-maxminddb
```

Please note that these packages are *not* maintained by MaxMind.

## Usage ##

## Example ##

```php
<?php
require_once 'vendor/autoload.php';

use MaxMind\Db\Reader;

$ipAddress = '24.24.24.24';
$databaseFile = 'GeoIP2-City.mmdb';

$reader = new Reader($databaseFile);

// get returns just the record for the IP address
print_r($reader->get($ipAddress));

// getWithPrefixLen returns an array containing the record and the
// associated prefix length for that record.
print_r($reader->getWithPrefixLen($ipAddress));

$reader->close();
```

## Optional PHP C Extension ##

MaxMind provides an optional C extension that is a drop-in replacement for
`MaxMind\Db\Reader`. In order to use this extension, you must install the
Reader API as described above and install the extension as described below. If
you are using an autoloader, no changes to your code should be necessary.

### Installing Extension ###

First install [libmaxminddb](https://github.com/maxmind/libmaxminddb) as
described in its [README.md
file](https://github.com/maxmind/libmaxminddb/blob/main/README.md#installing-from-a-tarball).
After successfully installing libmaxmindb, you may install the extension
from [pecl](https://pecl.php.net/package/maxminddb):

```
pecl install maxminddb
```

Alternatively, you may install it from the source. To do so, run the following
commands from the top-level directory of this distribution:

```
cd ext
phpize
./configure
make
make test
sudo make install
```

You then must load your extension. The recommended method is to add the
following to your `php.ini` file:

```
extension=maxminddb.so
```

Note: You may need to install the PHP development package on your OS such as
php5-dev for Debian-based systems or php-devel for RedHat/Fedora-based ones.

## 128-bit Integer Support ##

The MaxMind DB format includes 128-bit unsigned integer as a type. Although
no MaxMind-distributed database currently makes use of this type, both the
pure PHP reader and the C extension support this type. The pure PHP reader
requires gmp or bcmath to read databases with 128-bit unsigned integers.

The integer is currently returned as a hexadecimal string (prefixed with "0x")
by the C extension and a decimal string (no prefix) by the pure PHP reader.
Any change to make the reader implementations always return either a
hexadecimal or decimal representation of the integer will NOT be considered a
breaking change.

## Support ##

Please report all issues with this code using the [GitHub issue tracker](https://github.com/maxmind/MaxMind-DB-Reader-php/issues).

If you are having an issue with a MaxMind service that is not specific to the
client API, please see [our support page](https://www.maxmind.com/en/support).

## Requirements  ##

This library requires PHP 7.2 or greater.

The GMP or BCMath extension may be required to read some databases
using the pure PHP API.

## Contributing ##

Patches and pull requests are encouraged. All code should follow the PSR-1 and
PSR-2 style guidelines. Please include unit tests whenever possible.

## Versioning ##

The MaxMind DB Reader PHP API uses [Semantic Versioning](https://semver.org/).

## Copyright and License ##

This software is Copyright (c) 2014-2024 by MaxMind, Inc.

This is free software, licensed under the Apache License, Version 2.0.
