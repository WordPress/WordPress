<?php
/**
 * Matomo - free/libre analytics platform
 *
 * @link https://matomo.org
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 *
 */

namespace Piwik\Plugins\WordPress;

use Piwik\AssetManager;
use Piwik\AssetManager\UIAssetFetcher;
use Piwik\Container\StaticContainer;
use Piwik\Plugins\WordPress\AssetManager\NeverDeleteOnDiskUiAsset;
use Piwik\Translation\Translator;
use Piwik\ProxyHttp;
use Piwik\Version;

if (!defined( 'ABSPATH')) {
    exit; // if accessed directly
}

class WpAssetManager extends AssetManager
{
	public function __construct() {
		parent::__construct();
	}

    public function getMergedCoreJavaScript() {
		$path = rtrim( plugin_dir_path( MATOMO_ANALYTICS_FILE ), '/' ) . '/assets/js';
		$file = 'asset_manager_core_js.js';

		return new NeverDeleteOnDiskUiAsset( $path, $file );
	}

	private function isWp55OrOlder()
	{
		$wp_version = get_bloginfo( 'version' );

		return $wp_version && 1 === version_compare('5.6', $wp_version);
	}

	public function getJsInclusionDirective(bool $deferJS = false): string
	{
		$translator = StaticContainer::get(Translator::class);
		$result = "<script type=\"text/javascript\">\n" . $translator->getJavascriptTranslations() . "\n</script>";

		$jsFiles = array();
		$jsFiles[] = "jquery/jquery.js";
		$jsFiles[] = "node_modules/@materializecss/materialize/dist/js/materialize.min.js";

		if ($this->isWp55OrOlder()) {
			$jsFiles[] = 'jquery/ui/widget.min.js';
		}

		$jsFiles[] = 'jquery/ui/core.min.js';
		$jsFiles[] = 'jquery/ui/mouse.min.js';
		$jsFiles[] = 'jquery/ui/selectable.min.js';
		$jsFiles[] = 'jquery/ui/autocomplete.min.js';

		if ($this->isWp55OrOlder()) {
			$jsFiles[] = 'jquery/ui/position.min.js';
		}

		$jsFiles[] = 'jquery/ui/resizable.min.js';
		$jsFiles[] = 'jquery/ui/datepicker.min.js';
		$jsFiles[] = 'jquery/ui/dialog.min.js';
		$jsFiles[] = 'jquery/ui/menu.min.js';
		$jsFiles[] = 'jquery/ui/draggable.min.js';
		$jsFiles[] = 'jquery/ui/droppable.min.js';
		$jsFiles[] = 'jquery/ui/tooltip.min.js';
		$jsFiles[] = 'jquery/ui/sortable.min.js';
		$jsFiles[] = 'jquery/ui/spinner.min.js';
		$jsFiles[] = 'jquery/ui/tabs.min.js';
		$jsFiles[] = 'jquery/ui/button.min.js';
		$jsFiles[] = 'jquery/ui/effect.min.js';
		$jsFiles[] = 'jquery/ui/effect-highlight.min.js';

		foreach ($jsFiles as $jsFile) {
		    if (strpos($jsFile, 'node_modules') === 0) {
		        $jQueryPath = $jsFile;
            } else {
                $jQueryPath = includes_url('js/' . $jsFile);
			    if (ProxyHttp::isHttps()) {
				    $jQueryPath = str_replace('http://', 'https://', $jQueryPath);
			    } else {
				    $jQueryPath = str_replace('http://', '//', $jQueryPath);
			    }
            }
			$result .= sprintf(self::JS_IMPORT_DIRECTIVE, $jQueryPath);
		}

		$result .= "<script type=\"text/javascript\">window.$ = jQuery;</script>";

		$result .= sprintf(self::JS_IMPORT_DIRECTIVE, '../assets/js/asset_manager_core_js.js?v=' . Version::VERSION);

		// may need to change or allow to this... but how to make the wp-includes relative?
		// $result .= sprintf(self::JS_IMPORT_DIRECTIVE, plugins_url( 'assets/js/asset_manager_core_js.js', MATOMO_ANALYTICS_FILE )  . '?v=' . Version::VERSION);

		if ($this->isMergedAssetsDisabled()) {
			$this->getMergedNonCoreJSAsset()->delete();
			$result .= $this->getIndividualJsIncludesFromAssetFetcher($this->getNonCoreJScriptFetcher());
			$result .= $this->getIndividualJsIncludesFromAssetFetcher($this->getPluginUmdJScriptFetcher());
		} else {
			$result .= sprintf(self::JS_IMPORT_DIRECTIVE, self::GET_NON_CORE_JS_MODULE_ACTION);
			$result .= $this->getPluginUmdChunks();
		}
		return $result;
	}

    /**
     * Performs the same functionality as AssetManager::getIndividualJsIncludesFromAssetFetcher(),
     * except when an asset to a non-core plugin is found, it's correctly mapped to it's location
     * within a Matomo for WordPress install (using the plugins_url() function).
     *
     * @param UIAssetFetcher $assetFetcher
     * @return string
     */
    protected function getIndividualJsIncludesFromAssetFetcher($assetFetcher): string
    {
        $wpPluginsDir = rtrim(ABSPATH, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . 'wp-content' . DIRECTORY_SEPARATOR . 'plugins';

        $jsIncludeString = '';

        $assets = $assetFetcher->getCatalog()->getAssets();
        foreach ($assets as $jsFile) {
            $jsFile->validateFile();

            $assetUrlPath = $jsFile->getRelativeLocation();

            $absoluteFileLocation = realpath($jsFile->getAbsoluteLocation());
            if (strpos($absoluteFileLocation, $wpPluginsDir) === 0) {
                $relativeFilePathParts = explode('/', substr($absoluteFileLocation, strlen($wpPluginsDir)));
                $relativeFilePathParts = array_values(array_filter($relativeFilePathParts));

                $pluginName = $relativeFilePathParts[0];

                $pathRelativeToPlugin = array_slice($relativeFilePathParts, 1);
                $pathRelativeToPlugin = implode('/', $pathRelativeToPlugin);

                $assetUrlPath = plugins_url($pathRelativeToPlugin, $pluginName . '/' . $pluginName . '.php');
            }

            $jsIncludeString = $jsIncludeString . sprintf(self::JS_IMPORT_DIRECTIVE, $assetUrlPath);
        }

        return $jsIncludeString;
    }
}
