<?php
namespace Composer\Installers\Test;

use Composer\Installers\OctoberInstaller;
use Composer\Package\Package;
use Composer\Composer;

class OctoberInstallerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var OctoberInstaller
     */
    private $installer;

    public function setUp()
    {
        $this->installer = new OctoberInstaller(
            new Package('NyanCat', '4.2', '4.2'),
            new Composer()
        );
    }

    /**
     * @dataProvider packageNameInflectionProvider
     */
    public function testInflectPackageVars($type, $name, $expected)
    {
        $this->assertEquals(
            $this->installer->inflectPackageVars(array('name' => $name, 'type' => $type)),
            array('name' => $expected, 'type' => $type)
        );
    }

    public function packageNameInflectionProvider()
    {
        return array(
            array(
                'october-plugin',
                'subpagelist',
                'subpagelist',
            ),
            array(
                'october-plugin',
                'subpagelist-plugin',
                'subpagelist',
            ),
            array(
                'october-plugin',
                'semanticoctober',
                'semanticoctober',
            ),
            // tests that exactly one '-theme' is cut off
            array(
                'october-theme',
                'some-theme-theme',
                'some-theme',
            ),
            // tests that names without '-theme' suffix stay valid
            array(
                'october-theme',
                'someothertheme',
                'someothertheme',
            ),
        );
    }
}