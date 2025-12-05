<?php

namespace Yoast\WP\SEO\Integrations\Admin;

use WPSEO_Addon_Manager;
use Yoast\WP\SEO\Conditionals\Admin_Conditional;
use Yoast\WP\SEO\Conditionals\Not_Admin_Ajax_Conditional;
use Yoast\WP\SEO\Conditionals\User_Can_Manage_Wpseo_Options_Conditional;
use Yoast\WP\SEO\Config\Indexing_Reasons;
use Yoast\WP\SEO\Helpers\Current_Page_Helper;
use Yoast\WP\SEO\Helpers\Environment_Helper;
use Yoast\WP\SEO\Helpers\Indexing_Helper;
use Yoast\WP\SEO\Helpers\Notification_Helper;
use Yoast\WP\SEO\Helpers\Product_Helper;
use Yoast\WP\SEO\Helpers\Short_Link_Helper;
use Yoast\WP\SEO\Integrations\Integration_Interface;
use Yoast\WP\SEO\Presenters\Admin\Indexing_Failed_Notification_Presenter;
use Yoast\WP\SEO\Presenters\Admin\Indexing_Notification_Presenter;
use Yoast_Notification;
use Yoast_Notification_Center;

/**
 * Class Indexing_Notification_Integration.
 *
 * @package Yoast\WP\SEO\Integrations\Admin
 */
class Indexing_Notification_Integration implements Integration_Interface {

	/**
	 * The notification ID.
	 */
	public const NOTIFICATION_ID = 'wpseo-reindex';

	/**
	 * The Yoast notification center.
	 *
	 * @var Yoast_Notification_Center
	 */
	protected $notification_center;

	/**
	 * The product helper.
	 *
	 * @var Product_Helper
	 */
	protected $product_helper;

	/**
	 * The current page helper.
	 *
	 * @var Current_Page_Helper
	 */
	protected $page_helper;

	/**
	 * The short link helper.
	 *
	 * @var Short_Link_Helper
	 */
	protected $short_link_helper;

	/**
	 * The notification helper.
	 *
	 * @var Notification_Helper
	 */
	protected $notification_helper;

	/**
	 * The indexing helper.
	 *
	 * @var Indexing_Helper
	 */
	protected $indexing_helper;

	/**
	 * The Addon Manager.
	 *
	 * @var WPSEO_Addon_Manager
	 */
	protected $addon_manager;

	/**
	 * The Environment Helper.
	 *
	 * @var Environment_Helper
	 */
	protected $environment_helper;

	/**
	 * Indexing_Notification_Integration constructor.
	 *
	 * @param Yoast_Notification_Center $notification_center The notification center.
	 * @param Product_Helper            $product_helper      The product helper.
	 * @param Current_Page_Helper       $page_helper         The current page helper.
	 * @param Short_Link_Helper         $short_link_helper   The short link helper.
	 * @param Notification_Helper       $notification_helper The notification helper.
	 * @param Indexing_Helper           $indexing_helper     The indexing helper.
	 * @param WPSEO_Addon_Manager       $addon_manager       The addon manager.
	 * @param Environment_Helper        $environment_helper  The environment helper.
	 */
	public function __construct(
		Yoast_Notification_Center $notification_center,
		Product_Helper $product_helper,
		Current_Page_Helper $page_helper,
		Short_Link_Helper $short_link_helper,
		Notification_Helper $notification_helper,
		Indexing_Helper $indexing_helper,
		WPSEO_Addon_Manager $addon_manager,
		Environment_Helper $environment_helper
	) {
		$this->notification_center = $notification_center;
		$this->product_helper      = $product_helper;
		$this->page_helper         = $page_helper;
		$this->short_link_helper   = $short_link_helper;
		$this->notification_helper = $notification_helper;
		$this->indexing_helper     = $indexing_helper;
		$this->addon_manager       = $addon_manager;
		$this->environment_helper  = $environment_helper;
	}

	/**
	 * Initializes the integration.
	 *
	 * Adds hooks and jobs to cleanup or add the notification when necessary.
	 *
	 * @return void
	 */
	public function register_hooks() {
		if ( $this->page_helper->get_current_yoast_seo_page() === 'wpseo_dashboard' ) {
			\add_action( 'admin_init', [ $this, 'maybe_cleanup_notification' ] );
		}

		if ( $this->indexing_helper->has_reason() ) {
			\add_action( 'admin_init', [ $this, 'maybe_create_notification' ] );
		}

		\add_action( self::NOTIFICATION_ID, [ $this, 'maybe_create_notification' ] );
	}

	/**
	 * Returns the conditionals based on which this loadable should be active.
	 *
	 * @return array The conditionals.
	 */
	public static function get_conditionals() {
		return [
			Admin_Conditional::class,
			Not_Admin_Ajax_Conditional::class,
			User_Can_Manage_Wpseo_Options_Conditional::class,
		];
	}

	/**
	 * Checks whether the notification should be shown and adds
	 * it to the notification center if this is the case.
	 *
	 * @return void
	 */
	public function maybe_create_notification() {
		if ( ! $this->should_show_notification() ) {
			return;
		}

		if ( ! $this->notification_center->get_notification_by_id( self::NOTIFICATION_ID ) ) {
			$notification = $this->notification();
			$this->notification_helper->restore_notification( $notification );
			$this->notification_center->add_notification( $notification );
		}
	}

	/**
	 * Checks whether the notification should not be shown anymore and removes
	 * it from the notification center if this is the case.
	 *
	 * @return void
	 */
	public function maybe_cleanup_notification() {
		$notification = $this->notification_center->get_notification_by_id( self::NOTIFICATION_ID );

		if ( $notification === null ) {
			return;
		}

		if ( $this->should_show_notification() ) {
			return;
		}

		$this->notification_center->remove_notification_by_id( self::NOTIFICATION_ID );
	}

	/**
	 * Checks whether the notification should be shown.
	 *
	 * @return bool If the notification should be shown.
	 */
	protected function should_show_notification() {
		if ( ! $this->environment_helper->is_production_mode() ) {
			return false;
		}
		// Don't show a notification if the indexing has already been started earlier.
		if ( $this->indexing_helper->get_started() > 0 ) {
			return false;
		}

		// We're about to perform expensive queries, let's inform.
		\add_filter( 'wpseo_unindexed_count_queries_ran', '__return_true' );

		// Never show a notification when nothing should be indexed.
		return $this->indexing_helper->get_limited_filtered_unindexed_count( 1 ) > 0;
	}

	/**
	 * Returns an instance of the notification.
	 *
	 * @return Yoast_Notification The notification to show.
	 */
	protected function notification() {
		$reason = $this->indexing_helper->get_reason();

		$presenter = $this->get_presenter( $reason );

		return new Yoast_Notification(
			$presenter,
			[
				'type'         => Yoast_Notification::WARNING,
				'id'           => self::NOTIFICATION_ID,
				'capabilities' => 'wpseo_manage_options',
				'priority'     => 0.8,
			]
		);
	}

	/**
	 * Gets the presenter to use to show the notification.
	 *
	 * @param string $reason The reason for the notification.
	 *
	 * @return Indexing_Failed_Notification_Presenter|Indexing_Notification_Presenter
	 */
	protected function get_presenter( $reason ) {
		if ( $reason === Indexing_Reasons::REASON_INDEXING_FAILED ) {
			$presenter = new Indexing_Failed_Notification_Presenter( $this->product_helper, $this->short_link_helper, $this->addon_manager );
		}
		else {
			$total_unindexed = $this->indexing_helper->get_filtered_unindexed_count();
			$presenter       = new Indexing_Notification_Presenter( $this->short_link_helper, $total_unindexed, $reason );
		}

		return $presenter;
	}
}
