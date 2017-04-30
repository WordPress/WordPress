<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
?>
<div id="message" class="updated woocommerce-message wc-connect">
	<p><?php _e( '<strong>Your theme has bundled outdated copies of WooCommerce template files</strong> &#8211; if you encounter functionality issues on the frontend this could the reason. Ensure you update or remove them (in general we recommend only bundling the template files you actually need to customize). See the system report for full details.', 'woocommerce' ); ?></p>
	<p class="submit"><a class="button-primary" href="<?php echo esc_url( admin_url( 'admin.php?page=wc-status' ) ); ?>"><?php _e( 'System Status', 'woocommerce' ); ?></a> <a class="skip button-primary" href="<?php echo esc_url( add_query_arg( 'hide_template_files_notice', 'true' ) ); ?>"><?php _e( 'Hide this notice', 'woocommerce' ); ?></a></p>
</div>