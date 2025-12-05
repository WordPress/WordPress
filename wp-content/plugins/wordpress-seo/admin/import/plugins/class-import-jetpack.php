<?php
/**
 * File with the class to handle data from Jetpack's Advanced SEO settings.
 *
 * @package WPSEO\Admin\Import\Plugins
 */

/**
 * Class WPSEO_Import_Jetpack_SEO.
 *
 * Class with functionality to import & clean Jetpack SEO post metadata.
 */
class WPSEO_Import_Jetpack_SEO extends WPSEO_Plugin_Importer {

	/**
	 * The plugin name.
	 *
	 * @var string
	 */
	protected $plugin_name = 'Jetpack';

	/**
	 * Meta key, used in SQL LIKE clause for delete query.
	 *
	 * @var string
	 */
	protected $meta_key = 'advanced_seo_description';

	/**
	 * Array of meta keys to detect and import.
	 *
	 * @var array
	 */
	protected $clone_keys = [
		[
			'old_key' => 'advanced_seo_description',
			'new_key' => 'metadesc',
		],
	];
}
