<?php
/**
 * WooCommerce Shipping Label banner.
 */

namespace Automattic\WooCommerce\Internal\Admin;

use Automattic\Jetpack\Connection\Manager as Jetpack_Connection_Manager;

/**
 * Shows print shipping label banner on edit order page.
 */
class ShippingLabelBanner {

	/**
	 * Singleton for the display rules class
	 *
	 * @var ShippingLabelBannerDisplayRules
	 */
	private $shipping_label_banner_display_rules;

	/**
	 * Constructor
	 */
	public function __construct() {
		if ( ! is_admin() ) {
			return;
		}
		add_action( 'add_meta_boxes', array( $this, 'add_meta_boxes' ), 6, 2 );
	}

	/**
	 * Check if WooCommerce Shipping makes sense for this merchant.
	 *
	 * @return bool
	 */
	private function should_show_meta_box() {
		if ( ! $this->shipping_label_banner_display_rules ) {
			$jetpack_version   = null;
			$jetpack_connected = null;
			$wcs_version       = null;
			$wcs_tos_accepted  = null;

			if ( defined( 'JETPACK__VERSION' ) ) {
				$jetpack_version = JETPACK__VERSION;
			}

			if ( class_exists( Jetpack_Connection_Manager::class ) ) {
				$jetpack_connected = ( new Jetpack_Connection_Manager() )->has_connected_owner();
			}

			if ( class_exists( '\WC_Connect_Loader' ) ) {
				$wcs_version = \WC_Connect_Loader::get_wcs_version();
			}

			if ( class_exists( '\WC_Connect_Options' ) ) {
				$wcs_tos_accepted = \WC_Connect_Options::get_option( 'tos_accepted' );
			}

			$incompatible_plugins = class_exists( '\WC_Shipping_Fedex_Init' ) ||
				class_exists( '\WC_Shipping_UPS_Init' ) ||
				class_exists( '\WC_Integration_ShippingEasy' ) ||
				class_exists( '\WC_ShipStation_Integration' );

			$this->shipping_label_banner_display_rules =
				new ShippingLabelBannerDisplayRules(
					$jetpack_version,
					$jetpack_connected,
					$wcs_version,
					$wcs_tos_accepted,
					$incompatible_plugins
				);
		}

		return $this->shipping_label_banner_display_rules->should_display_banner();
	}

	/**
	 * Add metabox to order page.
	 *
	 * @param string   $post_type current post type.
	 * @param \WP_Post $post Current post object.
	 */
	public function add_meta_boxes( $post_type, $post ) {
		if ( 'shop_order' !== $post_type ) {
			return;
		}
		$order = wc_get_order( $post );
		if ( $this->should_show_meta_box() ) {
			add_meta_box(
				'woocommerce-admin-print-label',
				__( 'Shipping Label', 'woocommerce' ),
				array( $this, 'meta_box' ),
				null,
				'normal',
				'high',
				array(
					'context' => 'shipping_label',
					'order'   => $post->ID,
					'items'   => $this->count_shippable_items( $order ),
				)
			);
			add_action( 'admin_enqueue_scripts', array( $this, 'add_print_shipping_label_script' ) );
		}
	}

	/**
	 * Count shippable items
	 *
	 * @param \WC_Order $order Current order.
	 * @return int
	 */
	private function count_shippable_items( \WC_Order $order ) {
		$count = 0;
		foreach ( $order->get_items() as $item ) {
			if ( $item instanceof \WC_Order_Item_Product ) {
				$product = $item->get_product();
				if ( $product && $product->needs_shipping() ) {
					$count += $item->get_quantity();
				}
			}
		}
		return $count;
	}
	/**
	 * Adds JS to order page to render shipping banner.
	 *
	 * @param string $hook current page hook.
	 */
	public function add_print_shipping_label_script( $hook ) {
		$rtl = is_rtl() ? '.rtl' : '';
		wp_enqueue_style(
			'print-shipping-label-banner-style',
			WCAdminAssets::get_url( "print-shipping-label-banner/style{$rtl}", 'css' ),
			array( 'wp-components' ),
			WCAdminAssets::get_file_version( 'css' )
		);

		WCAdminAssets::register_script( 'wp-admin-scripts', 'print-shipping-label-banner', true );

		$payload = array(
			'nonce'                 => wp_create_nonce( 'wp_rest' ),
			'baseURL'               => get_rest_url(),
			'wcs_server_connection' => true,
		);

		wp_localize_script( 'print-shipping-label-banner', 'wcConnectData', $payload );
	}

	/**
	 * Render placeholder metabox.
	 *
	 * @param \WP_Post $post current post.
	 * @param array    $args empty args.
	 */
	public function meta_box( $post, $args ) {

		?>
		<div id="wc-admin-shipping-banner-root" class="woocommerce <?php echo esc_attr( 'wc-admin-shipping-banner' ); ?>" data-args="<?php echo esc_attr( wp_json_encode( $args['args'] ) ); ?>">
		</div>
		<?php
	}
}
