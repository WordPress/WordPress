<?php

namespace WPSentry\ScopedVendor\Composer\Installers;

class MediaWikiInstaller extends \WPSentry\ScopedVendor\Composer\Installers\BaseInstaller
{
    /** @var array<string, string> */
    protected $locations = array('core' => 'core/', 'extension' => 'extensions/{$name}/', 'skin' => 'skins/{$name}/');
    /**
     * Format package name.
     *
     * For package type mediawiki-extension, cut off a trailing '-extension' if present and transform
     * to CamelCase keeping existing uppercase chars.
     *
     * For package type mediawiki-skin, cut off a trailing '-skin' if present.
     */
    public function inflectPackageVars(array $vars) : array
    {
        if ($vars['type'] === 'mediawiki-extension') {
            return $this->inflectExtensionVars($vars);
        }
        if ($vars['type'] === 'mediawiki-skin') {
            return $this->inflectSkinVars($vars);
        }
        return $vars;
    }
    /**
     * @param array<string, string> $vars
     * @return array<string, string>
     */
    protected function inflectExtensionVars(array $vars) : array
    {
        $vars['name'] = $this->pregReplace('/-extension$/', '', $vars['name']);
        $vars['name'] = \str_replace('-', ' ', $vars['name']);
        $vars['name'] = \str_replace(' ', '', \ucwords($vars['name']));
        return $vars;
    }
    /**
     * @param array<string, string> $vars
     * @return array<string, string>
     */
    protected function inflectSkinVars(array $vars) : array
    {
        $vars['name'] = $this->pregReplace('/-skin$/', '', $vars['name']);
        return $vars;
    }
}
