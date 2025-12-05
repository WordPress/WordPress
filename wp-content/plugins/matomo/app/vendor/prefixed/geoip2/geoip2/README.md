# GeoIP2 PHP API #

## Description ##

This package provides an API for the GeoIP2 and GeoLite2
[web services](https://dev.maxmind.com/geoip/docs/web-services?lang=en) and
[databases](https://dev.maxmind.com/geoip/docs/databases?lang=en).

## Install via Composer ##

We recommend installing this package with [Composer](https://getcomposer.org/).

### Download Composer ###

To download Composer, run in the root directory of your project:

```bash
curl -sS https://getcomposer.org/installer | php
```

You should now have the file `composer.phar` in your project directory.

### Install Dependencies ###

Run in your project root:

```sh
php composer.phar require geoip2/geoip2:~2.0
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

## Install via Phar ##

Although we strongly recommend using Composer, we also provide a
[phar archive](https://php.net/manual/en/book.phar.php) containing most of the
dependencies for GeoIP2. Our latest phar archive is available on
[our releases page](https://github.com/maxmind/GeoIP2-php/releases).

### Install Dependencies ###

In order to use the phar archive, you must have the PHP
[Phar extension](https://php.net/manual/en/book.phar.php) installed and
enabled.

If you will be making web service requests, you must have the PHP
[cURL extension](https://php.net/manual/en/book.curl.php)
installed to use this archive. For Debian based distributions, this can
typically be found in the the `php-curl` package. For other operating
systems, please consult the relevant documentation. After installing the
extension you may need to restart your web server.

If you are missing this extension, you will see errors like the following:

```
PHP Fatal error:  Uncaught Error: Call to undefined function MaxMind\WebService\curl_version()
```

### Require Package ###

To use the archive, just require it from your script:

```php
require 'geoip2.phar';
```

## Optional C Extension ##

The [MaxMind DB API](https://github.com/maxmind/MaxMind-DB-Reader-php)
includes an optional C extension that you may install to dramatically increase
the performance of lookups in GeoIP2 or GeoLite2 databases. To install, please
follow the instructions included with that API.

The extension has no effect on web-service lookups.

## IP Geolocation Usage ##

IP geolocation is inherently imprecise. Locations are often near the center of
the population. Any location provided by a GeoIP2 database or web service
should not be used to identify a particular address or household.

## Database Reader ##

### Usage ###

To use this API, you must create a new `\GeoIp2\Database\Reader` object with
the path to the database file as the first argument to the constructor. You
may then call the method corresponding to the database you are using.

If the lookup succeeds, the method call will return a model class for the
record in the database. This model in turn contains multiple container
classes for the different parts of the data such as the city in which the
IP address is located.

If the record is not found, a `\GeoIp2\Exception\AddressNotFoundException`
is thrown. If the database is invalid or corrupt, a
`\MaxMind\Db\InvalidDatabaseException` will be thrown.

See the API documentation for more details.

### City Example ###

```php
<?php
require_once 'vendor/autoload.php';
use GeoIp2\Database\Reader;

// This creates the Reader object, which should be reused across
// lookups.
$reader = new Reader('/usr/local/share/GeoIP/GeoIP2-City.mmdb');

// Replace "city" with the appropriate method for your database, e.g.,
// "country".
$record = $reader->city('128.101.101.101');

print($record->country->isoCode . "\n"); // 'US'
print($record->country->name . "\n"); // 'United States'
print($record->country->names['zh-CN'] . "\n"); // '美国'

print($record->mostSpecificSubdivision->name . "\n"); // 'Minnesota'
print($record->mostSpecificSubdivision->isoCode . "\n"); // 'MN'

print($record->city->name . "\n"); // 'Minneapolis'

print($record->postal->code . "\n"); // '55455'

print($record->location->latitude . "\n"); // 44.9733
print($record->location->longitude . "\n"); // -93.2323

print($record->traits->network . "\n"); // '128.101.101.101/32'

```

### Anonymous IP Example ###

```php
<?php
require_once 'vendor/autoload.php';
use GeoIp2\Database\Reader;

// This creates the Reader object, which should be reused across
// lookups.
$reader = new Reader('/usr/local/share/GeoIP/GeoIP2-Anonymous-IP.mmdb');

$record = $reader->anonymousIp('128.101.101.101');

if ($record->isAnonymous) { print "anon\n"; }
print($record->ipAddress . "\n"); // '128.101.101.101'
print($record->network . "\n"); // '128.101.101.101/32'

```

### Connection-Type Example ###

```php
<?php
require_once 'vendor/autoload.php';
use GeoIp2\Database\Reader;

// This creates the Reader object, which should be reused across
// lookups.
$reader = new Reader('/usr/local/share/GeoIP/GeoIP2-Connection-Type.mmdb');

$record = $reader->connectionType('128.101.101.101');

print($record->connectionType . "\n"); // 'Corporate'
print($record->ipAddress . "\n"); // '128.101.101.101'
print($record->network . "\n"); // '128.101.101.101/32'

```

### Domain Example ###

```php
<?php
require_once 'vendor/autoload.php';
use GeoIp2\Database\Reader;

// This creates the Reader object, which should be reused across
// lookups.
$reader = new Reader('/usr/local/share/GeoIP/GeoIP2-Domain.mmdb');

$record = $reader->domain('128.101.101.101');

print($record->domain . "\n"); // 'umn.edu'
print($record->ipAddress . "\n"); // '128.101.101.101'
print($record->network . "\n"); // '128.101.101.101/32'

```

### Enterprise Example ###

```php
<?php
require_once 'vendor/autoload.php';
use GeoIp2\Database\Reader;

// This creates the Reader object, which should be reused across
// lookups.
$reader = new Reader('/usr/local/share/GeoIP/GeoIP2-Enterprise.mmdb');

// Use the ->enterprise method to do a lookup in the Enterprise database
$record = $reader->enterprise('128.101.101.101');

print($record->country->confidence . "\n"); // 99
print($record->country->isoCode . "\n"); // 'US'
print($record->country->name . "\n"); // 'United States'
print($record->country->names['zh-CN'] . "\n"); // '美国'

print($record->mostSpecificSubdivision->confidence . "\n"); // 77
print($record->mostSpecificSubdivision->name . "\n"); // 'Minnesota'
print($record->mostSpecificSubdivision->isoCode . "\n"); // 'MN'

print($record->city->confidence . "\n"); // 60
print($record->city->name . "\n"); // 'Minneapolis'

print($record->postal->code . "\n"); // '55455'

print($record->location->accuracyRadius . "\n"); // 50
print($record->location->latitude . "\n"); // 44.9733
print($record->location->longitude . "\n"); // -93.2323

print($record->traits->network . "\n"); // '128.101.101.101/32'

```

### ISP Example ###

```php
<?php
require_once 'vendor/autoload.php';
use GeoIp2\Database\Reader;

// This creates the Reader object, which should be reused across
// lookups.
$reader = new Reader('/usr/local/share/GeoIP/GeoIP2-ISP.mmdb');

$record = $reader->isp('128.101.101.101');

print($record->autonomousSystemNumber . "\n"); // 217
print($record->autonomousSystemOrganization . "\n"); // 'University of Minnesota'
print($record->isp . "\n"); // 'University of Minnesota'
print($record->organization . "\n"); // 'University of Minnesota'

print($record->ipAddress . "\n"); // '128.101.101.101'
print($record->network . "\n"); // '128.101.101.101/32'

```

## Database Updates ##

You can keep your databases up to date with our
[GeoIP Update program](https://github.com/maxmind/geoipupdate/releases).
[Learn more about GeoIP Update on our developer
portal.](https://dev.maxmind.com/geoip/updating-databases?lang=en)

There is also a third-party tool for updating databases using PHP and
Composer. MaxMind does not offer support for this tool or maintain it.
[Learn more about the Geoip2 Update tool for PHP and Composer on its
GitHub page.](https://github.com/tronovav/geoip2-update)

## Web Service Client ##

### Usage ###

To use this API, you must create a new `\GeoIp2\WebService\Client`
object with your `$accountId` and `$licenseKey`:

```php
$client = new Client(42, 'abcdef123456');
```

You may also call the constructor with additional arguments. The third argument
specifies the language preferences when using the `->name` method on the model
classes that this client creates. The fourth argument is additional options
such as `host` and `timeout`.

For instance, to call the GeoLite2 web service instead of the GeoIP2 web
service:

```php
$client = new Client(42, 'abcdef123456', ['en'], ['host' => 'geolite.info']);
```

After creating the client, you may now call the method corresponding to a
specific endpoint with the IP address to look up, e.g.:

```php
$record = $client->city('128.101.101.101');
```

If the request succeeds, the method call will return a model class for the
endpoint you called. This model in turn contains multiple record classes, each
of which represents part of the data returned by the web service.

If there is an error, a structured exception is thrown.

See the API documentation for more details.

### Example ###

```php
<?php
require_once 'vendor/autoload.php';
use GeoIp2\WebService\Client;

// This creates a Client object that can be reused across requests.
// Replace "42" with your account ID and "license_key" with your license
// key. Set the "host" to "geolite.info" in the fourth argument options
// array to use the GeoLite2 web service instead of the GeoIP2 web
// service.
$client = new Client(42, 'abcdef123456');

// Replace "city" with the method corresponding to the web service that
// you are using, e.g., "country", "insights".
$record = $client->city('128.101.101.101');

print($record->country->isoCode . "\n"); // 'US'
print($record->country->name . "\n"); // 'United States'
print($record->country->names['zh-CN'] . "\n"); // '美国'

print($record->mostSpecificSubdivision->name . "\n"); // 'Minnesota'
print($record->mostSpecificSubdivision->isoCode . "\n"); // 'MN'

print($record->city->name . "\n"); // 'Minneapolis'

print($record->postal->code . "\n"); // '55455'

print($record->location->latitude . "\n"); // 44.9733
print($record->location->longitude . "\n"); // -93.2323

print($record->traits->network . "\n"); // '128.101.101.101/32'

```

## Values to use for Database or Array Keys ##

**We strongly discourage you from using a value from any `names` property as
a key in a database or array.**

These names may change between releases. Instead we recommend using one of the
following:

* `GeoIp2\Record\City` - `$city->geonameId`
* `GeoIp2\Record\Continent` - `$continent->code` or `$continent->geonameId`
* `GeoIp2\Record\Country` and `GeoIp2\Record\RepresentedCountry` -
  `$country->isoCode` or `$country->geonameId`
* `GeoIp2\Record\Subdivision` - `$subdivision->isoCode` or `$subdivision->geonameId`

### What data is returned? ###

While many of the end points return the same basic records, the attributes
which can be populated vary between end points. In addition, while an end
point may offer a particular piece of data, MaxMind does not always have every
piece of data for any given IP address.

Because of these factors, it is possible for any end point to return a record
where some or all of the attributes are unpopulated.

See the
[GeoIP2 web service docs](https://dev.maxmind.com/geoip/docs/web-services?lang=en)
for details on what data each end point may return.

The only piece of data which is always returned is the `ipAddress`
attribute in the `GeoIp2\Record\Traits` record.

## Integration with GeoNames ##

[GeoNames](https://www.geonames.org/) offers web services and downloadable
databases with data on geographical features around the world, including
populated places. They offer both free and paid premium data. Each
feature is unique identified by a `geonameId`, which is an integer.

Many of the records returned by the GeoIP2 web services and databases
include a `geonameId` property. This is the ID of a geographical feature
(city, region, country, etc.) in the GeoNames database.

Some of the data that MaxMind provides is also sourced from GeoNames. We
source things like place names, ISO codes, and other similar data from
the GeoNames premium data set.

## Reporting data problems ##

If the problem you find is that an IP address is incorrectly mapped,
please
[submit your correction to MaxMind](https://www.maxmind.com/en/correction).

If you find some other sort of mistake, like an incorrect spelling,
please check the [GeoNames site](https://www.geonames.org/) first. Once
you've searched for a place and found it on the GeoNames map view, there
are a number of links you can use to correct data ("move", "edit",
"alternate names", etc.). Once the correction is part of the GeoNames
data set, it will be automatically incorporated into future MaxMind
releases.

If you are a paying MaxMind customer and you're not sure where to submit
a correction, please
[contact MaxMind support](https://www.maxmind.com/en/support) for help.

## Other Support ##

Please report all issues with this code using the
[GitHub issue tracker](https://github.com/maxmind/GeoIP2-php/issues).

If you are having an issue with a MaxMind service that is not specific
to the client API, please see
[our support page](https://www.maxmind.com/en/support).

## Requirements  ##

This library requires PHP 7.2 or greater.

This library also relies on the [MaxMind DB Reader](https://github.com/maxmind/MaxMind-DB-Reader-php).

## Contributing ##

Patches and pull requests are encouraged. All code should follow the PSR-2
style guidelines. Please include unit tests whenever possible. You may obtain
the test data for the maxmind-db folder by running `git submodule update
--init --recursive` or adding `--recursive` to your initial clone, or from
https://github.com/maxmind/MaxMind-DB

## Versioning ##

The GeoIP2 PHP API uses [Semantic Versioning](https://semver.org/).

## Copyright and License ##

This software is Copyright (c) 2013-2020 by MaxMind, Inc.

This is free software, licensed under the Apache License, Version 2.0.
