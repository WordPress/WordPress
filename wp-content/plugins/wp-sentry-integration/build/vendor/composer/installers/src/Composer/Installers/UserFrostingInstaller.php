<?php

namespace WPSentry\ScopedVendor\Composer\Installers;

class UserFrostingInstaller extends \WPSentry\ScopedVendor\Composer\Installers\BaseInstaller
{
    /** @var array<string, string> */
    protected $locations = array('sprinkle' => 'app/sprinkles/{$name}/');
}
