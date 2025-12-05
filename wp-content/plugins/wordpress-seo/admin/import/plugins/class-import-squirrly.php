<?php
/**
 * File with the class to handle data from Squirrly.
 *
 * @package WPSEO\Admin\Import\Plugins
 */

/**
 * Class with functionality to import & clean Squirrly post metadata.
 */
class WPSEO_Import_Squirrly extends WPSEO_Plugin_Importer {

	/**
	 * The plugin name.
	 *
	 * @var string
	 */
	protected $plugin_name = 'Squirrly SEO';

	/**
	 * Holds the name of the table Squirrly uses to store data.
	 *
	 * @var string
	 */
	protected $table_name;

	/**
	 * Meta key, used in SQL LIKE clause for delete query.
	 *
	 * @var string
	 */
	protected $meta_key = '_sq_post_keyword';

	/**
	 * Data to import from (and the target to field) the serialized array stored in the SEO field in the Squirrly table.
	 *
	 * @var array
	 */
	protected $seo_field_keys = [
		'noindex'        => 'meta-robots-noindex',
		'nofollow'       => 'meta-robots-nofollow',
		'title'          => 'title',
		'description'    => 'metadesc',
		'canonical'      => 'canonical',
		'cornerstone'    => '_yst_is_cornerstone',
		'tw_media'       => 'twitter-image',
		'tw_title'       => 'twitter-title',
		'tw_description' => 'twitter-description',
		'og_title'       => 'opengraph-title',
		'og_description' => 'opengraph-description',
		'og_media'       => 'opengraph-image',
		'focuskw'        => 'focuskw',
	];

	/**
	 * WPSEO_Import_Squirrly constructor.
	 */
	public function __construct() {
		parent::__construct();

		global $wpdb;
		$this->table_name = $wpdb->prefix . 'qss';
	}

	/**
	 * Imports the post meta values to Yoast SEO.
	 *
	 * @return bool Import success status.
	 */
	protected function import() {
		$results = $this->retrieve_posts();
		foreach ( $results as $post ) {
			$return = $this->import_post_values( $post->identifier );
			if ( ! $return ) {
				return false;
			}
		}

		return true;
	}

	/**
	 * Retrieve the posts from the Squirrly Database.
	 *
	 * @return array Array of post IDs from the DB.
	 */
	protected function retrieve_posts() {
		global $wpdb;
		return $wpdb->get_results(
			$wpdb->prepare(
				$this->retrieve_posts_query(),
				get_current_blog_id()
			)
		);
	}

	/**
	 * Returns the query to return an identifier for the posts to import.
	 *
	 * @return string Query to get post ID's from the DB.
	 */
	protected function retrieve_posts_query() {
		return "SELECT post_id AS identifier FROM {$this->table_name} WHERE blog_id = %d";
	}

	/**
	 * Removes the DB table and the post meta field Squirrly creates.
	 *
	 * @return bool Cleanup status.
	 */
	protected function cleanup() {
		global $wpdb;

		// If we can clean, let's clean.
		$wpdb->query( "DROP TABLE {$this->table_name}" );

		// This removes the post meta field for the focus keyword from the DB.
		parent::cleanup();

		// If we can still see the table, something went wrong.
		if ( $this->detect() ) {
			$this->cleanup_error_msg();
			return false;
		}

		return true;
	}

	/**
	 * Detects whether there is post meta data to import.
	 *
	 * @return bool Boolean indicating whether there is something to import.
	 */
	protected function detect() {
		global $wpdb;

		$result = $wpdb->get_var( "SHOW TABLES LIKE '{$this->table_name}'" );
		if ( is_wp_error( $result ) || $result === null ) {
			return false;
		}

		return true;
	}

	/**
	 * Imports the data of a post out of Squirrly's DB table.
	 *
	 * @param mixed $post_identifier Post identifier, can be ID or string.
	 *
	 * @return bool Import status.
	 */
	private function import_post_values( $post_identifier ) {
		$data = $this->retrieve_post_data( $post_identifier );
		if ( ! $data ) {
			return false;
		}

		if ( ! is_numeric( $post_identifier ) ) {
			$post_id = url_to_postid( $post_identifier );
		}

		if ( is_numeric( $post_identifier ) ) {
			$post_id         = (int) $post_identifier;
			$data['focuskw'] = $this->maybe_add_focus_kw( $post_identifier );
		}

		foreach ( $this->seo_field_keys as $squirrly_key => $yoast_key ) {
			$this->import_meta_helper( $squirrly_key, $yoast_key, $data, $post_id );
		}
		return true;
	}

	/**
	 * Retrieves the Squirrly SEO data for a post from the DB.
	 *
	 * @param int $post_identifier Post ID.
	 *
	 * @return array|bool Array of data or false.
	 */
	private function retrieve_post_data( $post_identifier ) {
		global $wpdb;

		if ( is_numeric( $post_identifier ) ) {
			$post_identifier = (int) $post_identifier;
			$query_where     = 'post_id = %d';
		}
		if ( ! is_numeric( $post_identifier ) ) {
			$query_where = 'URL = %s';
		}

		$replacements = [
			get_current_blog_id(),
			$post_identifier,
		];

		$data = $wpdb->get_var(
			$wpdb->prepare(
				"SELECT seo FROM {$this->table_name} WHERE blog_id = %d AND " . $query_where,
				$replacements
			)
		);
		if ( ! $data || is_wp_error( $data ) ) {
			return false;
		}
		$data = maybe_unserialize( $data );
		return $data;
	}

	/**
	 * Squirrly stores the focus keyword in post meta.
	 *
	 * @param int $post_id Post ID.
	 *
	 * @return string The focus keyword.
	 */
	private function maybe_add_focus_kw( $post_id ) {
		$focuskw = get_post_meta( $post_id, '_sq_post_keyword', true );
		if ( $focuskw ) {
			$focuskw = json_decode( $focuskw );
			return $focuskw->keyword;
		}
		return '';
	}
}
