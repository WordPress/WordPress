<?php

namespace WPSentry\ScopedVendor\Composer\Installers;

class MayaInstaller extends \WPSentry\ScopedVendor\Composer\Installers\BaseInstaller
{
    /** @var array<string, string> */
    protected $locations = array('module' => 'modules/{$name}/');
    /**
     * Format package name.
     *
     * For package type maya-module, cut off a trailing '-module' if present.
     */
    public function inflectPackageVars(array $vars) : array
    {
        if ($vars['type'] === 'maya-module') {
            return $this->inflectModuleVars($vars);
        }
        return $vars;
    }
    /**
     * @param array<string, string> $vars
     * @return array<string, string>
     */
    protected function inflectModuleVars(array $vars) : array
    {
        $vars['name'] = $this->pregReplace('/-module$/', '', $vars['name']);
        $vars['name'] = \str_replace(array('-', '_'), ' ', $vars['name']);
        $vars['name'] = \str_replace(' ', '', \ucwords($vars['name']));
        return $vars;
    }
}
