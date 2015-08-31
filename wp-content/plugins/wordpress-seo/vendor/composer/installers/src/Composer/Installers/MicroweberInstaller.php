<?php
namespace Composer\Installers;

class MicroweberInstaller extends BaseInstaller
{
    protected $locations = array(
        'module'      => 'userfiles/modules/{$name}/',
        'module-skin' => 'userfiles/modules/{$name}/templates/',
        'template'    => 'userfiles/templates/{$name}/',
        'element'     => 'userfiles/elements/{$name}/',
        'vendor'      => 'vendor/{$name}/',
        'components'  => 'components/{$name}/'
    );

    /**
     * Format package name.
     *
     * For package type microweber-module, cut off a trailing '-module' if present
     *
     * For package type microweber-template, cut off a trailing '-template' if present.
     *
     */
    public function inflectPackageVars($vars)
    {
        if ($vars['type'] === 'microweber-template') {
            return $this->inflectTemplateVars($vars);
        }
        if ($vars['type'] === 'microweber-templates') {
            return $this->inflectTemplatesVars($vars);
        }
        if ($vars['type'] === 'microweber-core') {
            return $this->inflectCoreVars($vars);
        }
        if ($vars['type'] === 'microweber-adapter') {
            return $this->inflectCoreVars($vars);
        }
        if ($vars['type'] === 'microweber-module') {
            return $this->inflectModuleVars($vars);
        }
        if ($vars['type'] === 'microweber-modules') {
            return $this->inflectModulesVars($vars);
        }
        if ($vars['type'] === 'microweber-skin') {
            return $this->inflectSkinVars($vars);
        }
        if ($vars['type'] === 'microweber-element' or $vars['type'] === 'microweber-elements') {
            return $this->inflectElementVars($vars);
        }

        return $vars;
    }

    protected function inflectTemplateVars($vars)
    {
        $vars['name'] = preg_replace('/-template$/', '', $vars['name']);
        $vars['name'] = preg_replace('/template-$/', '', $vars['name']);

        return $vars;
    }

    protected function inflectTemplatesVars($vars)
    {
        $vars['name'] = preg_replace('/-templates$/', '', $vars['name']);
        $vars['name'] = preg_replace('/templates-$/', '', $vars['name']);

        return $vars;
    }

    protected function inflectCoreVars($vars)
    {
        $vars['name'] = preg_replace('/-providers$/', '', $vars['name']);
        $vars['name'] = preg_replace('/-provider$/', '', $vars['name']);
        $vars['name'] = preg_replace('/-adapter$/', '', $vars['name']);

        return $vars;
    }

    protected function inflectModuleVars($vars)
    {
        $vars['name'] = preg_replace('/-module$/', '', $vars['name']);
        $vars['name'] = preg_replace('/module-$/', '', $vars['name']);

        return $vars;
    }

    protected function inflectModulesVars($vars)
    {
        $vars['name'] = preg_replace('/-modules$/', '', $vars['name']);
        $vars['name'] = preg_replace('/modules-$/', '', $vars['name']);

        return $vars;
    }

    protected function inflectSkinVars($vars)
    {
        $vars['name'] = preg_replace('/-skin$/', '', $vars['name']);
        $vars['name'] = preg_replace('/skin-$/', '', $vars['name']);

        return $vars;
    }

    protected function inflectElementVars($vars)
    {
        $vars['name'] = preg_replace('/-elements$/', '', $vars['name']);
        $vars['name'] = preg_replace('/elements-$/', '', $vars['name']);
        $vars['name'] = preg_replace('/-element$/', '', $vars['name']);
        $vars['name'] = preg_replace('/element-$/', '', $vars['name']);

        return $vars;
    }
}
