<?php
namespace Composer\Installers;

class PxcmsInstaller extends BaseInstaller
{
    protected $locations = array(
        'module' => 'app/Modules/{$name}/',
        'theme' => 'themes/{$name}/',
    );

    /**
     * Format package name.
     *
     * @param array $vars
     *
     * @return array
     */
    public function inflectPackageVars($vars)
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
     * return string
     */
    protected function inflectModuleVars($vars)
    {
        $vars['name'] = str_replace('pxcms-', '', $vars['name']);       // strip out pxcms- just incase (legacy)
        $vars['name'] = str_replace('module-', '', $vars['name']);      // strip out module-
        $vars['name'] = preg_replace('/-module$/', '', $vars['name']);  // strip out -module
        $vars['name'] = str_replace('-', '_', $vars['name']);           // make -'s be _'s
        $vars['name'] = ucwords($vars['name']);                         // make module name camelcased

        return $vars;
    }


    /**
     * For package type pxcms-module, cut off a trailing '-plugin' if present.
     *
     * return string
     */
    protected function inflectThemeVars($vars)
    {
        $vars['name'] = str_replace('pxcms-', '', $vars['name']);       // strip out pxcms- just incase (legacy)
        $vars['name'] = str_replace('theme-', '', $vars['name']);       // strip out theme-
        $vars['name'] = preg_replace('/-theme$/', '', $vars['name']);   // strip out -theme
        $vars['name'] = str_replace('-', '_', $vars['name']);           // make -'s be _'s
        $vars['name'] = ucwords($vars['name']);                         // make module name camelcased

        return $vars;
    }
}
