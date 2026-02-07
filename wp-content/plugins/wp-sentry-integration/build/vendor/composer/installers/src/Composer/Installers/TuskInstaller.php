<?php

namespace WPSentry\ScopedVendor\Composer\Installers;

/**
 * Composer installer for 3rd party Tusk utilities
 * @author Drew Ewing <drew@phenocode.com>
 */
class TuskInstaller extends \WPSentry\ScopedVendor\Composer\Installers\BaseInstaller
{
    /** @var array<string, string> */
    protected $locations = array('task' => '.tusk/tasks/{$name}/', 'command' => '.tusk/commands/{$name}/', 'asset' => 'assets/tusk/{$name}/');
}
