<?php
/**
 * This file holds the abstract class for dealing with imports from other plugins.
 *
 * @package WPSEO\Admin\Import\Plugins
 */

/**
 * Class WPSEO_Plugin_Importer.
 *
 * Class with functionality to import meta data from other plugins.
 */
abstract class WPSEO_Plugin_Importer {

	/**
	 * Holds the import status object.
	 *
	 * @var WPSEO_Import_Status
	 */
	protected $status;

	/**
	 * The plugin name.
	 *
	 * @var string
	 */
	protected $plugin_name;

	/**
	 * Meta key, used in SQL LIKE clause for delete query.
	 *
	 * @var string
	 */
	protected $meta_key;

	/**
	 * Array of meta keys to detect and import.
	 *
	 * @var array
	 */
	protected $clone_keys;

	/**
	 * Class constructor.
	 */
	public function __construct() {}

	/**
	 * Returns the string for the plugin we're importing from.
	 *
	 * @return string Plugin name.
	 */
	public function get_plugin_name() {
		return $this->plugin_name;
	}

	/**
	 * Imports the settings and post meta data from another SEO plugin.
	 *
	 * @return WPSEO_Import_Status Import status object.
	 */
	public function run_import() {
		$this->status = new WPSEO_Import_Status( 'import', false );

		if ( ! $this->detect() ) {
			return $this->status;
		}

		$this->status->set_status( $this->import() );

		// Flush the entire cache, as we no longer know what's valid and what's not.
		wp_cache_flush();

		return $this->status;
	}

	/**
	 * Handles post meta data to import.
	 *
	 * @return bool Import success status.
	 */
	protected function import() {
		return $this->meta_keys_clone( $this->clone_keys );
	}

	/**
	 * Removes the plugin data from the database.
	 *
	 * @return WPSEO_Import_Status Import status object.
	 */
	public function run_cleanup() {
		$this->status = new WPSEO_Import_Status( 'cleanup', false );

		if ( ! $this->detect() ) {
			return $this->status;
		}

		return $this->status->set_status( $this->cleanup() );
	}

	/**
	 * Removes the plugin data from the database.
	 *
	 * @return bool Cleanup status.
	 */
	protected function cleanup() {
		global $wpdb;
		if ( empty( $this->meta_key ) ) {
			return true;
		}
		$wpdb->query(
			$wpdb->prepare(
				"DELETE FROM {$wpdb->postmeta} WHERE meta_key LIKE %s",
				$this->meta_key
			)
		);
		$result = $wpdb->__get( 'result' );
		if ( ! $result ) {
			$this->cleanup_error_msg();
		}

		return $result;
	}

	/**
	 * Sets the status message for when a cleanup has gone bad.
	 *
	 * @return void
	 */
	protected function cleanup_error_msg() {
		/* translators: %s is replaced with the plugin's name. */
		$this->status->set_msg( sprintf( __( 'Cleanup of %s data failed.', 'wordpress-seo' ), $this->plugin_name ) );
	}

	/**
	 * Detects whether an import for this plugin is needed.
	 *
	 * @return WPSEO_Import_Status Import status object.
	 */
	public function run_detect() {
		$this->status = new WPSEO_Import_Status( 'detect', false );

		if ( ! $this->detect() ) {
			return $this->status;
		}

		return $this->status->set_status( true );
	}

	/**
	 * Detects whether there is post meta data to import.
	 *
	 * @return bool Boolean indicating whether there is something to import.
	 */
	protected function detect() {
		global $wpdb;

		$meta_keys = wp_list_pluck( $this->clone_keys, 'old_key' );
		$result    = $wpdb->get_var(
			$wpdb->prepare(
				"SELECT COUNT(*) AS `count`
					FROM {$wpdb->postmeta}
					WHERE meta_key IN ( " . implode( ', ', array_fill( 0, count( $meta_keys ), '%s' ) ) . ' )',
				$meta_keys
			)
		);

		if ( $result === '0' ) {
			return false;
		}

		return true;
	}

	/**
	 * Helper function to clone meta keys and (optionally) change their values in bulk.
	 *
	 * @param string $old_key        The existing meta key.
	 * @param string $new_key        The new meta key.
	 * @param array  $replace_values An array, keys old value, values new values.
	 *
	 * @return bool Clone status.
	 */
	protected function meta_key_clone( $old_key, $new_key, $replace_values = [] ) {
		global $wpdb;

		// First we create a temp table with all the values for meta_key.
		$result = $wpdb->query(
			$wpdb->prepare(
				// phpcs:ignore WordPress.DB.DirectDatabaseQuery.SchemaChange -- This is intentional + temporary.
				"CREATE TEMPORARY TABLE tmp_meta_table SELECT * FROM {$wpdb->postmeta} WHERE meta_key = %s",
				$old_key
			)
		);
		if ( $result === false ) {
			$this->set_missing_db_rights_status();
			return false;
		}

		// Delete all the values in our temp table for posts that already have data for $new_key.
		$wpdb->query(
			$wpdb->prepare(
				"DELETE FROM tmp_meta_table WHERE post_id IN ( SELECT post_id FROM {$wpdb->postmeta} WHERE meta_key = %s )",
				WPSEO_Meta::$meta_prefix . $new_key
			)
		);

		/*
		 * We set meta_id to NULL so on re-insert into the postmeta table, MYSQL can set
		 * new meta_id's and we don't get duplicates.
		 */
		$wpdb->query( 'UPDATE tmp_meta_table SET meta_id = NULL' );

		// Now we rename the meta_key.
		$wpdb->query(
			$wpdb->prepare(
				'UPDATE tmp_meta_table SET meta_key = %s',
				WPSEO_Meta::$meta_prefix . $new_key
			)
		);

		$this->meta_key_clone_replace( $replace_values );

		// With everything done, we insert all our newly cloned lines into the postmeta table.
		$wpdb->query( "INSERT INTO {$wpdb->postmeta} SELECT * FROM tmp_meta_table" );

		// Now we drop our temporary table.
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.SchemaChange -- This is intentional + a temporary table.
		$wpdb->query( 'DROP TEMPORARY TABLE IF EXISTS tmp_meta_table' );

		return true;
	}

	/**
	 * Clones multiple meta keys.
	 *
	 * @param array $clone_keys The keys to clone.
	 *
	 * @return bool Success status.
	 */
	protected function meta_keys_clone( $clone_keys ) {
		foreach ( $clone_keys as $clone_key ) {
			$result = $this->meta_key_clone( $clone_key['old_key'], $clone_key['new_key'], ( $clone_key['convert'] ?? [] ) );
			if ( ! $result ) {
				return false;
			}
		}
		return true;
	}

	/**
	 * Sets the import status to false and returns a message about why it failed.
	 *
	 * @return void
	 */
	protected function set_missing_db_rights_status() {
		$this->status->set_status( false );
		/* translators: %s is replaced with Yoast SEO. */
		$this->status->set_msg( sprintf( __( 'The %s importer functionality uses temporary database tables. It seems your WordPress install does not have the capability to do this, please consult your hosting provider.', 'wordpress-seo' ), 'Yoast SEO' ) );
	}

	/**
	 * Helper function to search for a key in an array and maybe save it as a meta field.
	 *
	 * @param string $plugin_key The key in the $data array to check.
	 * @param string $yoast_key  The identifier we use in our meta settings.
	 * @param array  $data       The array of data for this post to sift through.
	 * @param int    $post_id    The post ID.
	 *
	 * @return void
	 */
	protected function import_meta_helper( $plugin_key, $yoast_key, $data, $post_id ) {
		if ( ! empty( $data[ $plugin_key ] ) ) {
			$this->maybe_save_post_meta( $yoast_key, $data[ $plugin_key ], $post_id );
		}
	}

	/**
	 * Saves a post meta value if it doesn't already exist.
	 *
	 * @param string $new_key The key to save.
	 * @param mixed  $value   The value to set the key to.
	 * @param int    $post_id The Post to save the meta for.
	 *
	 * @return void
	 */
	protected function maybe_save_post_meta( $new_key, $value, $post_id ) {
		// Big. Fat. Sigh. Mostly used for _yst_is_cornerstone, but might be useful for other hidden meta's.
		$key        = WPSEO_Meta::$meta_prefix . $new_key;
		$wpseo_meta = true;
		if ( substr( $new_key, 0, 1 ) === '_' ) {
			$key        = $new_key;
			$wpseo_meta = false;
		}

		$existing_value = get_post_meta( $post_id, $key, true );
		if ( empty( $existing_value ) ) {
			if ( $wpseo_meta ) {
				WPSEO_Meta::set_value( $new_key, $value, $post_id );
				return;
			}
			update_post_meta( $post_id, $new_key, $value );
		}
	}

	/**
	 * Replaces values in our temporary table according to our settings.
	 *
	 * @param array $replace_values Key value pair of values to replace with other values.
	 *
	 * @return void
	 */
	protected function meta_key_clone_replace( $replace_values ) {
		global $wpdb;

		// Now we replace values if needed.
		if ( is_array( $replace_values ) && $replace_values !== [] ) {
			foreach ( $replace_values as $old_value => $new_value ) {
				$wpdb->query(
					$wpdb->prepare(
						'UPDATE tmp_meta_table SET meta_value = %s WHERE meta_value = %s',
						$new_value,
						$old_value
					)
				);
			}
		}
	}
}
