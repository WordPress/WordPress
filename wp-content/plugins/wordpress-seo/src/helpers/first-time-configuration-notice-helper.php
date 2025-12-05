<?php

namespace Yoast\WP\SEO\Helpers;

/**
 * A helper to determine the status of ftc and front-end related configuration.
 */
class First_Time_Configuration_Notice_Helper {

	/**
	 * The options' helper.
	 *
	 * @var Options_Helper
	 */
	private $options_helper;

	/**
	 * The indexing helper.
	 *
	 * @var Indexing_Helper
	 */
	private $indexing_helper;

	/**
	 * Whether we show the alternate mesage.
	 *
	 * @var bool
	 */
	private $show_alternate_message;

	/**
	 * First_Time_Configuration_Notice_Integration constructor.
	 *
	 * @param Options_Helper  $options_helper  The options helper.
	 * @param Indexing_Helper $indexing_helper The indexing helper.
	 */
	public function __construct( Options_Helper $options_helper, Indexing_Helper $indexing_helper ) {
		$this->options_helper         = $options_helper;
		$this->indexing_helper        = $indexing_helper;
		$this->show_alternate_message = false;
	}

	/**
	 * Determines whether and where the "First-time SEO Configuration" admin notice should be displayed.
	 *
	 * @return bool Whether the "First-time SEO Configuration" admin notice should be displayed.
	 */
	public function should_display_first_time_configuration_notice() {
		if ( ! $this->options_helper->get( 'dismiss_configuration_workout_notice', false ) === false ) {
			return false;
		}
		if ( ! $this->on_wpseo_admin_page_or_dashboard() ) {
			return false;
		}
		return $this->first_time_configuration_not_finished();
	}

	/**
	 * Gets the first time configuration title based on the show_alternate_message boolean
	 *
	 * @return string
	 */
	public function get_first_time_configuration_title() {
		return ( ! $this->show_alternate_message ) ? \__( 'First-time SEO configuration', 'wordpress-seo' ) : \__( 'SEO configuration', 'wordpress-seo' );
	}

	/**
	 * Determines if the first time configuration is completely finished.
	 *
	 * @return bool
	 */
	public function first_time_configuration_not_finished() {
		if ( ! $this->user_can_do_first_time_configuration() ) {
			return false;
		}

		if ( $this->is_first_time_configuration_finished() ) {
			return false;
		}

		if ( $this->options_helper->get( 'first_time_install', false ) !== false ) {
			return ! $this->are_site_representation_name_and_logo_set() || $this->indexing_helper->get_unindexed_count() > 0;
		}

		if ( $this->indexing_helper->is_initial_indexing() === false ) {
			return false;
		}

		if ( $this->indexing_helper->is_finished_indexables_indexing() === true ) {
			return false;
		}

		$this->show_alternate_message = true;

		return ! $this->are_site_representation_name_and_logo_set();
	}

	/**
	 * Whether the user can do the first-time configuration.
	 *
	 * @return bool Whether the current user can do the first-time configuration.
	 */
	private function user_can_do_first_time_configuration() {
		return \current_user_can( 'wpseo_manage_options' );
	}

	/**
	 * Whether the user is currently visiting one of our admin pages or the WordPress dashboard.
	 *
	 * @return bool Whether the current page is a Yoast SEO admin page
	 */
	private function on_wpseo_admin_page_or_dashboard() {
		$pagenow = $GLOBALS['pagenow'];

		// Show on the WP Dashboard.
		if ( $pagenow === 'index.php' ) {
			return true;
		}

		// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Reason: We are not processing form information.
		if ( isset( $_GET['page'] ) && \is_string( $_GET['page'] ) ) {
			// phpcs:ignore WordPress.Security.NonceVerification.Recommended, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- Reason: We are not processing form information and only comparing the variable in a condition.
			$page_from_get = \wp_unslash( $_GET['page'] );

			// Show on Yoast SEO pages, with some exceptions.
			if ( $pagenow === 'admin.php' && \strpos( $page_from_get, 'wpseo' ) === 0 ) {
				$exceptions = [
					'wpseo_installation_successful',
					'wpseo_installation_successful_free',
				];

				if ( ! \in_array( $page_from_get, $exceptions, true ) ) {
					return true;
				}
			}
		}

		return false;
	}

	/**
	 * Whether all steps of the first-time configuration have been finished.
	 *
	 * @return bool Whether the first-time configuration has been finished.
	 */
	private function is_first_time_configuration_finished() {
		$configuration_finished_steps = $this->options_helper->get( 'configuration_finished_steps', [] );

		return \count( $configuration_finished_steps ) === 3;
	}

	/**
	 * Whether the site representation name and logo have been set.
	 *
	 * @return bool  Whether the site representation name and logo have been set.
	 */
	private function are_site_representation_name_and_logo_set() {
		$company_or_person = $this->options_helper->get( 'company_or_person', '' );

		if ( $company_or_person === '' ) {
			return false;
		}

		if ( $company_or_person === 'company' ) {
			return ! empty( $this->options_helper->get( 'company_name' ) ) && ! empty( $this->options_helper->get( 'company_logo', '' ) );
		}

		return ! empty( $this->options_helper->get( 'company_or_person_user_id' ) ) && ! empty( $this->options_helper->get( 'person_logo', '' ) );
	}

	/**
	 * Getter for the show alternate message boolean.
	 *
	 * @return bool
	 */
	public function should_show_alternate_message() {
		return $this->show_alternate_message;
	}
}
