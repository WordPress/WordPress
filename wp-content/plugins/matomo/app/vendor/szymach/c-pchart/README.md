Table of contents:
==================
* [Support](#support)
* [Build status](#build-status)
* [Code quality](#code-quality)
* [About](#about)
* [License](#license)
* [Contributing](#contributing)
* [Installation](#installation-via-composer)
* [Usage](#usage)
    - [Charts created through Image class](#charts-created-through-image-class)
    - [Standalone charts](#standalone-charts)
    - [Barcodes](#barcodes)
    - [Cache](#cache)
    - [Fonts and palletes](#fonts-and-palletes)
* [Changelog](#changelog)
* [References](#references)
* [Links](#links)

Support:
========

This project is supported in a basic manner and no new features will be introduced.
Issues and pull requests will be reviewed and resolved if need be, so feel free
to post them.

Build status:
=============
- [![Build Status](https://app.travis-ci.com/szymach/c-pchart.svg?branch=master)](https://app.travis-ci.com/szymach/c-pchart) master
- [![Build Status](https://app.travis-ci.com/szymach/c-pchart.svg?branch=3.0)](https://app.travis-ci.com/szymach/c-pchart) 3.0
- [![Build Status](https://app.travis-ci.com/szymach/c-pchart.svg?branch=2.0)](https://app.travis-ci.com/szymach/c-pchart) 2.0

About:
======

This library is a port of the excellent pChart statistics library created by Jean-Damien Pogolotti,
and aims to allow the usage of it in modern applications. This was done through
applying PSR standards to code, introducing namespaces and typehints, along with
some basic annotations to methods.

This is the `3.x` version, which removes the factory service and reorganizes the
file structure a bit. It does not introduce any new features, but the changes are
not compatibile with the `2.x` branch. BC compatibility with the original library
is mostly retained, however you can still use the `1.x` version if you cannot risk
any of these.

What was done:

- Support for PHP versions from 5.4 to 8.1.

- Made a full port of the library's functionality. I have touched very little of
the actual logic, so most code from the original library should work.

- Defined and added namespaces to all classes.

- Replaced all `exit()` / `die()` commands with `throw` statements.

- Refactored the code to meet PSR-2 standard and added annotations (as best as I could figure them out)
to methods Also, typehinting was added to methods where possible, so some backwards compatibility breaks
may occur if you did some weird things.

- Moved all constants to a [single file](constants.php). It is loaded automatically
through Composer, so no need for manual action.

License:
========

It was previously stated that this package uses the [MIT](https://opensource.org/licenses/MIT) license,
which did not meet the requirements set by the original author. It is now under the
[GNU GPL v3](http://www.gnu.org/licenses/gpl-3.0.html) license, so if you wish to
use it in a commercial project, you need to pay an [appropriate fee](http://www.pchart.net/license).

Contributing:
=============

All in all, this is a legacy library ported over from PHP 4, so the code is neither
beautiful nor easy to understand. I did my best to modernize and cover it with
some basic tests, but there is much more that could be done. If you are willing and
have time to fix or improve anything, feel free to post a PR or issue.

Installation (via Composer):
============================

For composer installation, add:

```json
"require": {
    "szymach/c-pchart": "^3.0"
},
```

to your composer.json file and update your dependencies. Or you can run:

```sh
$ composer require szymach/c-pchart
```

in your project's root directory.

Usage:
======

Your best source to understanding how to use the library is still the [official wiki](http://wiki.pchart.net/).
However, I have ported at least one example for each chart into Markdown files,
so you can compare each version and figure out how to use the current implementation.

Charts created through Image class
---------------------------------------

Most of the basic charts are created through methods of the `CpChart\Image`
class. Below you can find a full list of these charts, alongside example code.

- [area](resources/doc/area.md)
- [bar](resources/doc/bar.md)
- [best fit](resources/doc/best_fit.md)
- [filled spline](resources/doc/filled_spline.md)
- [filled step](resources/doc/filled_step.md)
- [line](resources/doc/line.md)
- [plot](resources/doc/plot.md)
- [progress](resources/doc/progress.md)
- [spline](resources/doc/spline.md)
- [stacked area](resources/doc/stacked_area.md)
- [stacked bar](resources/doc/stacked_bar.md)
- [step](resources/doc/step.md)
- [zone](resources/doc/zone.md)

Standalone charts:
------------------------------------

The more advanced charts have their own separate class you need to use in order
to create them. As before, below is a full list of these, with example code.

- [2D pie](resources/doc/2d_pie.md)
- [3D pie](resources/doc/3d_pie.md)
- [2D ring](resources/doc/2d_ring.md)
- [3D ring](resources/doc/3d_ring.md)
- [bubble](resources/doc/bubble.md)
- [contour](resources/doc/contour.md)
- [polar](resources/doc/polar.md)
- [radar](resources/doc/radar.md)
- [scatter best fit](resources/doc/scatter_best_fit.md)
- [scatter line](resources/doc/scatter_line.md)
- [scatter plot](resources/doc/scatter_plot.md)
- [scatter spline](resources/doc/scatter_spline.md)
- [scatter threshold](resources/doc/scatter_threshold.md)
- [scatter threshold area](resources/doc/scatter_threshold_area.md)
- [split path](resources/doc/split_path.md)
- [spring](resources/doc/spring.md)
- [stock](resources/doc/stock.md)
- [surface](resources/doc/surface.md)

Barcodes
--------

The pChart library also provides a way to render barcodes 39 and 128. Below you
can find links to doc on creating them:

- [barcode39](resources/doc/barcode_39.md)
- [barcode128](resources/doc/barcode_128.md)

Cache
-----

If you find yourself creating charts out of a set of data more than once, you may
consider using the cache component of the library. Head on to the [dedicated part](resources/doc/cache.md)
of the documentation for information on how to do that.

Fonts and palletes
------------------

If you want to use any of the fonts or palletes files, provide only
the name of the actual file, do not add the `fonts` or `palettes` folder to the
string given into the function. If you want to load them from a different directory
than the default, you need to add the full path to the file (ex. `__DIR__.'/folder/to/my/palletes`).

References
==========
[The original pChart website](http://www.pchart.net/)

Links
=====

[GitHub](https://github.com/szymach/c-pchart)

[Packagist](https://packagist.org/packages/szymach/c-pchart)
