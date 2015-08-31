<?php
namespace Composer\Installers\Test;

use Composer\Installers\MediaWikiInstaller;
use Composer\Package\Package;
use Composer\Composer;

class MediaWikiInstallerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var MediaWikiInstaller
     */
    private $installer;

    public function setUp()
    {
        $this->installer = new MediaWikiInstaller(
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
                'mediawiki-extension',
                'sub-page-list',
                'SubPageList',
            ),
            array(
                'mediawiki-extension',
                'sub-page-list-extension',
                'SubPageList',
            ),
            array(
                'mediawiki-extension',
                'semantic-mediawiki',
                'SemanticMediawiki',
            ),
            // tests that exactly one '-skin' is cut off, and that skins do not get ucwords treatment like extensions
            array(
                'mediawiki-skin',
                'some-skin-skin',
                'some-skin',
            ),
            // tests that names without '-skin' suffix stay valid
            array(
                'mediawiki-skin',
                'someotherskin',
                'someotherskin',
            ),
        );
    }
}
