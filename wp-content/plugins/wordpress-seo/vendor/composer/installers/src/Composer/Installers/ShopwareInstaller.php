<?php
namespace Composer\Installers;

/**
 * Plugin/theme installer for shopware
 * @author Benjamin Boit
 */
class ShopwareInstaller extends BaseInstaller
{
    protected $locations = array(
        'backend-plugin'    => 'engine/Shopware/Plugins/Local/Backend/{$name}/',
        'core-plugin'       => 'engine/Shopware/Plugins/Local/Core/{$name}/',
        'frontend-plugin'   => 'engine/Shopware/Plugins/Local/Frontend/{$name}/',
        'theme'             => 'templates/{$name}/'
    );

    /**
     * Transforms the names
     * @param  array $vars
     * @return array
     */
    public function inflectPackageVars($vars)
    {
        if ($vars['type'] === 'shopware-theme') {
            return $this->correctThemeName($vars);
        } else {
            return $this->correctPluginName($vars);
        }
    }

    /**
     * Changes the name to a camelcased combination of vendor and name
     * @param  array $vars
     * @return array
     */
    private function correctPluginName($vars)
    {
        $camelCasedName = preg_replace_callback('/(-[a-z])/', function ($matches) {
            return strtoupper($matches[0][1]);
        }, $vars['name']);

        $vars['name'] = ucfirst($vars['vendor']) . ucfirst($camelCasedName);

        return $vars;
    }

    /**
     * Changes the name to a underscore separated name
     * @param  array $vars
     * @return array
     */
    private function correctThemeName($vars)
    {
        $vars['name'] = str_replace('-', '_', $vars['name']);

        return $vars;
    }
}
