<?php

namespace WPSentry\ScopedVendor\Composer\Installers;

use WPSentry\ScopedVendor\Composer\Package\PackageInterface;
class ExpressionEngineInstaller extends \WPSentry\ScopedVendor\Composer\Installers\BaseInstaller
{
    /** @var array<string, string> */
    private $ee2Locations = array('addon' => 'system/expressionengine/third_party/{$name}/', 'theme' => 'themes/third_party/{$name}/');
    /** @var array<string, string> */
    private $ee3Locations = array('addon' => 'system/user/addons/{$name}/', 'theme' => 'themes/user/{$name}/');
    public function getLocations(string $frameworkType) : array
    {
        if ($frameworkType === 'ee2') {
            $this->locations = $this->ee2Locations;
        } else {
            $this->locations = $this->ee3Locations;
        }
        return $this->locations;
    }
}
