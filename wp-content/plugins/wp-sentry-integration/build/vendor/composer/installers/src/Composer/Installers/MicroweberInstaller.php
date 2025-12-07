<?php

namespace WPSentry\ScopedVendor\Composer\Installers;

class MicroweberInstaller extends \WPSentry\ScopedVendor\Composer\Installers\BaseInstaller
{
    /** @var array<string, string> */
    protected $locations = array('module' => 'userfiles/modules/{$install_item_dir}/', 'module-skin' => 'userfiles/modules/{$install_item_dir}/templates/', 'template' => 'userfiles/templates/{$install_item_dir}/', 'element' => 'userfiles/elements/{$install_item_dir}/', 'vendor' => 'vendor/{$install_item_dir}/', 'components' => 'components/{$install_item_dir}/');
    /**
     * Format package name.
     *
     * For package type microweber-module, cut off a trailing '-module' if present
     *
     * For package type microweber-template, cut off a trailing '-template' if present.
     */
    public function inflectPackageVars(array $vars) : array
    {
        if ($this->package->getTargetDir() !== null && $this->package->getTargetDir() !== '') {
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
    /**
     * @param array<string, string> $vars
     * @return array<string, string>
     */
    protected function inflectTemplateVars(array $vars) : array
    {
        $vars['install_item_dir'] = $this->pregReplace('/-template$/', '', $vars['install_item_dir']);
        $vars['install_item_dir'] = $this->pregReplace('/template-$/', '', $vars['install_item_dir']);
        return $vars;
    }
    /**
     * @param array<string, string> $vars
     * @return array<string, string>
     */
    protected function inflectTemplatesVars(array $vars) : array
    {
        $vars['install_item_dir'] = $this->pregReplace('/-templates$/', '', $vars['install_item_dir']);
        $vars['install_item_dir'] = $this->pregReplace('/templates-$/', '', $vars['install_item_dir']);
        return $vars;
    }
    /**
     * @param array<string, string> $vars
     * @return array<string, string>
     */
    protected function inflectCoreVars(array $vars) : array
    {
        $vars['install_item_dir'] = $this->pregReplace('/-providers$/', '', $vars['install_item_dir']);
        $vars['install_item_dir'] = $this->pregReplace('/-provider$/', '', $vars['install_item_dir']);
        $vars['install_item_dir'] = $this->pregReplace('/-adapter$/', '', $vars['install_item_dir']);
        return $vars;
    }
    /**
     * @param array<string, string> $vars
     * @return array<string, string>
     */
    protected function inflectModuleVars(array $vars) : array
    {
        $vars['install_item_dir'] = $this->pregReplace('/-module$/', '', $vars['install_item_dir']);
        $vars['install_item_dir'] = $this->pregReplace('/module-$/', '', $vars['install_item_dir']);
        return $vars;
    }
    /**
     * @param array<string, string> $vars
     * @return array<string, string>
     */
    protected function inflectModulesVars(array $vars) : array
    {
        $vars['install_item_dir'] = $this->pregReplace('/-modules$/', '', $vars['install_item_dir']);
        $vars['install_item_dir'] = $this->pregReplace('/modules-$/', '', $vars['install_item_dir']);
        return $vars;
    }
    /**
     * @param array<string, string> $vars
     * @return array<string, string>
     */
    protected function inflectSkinVars(array $vars) : array
    {
        $vars['install_item_dir'] = $this->pregReplace('/-skin$/', '', $vars['install_item_dir']);
        $vars['install_item_dir'] = $this->pregReplace('/skin-$/', '', $vars['install_item_dir']);
        return $vars;
    }
    /**
     * @param array<string, string> $vars
     * @return array<string, string>
     */
    protected function inflectElementVars(array $vars) : array
    {
        $vars['install_item_dir'] = $this->pregReplace('/-elements$/', '', $vars['install_item_dir']);
        $vars['install_item_dir'] = $this->pregReplace('/elements-$/', '', $vars['install_item_dir']);
        $vars['install_item_dir'] = $this->pregReplace('/-element$/', '', $vars['install_item_dir']);
        $vars['install_item_dir'] = $this->pregReplace('/element-$/', '', $vars['install_item_dir']);
        return $vars;
    }
}
