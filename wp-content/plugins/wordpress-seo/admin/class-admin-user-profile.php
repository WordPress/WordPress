<?php
/**
 * WPSEO plugin file.
 *
 * @package WPSEO\Admin
 * @since   1.8.0
 */

/**
 * Customizes user profile.
 */
class WPSEO_Admin_User_Profile {

	/**
	 * Class constructor.
	 */
	public function __construct() {
		add_action( 'update_user_meta', [ $this, 'clear_author_sitemap_cache' ], 10, 3 );
	}

	/**
	 * Clear author sitemap cache when settings are changed.
	 *
	 * @since 3.1
	 *
	 * @param int    $meta_id   The ID of the meta option changed.
	 * @param int    $object_id The ID of the user.
	 * @param string $meta_key  The key of the meta field changed.
	 *
	 * @return void
	 */
	public function clear_author_sitemap_cache( $meta_id, $object_id, $meta_key ) {
		if ( $meta_key === '_yoast_wpseo_profile_updated' ) {
			WPSEO_Sitemaps_Cache::clear( [ 'author' ] );
		}
	}

	/**
	 * Updates the user metas that (might) have been set on the user profile page.
	 *
	 * @deprecated 22.6
	 * @codeCoverageIgnore
	 *
	 * @param int $user_id User ID of the updated user.
	 *
	 * @return void
	 */
	public function process_user_option_update( $user_id ) {
		_deprecated_function( __METHOD__, 'Yoast SEO 22.6' );

		update_user_meta( $user_id, '_yoast_wpseo_profile_updated', time() );

		if ( ! check_admin_referer( 'wpseo_user_profile_update', 'wpseo_nonce' ) ) {
			return;
		}

		$wpseo_author_title                        = isset( $_POST['wpseo_author_title'] ) ? sanitize_text_field( wp_unslash( $_POST['wpseo_author_title'] ) ) : '';
		$wpseo_author_metadesc                     = isset( $_POST['wpseo_author_metadesc'] ) ? sanitize_text_field( wp_unslash( $_POST['wpseo_author_metadesc'] ) ) : '';
		$wpseo_author_pronouns                     = isset( $_POST['wpseo_author_pronouns'] ) ? sanitize_text_field( wp_unslash( $_POST['wpseo_author_pronouns'] ) ) : '';
		$wpseo_noindex_author                      = isset( $_POST['wpseo_noindex_author'] ) ? sanitize_text_field( wp_unslash( $_POST['wpseo_noindex_author'] ) ) : '';
		$wpseo_content_analysis_disable            = isset( $_POST['wpseo_content_analysis_disable'] ) ? sanitize_text_field( wp_unslash( $_POST['wpseo_content_analysis_disable'] ) ) : '';
		$wpseo_keyword_analysis_disable            = isset( $_POST['wpseo_keyword_analysis_disable'] ) ? sanitize_text_field( wp_unslash( $_POST['wpseo_keyword_analysis_disable'] ) ) : '';
		$wpseo_inclusive_language_analysis_disable = isset( $_POST['wpseo_inclusive_language_analysis_disable'] ) ? sanitize_text_field( wp_unslash( $_POST['wpseo_inclusive_language_analysis_disable'] ) ) : '';

		update_user_meta( $user_id, 'wpseo_title', $wpseo_author_title );
		update_user_meta( $user_id, 'wpseo_metadesc', $wpseo_author_metadesc );
		update_user_meta( $user_id, 'wpseo_pronouns', $wpseo_author_pronouns );
		update_user_meta( $user_id, 'wpseo_noindex_author', $wpseo_noindex_author );
		update_user_meta( $user_id, 'wpseo_content_analysis_disable', $wpseo_content_analysis_disable );
		update_user_meta( $user_id, 'wpseo_keyword_analysis_disable', $wpseo_keyword_analysis_disable );
		update_user_meta( $user_id, 'wpseo_inclusive_language_analysis_disable', $wpseo_inclusive_language_analysis_disable );
	}
}
