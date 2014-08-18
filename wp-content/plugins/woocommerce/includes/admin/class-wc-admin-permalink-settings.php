<?php
/**
 * Adds settings to the permalinks admin settings page.
 *
 * @author 		WooThemes
 * @category 	Admin
 * @package 	WooCommerce/Admin
 * @version     2.1.0
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( ! class_exists( 'WC_Admin_Permalink_Settings' ) ) :

/**
 * WC_Admin_Permalink_Settings Class
 */
class WC_Admin_Permalink_Settings {

	/**
	 * Hook in tabs.
	 */
	public function __construct() {
		add_action( 'admin_init', array( $this, 'settings_init' ) );
		add_action( 'admin_init', array( $this, 'settings_save' ) );
	}

	/**
	 * Init our settings
	 */
	public function settings_init() {
		// Add a section to the permalinks page
		add_settings_section( 'woocommerce-permalink', __( 'Product permalink base', 'woocommerce' ), array( $this, 'settings' ), 'permalink' );

		// Add our settings
		add_settings_field(
			'woocommerce_product_category_slug',      	// id
			__( 'Product category base', 'woocommerce' ), 	// setting title
			array( $this, 'product_category_slug_input' ),  // display callback
			'permalink',                 				// settings page
			'optional'                  				// settings section
		);
		add_settings_field(
			'woocommerce_product_tag_slug',      		// id
			__( 'Product tag base', 'woocommerce' ), 	// setting title
			array( $this, 'product_tag_slug_input' ),  // display callback
			'permalink',                 				// settings page
			'optional'                  				// settings section
		);
		add_settings_field(
			'woocommerce_product_attribute_slug',      	// id
			__( 'Product attribute base', 'woocommerce' ), 	// setting title
			array( $this, 'product_attribute_slug_input' ),  // display callback
			'permalink',                 				// settings page
			'optional'                  				// settings section
		);
	}

	/**
	 * Show a slug input box.
	 */
	public function product_category_slug_input() {
		$permalinks = get_option( 'woocommerce_permalinks' );
		?>
		<input name="woocommerce_product_category_slug" type="text" class="regular-text code" value="<?php if ( isset( $permalinks['category_base'] ) ) echo esc_attr( $permalinks['category_base'] ); ?>" placeholder="<?php echo _x('product-category', 'slug', 'woocommerce') ?>" />
		<?php
	}

	/**
	 * Show a slug input box.
	 */
	public function product_tag_slug_input() {
		$permalinks = get_option( 'woocommerce_permalinks' );
		?>
		<input name="woocommerce_product_tag_slug" type="text" class="regular-text code" value="<?php if ( isset( $permalinks['tag_base'] ) ) echo esc_attr( $permalinks['tag_base'] ); ?>" placeholder="<?php echo _x('product-tag', 'slug', 'woocommerce') ?>" />
		<?php
	}

	/**
	 * Show a slug input box.
	 */
	public function product_attribute_slug_input() {
		$permalinks = get_option( 'woocommerce_permalinks' );
		?>
		<input name="woocommerce_product_attribute_slug" type="text" class="regular-text code" value="<?php if ( isset( $permalinks['attribute_base'] ) ) echo esc_attr( $permalinks['attribute_base'] ); ?>" /><code>/attribute-name/attribute/</code>
		<?php
	}

	/**
	 * Show the settings
	 */
	public function settings() {
		echo wpautop( __( 'These settings control the permalinks used for products. These settings only apply when <strong>not using "default" permalinks above</strong>.', 'woocommerce' ) );

		$permalinks = get_option( 'woocommerce_permalinks' );
		$product_permalink = $permalinks['product_base'];

		// Get shop page
		$shop_page_id 	= wc_get_page_id( 'shop' );
		$base_slug 		= ( $shop_page_id > 0 && get_page( $shop_page_id ) ) ? get_page_uri( $shop_page_id ) : _x( 'shop', 'default-slug', 'woocommerce' );
		$product_base 	= _x( 'product', 'default-slug', 'woocommerce' );

		$structures = array(
			0 => '',
			1 => '/' . trailingslashit( $product_base ),
			2 => '/' . trailingslashit( $base_slug ),
			3 => '/' . trailingslashit( $base_slug ) . trailingslashit( '%product_cat%' )
		);
		?>
		<table class="form-table">
			<tbody>
				<tr>
					<th><label><input name="product_permalink" type="radio" value="<?php echo $structures[0]; ?>" class="wctog" <?php checked( $structures[0], $product_permalink ); ?> /> <?php _e( 'Default', 'woocommerce' ); ?></label></th>
					<td><code><?php echo home_url(); ?>/?product=sample-product</code></td>
				</tr>
				<tr>
					<th><label><input name="product_permalink" type="radio" value="<?php echo $structures[1]; ?>" class="wctog" <?php checked( $structures[1], $product_permalink ); ?> /> <?php _e( 'Product', 'woocommerce' ); ?></label></th>
					<td><code><?php echo home_url(); ?>/<?php echo $product_base; ?>/sample-product/</code></td>
				</tr>
				<?php if ( $shop_page_id ) : ?>
					<tr>
						<th><label><input name="product_permalink" type="radio" value="<?php echo $structures[2]; ?>" class="wctog" <?php checked( $structures[2], $product_permalink ); ?> /> <?php _e( 'Shop base', 'woocommerce' ); ?></label></th>
						<td><code><?php echo home_url(); ?>/<?php echo $base_slug; ?>/sample-product/</code></td>
					</tr>
					<tr>
						<th><label><input name="product_permalink" type="radio" value="<?php echo $structures[3]; ?>" class="wctog" <?php checked( $structures[3], $product_permalink ); ?> /> <?php _e( 'Shop base with category', 'woocommerce' ); ?></label></th>
						<td><code><?php echo home_url(); ?>/<?php echo $base_slug; ?>/product-category/sample-product/</code></td>
					</tr>
				<?php endif; ?>
				<tr>
					<th><label><input name="product_permalink" id="woocommerce_custom_selection" type="radio" value="custom" class="tog" <?php checked( in_array( $product_permalink, $structures ), false ); ?> />
						<?php _e( 'Custom Base', 'woocommerce' ); ?></label></th>
					<td>
						<input name="product_permalink_structure" id="woocommerce_permalink_structure" type="text" value="<?php echo esc_attr( $product_permalink ); ?>" class="regular-text code"> <span class="description"><?php _e( 'Enter a custom base to use. A base <strong>must</strong> be set or WordPress will use default instead.', 'woocommerce' ); ?></span>
					</td>
				</tr>
			</tbody>
		</table>
		<script type="text/javascript">
			jQuery(function(){
				jQuery('input.wctog').change(function() {
					jQuery('#woocommerce_permalink_structure').val( jQuery(this).val() );
				});

				jQuery('#woocommerce_permalink_structure').focus(function(){
					jQuery('#woocommerce_custom_selection').click();
				});
			});
		</script>
		<?php
	}

	/**
	 * Save the settings
	 */
	public function settings_save() {
		if ( ! is_admin() )
			return;

		// We need to save the options ourselves; settings api does not trigger save for the permalinks page
		if ( isset( $_POST['permalink_structure'] ) || isset( $_POST['category_base'] ) && isset( $_POST['product_permalink'] ) ) {
			// Cat and tag bases
			$woocommerce_product_category_slug = wc_clean( $_POST['woocommerce_product_category_slug'] );
			$woocommerce_product_tag_slug = wc_clean( $_POST['woocommerce_product_tag_slug'] );
			$woocommerce_product_attribute_slug = wc_clean( $_POST['woocommerce_product_attribute_slug'] );

			$permalinks = get_option( 'woocommerce_permalinks' );
			if ( ! $permalinks )
				$permalinks = array();

			$permalinks['category_base'] 	= untrailingslashit( $woocommerce_product_category_slug );
			$permalinks['tag_base'] 		= untrailingslashit( $woocommerce_product_tag_slug );
			$permalinks['attribute_base'] 	= untrailingslashit( $woocommerce_product_attribute_slug );

			// Product base
			$product_permalink = wc_clean( $_POST['product_permalink'] );

			if ( $product_permalink == 'custom' ) {
				// Get permalink without slashes
				$product_permalink = trim( wc_clean( $_POST['product_permalink_structure'] ), '/' );

				// This is an invalid base structure and breaks pages
				if ( '%product_cat%' == $product_permalink ) {
					$product_permalink = _x( 'product', 'slug', 'woocommerce' ) . '/' . $product_permalink;
				}

				// Prepending slash
				$product_permalink = '/' . $product_permalink;
			} elseif ( empty( $product_permalink ) ) {
				$product_permalink = false;
			}

			$permalinks['product_base'] = untrailingslashit( $product_permalink );

			update_option( 'woocommerce_permalinks', $permalinks );
		}
	}
}

endif;

return new WC_Admin_Permalink_Settings();