<?php

namespace WPSentry\ScopedVendor\Composer\Installers;

use WPSentry\ScopedVendor\Composer\Package\PackageInterface;
class MauticInstaller extends \WPSentry\ScopedVendor\Composer\Installers\BaseInstaller
{
    /** @var array<string, string> */
    protected $locations = array('plugin' => 'plugins/{$name}/', 'theme' => 'themes/{$name}/', 'core' => 'app/');
    private function getDirectoryName() : string
    {
        $extra = $this->package->getExtra();
        if (!empty($extra['install-directory-name'])) {
            return $extra['install-directory-name'];
        }
        return $this->toCamelCase($this->package->getPrettyName());
    }
    private function toCamelCase(string $packageName) : string
    {
        return \str_replace(' ', '', \ucwords(\str_replace('-', ' ', \basename($packageName))));
    }
    /**
     * Format package name of mautic-plugins to CamelCase
     */
    public function inflectPackageVars(array $vars) : array
    {
        if ($vars['type'] == 'mautic-plugin' || $vars['type'] == 'mautic-theme') {
            $directoryName = $this->getDirectoryName();
            $vars['name'] = $directoryName;
        }
        return $vars;
    }
}
