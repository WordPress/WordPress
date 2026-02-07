<?php

namespace WPSentry\ScopedVendor\Composer\Installers;

class CroogoInstaller extends \WPSentry\ScopedVendor\Composer\Installers\BaseInstaller
{
    /** @var array<string, string> */
    protected $locations = array('plugin' => 'Plugin/{$name}/', 'theme' => 'View/Themed/{$name}/');
    /**
     * Format package name to CamelCase
     */
    public function inflectPackageVars(array $vars) : array
    {
        $vars['name'] = \strtolower(\str_replace(array('-', '_'), ' ', $vars['name']));
        $vars['name'] = \str_replace(' ', '', \ucwords($vars['name']));
        return $vars;
    }
}
