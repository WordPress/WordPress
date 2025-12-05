<?php

namespace Yoast\WP\SEO\Llms_Txt\User_Interface;

use Yoast\WP\SEO\Conditionals\No_Conditionals;
use Yoast\WP\SEO\Helpers\Options_Helper;
use Yoast\WP\SEO\Integrations\Integration_Interface;
use Yoast\WP\SEO\Llms_Txt\Application\File\Commands\Populate_File_Command_Handler;
use Yoast\WP\SEO\Llms_Txt\Application\File\File_Failure_Notification_Presenter;
use Yoast_Notification;
use Yoast_Notification_Center;

/**
 * Watches and handles changes to the LLMS.txt file failure option.
 *
 * @phpcs:disable Yoast.NamingConventions.ObjectNameDepth.MaxExceeded
 */
class File_Failure_Llms_Txt_Notification_Integration implements Integration_Interface {
	use No_Conditionals;

	/**
	 * The notification ID.
	 */
	public const NOTIFICATION_ID = 'wpseo-llms-txt-generation-failure';

	/**
	 * The options helper.
	 *
	 * @var Options_Helper
	 */
	private $options_helper;

	/**
	 * The notification center.
	 *
	 * @var Yoast_Notification_Center
	 */
	private $notification_center;

	/**
	 * The notification presenter.
	 *
	 * @var File_Failure_Notification_Presenter
	 */
	private $presenter;

	/**
	 * Constructor.
	 *
	 * @param Options_Helper                      $options_helper      The options helper.
	 * @param Yoast_Notification_Center           $notification_center The notification center.
	 * @param File_Failure_Notification_Presenter $presenter           The notification presenter.
	 */
	public function __construct(
		Options_Helper $options_helper,
		Yoast_Notification_Center $notification_center,
		File_Failure_Notification_Presenter $presenter
	) {
		$this->options_helper      = $options_helper;
		$this->notification_center = $notification_center;
		$this->presenter           = $presenter;
	}

	/**
	 * Initializes the integration.
	 *
	 * This is the place to register hooks and filters.
	 *
	 * @return void
	 */
	public function register_hooks() {
		\add_action( 'admin_init', [ $this, 'maybe_show_notification' ], 10, 2 );
	}

	/**
	 * Manage the search engines discouraged notification.
	 *
	 * Shows the notification if needed and deletes it if needed.
	 *
	 * @return void
	 */
	public function maybe_show_notification() {
		if ( ! $this->should_show_file_failure_notification() ) {
			$this->remove_file_failure_notification_if_exists();
		}
		else {
			$this->maybe_add_file_failure_notification();
		}
	}

	/**
	 * Whether the file failure notification should be shown.
	 *
	 * @return bool
	 */
	private function should_show_file_failure_notification(): bool {
		return $this->options_helper->get( 'enable_llms_txt', false ) && \get_option( Populate_File_Command_Handler::GENERATION_FAILURE_OPTION, false ) !== false;
	}

	/**
	 * Remove the search engines discouraged notification if it exists.
	 *
	 * @return void
	 */
	private function remove_file_failure_notification_if_exists() {
		$this->notification_center->remove_notification_by_id( self::NOTIFICATION_ID );
	}

	/**
	 * Add the search engines discouraged notification if it does not exist yet.
	 *
	 * @return void
	 */
	private function maybe_add_file_failure_notification() {
		if ( ! $this->notification_center->get_notification_by_id( self::NOTIFICATION_ID ) ) {
			$notification = new Yoast_Notification(
				$this->presenter->present(),
				[
					'type'         => Yoast_Notification::ERROR,
					'id'           => self::NOTIFICATION_ID,
					'capabilities' => 'wpseo_manage_options',
					'priority'     => 1,
				]
			);
			$this->notification_center->restore_notification( $notification );
			$this->notification_center->add_notification( $notification );
		}
	}
}
