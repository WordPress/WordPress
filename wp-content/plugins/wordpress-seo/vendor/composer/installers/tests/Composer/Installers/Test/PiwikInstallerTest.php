<?php
namespace Composer\Installers\Test;

use Composer\Composer;
use Composer\Installers\PiwikInstaller;
use Composer\Package\Package;
use Composer\Package\PackageInterface;

/**
 * Class PiwikInstallerTest
 *
 * @package Composer\Installers\Test
 */
class PiwikInstallerTest extends TestCase
{
    /**
     * @varComposer
     */
    private $composer;

    /**
     * @var PackageInterface
     */
    private $io;

    /**
     * @var Package
     */
    private $package;

    /**
     * setUp
     *
     * @return void
     */
    public function setUp()
    {
        $this->package = new Package('VisitSummary', '1.0', '1.0');
        $this->io = $this->getMock('Composer\IO\PackageInterface');
        $this->composer = new Composer();
    }

    /**
     * testInflectPackageVars
     *
     * @return void
     */
    public function testInflectPackageVars()
    {
        $installer = new PiwikInstaller($this->package, $this->composer);
        $result = $installer->inflectPackageVars(array('name' => 'VisitSummary'));
        $this->assertEquals($result, array('name' => 'VisitSummary'));

        $installer = new PiwikInstaller($this->package, $this->composer);
        $result = $installer->inflectPackageVars(array('name' => 'visit-summary'));
        $this->assertEquals($result, array('name' => 'VisitSummary'));

        $installer = new PiwikInstaller($this->package, $this->composer);
        $result = $installer->inflectPackageVars(array('name' => 'visit_summary'));
        $this->assertEquals($result, array('name' => 'VisitSummary'));
    }

}
