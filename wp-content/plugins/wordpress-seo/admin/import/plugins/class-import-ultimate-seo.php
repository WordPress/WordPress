<?php
/**
 * File with the class to handle data from Ultimate SEO.
 *
 * @package WPSEO\Admin\Import\Plugins
 */

/**
 * Class with functionality to import & clean Ultimate SEO post metadata.
 */
class WPSEO_Import_Ultimate_SEO extends WPSEO_Plugin_Importer {

	/**
	 * The plugin name.
	 *
	 * @var string
	 */
	protected $plugin_name = 'Ultimate SEO';

	/**
	 * Meta key, used in SQL LIKE clause for delete query.
	 *
	 * @var string
	 */
	protected $meta_key = '_su_%';

	/**
	 * Array of meta keys to detect and import.
	 *
	 * @var array
	 */
	protected $clone_keys = [
		[
			'old_key' => '_su_description',
			'new_key' => 'metadesc',
		],
		[
			'old_key' => '_su_title',
			'new_key' => 'title',
		],
		[
			'old_key' => '_su_og_title',
			'new_key' => 'opengraph-title',
		],
		[
			'old_key' => '_su_og_description',
			'new_key' => 'opengraph-description',
		],
		[
			'old_key' => '_su_og_image',
			'new_key' => 'opengraph-image',
		],
		[
			'old_key' => '_su_meta_robots_noindex',
			'new_key' => 'meta-robots-noindex',
			'convert' => [ 'on' => 1 ],
		],
		[
			'old_key' => '_su_meta_robots_nofollow',
			'new_key' => 'meta-robots-nofollow',
			'convert' => [ 'on' => 1 ],
		],
	];
}
