## Redux Options Framework [![Build Status](https://travis-ci.org/reduxframework/redux-framework.png?branch=master)](https://travis-ci.org/reduxframework/redux-framework) [![Built with Grunt](https://cdn.gruntjs.com/builtwith.png)](http://gruntjs.com/) [![Slack](http://slack.redux.io/badge.svg)](http://slack.redux.io)

WordPress options framework which uses the [WordPress Settings API](http://codex.wordpress.org/Settings_API "WordPress Settings API"), Custom Error/Validation Handling, Custom Field/Validation Types, and import/export functionality.

## Posting Guidelines for issues and questions ##
When using our Issue Tracker, you may ask questions where you may be a bit lost or need help understanding the documentation. If, however, you find a bug we require you to read and provide the information contained in our [Contributing Guidelines](https://github.com/ReduxFramework/redux-framework/blob/master/CONTRIBUTING.md). If you do not provide this information, we will request it before we can support you.

If you are stuck in some of your own code, or need help with PHP and anything else not Redux specific, we request you purchase some [Premium Support](http://reduxframework.com/extension/premium-support/) and we will be happy to assist you. If we feel the issue is outside of our scope we will suggest you to purchase some [Premium Support](http://reduxframework.com/extension/premium-support/) in order for us to serve you.

## Kickstart Your Development ##

Are you authoring a theme, or plugin?  Visit the  [Redux Builder](http://build.reduxframework.com) site and get started!

## Demo Your Products ##
We help you create a seamless user experience for your users to demo your WordPress products.  Not only that, we help you make sure they’re engaged, turning them into a potential customer.  Visit [wpdemo.io/](http://wpdemo.io/)

## Documentation ##

Need a little help with Redux?  Come check out our brand new documentation site at  [docs.reduxframework.com](http://docs.reduxframework.com), chock full of tutorials and examples!


## SMOF (Simple Modified Option Users) Converter! ##

Hot off the press, our Redux Converter plugin. It takes your SMOF instance, and allows you to try out Redux without any fear. It also spits out valid PHP source for you if you want to migrate complete with data migration! Give it a try today. It will be in the WordPress.org repo shortly.  ;)
https://github.com/ReduxFramework/redux-converter

## Help Us Translate Redux ##

Please head over to the wiki to learn how you can help us translate Redux quickly. Any and all are welcome. We appreciate your help!
https://github.com/ReduxFramework/ReduxFramework/wiki/translate

## Getting Started with Redux ##

ReduxFramework has been built from the ground up to be the most flexible framework around. You can run it as an auto-updating plugin, or embed it inside your plugin or theme. It allows for multiple copies of itself within the same WordPress instance. For a guide on getting started please refer to [https://github.com/ReduxFramework/redux-framework/wiki/Getting-Started](https://github.com/ReduxFramework/redux-framework/wiki/Getting-Started).

You can also [download our sample theme available here](https://github.com/ReduxFramework/ReduxSampleTheme) to start developing right away.

## Please Post Reviews and Spread the Word ##

ReduxFramework has just released to the WordPress Plugins directory. Please spread the word, tweet, and (most importantly) post reviews on http://wordpress.org/plugins/redux-framework/. 


## Donate to the Framework ##

If you can, please donate to help support the ongoing development of Redux Framework!

[![Donate to the framework](https://www.paypalobjects.com/en_US/i/btn/btn_donateCC_LG.gif "Donate to the framework")](https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=MMFMHWUPKHKPW)

## Features ##

* Uses the [WordPress Settings API](http://codex.wordpress.org/Settings_API "WordPress Settings API")
* Multiple built in field types
* Multple layout field types
* Fields can be overloaded with a callback function, for custom field types
* Easily extendable by creating Field Classes
* Built in Validation Classes
* Easily extendable by creating Validation Classes
* Custom Validation error handling, including error counts for each section, and custom styling for error fields
* Custom Validation warning handling, including warning counts for each section, and custom styling for warning fields
* Multiple Hook Points for customisation
* Import / Export Functionality - including cross site importing of settings
* Easily add page help through the class
* Fully responsive options panel
* Much more

## Stay In The Loop! ##

[![Follow us on Twitter](http://iod.unh.edu/Images/Twitter_follow_us.png "Follow us on Twitter")](https://www.twitter.com/ReduxFramework)

## FAQs ##

1. Why should we use ```require_once``` instead of ```get_template_part```?
 * First, because ```get_template_part``` is for... you guessed it, themes! Redux is designed to work with both themes *and* plugins.
 * Second, read [this](http://kovshenin.com/2013/get_template_part/).
2. Why shouldn't we edit ```sample-config.php``` in the plugin directory?
 * Because ```sample-config.php``` will be replaced at each update of the plugin. You will lose all your effort

## Are you using Redux? ##

Send us an email at info@reduxframework.com so we can add you to our showcase!

## Changelog ##

See [Changelog.md](https://github.com/ReduxFramework/redux-framework/blob/master/CHANGELOG.md)

## Running PHP Unit tests ##

The tests are built using [wordpress's make subversion repository](https://make.wordpress.org/core/handbook/automated-testing/)

`/var/www/wordpress-develop` as the destination for the core test files.
First download the wordress core tests repository, for these files.

```bash
cd /var/www
svn co http://develop.svn.wordpress.org/trunk/ wordpress-develop
```

In the newly created `/var/www/wordpress-develop` directory rename
`wp-tests-config-sample.php` to `wp-tests-config.php`. Now add your database
details to the new file:
```php
// WARNING WARNING WARNING!
// These tests will DROP ALL TABLES in the database with the prefix named below.
// DO NOT use a production database or one that is shared with something else.

define( 'DB_NAME', 'wordpress-tests' );
define( 'DB_USER', 'root' );
define( 'DB_PASSWORD', 'passowrd' );
define( 'DB_HOST', 'localhost' );
define( 'DB_CHARSET', 'utf8' );
define( 'DB_COLLATE', '' );
```
 - <b>n.b.</b> you may need to create the database first.
 - <b>n.b. n.b.</b> also note that the database used will be emptied on each run.

Set the `WP_TESTS_DIR` environment variable so that the `redux-framework` test bootstrap file can find the wordpress core tests:
```bash
export WP_TESTS_DIR='/var/www/wordpress-develop/tests/phpunit/includes/'
```

You should now be able to run the `redux-framework` unit tests:
```bash
redux-framework$ phpunit
Welcome to the TIVWP Test Suite
Version: 1.0

Tests folder: /var/www/wordpress-develop/tests/phpunit/includes/

Installing...
...
Configuration read from
redux-framework/phpunit.xml
...
```
