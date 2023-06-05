<?php
/**
 * DataRegenerator class file.
 */

namespace Automattic\WooCommerce\Internal\ProductAttributesLookup;

use Automattic\WooCommerce\Internal\Traits\AccessiblePrivateMethods;
use Automattic\WooCommerce\Internal\Utilities\DatabaseUtil;

defined( 'ABSPATH' ) || exit;

/**
 * This class handles the (re)generation of the product attributes lookup table.
 * It schedules the regeneration in small product batches by itself, so it can be used outside the
 * regular WooCommerce data regenerations mechanism.
 *
 * After the regeneration is completed a wp_wc_product_attributes_lookup table will exist with entries for
 * all the products that existed when initiate_regeneration was invoked; entries for products created after that
 * are supposed to be created/updated by the appropriate data store classes (or by the code that uses
 * the data store classes) whenever a product is created/updated.
 *
 * Additionally, after the regeneration is completed a 'woocommerce_attribute_lookup_enabled' option
 * with a value of 'yes' will have been created, thus effectively enabling the table usage
 * (with an exception: if the regeneration was manually aborted via deleting the
 * 'woocommerce_attribute_lookup_regeneration_in_progress' option) the option will be set to 'no'.
 *
 * This class also adds two entries to the Status - Tools menu: one for manually regenerating the table contents,
 * and another one for enabling or disabling the actual lookup table usage.
 */
class DataRegenerator {

	use AccessiblePrivateMethods;

	public const PRODUCTS_PER_GENERATION_STEP = 10;

	/**
	 * The data store to use.
	 *
	 * @var LookupDataStore
	 */
	private $data_store;

	/**
	 * The lookup table name.
	 *
	 * @var string
	 */
	private $lookup_table_name;

	/**
	 * DataRegenerator constructor.
	 */
	public function __construct() {
		global $wpdb;

		$this->lookup_table_name = $wpdb->prefix . 'wc_product_attributes_lookup';

		self::add_filter( 'woocommerce_debug_tools', array( $this, 'add_initiate_regeneration_entry_to_tools_array' ), 1, 999 );
		self::add_action( 'woocommerce_run_product_attribute_lookup_regeneration_callback', array( $this, 'run_regeneration_step_callback' ) );
		self::add_action( 'woocommerce_installed', array( $this, 'run_woocommerce_installed_callback' ) );
	}

	/**
	 * Class initialization, invoked by the DI container.
	 *
	 * @internal
	 * @param LookupDataStore $data_store The data store to use.
	 */
	final public function init( LookupDataStore $data_store ) {
		$this->data_store = $data_store;
	}

	/**
	 * Initialize the regeneration procedure:
	 * deletes the lookup table and related options if they exist,
	 * then it creates the table and runs the first step of the regeneration process.
	 *
	 * This method is intended ONLY to be used as a callback for a db update in wc-update-functions,
	 * regeneration triggered from the tools page will use initiate_regeneration_from_tools_page instead.
	 */
	public function initiate_regeneration() {
		$this->data_store->unset_regeneration_aborted_flag();
		$this->enable_or_disable_lookup_table_usage( false );

		$this->delete_all_attributes_lookup_data();
		$products_exist = $this->initialize_table_and_data();
		if ( $products_exist ) {
			$this->enqueue_regeneration_step_run();
		} else {
			$this->finalize_regeneration( true );
		}
	}

	/**
	 * Delete all the existing data related to the lookup table, including the table itself.
	 */
	private function delete_all_attributes_lookup_data() {
		global $wpdb;

		delete_option( 'woocommerce_attribute_lookup_enabled' );
		delete_option( 'woocommerce_attribute_lookup_last_product_id_to_process' );
		delete_option( 'woocommerce_attribute_lookup_processed_count' );
		$this->data_store->unset_regeneration_in_progress_flag();

		if ( $this->data_store->check_lookup_table_exists() ) {
			$wpdb->query( "TRUNCATE TABLE {$this->lookup_table_name}" ); // phpcs:disable WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		}
	}

	/**
	 * Create the lookup table and initialize the options that will be temporarily used
	 * while the regeneration is in progress.
	 *
	 * @return bool True if there's any product at all in the database, false otherwise.
	 */
	private function initialize_table_and_data() {
		$database_util = wc_get_container()->get( DatabaseUtil::class );
		$database_util->dbdelta( $this->get_table_creation_sql() );

		$last_existing_product_id = $this->get_last_existing_product_id();
		if ( ! $last_existing_product_id ) {
			// No products exist, nothing to (re)generate.
			return false;
		}

		$this->data_store->set_regeneration_in_progress_flag();
		update_option( 'woocommerce_attribute_lookup_last_product_id_to_process', $last_existing_product_id );
		update_option( 'woocommerce_attribute_lookup_processed_count', 0 );

		return true;
	}

	/**
	 * Get the highest existing product id.
	 *
	 * @return int|null Highest existing product id, or null if no products exist at all.
	 */
	private function get_last_existing_product_id(): ?int {
		$last_existing_product_id_array =
			WC()->call_function(
				'wc_get_products',
				array(
					'return'  => 'ids',
					'limit'   => 1,
					'orderby' => array(
						'ID' => 'DESC',
					),
				)
			);

		return empty( $last_existing_product_id_array ) ? null : current( $last_existing_product_id_array );
	}

	/**
	 * Action scheduler callback, performs one regeneration step and then
	 * schedules the next step if necessary.
	 */
	private function run_regeneration_step_callback() {
		if ( ! $this->data_store->regeneration_is_in_progress() ) {
			// No regeneration in progress at this point means that the regeneration process
			// was manually aborted via deleting the 'woocommerce_attribute_lookup_regeneration_in_progress' option.
			$this->data_store->set_regeneration_aborted_flag();
			$this->finalize_regeneration( false );
			return;
		}

		$result = $this->do_regeneration_step();
		if ( $result ) {
			$this->enqueue_regeneration_step_run();
		} else {
			$this->finalize_regeneration( true );
		}
	}

	/**
	 * Enqueue one regeneration step in action scheduler.
	 */
	private function enqueue_regeneration_step_run() {
		$queue = WC()->get_instance_of( \WC_Queue::class );
		$queue->schedule_single(
			WC()->call_function( 'time' ) + 1,
			'woocommerce_run_product_attribute_lookup_regeneration_callback',
			array(),
			'woocommerce-db-updates'
		);
	}

	/**
	 * Perform one regeneration step: grabs a chunk of products and creates
	 * the appropriate entries for them in the lookup table.
	 *
	 * @return bool True if more steps need to be run, false otherwise.
	 */
	private function do_regeneration_step() {
		/**
		 * Filter to alter the count of products that will be processed in each step of the product attributes lookup table regeneration process.
		 *
		 * @since 6.3
		 * @param int $count Default processing step size.
		 */
		$products_per_generation_step = apply_filters( 'woocommerce_attribute_lookup_regeneration_step_size', self::PRODUCTS_PER_GENERATION_STEP );

		$products_already_processed = get_option( 'woocommerce_attribute_lookup_processed_count', 0 );

		$product_ids = WC()->call_function(
			'wc_get_products',
			array(
				'limit'   => $products_per_generation_step,
				'offset'  => $products_already_processed,
				'orderby' => array(
					'ID' => 'ASC',
				),
				'return'  => 'ids',
			)
		);

		if ( ! $product_ids ) {
			return false;
		}

		foreach ( $product_ids as $id ) {
			$this->data_store->create_data_for_product( $id );
		}

		$products_already_processed += count( $product_ids );
		update_option( 'woocommerce_attribute_lookup_processed_count', $products_already_processed );

		$last_product_id_to_process = get_option( 'woocommerce_attribute_lookup_last_product_id_to_process', PHP_INT_MAX );
		return end( $product_ids ) < $last_product_id_to_process;
	}

	/**
	 * Cleanup/final option setup after the regeneration has been completed.
	 *
	 * @param bool $enable_usage Whether the table usage should be enabled or not.
	 */
	private function finalize_regeneration( bool $enable_usage ) {
		delete_option( 'woocommerce_attribute_lookup_last_product_id_to_process' );
		delete_option( 'woocommerce_attribute_lookup_processed_count' );
		update_option( 'woocommerce_attribute_lookup_enabled', $enable_usage ? 'yes' : 'no' );
		$this->data_store->unset_regeneration_in_progress_flag();
	}

	/**
	 * Add a 'Regenerate product attributes lookup table' entry to the Status - Tools page.
	 *
	 * @param array $tools_array The tool definitions array that is passed ro the woocommerce_debug_tools filter.
	 * @return array The tools array with the entry added.
	 */
	private function add_initiate_regeneration_entry_to_tools_array( array $tools_array ) {
		if ( ! $this->data_store->check_lookup_table_exists() ) {
			return $tools_array;
		}

		$generation_is_in_progress = $this->data_store->regeneration_is_in_progress();
		$generation_was_aborted    = $this->data_store->regeneration_was_aborted();

		$entry = array(
			'name'             => __( 'Regenerate the product attributes lookup table', 'woocommerce' ),
			'desc'             => __( 'This tool will regenerate the product attributes lookup table data from existing product(s) data. This process may take a while.', 'woocommerce' ),
			'requires_refresh' => true,
			'callback'         => function() {
				$this->initiate_regeneration_from_tools_page();
				return __( 'Product attributes lookup table data is regenerating', 'woocommerce' );

			},
			'selector'         => array(
				'description'   => __( 'Select a product to regenerate the data for, or leave empty for a full table regeneration:', 'woocommerce' ),
				'class'         => 'wc-product-search',
				'search_action' => 'woocommerce_json_search_products',
				'name'          => 'regenerate_product_attribute_lookup_data_product_id',
				'placeholder'   => esc_attr__( 'Search for a product&hellip;', 'woocommerce' ),
			),
		);

		if ( $generation_is_in_progress ) {
			$entry['button'] = sprintf(
				/* translators: %d: How many products have been processed so far. */
				__( 'Filling in progress (%d)', 'woocommerce' ),
				get_option( 'woocommerce_attribute_lookup_processed_count', 0 )
			);
			$entry['disabled'] = true;
		} else {
			$entry['button'] = __( 'Regenerate', 'woocommerce' );
		}

		$tools_array['regenerate_product_attributes_lookup_table'] = $entry;

		if ( $generation_is_in_progress ) {
			$entry = array(
				'name'             => __( 'Abort the product attributes lookup table regeneration', 'woocommerce' ),
				'desc'             => __( 'This tool will abort the regenerate product attributes lookup table regeneration. After this is done the process can be either started over, or resumed to continue where it stopped.', 'woocommerce' ),
				'requires_refresh' => true,
				'callback'         => function() {
					$this->abort_regeneration_from_tools_page();
					return __( 'Product attributes lookup table regeneration process has been aborted.', 'woocommerce' );
				},
				'button'           => __( 'Abort', 'woocommerce' ),
			);
			$tools_array['abort_product_attributes_lookup_table_regeneration'] = $entry;
		} elseif ( $generation_was_aborted ) {
			$processed_count = get_option( 'woocommerce_attribute_lookup_processed_count', 0 );
			$entry           = array(
				'name'             => __( 'Resume the product attributes lookup table regeneration', 'woocommerce' ),
				'desc'             =>
					sprintf(
						/* translators: %1$s = count of products already processed. */
						__( 'This tool will resume the product attributes lookup table regeneration at the point in which it was aborted (%1$s products were already processed).', 'woocommerce' ),
						$processed_count
					),
				'requires_refresh' => true,
				'callback'         => function() {
					$this->resume_regeneration_from_tools_page();
					return __( 'Product attributes lookup table regeneration process has been resumed.', 'woocommerce' );
				},
				'button'           => __( 'Resume', 'woocommerce' ),
			);
			$tools_array['resume_product_attributes_lookup_table_regeneration'] = $entry;
		}

		return $tools_array;
	}

	/**
	 * Callback to initiate the regeneration process from the Status - Tools page.
	 *
	 * @throws \Exception The regeneration is already in progress.
	 */
	private function initiate_regeneration_from_tools_page() {
		$this->verify_tool_execution_nonce();

		//phpcs:disable WordPress.Security.NonceVerification.Recommended
		if ( isset( $_REQUEST['regenerate_product_attribute_lookup_data_product_id'] ) ) {
			$product_id = (int) $_REQUEST['regenerate_product_attribute_lookup_data_product_id'];
			$this->check_can_do_lookup_table_regeneration( $product_id );
			$this->data_store->create_data_for_product( $product_id );
		} else {
			$this->check_can_do_lookup_table_regeneration();
			$this->initiate_regeneration();
		}
		//phpcs:enable WordPress.Security.NonceVerification.Recommended
	}

	/**
	 * Enable or disable the actual lookup table usage.
	 *
	 * @param bool $enable True to enable, false to disable.
	 * @throws \Exception A lookup table regeneration is currently in progress.
	 */
	private function enable_or_disable_lookup_table_usage( $enable ) {
		if ( $this->data_store->regeneration_is_in_progress() ) {
			throw new \Exception( "Can't enable or disable the attributes lookup table usage while it's regenerating." );
		}

		update_option( 'woocommerce_attribute_lookup_enabled', $enable ? 'yes' : 'no' );
	}

	/**
	 * Check if everything is good to go to perform a complete or per product lookup table data regeneration
	 * and throw an exception if not.
	 *
	 * @param mixed $product_id The product id to check the regeneration viability for, or null to check if a complete regeneration is possible.
	 * @throws \Exception Something prevents the regeneration from starting.
	 */
	private function check_can_do_lookup_table_regeneration( $product_id = null ) {
		if ( $product_id && ! $this->data_store->check_lookup_table_exists() ) {
			throw new \Exception( "Can't do product attribute lookup data regeneration: lookup table doesn't exist" );
		}
		if ( $this->data_store->regeneration_is_in_progress() ) {
			throw new \Exception( "Can't do product attribute lookup data regeneration: regeneration is already in progress" );
		}
		if ( $product_id && ! wc_get_product( $product_id ) ) {
			throw new \Exception( "Can't do product attribute lookup data regeneration: product doesn't exist" );
		}
	}

	/**
	 * Callback to abort the regeneration process from the Status - Tools page.
	 *
	 * @throws \Exception The lookup table doesn't exist, or there's no regeneration process in progress to abort.
	 */
	private function abort_regeneration_from_tools_page() {
		$this->verify_tool_execution_nonce();

		if ( ! $this->data_store->check_lookup_table_exists() ) {
			throw new \Exception( "Can't abort the product attribute lookup data regeneration process: lookup table doesn't exist" );
		}
		if ( ! $this->data_store->regeneration_is_in_progress() ) {
			throw new \Exception( "Can't abort the product attribute lookup data regeneration process since it's not currently in progress" );
		}

		$queue = WC()->get_instance_of( \WC_Queue::class );
		$queue->cancel_all( 'woocommerce_run_product_attribute_lookup_regeneration_callback' );
		$this->data_store->unset_regeneration_in_progress_flag();
		$this->data_store->set_regeneration_aborted_flag();
		$this->enable_or_disable_lookup_table_usage( false );

		// Note that we are NOT deleting the options that track the regeneration progress (processed count, last product id to process).
		// This is on purpose so that the regeneration can be resumed where it stopped.
	}

	/**
	 * Callback to resume the regeneration process from the Status - Tools page.
	 *
	 * @throws \Exception The lookup table doesn't exist, or a regeneration process is already in place.
	 */
	private function resume_regeneration_from_tools_page() {
		$this->verify_tool_execution_nonce();

		if ( ! $this->data_store->check_lookup_table_exists() ) {
			throw new \Exception( "Can't resume the product attribute lookup data regeneration process: lookup table doesn't exist" );
		}
		if ( $this->data_store->regeneration_is_in_progress() ) {
			throw new \Exception( "Can't resume the product attribute lookup data regeneration process: regeneration is already in progress" );
		}

		$this->data_store->unset_regeneration_aborted_flag();
		$this->data_store->set_regeneration_in_progress_flag();
		$this->enqueue_regeneration_step_run();
	}

	/**
	 * Verify the validity of the nonce received when executing a tool from the Status - Tools page.
	 *
	 * @throws \Exception Missing or invalid nonce received.
	 */
	private function verify_tool_execution_nonce() {
		// phpcs:ignore WordPress.Security.ValidatedSanitizedInput
		if ( ! isset( $_REQUEST['_wpnonce'] ) || wp_verify_nonce( $_REQUEST['_wpnonce'], 'debug_action' ) === false ) {
			throw new \Exception( 'Invalid nonce' );
		}
	}

	/**
	 * Get the name of the product attributes lookup table.
	 *
	 * @return string
	 */
	public function get_lookup_table_name() {
		return $this->lookup_table_name;
	}

	/**
	 * Get the SQL statement that creates the product attributes lookup table, including the indices.
	 *
	 * @return string
	 */
	public function get_table_creation_sql() {
		global $wpdb;

		$collate = $wpdb->has_cap( 'collation' ) ? $wpdb->get_charset_collate() : '';

		return "CREATE TABLE {$this->lookup_table_name} (
 product_id bigint(20) NOT NULL,
 product_or_parent_id bigint(20) NOT NULL,
 taxonomy varchar(32) NOT NULL,
 term_id bigint(20) NOT NULL,
 is_variation_attribute tinyint(1) NOT NULL,
 in_stock tinyint(1) NOT NULL,
 INDEX is_variation_attribute_term_id (is_variation_attribute, term_id),
 PRIMARY KEY  ( `product_or_parent_id`, `term_id`, `product_id`, `taxonomy` )
) $collate;";
	}

	/**
	 * Create the primary key for the table if it doesn't exist already.
	 * It also deletes the product_or_parent_id_term_id index if it exists, since it's now redundant.
	 *
	 * @return void
	 */
	public function create_table_primary_index() {
		$database_util = wc_get_container()->get( DatabaseUtil::class );
		$database_util->create_primary_key( $this->lookup_table_name, array( 'product_or_parent_id', 'term_id', 'product_id', 'taxonomy' ) );
		$database_util->drop_table_index( $this->lookup_table_name, 'product_or_parent_id_term_id' );

		if ( empty( $database_util->get_index_columns( $this->lookup_table_name ) ) ) {
			wc_get_logger()->error( "The creation of the primary key for the {$this->lookup_table_name} table failed" );
		}

		if ( ! empty( $database_util->get_index_columns( $this->lookup_table_name, 'product_or_parent_id_term_id' ) ) ) {
			wc_get_logger()->error( "Dropping the product_or_parent_id_term_id index from the {$this->lookup_table_name} table failed" );
		}
	}

	/**
	 * Run additional setup needed after a WooCommerce install or update finishes.
	 */
	private function run_woocommerce_installed_callback() {
		// The table must exist at this point (created via dbDelta), but we check just in case.
		if ( ! $this->data_store->check_lookup_table_exists() ) {
			return;
		}

		// If a table regeneration is in progress, leave it alone.
		if ( $this->data_store->regeneration_is_in_progress() ) {
			return;
		}

		// If the lookup table has data, or if it's empty because there are no products yet, we're good.
		// Otherwise (lookup table is empty but products exist) we need to initiate a regeneration if one isn't already in progress.
		if ( $this->data_store->lookup_table_has_data() || ! $this->get_last_existing_product_id() ) {
			$must_enable = get_option( 'woocommerce_attribute_lookup_enabled' ) !== 'no';
			$this->finalize_regeneration( $must_enable );
		} else {
			$this->initiate_regeneration();
		}
	}
}
