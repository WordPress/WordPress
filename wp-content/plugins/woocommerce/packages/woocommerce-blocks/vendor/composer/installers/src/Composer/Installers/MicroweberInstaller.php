<?php
namespace Composer\Installers;

class MicroweberInstaller extends BaseInstaller
{
    protected $locations = array(
        'module' => 'userfiles/modules/{$install_item_dir}/',
        'module-skin' => 'userfiles/modules/{$install_item_dir}/templates/',
        'template' => 'userfiles/templates/{$install_item_dir}/',
        'element' => 'userfiles/elements/{$install_item_dir}/',
        'vendor' => 'vendor/{$install_item_dir}/',
        'components' => 'components/{$install_item_dir}/'
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


        if ($this->package->getTargetDir()) {
            $vars['install_item_dir'] = $this->package->getTargetDir();
        } else {
            $vars['install_item_dir'] = $vars['name'];
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
        }


        return $vars;
    }

    protected function inflectTemplateVars($vars)
    {
        $vars['install_item_dir'] = preg_replace('/-template$/', '', $vars['install_item_dir']);
        $vars['install_item_dir'] = preg_replace('/template-$/', '', $vars['install_item_dir']);

        return $vars;
    }

    protected function inflectTemplatesVars($vars)
    {
        $vars['install_item_dir'] = preg_replace('/-templates$/', '', $vars['install_item_dir']);
        $vars['install_item_dir'] = preg_replace('/templates-$/', '', $vars['install_item_dir']);

        return $vars;
    }

    protected function inflectCoreVars($vars)
    {
        $vars['install_item_dir'] = preg_replace('/-providers$/', '', $vars['install_item_dir']);
        $vars['install_item_dir'] = preg_replace('/-provider$/', '', $vars['install_item_dir']);
        $vars['install_item_dir'] = preg_replace('/-adapter$/', '', $vars['install_item_dir']);

        return $vars;
    }

    protected function inflectModuleVars($vars)
    {
        $vars['install_item_dir'] = preg_replace('/-module$/', '', $vars['install_item_dir']);
        $vars['install_item_dir'] = preg_replace('/module-$/', '', $vars['install_item_dir']);

        return $vars;
    }

    protected function inflectModulesVars($vars)
    {
        $vars['install_item_dir'] = preg_replace('/-modules$/', '', $vars['install_item_dir']);
        $vars['install_item_dir'] = preg_replace('/modules-$/', '', $vars['install_item_dir']);

        return $vars;
    }

    protected function inflectSkinVars($vars)
    {
        $vars['install_item_dir'] = preg_replace('/-skin$/', '', $vars['install_item_dir']);
        $vars['install_item_dir'] = preg_replace('/skin-$/', '', $vars['install_item_dir']);

        return $vars;
    }

    protected function inflectElementVars($vars)
    {
        $vars['install_item_dir'] = preg_replace('/-elements$/', '', $vars['install_item_dir']);
        $vars['install_item_dir'] = preg_replace('/elements-$/', '', $vars['install_item_dir']);
        $vars['install_item_dir'] = preg_replace('/-element$/', '', $vars['install_item_dir']);
        $vars['install_item_dir'] = preg_replace('/element-$/', '', $vars['install_item_dir']);

        return $vars;
    }
}
