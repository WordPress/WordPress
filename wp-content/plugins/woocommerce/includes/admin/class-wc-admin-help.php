<?php
/**
 * Add some content to the help tab
 *
 * @package     WooCommerce\Admin
 * @version     2.1.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( class_exists( 'WC_Admin_Help', false ) ) {
	return new WC_Admin_Help();
}

/**
 * WC_Admin_Help Class.
 */
class WC_Admin_Help {

	/**
	 * Hook in tabs.
	 */
	public function __construct() {
		add_action( 'current_screen', array( $this, 'add_tabs' ), 50 );
	}

	/**
	 * Add help tabs.
	 */
	public function add_tabs() {
		$screen = get_current_screen();

		if ( ! $screen || ! in_array( $screen->id, wc_get_screen_ids() ) ) {
			return;
		}

		$screen->add_help_tab(
			array(
				'id'      => 'woocommerce_support_tab',
				'title'   => __( 'Help &amp; Support', 'woocommerce' ),
				'content' =>
					'<h2>' . __( 'Help &amp; Support', 'woocommerce' ) . '</h2>' .
					'<p>' . sprintf(
						/* translators: %s: Documentation URL */
						__( 'Should you need help understanding, using, or extending WooCommerce, <a href="%s">please read our documentation</a>. You will find all kinds of resources including snippets, tutorials and much more.', 'woocommerce' ),
						'https://docs.woocommerce.com/documentation/plugins/woocommerce/?utm_source=helptab&utm_medium=product&utm_content=docs&utm_campaign=woocommerceplugin'
					) . '</p>' .
					'<p>' . sprintf(
						/* translators: %s: Forum URL */
						__( 'For further assistance with WooCommerce core, use the <a href="%1$s">community forum</a>. For help with premium extensions sold on WooCommerce.com, <a href="%2$s">open a support request at WooCommerce.com</a>.', 'woocommerce' ),
						'https://wordpress.org/support/plugin/woocommerce',
						'https://woocommerce.com/my-account/create-a-ticket/?utm_source=helptab&utm_medium=product&utm_content=tickets&utm_campaign=woocommerceplugin'
					) . '</p>' .
					'<p>' . __( 'Before asking for help, we recommend checking the system status page to identify any problems with your configuration.', 'woocommerce' ) . '</p>' .
					'<p><a href="' . admin_url( 'admin.php?page=wc-status' ) . '" class="button button-primary">' . __( 'System status', 'woocommerce' ) . '</a> <a href="https://wordpress.org/support/plugin/woocommerce" class="button">' . __( 'Community forum', 'woocommerce' ) . '</a> <a href="https://woocommerce.com/my-account/create-a-ticket/?utm_source=helptab&utm_medium=product&utm_content=tickets&utm_campaign=woocommerceplugin" class="button">' . __( 'WooCommerce.com support', 'woocommerce' ) . '</a></p>',
			)
		);

		$screen->add_help_tab(
			array(
				'id'      => 'woocommerce_bugs_tab',
				'title'   => __( 'Found a bug?', 'woocommerce' ),
				'content' =>
					'<h2>' . __( 'Found a bug?', 'woocommerce' ) . '</h2>' .
					/* translators: 1: GitHub issues URL 2: GitHub contribution guide URL 3: System status report URL */
					'<p>' . sprintf( __( 'If you find a bug within WooCommerce core you can create a ticket via <a href="%1$s">GitHub issues</a>. Ensure you read the <a href="%2$s">contribution guide</a> prior to submitting your report. To help us solve your issue, please be as descriptive as possible and include your <a href="%3$s">system status report</a>.', 'woocommerce' ), 'https://github.com/woocommerce/woocommerce/issues?state=open', 'https://github.com/woocommerce/woocommerce/blob/trunk/.github/CONTRIBUTING.md', admin_url( 'admin.php?page=wc-status' ) ) . '</p>' .
					'<p><a href="https://github.com/woocommerce/woocommerce/issues/new?assignees=&labels=&template=1-bug-report.yml" class="button button-primary">' . __( 'Report a bug', 'woocommerce' ) . '</a> <a href="' . admin_url( 'admin.php?page=wc-status' ) . '" class="button">' . __( 'System status', 'woocommerce' ) . '</a></p>',

			)
		);

		$screen->set_help_sidebar(
			'<p><strong>' . __( 'For more information:', 'woocommerce' ) . '</strong></p>' .
			'<p><a href="https://woocommerce.com/?utm_source=helptab&utm_medium=product&utm_content=about&utm_campaign=woocommerceplugin" target="_blank">' . __( 'About WooCommerce', 'woocommerce' ) . '</a></p>' .
			'<p><a href="https://wordpress.org/plugins/woocommerce/" target="_blank">' . __( 'WordPress.org project', 'woocommerce' ) . '</a></p>' .
			'<p><a href="https://github.com/woocommerce/woocommerce/" target="_blank">' . __( 'GitHub project', 'woocommerce' ) . '</a></p>' .
			'<p><a href="https://woocommerce.com/storefront/?utm_source=helptab&utm_medium=product&utm_content=wcthemes&utm_campaign=woocommerceplugin" target="_blank">' . __( 'Official theme', 'woocommerce' ) . '</a></p>' .
			'<p><a href="https://woocommerce.com/product-category/woocommerce-extensions/?utm_source=helptab&utm_medium=product&utm_content=wcextensions&utm_campaign=woocommerceplugin" target="_blank">' . __( 'Official extensions', 'woocommerce' ) . '</a></p>'
		);
	}
}

return new WC_Admin_Help();
