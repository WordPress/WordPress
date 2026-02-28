<?php

namespace WPSentry\ScopedVendor\Composer\Installers;

use WPSentry\ScopedVendor\Composer\Package\PackageInterface;
class OxidInstaller extends \WPSentry\ScopedVendor\Composer\Installers\BaseInstaller
{
    const VENDOR_PATTERN = '/^modules\\/(?P<vendor>.+)\\/.+/';
    /** @var array<string, string> */
    protected $locations = array('module' => 'modules/{$name}/', 'theme' => 'application/views/{$name}/', 'out' => 'out/{$name}/');
    public function getInstallPath(\WPSentry\ScopedVendor\Composer\Package\PackageInterface $package, string $frameworkType = '') : string
    {
        $installPath = parent::getInstallPath($package, $frameworkType);
        $type = $this->package->getType();
        if ($type === 'oxid-module') {
            $this->prepareVendorDirectory($installPath);
        }
        return $installPath;
    }
    /**
     * Makes sure there is a vendormetadata.php file inside
     * the vendor folder if there is a vendor folder.
     */
    protected function prepareVendorDirectory(string $installPath) : void
    {
        $matches = '';
        $hasVendorDirectory = \preg_match(self::VENDOR_PATTERN, $installPath, $matches);
        if (!$hasVendorDirectory) {
            return;
        }
        $vendorDirectory = $matches['vendor'];
        $vendorPath = \getcwd() . '/modules/' . $vendorDirectory;
        if (!\file_exists($vendorPath)) {
            \mkdir($vendorPath, 0755, \true);
        }
        $vendorMetaDataPath = $vendorPath . '/vendormetadata.php';
        \touch($vendorMetaDataPath);
    }
}
