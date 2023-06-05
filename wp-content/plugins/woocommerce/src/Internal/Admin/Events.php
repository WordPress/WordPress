<?php
/**
 * Handle cron events.
 */

namespace Automattic\WooCommerce\Internal\Admin;

defined( 'ABSPATH' ) || exit;

use Automattic\WooCommerce\Admin\Features\Features;
use Automattic\WooCommerce\Admin\RemoteInboxNotifications\DataSourcePoller;
use Automattic\WooCommerce\Admin\RemoteInboxNotifications\RemoteInboxNotificationsEngine;
use Automattic\WooCommerce\Internal\Admin\Notes\AddFirstProduct;
use Automattic\WooCommerce\Internal\Admin\Notes\ChoosingTheme;
use Automattic\WooCommerce\Internal\Admin\Notes\CouponPageMoved;
use Automattic\WooCommerce\Internal\Admin\Notes\CustomizeStoreWithBlocks;
use Automattic\WooCommerce\Internal\Admin\Notes\CustomizingProductCatalog;
use Automattic\WooCommerce\Internal\Admin\Notes\EditProductsOnTheMove;
use Automattic\WooCommerce\Internal\Admin\Notes\EUVATNumber;
use Automattic\WooCommerce\Internal\Admin\Notes\FirstProduct;
use Automattic\WooCommerce\Internal\Admin\Notes\InstallJPAndWCSPlugins;
use Automattic\WooCommerce\Internal\Admin\Notes\LaunchChecklist;
use Automattic\WooCommerce\Internal\Admin\Notes\MagentoMigration;
use Automattic\WooCommerce\Internal\Admin\Notes\ManageOrdersOnTheGo;
use Automattic\WooCommerce\Internal\Admin\Notes\MarketingJetpack;
use Automattic\WooCommerce\Internal\Admin\Notes\MerchantEmailNotifications;
use Automattic\WooCommerce\Internal\Admin\Notes\MigrateFromShopify;
use Automattic\WooCommerce\Internal\Admin\Notes\MobileApp;
use Automattic\WooCommerce\Internal\Admin\Notes\NewSalesRecord;
use Automattic\WooCommerce\Internal\Admin\Notes\OnboardingPayments;
use Automattic\WooCommerce\Internal\Admin\Notes\OnlineClothingStore;
use Automattic\WooCommerce\Internal\Admin\Notes\OrderMilestones;
use Automattic\WooCommerce\Internal\Admin\Notes\PaymentsMoreInfoNeeded;
use Automattic\WooCommerce\Internal\Admin\Notes\PaymentsRemindMeLater;
use Automattic\WooCommerce\Internal\Admin\Notes\PerformanceOnMobile;
use Automattic\WooCommerce\Internal\Admin\Notes\PersonalizeStore;
use Automattic\WooCommerce\Internal\Admin\Notes\RealTimeOrderAlerts;
use Automattic\WooCommerce\Internal\Admin\Notes\SellingOnlineCourses;
use Automattic\WooCommerce\Internal\Admin\Notes\TestCheckout;
use Automattic\WooCommerce\Internal\Admin\Notes\TrackingOptIn;
use Automattic\WooCommerce\Internal\Admin\Notes\UnsecuredReportFiles;
use Automattic\WooCommerce\Internal\Admin\Notes\WooCommercePayments;
use Automattic\WooCommerce\Internal\Admin\Notes\WooCommerceSubscriptions;
use Automattic\WooCommerce\Internal\Admin\Notes\WooSubscriptionsNotes;
use Automattic\WooCommerce\Internal\Admin\Schedulers\MailchimpScheduler;
use Automattic\WooCommerce\Admin\Notes\Note;
use Automattic\WooCommerce\Admin\Features\PaymentGatewaySuggestions\PaymentGatewaySuggestionsDataSourcePoller;
use Automattic\WooCommerce\Internal\Admin\RemoteFreeExtensions\RemoteFreeExtensionsDataSourcePoller;

/**
 * Events Class.
 */
class Events {
	/**
	 * The single instance of the class.
	 *
	 * @var object
	 */
	protected static $instance = null;

	/**
	 * Constructor
	 *
	 * @return void
	 */
	protected function __construct() {}

	/**
	 * Array of note class to be added or updated.
	 *
	 * @var array
	 */
	private static $note_classes_to_added_or_updated = array(
		AddFirstProduct::class,
		ChoosingTheme::class,
		CustomizeStoreWithBlocks::class,
		CustomizingProductCatalog::class,
		EditProductsOnTheMove::class,
		EUVATNumber::class,
		FirstProduct::class,
		LaunchChecklist::class,
		MagentoMigration::class,
		ManageOrdersOnTheGo::class,
		MarketingJetpack::class,
		MigrateFromShopify::class,
		MobileApp::class,
		NewSalesRecord::class,
		OnboardingPayments::class,
		OnlineClothingStore::class,
		PaymentsMoreInfoNeeded::class,
		PaymentsRemindMeLater::class,
		PerformanceOnMobile::class,
		PersonalizeStore::class,
		RealTimeOrderAlerts::class,
		TestCheckout::class,
		TrackingOptIn::class,
		WooCommercePayments::class,
		WooCommerceSubscriptions::class,
	);

	/**
	 * The other note classes that are added in other places.
	 *
	 * @var array
	 */
	private static $other_note_classes = array(
		CouponPageMoved::class,
		InstallJPAndWCSPlugins::class,
		OrderMilestones::class,
		SellingOnlineCourses::class,
		UnsecuredReportFiles::class,
		WooSubscriptionsNotes::class,
	);


	/**
	 * Get class instance.
	 *
	 * @return object Instance.
	 */
	final public static function instance() {
		if ( null === static::$instance ) {
			static::$instance = new static();
		}
		return static::$instance;
	}

	/**
	 * Cron event handlers.
	 */
	public function init() {
		add_action( 'wc_admin_daily', array( $this, 'do_wc_admin_daily' ) );
		add_filter( 'woocommerce_get_note_from_db', array( $this, 'get_note_from_db' ), 10, 1 );

		// Initialize the WC_Notes_Refund_Returns Note to attach hook.
		\WC_Notes_Refund_Returns::init();
	}

	/**
	 * Daily events to run.
	 *
	 * Note: Order_Milestones::possibly_add_note is hooked to this as well.
	 */
	public function do_wc_admin_daily() {
		$this->possibly_add_notes();
		$this->possibly_delete_notes();
		$this->possibly_update_notes();
		$this->possibly_refresh_data_source_pollers();

		if ( $this->is_remote_inbox_notifications_enabled() ) {
			DataSourcePoller::get_instance()->read_specs_from_data_sources();
			RemoteInboxNotificationsEngine::run();
		}

		if ( $this->is_merchant_email_notifications_enabled() ) {
			MerchantEmailNotifications::run();
		}

		if ( Features::is_enabled( 'onboarding' ) ) {
			( new MailchimpScheduler() )->run();
		}
	}

	/**
	 * Get note.
	 *
	 * @param Note $note_from_db The note object from the database.
	 */
	public function get_note_from_db( $note_from_db ) {
		if ( ! $note_from_db instanceof Note || get_user_locale() === $note_from_db->get_locale() ) {
			return $note_from_db;
		}

		$note_classes = array_merge( self::$note_classes_to_added_or_updated, self::$other_note_classes );
		foreach ( $note_classes as $note_class ) {
			if ( defined( "$note_class::NOTE_NAME" ) && $note_class::NOTE_NAME === $note_from_db->get_name() ) {
				$note_from_class = method_exists( $note_class, 'get_note' ) ? $note_class::get_note() : null;

				if ( $note_from_class instanceof Note ) {
					$note = clone $note_from_db;
					$note->set_title( $note_from_class->get_title() );
					$note->set_content( $note_from_class->get_content() );
					$note->set_actions( $note_from_class->get_actions() );
					return $note;
				}
				break;
			}
		}
		return $note_from_db;
	}

	/**
	 * Adds notes that should be added.
	 */
	protected function possibly_add_notes() {
		foreach ( self::$note_classes_to_added_or_updated as $note_class ) {
			if ( method_exists( $note_class, 'possibly_add_note' ) ) {
				$note_class::possibly_add_note();
			}
		}
	}

	/**
	 * Deletes notes that should be deleted.
	 */
	protected function possibly_delete_notes() {
		PaymentsRemindMeLater::delete_if_not_applicable();
		PaymentsMoreInfoNeeded::delete_if_not_applicable();
	}

	/**
	 * Updates notes that should be updated.
	 */
	protected function possibly_update_notes() {
		foreach ( self::$note_classes_to_added_or_updated as $note_class ) {
			if ( method_exists( $note_class, 'possibly_update_note' ) ) {
				$note_class::possibly_update_note();
			}
		}
	}

	/**
	 * Checks if remote inbox notifications are enabled.
	 *
	 * @return bool Whether remote inbox notifications are enabled.
	 */
	protected function is_remote_inbox_notifications_enabled() {
		// Check if the feature flag is disabled.
		if ( ! Features::is_enabled( 'remote-inbox-notifications' ) ) {
			return false;
		}

		// Check if the site has opted out of marketplace suggestions.
		if ( get_option( 'woocommerce_show_marketplace_suggestions', 'yes' ) !== 'yes' ) {
			return false;
		}

		// All checks have passed.
		return true;
	}

	/**
	 * Checks if merchant email notifications are enabled.
	 *
	 * @return bool Whether merchant email notifications are enabled.
	 */
	protected function is_merchant_email_notifications_enabled() {
		// Check if the feature flag is disabled.
		if ( get_option( 'woocommerce_merchant_email_notifications', 'no' ) !== 'yes' ) {
			return false;
		}

		// All checks have passed.
		return true;
	}

	/**
	 *   Refresh transient for the following DataSourcePollers on wc_admin_daily cron job.
	 *   - PaymentGatewaySuggestionsDataSourcePoller
	 *   - RemoteFreeExtensionsDataSourcePoller
	 */
	protected function possibly_refresh_data_source_pollers() {
		$completed_tasks = get_option( 'woocommerce_task_list_tracked_completed_tasks', array() );

		if ( ! in_array( 'payments', $completed_tasks, true ) && ! in_array( 'woocommerce-payments', $completed_tasks, true ) ) {
			PaymentGatewaySuggestionsDataSourcePoller::get_instance()->read_specs_from_data_sources();
		}

		if ( ! in_array( 'store_details', $completed_tasks, true ) && ! in_array( 'marketing', $completed_tasks, true ) ) {
			RemoteFreeExtensionsDataSourcePoller::get_instance()->read_specs_from_data_sources();
		}
	}
}
