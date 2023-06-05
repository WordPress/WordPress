<?php
/**
 * Define deprecated classes to support changing the naming convention of
 * admin notes.
 */

namespace Automattic\WooCommerce\Admin\Notes;

defined( 'ABSPATH' ) || exit;

use Automattic\WooCommerce\Admin\DeprecatedClassFacade;

// phpcs:disable Generic.Files.OneObjectStructurePerFile.MultipleFound

/**
 * WC_Admin_Note.
 *
 * @deprecated since 4.8.0, use Note
 */
class WC_Admin_Note extends DeprecatedClassFacade {
	// These constants must be redeclared as to not break plugins that use them.
	const E_WC_ADMIN_NOTE_ERROR         = Note::E_WC_ADMIN_NOTE_ERROR;
	const E_WC_ADMIN_NOTE_WARNING       = Note::E_WC_ADMIN_NOTE_WARNING;
	const E_WC_ADMIN_NOTE_UPDATE        = Note::E_WC_ADMIN_NOTE_UPDATE;
	const E_WC_ADMIN_NOTE_INFORMATIONAL = Note::E_WC_ADMIN_NOTE_INFORMATIONAL;
	const E_WC_ADMIN_NOTE_MARKETING     = Note::E_WC_ADMIN_NOTE_MARKETING;
	const E_WC_ADMIN_NOTE_SURVEY        = Note::E_WC_ADMIN_NOTE_SURVEY;
	const E_WC_ADMIN_NOTE_PENDING       = Note::E_WC_ADMIN_NOTE_PENDING;
	const E_WC_ADMIN_NOTE_UNACTIONED    = Note::E_WC_ADMIN_NOTE_UNACTIONED;
	const E_WC_ADMIN_NOTE_ACTIONED      = Note::E_WC_ADMIN_NOTE_ACTIONED;
	const E_WC_ADMIN_NOTE_SNOOZED       = Note::E_WC_ADMIN_NOTE_SNOOZED;
	const E_WC_ADMIN_NOTE_EMAIL         = Note::E_WC_ADMIN_NOTE_EMAIL;

	/**
	 * The name of the non-deprecated class that this facade covers.
	 *
	 * @var string
	 */
	protected static $facade_over_classname = 'Automattic\WooCommerce\Admin\Notes\Note';

	/**
	 * The version that this class was deprecated in.
	 *
	 * @var string
	 */
	protected static $deprecated_in_version = '4.8.0';

	/**
	 * Note constructor. Loads note data.
	 *
	 * @param mixed $data Note data, object, or ID.
	 */
	public function __construct( $data = '' ) {
		$this->instance = new static::$facade_over_classname( $data );
	}
}

/**
 * WC_Admin_Notes.
 *
 * @deprecated since 4.8.0, use Notes
 */
class WC_Admin_Notes extends DeprecatedClassFacade {
	/**
	 * The name of the non-deprecated class that this facade covers.
	 *
	 * @var string
	 */
	protected static $facade_over_classname = 'Automattic\WooCommerce\Admin\Notes\Notes';

	/**
	 * The version that this class was deprecated in.
	 *
	 * @var string
	 */
	protected static $deprecated_in_version = '4.8.0';
}

/**
 * WC_Admin_Notes_Coupon_Page_Moved.
 *
 * @deprecated since 4.8.0, use CouponPageMoved
 */
class WC_Admin_Notes_Coupon_Page_Moved extends DeprecatedClassFacade {
	/**
	 * The name of the non-deprecated class that this facade covers.
	 *
	 * @var string
	 */
	protected static $facade_over_classname = 'Automattic\WooCommerce\Internal\Admin\Notes\CouponPageMoved';

	/**
	 * The version that this class was deprecated in.
	 *
	 * @var string
	 */
	protected static $deprecated_in_version = '4.8.0';
}

/**
 * WC_Admin_Notes_Customize_Store_With_Blocks.
 *
 * @deprecated since 4.8.0, use CustomizeStoreWithBlocks
 */
class WC_Admin_Notes_Customize_Store_With_Blocks extends DeprecatedClassFacade {
	/**
	 * The name of the non-deprecated class that this facade covers.
	 *
	 * @var string
	 */
	protected static $facade_over_classname = 'Automattic\WooCommerce\Internal\Admin\Notes\CustomizeStoreWithBlocks';

	/**
	 * The version that this class was deprecated in.
	 *
	 * @var string
	 */
	protected static $deprecated_in_version = '4.8.0';
}

/**
 * WC_Admin_Notes_Edit_Products_On_The_Move.
 *
 * @deprecated since 4.8.0, use EditProductsOnTheMove
 */
class WC_Admin_Notes_Edit_Products_On_The_Move extends DeprecatedClassFacade {
	/**
	 * The name of the non-deprecated class that this facade covers.
	 *
	 * @var string
	 */
	protected static $facade_over_classname = 'Automattic\WooCommerce\Internal\Admin\Notes\EditProductsOnTheMove';

	/**
	 * The version that this class was deprecated in.
	 *
	 * @var string
	 */
	protected static $deprecated_in_version = '4.8.0';
}

/**
 * WC_Admin_Notes_EU_VAT_Number.
 *
 * @deprecated since 4.8.0, use EUVATNumber
 */
class WC_Admin_Notes_EU_VAT_Number extends DeprecatedClassFacade {
	/**
	 * The name of the non-deprecated class that this facade covers.
	 *
	 * @var string
	 */
	protected static $facade_over_classname = 'Automattic\WooCommerce\Internal\Admin\Notes\EUVATNumber';

	/**
	 * The version that this class was deprecated in.
	 *
	 * @var string
	 */
	protected static $deprecated_in_version = '4.8.0';
}

/**
 * WC_Admin_Notes_Facebook_Marketing_Expert.
 *
 * @deprecated since 4.8.0, use FacebookMarketingExpert
 */
class WC_Admin_Notes_Facebook_Marketing_Expert extends DeprecatedClassFacade {
	/**
	 * The name of the non-deprecated class that this facade covers.
	 *
	 * @var string
	 */
	protected static $facade_over_classname = 'Automattic\WooCommerce\Admin\Notes\FacebookMarketingExpert';

	/**
	 * The version that this class was deprecated in.
	 *
	 * @var string
	 */
	protected static $deprecated_in_version = '4.8.0';
}

/**
 * WC_Admin_Notes_First_Product.
 *
 * @deprecated since 4.8.0, use FirstProduct
 */
class WC_Admin_Notes_First_Product extends DeprecatedClassFacade {
	/**
	 * The name of the non-deprecated class that this facade covers.
	 *
	 * @var string
	 */
	protected static $facade_over_classname = 'Automattic\WooCommerce\Internal\Admin\Notes\FirstProduct';

	/**
	 * The version that this class was deprecated in.
	 *
	 * @var string
	 */
	protected static $deprecated_in_version = '4.8.0';
}

/**
 * WC_Admin_Notes_Giving_Feedback_Notes.
 *
 * @deprecated since 4.8.0, use GivingFeedbackNotes
 */
class WC_Admin_Notes_Giving_Feedback_Notes extends DeprecatedClassFacade {
	/**
	 * The name of the non-deprecated class that this facade covers.
	 *
	 * @var string
	 */
	protected static $facade_over_classname = 'Automattic\WooCommerce\Internal\Admin\Notes\GivingFeedbackNotes';

	/**
	 * The version that this class was deprecated in.
	 *
	 * @var string
	 */
	protected static $deprecated_in_version = '4.8.0';
}

/**
 * WC_Admin_Notes_Install_JP_And_WCS_Plugins.
 *
 * @deprecated since 4.8.0, use InstallJPAndWCSPlugins
 */
class WC_Admin_Notes_Install_JP_And_WCS_Plugins extends DeprecatedClassFacade {
	/**
	 * The name of the non-deprecated class that this facade covers.
	 *
	 * @var string
	 */
	protected static $facade_over_classname = 'Automattic\WooCommerce\Internal\Admin\Notes\InstallJPAndWCSPlugins';

	/**
	 * The version that this class was deprecated in.
	 *
	 * @var string
	 */
	protected static $deprecated_in_version = '4.8.0';
}

/**
 * WC_Admin_Notes_Launch_Checklist.
 *
 * @deprecated since 4.8.0, use LaunchChecklist
 */
class WC_Admin_Notes_Launch_Checklist extends DeprecatedClassFacade {
	/**
	 * The name of the non-deprecated class that this facade covers.
	 *
	 * @var string
	 */
	protected static $facade_over_classname = 'Automattic\WooCommerce\Internal\Admin\Notes\LaunchChecklist';

	/**
	 * The version that this class was deprecated in.
	 *
	 * @var string
	 */
	protected static $deprecated_in_version = '4.8.0';
}

/**
 * WC_Admin_Notes_Migrate_From_Shopify.
 *
 * @deprecated since 4.8.0, use MigrateFromShopify
 */
class WC_Admin_Notes_Migrate_From_Shopify extends DeprecatedClassFacade {
	/**
	 * The name of the non-deprecated class that this facade covers.
	 *
	 * @var string
	 */
	protected static $facade_over_classname = 'Automattic\WooCommerce\Internal\Admin\Notes\MigrateFromShopify';

	/**
	 * The version that this class was deprecated in.
	 *
	 * @var string
	 */
	protected static $deprecated_in_version = '4.8.0';
}

/**
 * WC_Admin_Notes_Mobile_App.
 *
 * @deprecated since 4.8.0, use MobileApp
 */
class WC_Admin_Notes_Mobile_App extends DeprecatedClassFacade {
	/**
	 * The name of the non-deprecated class that this facade covers.
	 *
	 * @var string
	 */
	protected static $facade_over_classname = 'Automattic\WooCommerce\Internal\Admin\Notes\MobileApp';

	/**
	 * The version that this class was deprecated in.
	 *
	 * @var string
	 */
	protected static $deprecated_in_version = '4.8.0';
}

/**
 * WC_Admin_Notes_New_Sales_Record.
 *
 * @deprecated since 4.8.0, use NewSalesRecord
 */
class WC_Admin_Notes_New_Sales_Record extends DeprecatedClassFacade {
	/**
	 * The name of the non-deprecated class that this facade covers.
	 *
	 * @var string
	 */
	protected static $facade_over_classname = 'Automattic\WooCommerce\Internal\Admin\Notes\NewSalesRecord';

	/**
	 * The version that this class was deprecated in.
	 *
	 * @var string
	 */
	protected static $deprecated_in_version = '4.8.0';
}

/**
 * WC_Admin_Notes_Onboarding_Email_Marketing.
 *
 * @deprecated since 4.8.0, use OnboardingEmailMarketing
 */
class WC_Admin_Notes_Onboarding_Email_Marketing extends DeprecatedClassFacade {
	/**
	 * The name of the non-deprecated class that this facade covers.
	 *
	 * @var string
	 */
	protected static $facade_over_classname = 'Automattic\WooCommerce\Admin\Notes\OnboardingEmailMarketing';

	/**
	 * The version that this class was deprecated in.
	 *
	 * @var string
	 */
	protected static $deprecated_in_version = '4.8.0';
}

/**
 * WC_Admin_Notes_Onboarding_Payments.
 *
 * @deprecated since 4.8.0, use OnboardingPayments
 */
class WC_Admin_Notes_Onboarding_Payments extends DeprecatedClassFacade {
	/**
	 * The name of the non-deprecated class that this facade covers.
	 *
	 * @var string
	 */
	protected static $facade_over_classname = 'Automattic\WooCommerce\Internal\Admin\Notes\OnboardingPayments';

	/**
	 * The version that this class was deprecated in.
	 *
	 * @var string
	 */
	protected static $deprecated_in_version = '4.8.0';
}

/**
 * WC_Admin_Notes_Online_Clothing_Store.
 *
 * @deprecated since 4.8.0, use OnlineClothingStore
 */
class WC_Admin_Notes_Online_Clothing_Store extends DeprecatedClassFacade {
	/**
	 * The name of the non-deprecated class that this facade covers.
	 *
	 * @var string
	 */
	protected static $facade_over_classname = 'Automattic\WooCommerce\Internal\Admin\Notes\OnlineClothingStore';

	/**
	 * The version that this class was deprecated in.
	 *
	 * @var string
	 */
	protected static $deprecated_in_version = '4.8.0';
}

/**
 * WC_Admin_Notes_Order_Milestones.
 *
 * @deprecated since 4.8.0, use OrderMilestones
 */
class WC_Admin_Notes_Order_Milestones extends DeprecatedClassFacade {
	/**
	 * The name of the non-deprecated class that this facade covers.
	 *
	 * @var string
	 */
	protected static $facade_over_classname = 'Automattic\WooCommerce\Internal\Admin\Notes\OrderMilestones';

	/**
	 * The version that this class was deprecated in.
	 *
	 * @var string
	 */
	protected static $deprecated_in_version = '4.8.0';
}

/**
 * WC_Admin_Notes_Performance_On_Mobile.
 *
 * @deprecated since 4.8.0, use PerformanceOnMobile
 */
class WC_Admin_Notes_Performance_On_Mobile extends DeprecatedClassFacade {
	/**
	 * The name of the non-deprecated class that this facade covers.
	 *
	 * @var string
	 */
	protected static $facade_over_classname = 'Automattic\WooCommerce\Internal\Admin\Notes\PerformanceOnMobile';

	/**
	 * The version that this class was deprecated in.
	 *
	 * @var string
	 */
	protected static $deprecated_in_version = '4.8.0';
}

/**
 * WC_Admin_Notes_Personalize_Store.
 *
 * @deprecated since 4.8.0, use PersonalizeStore
 */
class WC_Admin_Notes_Personalize_Store extends DeprecatedClassFacade {
	/**
	 * The name of the non-deprecated class that this facade covers.
	 *
	 * @var string
	 */
	protected static $facade_over_classname = 'Automattic\WooCommerce\Internal\Admin\Notes\PersonalizeStore';

	/**
	 * The version that this class was deprecated in.
	 *
	 * @var string
	 */
	protected static $deprecated_in_version = '4.8.0';
}

/**
 * WC_Admin_Notes_Real_Time_Order_Alerts.
 *
 * @deprecated since 4.8.0, use RealTimeOrderAlerts
 */
class WC_Admin_Notes_Real_Time_Order_Alerts extends DeprecatedClassFacade {
	/**
	 * The name of the non-deprecated class that this facade covers.
	 *
	 * @var string
	 */
	protected static $facade_over_classname = 'Automattic\WooCommerce\Internal\Admin\Notes\RealTimeOrderAlerts';

	/**
	 * The version that this class was deprecated in.
	 *
	 * @var string
	 */
	protected static $deprecated_in_version = '4.8.0';
}

/**
 * WC_Admin_Notes_Selling_Online_Courses.
 *
 * @deprecated since 4.8.0, use SellingOnlineCourses
 */
class WC_Admin_Notes_Selling_Online_Courses extends DeprecatedClassFacade {
	/**
	 * The name of the non-deprecated class that this facade covers.
	 *
	 * @var string
	 */
	protected static $facade_over_classname = 'Automattic\WooCommerce\Internal\Admin\Notes\SellingOnlineCourses';

	/**
	 * The version that this class was deprecated in.
	 *
	 * @var string
	 */
	protected static $deprecated_in_version = '4.8.0';
}

/**
 * WC_Admin_Notes_Test_Checkout.
 *
 * @deprecated since 4.8.0, use TestCheckout
 */
class WC_Admin_Notes_Test_Checkout extends DeprecatedClassFacade {
	/**
	 * The name of the non-deprecated class that this facade covers.
	 *
	 * @var string
	 */
	protected static $facade_over_classname = 'Automattic\WooCommerce\Internal\Admin\Notes\TestCheckout';

	/**
	 * The version that this class was deprecated in.
	 *
	 * @var string
	 */
	protected static $deprecated_in_version = '4.8.0';
}

/**
 * WC_Admin_Notes_Tracking_Opt_In.
 *
 * @deprecated since 4.8.0, use TrackingOptIn
 */
class WC_Admin_Notes_Tracking_Opt_In extends DeprecatedClassFacade {
	/**
	 * The name of the non-deprecated class that this facade covers.
	 *
	 * @var string
	 */
	protected static $facade_over_classname = 'Automattic\WooCommerce\Internal\Admin\Notes\TrackingOptIn';

	/**
	 * The version that this class was deprecated in.
	 *
	 * @var string
	 */
	protected static $deprecated_in_version = '4.8.0';
}

/**
 * WC_Admin_Notes_Woo_Subscriptions_Notes.
 *
 * @deprecated since 4.8.0, use WooSubscriptionsNotes
 */
class WC_Admin_Notes_Woo_Subscriptions_Notes extends DeprecatedClassFacade {
	/**
	 * The name of the non-deprecated class that this facade covers.
	 *
	 * @var string
	 */
	protected static $facade_over_classname = 'Automattic\WooCommerce\Internal\Admin\Notes\WooSubscriptionsNotes';

	/**
	 * The version that this class was deprecated in.
	 *
	 * @var string
	 */
	protected static $deprecated_in_version = '4.8.0';
}

/**
 * WC_Admin_Notes_WooCommerce_Payments.
 *
 * @deprecated since 4.8.0, use WooCommercePayments
 */
class WC_Admin_Notes_WooCommerce_Payments extends DeprecatedClassFacade {
	/**
	 * The name of the non-deprecated class that this facade covers.
	 *
	 * @var string
	 */
	protected static $facade_over_classname = 'Automattic\WooCommerce\Internal\Admin\Notes\WooCommercePayments';

	/**
	 * The version that this class was deprecated in.
	 *
	 * @var string
	 */
	protected static $deprecated_in_version = '4.8.0';
}

/**
 * WC_Admin_Notes_WooCommerce_Subscriptions.
 *
 * @deprecated since 4.8.0, use WooCommerceSubscriptions
 */
class WC_Admin_Notes_WooCommerce_Subscriptions extends DeprecatedClassFacade {
	/**
	 * The name of the non-deprecated class that this facade covers.
	 *
	 * @var string
	 */
	protected static $facade_over_classname = 'Automattic\WooCommerce\Internal\Admin\Notes\WooCommerceSubscriptions';

	/**
	 * The version that this class was deprecated in.
	 *
	 * @var string
	 */
	protected static $deprecated_in_version = '4.8.0';
}
