<?php

namespace Yoast\WP\SEO\Actions\Configuration;

use Yoast\WP\SEO\Helpers\Options_Helper;
use Yoast\WP\SEO\Helpers\Social_Profiles_Helper;

/**
 * Class First_Time_Configuration_Action.
 */
class First_Time_Configuration_Action {

	/**
	 * The fields for the site representation payload.
	 */
	public const SITE_REPRESENTATION_FIELDS = [
		'company_or_person',
		'company_name',
		'website_name',
		'company_logo',
		'company_logo_id',
		'person_logo',
		'person_logo_id',
		'company_or_person_user_id',
		'description',
	];

	/**
	 * The Options_Helper instance.
	 *
	 * @var Options_Helper
	 */
	protected $options_helper;

	/**
	 * The Social_Profiles_Helper instance.
	 *
	 * @var Social_Profiles_Helper
	 */
	protected $social_profiles_helper;

	/**
	 * First_Time_Configuration_Action constructor.
	 *
	 * @param Options_Helper         $options_helper         The WPSEO options helper.
	 * @param Social_Profiles_Helper $social_profiles_helper The social profiles helper.
	 */
	public function __construct( Options_Helper $options_helper, Social_Profiles_Helper $social_profiles_helper ) {
		$this->options_helper         = $options_helper;
		$this->social_profiles_helper = $social_profiles_helper;
	}

	/**
	 * Stores the values for the site representation.
	 *
	 * @param array $params The values to store.
	 *
	 * @return object The response object.
	 */
	public function set_site_representation( $params ) {
		$failures   = [];
		$old_values = $this->get_old_values( self::SITE_REPRESENTATION_FIELDS );

		foreach ( self::SITE_REPRESENTATION_FIELDS as $field_name ) {
			if ( isset( $params[ $field_name ] ) ) {
				$result = $this->options_helper->set( $field_name, $params[ $field_name ] );

				if ( ! $result ) {
					$failures[] = $field_name;
				}
			}
		}

		// Delete cached logos in the db.
		$this->options_helper->set( 'company_logo_meta', false );
		$this->options_helper->set( 'person_logo_meta', false );

		/**
		 * Action: 'wpseo_post_update_site_representation' - Allows for Hiive event tracking.
		 *
		 * @param array $params     The new values of the options.
		 * @param array $old_values The old values of the options.
		 * @param array $failures   The options that failed to be saved.
		 *
		 * @internal
		 */
		\do_action( 'wpseo_ftc_post_update_site_representation', $params, $old_values, $failures );

		if ( \count( $failures ) === 0 ) {
			return (object) [
				'success' => true,
				'status'  => 200,
			];
		}

		return (object) [
			'success'  => false,
			'status'   => 500,
			'error'    => 'Could not save some options in the database',
			'failures' => $failures,
		];
	}

	/**
	 * Stores the values for the social profiles.
	 *
	 * @param array $params The values to store.
	 *
	 * @return object The response object.
	 */
	public function set_social_profiles( $params ) {
		$old_values = $this->get_old_values( \array_keys( $this->social_profiles_helper->get_organization_social_profile_fields() ) );
		$failures   = $this->social_profiles_helper->set_organization_social_profiles( $params );

		/**
		 * Action: 'wpseo_post_update_social_profiles' - Allows for Hiive event tracking.
		 *
		 * @param array $params     The new values of the options.
		 * @param array $old_values The old values of the options.
		 * @param array $failures   The options that failed to be saved.
		 *
		 * @internal
		 */
		\do_action( 'wpseo_ftc_post_update_social_profiles', $params, $old_values, $failures );

		if ( empty( $failures ) ) {
			return (object) [
				'success' => true,
				'status'  => 200,
			];
		}

		return (object) [
			'success'  => false,
			'status'   => 200,
			'error'    => 'Could not save some options in the database',
			'failures' => $failures,
		];
	}

	/**
	 * Stores the values for the social profiles.
	 *
	 * @param array $params The values to store.
	 *
	 * @return object The response object.
	 */
	public function set_person_social_profiles( $params ) {
		$social_profiles = \array_filter(
			$params,
			static function ( $key ) {
				return $key !== 'user_id';
			},
			\ARRAY_FILTER_USE_KEY
		);

		$failures = $this->social_profiles_helper->set_person_social_profiles( $params['user_id'], $social_profiles );

		if ( \count( $failures ) === 0 ) {
			return (object) [
				'success' => true,
				'status'  => 200,
			];
		}

		return (object) [
			'success'  => false,
			'status'   => 200,
			'error'    => 'Could not save some options in the database',
			'failures' => $failures,
		];
	}

	/**
	 * Gets the values for the social profiles.
	 *
	 * @param int $user_id The person ID.
	 *
	 * @return object The response object.
	 */
	public function get_person_social_profiles( $user_id ) {

		return (object) [
			'success'         => true,
			'status'          => 200,
			'social_profiles' => $this->social_profiles_helper->get_person_social_profiles( $user_id ),
		];
	}

	/**
	 * Stores the values to enable/disable tracking.
	 *
	 * @param array $params The values to store.
	 *
	 * @return object The response object.
	 */
	public function set_enable_tracking( $params ) {
		$success      = true;
		$option_value = $this->options_helper->get( 'tracking' );

		if ( $option_value !== $params['tracking'] ) {
			$this->options_helper->set( 'toggled_tracking', true );
			$success = $this->options_helper->set( 'tracking', $params['tracking'] );
		}

		/**
		 * Action: 'wpseo_post_update_enable_tracking' - Allows for Hiive event tracking.
		 *
		 * @param array $new_value The new value.
		 * @param array $old_value The old value.
		 * @param bool  $failure   Whether the option failed to be stored.
		 *
		 * @internal
		 */
		// $success is negated to be aligned with the other two actions which pass $failures.
		\do_action( 'wpseo_ftc_post_update_enable_tracking', $params['tracking'], $option_value, ! $success );

		if ( $success ) {
			return (object) [
				'success' => true,
				'status'  => 200,
			];
		}

		return (object) [
			'success' => false,
			'status'  => 500,
			'error'   => 'Could not save the option in the database',
		];
	}

	/**
	 * Checks if the current user has the capability a specific user.
	 *
	 * @param int $user_id The id of the user to be edited.
	 *
	 * @return object The response object.
	 */
	public function check_capability( $user_id ) {
		if ( $this->can_edit_profile( $user_id ) ) {
			return (object) [
				'success' => true,
				'status'  => 200,
			];
		}

		return (object) [
			'success' => false,
			'status'  => 403,
		];
	}

	/**
	 * Stores the first time configuration state.
	 *
	 * @param array $params The values to store.
	 *
	 * @return object The response object.
	 */
	public function save_configuration_state( $params ) {
		// If the finishedSteps param is not present in the REST request, it's a malformed request.
		if ( ! isset( $params['finishedSteps'] ) ) {
			return (object) [
				'success' => false,
				'status'  => 400,
				'error'   => 'Bad request',
			];
		}

		// Sanitize input.
		$finished_steps = \array_map( '\sanitize_text_field', \wp_unslash( $params['finishedSteps'] ) );

		$success = $this->options_helper->set( 'configuration_finished_steps', $finished_steps );

		if ( ! $success ) {
			return (object) [
				'success' => false,
				'status'  => 500,
				'error'   => 'Could not save the option in the database',
			];
		}

		// If all the five steps of the configuration have been completed, set first_time_install option to false.
		if ( \count( $params['finishedSteps'] ) === 3 ) {
			$this->options_helper->set( 'first_time_install', false );
		}

		return (object) [
			'success' => true,
			'status'  => 200,
		];
	}

	/**
	 * Gets the first time configuration state.
	 *
	 * @return object The response object.
	 */
	public function get_configuration_state() {
		$configuration_option = $this->options_helper->get( 'configuration_finished_steps' );

		if ( $configuration_option !== null ) {
			return (object) [
				'success' => true,
				'status'  => 200,
				'data'    => $configuration_option,
			];
		}

		return (object) [
			'success' => false,
			'status'  => 500,
			'error'   => 'Could not get data from the database',
		];
	}

	/**
	 * Checks if the current user has the capability to edit a specific user.
	 *
	 * @param int $person_id The id of the person to edit.
	 *
	 * @return bool
	 */
	private function can_edit_profile( $person_id ) {
		return \current_user_can( 'edit_user', $person_id );
	}

	/**
	 * Gets the old values for the given fields.
	 *
	 * @param array $fields_names The fields to get the old values for.
	 *
	 * @return array The old values.
	 */
	private function get_old_values( array $fields_names ): array {
		$old_values = [];

		foreach ( $fields_names as $field_name ) {
			$old_values[ $field_name ] = $this->options_helper->get( $field_name );
		}

		return $old_values;
	}
}
