<?php

namespace WPSentry\ScopedVendor\Composer\Installers;

class GravInstaller extends \WPSentry\ScopedVendor\Composer\Installers\BaseInstaller
{
    /** @var array<string, string> */
    protected $locations = array('plugin' => 'user/plugins/{$name}/', 'theme' => 'user/themes/{$name}/');
    /**
     * Format package name
     */
    public function inflectPackageVars(array $vars) : array
    {
        $restrictedWords = \implode('|', \array_keys($this->locations));
        $vars['name'] = \strtolower($vars['name']);
        $vars['name'] = $this->pregReplace('/^(?:grav-)?(?:(?:' . $restrictedWords . ')-)?(.*?)(?:-(?:' . $restrictedWords . '))?$/ui', '$1', $vars['name']);
        return $vars;
    }
}
