<?php
/**
 * LookupDataStore class file.
 */

namespace Automattic\WooCommerce\Internal\ProductAttributesLookup;

use Automattic\WooCommerce\Internal\Traits\AccessiblePrivateMethods;
use Automattic\WooCommerce\Utilities\ArrayUtil;
use Automattic\WooCommerce\Utilities\StringUtil;

defined( 'ABSPATH' ) || exit;

/**
 * Data store class for the product attributes lookup table.
 */
class LookupDataStore {

	use AccessiblePrivateMethods;

	/**
	 * Types of updates to perform depending on the current changest
	 */

	public const ACTION_NONE         = 0;
	public const ACTION_INSERT       = 1;
	public const ACTION_UPDATE_STOCK = 2;
	public const ACTION_DELETE       = 3;

	/**
	 * The lookup table name.
	 *
	 * @var string
	 */
	private $lookup_table_name;

	/**
	 * LookupDataStore constructor. Makes the feature hidden by default.
	 */
	public function __construct() {
		global $wpdb;

		$this->lookup_table_name = $wpdb->prefix . 'wc_product_attributes_lookup';

		$this->init_hooks();
	}

	/**
	 * Initialize the hooks used by the class.
	 */
	private function init_hooks() {
		self::add_action( 'woocommerce_run_product_attribute_lookup_update_callback', array( $this, 'run_update_callback' ), 10, 2 );
		self::add_filter( 'woocommerce_get_sections_products', array( $this, 'add_advanced_section_to_product_settings' ), 100, 1 );
		self::add_action( 'woocommerce_rest_insert_product', array( $this, 'on_product_created_or_updated_via_rest_api' ), 100, 2 );
		self::add_filter( 'woocommerce_get_settings_products', array( $this, 'add_product_attributes_lookup_table_settings' ), 100, 2 );
	}

	/**
	 * Check if the lookup table exists in the database.
	 *
	 * @return bool
	 */
	public function check_lookup_table_exists() {
		global $wpdb;

		$query = $wpdb->prepare( 'SHOW TABLES LIKE %s', $wpdb->esc_like( $this->lookup_table_name ) );

		// phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
		return $this->lookup_table_name === $wpdb->get_var( $query );
	}

	/**
	 * Get the name of the lookup table.
	 *
	 * @return string
	 */
	public function get_lookup_table_name() {
		return $this->lookup_table_name;
	}

	/**
	 * Insert/update the appropriate lookup table entries for a new or modified product or variation.
	 * This must be invoked after a product or a variation is created (including untrashing and duplication)
	 * or modified.
	 *
	 * @param int|\WC_Product $product Product object or product id.
	 * @param null|array      $changeset Changes as provided by 'get_changes' method in the product object, null if it's being created.
	 */
	public function on_product_changed( $product, $changeset = null ) {
		if ( ! $this->check_lookup_table_exists() ) {
			return;
		}

		if ( ! is_a( $product, \WC_Product::class ) ) {
			$product = WC()->call_function( 'wc_get_product', $product );
		}

		$action = $this->get_update_action( $changeset );
		if ( $action !== self::ACTION_NONE ) {
			$this->maybe_schedule_update( $product->get_id(), $action );
		}
	}

	/**
	 * Schedule an update of the product attributes lookup table for a given product.
	 * If an update for the same action is already scheduled, nothing is done.
	 *
	 * If the 'woocommerce_attribute_lookup_direct_update' option is set to 'yes',
	 * the update is done directly, without scheduling.
	 *
	 * @param int $product_id The product id to schedule the update for.
	 * @param int $action The action to perform, one of the ACTION_ constants.
	 */
	private function maybe_schedule_update( int $product_id, int $action ) {
		if ( get_option( 'woocommerce_attribute_lookup_direct_updates' ) === 'yes' ) {
			$this->run_update_callback( $product_id, $action );
			return;
		}

		$args = array( $product_id, $action );

		$queue             = WC()->get_instance_of( \WC_Queue::class );
		$already_scheduled = $queue->search(
			array(
				'hook'   => 'woocommerce_run_product_attribute_lookup_update_callback',
				'args'   => $args,
				'status' => \ActionScheduler_Store::STATUS_PENDING,
			),
			'ids'
		);

		if ( empty( $already_scheduled ) ) {
			$queue->schedule_single(
				WC()->call_function( 'time' ) + 1,
				'woocommerce_run_product_attribute_lookup_update_callback',
				$args,
				'woocommerce-db-updates'
			);
		}
	}

	/**
	 * Perform an update of the lookup table for a specific product.
	 *
	 * @param int $product_id The product id to perform the update for.
	 * @param int $action The action to perform, one of the ACTION_ constants.
	 */
	private function run_update_callback( int $product_id, int $action ) {
		if ( ! $this->check_lookup_table_exists() ) {
			return;
		}

		$product = WC()->call_function( 'wc_get_product', $product_id );
		if ( ! $product ) {
			$action = self::ACTION_DELETE;
		}

		switch ( $action ) {
			case self::ACTION_INSERT:
				$this->delete_data_for( $product_id );
				$this->create_data_for( $product );
				break;
			case self::ACTION_UPDATE_STOCK:
				$this->update_stock_status_for( $product );
				break;
			case self::ACTION_DELETE:
				$this->delete_data_for( $product_id );
				break;
		}
	}

	/**
	 * Determine the type of action to perform depending on the received changeset.
	 *
	 * @param array|null $changeset The changeset received by on_product_changed.
	 * @return int One of the ACTION_ constants.
	 */
	private function get_update_action( $changeset ) {
		if ( is_null( $changeset ) ) {
			// No changeset at all means that the product is new.
			return self::ACTION_INSERT;
		}

		$keys = array_keys( $changeset );

		// Order matters:
		// - The change with the most precedence is a change in catalog visibility
		// (which will result in all data being regenerated or deleted).
		// - Then a change in attributes (all data will be regenerated).
		// - And finally a change in stock status (existing data will be updated).
		// Thus these conditions must be checked in that same order.

		if ( in_array( 'catalog_visibility', $keys, true ) ) {
			$new_visibility = $changeset['catalog_visibility'];
			if ( $new_visibility === 'visible' || $new_visibility === 'catalog' ) {
				return self::ACTION_INSERT;
			} else {
				return self::ACTION_DELETE;
			}
		}

		if ( in_array( 'attributes', $keys, true ) ) {
			return self::ACTION_INSERT;
		}

		if ( array_intersect( $keys, array( 'stock_quantity', 'stock_status', 'manage_stock' ) ) ) {
			return self::ACTION_UPDATE_STOCK;
		}

		return self::ACTION_NONE;
	}

	/**
	 * Update the stock status of the lookup table entries for a given product.
	 *
	 * @param \WC_Product $product The product to update the entries for.
	 */
	private function update_stock_status_for( \WC_Product $product ) {
		global $wpdb;

		$in_stock = $product->is_in_stock();

		// phpcs:disable WordPress.DB.PreparedSQL.NotPrepared
		$wpdb->query(
			$wpdb->prepare(
				'UPDATE ' . $this->lookup_table_name . ' SET in_stock = %d WHERE product_id = %d',
				$in_stock ? 1 : 0,
				$product->get_id()
			)
		);
		// phpcs:enable WordPress.DB.PreparedSQL.NotPrepared
	}

	/**
	 * Delete the lookup table contents related to a given product or variation,
	 * if it's a variable product it deletes the information for variations too.
	 * This must be invoked after a product or a variation is trashed or deleted.
	 *
	 * @param int|\WC_Product $product Product object or product id.
	 */
	public function on_product_deleted( $product ) {
		if ( ! $this->check_lookup_table_exists() ) {
			return;
		}

		if ( is_a( $product, \WC_Product::class ) ) {
			$product_id = $product->get_id();
		} else {
			$product_id = $product;
		}

		$this->maybe_schedule_update( $product_id, self::ACTION_DELETE );
	}

	/**
	 * Create the lookup data for a given product, if a variable product is passed
	 * the information is created for all of its variations.
	 * This method is intended to be called from the data regenerator.
	 *
	 * @param int|WC_Product $product Product object or id.
	 * @throws \Exception A variation object is passed.
	 */
	public function create_data_for_product( $product ) {
		if ( ! is_a( $product, \WC_Product::class ) ) {
			$product = WC()->call_function( 'wc_get_product', $product );
		}

		if ( $this->is_variation( $product ) ) {
			throw new \Exception( "LookupDataStore::create_data_for_product can't be called for variations." );
		}

		$this->delete_data_for( $product->get_id() );
		$this->create_data_for( $product );
	}

	/**
	 * Create lookup table data for a given product.
	 *
	 * @param \WC_Product $product The product to create the data for.
	 */
	private function create_data_for( \WC_Product $product ) {
		if ( $this->is_variation( $product ) ) {
			$this->create_data_for_variation( $product );
		} elseif ( $this->is_variable_product( $product ) ) {
			$this->create_data_for_variable_product( $product );
		} else {
			$this->create_data_for_simple_product( $product );
		}
	}

	/**
	 * Delete all the lookup table entries for a given product,
	 * if it's a variable product information for variations is deleted too.
	 *
	 * @param int $product_id Simple product id, or main/parent product id for variable products.
	 */
	private function delete_data_for( int $product_id ) {
		global $wpdb;

		// phpcs:disable WordPress.DB.PreparedSQL.NotPrepared
		$wpdb->query(
			$wpdb->prepare(
				'DELETE FROM ' . $this->lookup_table_name . ' WHERE product_id = %d OR product_or_parent_id = %d',
				$product_id,
				$product_id
			)
		);
		// phpcs:enable WordPress.DB.PreparedSQL.NotPrepared
	}

	/**
	 * Create lookup table entries for a simple (non variable) product.
	 * Assumes that no entries exist yet.
	 *
	 * @param \WC_Product $product The product to create the entries for.
	 */
	private function create_data_for_simple_product( \WC_Product $product ) {
		$product_attributes_data = $this->get_attribute_taxonomies( $product );
		$has_stock               = $product->is_in_stock();
		$product_id              = $product->get_id();
		foreach ( $product_attributes_data as $taxonomy => $data ) {
			$term_ids = $data['term_ids'];
			foreach ( $term_ids as $term_id ) {
				$this->insert_lookup_table_data( $product_id, $product_id, $taxonomy, $term_id, false, $has_stock );
			}
		}
	}

	/**
	 * Create lookup table entries for a variable product.
	 * Assumes that no entries exist yet.
	 *
	 * @param \WC_Product_Variable $product The product to create the entries for.
	 */
	private function create_data_for_variable_product( \WC_Product_Variable $product ) {
		$product_attributes_data       = $this->get_attribute_taxonomies( $product );
		$variation_attributes_data     = array_filter(
			$product_attributes_data,
			function( $item ) {
				return $item['used_for_variations'];
			}
		);
		$non_variation_attributes_data = array_filter(
			$product_attributes_data,
			function( $item ) {
				return ! $item['used_for_variations'];
			}
		);

		$main_product_has_stock = $product->is_in_stock();
		$main_product_id        = $product->get_id();

		foreach ( $non_variation_attributes_data as $taxonomy => $data ) {
			$term_ids = $data['term_ids'];
			foreach ( $term_ids as $term_id ) {
				$this->insert_lookup_table_data( $main_product_id, $main_product_id, $taxonomy, $term_id, false, $main_product_has_stock );
			}
		}

		$term_ids_by_slug_cache = $this->get_term_ids_by_slug_cache( array_keys( $variation_attributes_data ) );
		$variations             = $this->get_variations_of( $product );

		foreach ( $variation_attributes_data as $taxonomy => $data ) {
			foreach ( $variations as $variation ) {
				$this->insert_lookup_table_data_for_variation( $variation, $taxonomy, $main_product_id, $data['term_ids'], $term_ids_by_slug_cache );
			}
		}
	}

	/**
	 * Create all the necessary lookup data for a given variation.
	 *
	 * @param \WC_Product_Variation $variation The variation to create entries for.
	 */
	private function create_data_for_variation( \WC_Product_Variation $variation ) {
		$main_product = WC()->call_function( 'wc_get_product', $variation->get_parent_id() );

		$product_attributes_data   = $this->get_attribute_taxonomies( $main_product );
		$variation_attributes_data = array_filter(
			$product_attributes_data,
			function( $item ) {
				return $item['used_for_variations'];
			}
		);

		$term_ids_by_slug_cache = $this->get_term_ids_by_slug_cache( array_keys( $variation_attributes_data ) );

		foreach ( $variation_attributes_data as $taxonomy => $data ) {
			$this->insert_lookup_table_data_for_variation( $variation, $taxonomy, $main_product->get_id(), $data['term_ids'], $term_ids_by_slug_cache );
		}
	}

	/**
	 * Create lookup table entries for a given variation, corresponding to a given taxonomy and a set of term ids.
	 *
	 * @param \WC_Product_Variation $variation The variation to create entries for.
	 * @param string                $taxonomy The taxonomy to create the entries for.
	 * @param int                   $main_product_id The parent product id.
	 * @param array                 $term_ids The term ids to create entries for.
	 * @param array                 $term_ids_by_slug_cache A dictionary of term ids by term slug, as returned by 'get_term_ids_by_slug_cache'.
	 */
	private function insert_lookup_table_data_for_variation( \WC_Product_Variation $variation, string $taxonomy, int $main_product_id, array $term_ids, array $term_ids_by_slug_cache ) {
		$variation_id                 = $variation->get_id();
		$variation_has_stock          = $variation->is_in_stock();
		$variation_definition_term_id = $this->get_variation_definition_term_id( $variation, $taxonomy, $term_ids_by_slug_cache );
		if ( $variation_definition_term_id ) {
			$this->insert_lookup_table_data( $variation_id, $main_product_id, $taxonomy, $variation_definition_term_id, true, $variation_has_stock );
		} else {
			$term_ids_for_taxonomy = $term_ids;
			foreach ( $term_ids_for_taxonomy as $term_id ) {
				$this->insert_lookup_table_data( $variation_id, $main_product_id, $taxonomy, $term_id, true, $variation_has_stock );
			}
		}
	}

	/**
	 * Get a cache of term ids by slug for a set of taxonomies, with this format:
	 *
	 * [
	 *   'taxonomy' => [
	 *     'slug_1' => id_1,
	 *     'slug_2' => id_2,
	 *     ...
	 *   ], ...
	 * ]
	 *
	 * @param array $taxonomies List of taxonomies to build the cache for.
	 * @return array A dictionary of taxonomies => dictionary of term slug => term id.
	 */
	private function get_term_ids_by_slug_cache( $taxonomies ) {
		$result = array();
		foreach ( $taxonomies as $taxonomy ) {
			$terms               = WC()->call_function(
				'get_terms',
				array(
					'taxonomy'   => wc_sanitize_taxonomy_name( $taxonomy ),
					'hide_empty' => false,
					'fields'     => 'id=>slug',
				)
			);
			$result[ $taxonomy ] = array_flip( $terms );
		}
		return $result;
	}

	/**
	 * Get the id of the term that defines a variation for a given taxonomy,
	 * or null if there's no such defining id (for variations having "Any <taxonomy>" as the definition)
	 *
	 * @param \WC_Product_Variation $variation The variation to get the defining term id for.
	 * @param string                $taxonomy The taxonomy to get the defining term id for.
	 * @param array                 $term_ids_by_slug_cache A term ids by slug as generated by get_term_ids_by_slug_cache.
	 * @return int|null The term id, or null if there's no defining id for that taxonomy in that variation.
	 */
	private function get_variation_definition_term_id( \WC_Product_Variation $variation, string $taxonomy, array $term_ids_by_slug_cache ) {
		$variation_attributes = $variation->get_attributes();
		$term_slug            = ArrayUtil::get_value_or_default( $variation_attributes, $taxonomy );
		if ( $term_slug ) {
			return $term_ids_by_slug_cache[ $taxonomy ][ $term_slug ];
		} else {
			return null;
		}
	}

	/**
	 * Get the variations of a given variable product.
	 *
	 * @param \WC_Product_Variable $product The product to get the variations for.
	 * @return array An array of WC_Product_Variation objects.
	 */
	private function get_variations_of( \WC_Product_Variable $product ) {
		$variation_ids = $product->get_children();
		return array_map(
			function( $id ) {
				return WC()->call_function( 'wc_get_product', $id );
			},
			$variation_ids
		);
	}

	/**
	 * Check if a given product is a variable product.
	 *
	 * @param \WC_Product $product The product to check.
	 * @return bool True if it's a variable product, false otherwise.
	 */
	private function is_variable_product( \WC_Product $product ) {
		return is_a( $product, \WC_Product_Variable::class );
	}

	/**
	 * Check if a given product is a variation.
	 *
	 * @param \WC_Product $product The product to check.
	 * @return bool True if it's a variation, false otherwise.
	 */
	private function is_variation( \WC_Product $product ) {
		return is_a( $product, \WC_Product_Variation::class );
	}

	/**
	 * Return the list of taxonomies used for variations on a product together with
	 * the associated term ids, with the following format:
	 *
	 * [
	 *   'taxonomy_name' =>
	 *   [
	 *     'term_ids' => [id, id, ...],
	 *     'used_for_variations' => true|false
	 *   ], ...
	 * ]
	 *
	 * @param \WC_Product $product The product to get the attribute taxonomies for.
	 * @return array Information about the attribute taxonomies of the product.
	 */
	private function get_attribute_taxonomies( \WC_Product $product ) {
		$product_attributes = $product->get_attributes();
		$result             = array();
		foreach ( $product_attributes as $taxonomy_name => $attribute_data ) {
			if ( ! $attribute_data->get_id() ) {
				// Custom product attribute, not suitable for attribute-based filtering.
				continue;
			}

			$result[ $taxonomy_name ] = array(
				'term_ids'            => $attribute_data->get_options(),
				'used_for_variations' => $attribute_data->get_variation(),
			);
		}

		return $result;
	}

	/**
	 * Insert one entry in the lookup table.
	 *
	 * @param int    $product_id The product id.
	 * @param int    $product_or_parent_id The product id for non-variable products, the main/parent product id for variations.
	 * @param string $taxonomy Taxonomy name.
	 * @param int    $term_id Term id.
	 * @param bool   $is_variation_attribute True if the taxonomy corresponds to an attribute used to define variations.
	 * @param bool   $has_stock True if the product is in stock.
	 */
	private function insert_lookup_table_data( int $product_id, int $product_or_parent_id, string $taxonomy, int $term_id, bool $is_variation_attribute, bool $has_stock ) {
		global $wpdb;

		// phpcs:disable WordPress.DB.PreparedSQL.NotPrepared
		$wpdb->query(
			$wpdb->prepare(
				'INSERT INTO ' . $this->lookup_table_name . ' (
					  product_id,
					  product_or_parent_id,
					  taxonomy,
					  term_id,
					  is_variation_attribute,
					  in_stock)
					VALUES
					  ( %d, %d, %s, %d, %d, %d )',
				$product_id,
				$product_or_parent_id,
				$taxonomy,
				$term_id,
				$is_variation_attribute ? 1 : 0,
				$has_stock ? 1 : 0
			)
		);
		// phpcs:enable WordPress.DB.PreparedSQL.NotPrepared
	}

	/**
	 * Handler for the woocommerce_rest_insert_product hook.
	 * Needed to update the lookup table when the REST API batch insert/update endpoints are used.
	 *
	 * @param \WP_Post         $product The post representing the created or updated product.
	 * @param \WP_REST_Request $request The REST request that caused the hook to be fired.
	 * @return void
	 */
	private function on_product_created_or_updated_via_rest_api( \WP_Post $product, \WP_REST_Request $request ): void {
		if ( StringUtil::ends_with( $request->get_route(), '/batch' ) ) {
			$this->on_product_changed( $product->ID );
		}
	}

	/**
	 * Tells if a lookup table regeneration is currently in progress.
	 *
	 * @return bool True if a lookup table regeneration is already in progress.
	 */
	public function regeneration_is_in_progress() {
		return get_option( 'woocommerce_attribute_lookup_regeneration_in_progress', null ) === 'yes';
	}

	/**
	 * Set a permanent flag (via option) indicating that the lookup table regeneration is in process.
	 */
	public function set_regeneration_in_progress_flag() {
		update_option( 'woocommerce_attribute_lookup_regeneration_in_progress', 'yes' );
	}

	/**
	 * Remove the flag indicating that the lookup table regeneration is in process.
	 */
	public function unset_regeneration_in_progress_flag() {
		delete_option( 'woocommerce_attribute_lookup_regeneration_in_progress' );
	}

	/**
	 * Set a flag indicating that the last lookup table regeneration process started was aborted.
	 */
	public function set_regeneration_aborted_flag() {
		update_option( 'woocommerce_attribute_lookup_regeneration_aborted', 'yes' );
	}

	/**
	 * Remove the flag indicating that the last lookup table regeneration process started was aborted.
	 */
	public function unset_regeneration_aborted_flag() {
		delete_option( 'woocommerce_attribute_lookup_regeneration_aborted' );
	}

	/**
	 * Tells if the last lookup table regeneration process started was aborted
	 * (via deleting the 'woocommerce_attribute_lookup_regeneration_in_progress' option).
	 *
	 * @return bool True if the last lookup table regeneration process was aborted.
	 */
	public function regeneration_was_aborted(): bool {
		return get_option( 'woocommerce_attribute_lookup_regeneration_aborted' ) === 'yes';
	}

	/**
	 * Check if the lookup table contains any entry at all.
	 *
	 * @return bool True if the table contains entries, false if the table is empty.
	 */
	public function lookup_table_has_data(): bool {
		global $wpdb;

		// phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		return ( (int) $wpdb->get_var( "SELECT EXISTS (SELECT 1 FROM {$this->lookup_table_name})" ) ) !== 0;
	}

	/**
	 * Handler for 'woocommerce_get_sections_products', adds the "Advanced" section to the product settings.
	 *
	 * @param array $products Original array of settings sections.
	 * @return array New array of settings sections.
	 */
	private function add_advanced_section_to_product_settings( array $products ): array {
		if ( $this->check_lookup_table_exists() ) {
			$products['advanced'] = __( 'Advanced', 'woocommerce' );
		}

		return $products;
	}

	/**
	 * Handler for 'woocommerce_get_settings_products', adds the settings related to the product attributes lookup table.
	 *
	 * @param array  $settings Original settings configuration array.
	 * @param string $section_id Settings section identifier.
	 * @return array New settings configuration array.
	 */
	private function add_product_attributes_lookup_table_settings( array $settings, string $section_id ): array {
		if ( $section_id === 'advanced' && $this->check_lookup_table_exists() ) {
			$title_item = array(
				'title' => __( 'Product attributes lookup table', 'woocommerce' ),
				'type'  => 'title',
			);

			$regeneration_is_in_progress = $this->regeneration_is_in_progress();

			if ( $regeneration_is_in_progress ) {
				$title_item['desc'] = __( 'These settings are not available while the lookup table regeneration is in progress.', 'woocommerce' );
			}

			$settings[] = $title_item;

			if ( ! $regeneration_is_in_progress ) {
				$regeneration_aborted_warning =
					$this->regeneration_was_aborted() ?
						sprintf(
							"<p><strong style='color: #E00000'>%s</strong></p><p>%s</p>",
							__( 'WARNING: The product attributes lookup table regeneration process was aborted.', 'woocommerce' ),
							__( 'This means that the table is probably in an inconsistent state. It\'s recommended to run a new regeneration process or to resume the aborted process (Status - Tools - Regenerate the product attributes lookup table/Resume the product attributes lookup table regeneration) before enabling the table usage.', 'woocommerce' )
						) : null;

				$settings[] = array(
					'title'         => __( 'Enable table usage', 'woocommerce' ),
					'desc'          => __( 'Use the product attributes lookup table for catalog filtering.', 'woocommerce' ),
					'desc_tip'      => $regeneration_aborted_warning,
					'id'            => 'woocommerce_attribute_lookup_enabled',
					'default'       => 'no',
					'type'          => 'checkbox',
					'checkboxgroup' => 'start',
				);

				$settings[] = array(
					'title'         => __( 'Direct updates', 'woocommerce' ),
					'desc'          => __( 'Update the table directly upon product changes, instead of scheduling a deferred update.', 'woocommerce' ),
					'id'            => 'woocommerce_attribute_lookup_direct_updates',
					'default'       => 'no',
					'type'          => 'checkbox',
					'checkboxgroup' => 'start',
				);
			}

			$settings[] = array( 'type' => 'sectionend' );
		}

		return $settings;
	}
}
