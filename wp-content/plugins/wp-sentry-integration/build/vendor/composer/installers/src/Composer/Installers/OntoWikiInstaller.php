<?php

namespace WPSentry\ScopedVendor\Composer\Installers;

class OntoWikiInstaller extends \WPSentry\ScopedVendor\Composer\Installers\BaseInstaller
{
    /** @var array<string, string> */
    protected $locations = array('extension' => 'extensions/{$name}/', 'theme' => 'extensions/themes/{$name}/', 'translation' => 'extensions/translations/{$name}/');
    /**
     * Format package name to lower case and remove ".ontowiki" suffix
     */
    public function inflectPackageVars(array $vars) : array
    {
        $vars['name'] = \strtolower($vars['name']);
        $vars['name'] = $this->pregReplace('/.ontowiki$/', '', $vars['name']);
        $vars['name'] = $this->pregReplace('/-theme$/', '', $vars['name']);
        $vars['name'] = $this->pregReplace('/-translation$/', '', $vars['name']);
        return $vars;
    }
}
