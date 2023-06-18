<?php
namespace Automattic\WooCommerce\Blocks\BlockTypes;

use WP_Query;

// phpcs:disable WordPress.DB.SlowDBQuery.slow_db_query_tax_query
// phpcs:disable WordPress.DB.SlowDBQuery.slow_db_query_meta_query
// phpcs:disable WordPress.DB.SlowDBQuery.slow_db_query_meta_key

/**
 * ProductQuery class.
 */
class ProductQuery extends AbstractBlock {
	/**
	 * Block name.
	 *
	 * @var string
	 */
	protected $block_name = 'product-query';

	/**
	 * The Block with its attributes before it gets rendered
	 *
	 * @var array
	 */
	protected $parsed_block;

	/**
	 * Orderby options not natively supported by WordPress REST API
	 *
	 * @var array
	 */
	protected $custom_order_opts = array( 'popularity', 'rating' );

	/**
	 * All the query args related to the filter by attributes block.
	 *
	 * @var array
	 */
	protected $attributes_filter_query_args = array();

	/** This is a feature flag to enable the custom inherit Global Query implementation.
	 * This is not intended to be a permanent feature flag, but rather a temporary.
	 * It is also necessary to enable this feature flag on the PHP side: `assets/js/blocks/product-query/utils.tsx:83`.
	 * https://github.com/woocommerce/woocommerce-blocks/pull/7382
	 *
	 * @var boolean
	 */
	protected $is_custom_inherit_global_query_implementation_enabled = false;

	/**
	 * All query args from WP_Query.
	 *
	 * @var array
	 */
	protected $valid_query_vars;

	/**
	 * Initialize this block type.
	 *
	 * - Hook into WP lifecycle.
	 * - Register the block with WordPress.
	 * - Hook into pre_render_block to update the query.
	 */
	protected function initialize() {
		add_filter( 'query_vars', array( $this, 'set_query_vars' ) );
		parent::initialize();
		add_filter(
			'pre_render_block',
			array( $this, 'update_query' ),
			10,
			2
		);
		add_filter( 'rest_product_query', array( $this, 'update_rest_query' ), 10, 2 );
		add_filter( 'rest_product_collection_params', array( $this, 'extend_rest_query_allowed_params' ), 10, 1 );
	}

	/**
	 * Check if a given block
	 *
	 * @param array $parsed_block The block being rendered.
	 * @return boolean
	 */
	public static function is_woocommerce_variation( $parsed_block ) {
		return isset( $parsed_block['attrs']['namespace'] )
		&& substr( $parsed_block['attrs']['namespace'], 0, 11 ) === 'woocommerce';
	}

	/**
	 * Update the query for the product query block.
	 *
	 * @param string|null $pre_render   The pre-rendered content. Default null.
	 * @param array       $parsed_block The block being rendered.
	 */
	public function update_query( $pre_render, $parsed_block ) {
		if ( 'core/query' !== $parsed_block['blockName'] ) {
			return;
		}

		$this->parsed_block = $parsed_block;

		if ( self::is_woocommerce_variation( $parsed_block ) ) {
			// Set this so that our product filters can detect if it's a PHP template.
			$this->asset_data_registry->add( 'has_filterable_products', true, true );
			$this->asset_data_registry->add( 'is_rendering_php_template', true, true );
			$this->asset_data_registry->add( 'product_ids', $this->get_products_ids_by_attributes( $parsed_block ), true );
			add_filter(
				'query_loop_block_query_vars',
				array( $this, 'build_query' ),
				10,
				1
			);
		}
	}

	/**
	 * Merge tax_queries from various queries.
	 *
	 * @param array ...$queries Query arrays to be merged.
	 * @return array
	 */
	private function merge_tax_queries( ...$queries ) {
		$tax_query = [];
		foreach ( $queries as $query ) {
			if ( ! empty( $query['tax_query'] ) ) {
				$tax_query = array_merge( $tax_query, $query['tax_query'] );
			}
		}
		return [ 'tax_query' => $tax_query ];
	}

	/**
	 * Update the query for the product query block in Editor.
	 *
	 * @param array           $args    Query args.
	 * @param WP_REST_Request $request Request.
	 */
	public function update_rest_query( $args, $request ): array {
		$woo_attributes      = $request->get_param( '__woocommerceAttributes' );
		$is_valid_attributes = is_array( $woo_attributes );
		$orderby             = $request->get_param( 'orderby' );
		$woo_stock_status    = $request->get_param( '__woocommerceStockStatus' );
		$on_sale             = $request->get_param( '__woocommerceOnSale' ) === 'true';

		$on_sale_query    = $on_sale ? $this->get_on_sale_products_query() : [];
		$orderby_query    = $orderby ? $this->get_custom_orderby_query( $orderby ) : [];
		$attributes_query = $is_valid_attributes ? $this->get_product_attributes_query( $woo_attributes ) : [];
		$stock_query      = is_array( $woo_stock_status ) ? $this->get_stock_status_query( $woo_stock_status ) : [];
		$visibility_query = is_array( $woo_stock_status ) ? $this->get_product_visibility_query( $stock_query ) : [];
		$tax_query        = $is_valid_attributes ? $this->merge_tax_queries( $attributes_query, $visibility_query ) : [];

		return array_merge( $args, $on_sale_query, $orderby_query, $stock_query, $tax_query );
	}

	/**
	 * Return a custom query based on attributes, filters and global WP_Query.
	 *
	 * @param WP_Query $query The WordPress Query.
	 * @return array
	 */
	public function build_query( $query ) {
		$parsed_block = $this->parsed_block;
		if ( ! $this->is_woocommerce_variation( $parsed_block ) ) {
			return $query;
		}

		$common_query_values = array(
			'meta_query'     => array(),
			'posts_per_page' => $query['posts_per_page'],
			'orderby'        => $query['orderby'],
			'order'          => $query['order'],
			'offset'         => $query['offset'],
			'post__in'       => array(),
			'post_status'    => 'publish',
			'post_type'      => 'product',
			'tax_query'      => array(),
		);

		$handpicked_products = isset( $parsed_block['attrs']['query']['include'] ) ?
			$parsed_block['attrs']['query']['include'] : $common_query_values['post__in'];

		$merged_query = $this->merge_queries(
			$common_query_values,
			$this->get_global_query( $parsed_block ),
			$this->get_custom_orderby_query( $query['orderby'] ),
			$this->get_queries_by_custom_attributes( $parsed_block ),
			$this->get_queries_by_applied_filters(),
			$this->get_filter_by_taxonomies_query( $query ),
			$this->get_filter_by_keyword_query( $query )
		);

		return $this->filter_query_to_only_include_ids( $merged_query, $handpicked_products );
	}

	/**
	 * Return the product ids based on the attributes and global query.
	 * This is used to allow the filter blocks to render data that matches with variations. More details here: https://github.com/woocommerce/woocommerce-blocks/issues/7245
	 *
	 * @param array $parsed_block The block being rendered.
	 * @return array
	 */
	private function get_products_ids_by_attributes( $parsed_block ) {
		$query = $this->merge_queries(
			array(
				'post_type'      => 'product',
				'post__in'       => array(),
				'post_status'    => 'publish',
				'posts_per_page' => -1,
				'meta_query'     => array(),
				'tax_query'      => array(),
			),
			$this->get_queries_by_custom_attributes( $parsed_block ),
			$this->get_global_query( $parsed_block )
		);

		$products = new \WP_Query( $query );
		$post_ids = wp_list_pluck( $products->posts, 'ID' );

		return $post_ids;
	}

	/**
	 * Merge in the first parameter the keys "post_in", "meta_query" and "tax_query" of the second parameter.
	 *
	 * @param array[] ...$queries Query arrays to be merged.
	 * @return array
	 */
	private function merge_queries( ...$queries ) {
		$merged_query = array_reduce(
			$queries,
			function( $acc, $query ) {
				if ( ! is_array( $query ) ) {
					return $acc;
				}
				// If the $query doesn't contain any valid query keys, we unpack/spread it then merge.
				if ( empty( array_intersect( $this->get_valid_query_vars(), array_keys( $query ) ) ) ) {
					return $this->merge_queries( $acc, ...array_values( $query ) );
				}
				return $this->array_merge_recursive_replace_non_array_properties( $acc, $query );
			},
			array()
		);

		/**
		 * If there are duplicated items in post__in, it means that we need to
		 * use the intersection of the results, which in this case, are the
		 * duplicated items.
		 */
		if (
			! empty( $merged_query['post__in'] ) &&
			count( $merged_query['post__in'] ) > count( array_unique( $merged_query['post__in'] ) )
		) {
			$merged_query['post__in'] = array_unique(
				array_diff(
					$merged_query['post__in'],
					array_unique( $merged_query['post__in'] )
				)
			);
		}

		return $merged_query;
	}

	/**
	 * Extends allowed `collection_params` for the REST API
	 *
	 * By itself, the REST API doesn't accept custom `orderby` values,
	 * even if they are supported by a custom post type.
	 *
	 * @param array $params  A list of allowed `orderby` values.
	 *
	 * @return array
	 */
	public function extend_rest_query_allowed_params( $params ) {
		$original_enum = isset( $params['orderby']['enum'] ) ? $params['orderby']['enum'] : array();

		$params['orderby']['enum'] = array_merge( $original_enum, $this->custom_order_opts );
		return $params;
	}

	/**
	 * Return a query for on sale products.
	 *
	 * @return array
	 */
	private function get_on_sale_products_query() {
		return array(
			'post__in' => wc_get_product_ids_on_sale(),
		);
	}

	/**
	 * Return query params to support custom sort values
	 *
	 * @param string $orderby  Sort order option.
	 *
	 * @return array
	 */
	private function get_custom_orderby_query( $orderby ) {
		if ( ! in_array( $orderby, $this->custom_order_opts, true ) ) {
			return array( 'orderby' => $orderby );
		}

		$meta_keys = array(
			'popularity' => 'total_sales',
			'rating'     => '_wc_average_rating',
		);

		return array(
			'meta_key' => $meta_keys[ $orderby ],
			'orderby'  => 'meta_value_num',
		);
	}

	/**
	 * Apply the query only to a subset of products
	 *
	 * @param array $query  The query.
	 * @param array $ids  Array of selected product ids.
	 *
	 * @return array
	 */
	private function filter_query_to_only_include_ids( $query, $ids ) {
		if ( ! empty( $ids ) ) {
			$query['post__in'] = empty( $query['post__in'] ) ?
				$ids : array_intersect( $ids, $query['post__in'] );
		}

		return $query;
	}

	/**
	 * Return the `tax_query` for the requested attributes
	 *
	 * @param array $attributes  Attributes and their terms.
	 *
	 * @return array
	 */
	private function get_product_attributes_query( $attributes = array() ) {
		$grouped_attributes = array_reduce(
			$attributes,
			function ( $carry, $item ) {
				$taxonomy = sanitize_title( $item['taxonomy'] );

				if ( ! key_exists( $taxonomy, $carry ) ) {
					$carry[ $taxonomy ] = array(
						'field'    => 'term_id',
						'operator' => 'IN',
						'taxonomy' => $taxonomy,
						'terms'    => array( $item['termId'] ),
					);
				} else {
					$carry[ $taxonomy ]['terms'][] = $item['termId'];
				}

				return $carry;
			},
			array()
		);

		return array(
			'tax_query' => array_values( $grouped_attributes ),
		);
	}

	/**
	 * Return a query for products depending on their stock status.
	 *
	 * @param array $stock_statii An array of acceptable stock statii.
	 * @return array
	 */
	private function get_stock_status_query( $stock_statii ) {
		if ( ! is_array( $stock_statii ) ) {
			return array();
		}

		$stock_status_options = array_keys( wc_get_product_stock_status_options() );

		/**
		 * If all available stock status are selected, we don't need to add the
		 * meta query for stock status.
		 */
		if (
			count( $stock_statii ) === count( $stock_status_options ) &&
			array_diff( $stock_statii, $stock_status_options ) === array_diff( $stock_status_options, $stock_statii )
		) {
			return array();
		}

		/**
		 * If all stock statuses are selected except 'outofstock', we use the
		 * product visibility query to filter out out of stock products.
		 *
		 * @see get_product_visibility_query()
		 */
		$diff = array_diff( $stock_status_options, $stock_statii );
		if ( count( $diff ) === 1 && in_array( 'outofstock', $diff, true ) ) {
			return array();
		}

		return array(
			'meta_query' => array(
				array(
					'key'     => '_stock_status',
					'value'   => (array) $stock_statii,
					'compare' => 'IN',
				),
			),
		);
	}

	/**
	 * Return a query for product visibility depending on their stock status.
	 *
	 * @param array $stock_query Stock status query.
	 *
	 * @return array Tax query for product visibility.
	 */
	private function get_product_visibility_query( $stock_query ) {
		$product_visibility_terms  = wc_get_product_visibility_term_ids();
		$product_visibility_not_in = array( is_search() ? $product_visibility_terms['exclude-from-search'] : $product_visibility_terms['exclude-from-catalog'] );

		// Hide out of stock products.
		if ( empty( $stock_query ) && 'yes' === get_option( 'woocommerce_hide_out_of_stock_items' ) ) {
			$product_visibility_not_in[] = $product_visibility_terms['outofstock'];
		}

		return array(
			'tax_query' => array(
				array(
					'taxonomy' => 'product_visibility',
					'field'    => 'term_taxonomy_id',
					'terms'    => $product_visibility_not_in,
					'operator' => 'NOT IN',
				),
			),
		);
	}

	/**
	 * Set the query vars that are used by filter blocks.
	 *
	 * @return array
	 */
	private function get_query_vars_from_filter_blocks() {
		$attributes_filter_query_args = array_reduce(
			array_values( $this->get_filter_by_attributes_query_vars() ),
			function( $acc, $array ) {
				return array_merge( array_values( $array ), $acc );
			},
			array()
		);

		return array(
			'price_filter_query_args'      => array( PriceFilter::MIN_PRICE_QUERY_VAR, PriceFilter::MAX_PRICE_QUERY_VAR ),
			'stock_filter_query_args'      => array( StockFilter::STOCK_STATUS_QUERY_VAR ),
			'attributes_filter_query_args' => $attributes_filter_query_args,
			'rating_filter_query_args'     => array( RatingFilter::RATING_QUERY_VAR ),
		);

	}

	/**
	 * Set the query vars that are used by filter blocks.
	 *
	 * @param array $public_query_vars Public query vars.
	 * @return array
	 */
	public function set_query_vars( $public_query_vars ) {
		$query_vars = $this->get_query_vars_from_filter_blocks();

		return array_reduce(
			array_values( $query_vars ),
			function( $acc, $query_vars_filter_block ) {
				return array_merge( $query_vars_filter_block, $acc );
			},
			$public_query_vars
		);
	}

	/**
	 * Get all the query args related to the filter by attributes block.
	 *
	 * @return array
	 * [color] => Array
	 *   (
	 *        [filter] => filter_color
	 *        [query_type] => query_type_color
	 *    )
	 *
	 * [size] => Array
	 *    (
	 *        [filter] => filter_size
	 *        [query_type] => query_type_size
	 *    )
	 * )
	 */
	private function get_filter_by_attributes_query_vars() {
		if ( ! empty( $this->attributes_filter_query_args ) ) {
			return $this->attributes_filter_query_args;
		}

		$this->attributes_filter_query_args = array_reduce(
			wc_get_attribute_taxonomies(),
			function( $acc, $attribute ) {
				$acc[ $attribute->attribute_name ] = array(
					'filter'     => AttributeFilter::FILTER_QUERY_VAR_PREFIX . $attribute->attribute_name,
					'query_type' => AttributeFilter::QUERY_TYPE_QUERY_VAR_PREFIX . $attribute->attribute_name,
				);
				return $acc;
			},
			array()
		);

		return $this->attributes_filter_query_args;
	}

	/**
	 * Return queries that are generated by query args.
	 *
	 * @return array
	 */
	private function get_queries_by_applied_filters() {
		return array(
			'price_filter'        => $this->get_filter_by_price_query(),
			'attributes_filter'   => $this->get_filter_by_attributes_query(),
			'stock_status_filter' => $this->get_filter_by_stock_status_query(),
			'rating_filter'       => $this->get_filter_by_rating_query(),
		);
	}

	/**
	 * Return queries that are generated by attributes
	 *
	 * @param array $parsed_block The Product Query that being rendered.
	 * @return array
	 */
	private function get_queries_by_custom_attributes( $parsed_block ) {
		$query            = $parsed_block['attrs']['query'];
		$on_sale_enabled  = isset( $query['__woocommerceOnSale'] ) && true === $query['__woocommerceOnSale'];
		$attributes_query = isset( $query['__woocommerceAttributes'] ) ? $this->get_product_attributes_query( $query['__woocommerceAttributes'] ) : array();
		$stock_query      = isset( $query['__woocommerceStockStatus'] ) ? $this->get_stock_status_query( $query['__woocommerceStockStatus'] ) : array();
		$visibility_query = $this->get_product_visibility_query( $stock_query );

		return array(
			'on_sale'      => ( $on_sale_enabled ? $this->get_on_sale_products_query() : array() ),
			'attributes'   => $attributes_query,
			'stock_status' => $stock_query,
			'visibility'   => $visibility_query,
		);
	}

	/**
	 * Return a query that filters products by price.
	 *
	 * @return array
	 */
	private function get_filter_by_price_query() {
		$min_price = get_query_var( PriceFilter::MIN_PRICE_QUERY_VAR );
		$max_price = get_query_var( PriceFilter::MAX_PRICE_QUERY_VAR );

		$max_price_query = empty( $max_price ) ? array() : [
			'key'     => '_price',
			'value'   => $max_price,
			'compare' => '<',
			'type'    => 'numeric',
		];

		$min_price_query = empty( $min_price ) ? array() : [
			'key'     => '_price',
			'value'   => $min_price,
			'compare' => '>=',
			'type'    => 'numeric',
		];

		if ( empty( $min_price_query ) && empty( $max_price_query ) ) {
			return array();
		}

		return array(
			'meta_query' => array(
				array(
					'relation' => 'AND',
					$max_price_query,
					$min_price_query,
				),
			),
		);
	}

	/**
	 * Return a query that filters products by attributes.
	 *
	 * @return array
	 */
	private function get_filter_by_attributes_query() {
		$attributes_filter_query_args = $this->get_filter_by_attributes_query_vars();

		$queries = array_reduce(
			$attributes_filter_query_args,
			function( $acc, $query_args ) {
				$attribute_name       = $query_args['filter'];
				$attribute_query_type = $query_args['query_type'];

				$attribute_value = get_query_var( $attribute_name );
				$attribute_query = get_query_var( $attribute_query_type );

				if ( empty( $attribute_value ) ) {
					return $acc;
				}

				// It is necessary explode the value because $attribute_value can be a string with multiple values (e.g. "red,blue").
				$attribute_value = explode( ',', $attribute_value );

				$acc[] = array(
					'taxonomy' => str_replace( AttributeFilter::FILTER_QUERY_VAR_PREFIX, 'pa_', $attribute_name ),
					'field'    => 'slug',
					'terms'    => $attribute_value,
					'operator' => 'and' === $attribute_query ? 'AND' : 'IN',
				);

				return $acc;
			},
			array()
		);

		if ( empty( $queries ) ) {
			return array();
		}

		return array(
			'tax_query' => array(
				array(
					'relation' => 'AND',
					$queries,
				),
			),
		);
	}

	/**
	 * Return a query that filters products by stock status.
	 *
	 * @return array
	 */
	private function get_filter_by_stock_status_query() {
		$filter_stock_status_values = get_query_var( StockFilter::STOCK_STATUS_QUERY_VAR );

		if ( empty( $filter_stock_status_values ) ) {
			return array();
		}

		$filtered_stock_status_values = array_filter(
			explode( ',', $filter_stock_status_values ),
			function( $stock_status ) {
				return in_array( $stock_status, StockFilter::get_stock_status_query_var_values(), true );
			}
		);

		if ( empty( $filtered_stock_status_values ) ) {
			return array();
		}

		return array(
			// Ignoring the warning of not using meta queries.
			// phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_query
			'meta_query' => array(
				array(
					'key'      => '_stock_status',
					'value'    => $filtered_stock_status_values,
					'operator' => 'IN',
				),
			),
		);
	}

	/**
	 * Return or initialize $valid_query_vars.
	 *
	 * @return array
	 */
	private function get_valid_query_vars() {
		if ( ! empty( $this->valid_query_vars ) ) {
			return $this->valid_query_vars;
		}

		$valid_query_vars       = array_keys( ( new WP_Query() )->fill_query_vars( array() ) );
		$this->valid_query_vars = array_merge(
			$valid_query_vars,
			// fill_query_vars doesn't include these vars so we need to add them manually.
			array(
				'date_query',
				'exact',
				'ignore_sticky_posts',
				'lazy_load_term_meta',
				'meta_compare_key',
				'meta_compare',
				'meta_query',
				'meta_type_key',
				'meta_type',
				'nopaging',
				'offset',
				'order',
				'orderby',
				'page',
				'post_type',
				'posts_per_page',
				'suppress_filters',
				'tax_query',
			)
		);

		return $this->valid_query_vars;
	}

	/**
	 * Merge two array recursively but replace the non-array values instead of
	 * merging them. The merging strategy:
	 *
	 * - If keys from merge array doesn't exist in the base array, create them.
	 * - For array items with numeric keys, we merge them as normal.
	 * - For array items with string keys:
	 *
	 *   - If the value isn't array, we'll use the value comming from the merge array.
	 *     $base = ['orderby' => 'date']
	 *     $new  = ['orderby' => 'meta_value_num']
	 *     Result: ['orderby' => 'meta_value_num']
	 *
	 *   - If the value is array, we'll use recursion to merge each key.
	 *     $base = ['meta_query' => [
	 *       [
	 *         'key'     => '_stock_status',
	 *         'compare' => 'IN'
	 *         'value'   =>  ['instock', 'onbackorder']
	 *       ]
	 *     ]]
	 *     $new  = ['meta_query' => [
	 *       [
	 *         'relation' => 'AND',
	 *         [...<max_price_query>],
	 *         [...<min_price_query>],
	 *       ]
	 *     ]]
	 *     Result: ['meta_query' => [
	 *       [
	 *         'key'     => '_stock_status',
	 *         'compare' => 'IN'
	 *         'value'   =>  ['instock', 'onbackorder']
	 *       ],
	 *       [
	 *         'relation' => 'AND',
	 *         [...<max_price_query>],
	 *         [...<min_price_query>],
	 *       ]
	 *     ]]
	 *
	 *     $base = ['post__in' => [1, 2, 3, 4, 5]]
	 *     $new  = ['post__in' => [3, 4, 5, 6, 7]]
	 *     Result: ['post__in' => [1, 2, 3, 4, 5, 3, 4, 5, 6, 7]]
	 *
	 * @param array $base First array.
	 * @param array $new  Second array.
	 */
	private function array_merge_recursive_replace_non_array_properties( $base, $new ) {
		foreach ( $new as $key => $value ) {
			if ( is_numeric( $key ) ) {
				$base[] = $value;
			} else {
				if ( is_array( $value ) ) {
					if ( ! isset( $base[ $key ] ) ) {
						$base[ $key ] = array();
					}
					$base[ $key ] = $this->array_merge_recursive_replace_non_array_properties( $base[ $key ], $value );
				} else {
					$base[ $key ] = $value;
				}
			}
		}

		return $base;
	}

	/**
	 * Get product-related query variables from the global query.
	 *
	 * @param array $parsed_block The Product Query that being rendered.
	 *
	 * @return array
	 */
	private function get_global_query( $parsed_block ) {
		if ( ! $this->is_custom_inherit_global_query_implementation_enabled ) {
			return array();
		}

		global $wp_query;

		$inherit_enabled = isset( $parsed_block['attrs']['query']['__woocommerceInherit'] ) && true === $parsed_block['attrs']['query']['__woocommerceInherit'];

		if ( ! $inherit_enabled ) {
			return array();
		}

		$query = array();

		if ( isset( $wp_query->query_vars['taxonomy'] ) && isset( $wp_query->query_vars['term'] ) ) {
			$query['tax_query'] = array(
				array(
					'taxonomy' => $wp_query->query_vars['taxonomy'],
					'field'    => 'slug',
					'terms'    => $wp_query->query_vars['term'],
				),
			);
		}

		if ( isset( $wp_query->query_vars['s'] ) ) {
			$query['s'] = $wp_query->query_vars['s'];
		}

		return $query;
	}

	/**
	 * Return a query that filters products by rating.
	 *
	 * @return array
	 */
	private function get_filter_by_rating_query() {
		$filter_rating_values = get_query_var( RatingFilter::RATING_QUERY_VAR );
		if ( empty( $filter_rating_values ) ) {
			return array();
		}

		$parsed_filter_rating_values = explode( ',', $filter_rating_values );
		$product_visibility_terms    = wc_get_product_visibility_term_ids();

		if ( empty( $parsed_filter_rating_values ) || empty( $product_visibility_terms ) ) {
			return array();
		}

		$rating_terms = array_map(
			function( $rating ) use ( $product_visibility_terms ) {
				return $product_visibility_terms[ 'rated-' . $rating ];
			},
			$parsed_filter_rating_values
		);

		return array(
			'tax_query' => array(
				array(
					'field'         => 'term_taxonomy_id',
					'taxonomy'      => 'product_visibility',
					'terms'         => $rating_terms,
					'operator'      => 'IN',
					'rating_filter' => true,
				),
			),
		);
	}


	/**
	 * Return a query to filter products by taxonomies (product categories, product tags, etc.)
	 *
	 * For example:
	 * User could provide "Product Categories" using "Filters" ToolsPanel available in Inspector Controls.
	 * We use this function to extract it's query from $tax_query.
	 *
	 * For example, this is how the query for product categories will look like in $tax_query array:
	 * Array
	 *    (
	 *        [taxonomy] => product_cat
	 *        [terms] => Array
	 *            (
	 *                [0] => 36
	 *            )
	 *    )
	 *
	 * For product categories, taxonomy would be "product_tag"
	 *
	 * @param array $query WP_Query.
	 * @return array Query to filter products by taxonomies.
	 */
	private function get_filter_by_taxonomies_query( $query ): array {
		if ( ! isset( $query['tax_query'] ) || ! is_array( $query['tax_query'] ) ) {
			return [];
		}

		$tax_query = $query['tax_query'];
		/**
		 * Get an array of taxonomy names associated with the "product" post type because
		 * we also want to include custom taxonomies associated with the "product" post type.
		 */
		$product_taxonomies = get_taxonomies( array( 'object_type' => array( 'product' ) ), 'names' );
		$result             = array_filter(
			$tax_query,
			function( $item ) use ( $product_taxonomies ) {
				return isset( $item['taxonomy'] ) && in_array( $item['taxonomy'], $product_taxonomies, true );
			}
		);

		return ! empty( $result ) ? [ 'tax_query' => $result ] : [];
	}

	/**
	 * Returns the keyword filter from the given query.
	 *
	 * @param WP_Query $query The query to extract the keyword filter from.
	 * @return array The keyword filter, or an empty array if none is found.
	 */
	private function get_filter_by_keyword_query( $query ): array {
		if ( ! is_array( $query ) ) {
			return [];
		}

		if ( isset( $query['s'] ) ) {
			return [ 's' => $query['s'] ];
		}

		return [];
	}
}
