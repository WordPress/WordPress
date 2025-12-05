<?php

namespace Yoast\WP\SEO\Integrations\Admin;

use WPSEO_Admin_Asset_Manager;
use Yoast\WP\SEO\Conditionals\Admin_Conditional;
use Yoast\WP\SEO\Conditionals\Non_Multisite_Conditional;
use Yoast\WP\SEO\Helpers\Options_Helper;
use Yoast\WP\SEO\Integrations\Integration_Interface;
use Yoast\WP\SEO\Presenters\Admin\Notice_Presenter;

/**
 * Deactivated_Premium_Integration class
 */
class Deactivated_Premium_Integration implements Integration_Interface {

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
	 * {@inheritDoc}
	 */
	public static function get_conditionals() {
		return [ Admin_Conditional::class, Non_Multisite_Conditional::class ];
	}

	/**
	 * First_Time_Configuration_Notice_Integration constructor.
	 *
	 * @param Options_Helper            $options_helper      The options helper.
	 * @param WPSEO_Admin_Asset_Manager $admin_asset_manager The admin asset manager.
	 */
	public function __construct(
		Options_Helper $options_helper,
		WPSEO_Admin_Asset_Manager $admin_asset_manager
	) {
		$this->options_helper      = $options_helper;
		$this->admin_asset_manager = $admin_asset_manager;
	}

	/**
	 * {@inheritDoc}
	 */
	public function register_hooks() {
		\add_action( 'admin_notices', [ $this, 'premium_deactivated_notice' ] );
		\add_action( 'wp_ajax_dismiss_premium_deactivated_notice', [ $this, 'dismiss_premium_deactivated_notice' ] );
	}

	/**
	 * Shows a notice if premium is installed but not activated.
	 *
	 * @return void
	 */
	public function premium_deactivated_notice() {
		global $pagenow;
		if ( $pagenow === 'update.php' ) {
			return;
		}

		if ( $this->options_helper->get( 'dismiss_premium_deactivated_notice', false ) === true ) {
			return;
		}

		$premium_file = 'wordpress-seo-premium/wp-seo-premium.php';

		if ( ! \current_user_can( 'activate_plugin', $premium_file ) ) {
			return;
		}

		if ( $this->premium_is_installed_not_activated( $premium_file ) ) {
			$this->admin_asset_manager->enqueue_style( 'monorepo' );

			$content = \sprintf(
				/* translators: 1: Yoast SEO Premium 2: Link start tag to activate premium, 3: Link closing tag. */
				\__( 'You\'ve installed %1$s but it\'s not activated yet. %2$sActivate %1$s now!%3$s', 'wordpress-seo' ),
				'Yoast SEO Premium',
				'<a href="' . \esc_url(
					\wp_nonce_url(
						\self_admin_url( 'plugins.php?action=activate&plugin=' . $premium_file ),
						'activate-plugin_' . $premium_file
					)
				) . '">',
				'</a>'
			);
			// phpcs:disable WordPress.Security.EscapeOutput.OutputNotEscaped -- Output escaped above.
			echo new Notice_Presenter(
				/* translators: 1: Yoast SEO Premium */
				\sprintf( \__( 'Activate %1$s!', 'wordpress-seo' ), 'Yoast SEO Premium' ),
				$content,
				'support-team.svg',
				null,
				true,
				'yoast-premium-deactivated-notice'
			);
			// phpcs:enable

			// Enable permanently dismissing the notice.
			echo "<script>
                function dismiss_premium_deactivated_notice(){
                    var data = {
                    'action': 'dismiss_premium_deactivated_notice',
                    };

                    jQuery( '#yoast-premium-deactivated-notice' ).hide();
                    jQuery.post( ajaxurl, data, function( response ) {});
                }

                jQuery( document ).ready( function() {
                    jQuery( 'body' ).on( 'click', '#yoast-premium-deactivated-notice .notice-dismiss', function() {
                        dismiss_premium_deactivated_notice();
                    } );
                } );
            </script>";
		}
	}

	/**
	 * Dismisses the premium deactivated notice.
	 *
	 * @return bool
	 */
	public function dismiss_premium_deactivated_notice() {
		return $this->options_helper->set( 'dismiss_premium_deactivated_notice', true );
	}

	/**
	 * Returns whether or not premium is installed and not activated.
	 *
	 * @param string $premium_file The premium file.
	 *
	 * @return bool Whether or not premium is installed and not activated.
	 */
	protected function premium_is_installed_not_activated( $premium_file ) {
		return (
			! \defined( 'WPSEO_PREMIUM_FILE' )
			&& \file_exists( \WP_PLUGIN_DIR . '/' . $premium_file )
		);
	}
}
