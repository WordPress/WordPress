<?php

namespace Automattic\WooCommerce\Internal\ProductDownloads\ApprovedDirectories;

use Automattic\WooCommerce\Internal\ProductDownloads\ApprovedDirectories\Admin\SyncUI;
use Automattic\WooCommerce\Internal\ProductDownloads\ApprovedDirectories\Admin\UI;
use Automattic\WooCommerce\Internal\Utilities\URL;
use Automattic\WooCommerce\Internal\Utilities\URLException;

/**
 * Maintains and manages the list of approved directories, within which product downloads can
 * be stored.
 */
class Register {
	/**
	 * Used to indicate the current mode.
	 */
	private const MODES = array(
		self::MODE_DISABLED,
		self::MODE_ENABLED,
	);

	public const MODE_DISABLED  = 'disabled';
	public const MODE_ENABLED   = 'enabled';

	/**
	 * Name of the option used to store the current mode. See self::MODES for a
	 * list of acceptable values for the actual option.
	 *
	 * @var string
	 */
	private $mode_option = 'wc_downloads_approved_directories_mode';

	/**
	 * Sets up the approved directories sub-system.
	 *
	 * @internal
	 */
	final public function init() {
		add_action(
			'admin_init',
			function () {
				wc_get_container()->get( SyncUI::class )->init_hooks();
				wc_get_container()->get( UI::class )->init_hooks();
			}
		);

		add_action(
			'before_woocommerce_init',
			function() {
				if ( get_option( Synchronize::SYNC_TASK_PAGE ) > 0 ) {
					wc_get_container()->get( Synchronize::class )->init_hooks();
				}
			}
		);
	}

	/**
	 * Supplies the name of the database table used to store approved directories.
	 *
	 * @return string
	 */
	public function get_table(): string {
		global $wpdb;
		return $wpdb->prefix . 'wc_product_download_directories';
	}

	/**
	 * Returns a string indicating the current mode.
	 *
	 * May be one of: 'disabled', 'enabled', 'migrating'.
	 *
	 * @return string
	 */
	public function get_mode(): string {
		$current_mode = get_option( $this->mode_option, self::MODE_DISABLED );
		return in_array( $current_mode, self::MODES, true ) ? $current_mode : self::MODE_DISABLED;
	}

	/**
	 * Sets the mode. This effectively controls if approved directories are enforced or not.
	 *
	 * May be one of: 'disabled', 'enabled', 'migrating'.
	 *
	 * @param string $mode One of the values contained within self::MODES.
	 *
	 * @return bool
	 */
	public function set_mode( string $mode ): bool {
		if ( ! in_array( $mode, self::MODES, true ) ) {
			return false;
		}

		update_option( $this->mode_option, $mode );
		return get_option( $this->mode_option ) === $mode;
	}

	/**
	 * Adds a new URL path.
	 *
	 * On success (or if the URL was already added) returns the URL ID, or else
	 * returns boolean false.
	 *
	 * @throws URLException                 If the URL was invalid.
	 * @throws ApprovedDirectoriesException If the operation could not be performed.
	 *
	 * @param string $url     The URL of the approved directory.
	 * @param bool   $enabled If the rule is enabled.
	 *
	 * @return int
	 */
	public function add_approved_directory( string $url, bool $enabled = true ): int {
		$url      = $this->prepare_url_for_upsert( $url );
		$existing = $this->get_by_url( $url );

		if ( $existing ) {
			return $existing->get_id();
		}

		global $wpdb;
		$insert_fields = array(
			'url'     => $url,
			'enabled' => (int) $enabled,
		);

		if ( false !== $wpdb->insert( $this->get_table(), $insert_fields ) ) {
			return $wpdb->insert_id;
		}

		throw new ApprovedDirectoriesException( __( 'URL could not be added (probable database error).', 'woocommerce' ), ApprovedDirectoriesException::DB_ERROR );
	}

	/**
	 * Updates an existing approved directory.
	 *
	 * On success or if there is an existing entry for the same URL, returns true.
	 *
	 * @throws ApprovedDirectoriesException If the operation could not be performed.
	 * @throws URLException                 If the URL was invalid.
	 *
	 * @param int    $id      The ID of the approved directory to be updated.
	 * @param string $url     The new URL for the specified option.
	 * @param bool   $enabled If the rule is enabled.
	 *
	 * @return bool
	 */
	public function update_approved_directory( int $id, string $url, bool $enabled = true ): bool {
		$url           = $this->prepare_url_for_upsert( $url );
		$existing_path = $this->get_by_url( $url );

		// No need to go any further if the URL is already listed and nothing has changed.
		if ( $existing_path && $existing_path->get_url() === $url && $enabled === $existing_path->is_enabled() ) {
			return true;
		}

		global $wpdb;
		$fields = array(
			'url'     => $url,
			'enabled' => (int) $enabled,
		);

		if ( false === $wpdb->update( $this->get_table(), $fields, array( 'url_id' => $id ) ) ) {
			throw new ApprovedDirectoriesException( __( 'URL could not be updated (probable database error).', 'woocommerce' ), ApprovedDirectoriesException::DB_ERROR );
		}

		return true;
	}

	/**
	 * Indicates if the specified URL is already an approved directory.
	 *
	 * @param string $url The URL to check.
	 *
	 * @return bool
	 */
	public function approved_directory_exists( string $url ): bool {
		return (bool) $this->get_by_url( $url );
	}

	/**
	 * Returns the path identified by $id, or false if it does not exist.
	 *
	 * @param int $id The ID of the rule we are looking for.
	 *
	 * @return StoredUrl|false
	 */
	public function get_by_id( int $id ) {
		global $wpdb;

		$table = $this->get_table();

		// phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		$result = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$table} WHERE url_id = %d", array( $id ) ) );

		if ( ! $result ) {
			return false;
		}

		return new StoredUrl( $result->url_id, $result->url, $result->enabled );
	}

	/**
	 * Returns the path identified by $url, or false if it does not exist.
	 *
	 * @param string $url The URL of the rule we are looking for.
	 *
	 * @return StoredUrl|false
	 */
	public function get_by_url( string $url ) {
		global $wpdb;

		$table = $this->get_table();
		$url   = trailingslashit( $url );

		// phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		$result = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$table} WHERE url = %s", array( $url ) ) );

		if ( ! $result ) {
			return false;
		}

		return new StoredUrl( $result->url_id, $result->url, $result->enabled );
	}

	/**
	 * Indicates if the URL is within an approved directory. The approved directory must be enabled
	 * (it is possible for individual approved directories to be disabled).
	 *
	 * For instance, for 'https://storage.king/12345/ebook.pdf' to be valid then 'https://storage.king/12345'
	 * would need to be within our register.
	 *
	 * If the provided URL is a filepath it can be passed in without the 'file://' scheme.
	 *
	 * @throws URLException If the provided URL is badly formed.
	 *
	 * @param string $download_url The URL to check.
	 *
	 * @return bool
	 */
	public function is_valid_path( string $download_url ): bool {
		global $wpdb;

		$parent_directories = array();

		foreach ( ( new URL( $this->normalize_url( $download_url ) ) )->get_all_parent_urls() as $parent ) {
			$parent_directories[] = "'" . esc_sql( $parent ) . "'";
		}

		if ( empty( $parent_directories ) ) {
			return false;
		}

		$parent_directories = join( ',', $parent_directories );
		$table              = $this->get_table();

		// Look for a rule that matches the start of the download URL being tested. Since rules describe parent
		// directories, we also ensure it ends with a trailing slash.
		//
		// phpcs:disable WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		$matches = (int) $wpdb->get_var(
			"
				SELECT COUNT(*)
				FROM   {$table}
				WHERE  enabled = 1
				       AND url IN ( {$parent_directories} )
			"
		);
		// phpcs:enable

		return $matches > 0;
	}

	/**
	 * Used when a URL string is prepared before potentially adding it to the database.
	 *
	 * It will be normalized and trailing-slashed; a length check will also be performed.
	 *
	 * @throws ApprovedDirectoriesException If the operation could not be performed.
	 * @throws URLException                 If the URL was invalid.
	 *
	 * @param string $url The string URL to be normalized and trailing-slashed.
	 *
	 * @return string
	 */
	private function prepare_url_for_upsert( string $url ): string {
		$url = trailingslashit( $this->normalize_url( $url ) );

		if ( mb_strlen( $url ) > 256 ) {
			throw new ApprovedDirectoriesException( __( 'Approved directory URLs cannot be longer than 256 characters.', 'woocommerce' ), ApprovedDirectoriesException::INVALID_URL );
		}

		return $url;
	}

	/**
	 * Normalizes the provided URL, by trimming whitespace per normal PHP conventions
	 * and removing any trailing slashes. If it lacks a scheme, the file scheme is
	 * assumed and prepended.
	 *
	 * @throws URLException If the URL is badly formed.
	 *
	 * @param string $url The URL to be normalized.
	 *
	 * @return string
	 */
	private function normalize_url( string $url ): string {
		$url = untrailingslashit( trim( $url ) );
		return ( new URL( $url ) )->get_url();
	}

	/**
	 * Lists currently approved directories.
	 *
	 * Returned array will have the following structure:
	 *
	 *     [
	 *         'total_urls'  => 12345,
	 *         'total_pages' => 123,
	 *         'urls'        => [],  # StoredUrl[]
	 *     ]
	 *
	 * @param array $args {
	 *     Controls pagination and ordering.
	 *
	 *     @type null|bool $enabled  Controls if only enabled (true), disabled (false) or all rules (null) should be listed.
	 *     @type string    $order    Ordering ('ASC' for ascending, 'DESC' for descending).
	 *     @type string    $order_by Field to order by (one of 'url_id' or 'url').
	 *     @type int       $page     The page of results to retrieve.
	 *     @type int       $per_page The number of results to retrieve per page.
	 *     @type string    $search   Term to search for.
	 * }
	 *
	 * @return array
	 */
	public function list( array $args ): array {
		global $wpdb;

		$args = array_merge(
			array(
				'enabled'  => null,
				'order'    => 'ASC',
				'order_by' => 'url',
				'page'     => 1,
				'per_page' => 20,
				'search'   => '',
			),
			$args
		);

		$table    = $this->get_table();
		$paths    = array();
		$order    = in_array( $args['order'], array( 'ASC', 'DESC' ), true ) ? $args['order'] : 'ASC';
		$order_by = in_array( $args['order_by'], array( 'url_id', 'url' ), true ) ? $args['order_by'] : 'url';
		$page     = absint( $args['page'] );
		$per_page = absint( $args['per_page'] );
		$enabled  = is_bool( $args['enabled'] ) ? $args['enabled'] : null;
		$search   = '%' . $wpdb->esc_like( sanitize_text_field( $args['search'] ) ) . '%';

		if ( $page < 1 ) {
			$page = 1;
		}

		if ( $per_page < 1 ) {
			$per_page = 1;
		}

		$where     = array();
		$where_sql = '';

		if ( ! empty( $search ) ) {
			$where[] = $wpdb->prepare( 'url LIKE %s', $search );
		}

		if ( is_bool( $enabled ) ) {
			$where[] = 'enabled = ' . (int) $enabled;
		}

		if ( ! empty( $where ) ) {
			$where_sql = 'WHERE ' . join( ' AND ', $where );
		}

		// phpcs:disable WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		$results = $wpdb->get_results(
			$wpdb->prepare(
				"
					SELECT   url_id, url, enabled
					FROM     {$table}
					{$where_sql}
					ORDER BY {$order_by} {$order}
					LIMIT    %d, %d
				",
				( $page - 1 ) * $per_page,
				$per_page
			)
		);

		$total_rows = (int) $wpdb->get_var( "SELECT COUNT( * ) FROM {$table} {$where_sql}" );
		// phpcs:enable

		foreach ( $results as $single_result ) {
			$paths[] = new StoredUrl( $single_result->url_id, $single_result->url, $single_result->enabled );
		}

		return array(
			'total_urls'           => $total_rows,
			'total_pages'          => (int) ceil( $total_rows / $per_page ),
			'approved_directories' => $paths,
		);
	}

	/**
	 * Delete the approved directory identitied by the supplied ID.
	 *
	 * @param int $id The ID of the rule to be deleted.
	 *
	 * @return bool
	 */
	public function delete_by_id( int $id ): bool {
		global $wpdb;
		$table = $this->get_table();

		return (bool) $wpdb->delete( $table, array( 'url_id' => $id ) );
	}

	/**
	 * Delete the entirev approved directory list.
	 *
	 * @return bool
	 */
	public function delete_all(): bool {
		global $wpdb;
		$table = $this->get_table();
		// phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		return (bool) $wpdb->query( "DELETE FROM $table" );
	}

	/**
	 * Enable the approved directory identitied by the supplied ID.
	 *
	 * @param int $id The ID of the rule to be deleted.
	 *
	 * @return bool
	 */
	public function enable_by_id( int $id ): bool {
		global $wpdb;
		$table = $this->get_table();
		return (bool) $wpdb->update( $table, array( 'enabled' => 1 ), array( 'url_id' => $id ) );
	}

	/**
	 * Disable the approved directory identitied by the supplied ID.
	 *
	 * @param int $id The ID of the rule to be deleted.
	 *
	 * @return bool
	 */
	public function disable_by_id( int $id ): bool {
		global $wpdb;
		$table = $this->get_table();
		return (bool) $wpdb->update( $table, array( 'enabled' => 0 ), array( 'url_id' => $id ) );
	}

	/**
	 * Enables all Approved Download Directory rules in a single operation.
	 *
	 * @return bool
	 */
	public function enable_all(): bool {
		global $wpdb;
		$table = $this->get_table();
		// phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		return (bool) $wpdb->query( "UPDATE {$table} SET enabled = 1" );
	}

	/**
	 * Disables all Approved Download Directory rules in a single operation.
	 *
	 * @return bool
	 */
	public function disable_all(): bool {
		global $wpdb;
		$table = $this->get_table();
		// phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		return (bool) $wpdb->query( "UPDATE {$table} SET enabled = 0" );
	}

	/**
	 * Indicates the number of approved directories that are enabled (or disabled, if optional
	 * param $enabled is set to false).
	 *
	 * @param bool $enabled Controls whether enabled or disabled directory rules are counted.
	 *
	 * @return int
	 */
	public function count( bool $enabled = true ): int {
		global $wpdb;
		$table = $this->get_table();

		return (int) $wpdb->get_var(
			$wpdb->prepare(
				// phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
				"SELECT COUNT(*) FROM {$table} WHERE enabled = %d",
				$enabled ? 1 : 0
			)
		);
	}
}
