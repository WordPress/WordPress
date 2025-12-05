<?php
/**
 * File with the class to handle data from SEO Framework.
 *
 * @package WPSEO\Admin\Import\Plugins
 */

/**
 * Class with functionality to import & clean SEO Framework post metadata.
 */
class WPSEO_Import_SEO_Framework extends WPSEO_Plugin_Importer {

	/**
	 * The plugin name.
	 *
	 * @var string
	 */
	protected $plugin_name = 'The SEO Framework';

	/**
	 * Meta key, used in SQL LIKE clause for delete query.
	 *
	 * @var string
	 */
	protected $meta_key = '_genesis_%';

	/**
	 * Array of meta keys to detect and import.
	 *
	 * @var array
	 */
	protected $clone_keys = [
		[
			'old_key' => '_genesis_description',
			'new_key' => 'metadesc',
		],
		[
			'old_key' => '_genesis_title',
			'new_key' => 'title',
		],
		[
			'old_key' => '_genesis_noindex',
			'new_key' => 'meta-robots-noindex',
		],
		[
			'old_key' => '_genesis_nofollow',
			'new_key' => 'meta-robots-nofollow',
		],
		[
			'old_key' => '_genesis_canonical_uri',
			'new_key' => 'canonical',
		],
		[
			'old_key' => '_open_graph_title',
			'new_key' => 'opengraph-title',
		],
		[
			'old_key' => '_open_graph_description',
			'new_key' => 'opengraph-description',
		],
		[
			'old_key' => '_social_image_url',
			'new_key' => 'opengraph-image',
		],
		[
			'old_key' => '_twitter_title',
			'new_key' => 'twitter-title',
		],
		[
			'old_key' => '_twitter_description',
			'new_key' => 'twitter-description',
		],
	];

	/**
	 * Removes all the metadata set by the SEO Framework plugin.
	 *
	 * @return bool
	 */
	protected function cleanup() {
		$set1 = parent::cleanup();

		$this->meta_key = '_social_image_%';
		$set2           = parent::cleanup();

		$this->meta_key = '_twitter_%';
		$set3           = parent::cleanup();

		$this->meta_key = '_open_graph_%';
		$set4           = parent::cleanup();

		return ( $set1 || $set2 || $set3 || $set4 );
	}
}
