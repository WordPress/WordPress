<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
?>
<div id="message" class="updated woocommerce-message wc-connect">
	<p><?php _e( '<strong>Welcome to WooCommerce</strong> &#8211; You\'re almost ready to start selling :)', 'woocommerce' ); ?></p>
	<p class="submit"><a href="<?php echo add_query_arg('install_woocommerce_pages', 'true', admin_url('admin.php?page=wc-settings') ); ?>" class="button-primary"><?php _e( 'Install WooCommerce Pages', 'woocommerce' ); ?></a> <a class="skip button-primary" href="<?php echo add_query_arg('skip_install_woocommerce_pages', 'true', admin_url('admin.php?page=wc-settings') ); ?>"><?php _e( 'Skip setup', 'woocommerce' ); ?></a></p>
</div>