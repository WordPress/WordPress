<?php
/**
 * WPSEO plugin file.
 *
 * @package WPSEO\Inc
 */

use Yoast\WP\SEO\General\User_Interface\General_Page_Integration;
use Yoast\WP\SEO\Plans\User_Interface\Plans_Page_Integration;
use Yoast\WP\SEO\Promotions\Application\Promotion_Manager;

/**
 * Represents the addon manager.
 */
class WPSEO_Addon_Manager {

	/**
	 * Holds the name of the transient.
	 *
	 * @var string
	 */
	public const SITE_INFORMATION_TRANSIENT = 'wpseo_site_information';

	/**
	 * Holds the name of the transient.
	 *
	 * @var string
	 */
	public const SITE_INFORMATION_TRANSIENT_QUICK = 'wpseo_site_information_quick';

	/**
	 * Holds the slug for YoastSEO free.
	 *
	 * @var string
	 */
	public const FREE_SLUG = 'yoast-seo-wordpress';

	/**
	 * Holds the slug for YoastSEO Premium.
	 *
	 * @var string
	 */
	public const PREMIUM_SLUG = 'yoast-seo-wordpress-premium';

	/**
	 * Holds the slug for Yoast News.
	 *
	 * @var string
	 */
	public const NEWS_SLUG = 'yoast-seo-news';

	/**
	 * Holds the slug for Video.
	 *
	 * @var string
	 */
	public const VIDEO_SLUG = 'yoast-seo-video';

	/**
	 * Holds the slug for WooCommerce.
	 *
	 * @var string
	 */
	public const WOOCOMMERCE_SLUG = 'yoast-seo-woocommerce';

	/**
	 * Holds the slug for Local.
	 *
	 * @var string
	 */
	public const LOCAL_SLUG = 'yoast-seo-local';

	/**
	 * The expected addon data.
	 *
	 * @var array<string, string>
	 */
	protected static $addons = [
		'wp-seo-premium.php'    => self::PREMIUM_SLUG,
		'wpseo-news.php'        => self::NEWS_SLUG,
		'video-seo.php'         => self::VIDEO_SLUG,
		'wpseo-woocommerce.php' => self::WOOCOMMERCE_SLUG,
		'local-seo.php'         => self::LOCAL_SLUG,
	];

	/**
	 * The addon data for the shortlinks.
	 *
	 * @var array<string, array<string, string>>
	 */
	private $addon_details = [
		self::PREMIUM_SLUG     => [
			'name'                  => 'Yoast SEO Premium',
			'short_link_activation' => 'https://yoa.st/13j',
			'short_link_renewal'    => 'https://yoa.st/4ey',
		],
		self::NEWS_SLUG        => [
			'name'                  => 'Yoast News SEO',
			'short_link_activation' => 'https://yoa.st/4xq',
			'short_link_renewal'    => 'https://yoa.st/4xv',
		],
		self::WOOCOMMERCE_SLUG => [
			'name'                  => 'Yoast WooCommerce SEO',
			'short_link_activation' => 'https://yoa.st/4xs',
			'short_link_renewal'    => 'https://yoa.st/4xx',
		],
		self::VIDEO_SLUG       => [
			'name'                  => 'Yoast Video SEO',
			'short_link_activation' => 'https://yoa.st/4xr',
			'short_link_renewal'    => 'https://yoa.st/4xw',
		],
		self::LOCAL_SLUG       => [
			'name'                  => 'Yoast Local SEO',
			'short_link_activation' => 'https://yoa.st/4xp',
			'short_link_renewal'    => 'https://yoa.st/4xu',
		],
	];

	/**
	 * Holds the site information data.
	 *
	 * @var stdClass
	 */
	private $site_information;

	/**
	 * Hooks into WordPress.
	 *
	 * @codeCoverageIgnore
	 *
	 * @return void
	 */
	public function register_hooks() {
		add_action( 'admin_init', [ $this, 'validate_addons' ], 15 );
		add_filter( 'pre_set_site_transient_update_plugins', [ $this, 'check_for_updates' ] );
		add_filter( 'plugins_api', [ $this, 'get_plugin_information' ], 10, 3 );
		add_action( 'plugins_loaded', [ $this, 'register_expired_messages' ], 10 );
	}

	/**
	 * Registers "expired subscription" warnings to the update messages of our addons.
	 *
	 * @return void
	 */
	public function register_expired_messages() {
		foreach ( array_keys( $this->get_installed_addons() ) as $plugin_file ) {
			add_action( 'in_plugin_update_message-' . $plugin_file, [ $this, 'expired_subscription_warning' ], 10, 2 );
		}
	}

	/**
	 * Gets the subscriptions for current site.
	 *
	 * @return stdClass The subscriptions.
	 */
	public function get_subscriptions() {
		return $this->get_site_information()->subscriptions;
	}

	/**
	 * Provides a list of addon filenames.
	 *
	 * @return string[] List of addon filenames with their slugs.
	 */
	public function get_addon_filenames() {
		return self::$addons;
	}

	/**
	 * Finds the plugin file.
	 *
	 * @param string $plugin_slug The plugin slug to search.
	 *
	 * @return bool|string Plugin file when installed, False when plugin isn't installed.
	 */
	public function get_plugin_file( $plugin_slug ) {
		$plugins            = $this->get_plugins();
		$plugin_files       = array_keys( $plugins );
		$target_plugin_file = array_search( $plugin_slug, $this->get_addon_filenames(), true );

		if ( ! $target_plugin_file ) {
			return false;
		}

		foreach ( $plugin_files as $plugin_file ) {
			if ( strpos( $plugin_file, $target_plugin_file ) !== false ) {
				return $plugin_file;
			}
		}

		return false;
	}

	/**
	 * Retrieves the subscription for the given slug.
	 *
	 * @param string $slug The plugin slug to retrieve.
	 *
	 * @return stdClass|false Subscription data when found, false when not found.
	 */
	public function get_subscription( $slug ) {
		foreach ( $this->get_subscriptions() as $subscription ) {
			if ( $subscription->product->slug === $slug ) {
				return $subscription;
			}
		}

		return false;
	}

	/**
	 * Retrieves a list of (subscription) slugs by the active addons.
	 *
	 * @return array<string, stdClass> The slugs.
	 */
	public function get_subscriptions_for_active_addons() {
		$active_addons      = array_keys( $this->get_active_addons() );
		$subscription_slugs = array_map( [ $this, 'get_slug_by_plugin_file' ], $active_addons );
		$subscriptions      = [];
		foreach ( $subscription_slugs as $subscription_slug ) {
			$subscriptions[ $subscription_slug ] = $this->get_subscription( $subscription_slug );
		}

		return $subscriptions;
	}

	/**
	 * Retrieves a list of versions for each addon.
	 *
	 * @return array<string, string> The addon versions.
	 */
	public function get_installed_addons_versions() {
		$addon_versions = [];
		foreach ( $this->get_installed_addons() as $plugin_file => $installed_addon ) {
			$addon_versions[ $this->get_slug_by_plugin_file( $plugin_file ) ] = $installed_addon['Version'];
		}

		return $addon_versions;
	}

	/**
	 * Retrieves the plugin information from the subscriptions.
	 *
	 * @param stdClass|false $data   The result object. Default false.
	 * @param string         $action The type of information being requested from the Plugin Installation API.
	 * @param stdClass       $args   Plugin API arguments.
	 *
	 * @return object Extended plugin data.
	 */
	public function get_plugin_information( $data, $action, $args ) {
		if ( $action !== 'plugin_information' ) {
			return $data;
		}

		if ( ! isset( $args->slug ) ) {
			return $data;
		}

		$subscription = $this->get_subscription( $args->slug );
		if ( ! $subscription ) {
			return $data;
		}

		$data = $this->convert_subscription_to_plugin( $subscription, null, true );

		if ( $this->has_subscription_expired( $subscription ) ) {
			unset( $data->package, $data->download_link );
		}

		return $data;
	}

	/**
	 * Retrieves information from MyYoast about which addons are connected to the current site.
	 *
	 * @return stdClass The list of addons activated for this site.
	 */
	public function get_myyoast_site_information() {
		if ( $this->site_information === null ) {
			$this->site_information = $this->get_site_information_transient();
		}

		if ( $this->site_information ) {
			return $this->site_information;
		}

		$this->site_information = $this->request_current_sites();
		if ( $this->site_information ) {
			$this->site_information = $this->map_site_information( $this->site_information );

			$this->set_site_information_transient( $this->site_information );

			return $this->site_information;
		}

		return $this->get_site_information_default();
	}

	/**
	 * Checks if the subscription for the given slug is valid.
	 *
	 * @param string $slug The plugin slug to retrieve.
	 *
	 * @return bool True when the subscription is valid.
	 */
	public function has_valid_subscription( $slug ) {
		$subscription = $this->get_subscription( $slug );

		// An non-existing subscription is never valid.
		if ( ! $subscription ) {
			return false;
		}

		return ! $this->has_subscription_expired( $subscription );
	}

	/**
	 * Checks if there are addon updates.
	 *
	 * @param stdClass|mixed $data The current data for update_plugins.
	 *
	 * @return stdClass Extended data for update_plugins.
	 */
	public function check_for_updates( $data ) {
		global $wp_version;

		if ( empty( $data ) ) {
			return $data;
		}

		// We have to figure out if we're safe to upgrade the add-ons, based on what the latest Yoast Free requirements for the WP version is.
		$yoast_free_data = $this->extract_yoast_data( $data );

		foreach ( $this->get_installed_addons() as $plugin_file => $installed_plugin ) {
			$subscription_slug = $this->get_slug_by_plugin_file( $plugin_file );
			$subscription      = $this->get_subscription( $subscription_slug );

			if ( ! $subscription ) {
				continue;
			}

			$plugin_data = $this->convert_subscription_to_plugin( $subscription, $yoast_free_data, false, $plugin_file );

			// Let's assume for now that it will get added in the 'no_update' key that we'll return to the WP API.
			$is_no_update = true;

			// If the add-on's version is the latest, we have to do no further checks.
			if ( version_compare( $installed_plugin['Version'], $plugin_data->new_version, '<' ) ) {
				// If we haven't retrieved the Yoast Free requirements for the WP version yet, do nothing. The next run will probably get us that information.
				if ( $plugin_data->requires === null ) {
					continue;
				}

				if ( version_compare( $plugin_data->requires, $wp_version, '<=' ) ) {
					// The add-on has an available update *and* the Yoast Free requirements for the WP version are also met, so go ahead and show the upgrade info to the user.
					$is_no_update                   = false;
					$data->response[ $plugin_file ] = $plugin_data;

					if ( $this->has_subscription_expired( $subscription ) ) {
						unset( $data->response[ $plugin_file ]->package, $data->response[ $plugin_file ]->download_link );
					}
				}
			}

			if ( $is_no_update ) {
				// Still convert subscription when no updates is available.
				$data->no_update[ $plugin_file ] = $plugin_data;

				if ( $this->has_subscription_expired( $subscription ) ) {
					unset( $data->no_update[ $plugin_file ]->package, $data->no_update[ $plugin_file ]->download_link );
				}
			}
		}

		return $data;
	}

	/**
	 * Extracts Yoast SEO Free's data from the wp.org API response.
	 *
	 * @param object $data The wp.org API response.
	 *
	 * @return object Yoast Free's data from wp.org.
	 */
	protected function extract_yoast_data( $data ) {
		if ( isset( $data->response[ WPSEO_BASENAME ] ) ) {
			return $data->response[ WPSEO_BASENAME ];
		}

		if ( isset( $data->no_update[ WPSEO_BASENAME ] ) ) {
			return $data->no_update[ WPSEO_BASENAME ];
		}

		return (object) [];
	}

	/**
	 * If the plugin is lacking an active subscription, throw a warning.
	 *
	 * @param array $plugin_data The data for the plugin in this row.
	 *
	 * @return void
	 */
	public function expired_subscription_warning( $plugin_data ) {
		$subscription = $this->get_subscription( $plugin_data['slug'] );
		if ( $subscription && $this->has_subscription_expired( $subscription ) ) {
			$addon_link = ( isset( $this->addon_details[ $plugin_data['slug'] ] ) ) ? $this->addon_details[ $plugin_data['slug'] ]['short_link_renewal'] : $this->addon_details[ self::PREMIUM_SLUG ]['short_link_renewal'];

			$sale_copy = '';
			if ( YoastSEO()->classes->get( Promotion_Manager::class )->is( 'black-friday-promotion' ) ) {
				$sale_copy = sprintf(
				/* translators: %1$s and %2$s are a <span> opening and closing tag. */
					esc_html__( '%1$s30%% OFF - Black Friday %2$s', 'wordpress-seo' ),
					'<span class="yoast-update-plugin-bf-sale-badge">',
					'</span>'
				);
			}
			echo '<br><br>';
			echo '<strong><span class="yoast-dashicons-notice warning dashicons dashicons-warning"></span> '
				. sprintf(
					/* translators: %1$s is the plugin name, %2$s and %3$s are a link. */
					esc_html__( 'Your %1$s plugin cannot be updated as your subscription has expired. %2$sRenew your product subscription%3$s to restore updates and full feature access.', 'wordpress-seo' ),
					esc_html( $plugin_data['name'] ),
					'<a href="' . esc_url( WPSEO_Shortlinker::get( $addon_link ) ) . '">',
					'</a>'
				)
				// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Output is escaped above.
				. $sale_copy
				. '</strong>';
		}
	}

	/**
	 * Checks if there are any installed addons.
	 *
	 * @return bool True when there are installed Yoast addons.
	 */
	public function has_installed_addons() {
		$installed_addons = $this->get_installed_addons();

		return ! empty( $installed_addons );
	}

	/**
	 * Checks if the plugin is installed and activated in WordPress.
	 *
	 * @param string $slug The class' slug.
	 *
	 * @return bool True when installed and activated.
	 */
	public function is_installed( $slug ) {
		$slug_to_class_map = [
			static::PREMIUM_SLUG     => 'WPSEO_Premium',
			static::NEWS_SLUG        => 'WPSEO_News',
			static::WOOCOMMERCE_SLUG => 'Yoast_WooCommerce_SEO',
			static::VIDEO_SLUG       => 'WPSEO_Video_Sitemap',
			static::LOCAL_SLUG       => 'WPSEO_Local_Core',
		];

		if ( ! isset( $slug_to_class_map[ $slug ] ) ) {
			return false;
		}

		return class_exists( $slug_to_class_map[ $slug ] );
	}

	/**
	 * Validates the addons and show a notice for the ones that are invalid.
	 *
	 * @return void
	 */
	public function validate_addons() {
		$notification_center = Yoast_Notification_Center::get();

		if ( $notification_center === null ) {
			return;
		}

		foreach ( $this->addon_details as $slug => $addon_info ) {
			$notification = $this->create_notification( $addon_info['name'], $addon_info['short_link_activation'] );

			// Add a notification when the installed plugin isn't activated in My Yoast.
			if ( $this->is_installed( $slug ) && ! $this->has_valid_subscription( $slug ) ) {
				$notification_center->add_notification( $notification );

				continue;
			}

			$notification_center->remove_notification( $notification );
		}
	}

	/**
	 * Checks if the user has any active addons.
	 *
	 * @return bool Whether there are active addons.
	 */
	public function has_active_addons() {
		$active_addons = $this->get_active_addons();

		return ! empty( $active_addons );
	}

	/**
	 * Removes the site information transients.
	 *
	 * @codeCoverageIgnore
	 *
	 * @return void
	 */
	public function remove_site_information_transients() {
		delete_transient( self::SITE_INFORMATION_TRANSIENT );
		delete_transient( self::SITE_INFORMATION_TRANSIENT_QUICK );
	}

	/**
	 * Creates an instance of Yoast_Notification.
	 *
	 * @param string $product_name The product to create the notification for.
	 * @param string $short_link   The short link for the addon notification.
	 *
	 * @return Yoast_Notification The created notification.
	 */
	protected function create_notification( $product_name, $short_link ) {
		$notification_options = [
			'type'         => Yoast_Notification::ERROR,
			'id'           => 'wpseo-dismiss-' . sanitize_title_with_dashes( $product_name, null, 'save' ),
			'capabilities' => 'wpseo_manage_options',
		];

		return new Yoast_Notification(
			sprintf(
			/* translators: %1$s expands to a strong tag, %2$s expands to the product name, %3$s expands to a closing strong tag, %4$s expands to an a tag. %5$s expands to MyYoast, %6$s expands to a closing a tag,  %7$s expands to the product name  */
				__( '%1$s %2$s isn\'t working as expected %3$s and you are not receiving updates or support! Make sure to %4$s activate your product subscription in %5$s%6$s to unlock all the features of %7$s.', 'wordpress-seo' ),
				'<strong>',
				$product_name,
				'</strong>',
				'<a href="' . WPSEO_Shortlinker::get( $short_link ) . '" target="_blank">',
				'MyYoast',
				'</a>',
				$product_name
			),
			$notification_options
		);
	}

	/**
	 * Checks whether a plugin expiry date has been passed.
	 *
	 * @param stdClass $subscription Plugin subscription.
	 *
	 * @return bool Has the plugin expired.
	 */
	protected function has_subscription_expired( $subscription ) {
		return ( strtotime( $subscription->expiry_date ) - time() ) < 0;
	}

	/**
	 * Converts a subscription to plugin based format.
	 *
	 * @param stdClass      $subscription    The subscription to convert.
	 * @param stdClass|null $yoast_free_data The Yoast Free's data.
	 * @param bool          $plugin_info     Whether we're in the plugin information modal.
	 * @param string        $plugin_file     The plugin filename.
	 *
	 * @return stdClass The converted subscription.
	 */
	protected function convert_subscription_to_plugin( $subscription, $yoast_free_data = null, $plugin_info = false, $plugin_file = '' ) {
		$changelog = '';
		if ( isset( $subscription->product->changelog ) ) {
			// We need to replace h2's and h3's with h4's because the styling expects that.
			$changelog = str_replace( '</h2', '</h4', str_replace( '<h2', '<h4', $subscription->product->changelog ) );
			$changelog = str_replace( '</h3', '</h4', str_replace( '<h3', '<h4', $changelog ) );
		}

		// If we're running this because we want to just show the plugin info in the version details modal, we can fallback to the Yoast Free constants, since that modal will not be accessible anyway in the event that the new Free version increases those constants.
		$defaults = [
			// It can be expanded if we have the 'tested' and 'requires_php' data be returned from wp.org in the future.
			'requires'     => ( $plugin_info ) ? YOAST_SEO_WP_REQUIRED : null,
		];

		return (object) [
			'new_version'      => ( $subscription->product->version ?? '' ),
			'name'             => $subscription->product->name,
			'slug'             => $subscription->product->slug,
			'plugin'           => $plugin_file,
			'url'              => $subscription->product->store_url,
			'last_update'      => $subscription->product->last_updated,
			'homepage'         => $subscription->product->store_url,
			'download_link'    => $subscription->product->download,
			'package'          => $subscription->product->download,
			'sections'         => [
				'changelog' => $changelog,
				'support'   => $this->get_support_section(),
			],
			'icons'            => [
				'2x' => $this->get_icon( $subscription->product->slug ),
			],
			'update_supported' => true,
			'banners'          => $this->get_banners( $subscription->product->slug ),
			// If we have extracted Yoast Free's data before, use that. If not, resort to the defaults.
			'tested'           => YOAST_SEO_WP_TESTED,
			'requires'         => ( $yoast_free_data->requires ?? $defaults['requires'] ),
			'requires_php'     => YOAST_SEO_PHP_REQUIRED,
		];
	}

	/**
	 * Returns the plugin's icon URL.
	 *
	 * @param string $slug The plugin slug.
	 *
	 * @return string The icon URL for this plugin.
	 */
	protected function get_icon( $slug ) {
		switch ( $slug ) {
			case self::LOCAL_SLUG:
				return 'https://yoa.st/local-seo-icon';
			case self::NEWS_SLUG:
				return 'https://yoa.st/news-seo-icon';
			case self::PREMIUM_SLUG:
				return 'https://yoa.st/yoast-seo-icon';
			case self::VIDEO_SLUG:
				return 'https://yoa.st/video-seo-icon';
			case self::WOOCOMMERCE_SLUG:
				return 'https://yoa.st/woo-seo-icon';
		}
	}

	/**
	 * Return an array of plugin banner URLs.
	 *
	 * @param string $slug The plugin slug.
	 *
	 * @return string[]
	 */
	protected function get_banners( $slug ) {
		switch ( $slug ) {
			case self::LOCAL_SLUG:
				return [
					'high' => 'https://yoa.st/yoast-seo-banner-local',
					'low'  => 'https://yoa.st/yoast-seo-banner-low-local',
				];
			case self::NEWS_SLUG:
				return [
					'high' => 'https://yoa.st/yoast-seo-banner-news',
					'low'  => 'https://yoa.st/yoast-seo-banner-low-news',
				];
			case self::PREMIUM_SLUG:
				return [
					'high' => 'https://yoa.st/yoast-seo-banner-premium',
					'low'  => 'https://yoa.st/yoast-seo-banner-low-premium',
				];
			case self::VIDEO_SLUG:
				return [
					'high' => 'https://yoa.st/yoast-seo-banner-video',
					'low'  => 'https://yoa.st/yoast-seo-banner-low-video',
				];
			case self::WOOCOMMERCE_SLUG:
				return [
					'high' => 'https://yoa.st/yoast-seo-banner-woo',
					'low'  => 'https://yoa.st/yoast-seo-banner-low-woo',
				];
		}
	}

	/**
	 * Checks if the given plugin_file belongs to a Yoast addon.
	 *
	 * @param string $plugin_file Path to the plugin.
	 *
	 * @return bool True when plugin file is for a Yoast addon.
	 */
	protected function is_yoast_addon( $plugin_file ) {
		return $this->get_slug_by_plugin_file( $plugin_file ) !== '';
	}

	/**
	 * Retrieves the addon slug by given plugin file path.
	 *
	 * @param string $plugin_file The file path to the plugin.
	 *
	 * @return string The slug when found or empty string when not.
	 */
	protected function get_slug_by_plugin_file( $plugin_file ) {
		$addons = self::$addons;

		// Yoast SEO Free isn't an addon, but we needed it in Premium to fetch translations.
		if ( YoastSEO()->helpers->product->is_premium() ) {
			$addons['wp-seo.php'] = self::FREE_SLUG;
		}

		foreach ( $addons as $addon => $addon_slug ) {
			if ( strpos( $plugin_file, $addon ) !== false ) {
				return $addon_slug;
			}
		}

		return '';
	}

	/**
	 * Retrieves the installed Yoast addons.
	 *
	 * @return array The installed plugins.
	 */
	protected function get_installed_addons() {
		return array_filter( $this->get_plugins(), [ $this, 'is_yoast_addon' ], ARRAY_FILTER_USE_KEY );
	}

	/**
	 * Retrieves a list of active addons.
	 *
	 * @return array The active addons.
	 */
	protected function get_active_addons() {
		return array_filter( $this->get_installed_addons(), [ $this, 'is_plugin_active' ], ARRAY_FILTER_USE_KEY );
	}

	/**
	 * Retrieves the current sites from the API.
	 *
	 * @codeCoverageIgnore
	 *
	 * @return bool|stdClass Object when request is successful. False if not.
	 */
	protected function request_current_sites() {
		$api_request = new WPSEO_MyYoast_Api_Request( 'sites/current' );
		if ( $api_request->fire() ) {
			return $api_request->get_response();
		}

		return $this->get_site_information_default();
	}

	/**
	 * Retrieves the transient value with the site information.
	 *
	 * @codeCoverageIgnore
	 *
	 * @return stdClass|false The transient value.
	 */
	protected function get_site_information_transient() {
		global $pagenow;

		// Force re-check on license & dashboard pages.
		$current_page = null;
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Reason: We are not processing form information.
		if ( isset( $_GET['page'] ) && is_string( $_GET['page'] ) ) {
			// phpcs:ignore WordPress.Security.NonceVerification.Recommended,WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- Reason: We are not processing form information, We are only strictly comparing and thus no need to sanitize.
			$current_page = wp_unslash( $_GET['page'] );
		}

		// Check whether the licenses are valid or whether we need to show notifications.
		$quick = ( $current_page === Plans_Page_Integration::PAGE || $current_page === General_Page_Integration::PAGE );

		// Also do a fresh request on Plugins & Core Update pages.
		$quick = $quick || $pagenow === 'plugins.php';
		$quick = $quick || $pagenow === 'update-core.php';

		if ( $quick ) {
			return get_transient( self::SITE_INFORMATION_TRANSIENT_QUICK );
		}

		return get_transient( self::SITE_INFORMATION_TRANSIENT );
	}

	/**
	 * Sets the site information transient.
	 *
	 * @codeCoverageIgnore
	 *
	 * @param stdClass $site_information The site information to save.
	 *
	 * @return void
	 */
	protected function set_site_information_transient( $site_information ) {
		set_transient( self::SITE_INFORMATION_TRANSIENT, $site_information, DAY_IN_SECONDS );
		set_transient( self::SITE_INFORMATION_TRANSIENT_QUICK, $site_information, 60 );
	}

	/**
	 * Retrieves all installed WordPress plugins.
	 *
	 * @codeCoverageIgnore
	 *
	 * @return array The plugins.
	 */
	protected function get_plugins() {
		if ( ! function_exists( 'get_plugins' ) ) {
			require_once ABSPATH . 'wp-admin/includes/plugin.php';
		}

		return get_plugins();
	}

	/**
	 * Checks if the given plugin file belongs to an active plugin.
	 *
	 * @codeCoverageIgnore
	 *
	 * @param string $plugin_file The file path to the plugin.
	 *
	 * @return bool True when plugin is active.
	 */
	protected function is_plugin_active( $plugin_file ) {
		return is_plugin_active( $plugin_file );
	}

	/**
	 * Returns an object with no subscriptions.
	 *
	 * @codeCoverageIgnore
	 *
	 * @return stdClass Site information.
	 */
	protected function get_site_information_default() {
		return (object) [
			'url'           => WPSEO_Utils::get_home_url(),
			'subscriptions' => [],
		];
	}

	/**
	 * Maps the plugin API response.
	 *
	 * @param object $site_information Site information as received from the API.
	 *
	 * @return stdClass Mapped site information.
	 */
	protected function map_site_information( $site_information ) {
		return (object) [
			'url'           => $site_information->url,
			'subscriptions' => array_map( [ $this, 'map_subscription' ], $site_information->subscriptions ),
		];
	}

	/**
	 * Maps a plugin subscription.
	 *
	 * @param object $subscription Subscription information as received from the API.
	 *
	 * @return stdClass Mapped subscription.
	 */
	protected function map_subscription( $subscription ) {
		// phpcs:disable WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase -- Not our properties.
		return (object) [
			'renewal_url' => $subscription->renewalUrl,
			'expiry_date' => $subscription->expiryDate,
			'product'     => (object) [
				'version'      => $subscription->product->version,
				'name'         => $subscription->product->name,
				'slug'         => $subscription->product->slug,
				'last_updated' => $subscription->product->lastUpdated,
				'store_url'    => $subscription->product->storeUrl,
				// Ternary operator is necessary because download can be undefined.
				'download'     => ( $subscription->product->download ?? null ),
				'changelog'    => $subscription->product->changelog,
			],
		];
		// phpcs:enable
	}

	/**
	 * Retrieves the site information.
	 *
	 * @return stdClass The site information.
	 */
	private function get_site_information() {
		if ( ! $this->has_installed_addons() ) {
			return $this->get_site_information_default();
		}

		return $this->get_myyoast_site_information();
	}

	/**
	 * Retrieves the contents for the support section.
	 *
	 * @return string The support section content.
	 */
	protected function get_support_section() {
		return '<h4>' . __( 'Need support?', 'wordpress-seo' ) . '</h4>'
			. '<p>'
			/* translators: 1: expands to <a> that refers to the help page, 2: </a> closing tag. */
			. sprintf( __( 'You can probably find an answer to your question in our %1$shelp center%2$s.', 'wordpress-seo' ), '<a href="https://yoast.com/help/">', '</a>' )
			. ' '
			/* translators: %s expands to a mailto support link. */
			. sprintf( __( 'If you still need support and have an active subscription for this product, please email %s.', 'wordpress-seo' ), '<a href="mailto:support@yoast.com">support@yoast.com</a>' )
			. '</p>';
	}
}
