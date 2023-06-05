<?php
/**
 * WooCommerce product embed
 *
 * @version  2.4.11
 * @package  WooCommerce\Classes\Embed
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Embed Class which handles any WooCommerce Products that are embedded on this site or another site.
 */
class WC_Embed {

	/**
	 * Init embed class.
	 *
	 * @since 2.4.11
	 */
	public static function init() {

		// Filter all of the content that's going to be embedded.
		add_filter( 'the_excerpt_embed', array( __CLASS__, 'the_excerpt' ), 10 );

		// Make sure no comments display. Doesn't make sense for products.
		add_action( 'embed_content_meta', array( __CLASS__, 'remove_comments_button' ), 5 );

		// In the comments place let's display the product rating.
		add_action( 'embed_content_meta', array( __CLASS__, 'get_ratings' ), 5 );

		// Add some basic styles.
		add_action( 'embed_head', array( __CLASS__, 'print_embed_styles' ) );
	}

	/**
	 * Remove comments button on product embeds.
	 *
	 * @since 2.6.0
	 */
	public static function remove_comments_button() {
		if ( self::is_embedded_product() ) {
			remove_action( 'embed_content_meta', 'print_embed_comments_button' );
		}
	}

	/**
	 * Check if this is an embedded product - to make sure we don't mess up regular posts.
	 *
	 * @since 2.4.11
	 * @return bool
	 */
	public static function is_embedded_product() {
		if ( function_exists( 'is_embed' ) && is_embed() && is_product() ) {
			return true;
		}
		return false;
	}

	/**
	 * Create the excerpt for embedded products - we want to add the buy button to it.
	 *
	 * @since 2.4.11
	 * @param string $excerpt Embed short description.
	 * @return string
	 */
	public static function the_excerpt( $excerpt ) {
		global $post;

		// Get product.
		$_product = wc_get_product( get_the_ID() );

		// Make sure we're only affecting embedded products.
		if ( self::is_embedded_product() ) {
			echo '<p><span class="wc-embed-price">' . $_product->get_price_html() . '</span></p>'; // WPCS: XSS ok.

			if ( ! empty( $post->post_excerpt ) ) {
				ob_start();
				woocommerce_template_single_excerpt();
				$excerpt = ob_get_clean();
			}

			// Add the button.
			$excerpt .= self::product_buttons();
		}
		return $excerpt;
	}

	/**
	 * Create the button to go to the product page for embedded products.
	 *
	 * @since 2.4.11
	 * @return string
	 */
	public static function product_buttons() {
		$_product = wc_get_product( get_the_ID() );
		$buttons  = array();
		$button   = '<a href="%s" class="wp-embed-more wc-embed-button">%s</a>';

		if ( $_product->is_type( 'simple' ) && $_product->is_purchasable() && $_product->is_in_stock() ) {
			$buttons[] = sprintf( $button, esc_url( add_query_arg( 'add-to-cart', get_the_ID(), wc_get_cart_url() ) ), esc_html__( 'Buy now', 'woocommerce' ) );
		}

		$buttons[] = sprintf( $button, get_the_permalink(), esc_html__( 'Read more', 'woocommerce' ) );

		return '<p>' . implode( ' ', $buttons ) . '</p>';
	}

	/**
	 * Prints the markup for the rating stars.
	 *
	 * @since 2.4.11
	 */
	public static function get_ratings() {
		// Make sure we're only affecting embedded products.
		if ( ! self::is_embedded_product() ) {
			return;
		}

		$_product = wc_get_product( get_the_ID() );

		if ( $_product && $_product->get_average_rating() > 0 ) {
			?>
			<div class="wc-embed-rating">
				<?php
					printf(
						/* translators: %s: average rating */
						esc_html__( 'Rated %s out of 5', 'woocommerce' ),
						esc_html( $_product->get_average_rating() )
					);
				?>
			</div>
			<?php
		}
	}

	/**
	 * Basic styling.
	 */
	public static function print_embed_styles() {
		if ( ! self::is_embedded_product() ) {
			return;
		}
		?>
		<style type="text/css">
			a.wc-embed-button {
				border-radius: 4px;
				border: 1px solid #ddd;
				box-shadow: 0px 1px 0 0px rgba(0, 0, 0, 0.05);
				display:inline-block;
				padding: .5em;
			}
			a.wc-embed-button:hover, a.wc-embed-button:focus {
				border: 1px solid #ccc;
				box-shadow: 0px 1px 0 0px rgba(0, 0, 0, 0.1);
				color: #999;
				text-decoration: none;
			}
			.wp-embed-excerpt p {
				margin: 0 0 1em;
			}
			.wc-embed-price {
				display: block;
				opacity: .75;
				font-weight: 700;
				margin-top: -.75em;
			}
			.wc-embed-rating {
				display: inline-block;
			}
		</style>
		<?php
	}
}

WC_Embed::init();
