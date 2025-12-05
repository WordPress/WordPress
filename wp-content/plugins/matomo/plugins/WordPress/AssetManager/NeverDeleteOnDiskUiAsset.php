<?php
/**
 * Matomo - free/libre analytics platform
 *
 * @link https://matomo.org
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 *
 */

namespace Piwik\Plugins\WordPress\AssetManager;

use Piwik\AssetManager\UIAsset\OnDiskUIAsset;

if (!defined( 'ABSPATH')) {
    exit; // if accessed directly
}

class NeverDeleteOnDiskUiAsset extends OnDiskUIAsset
{
	public function delete() {
		// prevent the core asset from being removed
	}
}
