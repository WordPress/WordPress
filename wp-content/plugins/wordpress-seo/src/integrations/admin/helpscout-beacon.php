<?php

namespace Yoast\WP\SEO\Integrations\Admin;

use WPSEO_Addon_Manager;
use WPSEO_Admin_Asset_Manager;
use WPSEO_Tracking_Server_Data;
use WPSEO_Utils;
use Yoast\WP\SEO\Conditionals\Admin_Conditional;
use Yoast\WP\SEO\Config\Migration_Status;
use Yoast\WP\SEO\Helpers\Options_Helper;
use Yoast\WP\SEO\Integrations\Academy_Integration;
use Yoast\WP\SEO\Integrations\Integration_Interface;
use Yoast\WP\SEO\Integrations\Settings_Integration;
use Yoast\WP\SEO\Integrations\Support_Integration;
use Yoast\WP\SEO\Plans\User_Interface\Plans_Page_Integration;

/**
 * Class WPSEO_HelpScout
 */
class HelpScout_Beacon implements Integration_Interface {

	/**
	 * The id for the beacon.
	 *
	 * @var string
	 */
	protected $beacon_id = '2496aba6-0292-489c-8f5d-1c0fba417c2f';

	/**
	 * The id for the beacon for users that have tracking on.
	 *
	 * @var string
	 */
	protected $beacon_id_tracking_users = '6b8e74c5-aa81-4295-b97b-c2a62a13ea7f';

	/**
	 * The id for the beacon for Premium users.
	 *
	 * @var string
	 */
	protected $beacon_id_premium = '1ae02e91-5865-4f13-b220-7daed946ba25';

	/**
	 * The id for the beacon for WooCommerce SEO users.
	 *
	 * @var string
	 */
	protected $beacon_id_woocommerce = '8535d745-4e80-48b9-b211-087880aa857d';

	/**
	 * The products the beacon is loaded for.
	 *
	 * @var array<string>
	 */
	protected $products = [];

	/**
	 * Whether to ask the user's consent before loading in HelpScout.
	 *
	 * @var bool
	 */
	protected $ask_consent = true;

	/**
	 * The options helper.
	 *
	 * @var Options_Helper
	 */
	protected $options;

	/**
	 * The addon manager.
	 *
	 * @var WPSEO_Addon_Manager
	 */
	protected $addon_manager;

	/**
	 * The array of pages we need to show the beacon on with their respective beacon IDs.
	 *
	 * @var array<string, string>
	 */
	protected $pages_ids;

	/**
	 * The array of pages we need to show the beacon on.
	 *
	 * @var array<string>
	 */
	protected $base_pages = [
		'wpseo_dashboard',
		Settings_Integration::PAGE,
		Academy_Integration::PAGE,
		Support_Integration::PAGE,
		'wpseo_search_console',
		'wpseo_tools',
		Plans_Page_Integration::PAGE,
		'wpseo_workouts',
		'wpseo_integrations',
	];

	/**
	 * The current admin page
	 *
	 * @var string|null
	 */
	protected $page;

	/**
	 * The asset manager.
	 *
	 * @var WPSEO_Admin_Asset_Manager
	 */
	protected $asset_manager;

	/**
	 * The migration status object.
	 *
	 * @var Migration_Status
	 */
	protected $migration_status;

	/**
	 * Headless_Rest_Endpoints_Enabled_Conditional constructor.
	 *
	 * @param Options_Helper            $options          The options helper.
	 * @param WPSEO_Admin_Asset_Manager $asset_manager    The asset manager.
	 * @param Migration_Status          $migration_status The migrations status.
	 * @param WPSEO_Addon_Manager       $addon_manager    The addon manager.
	 */
	public function __construct( Options_Helper $options, WPSEO_Admin_Asset_Manager $asset_manager, Migration_Status $migration_status, WPSEO_Addon_Manager $addon_manager ) {
		$this->options       = $options;
		$this->asset_manager = $asset_manager;
		$this->addon_manager = $addon_manager;
		$this->ask_consent   = ! $this->options->get( 'tracking' );
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Reason: We are not processing form information.
		if ( isset( $_GET['page'] ) && \is_string( $_GET['page'] ) ) {
			// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Reason: We are not processing form information.
			$this->page = \sanitize_text_field( \wp_unslash( $_GET['page'] ) );
		}
		else {
			$this->page = null;
		}
		$this->migration_status = $migration_status;

		$beacon_id = $this->get_beacon_id();
		foreach ( $this->base_pages as $page ) {
			$this->pages_ids[ $page ] = $beacon_id;
		}
	}

	/**
	 * {@inheritDoc}
	 *
	 * @return void
	 */
	public function register_hooks() {
		\add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_help_scout_script' ] );
		\add_action( 'admin_footer', [ $this, 'output_beacon_js' ] );
	}

	/**
	 * Enqueues the HelpScout script.
	 *
	 * @return void
	 */
	public function enqueue_help_scout_script() {
		// Make sure plugins can filter in their "stuff", before we check whether we're outputting a beacon.
		$this->filter_settings();
		if ( ! $this->is_beacon_page() ) {
			return;
		}

		$this->asset_manager->enqueue_script( 'help-scout-beacon' );
	}

	/**
	 * Outputs a small piece of javascript for the beacon.
	 *
	 * @return void
	 */
	public function output_beacon_js() {
		if ( ! $this->is_beacon_page() ) {
			return;
		}

		\printf(
			'<script type="text/javascript">window.%1$s(\'%2$s\', %3$s)</script>',
			( $this->ask_consent ) ? 'wpseoHelpScoutBeaconConsent' : 'wpseoHelpScoutBeacon',
			\esc_html( $this->pages_ids[ $this->page ] ),
			// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaping done in format_json_encode.
			WPSEO_Utils::format_json_encode( (array) $this->get_session_data() )
		);
	}

	/**
	 * Checks if the current page is a page containing the beacon.
	 *
	 * @return bool
	 */
	private function is_beacon_page() {
		$return = false;
		if ( ! empty( $this->page ) && $GLOBALS['pagenow'] === 'admin.php' && isset( $this->pages_ids[ $this->page ] ) ) {
			$return = true;
		}

		/**
		 * Filter: 'wpseo_helpscout_show_beacon' - Allows overriding whether we show the HelpScout beacon.
		 *
		 * @param bool $show_beacon Whether we show the beacon or not.
		 */
		return \apply_filters( 'wpseo_helpscout_show_beacon', $return );
	}

	/**
	 * Retrieves the identifying data.
	 *
	 * @return string The data to pass as identifying data.
	 */
	protected function get_session_data() {
		// Short-circuit if we can get the needed data from a transient.
		$transient_data = \get_transient( 'yoast_beacon_session_data' );

		if ( \is_array( $transient_data ) ) {
			return WPSEO_Utils::format_json_encode( $transient_data );
		}

		$current_user = \wp_get_current_user();

		// Do not make these strings translatable! They are for our support agents, the user won't see them!
		$data = \array_merge(
			[
				'name'               => \trim( $current_user->user_firstname . ' ' . $current_user->user_lastname ),
				'email'              => $current_user->user_email,
				'Languages'          => $this->get_language_settings(),
			],
			$this->get_server_info(),
			[
				'WordPress Version'    => $this->get_wordpress_version(),
				'Active theme'         => $this->get_theme_info(),
				'Active plugins'       => $this->get_active_plugins(),
				'Must-use and dropins' => $this->get_mustuse_and_dropins(),
				'Indexables status'    => $this->get_indexables_status(),
			]
		);

		if ( ! empty( $this->products ) ) {
			$addon_manager = new WPSEO_Addon_Manager();
			foreach ( $this->products as $product ) {
				$subscription = $addon_manager->get_subscription( $product );

				if ( ! $subscription ) {
					continue;
				}

				$data[ $subscription->product->name ] = $this->get_product_info( $subscription );
			}
		}

		// Store the data in a transient for 5 minutes to prevent overhead on every backend pageload.
		\set_transient( 'yoast_beacon_session_data', $data, ( 5 * \MINUTE_IN_SECONDS ) );

		return WPSEO_Utils::format_json_encode( $data );
	}

	/**
	 * Returns basic info about the server software.
	 *
	 * @return array<string, string>
	 */
	private function get_server_info() {
		$server_tracking_data = new WPSEO_Tracking_Server_Data();
		$server_data          = $server_tracking_data->get();
		$server_data          = $server_data['server'];

		$fields_to_use = [
			'Server IP'        => 'ip',
			'PHP Version'      => 'PhpVersion',
			'cURL Version'     => 'CurlVersion',
		];

		$server_data['CurlVersion'] = $server_data['CurlVersion']['version'] . ' (SSL Support ' . $server_data['CurlVersion']['sslSupport'] . ')';

		$server_info = [];

		foreach ( $fields_to_use as $label => $field_to_use ) {
			if ( isset( $server_data[ $field_to_use ] ) ) {
				$server_info[ $label ] = \esc_html( $server_data[ $field_to_use ] );
			}
		}

		// Get the memory limits for the server and, if different, from WordPress as well.
		$memory_limit                 = \ini_get( 'memory_limit' );
		$server_info['Memory limits'] = 'Server memory limit: ' . $memory_limit;

		if ( $memory_limit !== \WP_MEMORY_LIMIT ) {
			$server_info['Memory limits'] .= ', WP_MEMORY_LIMIT: ' . \WP_MEMORY_LIMIT;
		}

		if ( $memory_limit !== \WP_MAX_MEMORY_LIMIT ) {
			$server_info['Memory limits'] .= ', WP_MAX_MEMORY_LIMIT: ' . \WP_MAX_MEMORY_LIMIT;
		}

		return $server_info;
	}

	/**
	 * Returns info about the Yoast SEO plugin version and license.
	 *
	 * @param object $plugin The plugin.
	 *
	 * @return string The product info.
	 */
	private function get_product_info( $plugin ) {
		if ( empty( $plugin ) ) {
			return '';
		}

		$product_info = \sprintf(
			'Expiration date %1$s',
			$plugin->expiry_date
		);

		return $product_info;
	}

	/**
	 * Returns the WordPress version + a suffix about the multisite status.
	 *
	 * @return string The WordPress version string.
	 */
	private function get_wordpress_version() {
		global $wp_version;

		$wordpress_version = $wp_version;
		if ( \is_multisite() ) {
			$wordpress_version .= ' (multisite: yes)';
		}
		else {
			$wordpress_version .= ' (multisite: no)';
		}

		return $wordpress_version;
	}

	/**
	 * Returns information about the current theme.
	 *
	 * @return string The theme info as string.
	 */
	private function get_theme_info() {
		$theme = \wp_get_theme();

		$theme_info = \sprintf(
			'%1$s (Version %2$s, %3$s)',
			\esc_html( $theme->display( 'Name' ) ),
			\esc_html( $theme->display( 'Version' ) ),
			\esc_attr( $theme->display( 'ThemeURI' ) )
		);

		if ( \is_child_theme() ) {
			$theme_info .= \sprintf( ', this is a child theme of: %1$s', \esc_html( $theme->display( 'Template' ) ) );
		}

		return $theme_info;
	}

	/**
	 * Returns a stringified list of all active plugins, separated by a pipe.
	 *
	 * @return string The active plugins.
	 */
	private function get_active_plugins() {
		$updates_available = \get_site_transient( 'update_plugins' );

		$active_plugins = '';
		foreach ( \wp_get_active_and_valid_plugins() as $plugin ) {
			$plugin_data             = \get_plugin_data( $plugin );
			$plugin_file             = \str_replace( \trailingslashit( \WP_PLUGIN_DIR ), '', $plugin );
			$plugin_update_available = '';

			if ( isset( $updates_available->response[ $plugin_file ] ) ) {
				$plugin_update_available = ' [update available]';
			}

			$active_plugins .= \sprintf(
				'%1$s (Version %2$s%3$s, %4$s) | ',
				\esc_html( $plugin_data['Name'] ),
				\esc_html( $plugin_data['Version'] ),
				$plugin_update_available,
				\esc_attr( $plugin_data['PluginURI'] )
			);
		}

		return $active_plugins;
	}

	/**
	 * Returns a CSV list of all must-use and drop-in plugins.
	 *
	 * @return string The active plugins.
	 */
	private function get_mustuse_and_dropins() {
		$dropins         = \get_dropins();
		$mustuse_plugins = \get_mu_plugins();

		if ( ! \is_array( $dropins ) ) {
			$dropins = [];
		}

		if ( ! \is_array( $mustuse_plugins ) ) {
			$mustuse_plugins = [];
		}

		return \sprintf( 'Must-Use plugins: %1$d, Drop-ins: %2$d', \count( $mustuse_plugins ), \count( $dropins ) );
	}

	/**
	 * Return the indexables status details.
	 *
	 * @return string The indexables status in a string.
	 */
	private function get_indexables_status() {
		$indexables_status  = 'Indexing completed: ';
		$indexing_completed = $this->options->get( 'indexables_indexing_completed' );
		$indexing_reason    = $this->options->get( 'indexing_reason' );

		$indexables_status .= ( $indexing_completed ) ? 'yes' : 'no';
		$indexables_status .= ( $indexing_reason ) ? ', latest indexing reason: ' . \esc_html( $indexing_reason ) : '';

		foreach ( [ 'free', 'premium' ] as $migration_name ) {
			$current_status = $this->migration_status->get_error( $migration_name );

			if ( \is_array( $current_status ) && isset( $current_status['message'] ) ) {
				$indexables_status .= ', migration error: ' . \esc_html( $current_status['message'] );
			}
		}

		return $indexables_status;
	}

	/**
	 * Returns language settings for the website and the current user.
	 *
	 * @return string The locale settings of the site and user.
	 */
	private function get_language_settings() {
		$site_locale = \get_locale();
		$user_locale = \get_user_locale();

		$language_settings = \sprintf(
			'Site locale: %1$s, user locale: %2$s',
			( \is_string( $site_locale ) ) ? \esc_html( $site_locale ) : 'unknown',
			( \is_string( $user_locale ) ) ? \esc_html( $user_locale ) : 'unknown'
		);

		return $language_settings;
	}

	/**
	 * Returns the conditionals based on which this integration should be active.
	 *
	 * @return array<string> The array of conditionals.
	 */
	public static function get_conditionals() {
		return [ Admin_Conditional::class ];
	}

	/**
	 * Get the beacon id to use based on the user's subscription and tracking settings.
	 *
	 * @return string The beacon id to use.
	 */
	private function get_beacon_id() {
		// Case where the user has a Yoast WooCommerce SEO plan subscription (highest priority).
		if ( $this->addon_manager->has_active_addons() && $this->addon_manager->has_valid_subscription( WPSEO_Addon_Manager::WOOCOMMERCE_SLUG ) ) {
			return $this->beacon_id_woocommerce;
		}

		// Case where the user has a Yoast SEO Premium plan subscription.
		if ( $this->addon_manager->has_active_addons() && $this->addon_manager->has_valid_subscription( WPSEO_Addon_Manager::PREMIUM_SLUG ) ) {
			return $this->beacon_id_premium;
		}

		// Case where the user has no plan active and tracking enabled.
		if ( $this->ask_consent ) {
			return $this->beacon_id_tracking_users;
		}

		// Case where the user has no plan active and tracking disabled.
		return $this->beacon_id;
	}

	/**
	 * Allows filtering of the HelpScout settings. Hooked to admin_head to prevent timing issues, not too early, not too late.
	 *
	 * @return void
	 */
	protected function filter_settings() {
		$filterable_helpscout_setting = [
			'products'  => $this->products,
			'pages_ids' => $this->pages_ids,
		];

		/**
		 * Filter: 'wpseo_helpscout_beacon_settings' - Allows overriding the HelpScout beacon settings.
		 *
		 * @param string $beacon_settings The HelpScout beacon settings.
		 */
		$helpscout_settings = \apply_filters( 'wpseo_helpscout_beacon_settings', $filterable_helpscout_setting );
		$this->products     = $helpscout_settings['products'];
		$this->pages_ids    = $helpscout_settings['pages_ids'];
	}
}
