PHP 5.2 Autoloading for Composer
================================

This package provides an easy way to get a PHP 5.2 compatible autoloader out of Composer. The generated autoloader is fully compatible to the original and is written into separate files, each ending with `_52.php`.

Usage
-----

In your project's `composer.json`, add the following lines:

    :::json
    {
        "require": {
            "xrstf/composer-php52": "1.*"
        },
        "scripts": {
            "post-install-cmd": [
                "xrstf\\Composer52\\Generator::onPostInstallCmd"
            ],
            "post-update-cmd": [
                "xrstf\\Composer52\\Generator::onPostInstallCmd"
            ],
            "post-autoload-dump": [
                "xrstf\\Composer52\\Generator::onPostInstallCmd"
            ]
        }
    }

After the next update/install, you will have a `vendor/autoload_52.php` file, that you can simply include and use in PHP 5.2 projects.
