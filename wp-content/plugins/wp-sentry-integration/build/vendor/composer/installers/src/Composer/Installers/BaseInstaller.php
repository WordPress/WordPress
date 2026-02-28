<?php

namespace WPSentry\ScopedVendor\Composer\Installers;

use WPSentry\ScopedVendor\Composer\IO\IOInterface;
use WPSentry\ScopedVendor\Composer\Composer;
use WPSentry\ScopedVendor\Composer\Package\PackageInterface;
abstract class BaseInstaller
{
    /** @var array<string, string> */
    protected $locations = array();
    /** @var Composer */
    protected $composer;
    /** @var PackageInterface */
    protected $package;
    /** @var IOInterface */
    protected $io;
    /**
     * Initializes base installer.
     */
    public function __construct(\WPSentry\ScopedVendor\Composer\Package\PackageInterface $package, \WPSentry\ScopedVendor\Composer\Composer $composer, \WPSentry\ScopedVendor\Composer\IO\IOInterface $io)
    {
        $this->composer = $composer;
        $this->package = $package;
        $this->io = $io;
    }
    /**
     * Return the install path based on package type.
     */
    public function getInstallPath(\WPSentry\ScopedVendor\Composer\Package\PackageInterface $package, string $frameworkType = '') : string
    {
        $type = $this->package->getType();
        $prettyName = $this->package->getPrettyName();
        if (\strpos($prettyName, '/') !== \false) {
            list($vendor, $name) = \explode('/', $prettyName);
        } else {
            $vendor = '';
            $name = $prettyName;
        }
        $availableVars = $this->inflectPackageVars(\compact('name', 'vendor', 'type'));
        $extra = $package->getExtra();
        if (!empty($extra['installer-name'])) {
            $availableVars['name'] = $extra['installer-name'];
        }
        $extra = $this->composer->getPackage()->getExtra();
        if (!empty($extra['installer-paths'])) {
            $customPath = $this->mapCustomInstallPaths($extra['installer-paths'], $prettyName, $type, $vendor);
            if ($customPath !== \false) {
                return $this->templatePath($customPath, $availableVars);
            }
        }
        $packageType = \substr($type, \strlen($frameworkType) + 1);
        $locations = $this->getLocations($frameworkType);
        if (!isset($locations[$packageType])) {
            throw new \InvalidArgumentException(\sprintf('Package type "%s" is not supported', $type));
        }
        return $this->templatePath($locations[$packageType], $availableVars);
    }
    /**
     * For an installer to override to modify the vars per installer.
     *
     * @param  array<string, string> $vars This will normally receive array{name: string, vendor: string, type: string}
     * @return array<string, string>
     */
    public function inflectPackageVars(array $vars) : array
    {
        return $vars;
    }
    /**
     * Gets the installer's locations
     *
     * @return array<string, string> map of package types => install path
     */
    public function getLocations(string $frameworkType)
    {
        return $this->locations;
    }
    /**
     * Replace vars in a path
     *
     * @param  array<string, string> $vars
     */
    protected function templatePath(string $path, array $vars = array()) : string
    {
        if (\strpos($path, '{') !== \false) {
            \extract($vars);
            \preg_match_all('@\\{\\$([A-Za-z0-9_]*)\\}@i', $path, $matches);
            if (!empty($matches[1])) {
                foreach ($matches[1] as $var) {
                    $path = \str_replace('{$' . $var . '}', ${$var}, $path);
                }
            }
        }
        return $path;
    }
    /**
     * Search through a passed paths array for a custom install path.
     *
     * @param  array<string, string[]|string> $paths
     * @return string|false
     */
    protected function mapCustomInstallPaths(array $paths, string $name, string $type, ?string $vendor = null)
    {
        foreach ($paths as $path => $names) {
            $names = (array) $names;
            if (\in_array($name, $names) || \in_array('type:' . $type, $names) || \in_array('vendor:' . $vendor, $names)) {
                return $path;
            }
        }
        return \false;
    }
    protected function pregReplace(string $pattern, string $replacement, string $subject) : string
    {
        $result = \preg_replace($pattern, $replacement, $subject);
        if (null === $result) {
            throw new \RuntimeException('Failed to run preg_replace with ' . $pattern . ': ' . \preg_last_error());
        }
        return $result;
    }
}
