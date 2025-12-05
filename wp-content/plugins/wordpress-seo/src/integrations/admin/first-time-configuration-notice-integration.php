<?php

namespace Yoast\WP\SEO\Integrations\Admin;

use WPSEO_Admin_Asset_Manager;
use Yoast\WP\SEO\Conditionals\Admin_Conditional;
use Yoast\WP\SEO\Helpers\First_Time_Configuration_Notice_Helper;
use Yoast\WP\SEO\Helpers\Options_Helper;
use Yoast\WP\SEO\Integrations\Integration_Interface;
use Yoast\WP\SEO\Presenters\Admin\Notice_Presenter;

/**
 * First_Time_Configuration_Notice_Integration class
 */
class First_Time_Configuration_Notice_Integration implements Integration_Interface {

	/**
	 * The options' helper.
	 *
	 * @var Options_Helper
	 */
	private $options_helper;

	/**
	 * The admin asset manager.
	 *
	 * @var WPSEO_Admin_Asset_Manager
	 */
	private $admin_asset_manager;

	/**
	 * The first time configuration notice helper.
	 *
	 * @var First_Time_Configuration_Notice_Helper
	 */
	private $first_time_configuration_notice_helper;

	/**
	 * {@inheritDoc}
	 */
	public static function get_conditionals() {
		return [ Admin_Conditional::class ];
	}

	/**
	 * First_Time_Configuration_Notice_Integration constructor.
	 *
	 * @param Options_Helper                         $options_helper                         The options helper.
	 * @param First_Time_Configuration_Notice_Helper $first_time_configuration_notice_helper The first time configuration notice helper.
	 * @param WPSEO_Admin_Asset_Manager              $admin_asset_manager                    The admin asset manager.
	 */
	public function __construct(
		Options_Helper $options_helper,
		First_Time_Configuration_Notice_Helper $first_time_configuration_notice_helper,
		WPSEO_Admin_Asset_Manager $admin_asset_manager
	) {
		$this->options_helper                         = $options_helper;
		$this->admin_asset_manager                    = $admin_asset_manager;
		$this->first_time_configuration_notice_helper = $first_time_configuration_notice_helper;
	}

	/**
	 * {@inheritDoc}
	 */
	public function register_hooks() {
		\add_action( 'wp_ajax_dismiss_first_time_configuration_notice', [ $this, 'dismiss_first_time_configuration_notice' ] );
		\add_action( 'admin_notices', [ $this, 'first_time_configuration_notice' ] );
	}

	/**
	 * Dismisses the First-time configuration notice.
	 *
	 * @return bool
	 */
	public function dismiss_first_time_configuration_notice() {
		// Check for nonce.
		if ( ! \check_ajax_referer( 'wpseo-dismiss-first-time-configuration-notice', 'nonce', false ) ) {
			return false;
		}
		return $this->options_helper->set( 'dismiss_configuration_workout_notice', true );
	}

	/**
	 * Determines whether and where the "First-time SEO Configuration" admin notice should be displayed.
	 *
	 * @return bool Whether the "First-time SEO Configuration" admin notice should be displayed.
	 */
	public function should_display_first_time_configuration_notice() {
		return $this->first_time_configuration_notice_helper->should_display_first_time_configuration_notice();
	}

	/**
	 * Displays an admin notice when the first-time configuration has not been finished yet.
	 *
	 * @return void
	 */
	public function first_time_configuration_notice() {
		if ( ! $this->should_display_first_time_configuration_notice() ) {
			return;
		}

		$this->admin_asset_manager->enqueue_style( 'monorepo' );

		$title    = $this->first_time_configuration_notice_helper->get_first_time_configuration_title();
		$link_url = \esc_url( \self_admin_url( 'admin.php?page=wpseo_dashboard#/first-time-configuration' ) );

		if ( ! $this->first_time_configuration_notice_helper->should_show_alternate_message() ) {
			$content = \sprintf(
				/* translators: 1: Link start tag to the first-time configuration, 2: Yoast SEO, 3: Link closing tag. */
				\__( 'Get started quickly with the %1$s%2$s First-time configuration%3$s and configure Yoast SEO with the optimal SEO settings for your site!', 'wordpress-seo' ),
				'<a href="' . $link_url . '">',
				'Yoast SEO',
				'</a>'
			);
		}
		else {
			$content = \sprintf(
				/* translators: 1: Link start tag to the first-time configuration, 2: Link closing tag. */
				\__( 'We noticed that you haven\'t fully configured Yoast SEO yet. Optimize your SEO settings even further by using our improved %1$s First-time configuration%2$s.', 'wordpress-seo' ),
				'<a href="' . $link_url . '">',
				'</a>'
			);
		}

		$notice = new Notice_Presenter(
			$title,
			$content,
			'mirrored_fit_bubble_woman_1_optim.svg',
			null,
			true,
			'yoast-first-time-configuration-notice'
		);

		//phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Output from present() is considered safe.
		echo $notice->present();

		// Enable permanently dismissing the notice.
		echo '<script>
				jQuery( document ).ready( function() {
					jQuery( "body" ).on( "click", "#yoast-first-time-configuration-notice .notice-dismiss", function() {
						jQuery( "#yoast-first-time-configuration-notice" ).hide();
						const data = {
							"action": "dismiss_first_time_configuration_notice",
							"nonce": "' . \esc_js( \wp_create_nonce( 'wpseo-dismiss-first-time-configuration-notice' ) ) . '"
						};
						jQuery.post( ajaxurl, data, function( response ) {});
					} );
				} );
				</script>';
	}
}
