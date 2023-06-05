<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<h2>
	<a href="<?php echo admin_url( 'admin.php?page=wc-settings&tab=shipping' ); ?>"><?php _e( 'Shipping zones', 'woocommerce' ); ?></a> &gt;
	<a href="<?php echo admin_url( 'admin.php?page=wc-settings&tab=shipping&zone_id=' . absint( $zone->get_id() ) ); ?>"><?php echo esc_html( $zone->get_zone_name() ); ?></a> &gt;
	<?php echo esc_html( $shipping_method->get_method_title() ); ?>
</h2>

<?php $shipping_method->admin_options(); ?>
