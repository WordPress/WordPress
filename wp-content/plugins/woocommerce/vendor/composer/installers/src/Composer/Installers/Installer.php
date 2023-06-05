<?php

namespace Composer\Installers;

use Composer\Composer;
use Composer\Installer\BinaryInstaller;
use Composer\Installer\LibraryInstaller;
use Composer\IO\IOInterface;
use Composer\Package\PackageInterface;
use Composer\Repository\InstalledRepositoryInterface;
use Composer\Util\Filesystem;
use React\Promise\PromiseInterface;

class Installer extends LibraryInstaller
{

    /**
     * Package types to installer class map
     *
     * @var array
     */
    private $supportedTypes = array(
        'aimeos'       => 'AimeosInstaller',
        'asgard'       => 'AsgardInstaller',
        'attogram'     => 'AttogramInstaller',
        'agl'          => 'AglInstaller',
        'annotatecms'  => 'AnnotateCmsInstaller',
        'bitrix'       => 'BitrixInstaller',
        'bonefish'     => 'BonefishInstaller',
        'cakephp'      => 'CakePHPInstaller',
        'chef'         => 'ChefInstaller',
        'civicrm'      => 'CiviCrmInstaller',
        'ccframework'  => 'ClanCatsFrameworkInstaller',
        'cockpit'      => 'CockpitInstaller',
        'codeigniter'  => 'CodeIgniterInstaller',
        'concrete5'    => 'Concrete5Installer',
        'craft'        => 'CraftInstaller',
        'croogo'       => 'CroogoInstaller',
        'dframe'       => 'DframeInstaller',
        'dokuwiki'     => 'DokuWikiInstaller',
        'dolibarr'     => 'DolibarrInstaller',
        'decibel'      => 'DecibelInstaller',
        'drupal'       => 'DrupalInstaller',
        'elgg'         => 'ElggInstaller',
        'eliasis'      => 'EliasisInstaller',
        'ee3'          => 'ExpressionEngineInstaller',
        'ee2'          => 'ExpressionEngineInstaller',
        'ezplatform'   => 'EzPlatformInstaller',
        'fuel'         => 'FuelInstaller',
        'fuelphp'      => 'FuelphpInstaller',
        'grav'         => 'GravInstaller',
        'hurad'        => 'HuradInstaller',
        'tastyigniter' => 'TastyIgniterInstaller',
        'imagecms'     => 'ImageCMSInstaller',
        'itop'         => 'ItopInstaller',
        'joomla'       => 'JoomlaInstaller',
        'kanboard'     => 'KanboardInstaller',
        'kirby'        => 'KirbyInstaller',
        'known'	       => 'KnownInstaller',
        'kodicms'      => 'KodiCMSInstaller',
        'kohana'       => 'KohanaInstaller',
        'lms'      => 'LanManagementSystemInstaller',
        'laravel'      => 'LaravelInstaller',
        'lavalite'     => 'LavaLiteInstaller',
        'lithium'      => 'LithiumInstaller',
        'magento'      => 'MagentoInstaller',
        'majima'       => 'MajimaInstaller',
        'mantisbt'     => 'MantisBTInstaller',
        'mako'         => 'MakoInstaller',
        'maya'         => 'MayaInstaller',
        'mautic'       => 'MauticInstaller',
        'mediawiki'    => 'MediaWikiInstaller',
        'miaoxing'     => 'MiaoxingInstaller',
        'microweber'   => 'MicroweberInstaller',
        'modulework'   => 'MODULEWorkInstaller',
        'modx'         => 'ModxInstaller',
        'modxevo'      => 'MODXEvoInstaller',
        'moodle'       => 'MoodleInstaller',
        'october'      => 'OctoberInstaller',
        'ontowiki'     => 'OntoWikiInstaller',
        'oxid'         => 'OxidInstaller',
        'osclass'      => 'OsclassInstaller',
        'pxcms'        => 'PxcmsInstaller',
        'phpbb'        => 'PhpBBInstaller',
        'pimcore'      => 'PimcoreInstaller',
        'piwik'        => 'PiwikInstaller',
        'plentymarkets'=> 'PlentymarketsInstaller',
        'ppi'          => 'PPIInstaller',
        'puppet'       => 'PuppetInstaller',
        'radphp'       => 'RadPHPInstaller',
        'phifty'       => 'PhiftyInstaller',
        'porto'        => 'PortoInstaller',
        'processwire'  => 'ProcessWireInstaller',
        'quicksilver'  => 'PantheonInstaller',
        'redaxo'       => 'RedaxoInstaller',
        'redaxo5'      => 'Redaxo5Installer',
        'reindex'      => 'ReIndexInstaller',
        'roundcube'    => 'RoundcubeInstaller',
        'shopware'     => 'ShopwareInstaller',
        'sitedirect'   => 'SiteDirectInstaller',
        'silverstripe' => 'SilverStripeInstaller',
        'smf'          => 'SMFInstaller',
        'starbug'      => 'StarbugInstaller',
        'sydes'        => 'SyDESInstaller',
        'sylius'       => 'SyliusInstaller',
        'symfony1'     => 'Symfony1Installer',
        'tao'          => 'TaoInstaller',
        'thelia'       => 'TheliaInstaller',
        'tusk'         => 'TuskInstaller',
        'typo3-cms'    => 'TYPO3CmsInstaller',
        'typo3-flow'   => 'TYPO3FlowInstaller',
        'userfrosting' => 'UserFrostingInstaller',
        'vanilla'      => 'VanillaInstaller',
        'whmcs'        => 'WHMCSInstaller',
        'winter'       => 'WinterInstaller',
        'wolfcms'      => 'WolfCMSInstaller',
        'wordpress'    => 'WordPressInstaller',
        'yawik'        => 'YawikInstaller',
        'zend'         => 'ZendInstaller',
        'zikula'       => 'ZikulaInstaller',
        'prestashop'   => 'PrestashopInstaller'
    );

    /**
     * Installer constructor.
     *
     * Disables installers specified in main composer extra installer-disable
     * list
     *
     * @param IOInterface          $io
     * @param Composer             $composer
     * @param string               $type
     * @param Filesystem|null      $filesystem
     * @param BinaryInstaller|null $binaryInstaller
     */
    public function __construct(
        IOInterface $io,
        Composer $composer,
        $type = 'library',
        Filesystem $filesystem = null,
        BinaryInstaller $binaryInstaller = null
    ) {
        parent::__construct($io, $composer, $type, $filesystem,
            $binaryInstaller);
        $this->removeDisabledInstallers();
    }

    /**
     * {@inheritDoc}
     */
    public function getInstallPath(PackageInterface $package)
    {
        $type = $package->getType();
        $frameworkType = $this->findFrameworkType($type);

        if ($frameworkType === false) {
            throw new \InvalidArgumentException(
                'Sorry the package type of this package is not yet supported.'
            );
        }

        $class = 'Composer\\Installers\\' . $this->supportedTypes[$frameworkType];
        $installer = new $class($package, $this->composer, $this->getIO());

        return $installer->getInstallPath($package, $frameworkType);
    }

    public function uninstall(InstalledRepositoryInterface $repo, PackageInterface $package)
    {
        $installPath = $this->getPackageBasePath($package);
        $io = $this->io;
        $outputStatus = function () use ($io, $installPath) {
            $io->write(sprintf('Deleting %s - %s', $installPath, !file_exists($installPath) ? '<comment>deleted</comment>' : '<error>not deleted</error>'));
        };

        $promise = parent::uninstall($repo, $package);

        // Composer v2 might return a promise here
        if ($promise instanceof PromiseInterface) {
            return $promise->then($outputStatus);
        }

        // If not, execute the code right away as parent::uninstall executed synchronously (composer v1, or v2 without async)
        $outputStatus();

        return null;
    }

    /**
     * {@inheritDoc}
     */
    public function supports($packageType)
    {
        $frameworkType = $this->findFrameworkType($packageType);

        if ($frameworkType === false) {
            return false;
        }

        $locationPattern = $this->getLocationPattern($frameworkType);

        return preg_match('#' . $frameworkType . '-' . $locationPattern . '#', $packageType, $matches) === 1;
    }

    /**
     * Finds a supported framework type if it exists and returns it
     *
     * @param  string       $type
     * @return string|false
     */
    protected function findFrameworkType($type)
    {
        krsort($this->supportedTypes);

        foreach ($this->supportedTypes as $key => $val) {
            if ($key === substr($type, 0, strlen($key))) {
                return substr($type, 0, strlen($key));
            }
        }

        return false;
    }

    /**
     * Get the second part of the regular expression to check for support of a
     * package type
     *
     * @param  string $frameworkType
     * @return string
     */
    protected function getLocationPattern($frameworkType)
    {
        $pattern = false;
        if (!empty($this->supportedTypes[$frameworkType])) {
            $frameworkClass = 'Composer\\Installers\\' . $this->supportedTypes[$frameworkType];
            /** @var BaseInstaller $framework */
            $framework = new $frameworkClass(null, $this->composer, $this->getIO());
            $locations = array_keys($framework->getLocations());
            $pattern = $locations ? '(' . implode('|', $locations) . ')' : false;
        }

        return $pattern ? : '(\w+)';
    }

    /**
     * Get I/O object
     *
     * @return IOInterface
     */
    private function getIO()
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
     *
     * @return void
     */
    protected function removeDisabledInstallers()
    {
        $extra = $this->composer->getPackage()->getExtra();

        if (!isset($extra['installer-disable']) || $extra['installer-disable'] === false) {
            // No installers are disabled
            return;
        }

        // Get installers to disable
        $disable = $extra['installer-disable'];

        // Ensure $disabled is an array
        if (!is_array($disable)) {
            $disable = array($disable);
        }

        // Check which installers should be disabled
        $all = array(true, "all", "*");
        $intersect = array_intersect($all, $disable);
        if (!empty($intersect)) {
            // Disable all installers
            $this->supportedTypes = array();
        } else {
            // Disable specified installers
            foreach ($disable as $key => $installer) {
                if (is_string($installer) && key_exists($installer, $this->supportedTypes)) {
                    unset($this->supportedTypes[$installer]);
                }
            }
        }
    }
}
