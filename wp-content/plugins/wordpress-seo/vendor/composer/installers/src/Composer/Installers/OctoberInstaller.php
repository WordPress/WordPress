<?php
namespace Composer\Installers;

class OctoberInstaller extends BaseInstaller
{
    protected $locations = array(
        'module'    => 'modules/{$name}/',
        'plugin'    => 'plugins/{$vendor}/{$name}/',
        'theme'     => 'themes/{$name}/'
    );

    /**
     * Format package name.
     *
     * For package type october-plugin, cut off a trailing '-plugin' if present.
     *
     * For package type october-theme, cut off a trailing '-theme' if present.
     *
     */
    public function inflectPackageVars($vars)
    {
        if ($vars['type'] === 'october-plugin') {
            return $this->inflectPluginVars($vars);
        }

        if ($vars['type'] === 'october-theme') {
            return $this->inflectThemeVars($vars);
        }

        return $vars;
    }

    protected function inflectPluginVars($vars)
    {
        $vars['name'] = preg_replace('/-plugin$/', '', $vars['name']);

        return $vars;
    }

    protected function inflectThemeVars($vars)
    {
        $vars['name'] = preg_replace('/-theme$/', '', $vars['name']);

        return $vars;
    }
}