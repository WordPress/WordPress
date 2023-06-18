<?php
/**
 * Displays the attributes tab in the product data meta box.
 *
 * @package WooCommerce\Admin
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

global $wc_product_attributes;
// Array of defined attribute taxonomies.
$attribute_taxonomies = wc_get_attribute_taxonomies();
// Product attributes - taxonomies and custom, ordered, with visibility and variation attributes set.
$product_attributes = $product_object->get_attributes( 'edit' );
?>
<div id="product_attributes" class="panel wc-metaboxes-wrapper hidden">
	<div class="toolbar toolbar-top">
		<div id="message" class="inline notice woocommerce-message">
			<p>
				<?php
				esc_html_e(
					'Add descriptive pieces of information that customers can use to search for this product on your store, such as “Material” or “Brand”.',
					'woocommerce'
				);
				?>
			</p>
		</div>
		<span class="expand-close">
			<a href="#" class="expand_all"><?php esc_html_e( 'Expand', 'woocommerce' ); ?></a> / <a href="#" class="close_all"><?php esc_html_e( 'Close', 'woocommerce' ); ?></a>
		</span>
		<div class="actions">
			<button type="button" class="button add_custom_attribute"><?php esc_html_e( 'Add new', 'woocommerce' ); ?></button>
			<select class="wc-attribute-search" data-placeholder="<?php esc_attr_e( 'Add existing', 'woocommerce' ); ?>" data-minimum-input-length="0">
			</select>
		</div>
	</div>
	<div class="product_attributes wc-metaboxes">
		<?php
		$i = -1;

		foreach ( $product_attributes as $attribute ) {
			$i++;
			$metabox_class = array();

			if ( $attribute->is_taxonomy() ) {
				$metabox_class[] = 'taxonomy';
				$metabox_class[] = $attribute->get_name();
			}

			include __DIR__ . '/html-product-attribute.php';
		}
		?>
	</div>
	<div class="toolbar toolbar-buttons">
		<span class="expand-close">
			<a href="#" class="expand_all"><?php esc_html_e( 'Expand', 'woocommerce' ); ?></a> / <a href="#" class="close_all"><?php esc_html_e( 'Close', 'woocommerce' ); ?></a>
		</span>
		<button type="button" aria-disabled="true" class="button save_attributes button-primary disabled"><?php esc_html_e( 'Save attributes', 'woocommerce' ); ?></button>
	</div>
	<?php do_action( 'woocommerce_product_options_attributes' ); ?>
</div>
