<?php
/**
 * @package WPSEO\Admin
 * @since      1.8.0
 */

/**
 * Customizes user profile.
 */
class WPSEO_Admin_User_Profile {
	/**
	 * Class constructor
	 */
	public function __construct() {
		add_action( 'show_user_profile', array( $this, 'user_profile' ) );
		add_action( 'edit_user_profile', array( $this, 'user_profile' ) );
		add_action( 'personal_options_update', array( $this, 'process_user_option_update' ) );
		add_action( 'edit_user_profile_update', array( $this, 'process_user_option_update' ) );
	}

	/**
	 * Filter POST variables.
	 *
	 * @param string $var_name
	 *
	 * @return mixed
	 */
	private function filter_input_post( $var_name ) {
		$val = filter_input( INPUT_POST, $var_name );
		if ( $val ) {
			return WPSEO_Utils::sanitize_text_field( $val );
		}
		return '';
	}

	/**
	 * Updates the user metas that (might) have been set on the user profile page.
	 *
	 * @param    int $user_id of the updated user.
	 */
	public function process_user_option_update( $user_id ) {
		update_user_meta( $user_id, '_yoast_wpseo_profile_updated', time() );

		check_admin_referer( 'wpseo_user_profile_update', 'wpseo_nonce' );

		update_user_meta( $user_id, 'wpseo_title', $this->filter_input_post( 'wpseo_author_title' ) );
		update_user_meta( $user_id, 'wpseo_metadesc', $this->filter_input_post( 'wpseo_author_metadesc' ) );
		update_user_meta( $user_id, 'wpseo_metakey', $this->filter_input_post( 'wpseo_author_metakey' ) );
		update_user_meta( $user_id, 'wpseo_excludeauthorsitemap', $this->filter_input_post( 'wpseo_author_exclude' ) );
	}

	/**
	 * Add the inputs needed for SEO values to the User Profile page
	 *
	 * @param    object $user
	 */
	public function user_profile( $user ) {
		$options = WPSEO_Options::get_all();

		wp_nonce_field( 'wpseo_user_profile_update', 'wpseo_nonce' );

		require_once( 'views/user-profile.php' );
	}

}
