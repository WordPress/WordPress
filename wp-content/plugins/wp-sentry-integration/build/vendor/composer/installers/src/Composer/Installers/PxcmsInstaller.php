<?php

namespace WPSentry\ScopedVendor\Composer\Installers;

class PxcmsInstaller extends \WPSentry\ScopedVendor\Composer\Installers\BaseInstaller
{
    /** @var array<string, string> */
    protected $locations = array('module' => 'app/Modules/{$name}/', 'theme' => 'themes/{$name}/');
    /**
     * Format package name.
     */
    public function inflectPackageVars(array $vars) : array
    {
        if ($vars['type'] === 'pxcms-module') {
            return $this->inflectModuleVars($vars);
        }
        if ($vars['type'] === 'pxcms-theme') {
            return $this->inflectThemeVars($vars);
        }
        return $vars;
    }
    /**
     * For package type pxcms-module, cut off a trailing '-plugin' if present.
     *
     * @param array<string, string> $vars
     * @return array<string, string>
     */
    protected function inflectModuleVars(array $vars) : array
    {
        $vars['name'] = \str_replace('pxcms-', '', $vars['name']);
        // strip out pxcms- just incase (legacy)
        $vars['name'] = \str_replace('module-', '', $vars['name']);
        // strip out module-
        $vars['name'] = $this->pregReplace('/-module$/', '', $vars['name']);
        // strip out -module
        $vars['name'] = \str_replace('-', '_', $vars['name']);
        // make -'s be _'s
        $vars['name'] = \ucwords($vars['name']);
        // make module name camelcased
        return $vars;
    }
    /**
     * For package type pxcms-module, cut off a trailing '-plugin' if present.
     *
     * @param array<string, string> $vars
     * @return array<string, string>
     */
    protected function inflectThemeVars(array $vars) : array
    {
        $vars['name'] = \str_replace('pxcms-', '', $vars['name']);
        // strip out pxcms- just incase (legacy)
        $vars['name'] = \str_replace('theme-', '', $vars['name']);
        // strip out theme-
        $vars['name'] = $this->pregReplace('/-theme$/', '', $vars['name']);
        // strip out -theme
        $vars['name'] = \str_replace('-', '_', $vars['name']);
        // make -'s be _'s
        $vars['name'] = \ucwords($vars['name']);
        // make module name camelcased
        return $vars;
    }
}
