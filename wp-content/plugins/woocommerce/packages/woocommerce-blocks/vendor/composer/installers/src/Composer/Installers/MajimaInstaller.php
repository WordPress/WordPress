<?php
namespace Composer\Installers;

/**
 * Plugin/theme installer for majima
 * @author David Neustadt
 */
class MajimaInstaller extends BaseInstaller
{
    protected $locations = array(
        'plugin' => 'plugins/{$name}/',
    );

    /**
     * Transforms the names
     * @param  array $vars
     * @return array
     */
    public function inflectPackageVars($vars)
    {
        return $this->correctPluginName($vars);
    }

    /**
     * Change hyphenated names to camelcase
     * @param  array $vars
     * @return array
     */
    private function correctPluginName($vars)
    {
        $camelCasedName = preg_replace_callback('/(-[a-z])/', function ($matches) {
            return strtoupper($matches[0][1]);
        }, $vars['name']);
        $vars['name'] = ucfirst($camelCasedName);
        return $vars;
    }
}
