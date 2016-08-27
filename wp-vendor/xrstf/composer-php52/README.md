PHP 5.2 Autoloading for Composer
================================

This package provides an easy way to get a PHP 5.2 compatible autoloader out of Composer. The generated autoloader is fully compatible to the original and is written into separate files, each ending with `_52.php`.

Legacy
------

Please do not use this, if you can avoid it. It's a horrible hack, often breaks and is extremely tied to Composer's interna. This package was originally developed in 2012, when PHP 5.2 was much more common on cheap webhosts.

In 2016, this package has been moved from Bitbucket to a Github organization, because the original developer could no longer reliably maintain it. This is the reason for this legacy package name ``xrstf/...``.

Usage
-----

In your project's `composer.json`, add the following lines:

```json
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
```

After the next update/install, you will have a `vendor/autoload_52.php` file, that you can simply include and use in PHP 5.2 projects.
