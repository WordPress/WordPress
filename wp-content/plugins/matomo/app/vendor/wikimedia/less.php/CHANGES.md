# Changelog

## 3.2.1

* [All changes](https://gerrit.wikimedia.org/g/mediawiki/libs/less.php/+log/v3.2.1)
* Tree_Ruleset: Fix support for nested parent selectors (Timo Tijhof) [T204816](https://phabricator.wikimedia.org/T204816)
* Fix ParseError when interpolating variable after colon in selector (Timo Tijhof) [T327163](https://phabricator.wikimedia.org/T327163)
* Functions: Fix "Undefined property" warning on bad minmax arg
* Tree_Call: Include previous exception when catching functions (Robert Frunzke)

## 3.2.0

* [All changes](https://github.com/wikimedia/less.php/compare/v3.1.0...v3.2.0)
* Fix "Implicit conversion" PHP 8.1 warnings (Ayokunle Odusan)
* Fix "Creation of dynamic property" PHP 8.2 warnings (Bas Couwenberg)
* Fix "Creation of dynamic property" PHP 8.2 warnings (Rajesh Kumar)
* Tree_Url: Add support for "Url" type to `Parser::getVariables()` (ciroarcadio) [#51](https://github.com/wikimedia/less.php/pull/51)
* Tree_Import: Add support for importing URLs without file extension (Timo Tijhof) [#27](https://github.com/wikimedia/less.php/issues/27)

## 3.1.0

* [All changes](https://github.com/wikimedia/less.php/compare/v3.0.0...v3.1.0)
* Add PHP 8.0 support: Drop use of curly braces for sub-string eval (James D. Forrester)
* Make `Directive::__construct` $rules arg optional (fix PHP 7.4 warning) (Sam Reed)
* ProcessExtends: Improve performance by using a map for selectors and parents (Andrey Legayev)

## 3.0.0

* [All changes](https://github.com/wikimedia/less.php/compare/v2.0.0...v3.0.0)
* Raise PHP requirement from 7.1 to 7.2.9 (James Forrester)

## 2.0.0

* [All changes](https://github.com/wikimedia/less.php/compare/v1.8.2...v2.0.0)
* Relax PHP requirement down to 7.1, from 7.2.9 (Franz Liedke)
* Reflect recent breaking changes properly with the semantic versioning (James Forrester)

## 1.8.2

* [All changes](https://github.com/wikimedia/less.php/compare/v1.8.1...v1.8.2)
* Require PHP 7.2.9+, up from 5.3+ (James Forrester)
* release: Update Version.php with the current release ID (COBadger)
* Fix access array offset on value of type null (Michele Locati)
* Fix test suite on PHP 7.4 (Sergei Morozov)

## 1.8.1

* [All changes](https://github.com/wikimedia/less.php/compare/v1.8.0...v1.8.1)
* Another PHP 7.3 compatibility tweak

## 1.8.0

Library forked by Wikimedia, from [oyejorge/less.php](https://github.com/oyejorge/less.php).

* [All changes](https://github.com/wikimedia/less.php/compare/v1.7.0.13...v1.8.0)
* Supports up to PHP 7.3
* No longer tested against PHP 5, though it's still remains allowed in `composer.json` for HHVM compatibility
* Switched to [semantic versioning](https://semver.org/), hence version numbers now use 3 digits

## 1.7.0.13

* [All changes](https://github.com/wikimedia/less.php/compare/v1.7.0.12...v1.7.0.13)
* Fix composer.json (PSR-4 was invalid)

## 1.7.0.12

* [All changes](https://github.com/wikimedia/less.php/compare/v1.7.0.11...v1.7.0.12)
* set bin/lessc bit executable
* Add `gettingVariables` method to `Less_Parser`

## 1.7.0.11

* [All changes](https://github.com/wikimedia/less.php/compare/v1.7.0.10...v1.7.0.11)
* Fix realpath issue (windows)
* Set Less_Tree_Call property back to public ( Fix 258 266 267 issues from oyejorge/less.php)

## 1.7.0.10

* [All changes](https://github.com/wikimedia/less.php/compare/v1.7.0.9...v1.7.10)
* Add indentation option
* Add `optional` modifier for `@import`
* Fix $color in Exception messages
* take relative-url into account when building the cache filename
* urlArgs should be string no array()
* fix missing on NameValue type [#269](https://github.com/oyejorge/less.php/issues/269)

## 1.7.0.9

* [All changes](https://github.com/wikimedia/less.php/compare/v1.7.0.8...v1.7.0.9)
* Remove space at beginning of Version.php
* Revert require() paths in test interface
