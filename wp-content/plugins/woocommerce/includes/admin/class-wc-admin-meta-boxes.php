<?php
/**
 * WooCommerce Meta Boxes
 *
 * Sets up the write panels used by products and orders (custom post types).
 *
 * @package WooCommerce\Admin\Meta Boxes
 */

use Automattic\Jetpack\Constants;
use Automattic\WooCommerce\Internal\Admin\Orders\Edit as OrderEdit;

defined( 'ABSPATH' ) || exit;

/**
 * WC_Admin_Meta_Boxes.
 */
class WC_Admin_Meta_Boxes {
	/**
	 * Name of the option used to store errors to be displayed at the next suitable opportunity.
	 *
	 * @since 6.5.0
	 */
	public const ERROR_STORE = 'woocommerce_meta_box_errors';

	/**
	 * Is meta boxes saved once?
	 *
	 * @var boolean
	 */
	private static $saved_meta_boxes = false;

	/**
	 * Meta box error messages.
	 *
	 * @var array
	 */
	public static $meta_box_errors = array();

	/**
	 * Constructor.
	 */
	public function __construct() {
		add_action( 'add_meta_boxes', array( $this, 'remove_meta_boxes' ), 10 );
		add_action( 'add_meta_boxes', array( $this, 'rename_meta_boxes' ), 20 );
		add_action( 'add_meta_boxes', array( $this, 'add_meta_boxes' ), 30 );
		add_action( 'add_meta_boxes', array( $this, 'add_product_boxes_sort_order' ), 40 );
		add_action( 'save_post', array( $this, 'save_meta_boxes' ), 1, 2 );

		OrderEdit::add_save_meta_boxes();

		// Save Product Meta Boxes.
		add_action( 'woocommerce_process_product_meta', 'WC_Meta_Box_Product_Data::save', 10, 2 );
		add_action( 'woocommerce_process_product_meta', 'WC_Meta_Box_Product_Images::save', 20, 2 );

		// Save Coupon Meta Boxes.
		add_action( 'woocommerce_process_shop_coupon_meta', 'WC_Meta_Box_Coupon_Data::save', 10, 2 );

		// Save Rating Meta Boxes.
		add_filter( 'wp_update_comment_data', 'WC_Meta_Box_Product_Reviews::save', 1 );

		// Error handling (for showing errors from meta boxes on next page load).
		add_action( 'admin_notices', array( $this, 'output_errors' ) );
		add_action( 'shutdown', array( $this, 'append_to_error_store' ) );

		add_filter( 'theme_product_templates', array( $this, 'remove_block_templates' ), 10, 1 );
	}

	/**
	 * Add an error message.
	 *
	 * @param string $text Error to add.
	 */
	public static function add_error( $text ) {
		self::$meta_box_errors[] = $text;
	}

	/**
	 * Save errors to an option.
	 *
	 * Note that calling this will overwrite any errors that have already been stored via the Options API.
	 * Unless you are sure you want this, consider using the append_to_error_store() method instead.
	 */
	public function save_errors() {
		update_option( self::ERROR_STORE, self::$meta_box_errors );
	}

	/**
	 * If additional errors have been added in the current request (ie, via the add_error() method) then they
	 * will be added to the persistent error store via the Options API.
	 *
	 * @since 6.5.0
	 */
	public function append_to_error_store() {
		if ( empty( self::$meta_box_errors ) ) {
			return;
		}

		$existing_errors = get_option( self::ERROR_STORE, array() );
		update_option( self::ERROR_STORE, array_unique( array_merge( $existing_errors, self::$meta_box_errors ) ) );
	}

	/**
	 * Show any stored error messages.
	 */
	public function output_errors() {
		$errors = array_filter( (array) get_option( self::ERROR_STORE ) );

		if ( ! empty( $errors ) ) {

			echo '<div id="woocommerce_errors" class="error notice is-dismissible">';

			foreach ( $errors as $error ) {
				echo '<p>' . wp_kses_post( $error ) . '</p>';
			}

			echo '</div>';

			// Clear.
			delete_option( self::ERROR_STORE );
		}
	}

	/**
	 * Add WC Meta boxes.
	 */
	public function add_meta_boxes() {
		$screen    = get_current_screen();
		$screen_id = $screen ? $screen->id : '';

		// Products.
		add_meta_box( 'postexcerpt', __( 'Product short description', 'woocommerce' ), 'WC_Meta_Box_Product_Short_Description::output', 'product', 'normal' );
		add_meta_box( 'woocommerce-product-data', __( 'Product data', 'woocommerce' ), 'WC_Meta_Box_Product_Data::output', 'product', 'normal', 'high' );
		add_meta_box( 'woocommerce-product-images', __( 'Product gallery', 'woocommerce' ), 'WC_Meta_Box_Product_Images::output', 'product', 'side', 'low' );

		// Orders.
		foreach ( wc_get_order_types( 'order-meta-boxes' ) as $type ) {
			$order_type_object = get_post_type_object( $type );
			OrderEdit::add_order_meta_boxes( $type, $order_type_object->labels->singular_name );
		}

		// Coupons.
		add_meta_box( 'woocommerce-coupon-data', __( 'Coupon data', 'woocommerce' ), 'WC_Meta_Box_Coupon_Data::output', 'shop_coupon', 'normal', 'high' );

		// Comment rating.
		if ( 'comment' === $screen_id && isset( $_GET['c'] ) && metadata_exists( 'comment', wc_clean( wp_unslash( $_GET['c'] ) ), 'rating' ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			add_meta_box( 'woocommerce-rating', __( 'Rating', 'woocommerce' ), 'WC_Meta_Box_Product_Reviews::output', 'comment', 'normal', 'high' );
		}
	}

	/**
	 * Add default sort order for meta boxes on product page.
	 */
	public function add_product_boxes_sort_order() {
		$current_value = get_user_meta( get_current_user_id(), 'meta-box-order_product', true );

		if ( $current_value ) {
			return;
		}

		update_user_meta(
			get_current_user_id(),
			'meta-box-order_product',
			array(
				'side'     => 'submitdiv,postimagediv,woocommerce-product-images,product_catdiv,tagsdiv-product_tag',
				'normal'   => 'woocommerce-product-data,postcustom,slugdiv,postexcerpt',
				'advanced' => '',
			)
		);

	}

	/**
	 * Remove bloat.
	 */
	public function remove_meta_boxes() {
		remove_meta_box( 'postexcerpt', 'product', 'normal' );
		remove_meta_box( 'product_shipping_classdiv', 'product', 'side' );
		remove_meta_box( 'commentsdiv', 'product', 'normal' );
		remove_meta_box( 'commentstatusdiv', 'product', 'side' );
		remove_meta_box( 'commentstatusdiv', 'product', 'normal' );
		remove_meta_box( 'woothemes-settings', 'shop_coupon', 'normal' );
		remove_meta_box( 'commentstatusdiv', 'shop_coupon', 'normal' );
		remove_meta_box( 'slugdiv', 'shop_coupon', 'normal' );

		foreach ( wc_get_order_types( 'order-meta-boxes' ) as $type ) {
			remove_meta_box( 'commentsdiv', $type, 'normal' );
			remove_meta_box( 'woothemes-settings', $type, 'normal' );
			remove_meta_box( 'commentstatusdiv', $type, 'normal' );
			remove_meta_box( 'slugdiv', $type, 'normal' );
			remove_meta_box( 'submitdiv', $type, 'side' );
		}
	}

	/**
	 * Rename core meta boxes.
	 */
	public function rename_meta_boxes() {
		global $post;

		// Comments/Reviews.
		if ( isset( $post ) && ( 'publish' === $post->post_status || 'private' === $post->post_status ) && post_type_supports( 'product', 'comments' ) ) {
			remove_meta_box( 'commentsdiv', 'product', 'normal' );
			add_meta_box( 'commentsdiv', __( 'Reviews', 'woocommerce' ), 'post_comment_meta_box', 'product', 'normal' );
		}
	}

	/**
	 * Check if we're saving, the trigger an action based on the post type.
	 *
	 * @param  int    $post_id Post ID.
	 * @param  object $post Post object.
	 */
	public function save_meta_boxes( $post_id, $post ) {
		$post_id = absint( $post_id );

		// $post_id and $post are required
		if ( empty( $post_id ) || empty( $post ) || self::$saved_meta_boxes ) {
			return;
		}

		// Dont' save meta boxes for revisions or autosaves.
		if ( Constants::is_true( 'DOING_AUTOSAVE' ) || is_int( wp_is_post_revision( $post ) ) || is_int( wp_is_post_autosave( $post ) ) ) {
			return;
		}

		// Check the nonce.
		if ( empty( $_POST['woocommerce_meta_nonce'] ) || ! wp_verify_nonce( wp_unslash( $_POST['woocommerce_meta_nonce'] ), 'woocommerce_save_data' ) ) { // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
			return;
		}

		// Check the post being saved == the $post_id to prevent triggering this call for other save_post events.
		if ( empty( $_POST['post_ID'] ) || absint( $_POST['post_ID'] ) !== $post_id ) {
			return;
		}

		// Check user has permission to edit.
		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}

		// We need this save event to run once to avoid potential endless loops. This would have been perfect:
		// remove_action( current_filter(), __METHOD__ );
		// But cannot be used due to https://github.com/woocommerce/woocommerce/issues/6485
		// When that is patched in core we can use the above.
		self::$saved_meta_boxes = true;

		// Check the post type.
		if ( in_array( $post->post_type, wc_get_order_types( 'order-meta-boxes' ), true ) ) {
			/**
			 * Save meta for shop order.
			 *
			 * @param int $post_id Post ID.
			 * @param object $post Post object.
			 *
			 * @since 2.1.0
			 */
			do_action( 'woocommerce_process_shop_order_meta', $post_id, $post );
		} elseif ( in_array( $post->post_type, array( 'product', 'shop_coupon' ), true ) ) {
			/**
			 * Save meta for product.
			 *
			 * @param int $post_id Post ID.
			 * @param object $post Post object.
			 *
			 * @since 2.1.0
			 */
			do_action( 'woocommerce_process_' . $post->post_type . '_meta', $post_id, $post );
		}
	}

	/**
	 * Remove irrelevant block templates from the list of available templates for products.
	 * This will also remove custom created templates.
	 *
	 * @param string[] $templates Array of template header names keyed by the template file name.
	 *
	 * @return string[] Templates array excluding block-based templates.
	 */
	public function remove_block_templates( $templates ) {
		if ( count( $templates ) === 0 || ! wc_current_theme_is_fse_theme() || ( ! function_exists( 'gutenberg_get_block_template' ) && ! function_exists( 'get_block_template' ) ) ) {
			return $templates;
		}

		$theme              = wp_get_theme()->get_stylesheet();
		$filtered_templates = array();

		foreach ( $templates as $template_key => $template_name ) {
			// Filter out the single-product.html template as this is a duplicate of "Default Template".
			if ( 'single-product' === $template_key ) {
				continue;
			}

			$block_template = function_exists( 'gutenberg_get_block_template' ) ?
				gutenberg_get_block_template( $theme . '//' . $template_key ) :
				get_block_template( $theme . '//' . $template_key );

			// If the block template has the product post type specified, include it.
			if ( $block_template && is_array( $block_template->post_types ) && in_array( 'product', $block_template->post_types ) ) {
				$filtered_templates[ $template_key ] = $template_name;
			}
		}

		return $filtered_templates;
	}
}

new WC_Admin_Meta_Boxes();
