<?php

namespace WPSentry\ScopedVendor\Jean85;

use WPSentry\ScopedVendor\PackageVersions\Versions;
class PrettyVersions
{
    const SHORT_COMMIT_LENGTH = 7;
    public static function getVersion(string $packageName) : \WPSentry\ScopedVendor\Jean85\Version
    {
        return new \WPSentry\ScopedVendor\Jean85\Version($packageName, \WPSentry\ScopedVendor\PackageVersions\Versions::getVersion($packageName));
    }
    public static function getRootPackageName() : string
    {
        return \WPSentry\ScopedVendor\PackageVersions\Versions::ROOT_PACKAGE_NAME;
    }
    public static function getRootPackageVersion() : \WPSentry\ScopedVendor\Jean85\Version
    {
        return self::getVersion(\WPSentry\ScopedVendor\PackageVersions\Versions::ROOT_PACKAGE_NAME);
    }
}
