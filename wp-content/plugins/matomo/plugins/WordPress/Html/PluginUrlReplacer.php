<?php
/**
 * Matomo - free/libre analytics platform
 *
 * @link https://matomo.org
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 *
 */

namespace Piwik\Plugins\WordPress\Html;

use Piwik\Common;

class PluginUrlReplacer
{
    public function replaceThirdPartyPluginUrls($html): string
    {
        // replace all links to third party Matomo plugin files with their proper WordPress URLs
        $html = preg_replace_callback(
            '%\"((?:\\./)?plugins/.*?)\"%',
            function ($matches) {
                // $url looks like plugins/SearchEngineKeywordsPerformance/images/...
                $url = $matches[1];
                $replace = $this->rewritePathIfThirdPartyPluginUrl($url);
                if (!empty($replace)) {
                    return '"' . $replace . '"';
                }

                return $matches[0];
            },
            $html
        );

        // replace URLs to third party Matomo plugin files in JSON values (used to initiate Vue components)
        $html = preg_replace_callback(
            '%&quot;(?:\\.\\\\/)?plugins\\\\/.*?&quot;%',
            function ($matches) {
                // $url looks like plugins/SearchEngineKeywordsPerformance/images/...
                $url = Common::unsanitizeInputValue( $matches[0] );
                $url = json_decode($url, true);
                if (empty($url) || !is_string($url)) { // sanity check
                    return $matches[0];
                }

                $replace = $this->rewritePathIfThirdPartyPluginUrl($url);
                if (!empty($replace)) {
                    $replace = json_encode($replace);
                    $replace = Common::sanitizeInputValue($replace);
                    return $replace;
                }

                return $matches[0];
            },
            $html
        );

        return $html;
    }

    private function rewritePathIfThirdPartyPluginUrl(string $url): ?string
    {
        if (substr($url, 0, 2) === './') {
            $url = substr($url, 2);
        }

        $segments = explode('/', $url);
        $plugin = $segments[1] ?? '';

        // entries in this array will look like:
        // /path/to/wordpress/wp-content/plugins/SearchEngineKeywordsPerformance/SearchEngineKeywordsPerformance.php
        $allPluginsInstalledInWp = $GLOBALS['MATOMO_PLUGIN_FILES'] ?? [];
        foreach ($allPluginsInstalledInWp as $matomoPluginFile) {
            if (basename($matomoPluginFile) === $plugin . '.php') {
                array_shift($segments);
                array_shift($segments);
                $urlRelativeToPlugin = implode('/', $segments);

                $replace = plugins_url($urlRelativeToPlugin, $matomoPluginFile);
                return $replace;
            }
        }

        return null;
    }
}
