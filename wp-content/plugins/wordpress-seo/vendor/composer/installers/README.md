# A Multi-Framework [Composer](http://getcomposer.org) Library Installer

[![Build Status](http://img.shields.io/travis/composer/installers.svg)](http://travis-ci.org/composer/installers)

This is for PHP package authors to require in their `composer.json`. It will
install their package to the correct location based on the specified package
type.

The goal of `installers` is to be a simple package type to install path map.
Users can also customize the install path per package and package authors can
modify the package name upon installing.

`installers` isn't intended on replacing all custom installers. If your
package requires special installation handling then by all means, create a
custom installer to handle it.

**Natively Supported Frameworks**:

The following frameworks natively work with Composer and will be
installed to the default `vendor` directory. `composer/installers`
is not needed to install packages with these frameworks:

* Aura
* Symfony2
* Yii
* Yii2

**Current Supported Package Types**:

> Stable types are marked as **bold**, this means that installation paths
> for those type will not be changed. Any adjustment for those types would
> require creation of brand new type that will cover required changes.

| Framework    | Types
| ---------    | -----
| Aimeos       | `aimeos-extension`
| Asgard       | `asgard-module`<br>`asgard-theme`
| AGL          | `agl-module`
| AnnotateCms  | `annotatecms-module`<br>`annotatecms-component`<br>`annotatecms-service`
| Bitrix       | `bitrix-module`<br>`bitrix-component`<br>`bitrix-theme`
| CakePHP 2+   | **`cakephp-plugin`**
| Chef         | `chef-cookbook`<br>`chef-role`
| CCFramework  | `ccframework-ship`<br>`ccframework-theme`
| CodeIgniter  | `codeigniter-library`<br>`codeigniter-third-party`<br>`codeigniter-module`
| concrete5    | `concrete5-block`<br>`concrete5-package`<br>`concrete5-theme`<br>`concrete5-update`
| Craft        | `craft-plugin`
| Croogo       | `croogo-plugin`<br>`croogo-theme`
| DokuWiki     | `dokuwiki-plugin`<br>`dokuwiki-template`
| Dolibarr     | `dolibarr-module`
| Drupal       | <b>`drupal-module`<br>`drupal-theme`</b><br>`drupal-library`<br>`drupal-profile`<br>`drupal-drush`
| Elgg         | `elgg-plugin`
| FuelPHP v1.x | `fuel-module`<br>`fuel-package`<br/>`fuel-theme`
| FuelPHP v2.x | `fuelphp-component`
| Grav         | `grav-plugin`<br>`grav-theme`
| Hurad        | `hurad-plugin`<br>`hurad-theme`
| Joomla       | `joomla-component`<br>`joomla-module`<br>`joomla-template`<br>`joomla-plugin`<br>`joomla-library`
| Kirby        | **`kirby-plugin`**
| Kohana       | **`kohana-module`**
| Laravel      | `laravel-library`
| Lithium      | **`lithium-library`<br>`lithium-source`**
| Magento      | `magento-library`<br>`magento-skin`<br>`magento-theme`
| Mako         | `mako-package`
| MODX Evo     | `modxevo-snippet`<br>`modxevo-plugin`<br>`modxevo-module`<br>`modxevo-template`<br>`modxevo-lib`
| MediaWiki    | `mediawiki-extension`
| October      | **`october-module`<br>`october-plugin`<br>`october-theme`**
| OXID         | `oxid-module`<br>`oxid-theme`<br>`oxid-out`
| MODULEWork   | `modulework-module`
| Moodle       | `moodle-*` (Please [check source](https://raw.githubusercontent.com/composer/installers/master/src/Composer/Installers/MoodleInstaller.php) for all supported types)
| Piwik        | `piwik-plugin`
| phpBB        | `phpbb-extension`<br>`phpbb-style`<br>`phpbb-language`
| Pimcore      | `pimcore-plugin`
| PPI          | **`ppi-module`**
| Puppet       | `puppet-module`
| REDAXO       | `redaxo-addon`
| Roundcube    | `roundcube-plugin`
| shopware     | `shopware-backend-plugin`<br/>`shopware-core-plugin`<br/>`shopware-frontend-plugin`<br/>`shopware-theme`
| SilverStripe | `silverstripe-module`<br>`silverstripe-theme`
| SMF          | `smf-module`<br>`smf-theme`
| symfony1     | **`symfony1-plugin`**
| Tusk         | `tusk-task`<br>`tusk-command`<br>`tusk-asset`
| TYPO3 Flow   | `typo3-flow-package`<br>`typo3-flow-framework`<br>`typo3-flow-plugin`<br>`typo3-flow-site`<br>`typo3-flow-boilerplate`<br>`typo3-flow-build`
| TYPO3 CMS    | `typo3-cms-extension`
| Wolf CMS     | `wolfcms-plugin`
| WordPress    | <b>`wordpress-plugin`<br>`wordpress-theme`</b><br>`wordpress-muplugin`
| Zend         | `zend-library`<br>`zend-extra`<br>`zend-module`
| Zikula       | `zikula-module`<br>`zikula-theme`
| Prestashop   | `prestashop-module`<br>`prestashop-theme`

## Example `composer.json` File

This is an example for a CakePHP plugin. The only important parts to set in your
composer.json file are `"type": "cakephp-plugin"` which describes what your
package is and `"require": { "composer/installers": "~1.0" }` which tells composer
to load the custom installers.

```json
{
    "name": "you/ftp",
    "type": "cakephp-plugin",
    "require": {
        "composer/installers": "~1.0"
    }
}
```

This would install your package to the `Plugin/Ftp/` folder of a CakePHP app
when a user runs `php composer.phar install`.

So submit your packages to [packagist.org](http://packagist.org)!

## Custom Install Paths

If you are consuming a package that uses the `composer/installers` you can
override the install path with the following extra in your `composer.json`:

```json
{
    "extra": {
        "installer-paths": {
            "your/custom/path/{$name}/": ["shama/ftp", "vendor/package"]
        }
    }
}
```

A package type can have a custom installation path with a `type:` prefix.

``` json
{
    "extra": {
        "installer-paths": {
            "your/custom/path/{$name}/": ["type:wordpress-plugin"]
        }
    }
}
```

This would use your custom path for each of the listed packages. The available
variables to use in your paths are: `{$name}`, `{$vendor}`, `{$type}`.

## Custom Install Names

If you're a package author and need your package to be named differently when
installed consider using the `installer-name` extra.

For example you have a package named `shama/cakephp-ftp` with the type
`cakephp-plugin`. Installing with `composer/installers` would install to the
path `Plugin/CakephpFtp`. Due to the strict naming conventions, you as a
package author actually need the package to be named and installed to
`Plugin/Ftp`. Using the following config within your **package** `composer.json`
will allow this:

```json
{
    "name": "shama/cakephp-ftp",
    "type": "cakephp-plugin",
    "extra": {
        "installer-name": "Ftp"
    }
}
```

Please note the name entered into `installer-name` will be the final and will
not be inflected.

## Contribute!

* [Fork and clone](https://help.github.com/articles/fork-a-repo).
* Run the command `php composer.phar install --dev` to install the dev
  dependencies. See [Composer](https://github.com/composer/composer#installation--usage).
* Use the command `phpunit` to run the tests. See [PHPUnit](http://phpunit.de).
* Create a branch, commit, push and send us a
  [pull request](https://help.github.com/articles/using-pull-requests).

To ensure a consistent code base, you should make sure the code follows the
[Coding Standards](http://symfony.com/doc/2.0/contributing/code/standards.html)
which we borrowed from Symfony.

If you would like to help, please take a look at the list of
[issues](https://github.com/composer/installers/issues).

### Should we allow dynamic package types or paths? No.
What are they? The ability for a package author to determine where a package
will be installed either through setting the path directly in their
`composer.json` or through a dynamic package type: `"type":
"framework-install-here"`.

It has been proposed many times. Even implemented once early on and then
removed. `installers` won't do this because it would allow a single package
author to wipe out entire folders without the user's consent. That user would
then come here to yell at us.
