<?php

namespace WPSentry\ScopedVendor\Composer\Installers;

class ForkCMSInstaller extends \WPSentry\ScopedVendor\Composer\Installers\BaseInstaller
{
    /** @var array<string, string> */
    protected $locations = ['module' => 'src/Modules/{$name}/', 'theme' => 'src/Themes/{$name}/'];
    /**
     * Format package name.
     *
     * For package type fork-cms-module, cut off a trailing '-plugin' if present.
     *
     * For package type fork-cms-theme, cut off a trailing '-theme' if present.
     */
    public function inflectPackageVars(array $vars) : array
    {
        if ($vars['type'] === 'fork-cms-module') {
            return $this->inflectModuleVars($vars);
        }
        if ($vars['type'] === 'fork-cms-theme') {
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
        $vars['name'] = $this->pregReplace('/^fork-cms-|-module|ForkCMS|ForkCms|Forkcms|forkcms|Module$/', '', $vars['name']);
        $vars['name'] = \str_replace(array('-', '_'), ' ', $vars['name']);
        // replace hyphens with spaces
        $vars['name'] = \str_replace(' ', '', \ucwords($vars['name']));
        // make module name camelcased
        return $vars;
    }
    /**
     * @param array<string, string> $vars
     * @return array<string, string>
     */
    protected function inflectThemeVars(array $vars) : array
    {
        $vars['name'] = $this->pregReplace('/^fork-cms-|-theme|ForkCMS|ForkCms|Forkcms|forkcms|Theme$/', '', $vars['name']);
        $vars['name'] = \str_replace(array('-', '_'), ' ', $vars['name']);
        // replace hyphens with spaces
        $vars['name'] = \str_replace(' ', '', \ucwords($vars['name']));
        // make theme name camelcased
        return $vars;
    }
}
