<?php

namespace WPSentry\ScopedVendor\Composer\Installers;

class VgmcpInstaller extends \WPSentry\ScopedVendor\Composer\Installers\BaseInstaller
{
    /** @var array<string, string> */
    protected $locations = array('bundle' => 'src/{$vendor}/{$name}/', 'theme' => 'themes/{$name}/');
    /**
     * Format package name.
     *
     * For package type vgmcp-bundle, cut off a trailing '-bundle' if present.
     *
     * For package type vgmcp-theme, cut off a trailing '-theme' if present.
     *
     */
    public function inflectPackageVars(array $vars) : array
    {
        if ($vars['type'] === 'vgmcp-bundle') {
            return $this->inflectPluginVars($vars);
        }
        if ($vars['type'] === 'vgmcp-theme') {
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
        $vars['name'] = $this->pregReplace('/-bundle$/', '', $vars['name']);
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
