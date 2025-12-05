<?php
/**
 * Piwik - free/libre analytics platform
 *
 * @link https://matomo.org
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 *
 */

namespace Piwik\Plugins\WordPress\Commands;

use Piwik\AssetManager;
use Piwik\Filesystem;
use Piwik\FrontController;
use Piwik\Piwik;
use Piwik\Plugin\ConsoleCommand;
use Piwik\Plugins\Installation\ServerFilesGenerator;
use Piwik\Plugins\WordPress\WpAssetManager;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

if (!defined( 'ABSPATH')) {
	exit; // if accessed directly
}

class GenerateCoreAssets extends ConsoleCommand
{
    protected function configure()
    {
        $this->setName('wordpress:generate-core-assets');
        $this->setDescription('Generate the core JS asset file');
    }

    protected function doExecute(): int
    {
	    ServerFilesGenerator::createFilesForSecurity();

    	Piwik::addAction('AssetManager.makeNewAssetManagerObject', function (&$assetManager) {
		    $assetManager = new AssetManager();
	    });

	    Piwik::addAction('AssetManager.getJavaScriptFiles', function (&$files){
	    	foreach ($files as $index => $file) {
	    		$basename = basename($file);
			    $basename = strtolower($basename);
	    		if ($basename === 'jquery.js'
			        || $basename === 'jquery.min.js'
			        || $basename === 'materialize.min.js' // we embed it manually as it needs to be loaded before jquery ui
			        || $basename === 'jquery-ui.js'
			        || $basename === 'jquery-ui.min.js') {
				    // we are not allowed to ship matomo with that
				    $files[$index] = null;
			    }
		    }
		    $files = array_values(array_filter($files));
	    });

	    FrontController::getInstance()->init();

        // make sure it will regenerate the core asset file
        Filesystem::deleteAllCacheOnUpdate();
        $assetManager = AssetManager::getInstance();
        if ($assetManager instanceof WpAssetManager) {
        	throw new \Exception('Wrong asset manager is used, it should use the core assets manager');
        }
        $content = $assetManager->getMergedCoreJavaScript()->getContent();

        file_put_contents(plugin_dir_path(MATOMO_ANALYTICS_FILE) . 'assets/js/asset_manager_core_js.js', $content);

        $this->getOutput()->writeln("<info>Finished generating core assets.</info>");

        return self::SUCCESS;
    }

}
