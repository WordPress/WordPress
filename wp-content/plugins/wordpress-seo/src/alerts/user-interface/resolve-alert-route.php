<?php
// phpcs:disable Yoast.NamingConventions.NamespaceName.TooLong -- Needed in the folder structure.
namespace Yoast\WP\SEO\Alerts\User_Interface;

use Yoast\WP\SEO\Conditionals\No_Conditionals;
use Yoast\WP\SEO\Helpers\Capability_Helper;
use Yoast\WP\SEO\Helpers\User_Helper;
use Yoast\WP\SEO\Integrations\Integration_Interface;

/**
 * Registers a route to resolve an alert
 *
 * @phpcs:disable Yoast.NamingConventions.ObjectNameDepth.MaxExceeded
 */
class Resolve_Alert_Route implements Integration_Interface {

	use No_Conditionals;

	/**
	 * The user helper.
	 *
	 * @var User_Helper
	 */
	private $user_helper;

	/**
	 * The capability helper.
	 *
	 * @var Capability_Helper
	 */
	private $capability_helper;

	/**
	 * Class constructor.
	 *
	 * @param User_Helper       $user_helper       The user helper.
	 * @param Capability_Helper $capability_helper The capability helper.
	 */
	public function __construct(
		User_Helper $user_helper,
		Capability_Helper $capability_helper
	) {
		$this->user_helper       = $user_helper;
		$this->capability_helper = $capability_helper;
	}

	/**
	 * Registers all hooks to WordPress.
	 *
	 * @return void
	 */
	public function register_hooks() {
		\add_action( 'wp_ajax_wpseo_resolve_alert', [ $this, 'resolve_alert' ] );
	}

	/**
	 * Runs the callback to resolve an alert for the current user.
	 *
	 * @return void.
	 */
	public function resolve_alert() {
		if ( ! \check_ajax_referer( 'wpseo-resolve-alert-nonce', 'nonce', false ) || ! $this->capability_helper->current_user_can( 'wpseo_manage_options' ) ) {
			\wp_send_json_error(
				[
					'message' => 'Security check failed.',
				]
			);
			return;
		}

		if ( ! isset( $_POST['alertId'] ) ) {
			\wp_send_json_error(
				[
					'message' => 'Alert ID is missing.',
				]
			);
			return;
		}

		$alert_id = \sanitize_text_field( \wp_unslash( $_POST['alertId'] ) );
		$user_id  = \get_current_user_id();

		$this->user_helper->update_meta( $user_id, $alert_id . '_resolved', true );

		\wp_send_json_success(
			[
				'message' => 'Alert resolved successfully.',
			]
		);
	}
}
