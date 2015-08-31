<?php
namespace Composer\Installers;

class DokuWikiInstaller extends BaseInstaller
{
    protected $locations = array(
        'plugin' => 'lib/plugins/{$name}/',
        'template' => 'lib/tpl/{$name}/',
    );

    /**
     * Format package name.
     *
     * For package type dokuwiki-plugin, cut off a trailing '-plugin', 
     * or leading dokuwiki_ if present.
     * 
     * For package type dokuwiki-template, cut off a trailing '-template' if present.
     *
     */
    public function inflectPackageVars($vars)
    {

        if ($vars['type'] === 'dokuwiki-plugin') {
            return $this->inflectPluginVars($vars);
        }

        if ($vars['type'] === 'dokuwiki-template') {
            return $this->inflectTemplateVars($vars);
        }

        return $vars;
    }

    protected function inflectPluginVars($vars)
    {
        $vars['name'] = preg_replace('/-plugin$/', '', $vars['name']);
        $vars['name'] = preg_replace('/^dokuwiki_?-?/', '', $vars['name']);

        return $vars;
    }

    protected function inflectTemplateVars($vars)
    {
        $vars['name'] = preg_replace('/-template$/', '', $vars['name']);
        $vars['name'] = preg_replace('/^dokuwiki_?-?/', '', $vars['name']);

        return $vars;
    }

}
