<?php
/**
 * Admin functions for post types
 *
 * @author 		WooThemes
 * @category 	Admin
 * @package 	WooCommerce/Admin/Post Types
 * @version     2.1.0
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( ! class_exists( 'WC_Admin_CPT' ) ) :

/**
 * WC_Admin_CPT Class
 */
class WC_Admin_CPT {

	protected $type = '';

	/**
	 * Constructor
	 */
	public function __construct() {
		// Insert into X media browser
		add_filter( 'media_view_strings', array( $this, 'change_insert_into_post' ) );
	}

	/**
	 * Change label for insert buttons.
	 * @access   public
	 * @param array $strings
	 * @return array
	 */
	function change_insert_into_post( $strings ) {
		global $post_type;

		if ( $post_type == $this->type ) {
			$obj = get_post_type_object( $this->type );

			$strings['insertIntoPost']     = sprintf( __( 'Insert into %s', 'woocommerce' ), $obj->labels->singular_name );
			$strings['uploadedToThisPost'] = sprintf( __( 'Uploaded to this %s', 'woocommerce' ), $obj->labels->singular_name );
		}

		return $strings;
	}
}

endif;