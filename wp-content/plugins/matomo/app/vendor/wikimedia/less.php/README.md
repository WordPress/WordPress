[![Packagist](https://img.shields.io/packagist/v/wikimedia/less.php.svg?style=flat)](https://packagist.org/packages/wikimedia/less.php)

Less.php
========

This is a PHP port of the [official LESS processor](https://lesscss.org).

## About

The code structure of Less.php mirrors that of upstream Less.js to ensure compatibility and help reduce maintenance. The port is currently compatible with Less.js 2.5.3. Please note that "inline JavaScript expressions" (via eval or backticks) are not supported.

* [API § Caching](./API.md#caching), Less.php includes a file-based cache.
* [API § Source maps](./API.md#source-maps), Less.php supports v3 sourcemaps.
* [API § Command line](./API.md#command-line), the `lessc` command includes a watch mode.

## Installation

You can install the library with Composer or standalone.

If you have [Composer](https://getcomposer.org/download/) installed:

1. Run `composer require wikimedia/less.php`
2. Use `Less_Parser` in your code.

Or standalone:

1. [Download Less.php](https://gerrit.wikimedia.org/g/mediawiki/libs/less.php/+archive/HEAD.tar.gz) and upload the PHP files to your server.
2. Include the library:
   ```php
   require_once '[path to]/less.php/lib/Less/Autoloader.php';
   Less_Autoloader::register();
   ```
3. Use `Less_Parser` in your code.

## Security

The LESS processor language is powerful and includes features that may read or embed arbitrary files that the web server has access to, and features that may be computationally exensive if misused.

In general you should treat LESS files as being in the same trust domain as other server-side executables, such as PHP code. In particular, it is not recommended to allow people that use your web service to provide arbitrary LESS code for server-side processing.

_See also [SECURITY](./SECURITY.md)._

## Who uses Less.php?

* **[Wikipedia](https://en.wikipedia.org/wiki/MediaWiki)** and the MediaWiki platform ([docs](https://www.mediawiki.org/wiki/ResourceLoader/Architecture#Resource:_Styles)).
* **[Matomo](https://en.wikipedia.org/wiki/Matomo_(software))** ([docs](https://devdocs.magento.com/guides/v2.4/frontend-dev-guide/css-topics/custom_preprocess.html)).
* **[Magento](https://en.wikipedia.org/wiki/Magento)** as part of Adobe Commerce ([docs](https://developer.matomo.org/guides/asset-pipeline#vanilla-javascript-css-and-less-files)).
* **[Icinga](https://en.wikipedia.org/wiki/Icinga)** in Icinga Web ([docs](https://github.com/Icinga/icingaweb2)).
* **[Shopware](https://de.wikipedia.org/wiki/Shopware)** ([docs](https://developers.shopware.com/designers-guide/less/)).

## Integrations

Less.php has been integrated with various other projects.

#### Transitioning from Leafo/lessphp

If you're looking to transition from the [Leafo/lessphp](https://github.com/leafo/lessphp) library, use the `lessc.inc.php` adapter file that comes with Less.php.

This allows Less.php to be a drop-in replacement for Leafo/lessphp.

[Download Less.php](https://gerrit.wikimedia.org/g/mediawiki/libs/less.php/+archive/HEAD.tar.gz), unzip the files into your project, and include its `lessc.inc.php` instead.

Note: The `setPreserveComments` option is ignored. Less.php already preserves CSS block comments by default, and removes LESS inline comments.

#### Drupal

Less.php can be used with [Drupal's less module](https://drupal.org/project/less) via the `lessc.inc.php` adapter. [Download Less.php](https://gerrit.wikimedia.org/g/mediawiki/libs/less.php/+archive/HEAD.tar.gz) and unzip it so that `lessc.inc.php` is located at `sites/all/libraries/lessphp/lessc.inc.php`, then install the Drupal less module as usual.

#### WordPress

* [wp_enqueue_less](https://github.com/Ed-ITSolutions/wp_enqueue_less) is a Composer package for use in WordPress themes and plugins. It provides a `wp_enqueue_less()` function to automatically manage caching and compilation on-demand, and loads the compressed CSS on the page.
* [JBST framework](https://github.com/bassjobsen/jamedo-bootstrap-start-theme) bundles a copy of Less.php.
* The [lessphp plugin](https://wordpress.org/plugins/lessphp/) bundles a copy of Less.php for use in other plugins or themes. This dependency can also be combined with the [TGM Library](http://tgmpluginactivation.com/).

## Credits

Less.php was originally ported to PHP in 2011 by [Matt Agar](https://github.com/agar) and then updated by [Martin Jantošovič](https://github.com/Mordred) in 2012. From 2013 to 2017, [Josh Schmidt](https://github.com/oyejorge) lead development of the library. Since 2019, the library is maintained by Wikimedia Foundation.
