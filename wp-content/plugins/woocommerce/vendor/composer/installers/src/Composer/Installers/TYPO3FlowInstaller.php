<?php
namespace Composer\Installers;

/**
 * An installer to handle TYPO3 Flow specifics when installing packages.
 */
class TYPO3FlowInstaller extends BaseInstaller
{
    protected $locations = array(
        'package'       => 'Packages/Application/{$name}/',
        'framework'     => 'Packages/Framework/{$name}/',
        'plugin'        => 'Packages/Plugins/{$name}/',
        'site'          => 'Packages/Sites/{$name}/',
        'boilerplate'   => 'Packages/Boilerplates/{$name}/',
        'build'         => 'Build/{$name}/',
    );

    /**
     * Modify the package name to be a TYPO3 Flow style key.
     *
     * @param  array $vars
     * @return array
     */
    public function inflectPackageVars($vars)
    {
        $autoload = $this->package->getAutoload();
        if (isset($autoload['psr-0']) && is_array($autoload['psr-0'])) {
            $namespace = key($autoload['psr-0']);
            $vars['name'] = str_replace('\\', '.', $namespace);
        }
        if (isset($autoload['psr-4']) && is_array($autoload['psr-4'])) {
            $namespace = key($autoload['psr-4']);
            $vars['name'] = rtrim(str_replace('\\', '.', $namespace), '.');
        }

        return $vars;
    }
}
