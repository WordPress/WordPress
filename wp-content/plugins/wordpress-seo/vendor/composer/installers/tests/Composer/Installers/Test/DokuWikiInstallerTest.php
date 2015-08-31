<?php
namespace Composer\Installers\Test;

use Composer\Installers\DokuWikiInstaller;
use Composer\Package\Package;
use Composer\Composer;

class DokuWikiInstallerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var DokuWikiInstaller
     */
    private $installer;

    public function setUp()
    {
        $this->installer = new DokuWikiInstaller(
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
            $this->installer->inflectPackageVars(array('name' => $name, 'type'=>$type)),
            array('name' => $expected, 'type'=>$type)
        );
    }

    public function packageNameInflectionProvider()
    {
        return array(
            array(
                'dokuwiki-plugin',
                'dokuwiki-test-plugin',
                'test',
            ),
            array(
                'dokuwiki-plugin',
                'test-plugin',
                'test',
            ),
            array(
                'dokuwiki-plugin',
                'dokuwiki_test',
                'test',
            ),
            array(
                'dokuwiki-plugin',
                'test',
                'test',
            ),
            array(
                'dokuwiki-plugin',
                'test-template',
                'test-template',
            ),
            array(
                'dokuwiki-template',
                'dokuwiki-test-template',
                'test',
            ),
            array(
                'dokuwiki-template',
                'test-template',
                'test',
            ),
            array(
                'dokuwiki-template',
                'dokuwiki_test',
                'test',
            ),
            array(
                'dokuwiki-template',
                'test',
                'test',
            ),
            array(
                'dokuwiki-template',
                'test-plugin',
                'test-plugin',
            ),
        );
    }
}
