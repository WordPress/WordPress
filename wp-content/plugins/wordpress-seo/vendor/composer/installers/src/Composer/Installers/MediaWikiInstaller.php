<?php
namespace Composer\Installers;

class MediaWikiInstaller extends BaseInstaller
{
    protected $locations = array(
        'extension' => 'extensions/{$name}/',
        'skin' => 'skins/{$name}/',
    );

    /**
     * Format package name.
     *
     * For package type mediawiki-extension, cut off a trailing '-extension' if present and transform
     * to CamelCase keeping existing uppercase chars.
     *
     * For package type mediawiki-skin, cut off a trailing '-skin' if present.
     *
     */
    public function inflectPackageVars($vars)
    {

        if ($vars['type'] === 'mediawiki-extension') {
            return $this->inflectExtensionVars($vars);
        }

        if ($vars['type'] === 'mediawiki-skin') {
            return $this->inflectSkinVars($vars);
        }

        return $vars;
    }

    protected function inflectExtensionVars($vars)
    {
        $vars['name'] = preg_replace('/-extension$/', '', $vars['name']);
        $vars['name'] = str_replace('-', ' ', $vars['name']);
        $vars['name'] = str_replace(' ', '', ucwords($vars['name']));

        return $vars;
    }

    protected function inflectSkinVars($vars)
    {
        $vars['name'] = preg_replace('/-skin$/', '', $vars['name']);

        return $vars;
    }

}
