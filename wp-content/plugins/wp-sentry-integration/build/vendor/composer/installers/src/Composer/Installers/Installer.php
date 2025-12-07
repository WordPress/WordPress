<?php

namespace WPSentry\ScopedVendor\Composer\Installers;

use WPSentry\ScopedVendor\Composer\Composer;
use WPSentry\ScopedVendor\Composer\Installer\BinaryInstaller;
use WPSentry\ScopedVendor\Composer\Installer\LibraryInstaller;
use WPSentry\ScopedVendor\Composer\IO\IOInterface;
use WPSentry\ScopedVendor\Composer\Package\Package;
use WPSentry\ScopedVendor\Composer\Package\PackageInterface;
use WPSentry\ScopedVendor\Composer\Repository\InstalledRepositoryInterface;
use WPSentry\ScopedVendor\Composer\Util\Filesystem;
use WPSentry\ScopedVendor\React\Promise\PromiseInterface;
class Installer extends \WPSentry\ScopedVendor\Composer\Installer\LibraryInstaller
{
    /**
     * Package types to installer class map
     *
     * @var array<string, string>
     */
    private $supportedTypes = array('akaunting' => 'AkauntingInstaller', 'asgard' => 'AsgardInstaller', 'attogram' => 'AttogramInstaller', 'agl' => 'AglInstaller', 'annotatecms' => 'AnnotateCmsInstaller', 'bitrix' => 'BitrixInstaller', 'botble' => 'BotbleInstaller', 'bonefish' => 'BonefishInstaller', 'cakephp' => 'CakePHPInstaller', 'chef' => 'ChefInstaller', 'civicrm' => 'CiviCrmInstaller', 'ccframework' => 'ClanCatsFrameworkInstaller', 'cockpit' => 'CockpitInstaller', 'codeigniter' => 'CodeIgniterInstaller', 'concrete5' => 'Concrete5Installer', 'concretecms' => 'ConcreteCMSInstaller', 'croogo' => 'CroogoInstaller', 'dframe' => 'DframeInstaller', 'dokuwiki' => 'DokuWikiInstaller', 'dolibarr' => 'DolibarrInstaller', 'decibel' => 'DecibelInstaller', 'drupal' => 'DrupalInstaller', 'elgg' => 'ElggInstaller', 'eliasis' => 'EliasisInstaller', 'ee3' => 'ExpressionEngineInstaller', 'ee2' => 'ExpressionEngineInstaller', 'ezplatform' => 'EzPlatformInstaller', 'fork' => 'ForkCMSInstaller', 'fuel' => 'FuelInstaller', 'fuelphp' => 'FuelphpInstaller', 'grav' => 'GravInstaller', 'hurad' => 'HuradInstaller', 'tastyigniter' => 'TastyIgniterInstaller', 'imagecms' => 'ImageCMSInstaller', 'itop' => 'ItopInstaller', 'kanboard' => 'KanboardInstaller', 'known' => 'KnownInstaller', 'kodicms' => 'KodiCMSInstaller', 'kohana' => 'KohanaInstaller', 'lms' => 'LanManagementSystemInstaller', 'laravel' => 'LaravelInstaller', 'lavalite' => 'LavaLiteInstaller', 'lithium' => 'LithiumInstaller', 'magento' => 'MagentoInstaller', 'majima' => 'MajimaInstaller', 'mantisbt' => 'MantisBTInstaller', 'mako' => 'MakoInstaller', 'matomo' => 'MatomoInstaller', 'maya' => 'MayaInstaller', 'mautic' => 'MauticInstaller', 'mediawiki' => 'MediaWikiInstaller', 'miaoxing' => 'MiaoxingInstaller', 'microweber' => 'MicroweberInstaller', 'modulework' => 'MODULEWorkInstaller', 'modx' => 'ModxInstaller', 'modxevo' => 'MODXEvoInstaller', 'moodle' => 'MoodleInstaller', 'october' => 'OctoberInstaller', 'ontowiki' => 'OntoWikiInstaller', 'oxid' => 'OxidInstaller', 'osclass' => 'OsclassInstaller', 'pxcms' => 'PxcmsInstaller', 'phpbb' => 'PhpBBInstaller', 'piwik' => 'PiwikInstaller', 'plentymarkets' => 'PlentymarketsInstaller', 'ppi' => 'PPIInstaller', 'puppet' => 'PuppetInstaller', 'radphp' => 'RadPHPInstaller', 'phifty' => 'PhiftyInstaller', 'porto' => 'PortoInstaller', 'processwire' => 'ProcessWireInstaller', 'quicksilver' => 'PantheonInstaller', 'redaxo' => 'RedaxoInstaller', 'redaxo5' => 'Redaxo5Installer', 'reindex' => 'ReIndexInstaller', 'roundcube' => 'RoundcubeInstaller', 'shopware' => 'ShopwareInstaller', 'sitedirect' => 'SiteDirectInstaller', 'silverstripe' => 'SilverStripeInstaller', 'smf' => 'SMFInstaller', 'starbug' => 'StarbugInstaller', 'sydes' => 'SyDESInstaller', 'sylius' => 'SyliusInstaller', 'tao' => 'TaoInstaller', 'thelia' => 'TheliaInstaller', 'tusk' => 'TuskInstaller', 'userfrosting' => 'UserFrostingInstaller', 'vanilla' => 'VanillaInstaller', 'whmcs' => 'WHMCSInstaller', 'winter' => 'WinterInstaller', 'wolfcms' => 'WolfCMSInstaller', 'wordpress' => 'WordPressInstaller', 'yawik' => 'YawikInstaller', 'zend' => 'ZendInstaller', 'zikula' => 'ZikulaInstaller', 'prestashop' => 'PrestashopInstaller');
    /**
     * Disables installers specified in main composer extra installer-disable
     * list
     */
    public function __construct(\WPSentry\ScopedVendor\Composer\IO\IOInterface $io, \WPSentry\ScopedVendor\Composer\Composer $composer, string $type = 'library', ?\WPSentry\ScopedVendor\Composer\Util\Filesystem $filesystem = null, ?\WPSentry\ScopedVendor\Composer\Installer\BinaryInstaller $binaryInstaller = null)
    {
        parent::__construct($io, $composer, $type, $filesystem, $binaryInstaller);
        $this->removeDisabledInstallers();
    }
    /**
     * {@inheritDoc}
     */
    public function getInstallPath(\WPSentry\ScopedVendor\Composer\Package\PackageInterface $package)
    {
        $type = $package->getType();
        $frameworkType = $this->findFrameworkType($type);
        if ($frameworkType === \false) {
            throw new \InvalidArgumentException('Sorry the package type of this package is not yet supported.');
        }
        $class = 'Composer\\Installers\\' . $this->supportedTypes[$frameworkType];
        /**
         * @var BaseInstaller
         */
        $installer = new $class($package, $this->composer, $this->getIO());
        $path = $installer->getInstallPath($package, $frameworkType);
        if (!$this->filesystem->isAbsolutePath($path)) {
            $path = \getcwd() . '/' . $path;
        }
        return $path;
    }
    public function uninstall(\WPSentry\ScopedVendor\Composer\Repository\InstalledRepositoryInterface $repo, \WPSentry\ScopedVendor\Composer\Package\PackageInterface $package)
    {
        $installPath = $this->getPackageBasePath($package);
        $io = $this->io;
        $outputStatus = function () use($io, $installPath) {
            $io->write(\sprintf('Deleting %s - %s', $installPath, !\file_exists($installPath) ? '<comment>deleted</comment>' : '<error>not deleted</error>'));
        };
        $promise = parent::uninstall($repo, $package);
        // Composer v2 might return a promise here
        if ($promise instanceof \WPSentry\ScopedVendor\React\Promise\PromiseInterface) {
            return $promise->then($outputStatus);
        }
        // If not, execute the code right away as parent::uninstall executed synchronously (composer v1, or v2 without async)
        $outputStatus();
        return null;
    }
    /**
     * {@inheritDoc}
     *
     * @param string $packageType
     */
    public function supports($packageType)
    {
        $frameworkType = $this->findFrameworkType($packageType);
        if ($frameworkType === \false) {
            return \false;
        }
        $locationPattern = $this->getLocationPattern($frameworkType);
        return \preg_match('#' . $frameworkType . '-' . $locationPattern . '#', $packageType, $matches) === 1;
    }
    /**
     * Finds a supported framework type if it exists and returns it
     *
     * @return string|false
     */
    protected function findFrameworkType(string $type)
    {
        \krsort($this->supportedTypes);
        foreach ($this->supportedTypes as $key => $val) {
            if ($key === \substr($type, 0, \strlen($key))) {
                return \substr($type, 0, \strlen($key));
            }
        }
        return \false;
    }
    /**
     * Get the second part of the regular expression to check for support of a
     * package type
     */
    protected function getLocationPattern(string $frameworkType) : string
    {
        $pattern = null;
        if (!empty($this->supportedTypes[$frameworkType])) {
            $frameworkClass = 'Composer\\Installers\\' . $this->supportedTypes[$frameworkType];
            /** @var BaseInstaller $framework */
            $framework = new $frameworkClass(new \WPSentry\ScopedVendor\Composer\Package\Package('dummy/pkg', '1.0.0.0', '1.0.0'), $this->composer, $this->getIO());
            $locations = \array_keys($framework->getLocations($frameworkType));
            if ($locations) {
                $pattern = '(' . \implode('|', $locations) . ')';
            }
        }
        return $pattern ?: '(\\w+)';
    }
    private function getIO() : \WPSentry\ScopedVendor\Composer\IO\IOInterface
    {
        return $this->io;
    }
    /**
     * Look for installers set to be disabled in composer's extra config and
     * remove them from the list of supported installers.
     *
     * Globals:
     *  - true, "all", and "*" - disable all installers.
     *  - false - enable all installers (useful with
     *     wikimedia/composer-merge-plugin or similar)
     */
    protected function removeDisabledInstallers() : void
    {
        $extra = $this->composer->getPackage()->getExtra();
        if (!isset($extra['installer-disable']) || $extra['installer-disable'] === \false) {
            // No installers are disabled
            return;
        }
        // Get installers to disable
        $disable = $extra['installer-disable'];
        // Ensure $disabled is an array
        if (!\is_array($disable)) {
            $disable = array($disable);
        }
        // Check which installers should be disabled
        $all = array(\true, "all", "*");
        $intersect = \array_intersect($all, $disable);
        if (!empty($intersect)) {
            // Disable all installers
            $this->supportedTypes = array();
            return;
        }
        // Disable specified installers
        foreach ($disable as $key => $installer) {
            if (\is_string($installer) && \key_exists($installer, $this->supportedTypes)) {
                unset($this->supportedTypes[$installer]);
            }
        }
    }
}
