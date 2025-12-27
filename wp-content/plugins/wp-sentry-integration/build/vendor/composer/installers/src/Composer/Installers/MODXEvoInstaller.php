<?php

namespace WPSentry\ScopedVendor\Composer\Installers;

/**
 * An installer to handle MODX Evolution specifics when installing packages.
 */
class MODXEvoInstaller extends \WPSentry\ScopedVendor\Composer\Installers\BaseInstaller
{
    /** @var array<string, string> */
    protected $locations = array('snippet' => 'assets/snippets/{$name}/', 'plugin' => 'assets/plugins/{$name}/', 'module' => 'assets/modules/{$name}/', 'template' => 'assets/templates/{$name}/', 'lib' => 'assets/lib/{$name}/');
}
