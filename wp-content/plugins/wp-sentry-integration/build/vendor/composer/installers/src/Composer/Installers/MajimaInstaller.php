<?php

namespace WPSentry\ScopedVendor\Composer\Installers;

/**
 * Plugin/theme installer for majima
 * @author David Neustadt
 */
class MajimaInstaller extends \WPSentry\ScopedVendor\Composer\Installers\BaseInstaller
{
    /** @var array<string, string> */
    protected $locations = array('plugin' => 'plugins/{$name}/');
    /**
     * Transforms the names
     *
     * @param array<string, string> $vars
     * @return array<string, string>
     */
    public function inflectPackageVars(array $vars) : array
    {
        return $this->correctPluginName($vars);
    }
    /**
     * Change hyphenated names to camelcase
     *
     * @param array<string, string> $vars
     * @return array<string, string>
     */
    private function correctPluginName(array $vars) : array
    {
        $camelCasedName = \preg_replace_callback('/(-[a-z])/', function ($matches) {
            return \strtoupper($matches[0][1]);
        }, $vars['name']);
        if (null === $camelCasedName) {
            throw new \RuntimeException('Failed to run preg_replace_callback: ' . \preg_last_error());
        }
        $vars['name'] = \ucfirst($camelCasedName);
        return $vars;
    }
}
