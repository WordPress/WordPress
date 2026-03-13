<?php

namespace WPSentry\ScopedVendor\Composer\Installers;

class OctoberInstaller extends \WPSentry\ScopedVendor\Composer\Installers\BaseInstaller
{
    /** @var array<string, string> */
    protected $locations = array('module' => 'modules/{$name}/', 'plugin' => 'plugins/{$vendor}/{$name}/', 'theme' => 'themes/{$vendor}-{$name}/');
    /**
     * Format package name.
     *
     * For package type october-plugin, cut off a trailing '-plugin' if present.
     *
     * For package type october-theme, cut off a trailing '-theme' if present.
     */
    public function inflectPackageVars(array $vars) : array
    {
        if ($vars['type'] === 'october-plugin') {
            return $this->inflectPluginVars($vars);
        }
        if ($vars['type'] === 'october-theme') {
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
        $vars['name'] = $this->pregReplace('/^oc-|-plugin$/', '', $vars['name']);
        $vars['vendor'] = $this->pregReplace('/[^a-z0-9_]/i', '', $vars['vendor']);
        return $vars;
    }
    /**
     * @param array<string, string> $vars
     * @return array<string, string>
     */
    protected function inflectThemeVars(array $vars) : array
    {
        $vars['name'] = $this->pregReplace('/^oc-|-theme$/', '', $vars['name']);
        $vars['vendor'] = $this->pregReplace('/[^a-z0-9_]/i', '', $vars['vendor']);
        return $vars;
    }
}
