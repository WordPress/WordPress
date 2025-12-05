<?php

// phpcs:disable Yoast.NamingConventions.NamespaceName.TooLong -- Given it's a very specific case.
namespace Yoast\WP\SEO\Actions\Importing\Aioseo;

use wpdb;
use Yoast\WP\SEO\Actions\Importing\Abstract_Aioseo_Importing_Action;
use Yoast\WP\SEO\Helpers\Options_Helper;

/**
 * Importing action for cleaning up AIOSEO data.
 */
class Aioseo_Cleanup_Action extends Abstract_Aioseo_Importing_Action {

	/**
	 * The plugin of the action.
	 */
	public const PLUGIN = 'aioseo';

	/**
	 * The type of the action.
	 */
	public const TYPE = 'cleanup';

	/**
	 * The AIOSEO meta_keys to be cleaned up.
	 *
	 * @var array<string>
	 */
	protected $aioseo_postmeta_keys = [
		'_aioseo_title',
		'_aioseo_description',
		'_aioseo_og_title',
		'_aioseo_og_description',
		'_aioseo_twitter_title',
		'_aioseo_twitter_description',
	];

	/**
	 * The WordPress database instance.
	 *
	 * @var wpdb
	 */
	protected $wpdb;

	/**
	 * Class constructor.
	 *
	 * @param wpdb           $wpdb    The WordPress database instance.
	 * @param Options_Helper $options The options helper.
	 */
	public function __construct( wpdb $wpdb, Options_Helper $options ) {
		$this->wpdb    = $wpdb;
		$this->options = $options;
	}

	/**
	 * Retrieves the postmeta along with the db prefix.
	 *
	 * @return string The postmeta table name along with the db prefix.
	 */
	protected function get_postmeta_table() {
		return $this->wpdb->prefix . 'postmeta';
	}

	/**
	 * Just checks if the cleanup has been completed in the past.
	 *
	 * @return int The total number of unimported objects.
	 */
	public function get_total_unindexed() {
		if ( ! $this->aioseo_helper->aioseo_exists() ) {
			return 0;
		}

		return ( ! $this->get_completed() ) ? 1 : 0;
	}

	/**
	 * Just checks if the cleanup has been completed in the past.
	 *
	 * @param int $limit The maximum number of unimported objects to be returned.
	 *
	 * @return int|false The limited number of unindexed posts. False if the query fails.
	 */
	public function get_limited_unindexed_count( $limit ) {
		if ( ! $this->aioseo_helper->aioseo_exists() ) {
			return 0;
		}

		return ( ! $this->get_completed() ) ? 1 : 0;
	}

	/**
	 * Cleans up AIOSEO data.
	 *
	 * @return Indexable[]|false An array of created indexables or false if aioseo data was not found.
	 */
	public function index() {
		if ( $this->get_completed() ) {
			return [];
		}

		// phpcs:disable WordPress.DB.PreparedSQL.NotPrepared -- Reason: There is no unescaped user input.
		$meta_data                  = $this->wpdb->query( $this->cleanup_postmeta_query() );
		$aioseo_table_truncate_done = $this->wpdb->query( $this->truncate_query() );
		// phpcs:enable WordPress.DB.PreparedSQL.NotPrepared

		if ( $meta_data === false && $aioseo_table_truncate_done === false ) {
			return false;
		}

		$this->set_completed( true );

		return [
			'metadata_cleanup'   => $meta_data,
			'indexables_cleanup' => $aioseo_table_truncate_done,
		];
	}

	/**
	 * Creates a DELETE query string for deleting AIOSEO postmeta data.
	 *
	 * @return string The query to use for importing or counting the number of items to import.
	 */
	public function cleanup_postmeta_query() {
		$table               = $this->get_postmeta_table();
		$meta_keys_to_delete = $this->aioseo_postmeta_keys;

		// phpcs:disable WordPress.DB.PreparedSQL.InterpolatedNotPrepared -- Reason: There is no unescaped user input.
		return $this->wpdb->prepare(
			"DELETE FROM {$table} WHERE meta_key IN (" . \implode( ', ', \array_fill( 0, \count( $meta_keys_to_delete ), '%s' ) ) . ')',
			$meta_keys_to_delete
		);
		// phpcs:enable WordPress.DB.PreparedSQL.InterpolatedNotPrepared
	}

	/**
	 * Creates a TRUNCATE query string for emptying the AIOSEO indexable table, if it exists.
	 *
	 * @return string The query to use for importing or counting the number of items to import.
	 */
	public function truncate_query() {
		if ( ! $this->aioseo_helper->aioseo_exists() ) {
			// If the table doesn't exist, we need a string that will amount to a quick query that doesn't return false when ran.
			return 'SELECT 1';
		}

		$table = $this->aioseo_helper->get_table();

		return "TRUNCATE TABLE {$table}";
	}

	/**
	 * Used nowhere. Exists to comply with the interface.
	 *
	 * @return int The limit.
	 */
	public function get_limit() {
		/**
		 * Filter 'wpseo_aioseo_cleanup_limit' - Allow filtering the number of posts indexed during each indexing pass.
		 *
		 * @param int $max_posts The maximum number of posts cleaned up.
		 */
		$limit = \apply_filters( 'wpseo_aioseo_cleanup_limit', 25 );

		if ( ! \is_int( $limit ) || $limit < 1 ) {
			$limit = 25;
		}

		return $limit;
	}
}
