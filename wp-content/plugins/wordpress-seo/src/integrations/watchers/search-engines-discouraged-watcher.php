<?php

namespace Yoast\WP\SEO\Integrations\Watchers;

use Yoast\WP\SEO\Conditionals\No_Conditionals;
use Yoast\WP\SEO\Helpers\Capability_Helper;
use Yoast\WP\SEO\Helpers\Current_Page_Helper;
use Yoast\WP\SEO\Helpers\Notification_Helper;
use Yoast\WP\SEO\Helpers\Options_Helper;
use Yoast\WP\SEO\Integrations\Integration_Interface;
use Yoast\WP\SEO\Presenters\Admin\Search_Engines_Discouraged_Presenter;
use Yoast_Notification;
use Yoast_Notification_Center;

/**
 * Shows a notification for users who have access for robots disabled.
 */
class Search_Engines_Discouraged_Watcher implements Integration_Interface {

	use No_Conditionals;

	/**
	 * The notification ID.
	 */
	public const NOTIFICATION_ID = 'wpseo-search-engines-discouraged';

	/**
	 * The Yoast notification center.
	 *
	 * @var Yoast_Notification_Center
	 */
	protected $notification_center;

	/**
	 * The notification helper.
	 *
	 * @var Notification_Helper
	 */
	protected $notification_helper;

	/**
	 * The search engines discouraged presenter.
	 *
	 * @var Search_Engines_Discouraged_Presenter
	 */
	protected $presenter;

	/**
	 * The current page helper.
	 *
	 * @var Current_Page_Helper
	 */
	protected $current_page_helper;

	/**
	 * The options helper.
	 *
	 * @var Options_Helper
	 */
	protected $options_helper;

	/**
	 * The capability helper.
	 *
	 * @var Capability_Helper
	 */
	protected $capability_helper;

	/**
	 * Search_Engines_Discouraged_Watcher constructor.
	 *
	 * @param Yoast_Notification_Center $notification_center The notification center.
	 * @param Notification_Helper       $notification_helper The notification helper.
	 * @param Current_Page_Helper       $current_page_helper The current page helper.
	 * @param Options_Helper            $options_helper      The options helper.
	 * @param Capability_Helper         $capability_helper   The capability helper.
	 */
	public function __construct(
		Yoast_Notification_Center $notification_center,
		Notification_Helper $notification_helper,
		Current_Page_Helper $current_page_helper,
		Options_Helper $options_helper,
		Capability_Helper $capability_helper
	) {
		$this->notification_center = $notification_center;
		$this->notification_helper = $notification_helper;
		$this->current_page_helper = $current_page_helper;
		$this->options_helper      = $options_helper;
		$this->capability_helper   = $capability_helper;
		$this->presenter           = new Search_Engines_Discouraged_Presenter();
	}

	/**
	 * Initializes the integration.
	 *
	 * On admin_init, it is checked whether the notification about search engines being discouraged should be shown.
	 * On admin_notices, the notice about the search engines being discouraged will be shown when necessary.
	 *
	 * @return void
	 */
	public function register_hooks() {
		\add_action( 'admin_init', [ $this, 'manage_search_engines_discouraged_notification' ] );

		\add_action( 'update_option_blog_public', [ $this, 'restore_ignore_option' ] );

		/*
		 * The `admin_notices` hook fires on single site admin pages vs.
		 * `network_admin_notices` which fires on multisite admin pages and
		 * `user_admin_notices` which fires on multisite user admin pages.
		 */
		\add_action( 'admin_notices', [ $this, 'maybe_show_search_engines_discouraged_notice' ] );
	}

	/**
	 * Manage the search engines discouraged notification.
	 *
	 * Shows the notification if needed and deletes it if needed.
	 *
	 * @return void
	 */
	public function manage_search_engines_discouraged_notification() {
		if ( ! $this->should_show_search_engines_discouraged_notification() ) {
			$this->remove_search_engines_discouraged_notification_if_exists();
		}
		else {
			$this->maybe_add_search_engines_discouraged_notification();
		}
	}

	/**
	 * Show the search engine discouraged notice when needed.
	 *
	 * @return void
	 */
	public function maybe_show_search_engines_discouraged_notice() {
		if ( ! $this->should_show_search_engines_discouraged_notice() ) {
			return;
		}
		$this->show_search_engines_discouraged_notice();
	}

	/**
	 * Whether the search engines discouraged notification should be shown.
	 *
	 * @return bool
	 */
	protected function should_show_search_engines_discouraged_notification() {
		return $this->search_engines_are_discouraged() && $this->options_helper->get( 'ignore_search_engines_discouraged_notice', false ) === false;
	}

	/**
	 * Remove the search engines discouraged notification if it exists.
	 *
	 * @return void
	 */
	protected function remove_search_engines_discouraged_notification_if_exists() {
		$this->notification_center->remove_notification_by_id( self::NOTIFICATION_ID );
	}

	/**
	 * Add the search engines discouraged notification if it does not exist yet.
	 *
	 * @return void
	 */
	protected function maybe_add_search_engines_discouraged_notification() {
		if ( ! $this->notification_center->get_notification_by_id( self::NOTIFICATION_ID ) ) {
			$notification = $this->notification();
			$this->notification_helper->restore_notification( $notification );
			$this->notification_center->add_notification( $notification );
		}
	}

	/**
	 * Checks whether search engines are discouraged from indexing the site.
	 *
	 * @return bool Whether search engines are discouraged from indexing the site.
	 */
	protected function search_engines_are_discouraged() {
		return (string) \get_option( 'blog_public' ) === '0';
	}

	/**
	 * Whether the search engines notice should be shown.
	 *
	 * @return bool
	 */
	protected function should_show_search_engines_discouraged_notice() {
		$pages_to_show_notice = [
			'index.php',
			'plugins.php',
			'update-core.php',
		];

		return (
			$this->search_engines_are_discouraged()
			&& $this->capability_helper->current_user_can( 'manage_options' )
			&& $this->options_helper->get( 'ignore_search_engines_discouraged_notice', false ) === false
			&& (
				$this->current_page_helper->is_yoast_seo_page()
				|| \in_array( $this->current_page_helper->get_current_admin_page(), $pages_to_show_notice, true )
			)
			&& $this->current_page_helper->get_current_yoast_seo_page() !== 'wpseo_dashboard'
		);
	}

	/**
	 * Show the search engines discouraged notice.
	 *
	 * @return void
	 */
	protected function show_search_engines_discouraged_notice() {
		\printf(
			'<div id="robotsmessage" class="notice notice-error">%1$s</div>',
			// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Output from present() is considered safe.
			$this->presenter->present()
		);
	}

	/**
	 * Returns an instance of the notification.
	 *
	 * @return Yoast_Notification The notification to show.
	 */
	protected function notification() {
		return new Yoast_Notification(
			$this->presenter->present(),
			[
				'type'         => Yoast_Notification::ERROR,
				'id'           => self::NOTIFICATION_ID,
				'capabilities' => 'wpseo_manage_options',
				'priority'     => 1,
			]
		);
	}

	/**
	 * Should restore the ignore option for the search engines discouraged notice.
	 *
	 * @return void
	 */
	public function restore_ignore_option() {
		if ( ! $this->search_engines_are_discouraged() ) {
			$this->options_helper->set( 'ignore_search_engines_discouraged_notice', false );
		}
	}
}
