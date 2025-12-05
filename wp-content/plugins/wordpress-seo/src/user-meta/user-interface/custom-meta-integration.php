<?php

namespace Yoast\WP\SEO\User_Meta\User_Interface;

use Yoast\WP\SEO\Conditionals\Admin_Conditional;
use Yoast\WP\SEO\Conditionals\User_Can_Edit_Users_Conditional;
use Yoast\WP\SEO\Conditionals\User_Edit_Conditional;
use Yoast\WP\SEO\Integrations\Integration_Interface;
use Yoast\WP\SEO\User_Meta\Application\Custom_Meta_Collector;

/**
 * Handles custom meta for users.
 */
class Custom_Meta_Integration implements Integration_Interface {

	/**
	 * The custom meta collector.
	 *
	 * @var Custom_Meta_Collector
	 */
	private $custom_meta_collector;

	/**
	 * The constructor.
	 *
	 * @param Custom_Meta_Collector $custom_meta_collector The custom meta collector.
	 */
	public function __construct( Custom_Meta_Collector $custom_meta_collector ) {
		$this->custom_meta_collector = $custom_meta_collector;
	}

	/**
	 * Retrieves the conditionals for the integration.
	 *
	 * @return array<Yoast\WP\SEO\Conditionals> The conditionals.
	 */
	public static function get_conditionals() {
		return [
			Admin_Conditional::class,
			User_Can_Edit_Users_Conditional::class,
			User_Edit_Conditional::class,
		];
	}

	/**
	 * Registers action hook.
	 *
	 * @return void
	 */
	public function register_hooks(): void {
		\add_action( 'show_user_profile', [ $this, 'user_profile' ] );
		\add_action( 'edit_user_profile', [ $this, 'user_profile' ] );

		\add_action( 'personal_options_update', [ $this, 'process_user_option_update' ] );
		\add_action( 'edit_user_profile_update', [ $this, 'process_user_option_update' ] );
	}

	/**
	 * Updates the user metas that (might) have been set on the user profile page.
	 *
	 * @param int $user_id User ID of the updated user.
	 *
	 * @return void
	 */
	public function process_user_option_update( $user_id ) {
		\update_user_meta( $user_id, '_yoast_wpseo_profile_updated', \time() );

		if ( ! \check_admin_referer( 'wpseo_user_profile_update', 'wpseo_nonce' ) ) {
			return;
		}

		foreach ( $this->custom_meta_collector->get_custom_meta() as $meta ) {
			if ( ! $meta->is_setting_enabled() ) {
				continue;
			}

			$meta_field_id       = $meta->get_field_id();
			$user_input_to_store = isset( $_POST[ $meta_field_id ] ) ? \sanitize_text_field( \wp_unslash( $_POST[ $meta_field_id ] ) ) : '';

			if ( $meta->is_empty_allowed() || $user_input_to_store !== '' ) {
				\update_user_meta( $user_id, $meta->get_key(), $user_input_to_store );
				continue;
			}

			\delete_user_meta( $user_id, $meta->get_key() );
		}
	}

	/**
	 * Adds the inputs needed for SEO values to the User Profile page.
	 *
	 * @param WP_User $user User instance to output for.
	 *
	 * @return void
	 */
	public function user_profile( $user ) {
		\wp_nonce_field( 'wpseo_user_profile_update', 'wpseo_nonce' );

		/* translators: %1$s expands to Yoast SEO */
		$yoast_user_settings_header = \sprintf( \__( '%1$s settings', 'wordpress-seo' ), 'Yoast SEO' );

		echo '

		<div class="yoast yoast-settings">
			
			<h2 id="wordpress-seo">' . \esc_html( $yoast_user_settings_header ) . '</h2>';

		foreach ( $this->custom_meta_collector->get_sorted_custom_meta() as $meta ) {
			if ( ! $meta->is_setting_enabled() ) {
				continue;
			}

			$meta->render_field( $user->ID );
		}

		\do_action( 'wpseo_render_user_profile', $user );
		echo '</div>';
	}
}
