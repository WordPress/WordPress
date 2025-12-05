<?php
/**
 * File with the class to handle data from WP Meta SEO.
 *
 * @package WPSEO\Admin\Import\Plugins
 */

/**
 * Class with functionality to import & clean WP Meta SEO post metadata.
 */
class WPSEO_Import_WP_Meta_SEO extends WPSEO_Plugin_Importer {

	/**
	 * The plugin name.
	 *
	 * @var string
	 */
	protected $plugin_name = 'WP Meta SEO';

	/**
	 * Meta key, used in SQL LIKE clause for delete query.
	 *
	 * @var string
	 */
	protected $meta_key = '_metaseo_%';

	/**
	 * Array of meta keys to detect and import.
	 *
	 * @var array
	 */
	protected $clone_keys = [
		[
			'old_key' => '_metaseo_metadesc',
			'new_key' => 'metadesc',
		],
		[
			'old_key' => '_metaseo_metatitle',
			'new_key' => 'title',
		],
		[
			'old_key' => '_metaseo_metaopengraph-title',
			'new_key' => 'opengraph-title',
		],
		[
			'old_key' => '_metaseo_metaopengraph-desc',
			'new_key' => 'opengraph-description',
		],
		[
			'old_key' => '_metaseo_metaopengraph-image',
			'new_key' => 'opengraph-image',
		],
		[
			'old_key' => '_metaseo_metatwitter-title',
			'new_key' => 'twitter-title',
		],
		[
			'old_key' => '_metaseo_metatwitter-desc',
			'new_key' => 'twitter-description',
		],
		[
			'old_key' => '_metaseo_metatwitter-image',
			'new_key' => 'twitter-image',
		],
		[
			'old_key' => '_metaseo_metaindex',
			'new_key' => 'meta-robots-noindex',
			'convert' => [
				'index'   => 0,
				'noindex' => 1,
			],
		],
		[
			'old_key' => '_metaseo_metafollow',
			'new_key' => 'meta-robots-nofollow',
			'convert' => [
				'follow'   => 0,
				'nofollow' => 1,
			],
		],
	];
}
