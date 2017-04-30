<?php
/**
 * Add some content to the help tab.
 *
 * @author 		WooThemes
 * @category 	Admin
 * @package 	WooCommerce/Admin
 * @version     2.1.0
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( ! class_exists( 'WC_Admin_Help' ) ) :

/**
 * WC_Admin_Help Class
 */
class WC_Admin_Help {

	/**
	 * Hook in tabs.
	 */
	public function __construct() {
		add_action( "current_screen", array( $this, 'add_tabs' ), 50 );
	}

	/**
	 * Add help tabs
	 */
	public function add_tabs() {
		$screen = get_current_screen();

		if ( ! in_array( $screen->id, wc_get_screen_ids() ) )
			return;

		$screen->add_help_tab( array(
		    'id'	=> 'woocommerce_docs_tab',
		    'title'	=> __( 'Documentation', 'woocommerce' ),
		    'content'	=>

		    	'<p>' . __( 'Thank you for using WooCommerce :) Should you need help using or extending WooCommerce please read the documentation.', 'woocommerce' ) . '</p>' .

		    	'<p><a href="' . 'http://docs.woothemes.com/documentation/plugins/woocommerce/' . '" class="button button-primary">' . __( 'WooCommerce Documentation', 'woocommerce' ) . '</a> <a href="' . 'http://docs.woothemes.com/wc-apidocs/' . '" class="button">' . __( 'Developer API Docs', 'woocommerce' ) . '</a></p>'

		) );

		$screen->add_help_tab( array(
		    'id'	=> 'woocommerce_support_tab',
		    'title'	=> __( 'Support', 'woocommerce' ),
		    'content'	=>

		    	'<p>' . sprintf(__( 'After <a href="%s">reading the documentation</a>, for further assistance you can use the <a href="%s">community forum</a>, or if you have access as a WooThemes customer, <a href="%s">our support desk</a>.', 'woocommerce' ), 'http://docs.woothemes.com/documentation/plugins/woocommerce/', 'http://wordpress.org/support/plugin/woocommerce', 'http://support.woothemes.com' ) . '</p>' .

		    	'<p>' . __( 'Before asking for help we recommend checking the status page to identify any problems with your configuration.', 'woocommerce' ) . '</p>' .

		    	'<p><a href="' . admin_url('admin.php?page=wc-status') . '" class="button button-primary">' . __( 'System Status', 'woocommerce' ) . '</a> <a href="' . 'http://wordpress.org/support/plugin/woocommerce' . '" class="button">' . __( 'Community Support', 'woocommerce' ) . '</a> <a href="' . 'http://support.woothemes.com' . '" class="button">' . __( 'Customer Support', 'woocommerce' ) . '</a></p>'

		) );

		$screen->add_help_tab( array(
		    'id'	=> 'woocommerce_bugs_tab',
		    'title'	=> __( 'Found a bug?', 'woocommerce' ),
		    'content'	=>

		    	'<p>' . sprintf(__( 'If you find a bug within WooCommerce core you can create a ticket via <a href="%s">Github issues</a>. Ensure you read the <a href="%s">contribution guide</a> prior to submitting your report. Be as descriptive as possible and please include your <a href="%s">system status report</a>.', 'woocommerce' ), 'https://github.com/woothemes/woocommerce/issues?state=open', 'https://github.com/woothemes/woocommerce/blob/master/CONTRIBUTING.md', admin_url( 'admin.php?page=wc-status' ) ) . '</p>' .

		    	'<p><a href="https://github.com/woothemes/woocommerce/issues?state=open" class="button button-primary">' . __( 'Report a bug', 'woocommerce' ) . '</a> <a href="' . admin_url('admin.php?page=wc-status') . '" class="button">' . __( 'System Status', 'woocommerce' ) . '</a></p>'

		) );


		$screen->set_help_sidebar(
			'<p><strong>' . __( 'For more information:', 'woocommerce' ) . '</strong></p>' .
			'<p><a href="http://www.woothemes.com/woocommerce/" target="_blank">' . __( 'About WooCommerce', 'woocommerce' ) . '</a></p>' .
			'<p><a href="http://wordpress.org/extend/plugins/woocommerce/" target="_blank">' . __( 'Project on WordPress.org', 'woocommerce' ) . '</a></p>' .
			'<p><a href="https://github.com/woothemes/woocommerce" target="_blank">' . __( 'Project on Github', 'woocommerce' ) . '</a></p>' .
			'<p><a href="http://www.woothemes.com/product-category/woocommerce-extensions/" target="_blank">' . __( 'Official Extensions', 'woocommerce' ) . '</a></p>' .
			'<p><a href="http://www.woothemes.com/product-category/themes/woocommerce/" target="_blank">' . __( 'Official Themes', 'woocommerce' ) . '</a></p>'
		);
	}

}

endif;

return new WC_Admin_Help();