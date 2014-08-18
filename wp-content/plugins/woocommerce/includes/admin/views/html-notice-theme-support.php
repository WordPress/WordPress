<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
?>
<div id="message" class="updated woocommerce-message wc-connect">
	<p><?php _e( '<strong>Your theme does not declare WooCommerce support</strong> &#8211; if you encounter layout issues please read our integration guide or choose a WooCommerce theme :)', 'woocommerce' ); ?></p>
	<p class="submit"><a href="<?php echo esc_url( apply_filters( 'woocommerce_docs_url', 'http://docs.woothemes.com/document/third-party-custom-theme-compatibility/', 'theme-compatibility' ) ); ?>" class="button-primary"><?php _e( 'Theme Integration Guide', 'woocommerce' ); ?></a> <a class="skip button-primary" href="<?php echo esc_url( add_query_arg( 'hide_theme_support_notice', 'true' ) ); ?>"><?php _e( 'Hide this notice', 'woocommerce' ); ?></a></p>
</div>