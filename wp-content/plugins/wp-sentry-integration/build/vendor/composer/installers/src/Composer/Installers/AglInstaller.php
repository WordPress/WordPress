<?php

namespace WPSentry\ScopedVendor\Composer\Installers;

class AglInstaller extends \WPSentry\ScopedVendor\Composer\Installers\BaseInstaller
{
    /** @var array<string, string> */
    protected $locations = array('module' => 'More/{$name}/');
    /**
     * Format package name to CamelCase
     */
    public function inflectPackageVars(array $vars) : array
    {
        $name = \preg_replace_callback('/(?:^|_|-)(.?)/', function ($matches) {
            return \strtoupper($matches[1]);
        }, $vars['name']);
        if (null === $name) {
            throw new \RuntimeException('Failed to run preg_replace_callback: ' . \preg_last_error());
        }
        $vars['name'] = $name;
        return $vars;
    }
}
