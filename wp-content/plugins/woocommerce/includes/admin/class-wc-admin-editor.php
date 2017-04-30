<?php
/**
 * Admin Editor
 *
 * Methods which tweak the WP Editor.
 *
 * @author 		WooThemes
 * @category 	Admin
 * @package 	WooCommerce/Admin
 * @version     2.1.0
*/

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * WC_Admin_Editor class.
 *
 * @since 2.0
 */
class WC_Admin_Editor {

	/**
	 * Constructor
	 */
	public function __construct() {
		add_action( 'admin_head', array( $this, 'add_shortcode_button' ) );
		add_filter( 'tiny_mce_version', array( $this, 'refresh_mce' ) );
		add_filter( 'mce_external_languages', array( $this, 'add_tinymce_lang' ), 10, 1 );
	}

	/**
	 * Add a button for shortcodes to the WP editor.
	 */
	public function add_shortcode_button() {
		if ( ! current_user_can( 'edit_posts' ) && ! current_user_can( 'edit_pages' ) ) {
			return;
		}

		if ( 'true' == get_user_option( 'rich_editing' ) ) {
			add_filter( 'mce_external_plugins', array( $this, 'add_shortcode_tinymce_plugin' ) );
			add_filter( 'mce_buttons', array( $this, 'register_shortcode_button' ) );
		}
	}

	/**
	 * woocommerce_add_tinymce_lang function.
	 *
	 * @param array $arr
	 * @return array
	 */
	public function add_tinymce_lang( $arr ) {
	    $arr['wc_shortcodes_button'] = WC()->plugin_path() . '/assets/js/admin/editor_plugin_lang.php';
	    return $arr;
	}

	/**
	 * Register the shortcode button.
	 *
	 * @param array $buttons
	 * @return array
	 */
	public function register_shortcode_button( $buttons ) {
		array_push( $buttons, '|', 'wc_shortcodes_button' );
		return $buttons;
	}

	/**
	 * Add the shortcode button to TinyMCE
	 *
	 * @param array $plugin_array
	 * @return array
	 */
	public function add_shortcode_tinymce_plugin( $plugin_array ) {
		$wp_version = get_bloginfo( 'version' );
		$suffix     = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

		if ( version_compare( $wp_version, '3.9', '>=' ) ) {
			$plugin_array['wc_shortcodes_button'] = WC()->plugin_url() . '/assets/js/admin/editor_plugin' . $suffix . '.js';
		} else {
			$plugin_array['wc_shortcodes_button'] = WC()->plugin_url() . '/assets/js/admin/editor_plugin_legacy' . $suffix . '.js';
		}

		return $plugin_array;
	}

	/**
	 * Force TinyMCE to refresh.
	 *
	 * @param int $ver
	 * @return int
	 */
	public function refresh_mce( $ver ) {
		$ver += 3;
		return $ver;
	}

}

new WC_Admin_Editor();
