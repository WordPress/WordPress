<?php
/**
 * DataSynchronizer class file.
 */

namespace Automattic\WooCommerce\Internal\DataStores\Orders;

use Automattic\WooCommerce\Caches\OrderCache;
use Automattic\WooCommerce\Caches\OrderCacheController;
use Automattic\WooCommerce\Database\Migrations\CustomOrderTable\PostsToOrdersMigrationController;
use Automattic\WooCommerce\Internal\BatchProcessing\BatchProcessorInterface;
use Automattic\WooCommerce\Internal\Features\FeaturesController;
use Automattic\WooCommerce\Internal\Traits\AccessiblePrivateMethods;
use Automattic\WooCommerce\Internal\Utilities\DatabaseUtil;
use Automattic\WooCommerce\Proxies\LegacyProxy;

defined( 'ABSPATH' ) || exit;

/**
 * This class handles the database structure creation and the data synchronization for the custom orders tables. Its responsibilites are:
 *
 * - Providing entry points for creating and deleting the required database tables.
 * - Synchronizing changes between the custom orders tables and the posts table whenever changes in orders happen.
 */
class DataSynchronizer implements BatchProcessorInterface {

	use AccessiblePrivateMethods;

	public const ORDERS_DATA_SYNC_ENABLED_OPTION           = 'woocommerce_custom_orders_table_data_sync_enabled';
	private const INITIAL_ORDERS_PENDING_SYNC_COUNT_OPTION = 'woocommerce_initial_orders_pending_sync_count';
	public const PENDING_SYNCHRONIZATION_FINISHED_ACTION   = 'woocommerce_orders_sync_finished';
	public const PLACEHOLDER_ORDER_POST_TYPE               = 'shop_order_placehold';

	private const ORDERS_SYNC_BATCH_SIZE = 250;
	// Allowed values for $type in get_ids_of_orders_pending_sync method.
	public const ID_TYPE_MISSING_IN_ORDERS_TABLE = 0;
	public const ID_TYPE_MISSING_IN_POSTS_TABLE  = 1;
	public const ID_TYPE_DIFFERENT_UPDATE_DATE   = 2;

	/**
	 * The data store object to use.
	 *
	 * @var OrdersTableDataStore
	 */
	private $data_store;

	/**
	 * The database util object to use.
	 *
	 * @var DatabaseUtil
	 */
	private $database_util;

	/**
	 * The posts to COT migrator to use.
	 *
	 * @var PostsToOrdersMigrationController
	 */
	private $posts_to_cot_migrator;

	/**
	 * Logger object to be used to log events.
	 *
	 * @var \WC_Logger
	 */
	private $error_logger;

	/**
	 * The order cache controller.
	 *
	 * @var OrderCacheController
	 */
	private $order_cache_controller;

	/**
	 * Class constructor.
	 */
	public function __construct() {
		self::add_action( 'deleted_post', array( $this, 'handle_deleted_post' ), 10, 2 );
		self::add_action( 'woocommerce_new_order', array( $this, 'handle_updated_order' ), 100 );
		self::add_action( 'woocommerce_update_order', array( $this, 'handle_updated_order' ), 100 );
		self::add_filter( 'woocommerce_feature_description_tip', array( $this, 'handle_feature_description_tip' ), 10, 3 );
	}

	/**
	 * Class initialization, invoked by the DI container.
	 *
	 * @param OrdersTableDataStore             $data_store The data store to use.
	 * @param DatabaseUtil                     $database_util The database util class to use.
	 * @param PostsToOrdersMigrationController $posts_to_cot_migrator The posts to COT migration class to use.
	 * @param LegacyProxy                      $legacy_proxy The legacy proxy instance to use.
	 * @param OrderCacheController             $order_cache_controller The order cache controller instance to use.
	 * @internal
	 */
	final public function init(
		OrdersTableDataStore $data_store,
		DatabaseUtil $database_util,
		PostsToOrdersMigrationController $posts_to_cot_migrator,
		LegacyProxy $legacy_proxy,
		OrderCacheController $order_cache_controller
	) {
		$this->data_store             = $data_store;
		$this->database_util          = $database_util;
		$this->posts_to_cot_migrator  = $posts_to_cot_migrator;
		$this->error_logger           = $legacy_proxy->call_function( 'wc_get_logger' );
		$this->order_cache_controller = $order_cache_controller;
	}

	/**
	 * Does the custom orders tables exist in the database?
	 *
	 * @return bool True if the custom orders tables exist in the database.
	 */
	public function check_orders_table_exists(): bool {
		$missing_tables = $this->database_util->get_missing_tables( $this->data_store->get_database_schema() );

		return count( $missing_tables ) === 0;
	}

	/**
	 * Create the custom orders database tables.
	 */
	public function create_database_tables() {
		$this->database_util->dbdelta( $this->data_store->get_database_schema() );
	}

	/**
	 * Delete the custom orders database tables.
	 */
	public function delete_database_tables() {
		$table_names = $this->data_store->get_all_table_names();

		foreach ( $table_names as $table_name ) {
			$this->database_util->drop_database_table( $table_name );
		}
	}

	/**
	 * Is the data sync between old and new tables currently enabled?
	 *
	 * @return bool
	 */
	public function data_sync_is_enabled(): bool {
		return 'yes' === get_option( self::ORDERS_DATA_SYNC_ENABLED_OPTION );
	}

	/**
	 * Get the current sync process status.
	 * The information is meaningful only if pending_data_sync_is_in_progress return true.
	 *
	 * @return array
	 */
	public function get_sync_status() {
		return array(
			'initial_pending_count' => (int) get_option( self::INITIAL_ORDERS_PENDING_SYNC_COUNT_OPTION, 0 ),
			'current_pending_count' => $this->get_total_pending_count(),
		);
	}

	/**
	 * Get the total number of orders pending synchronization.
	 *
	 * @return int
	 */
	public function get_current_orders_pending_sync_count_cached() : int {
		return $this->get_current_orders_pending_sync_count( true );
	}

	/**
	 * Calculate how many orders need to be synchronized currently.
	 * A database query is performed to get how many orders match one of the following:
	 *
	 * - Existing in the authoritative table but not in the backup table.
	 * - Existing in both tables, but they have a different update date.
	 *
	 * @param bool $use_cache Whether to use the cached value instead of fetching from database.
	 */
	public function get_current_orders_pending_sync_count( $use_cache = false ): int {
		global $wpdb;

		if ( $use_cache ) {
			$pending_count = wp_cache_get( 'woocommerce_hpos_pending_sync_count' );
			if ( false !== $pending_count ) {
				return (int) $pending_count;
			}
		}
		$orders_table     = $this->data_store::get_orders_table_name();
		$order_post_types = wc_get_order_types( 'cot-migration' );

		if ( empty( $order_post_types ) ) {
			$this->error_logger->debug(
				sprintf(
					/* translators: 1: method name. */
					esc_html__( '%1$s was called but no order types were registered: it may have been called too early.', 'woocommerce' ),
					__METHOD__
				)
			);

			return 0;
		}

		$order_post_type_placeholder = implode( ', ', array_fill( 0, count( $order_post_types ), '%s' ) );

		if ( $this->custom_orders_table_is_authoritative() ) {
			$missing_orders_count_sql = "
SELECT COUNT(1) FROM $wpdb->posts posts
INNER JOIN $orders_table orders ON posts.id=orders.id
WHERE posts.post_type = '" . self::PLACEHOLDER_ORDER_POST_TYPE . "'
 AND orders.status not in ( 'auto-draft' )
";
			$operator                 = '>';
		} else {
			// phpcs:disable WordPress.DB.PreparedSQL.InterpolatedNotPrepared, WordPress.DB.PreparedSQLPlaceholders.UnfinishedPrepare -- $order_post_type_placeholder is prepared.
			$missing_orders_count_sql = $wpdb->prepare(
				"
SELECT COUNT(1) FROM $wpdb->posts posts
LEFT JOIN $orders_table orders ON posts.id=orders.id
WHERE
  posts.post_type in ($order_post_type_placeholder)
  AND posts.post_status != 'auto-draft'
  AND orders.id IS NULL",
				$order_post_types
			);
			// phpcs:enable
			$operator = '<';
		}

		// phpcs:disable WordPress.DB.PreparedSQL.InterpolatedNotPrepared, WordPress.DB.PreparedSQLPlaceholders.UnfinishedPrepare -- $missing_orders_count_sql is prepared.
		$sql = $wpdb->prepare(
			"
SELECT(
	($missing_orders_count_sql)
	+
	(SELECT COUNT(1) FROM (
		SELECT orders.id FROM $orders_table orders
		JOIN $wpdb->posts posts on posts.ID = orders.id
		WHERE
		  posts.post_type IN ($order_post_type_placeholder)
		  AND orders.date_updated_gmt $operator posts.post_modified_gmt
	) x)
) count",
			$order_post_types
		);
		// phpcs:enable

		// phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
		$pending_count = (int) $wpdb->get_var( $sql );
		wp_cache_set( 'woocommerce_hpos_pending_sync_count', $pending_count );
		return $pending_count;
	}

	/**
	 * Is the custom orders table the authoritative data source for orders currently?
	 *
	 * @return bool Whether the custom orders table the authoritative data source for orders currently.
	 */
	public function custom_orders_table_is_authoritative(): bool {
		return wc_string_to_bool( get_option( CustomOrdersTableController::CUSTOM_ORDERS_TABLE_USAGE_ENABLED_OPTION ) );
	}

	/**
	 * Get a list of ids of orders than are out of sync.
	 *
	 * Valid values for $type are:
	 *
	 * ID_TYPE_MISSING_IN_ORDERS_TABLE: orders that exist in posts table but not in orders table.
	 * ID_TYPE_MISSING_IN_POSTS_TABLE: orders that exist in orders table but not in posts table (the corresponding post entries are placeholders).
	 * ID_TYPE_DIFFERENT_UPDATE_DATE: orders that exist in both tables but have different last update dates.
	 *
	 * @param int $type One of ID_TYPE_MISSING_IN_ORDERS_TABLE, ID_TYPE_MISSING_IN_POSTS_TABLE, ID_TYPE_DIFFERENT_UPDATE_DATE.
	 * @param int $limit Maximum number of ids to return.
	 * @return array An array of order ids.
	 * @throws \Exception Invalid parameter.
	 */
	public function get_ids_of_orders_pending_sync( int $type, int $limit ) {
		global $wpdb;

		if ( $limit < 1 ) {
			throw new \Exception( '$limit must be at least 1' );
		}

		$orders_table                 = $this->data_store::get_orders_table_name();
		$order_post_types             = wc_get_order_types( 'cot-migration' );
		$order_post_type_placeholders = implode( ', ', array_fill( 0, count( $order_post_types ), '%s' ) );

		// phpcs:disable WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		switch ( $type ) {
			case self::ID_TYPE_MISSING_IN_ORDERS_TABLE:
				// phpcs:disable WordPress.DB.PreparedSQL.InterpolatedNotPrepared, WordPress.DB.PreparedSQLPlaceholders.UnfinishedPrepare -- $order_post_type_placeholders is prepared.
				$sql = $wpdb->prepare(
					"
SELECT posts.ID FROM $wpdb->posts posts
LEFT JOIN $orders_table orders ON posts.ID = orders.id
WHERE
  posts.post_type IN ($order_post_type_placeholders)
  AND posts.post_status != 'auto-draft'
  AND orders.id IS NULL
ORDER BY posts.ID ASC",
					$order_post_types
				);
				// phpcs:enable WordPress.DB.PreparedSQLPlaceholders.UnfinishedPrepare
				break;
			case self::ID_TYPE_MISSING_IN_POSTS_TABLE:
				$sql = "
SELECT posts.ID FROM $wpdb->posts posts
INNER JOIN $orders_table orders ON posts.id=orders.id
WHERE posts.post_type = '" . self::PLACEHOLDER_ORDER_POST_TYPE . "'
AND orders.status not in ( 'auto-draft' )
ORDER BY posts.id ASC
";
				break;
			case self::ID_TYPE_DIFFERENT_UPDATE_DATE:
				$operator = $this->custom_orders_table_is_authoritative() ? '>' : '<';
				// phpcs:disable WordPress.DB.PreparedSQL.InterpolatedNotPrepared, WordPress.DB.PreparedSQLPlaceholders.UnfinishedPrepare -- $order_post_type_placeholders is prepared.
				$sql = $wpdb->prepare(
					"
SELECT orders.id FROM $orders_table orders
JOIN $wpdb->posts posts on posts.ID = orders.id
WHERE
  posts.post_type IN ($order_post_type_placeholders)
  AND orders.date_updated_gmt $operator posts.post_modified_gmt
ORDER BY orders.id ASC
",
					$order_post_types
				);
				// phpcs:enable
				break;
			default:
				throw new \Exception( 'Invalid $type, must be one of the ID_TYPE_... constants.' );
		}
		// phpcs:enable WordPress.DB.PreparedSQL.InterpolatedNotPrepared

		// phpcs:ignore WordPress.DB
		return array_map( 'intval', $wpdb->get_col( $sql . " LIMIT $limit" ) );
	}

	/**
	 * Cleanup all the synchronization status information,
	 * because the process has been disabled by the user via settings,
	 * or because there's nothing left to synchronize.
	 */
	public function cleanup_synchronization_state() {
		delete_option( self::INITIAL_ORDERS_PENDING_SYNC_COUNT_OPTION );
	}

	/**
	 * Process data for current batch.
	 *
	 * @param array $batch Batch details.
	 */
	public function process_batch( array $batch ) : void {
		$this->order_cache_controller->temporarily_disable_orders_cache_usage();

		if ( $this->custom_orders_table_is_authoritative() ) {
			foreach ( $batch as $id ) {
				$order = wc_get_order( $id );
				if ( ! $order ) {
					$this->error_logger->error( "Order $id not found during batch process, skipping." );
					continue;
				}
				$data_store = $order->get_data_store();
				$data_store->backfill_post_record( $order );
			}
		} else {
			$this->posts_to_cot_migrator->migrate_orders( $batch );
		}
		if ( 0 === $this->get_total_pending_count() ) {
			$this->cleanup_synchronization_state();
			$this->order_cache_controller->maybe_restore_orders_cache_usage();
		}
	}

	/**
	 * Get total number of pending records that require update.
	 *
	 * @return int Number of pending records.
	 */
	public function get_total_pending_count(): int {
		return $this->get_current_orders_pending_sync_count();
	}

	/**
	 * Returns the batch with records that needs to be processed for a given size.
	 *
	 * @param int $size Size of the batch.
	 *
	 * @return array Batch of records.
	 */
	public function get_next_batch_to_process( int $size ): array {
		if ( $this->custom_orders_table_is_authoritative() ) {
			$order_ids = $this->get_ids_of_orders_pending_sync( self::ID_TYPE_MISSING_IN_POSTS_TABLE, $size );
		} else {
			$order_ids = $this->get_ids_of_orders_pending_sync( self::ID_TYPE_MISSING_IN_ORDERS_TABLE, $size );
		}
		if ( count( $order_ids ) >= $size ) {
			return $order_ids;
		}

		$order_ids = $order_ids + $this->get_ids_of_orders_pending_sync( self::ID_TYPE_DIFFERENT_UPDATE_DATE, $size - count( $order_ids ) );
		return $order_ids;
	}

	/**
	 * Default batch size to use.
	 *
	 * @return int Default batch size.
	 */
	public function get_default_batch_size(): int {
		$batch_size = self::ORDERS_SYNC_BATCH_SIZE;

		if ( $this->custom_orders_table_is_authoritative() ) {
			// Back-filling is slower than migration.
			$batch_size = absint( self::ORDERS_SYNC_BATCH_SIZE / 10 ) + 1;
		}
		/**
		 * Filter to customize the count of orders that will be synchronized in each step of the custom orders table to/from posts table synchronization process.
		 *
		 * @since 6.6.0
		 *
		 * @param int Default value for the count.
		 */
		return apply_filters( 'woocommerce_orders_cot_and_posts_sync_step_size', $batch_size );
	}

	/**
	 * A user friendly name for this process.
	 *
	 * @return string Name of the process.
	 */
	public function get_name(): string {
		return 'Order synchronizer';
	}

	/**
	 * A user friendly description for this process.
	 *
	 * @return string Description.
	 */
	public function get_description(): string {
		return 'Synchronizes orders between posts and custom order tables.';
	}

	/**
	 * Handle the 'deleted_post' action.
	 *
	 * When posts is authoritative and sync is enabled, deleting a post also deletes COT data.
	 *
	 * @param int     $postid The post id.
	 * @param WP_Post $post The deleted post.
	 */
	private function handle_deleted_post( $postid, $post ): void {
		if ( 'shop_order' === $post->post_type && $this->data_sync_is_enabled() ) {
			$this->data_store->delete_order_data_from_custom_order_tables( $postid );
		}
	}

	/**
	 * Handle the 'woocommerce_update_order' action.
	 *
	 * When posts is authoritative and sync is enabled, updating a post triggers a corresponding change in the COT table.
	 *
	 * @param int $order_id The order id.
	 */
	private function handle_updated_order( $order_id ): void {
		if ( ! $this->custom_orders_table_is_authoritative() && $this->data_sync_is_enabled() ) {
			$this->posts_to_cot_migrator->migrate_orders( array( $order_id ) );
		}
	}

	/**
	 * Handle the 'woocommerce_feature_description_tip' filter.
	 *
	 * When the COT feature is enabled and there are orders pending sync (in either direction),
	 * show a "you should ync before disabling" warning under the feature in the features page.
	 * Skip this if the UI prevents changing the feature enable status.
	 *
	 * @param string $desc_tip The original description tip for the feature.
	 * @param string $feature_id The feature id.
	 * @param bool   $ui_disabled True if the UI doesn't allow to enable or disable the feature.
	 * @return string The new description tip for the feature.
	 */
	private function handle_feature_description_tip( $desc_tip, $feature_id, $ui_disabled ): string {
		if ( 'custom_order_tables' !== $feature_id || $ui_disabled ) {
			return $desc_tip;
		}

		$features_controller = wc_get_container()->get( FeaturesController::class );
		$feature_is_enabled  = $features_controller->feature_is_enabled( 'custom_order_tables' );
		if ( ! $feature_is_enabled ) {
			return $desc_tip;
		}

		$pending_sync_count = $this->get_current_orders_pending_sync_count();
		if ( ! $pending_sync_count ) {
			return $desc_tip;
		}

		if ( $this->custom_orders_table_is_authoritative() ) {
			$extra_tip = sprintf(
				_n(
					"⚠ There's one order pending sync from the orders table to the posts table. The feature shouldn't be disabled until this order is synchronized.",
					"⚠ There are %1\$d orders pending sync from the orders table to the posts table. The feature shouldn't be disabled until these orders are synchronized.",
					$pending_sync_count,
					'woocommerce'
				),
				$pending_sync_count
			);
		} else {
			$extra_tip = sprintf(
				_n(
					"⚠ There's one order pending sync from the posts table to the orders table. The feature shouldn't be disabled until this order is synchronized.",
					"⚠ There are %1\$d orders pending sync from the posts table to the orders table. The feature shouldn't be disabled until these orders are synchronized.",
					$pending_sync_count,
					'woocommerce'
				),
				$pending_sync_count
			);
		}

		$cot_settings_url = add_query_arg(
			array(
				'page'    => 'wc-settings',
				'tab'     => 'advanced',
				'section' => 'custom_data_stores',
			),
			admin_url( 'admin.php' )
		);

		/* translators: %s = URL of the custom data stores settings page */
		$manage_cot_settings_link = sprintf( __( "<a href='%s'>Manage orders synchronization</a>", 'woocommerce' ), $cot_settings_url );

		return $desc_tip ? "{$desc_tip}<br/>{$extra_tip} {$manage_cot_settings_link}" : "{$extra_tip} {$manage_cot_settings_link}";
	}
}
