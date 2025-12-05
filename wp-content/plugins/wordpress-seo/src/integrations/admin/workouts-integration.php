<?php

namespace Yoast\WP\SEO\Integrations\Admin;

use WPSEO_Addon_Manager;
use WPSEO_Admin_Asset_Manager;
use Yoast\WP\SEO\Conditionals\Admin_Conditional;
use Yoast\WP\SEO\Helpers\Options_Helper;
use Yoast\WP\SEO\Helpers\Product_Helper;
use Yoast\WP\SEO\Integrations\Integration_Interface;
use Yoast\WP\SEO\Presenters\Admin\Notice_Presenter;

/**
 * WorkoutsIntegration class
 */
class Workouts_Integration implements Integration_Interface {

	/**
	 * The admin asset manager.
	 *
	 * @var WPSEO_Admin_Asset_Manager
	 */
	private $admin_asset_manager;

	/**
	 * The addon manager.
	 *
	 * @var WPSEO_Addon_Manager
	 */
	private $addon_manager;

	/**
	 * The options helper.
	 *
	 * @var Options_Helper
	 */
	private $options_helper;

	/**
	 * The product helper.
	 *
	 * @var Product_Helper
	 */
	private $product_helper;

	/**
	 * {@inheritDoc}
	 */
	public static function get_conditionals() {
		return [ Admin_Conditional::class ];
	}

	/**
	 * Workouts_Integration constructor.
	 *
	 * @param WPSEO_Addon_Manager       $addon_manager       The addon manager.
	 * @param WPSEO_Admin_Asset_Manager $admin_asset_manager The admin asset manager.
	 * @param Options_Helper            $options_helper      The options helper.
	 * @param Product_Helper            $product_helper      The product helper.
	 */
	public function __construct(
		WPSEO_Addon_Manager $addon_manager,
		WPSEO_Admin_Asset_Manager $admin_asset_manager,
		Options_Helper $options_helper,
		Product_Helper $product_helper
	) {
		$this->addon_manager       = $addon_manager;
		$this->admin_asset_manager = $admin_asset_manager;
		$this->options_helper      = $options_helper;
		$this->product_helper      = $product_helper;
	}

	/**
	 * {@inheritDoc}
	 */
	public function register_hooks() {
		\add_filter( 'wpseo_submenu_pages', [ $this, 'add_submenu_page' ], 8 );
		\add_filter( 'wpseo_submenu_pages', [ $this, 'remove_old_submenu_page' ], 10 );
		\add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_assets' ], 11 );
	}

	/**
	 * Adds the workouts submenu page.
	 *
	 * @param array $submenu_pages The Yoast SEO submenu pages.
	 *
	 * @return array The filtered submenu pages.
	 */
	public function add_submenu_page( $submenu_pages ) {
		$submenu_pages[] = [
			'wpseo_dashboard',
			'',
			\__( 'Workouts', 'wordpress-seo' ) . ' <span class="yoast-badge yoast-premium-badge"></span>',
			'edit_others_posts',
			'wpseo_workouts',
			[ $this, 'render_target' ],
		];

		return $submenu_pages;
	}

	/**
	 * Removes the workouts submenu page from older Premium versions
	 *
	 * @param array $submenu_pages The Yoast SEO submenu pages.
	 *
	 * @return array The filtered submenu pages.
	 */
	public function remove_old_submenu_page( $submenu_pages ) {
		if ( ! $this->should_update_premium() ) {
			return $submenu_pages;
		}

		// Copy only the Workouts page item that comes first in the array.
		$result_submenu_pages      = [];
		$workouts_page_encountered = false;
		foreach ( $submenu_pages as $item ) {
			if ( $item[4] !== 'wpseo_workouts' || ! $workouts_page_encountered ) {
				$result_submenu_pages[] = $item;
			}
			if ( $item[4] === 'wpseo_workouts' ) {
				$workouts_page_encountered = true;
			}
		}

		return $result_submenu_pages;
	}

	/**
	 * Enqueue the workouts app.
	 *
	 * @return void
	 */
	public function enqueue_assets() {
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Date is not processed or saved.
		if ( ! isset( $_GET['page'] ) || $_GET['page'] !== 'wpseo_workouts' ) {
			return;
		}

		if ( $this->should_update_premium() ) {
			\wp_dequeue_script( 'yoast-seo-premium-workouts' );
		}

		$this->admin_asset_manager->enqueue_style( 'workouts' );

		$workouts_option = $this->get_workouts_option();
		$ftc_url         = \esc_url( \admin_url( 'admin.php?page=wpseo_dashboard#/first-time-configuration' ) );

		$this->admin_asset_manager->enqueue_script( 'workouts' );
		$this->admin_asset_manager->localize_script(
			'workouts',
			'wpseoWorkoutsData',
			[
				'workouts'                  => $workouts_option,
				'homeUrl'                   => \home_url(),
				'pluginUrl'                 => \esc_url( \plugins_url( '', \WPSEO_FILE ) ),
				'toolsPageUrl'              => \esc_url( \admin_url( 'admin.php?page=wpseo_tools' ) ),
				'usersPageUrl'              => \esc_url( \admin_url( 'users.php' ) ),
				'firstTimeConfigurationUrl' => $ftc_url,
				'isPremium'                 => $this->product_helper->is_premium(),
				'upsellText'                => $this->get_upsell_text(),
				'upsellLink'                => $this->get_upsell_link(),
			]
		);
	}

	/**
	 * Renders the target for the React to mount to.
	 *
	 * @return void
	 */
	public function render_target() {
		if ( $this->should_update_premium() ) {
			// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Output escaped in get_update_premium_notice.
			echo $this->get_update_premium_notice();
		}

		echo '<div id="wpseo-workouts-container-free" class="yoast"></div>';
	}

	/**
	 * Gets the workouts option.
	 *
	 * @return mixed|null Returns workouts option if found, null if not.
	 */
	private function get_workouts_option() {
		$workouts_option = $this->options_helper->get( 'workouts_data' );

		// This filter is documented in src/routes/workouts-route.php.
		return \apply_filters( 'Yoast\WP\SEO\workouts_options', $workouts_option );
	}

	/**
	 * Returns the notification to show when Premium needs to be updated.
	 *
	 * @return string The notification to update Premium.
	 */
	private function get_update_premium_notice() {
		$url = $this->get_upsell_link();

		if ( $this->has_premium_subscription_expired() ) {
			/* translators: %s: expands to 'Yoast SEO Premium'. */
			$title = \sprintf( \__( 'Renew your subscription of %s', 'wordpress-seo' ), 'Yoast SEO Premium' );
			$copy  = \sprintf(
				/* translators: %s: expands to 'Yoast SEO Premium'. */
				\esc_html__(
					'Accessing the latest workouts requires an updated version of %s (at least 17.7), but it looks like your subscription has expired. Please renew your subscription to update and gain access to all the latest features.',
					'wordpress-seo'
				),
				'Yoast SEO Premium'
			);
			$button = '<a class="yoast-button yoast-button-upsell yoast-button--small" href="' . \esc_url( $url ) . '" target="_blank">'
					. \esc_html__( 'Renew your subscription', 'wordpress-seo' )
					/* translators: Hidden accessibility text. */
					. '<span class="screen-reader-text">' . \__( '(Opens in a new browser tab)', 'wordpress-seo' ) . '</span>'
					. '<span aria-hidden="true" class="yoast-button-upsell__caret"></span>'
					. '</a>';
		}
		elseif ( $this->has_premium_subscription_activated() ) {
			/* translators: %s: expands to 'Yoast SEO Premium'. */
			$title = \sprintf( \__( 'Update to the latest version of %s', 'wordpress-seo' ), 'Yoast SEO Premium' );
			$copy  = \sprintf(
				/* translators: 1: expands to 'Yoast SEO Premium', 2: Link start tag to the page to update Premium, 3: Link closing tag. */
				\esc_html__( 'It looks like you\'re running an outdated version of %1$s, please %2$supdate to the latest version (at least 17.7)%3$s to gain access to our updated workouts section.', 'wordpress-seo' ),
				'Yoast SEO Premium',
				'<a href="' . \esc_url( $url ) . '">',
				'</a>'
			);
			$button = null;
		}
		else {
			/* translators: %s: expands to 'Yoast SEO Premium'. */
			$title      = \sprintf( \__( 'Activate your subscription of %s', 'wordpress-seo' ), 'Yoast SEO Premium' );
			$url_button = 'https://yoa.st/workouts-activate-notice-help';
			$copy       = \sprintf(
				/* translators: 1: expands to 'Yoast SEO Premium', 2: Link start tag to the page to update Premium, 3: Link closing tag. */
				\esc_html__( 'It looks like you’re running an outdated and unactivated version of %1$s, please activate your subscription in %2$sMyYoast%3$s and update to the latest version (at least 17.7) to gain access to our updated workouts section.', 'wordpress-seo' ),
				'Yoast SEO Premium',
				'<a href="' . \esc_url( $url ) . '">',
				'</a>'
			);
			$button = '<a class="yoast-button yoast-button--primary yoast-button--small" href="' . \esc_url( $url_button ) . '" target="_blank">'
					. \esc_html__( 'Get help activating your subscription', 'wordpress-seo' )
					/* translators: Hidden accessibility text. */
					. '<span class="screen-reader-text">' . \__( '(Opens in a new browser tab)', 'wordpress-seo' ) . '</span>'
					. '</a>';
		}

		$notice = new Notice_Presenter(
			$title,
			$copy,
			null,
			$button
		);

		return $notice->present();
	}

	/**
	 * Check whether Premium should be updated.
	 *
	 * @return bool Returns true when Premium is enabled and the version is below 17.7.
	 */
	private function should_update_premium() {
		$premium_version = $this->product_helper->get_premium_version();
		return $premium_version !== null && \version_compare( $premium_version, '17.7-RC1', '<' );
	}

	/**
	 * Check whether the Premium subscription has expired.
	 *
	 * @return bool Returns true when Premium subscription has expired.
	 */
	private function has_premium_subscription_expired() {
		$subscription = $this->addon_manager->get_subscription( WPSEO_Addon_Manager::PREMIUM_SLUG );

		return ( isset( $subscription->expiry_date ) && ( \strtotime( $subscription->expiry_date ) - \time() ) < 0 );
	}

	/**
	 * Check whether the Premium subscription is activated.
	 *
	 * @return bool Returns true when Premium subscription is activated.
	 */
	private function has_premium_subscription_activated() {
		return $this->addon_manager->has_valid_subscription( WPSEO_Addon_Manager::PREMIUM_SLUG );
	}

	/**
	 * Returns the upsell/update copy to show in the card buttons.
	 *
	 * @return string Returns a string with the upsell/update copy for the card buttons.
	 */
	private function get_upsell_text() {
		if ( ! $this->product_helper->is_premium() || ! $this->should_update_premium() ) {
			// Use the default defined in the component.
			return '';
		}
		if ( $this->has_premium_subscription_expired() ) {
			return \sprintf(
				/* translators: %s: expands to 'Yoast SEO Premium'. */
				\__( 'Renew %s', 'wordpress-seo' ),
				'Yoast SEO Premium'
			);
		}
		if ( $this->has_premium_subscription_activated() ) {
			return \sprintf(
				/* translators: %s: expands to 'Yoast SEO Premium'. */
				\__( 'Update %s', 'wordpress-seo' ),
				'Yoast SEO Premium'
			);
		}
		return \sprintf(
			/* translators: %s: expands to 'Yoast SEO Premium'. */
			\__( 'Activate %s', 'wordpress-seo' ),
			'Yoast SEO Premium'
		);
	}

	/**
	 * Returns the upsell/update link to show in the card buttons.
	 *
	 * @return string Returns a string with the upsell/update link for the card buttons.
	 */
	private function get_upsell_link() {
		if ( ! $this->product_helper->is_premium() || ! $this->should_update_premium() ) {
			// Use the default defined in the component.
			return '';
		}
		if ( $this->has_premium_subscription_expired() ) {
			return 'https://yoa.st/workout-renew-notice';
		}
		if ( $this->has_premium_subscription_activated() ) {
			return \wp_nonce_url( \self_admin_url( 'update.php?action=upgrade-plugin&plugin=wordpress-seo-premium/wp-seo-premium.php' ), 'upgrade-plugin_wordpress-seo-premium/wp-seo-premium.php' );
		}
		return 'https://yoa.st/workouts-activate-notice-myyoast';
	}
}
