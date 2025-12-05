<?php
/**
 * File with the class to handle data from Premium SEO Pack.
 *
 * @package WPSEO\Admin\Import\Plugins
 */

/**
 * Class with functionality to import & clean Premium SEO Pack post metadata.
 */
class WPSEO_Import_Premium_SEO_Pack extends WPSEO_Import_Squirrly {

	/**
	 * The plugin name.
	 *
	 * @var string
	 */
	protected $plugin_name = 'Premium SEO Pack';

	/**
	 * WPSEO_Import_Premium_SEO_Pack constructor.
	 */
	public function __construct() {
		parent::__construct();

		global $wpdb;
		$this->table_name = $wpdb->prefix . 'psp';
		$this->meta_key   = '';
	}

	/**
	 * Returns the query to return an identifier for the posts to import.
	 *
	 * @return string
	 */
	protected function retrieve_posts_query() {
		return "SELECT URL AS identifier FROM {$this->table_name} WHERE blog_id = %d";
	}
}
