<?php
namespace Composer\Installers;

class MayaInstaller extends BaseInstaller
{
    protected $locations = array(
        'module' => 'modules/{$name}/',
    );

    /**
     * Format package name.
     *
     * For package type maya-module, cut off a trailing '-module' if present.
     *
     */
    public function inflectPackageVars($vars)
    {
        if ($vars['type'] === 'maya-module') {
            return $this->inflectModuleVars($vars);
        }

        return $vars;
    }

    protected function inflectModuleVars($vars)
    {
        $vars['name'] = preg_replace('/-module$/', '', $vars['name']);
        $vars['name'] = str_replace(array('-', '_'), ' ', $vars['name']);
        $vars['name'] = str_replace(' ', '', ucwords($vars['name']));

        return $vars;
    }
}
