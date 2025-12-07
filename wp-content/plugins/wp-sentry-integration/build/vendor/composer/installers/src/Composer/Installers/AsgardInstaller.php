<?php

namespace WPSentry\ScopedVendor\Composer\Installers;

class AsgardInstaller extends \WPSentry\ScopedVendor\Composer\Installers\BaseInstaller
{
    /** @var array<string, string> */
    protected $locations = array('module' => 'Modules/{$name}/', 'theme' => 'Themes/{$name}/');
    /**
     * Format package name.
     *
     * For package type asgard-module, cut off a trailing '-plugin' if present.
     *
     * For package type asgard-theme, cut off a trailing '-theme' if present.
     */
    public function inflectPackageVars(array $vars) : array
    {
        if ($vars['type'] === 'asgard-module') {
            return $this->inflectPluginVars($vars);
        }
        if ($vars['type'] === 'asgard-theme') {
            return $this->inflectThemeVars($vars);
        }
        return $vars;
    }
    /**
     * @param array<string, string> $vars
     * @return array<string, string>
     */
    protected function inflectPluginVars(array $vars) : array
    {
        $vars['name'] = $this->pregReplace('/-module$/', '', $vars['name']);
        $vars['name'] = \str_replace(array('-', '_'), ' ', $vars['name']);
        $vars['name'] = \str_replace(' ', '', \ucwords($vars['name']));
        return $vars;
    }
    /**
     * @param array<string, string> $vars
     * @return array<string, string>
     */
    protected function inflectThemeVars(array $vars) : array
    {
        $vars['name'] = $this->pregReplace('/-theme$/', '', $vars['name']);
        $vars['name'] = \str_replace(array('-', '_'), ' ', $vars['name']);
        $vars['name'] = \str_replace(' ', '', \ucwords($vars['name']));
        return $vars;
    }
}
