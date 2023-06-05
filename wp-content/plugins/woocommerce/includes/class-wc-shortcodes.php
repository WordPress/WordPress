<?php
/**
 * Shortcodes
 *
 * @package WooCommerce\Classes
 * @version 3.2.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * WooCommerce Shortcodes class.
 */
class WC_Shortcodes {

	/**
	 * Init shortcodes.
	 */
	public static function init() {
		$shortcodes = array(
			'product'                    => __CLASS__ . '::product',
			'product_page'               => __CLASS__ . '::product_page',
			'product_category'           => __CLASS__ . '::product_category',
			'product_categories'         => __CLASS__ . '::product_categories',
			'add_to_cart'                => __CLASS__ . '::product_add_to_cart',
			'add_to_cart_url'            => __CLASS__ . '::product_add_to_cart_url',
			'products'                   => __CLASS__ . '::products',
			'recent_products'            => __CLASS__ . '::recent_products',
			'sale_products'              => __CLASS__ . '::sale_products',
			'best_selling_products'      => __CLASS__ . '::best_selling_products',
			'top_rated_products'         => __CLASS__ . '::top_rated_products',
			'featured_products'          => __CLASS__ . '::featured_products',
			'product_attribute'          => __CLASS__ . '::product_attribute',
			'related_products'           => __CLASS__ . '::related_products',
			'shop_messages'              => __CLASS__ . '::shop_messages',
			'woocommerce_order_tracking' => __CLASS__ . '::order_tracking',
			'woocommerce_cart'           => __CLASS__ . '::cart',
			'woocommerce_checkout'       => __CLASS__ . '::checkout',
			'woocommerce_my_account'     => __CLASS__ . '::my_account',
		);

		foreach ( $shortcodes as $shortcode => $function ) {
			add_shortcode( apply_filters( "{$shortcode}_shortcode_tag", $shortcode ), $function );
		}

		// Alias for pre 2.1 compatibility.
		add_shortcode( 'woocommerce_messages', __CLASS__ . '::shop_messages' );
	}

	/**
	 * Shortcode Wrapper.
	 *
	 * @param string[] $function Callback function.
	 * @param array    $atts     Attributes. Default to empty array.
	 * @param array    $wrapper  Customer wrapper data.
	 *
	 * @return string
	 */
	public static function shortcode_wrapper(
		$function,
		$atts = array(),
		$wrapper = array(
			'class'  => 'woocommerce',
			'before' => null,
			'after'  => null,
		)
	) {
		ob_start();

		// @codingStandardsIgnoreStart
		echo empty( $wrapper['before'] ) ? '<div class="' . esc_attr( $wrapper['class'] ) . '">' : $wrapper['before'];
		call_user_func( $function, $atts );
		echo empty( $wrapper['after'] ) ? '</div>' : $wrapper['after'];
		// @codingStandardsIgnoreEnd

		return ob_get_clean();
	}

	/**
	 * Cart page shortcode.
	 *
	 * @return string
	 */
	public static function cart() {
		return is_null( WC()->cart ) ? '' : self::shortcode_wrapper( array( 'WC_Shortcode_Cart', 'output' ) );
	}

	/**
	 * Checkout page shortcode.
	 *
	 * @param array $atts Attributes.
	 * @return string
	 */
	public static function checkout( $atts ) {
		return self::shortcode_wrapper( array( 'WC_Shortcode_Checkout', 'output' ), $atts );
	}

	/**
	 * Order tracking page shortcode.
	 *
	 * @param array $atts Attributes.
	 * @return string
	 */
	public static function order_tracking( $atts ) {
		return self::shortcode_wrapper( array( 'WC_Shortcode_Order_Tracking', 'output' ), $atts );
	}

	/**
	 * My account page shortcode.
	 *
	 * @param array $atts Attributes.
	 * @return string
	 */
	public static function my_account( $atts ) {
		return self::shortcode_wrapper( array( 'WC_Shortcode_My_Account', 'output' ), $atts );
	}

	/**
	 * List products in a category shortcode.
	 *
	 * @param array $atts Attributes.
	 * @return string
	 */
	public static function product_category( $atts ) {
		if ( empty( $atts['category'] ) ) {
			return '';
		}

		$atts = array_merge(
			array(
				'limit'        => '12',
				'columns'      => '4',
				'orderby'      => 'menu_order title',
				'order'        => 'ASC',
				'category'     => '',
				'cat_operator' => 'IN',
			),
			(array) $atts
		);

		$shortcode = new WC_Shortcode_Products( $atts, 'product_category' );

		return $shortcode->get_content();
	}

	/**
	 * List all (or limited) product categories.
	 *
	 * @param array $atts Attributes.
	 * @return string
	 */
	public static function product_categories( $atts ) {
		if ( isset( $atts['number'] ) ) {
			$atts['limit'] = $atts['number'];
		}

		$atts = shortcode_atts(
			array(
				'limit'      => '-1',
				'orderby'    => 'name',
				'order'      => 'ASC',
				'columns'    => '4',
				'hide_empty' => 1,
				'parent'     => '',
				'ids'        => '',
			),
			$atts,
			'product_categories'
		);

		$ids        = array_filter( array_map( 'trim', explode( ',', $atts['ids'] ) ) );
		$hide_empty = ( true === $atts['hide_empty'] || 'true' === $atts['hide_empty'] || 1 === $atts['hide_empty'] || '1' === $atts['hide_empty'] ) ? 1 : 0;

		// Get terms and workaround WP bug with parents/pad counts.
		$args = array(
			'orderby'    => $atts['orderby'],
			'order'      => $atts['order'],
			'hide_empty' => $hide_empty,
			'include'    => $ids,
			'pad_counts' => true,
			'child_of'   => $atts['parent'],
		);

		$product_categories = apply_filters(
			'woocommerce_product_categories',
			get_terms( 'product_cat', $args )
		);

		if ( '' !== $atts['parent'] ) {
			$product_categories = wp_list_filter(
				$product_categories,
				array(
					'parent' => $atts['parent'],
				)
			);
		}

		if ( $hide_empty ) {
			foreach ( $product_categories as $key => $category ) {
				if ( 0 === $category->count ) {
					unset( $product_categories[ $key ] );
				}
			}
		}

		$atts['limit'] = '-1' === $atts['limit'] ? null : intval( $atts['limit'] );
		if ( $atts['limit'] ) {
			$product_categories = array_slice( $product_categories, 0, $atts['limit'] );
		}

		$columns = absint( $atts['columns'] );

		wc_set_loop_prop( 'columns', $columns );
		wc_set_loop_prop( 'is_shortcode', true );

		ob_start();

		if ( $product_categories ) {
			woocommerce_product_loop_start();

			foreach ( $product_categories as $category ) {
				wc_get_template(
					'content-product_cat.php',
					array(
						'category' => $category,
					)
				);
			}

			woocommerce_product_loop_end();
		}

		wc_reset_loop();

		return '<div class="woocommerce columns-' . $columns . '">' . ob_get_clean() . '</div>';
	}

	/**
	 * Recent Products shortcode.
	 *
	 * @param array $atts Attributes.
	 * @return string
	 */
	public static function recent_products( $atts ) {
		$atts = array_merge(
			array(
				'limit'        => '12',
				'columns'      => '4',
				'orderby'      => 'date',
				'order'        => 'DESC',
				'category'     => '',
				'cat_operator' => 'IN',
			),
			(array) $atts
		);

		$shortcode = new WC_Shortcode_Products( $atts, 'recent_products' );

		return $shortcode->get_content();
	}

	/**
	 * List multiple products shortcode.
	 *
	 * @param array $atts Attributes.
	 * @return string
	 */
	public static function products( $atts ) {
		$atts = (array) $atts;
		$type = 'products';

		// Allow list product based on specific cases.
		if ( isset( $atts['on_sale'] ) && wc_string_to_bool( $atts['on_sale'] ) ) {
			$type = 'sale_products';
		} elseif ( isset( $atts['best_selling'] ) && wc_string_to_bool( $atts['best_selling'] ) ) {
			$type = 'best_selling_products';
		} elseif ( isset( $atts['top_rated'] ) && wc_string_to_bool( $atts['top_rated'] ) ) {
			$type = 'top_rated_products';
		}

		$shortcode = new WC_Shortcode_Products( $atts, $type );

		return $shortcode->get_content();
	}

	/**
	 * Display a single product.
	 *
	 * @param array $atts Attributes.
	 * @return string
	 */
	public static function product( $atts ) {
		if ( empty( $atts ) ) {
			return '';
		}

		$atts['skus']  = isset( $atts['sku'] ) ? $atts['sku'] : '';
		$atts['ids']   = isset( $atts['id'] ) ? $atts['id'] : '';
		$atts['limit'] = '1';
		$shortcode     = new WC_Shortcode_Products( (array) $atts, 'product' );

		return $shortcode->get_content();
	}

	/**
	 * Display a single product price + cart button.
	 *
	 * @param array $atts Attributes.
	 * @return string
	 */
	public static function product_add_to_cart( $atts ) {
		global $post;

		if ( empty( $atts ) ) {
			return '';
		}

		$atts = shortcode_atts(
			array(
				'id'         => '',
				'class'      => '',
				'quantity'   => '1',
				'sku'        => '',
				'style'      => 'border:4px solid #ccc; padding: 12px;',
				'show_price' => 'true',
			),
			$atts,
			'product_add_to_cart'
		);

		if ( ! empty( $atts['id'] ) ) {
			$product_data = get_post( $atts['id'] );
		} elseif ( ! empty( $atts['sku'] ) ) {
			$product_id   = wc_get_product_id_by_sku( $atts['sku'] );
			$product_data = get_post( $product_id );
		} else {
			return '';
		}

		$product = is_object( $product_data ) && in_array( $product_data->post_type, array( 'product', 'product_variation' ), true ) ? wc_setup_product_data( $product_data ) : false;

		if ( ! $product ) {
			return '';
		}

		ob_start();

		echo '<p class="product woocommerce add_to_cart_inline ' . esc_attr( $atts['class'] ) . '" style="' . ( empty( $atts['style'] ) ? '' : esc_attr( $atts['style'] ) ) . '">';

		if ( wc_string_to_bool( $atts['show_price'] ) ) {
			// @codingStandardsIgnoreStart
			echo $product->get_price_html();
			// @codingStandardsIgnoreEnd
		}

		woocommerce_template_loop_add_to_cart(
			array(
				'quantity' => $atts['quantity'],
			)
		);

		echo '</p>';

		// Restore Product global in case this is shown inside a product post.
		wc_setup_product_data( $post );

		return ob_get_clean();
	}

	/**
	 * Get the add to cart URL for a product.
	 *
	 * @param array $atts Attributes.
	 * @return string
	 */
	public static function product_add_to_cart_url( $atts ) {
		if ( empty( $atts ) ) {
			return '';
		}

		if ( isset( $atts['id'] ) ) {
			$product_data = get_post( $atts['id'] );
		} elseif ( isset( $atts['sku'] ) ) {
			$product_id   = wc_get_product_id_by_sku( $atts['sku'] );
			$product_data = get_post( $product_id );
		} else {
			return '';
		}

		$product = is_object( $product_data ) && in_array( $product_data->post_type, array( 'product', 'product_variation' ), true ) ? wc_setup_product_data( $product_data ) : false;

		if ( ! $product ) {
			return '';
		}

		$_product = wc_get_product( $product_data );

		return esc_url( $_product->add_to_cart_url() );
	}

	/**
	 * List all products on sale.
	 *
	 * @param array $atts Attributes.
	 * @return string
	 */
	public static function sale_products( $atts ) {
		$atts = array_merge(
			array(
				'limit'        => '12',
				'columns'      => '4',
				'orderby'      => 'title',
				'order'        => 'ASC',
				'category'     => '',
				'cat_operator' => 'IN',
			),
			(array) $atts
		);

		$shortcode = new WC_Shortcode_Products( $atts, 'sale_products' );

		return $shortcode->get_content();
	}

	/**
	 * List best selling products on sale.
	 *
	 * @param array $atts Attributes.
	 * @return string
	 */
	public static function best_selling_products( $atts ) {
		$atts = array_merge(
			array(
				'limit'        => '12',
				'columns'      => '4',
				'category'     => '',
				'cat_operator' => 'IN',
			),
			(array) $atts
		);

		$shortcode = new WC_Shortcode_Products( $atts, 'best_selling_products' );

		return $shortcode->get_content();
	}

	/**
	 * List top rated products on sale.
	 *
	 * @param array $atts Attributes.
	 * @return string
	 */
	public static function top_rated_products( $atts ) {
		$atts = array_merge(
			array(
				'limit'        => '12',
				'columns'      => '4',
				'orderby'      => 'title',
				'order'        => 'ASC',
				'category'     => '',
				'cat_operator' => 'IN',
			),
			(array) $atts
		);

		$shortcode = new WC_Shortcode_Products( $atts, 'top_rated_products' );

		return $shortcode->get_content();
	}

	/**
	 * Output featured products.
	 *
	 * @param array $atts Attributes.
	 * @return string
	 */
	public static function featured_products( $atts ) {
		$atts = array_merge(
			array(
				'limit'        => '12',
				'columns'      => '4',
				'orderby'      => 'date',
				'order'        => 'DESC',
				'category'     => '',
				'cat_operator' => 'IN',
			),
			(array) $atts
		);

		$atts['visibility'] = 'featured';

		$shortcode = new WC_Shortcode_Products( $atts, 'featured_products' );

		return $shortcode->get_content();
	}

	/**
	 * Show a single product page.
	 *
	 * @param array $atts Attributes.
	 * @return string
	 */
	public static function product_page( $atts ) {
		if ( empty( $atts ) ) {
			return '';
		}

		if ( ! isset( $atts['id'] ) && ! isset( $atts['sku'] ) ) {
			return '';
		}

		$args = array(
			'posts_per_page'      => 1,
			'post_type'           => 'product',
			'post_status'         => ( ! empty( $atts['status'] ) ) ? $atts['status'] : 'publish',
			'ignore_sticky_posts' => 1,
			'no_found_rows'       => 1,
		);

		if ( isset( $atts['sku'] ) ) {
			$args['meta_query'][] = array(
				'key'     => '_sku',
				'value'   => sanitize_text_field( $atts['sku'] ),
				'compare' => '=',
			);

			$args['post_type'] = array( 'product', 'product_variation' );
		}

		if ( isset( $atts['id'] ) ) {
			$args['p'] = absint( $atts['id'] );
		}

		// Don't render titles if desired.
		if ( isset( $atts['show_title'] ) && ! $atts['show_title'] ) {
			remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_title', 5 );
		}

		// Change form action to avoid redirect.
		add_filter( 'woocommerce_add_to_cart_form_action', '__return_empty_string' );

		$single_product = new WP_Query( $args );

		$preselected_id = '0';

		// Check if sku is a variation.
		if ( isset( $atts['sku'] ) && $single_product->have_posts() && 'product_variation' === $single_product->post->post_type ) {

			$variation  = wc_get_product_object( 'variation', $single_product->post->ID );
			$attributes = $variation->get_attributes();

			// Set preselected id to be used by JS to provide context.
			$preselected_id = $single_product->post->ID;

			// Get the parent product object.
			$args = array(
				'posts_per_page'      => 1,
				'post_type'           => 'product',
				'post_status'         => 'publish',
				'ignore_sticky_posts' => 1,
				'no_found_rows'       => 1,
				'p'                   => $single_product->post->post_parent,
			);

			$single_product = new WP_Query( $args );
			?>
			<script type="text/javascript">
				jQuery( function( $ ) {
					var $variations_form = $( '[data-product-page-preselected-id="<?php echo esc_attr( $preselected_id ); ?>"]' ).find( 'form.variations_form' );

					<?php foreach ( $attributes as $attr => $value ) { ?>
						$variations_form.find( 'select[name="<?php echo esc_attr( $attr ); ?>"]' ).val( '<?php echo esc_js( $value ); ?>' );
					<?php } ?>
				});
			</script>
			<?php
		}

		// For "is_single" to always make load comments_template() for reviews.
		$single_product->is_single = true;

		ob_start();

		global $wp_query;

		// Backup query object so following loops think this is a product page.
		$previous_wp_query = $wp_query;
		// @codingStandardsIgnoreStart
		$wp_query          = $single_product;
		// @codingStandardsIgnoreEnd

		wp_enqueue_script( 'wc-single-product' );

		while ( $single_product->have_posts() ) {
			$single_product->the_post()
			?>
			<div class="single-product" data-product-page-preselected-id="<?php echo esc_attr( $preselected_id ); ?>">
				<?php wc_get_template_part( 'content', 'single-product' ); ?>
			</div>
			<?php
		}

		// Restore $previous_wp_query and reset post data.
		// @codingStandardsIgnoreStart
		$wp_query = $previous_wp_query;
		// @codingStandardsIgnoreEnd
		wp_reset_postdata();

		// Re-enable titles if they were removed.
		if ( isset( $atts['show_title'] ) && ! $atts['show_title'] ) {
			add_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_title', 5 );
		}

		remove_filter( 'woocommerce_add_to_cart_form_action', '__return_empty_string' );

		return '<div class="woocommerce">' . ob_get_clean() . '</div>';
	}

	/**
	 * Show messages.
	 *
	 * @return string
	 */
	public static function shop_messages() {
		if ( ! function_exists( 'wc_print_notices' ) ) {
			return '';
		}
		return '<div class="woocommerce">' . wc_print_notices( true ) . '</div>';
	}

	/**
	 * Order by rating.
	 *
	 * @deprecated 3.2.0 Use WC_Shortcode_Products::order_by_rating_post_clauses().
	 * @param      array $args Query args.
	 * @return     array
	 */
	public static function order_by_rating_post_clauses( $args ) {
		return WC_Shortcode_Products::order_by_rating_post_clauses( $args );
	}

	/**
	 * List products with an attribute shortcode.
	 * Example [product_attribute attribute="color" filter="black"].
	 *
	 * @param array $atts Attributes.
	 * @return string
	 */
	public static function product_attribute( $atts ) {
		$atts = array_merge(
			array(
				'limit'     => '12',
				'columns'   => '4',
				'orderby'   => 'title',
				'order'     => 'ASC',
				'attribute' => '',
				'terms'     => '',
			),
			(array) $atts
		);

		if ( empty( $atts['attribute'] ) ) {
			return '';
		}

		$shortcode = new WC_Shortcode_Products( $atts, 'product_attribute' );

		return $shortcode->get_content();
	}

	/**
	 * List related products.
	 *
	 * @param array $atts Attributes.
	 * @return string
	 */
	public static function related_products( $atts ) {
		if ( isset( $atts['per_page'] ) ) {
			$atts['limit'] = $atts['per_page'];
		}

		// @codingStandardsIgnoreStart
		$atts = shortcode_atts( array(
			'limit'    => '4',
			'columns'  => '4',
			'orderby'  => 'rand',
		), $atts, 'related_products' );
		// @codingStandardsIgnoreEnd

		ob_start();

		// Rename arg.
		$atts['posts_per_page'] = absint( $atts['limit'] );

		woocommerce_related_products( $atts );

		return ob_get_clean();
	}
}
