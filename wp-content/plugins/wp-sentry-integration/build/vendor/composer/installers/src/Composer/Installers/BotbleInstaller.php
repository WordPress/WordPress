<?php

namespace WPSentry\ScopedVendor\Composer\Installers;

class BotbleInstaller extends \WPSentry\ScopedVendor\Composer\Installers\BaseInstaller
{
    /** @var array<string, string> */
    protected $locations = array('plugin' => 'platform/plugins/{$name}/', 'theme' => 'platform/themes/{$name}/');
}
