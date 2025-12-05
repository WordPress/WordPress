<?php
/**
 * WPSEO plugin file.
 *
 * @package WPSEO\Admin\Import\Plugins
 */

/**
 * Class WPSEO_Plugin_Importers.
 *
 * Object which contains all importers.
 */
class WPSEO_Plugin_Importers {

	/**
	 * List of supported importers.
	 *
	 * @var array
	 */
	private static $importers = [
		'WPSEO_Import_AIOSEO',
		'WPSEO_Import_AIOSEO_V4',
		'WPSEO_Import_Greg_SEO',
		'WPSEO_Import_HeadSpace',
		'WPSEO_Import_Jetpack_SEO',
		'WPSEO_Import_WP_Meta_SEO',
		'WPSEO_Import_Platinum_SEO',
		'WPSEO_Import_Premium_SEO_Pack',
		'WPSEO_Import_RankMath',
		'WPSEO_Import_SEOPressor',
		'WPSEO_Import_SEO_Framework',
		'WPSEO_Import_Smartcrawl_SEO',
		'WPSEO_Import_Squirrly',
		'WPSEO_Import_Ultimate_SEO',
		'WPSEO_Import_WooThemes_SEO',
		'WPSEO_Import_WPSEO',
	];

	/**
	 * Returns an array of importers available.
	 *
	 * @return array Available importers.
	 */
	public static function get() {
		return self::$importers;
	}
}
