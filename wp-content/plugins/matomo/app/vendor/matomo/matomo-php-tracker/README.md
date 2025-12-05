# PHP Client for Matomo Analytics Tracking API

The PHP Tracker Client provides all features of the [Matomo Javascript Tracker](https://developer.matomo.org/api-reference/tracking-javascript), such as Ecommerce Tracking, Custom Variables, Event Tracking and more. 

## Documentation and examples 
Check out our [Matomo-PHP-Tracker developer documentation](https://developer.matomo.org/api-reference/PHP-Piwik-Tracker) and [Matomo Tracking API guide](https://matomo.org/docs/tracking-api/).


```php
// Required variables
$matomoSiteId = 6;                  // Site ID
$matomoUrl = "https://example.tld"; // Your matomo URL
$matomoToken = "";                  // Your authentication token

// Optional variable
$matomoPageTitle = "";              // The title of the page

// Load object
require_once("MatomoTracker.php");

// Matomo object
$matomoTracker = new MatomoTracker((int)$matomoSiteId, $matomoUrl);

// Set authentication token
$matomoTracker->setTokenAuth($matomoToken);

// Track page view
$matomoTracker->doTrackPageView($matomoPageTitle);
```

## Requirements:
* JSON extension (json_decode, json_encode)
* cURL or stream extension (to issue the HTTPS request to Matomo)

## Installation

### Composer

```
composer require matomo/matomo-php-tracker
``` 

### Manually

Alternatively, you can download the files and require the Matomo tracker manually: 

```
require_once("MatomoTracker.php");
```

## License

Released under the [BSD License](https://opensource.org/licenses/BSD-3-Clause)
