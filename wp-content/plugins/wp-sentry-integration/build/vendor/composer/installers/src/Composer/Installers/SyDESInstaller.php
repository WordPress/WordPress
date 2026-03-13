<?php

namespace WPSentry\ScopedVendor\Composer\Installers;

class SyDESInstaller extends \WPSentry\ScopedVendor\Composer\Installers\BaseInstaller
{
    /** @var array<string, string> */
    protected $locations = array('module' => 'app/modules/{$name}/', 'theme' => 'themes/{$name}/');
    /**
     * Format module name.
     *
     * Strip `sydes-` prefix and a trailing '-theme' or '-module' from package name if present.
     */
    public function inflectPackageVars(array $vars) : array
    {
        if ($vars['type'] == 'sydes-module') {
            return $this->inflectModuleVars($vars);
        }
        if ($vars['type'] === 'sydes-theme') {
            return $this->inflectThemeVars($vars);
        }
        return $vars;
    }
    /**
     * @param array<string, string> $vars
     * @return array<string, string>
     */
    public function inflectModuleVars(array $vars) : array
    {
        $vars['name'] = $this->pregReplace('/(^sydes-|-module$)/i', '', $vars['name']);
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
        $vars['name'] = $this->pregReplace('/(^sydes-|-theme$)/', '', $vars['name']);
        $vars['name'] = \strtolower($vars['name']);
        return $vars;
    }
}
