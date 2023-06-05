<?php
namespace Composer\Installers;

class WinterInstaller extends BaseInstaller
{
    protected $locations = array(
        'module'    => 'modules/{$name}/',
        'plugin'    => 'plugins/{$vendor}/{$name}/',
        'theme'     => 'themes/{$name}/'
    );

    /**
     * Format package name.
     *
     * For package type winter-plugin, cut off a trailing '-plugin' if present.
     *
     * For package type winter-theme, cut off a trailing '-theme' if present.
     *
     */
    public function inflectPackageVars($vars)
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
    
    protected function inflectModuleVars($vars)
    {
        $vars['name'] = preg_replace('/^wn-|-module$/', '', $vars['name']);

        return $vars;
    }

    protected function inflectPluginVars($vars)
    {
        $vars['name'] = preg_replace('/^wn-|-plugin$/', '', $vars['name']);
        $vars['vendor'] = preg_replace('/[^a-z0-9_]/i', '', $vars['vendor']);

        return $vars;
    }

    protected function inflectThemeVars($vars)
    {
        $vars['name'] = preg_replace('/^wn-|-theme$/', '', $vars['name']);

        return $vars;
    }
}
