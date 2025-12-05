# Sparkline
PHP Class (using GD) to generate sparklines

[![Build Status](https://app.travis-ci.com/davaxi/Sparkline.svg?branch=master)](https://app.travis-ci.com/davaxi/Sparkline)
[![Latest Stable Version](https://poser.pugx.org/davaxi/sparkline/v/stable)](https://packagist.org/packages/davaxi/sparkline) 
[![Total Downloads](https://poser.pugx.org/davaxi/sparkline/downloads)](https://packagist.org/packages/davaxi/sparkline) 
[![Latest Unstable Version](https://poser.pugx.org/davaxi/sparkline/v/unstable)](https://packagist.org/packages/davaxi/sparkline) 
[![License](https://poser.pugx.org/davaxi/sparkline/license)](https://packagist.org/packages/davaxi/sparkline)
[![Maintainability](https://api.codeclimate.com/v1/badges/9a5da533685204c53989/maintainability)](https://codeclimate.com/github/davaxi/Sparkline/maintainability)
[![Test Coverage](https://api.codeclimate.com/v1/badges/9a5da533685204c53989/test_coverage)](https://codeclimate.com/github/davaxi/Sparkline/test_coverage)
[![Issue Count](https://codeclimate.com/github/davaxi/Sparkline/badges/issue_count.svg)](https://codeclimate.com/github/davaxi/Sparkline)

## Installation

This page contains information about installing the Library for PHP.

### Requirements

- PHP version 7.0.0 or greater
- The GD PHP extension
- The MBString PHP extension

#### Using Composer

You can install the library by adding it as a dependency to your composer.json.

```shell
$ composer require davaxi/sparkline
```

or

```json
  "require": {
    "davaxi/sparkline": "^2.2"
  }
```

#### For PHP >= 5.4.0

Show https://github.com/davaxi/Sparkline/tree/1.2.3

## Usage

Example: 

![Sparkline](https://raw.githubusercontent.com/davaxi/Sparkline/master/tests/data/testGenerate2-mockup.png)

```php
<?php

require '/path/to/sparkline/folder/autoload.php';

$sparkline = new Davaxi\Sparkline();
$sparkline->setData(array(2,4,5,6,10,7,8,5,7,7,11,8,6,9,11,9,13,14,12,16));
$sparkline->display();

?>
```

## Documentation

```php
$sparkline = new Davaxi\Sparkline();

// Change format (Default value 80x20)
$sparkline->setFormat('100x40');
// or 
$sparkline->setWidth(100);
$sparkline->setHeight(40);

// Apply padding
$sparkline->setPadding('10'); // > top: 10 | right: 10 | bottom: 10 | left: 10
$sparkline->setPadding('10 20'); // > top: 10 | right: 20 | bottom: 10 | left: 20
$sparkline->setPadding('10 20 30'); // > top: 10 | right: 20 | bottom: 30 | left: 20
$sparkline->setPadding('10 20 30 40'); // > top: 10 | right: 20 | bottom: 30 | left: 40

// Change background color (Default value #FFFFFF)
$sparkline->setBackgroundColorHex('#0f354b');
// or
$sparkline->setBackgroundColorRGB(15, 53, 75);
// or
$sparkline->deactivateBackgroundColor();

// Change line color (Default value #1388db)
$sparkline->setLineColorHex('#1c628b');
// or
$sparkline->setLineColorRGB(28, 98, 139);

// Change line thickness (Default value 1.75 px)
$sparkline->setLineThickness(2.2);

// Change fill color (Default value #e6f2fa)
$sparkline->setFillColorHex('#8b1c2b');
// or
$sparkline->setFillColorRGB(139, 28, 43);
// or for specific series
$sparkline->deactivateFillColor();
// or for all series
$sparkline->deactivateAllFillColor();

$sparkline->setData(array(.....)); // Set data set
$sparkline->getData(); // Get seted data
$sparkline->generate(); // If ou want regenerate picture 

// Change base of height value (default max($data))
$sparkline->setBase(20);

// Change origin of chart value (yAxis) (default: 0)
$sparkline->setOriginValue(40);

// Add dot on first/last/minimal/maximal value
// Data set before used method
$sparkline->addPoint('minimum', 3, '#efefef');
$sparkline->addPoint('maximum', 3, '#efefef');
$sparkline->addPoint('first', 3, '#efefef');
$sparkline->addPoint('last', 3, '#efefef');

// Or by index
$sparkline->addPoint(1, 3, '#efefef');

// If display
$sparkline->setEtag('your hash'); // If you want add ETag header
$sparkline->setFilename('yourPictureName'); // For filenamen header
$sparkline->setExpire('+1 day'); // If you want add expire header
// or
$sparkline->setExpire(strtotime('+1 day'));
$sparkline->display(); // Display with correctly headers

// If save
$sparkline->save('/your/path/to/save/picture');

$sparkline->destroy(); // Destroy picture after generated / displayed / saved
```

### Multiple sparkline series

```php
<?php

$sparkline = new Davaxi\Sparkline();

// For add series
$sparkline->addSeries([0,1,2,3]);
$sparkline->addSeries([2,3,5,6]);

// Or 

$sparkline->setData(
    [0,1,2,3],
    [2,3,5,6]
);

// For add point on series
$sparkline->addPoint('first', 3, '#efefef', 0); // Add point on series 0
$sparkline->addPoint('last', 3, '#efefef', 1); // add point on series 1

// For fill colors, specify on last argument series index's
$sparkline->setFillColorHex('#8b1c2b', 0);
$sparkline->setFillColorHex('#8bdddf', 1);
// or
$sparkline->setFillColorRGB(139, 28, 43, 0);
$sparkline->setFillColorRGB(139, 28, 55, 1);

// For line colors, specify on last argument series index's
$sparkline->setLineColorHex('#1c628b', 0);
$sparkline->setLineColorHex('#1c62df', 1);
// or
$sparkline->setLineColorRGB(28, 98, 139, 0);
$sparkline->setLineColorRGB(28, 98, 55, 1);
```
