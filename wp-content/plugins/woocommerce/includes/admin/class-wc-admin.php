<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * WooCommerce Admin.
 *
 * @class 		WC_Admin 
 * @author 		WooThemes
 * @category 	Admin
 * @package 	WooCommerce/Admin
 * @version     2.1.0
 */
class WC_Admin {

	/**
	 * Constructor
	 */
	public function __construct() {
		add_action( 'init', array( $this, 'includes' ) );
		add_action( 'current_screen', array( $this, 'conditonal_includes' ) );
		add_action( 'admin_init', array( $this, 'prevent_admin_access' ) );
		add_action( 'wp_ajax_page_slurp', array( 'WC_Gateway_Mijireh', 'page_slurp' ) );
		add_action( 'admin_init', array( $this, 'preview_emails' ) );
		add_action( 'admin_footer', 'wc_print_js', 25 );
	}

	/**
	 * Include any classes we need within admin.
	 */
	public function includes() {
		// Functions
		include_once( 'wc-admin-functions.php' );
		include_once( 'wc-meta-box-functions.php' );

		// Classes
		include_once( 'class-wc-admin-post-types.php' );
		include_once( 'class-wc-admin-taxonomies.php' );

		// Classes we only need if the ajax is not-ajax
		if ( ! is_ajax() ) {
			include( 'class-wc-admin-menus.php' );
			include( 'class-wc-admin-welcome.php' );
			include( 'class-wc-admin-notices.php' );
			include( 'class-wc-admin-assets.php' );
			include( 'class-wc-admin-permalink-settings.php' );
			include( 'class-wc-admin-editor.php' );

			// Help
			if ( apply_filters( 'woocommerce_enable_admin_help_tab', true ) )
				include( 'class-wc-admin-help.php' );
		}

		// Importers
		if ( defined( 'WP_LOAD_IMPORTERS' ) )
			include( 'class-wc-admin-importers.php' );
	}

	/**
	 * Include admin files conditionally
	 */
	public function conditonal_includes() {
		$screen = get_current_screen();

		switch ( $screen->id ) {
			case 'dashboard' :
				include( 'class-wc-admin-dashboard.php' );
			break;
			case 'users' :
			case 'user' :
			case 'profile' :
			case 'user-edit' :
				include( 'class-wc-admin-profile.php' );
			break;
		}
	}

	/**
	 * Prevent any user who cannot 'edit_posts' (subscribers, customers etc) from accessing admin
	 */
	public function prevent_admin_access() {
		$prevent_access = false;

		if ( 'yes' == get_option( 'woocommerce_lock_down_admin' ) && ! is_ajax() && ! ( current_user_can( 'edit_posts' ) || current_user_can( 'manage_woocommerce' ) ) && basename( $_SERVER["SCRIPT_FILENAME"] ) !== 'admin-post.php' ) {
			$prevent_access = true;
		}

		$prevent_access = apply_filters( 'woocommerce_prevent_admin_access', $prevent_access );

		if ( $prevent_access ) {
			wp_safe_redirect( get_permalink( wc_get_page_id( 'myaccount' ) ) );
			exit;
		}
	}

	/**
	 * Preview email template
	 * @return [type]
	 */
	public function preview_emails() {
		if ( isset( $_GET['preview_woocommerce_mail'] ) ) {
			if ( ! wp_verify_nonce( $_REQUEST['_wpnonce'], 'preview-mail') )
				die( 'Security check' );

			global $email_heading;

			ob_start();

			include( 'views/html-email-template-preview.php' );

			$mailer        = WC()->mailer();
			$message       = ob_get_clean();
			$email_heading = __( 'HTML Email Template', 'woocommerce' );

			echo $mailer->wrap_message( $email_heading, $message );
			exit;
		}
	}
}

return new WC_Admin();