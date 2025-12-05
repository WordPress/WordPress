<?php
/**
 * File with the class to handle data from HeadSpace.
 *
 * @package WPSEO\Admin\Import\Plugins
 */

/**
 * Class WPSEO_Import_HeadSpace.
 *
 * Class with functionality to import & clean HeadSpace SEO post metadata.
 */
class WPSEO_Import_HeadSpace extends WPSEO_Plugin_Importer {

	/**
	 * The plugin name.
	 *
	 * @var string
	 */
	protected $plugin_name = 'HeadSpace SEO';

	/**
	 * Meta key, used in SQL LIKE clause for delete query.
	 *
	 * @var string
	 */
	protected $meta_key = '_headspace_%';

	/**
	 * Array of meta keys to detect and import.
	 *
	 * @var array
	 */
	protected $clone_keys = [
		[
			'old_key' => '_headspace_description',
			'new_key' => 'metadesc',
		],
		[
			'old_key' => '_headspace_page_title',
			'new_key' => 'title',
		],
		[
			'old_key' => '_headspace_noindex',
			'new_key' => 'meta-robots-noindex',
			'convert' => [ 'on' => 1 ],
		],
		[
			'old_key' => '_headspace_nofollow',
			'new_key' => 'meta-robots-nofollow',
			'convert' => [ 'on' => 1 ],
		],
	];
}
