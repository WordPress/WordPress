<?php
namespace Automattic\WooCommerce\Blocks\BlockTypes;

use Automattic\WooCommerce\Blocks\Utils\BlocksWpQuery;
use Automattic\WooCommerce\StoreApi\SchemaController;
use Automattic\WooCommerce\StoreApi\StoreApi;

/**
 * AbstractProductGrid class.
 */
abstract class AbstractProductGrid extends AbstractDynamicBlock {

	/**
	 * Attributes.
	 *
	 * @var array
	 */
	protected $attributes = array();

	/**
	 * InnerBlocks content.
	 *
	 * @var string
	 */
	protected $content = '';

	/**
	 * Query args.
	 *
	 * @var array
	 */
	protected $query_args = array();

	/**
	 * Meta query args.
	 *
	 * @var array
	 */
	protected $meta_query = array();

	/**
	 * Get a set of attributes shared across most of the grid blocks.
	 *
	 * @return array List of block attributes with type and defaults.
	 */
	protected function get_block_type_attributes() {
		return array(
			'className'         => $this->get_schema_string(),
			'columns'           => $this->get_schema_number( wc_get_theme_support( 'product_blocks::default_columns', 3 ) ),
			'rows'              => $this->get_schema_number( wc_get_theme_support( 'product_blocks::default_rows', 3 ) ),
			'categories'        => $this->get_schema_list_ids(),
			'catOperator'       => array(
				'type'    => 'string',
				'default' => 'any',
			),
			'contentVisibility' => $this->get_schema_content_visibility(),
			'align'             => $this->get_schema_align(),
			'alignButtons'      => $this->get_schema_boolean( false ),
			'isPreview'         => $this->get_schema_boolean( false ),
			'stockStatus'       => array(
				'type'    => 'array',
				'default' => array_keys( wc_get_product_stock_status_options() ),
			),
		);
	}

	/**
	 * Include and render the dynamic block.
	 *
	 * @param array         $attributes Block attributes. Default empty array.
	 * @param string        $content    Block content. Default empty string.
	 * @param WP_Block|null $block      Block instance.
	 * @return string Rendered block type output.
	 */
	protected function render( $attributes = array(), $content = '', $block = null ) {
		$this->attributes = $this->parse_attributes( $attributes );
		$this->content    = $content;
		$this->query_args = $this->parse_query_args();
		$products         = array_filter( array_map( 'wc_get_product', $this->get_products() ) );

		if ( ! $products ) {
			return '';
		}

		/**
		 * Override product description to prevent infinite loop.
		 *
		 * @see https://github.com/woocommerce/woocommerce-blocks/pull/6849
		 */
		foreach ( $products as $product ) {
			$product->set_description( '' );
		}

		/**
		 * Product List Render event.
		 *
		 * Fires a WP Hook named `experimental__woocommerce_blocks-product-list-render` on render so that the client
		 * can add event handling when certain products are displayed. This can be used by tracking extensions such
		 * as Google Analytics to track impressions.
		 *
		 * Provides the list of product data (shaped like the Store API responses) and the block name.
		 */
		$this->asset_api->add_inline_script(
			'wp-hooks',
			'
			window.addEventListener( "DOMContentLoaded", () => {
				wp.hooks.doAction(
					"experimental__woocommerce_blocks-product-list-render",
					{
						products: JSON.parse( decodeURIComponent( "' . esc_js(
				rawurlencode(
					wp_json_encode(
						array_map(
							[ StoreApi::container()->get( SchemaController::class )->get( 'product' ), 'get_item_response' ],
							$products
						)
					)
				)
			) . '" ) ),
						listName: "' . esc_js( $this->block_name ) . '"
					}
				);
			} );
			',
			'after'
		);

		return sprintf(
			'<div class="%s"><ul class="wc-block-grid__products">%s</ul></div>',
			esc_attr( $this->get_container_classes() ),
			implode( '', array_map( array( $this, 'render_product' ), $products ) )
		);
	}

	/**
	 * Get the schema for the contentVisibility attribute
	 *
	 * @return array List of block attributes with type and defaults.
	 */
	protected function get_schema_content_visibility() {
		return array(
			'type'       => 'object',
			'properties' => array(
				'image'  => $this->get_schema_boolean( true ),
				'title'  => $this->get_schema_boolean( true ),
				'price'  => $this->get_schema_boolean( true ),
				'rating' => $this->get_schema_boolean( true ),
				'button' => $this->get_schema_boolean( true ),
			),
		);
	}

	/**
	 * Get the schema for the orderby attribute.
	 *
	 * @return array Property definition of `orderby` attribute.
	 */
	protected function get_schema_orderby() {
		return array(
			'type'    => 'string',
			'enum'    => array( 'date', 'popularity', 'price_asc', 'price_desc', 'rating', 'title', 'menu_order' ),
			'default' => 'date',
		);
	}

	/**
	 * Get the block's attributes.
	 *
	 * @param array $attributes Block attributes. Default empty array.
	 * @return array  Block attributes merged with defaults.
	 */
	protected function parse_attributes( $attributes ) {
		// These should match what's set in JS `registerBlockType`.
		$defaults = array(
			'columns'           => wc_get_theme_support( 'product_blocks::default_columns', 3 ),
			'rows'              => wc_get_theme_support( 'product_blocks::default_rows', 3 ),
			'alignButtons'      => false,
			'categories'        => array(),
			'catOperator'       => 'any',
			'contentVisibility' => array(
				'image'  => true,
				'title'  => true,
				'price'  => true,
				'rating' => true,
				'button' => true,
			),
			'stockStatus'       => array_keys( wc_get_product_stock_status_options() ),
		);

		return wp_parse_args( $attributes, $defaults );
	}

	/**
	 * Parse query args.
	 *
	 * @return array
	 */
	protected function parse_query_args() {
		// Store the original meta query.
		$this->meta_query = WC()->query->get_meta_query();

		$query_args = array(
			'post_type'           => 'product',
			'post_status'         => 'publish',
			'fields'              => 'ids',
			'ignore_sticky_posts' => true,
			'no_found_rows'       => false,
			'orderby'             => '',
			'order'               => '',
			'meta_query'          => $this->meta_query, // phpcs:ignore WordPress.DB.SlowDBQuery
			'tax_query'           => array(), // phpcs:ignore WordPress.DB.SlowDBQuery
			'posts_per_page'      => $this->get_products_limit(),
		);

		$this->set_block_query_args( $query_args );
		$this->set_ordering_query_args( $query_args );
		$this->set_categories_query_args( $query_args );
		$this->set_visibility_query_args( $query_args );
		$this->set_stock_status_query_args( $query_args );

		return $query_args;
	}

	/**
	 * Parse query args.
	 *
	 * @param array $query_args Query args.
	 */
	protected function set_ordering_query_args( &$query_args ) {
		if ( isset( $this->attributes['orderby'] ) ) {
			if ( 'price_desc' === $this->attributes['orderby'] ) {
				$query_args['orderby'] = 'price';
				$query_args['order']   = 'DESC';
			} elseif ( 'price_asc' === $this->attributes['orderby'] ) {
				$query_args['orderby'] = 'price';
				$query_args['order']   = 'ASC';
			} elseif ( 'date' === $this->attributes['orderby'] ) {
				$query_args['orderby'] = 'date';
				$query_args['order']   = 'DESC';
			} else {
				$query_args['orderby'] = $this->attributes['orderby'];
			}
		}

		$query_args = array_merge(
			$query_args,
			WC()->query->get_catalog_ordering_args( $query_args['orderby'], $query_args['order'] )
		);
	}

	/**
	 * Set args specific to this block
	 *
	 * @param array $query_args Query args.
	 */
	abstract protected function set_block_query_args( &$query_args );

	/**
	 * Set categories query args.
	 *
	 * @param array $query_args Query args.
	 */
	protected function set_categories_query_args( &$query_args ) {
		if ( ! empty( $this->attributes['categories'] ) ) {
			$categories = array_map( 'absint', $this->attributes['categories'] );

			$query_args['tax_query'][] = array(
				'taxonomy'         => 'product_cat',
				'terms'            => $categories,
				'field'            => 'term_id',
				'operator'         => 'all' === $this->attributes['catOperator'] ? 'AND' : 'IN',

				/*
				 * When cat_operator is AND, the children categories should be excluded,
				 * as only products belonging to all the children categories would be selected.
				 */
				'include_children' => 'all' === $this->attributes['catOperator'] ? false : true,
			);
		}
	}

	/**
	 * Set visibility query args.
	 *
	 * @param array $query_args Query args.
	 */
	protected function set_visibility_query_args( &$query_args ) {
		$product_visibility_terms  = wc_get_product_visibility_term_ids();
		$product_visibility_not_in = array( $product_visibility_terms['exclude-from-catalog'] );

		if ( 'yes' === get_option( 'woocommerce_hide_out_of_stock_items' ) ) {
			$product_visibility_not_in[] = $product_visibility_terms['outofstock'];
		}

		$query_args['tax_query'][] = array(
			'taxonomy' => 'product_visibility',
			'field'    => 'term_taxonomy_id',
			'terms'    => $product_visibility_not_in,
			'operator' => 'NOT IN',
		);
	}

	/**
	 * Set which stock status to use when displaying products.
	 *
	 * @param array $query_args Query args.
	 * @return void
	 */
	protected function set_stock_status_query_args( &$query_args ) {
		$stock_statuses = array_keys( wc_get_product_stock_status_options() );

		// phpcs:disable WordPress.DB.SlowDBQuery.slow_db_query_meta_query
		if ( isset( $this->attributes['stockStatus'] ) && $stock_statuses !== $this->attributes['stockStatus'] ) {
			// Reset meta_query then update with our stock status.
			$query_args['meta_query']   = $this->meta_query;
			$query_args['meta_query'][] = array(
				'key'     => '_stock_status',
				'value'   => array_merge( [ '' ], $this->attributes['stockStatus'] ),
				'compare' => 'IN',
			);
		} else {
			$query_args['meta_query'] = $this->meta_query;
		}
		// phpcs:enable WordPress.DB.SlowDBQuery.slow_db_query_meta_query
	}

	/**
	 * Works out the item limit based on rows and columns, or returns default.
	 *
	 * @return int
	 */
	protected function get_products_limit() {
		if ( isset( $this->attributes['rows'], $this->attributes['columns'] ) && ! empty( $this->attributes['rows'] ) ) {
			$this->attributes['limit'] = intval( $this->attributes['columns'] ) * intval( $this->attributes['rows'] );
		}
		return intval( $this->attributes['limit'] );
	}

	/**
	 * Run the query and return an array of product IDs
	 *
	 * @return array List of product IDs
	 */
	protected function get_products() {
		/**
		 * Filters whether or not the product grid is cacheable.
		 *
		 * @param boolean $is_cacheable The list of script dependencies.
		 * @param array $query_args Query args for the products query passed to BlocksWpQuery.
		 * @return array True to enable cache, false to disable cache.
		 *
		 * @since 2.5.0
		 */
		$is_cacheable      = (bool) apply_filters( 'woocommerce_blocks_product_grid_is_cacheable', true, $this->query_args );
		$transient_version = \WC_Cache_Helper::get_transient_version( 'product_query' );

		$query   = new BlocksWpQuery( $this->query_args );
		$results = wp_parse_id_list( $is_cacheable ? $query->get_cached_posts( $transient_version ) : $query->get_posts() );

		// Remove ordering query arguments which may have been added by get_catalog_ordering_args.
		WC()->query->remove_ordering_args();

		// Prime caches to reduce future queries. Note _prime_post_caches is private--we could replace this with our own
		// query if it becomes unavailable.
		if ( is_callable( '_prime_post_caches' ) ) {
			_prime_post_caches( $results );
		}

		$this->prime_product_variations( $results );

		return $results;
	}

	/**
	 * Retrieve IDs that are not already present in the cache.
	 *
	 * Based on WordPress function: _get_non_cached_ids
	 *
	 * @param int[]  $product_ids Array of IDs.
	 * @param string $cache_key  The cache bucket to check against.
	 * @return int[] Array of IDs not present in the cache.
	 */
	protected function get_non_cached_ids( $product_ids, $cache_key ) {
		$non_cached_ids = array();
		$cache_values   = wp_cache_get_multiple( $product_ids, $cache_key );

		foreach ( $cache_values as $id => $value ) {
			if ( ! $value ) {
				$non_cached_ids[] = (int) $id;
			}
		}

		return $non_cached_ids;
	}

	/**
	 * Prime query cache of product variation meta data.
	 *
	 * Prepares values in the product_ID_variation_meta_data cache for later use in the ProductSchema::get_variations()
	 * method. Doing so here reduces the total number of queries needed.
	 *
	 * @param int[] $product_ids Product ids to prime variation cache for.
	 */
	protected function prime_product_variations( $product_ids ) {
		$cache_group       = 'product_variation_meta_data';
		$prime_product_ids = $this->get_non_cached_ids( wp_parse_id_list( $product_ids ), $cache_group );

		if ( ! $prime_product_ids ) {
			return;
		}

		global $wpdb;

		// phpcs:disable WordPress.DB.PreparedSQL.NotPrepared
		$product_variations      = $wpdb->get_results( "SELECT ID as variation_id, post_parent as product_id from {$wpdb->posts} WHERE post_parent IN ( " . implode( ',', $prime_product_ids ) . ' )', ARRAY_A );
		$prime_variation_ids     = array_column( $product_variations, 'variation_id' );
		$variation_ids_by_parent = array_column( $product_variations, 'product_id', 'variation_id' );

		if ( empty( $prime_variation_ids ) ) {
			return;
		}

		$all_variation_meta_data = $wpdb->get_results(
			$wpdb->prepare(
				"SELECT post_id as variation_id, meta_key as attribute_key, meta_value as attribute_value FROM {$wpdb->postmeta} WHERE post_id IN (" . implode( ',', array_map( 'esc_sql', $prime_variation_ids ) ) . ') AND meta_key LIKE %s',
				$wpdb->esc_like( 'attribute_' ) . '%'
			)
		);
		// phpcs:enable
		// Prepare the data to cache by indexing by the parent product.
		$primed_data = array_reduce(
			$all_variation_meta_data,
			function( $values, $data ) use ( $variation_ids_by_parent ) {
				$values[ $variation_ids_by_parent[ $data->variation_id ] ?? 0 ][] = $data;
				return $values;
			},
			array_fill_keys( $prime_product_ids, [] )
		);

		// Cache everything.
		foreach ( $primed_data as $product_id => $variation_meta_data ) {
			wp_cache_set(
				$product_id,
				[
					'last_modified' => get_the_modified_date( 'U', $product_id ),
					'data'          => $variation_meta_data,
				],
				$cache_group
			);
		}
	}

	/**
	 * Get the list of classes to apply to this block.
	 *
	 * @return string space-separated list of classes.
	 */
	protected function get_container_classes() {
		$classes = array(
			'wc-block-grid',
			"wp-block-{$this->block_name}",
			"wc-block-{$this->block_name}",
			"has-{$this->attributes['columns']}-columns",
		);

		if ( $this->attributes['rows'] > 1 ) {
			$classes[] = 'has-multiple-rows';
		}

		if ( isset( $this->attributes['align'] ) ) {
			$classes[] = "align{$this->attributes['align']}";
		}

		if ( ! empty( $this->attributes['alignButtons'] ) ) {
			$classes[] = 'has-aligned-buttons';
		}

		if ( ! empty( $this->attributes['className'] ) ) {
			$classes[] = $this->attributes['className'];
		}

		return implode( ' ', $classes );
	}

	/**
	 * Render a single products.
	 *
	 * @param \WC_Product $product Product object.
	 * @return string Rendered product output.
	 */
	protected function render_product( $product ) {
		$data = (object) array(
			'permalink' => esc_url( $product->get_permalink() ),
			'image'     => $this->get_image_html( $product ),
			'title'     => $this->get_title_html( $product ),
			'rating'    => $this->get_rating_html( $product ),
			'price'     => $this->get_price_html( $product ),
			'badge'     => $this->get_sale_badge_html( $product ),
			'button'    => $this->get_button_html( $product ),
		);

		/**
		 * Filters the HTML for products in the grid.
		 *
		 * @param string $html Product grid item HTML.
		 * @param array $data Product data passed to the template.
		 * @param \WC_Product $product Product object.
		 * @return string Updated product grid item HTML.
		 *
		 * @since 2.2.0
		 */
		return apply_filters(
			'woocommerce_blocks_product_grid_item_html',
			"<li class=\"wc-block-grid__product\">
				<a href=\"{$data->permalink}\" class=\"wc-block-grid__product-link\">
					{$data->image}
					{$data->title}
				</a>
				{$data->badge}
				{$data->price}
				{$data->rating}
				{$data->button}
			</li>",
			$data,
			$product
		);
	}

	/**
	 * Get the product image.
	 *
	 * @param \WC_Product $product Product.
	 * @return string
	 */
	protected function get_image_html( $product ) {
		if ( array_key_exists( 'image', $this->attributes['contentVisibility'] ) && false === $this->attributes['contentVisibility']['image'] ) {
			return '';
		}

		$attr = array(
			'alt' => '',
		);

		if ( $product->get_image_id() ) {
			$image_alt = get_post_meta( $product->get_image_id(), '_wp_attachment_image_alt', true );
			$attr      = array(
				'alt' => ( $image_alt ? $image_alt : $product->get_name() ),
			);
		}

		return '<div class="wc-block-grid__product-image">' . $product->get_image( 'woocommerce_thumbnail', $attr ) . '</div>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}

	/**
	 * Get the product title.
	 *
	 * @param \WC_Product $product Product.
	 * @return string
	 */
	protected function get_title_html( $product ) {
		if ( empty( $this->attributes['contentVisibility']['title'] ) ) {
			return '';
		}

		return '<div class="wc-block-grid__product-title">' . wp_kses_post( $product->get_title() ) . '</div>';
	}

	/**
	 * Render the rating icons.
	 *
	 * @param WC_Product $product Product.
	 * @return string Rendered product output.
	 */
	protected function get_rating_html( $product ) {
		if ( empty( $this->attributes['contentVisibility']['rating'] ) ) {
			return '';
		}
		$rating_count = $product->get_rating_count();
		$average      = $product->get_average_rating();

		if ( $rating_count > 0 ) {
			return sprintf(
				'<div class="wc-block-grid__product-rating">%s</div>',
				wc_get_rating_html( $average, $rating_count ) // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			);
		}
		return '';
	}

	/**
	 * Get the price.
	 *
	 * @param \WC_Product $product Product.
	 * @return string Rendered product output.
	 */
	protected function get_price_html( $product ) {
		if ( empty( $this->attributes['contentVisibility']['price'] ) ) {
			return '';
		}
		return sprintf(
			'<div class="wc-block-grid__product-price price">%s</div>',
			wp_kses_post( $product->get_price_html() )
		);
	}

	/**
	 * Get the sale badge.
	 *
	 * @param \WC_Product $product Product.
	 * @return string Rendered product output.
	 */
	protected function get_sale_badge_html( $product ) {
		if ( empty( $this->attributes['contentVisibility']['price'] ) ) {
			return '';
		}

		if ( ! $product->is_on_sale() ) {
			return;
		}

		return '<div class="wc-block-grid__product-onsale">
			<span aria-hidden="true">' . esc_html__( 'Sale', 'woocommerce' ) . '</span>
			<span class="screen-reader-text">' . esc_html__( 'Product on sale', 'woocommerce' ) . '</span>
		</div>';
	}

	/**
	 * Get the button.
	 *
	 * @param \WC_Product $product Product.
	 * @return string Rendered product output.
	 */
	protected function get_button_html( $product ) {
		if ( empty( $this->attributes['contentVisibility']['button'] ) ) {
			return '';
		}
		return '<div class="wp-block-button wc-block-grid__product-add-to-cart">' . $this->get_add_to_cart( $product ) . '</div>';
	}

	/**
	 * Get the "add to cart" button.
	 *
	 * @param \WC_Product $product Product.
	 * @return string Rendered product output.
	 */
	protected function get_add_to_cart( $product ) {
		$attributes = array(
			'aria-label'       => $product->add_to_cart_description(),
			'data-quantity'    => '1',
			'data-product_id'  => $product->get_id(),
			'data-product_sku' => $product->get_sku(),
			'rel'              => 'nofollow',
			'class'            => 'wp-block-button__link ' . ( function_exists( 'wc_wp_theme_get_element_class_name' ) ? wc_wp_theme_get_element_class_name( 'button' ) : '' ) . ' add_to_cart_button',
		);

		if (
			$product->supports( 'ajax_add_to_cart' ) &&
			$product->is_purchasable() &&
			( $product->is_in_stock() || $product->backorders_allowed() )
		) {
			$attributes['class'] .= ' ajax_add_to_cart';
		}

		return sprintf(
			'<a href="%s" %s>%s</a>',
			esc_url( $product->add_to_cart_url() ),
			wc_implode_html_attributes( $attributes ),
			esc_html( $product->add_to_cart_text() )
		);
	}

	/**
	 * Extra data passed through from server to client for block.
	 *
	 * @param array $attributes  Any attributes that currently are available from the block.
	 *                           Note, this will be empty in the editor context when the block is
	 *                           not in the post content on editor load.
	 */
	protected function enqueue_data( array $attributes = [] ) {
		parent::enqueue_data( $attributes );
		$this->asset_data_registry->add( 'min_columns', wc_get_theme_support( 'product_blocks::min_columns', 1 ), true );
		$this->asset_data_registry->add( 'max_columns', wc_get_theme_support( 'product_blocks::max_columns', 6 ), true );
		$this->asset_data_registry->add( 'default_columns', wc_get_theme_support( 'product_blocks::default_columns', 3 ), true );
		$this->asset_data_registry->add( 'min_rows', wc_get_theme_support( 'product_blocks::min_rows', 1 ), true );
		$this->asset_data_registry->add( 'max_rows', wc_get_theme_support( 'product_blocks::max_rows', 6 ), true );
		$this->asset_data_registry->add( 'default_rows', wc_get_theme_support( 'product_blocks::default_rows', 3 ), true );
		$this->asset_data_registry->add( 'stock_status_options', wc_get_product_stock_status_options(), true );
	}
}
