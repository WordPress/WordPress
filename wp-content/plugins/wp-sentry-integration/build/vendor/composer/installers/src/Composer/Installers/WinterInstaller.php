<?php

namespace WPSentry\ScopedVendor\Composer\Installers;

class WinterInstaller extends \WPSentry\ScopedVendor\Composer\Installers\BaseInstaller
{
    /** @var array<string, string> */
    protected $locations = array('module' => 'modules/{$name}/', 'plugin' => 'plugins/{$vendor}/{$name}/', 'theme' => 'themes/{$name}/');
    /**
     * Format package name.
     *
     * For package type winter-plugin, cut off a trailing '-plugin' if present.
     *
     * For package type winter-theme, cut off a trailing '-theme' if present.
     */
    public function inflectPackageVars(array $vars) : array
    {
        if ($vars['type'] === 'winter-module') {
            return $this->inflectModuleVars($vars);
        }
        if ($vars['type'] === 'winter-plugin') {
            return $this->inflectPluginVars($vars);
        }
        if ($vars['type'] === 'winter-theme') {
            return $this->inflectThemeVars($vars);
        }
        return $vars;
    }
    /**
     * @param array<string, string> $vars
     * @return array<string, string>
     */
    protected function inflectModuleVars(array $vars) : array
    {
        $vars['name'] = $this->pregReplace('/^wn-|-module$/', '', $vars['name']);
        return $vars;
    }
    /**
     * @param array<string, string> $vars
     * @return array<string, string>
     */
    protected function inflectPluginVars(array $vars) : array
    {
        $vars['name'] = $this->pregReplace('/^wn-|-plugin$/', '', $vars['name']);
        $vars['vendor'] = $this->pregReplace('/[^a-z0-9_]/i', '', $vars['vendor']);
        return $vars;
    }
    /**
     * @param array<string, string> $vars
     * @return array<string, string>
     */
    protected function inflectThemeVars(array $vars) : array
    {
        $vars['name'] = $this->pregReplace('/^wn-|-theme$/', '', $vars['name']);
        return $vars;
    }
}
