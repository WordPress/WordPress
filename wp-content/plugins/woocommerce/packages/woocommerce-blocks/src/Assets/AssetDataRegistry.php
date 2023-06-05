<?php
namespace Automattic\WooCommerce\Blocks\Assets;

use Automattic\WooCommerce\Blocks\Package;

use Exception;
use InvalidArgumentException;

/**
 * Class instance for registering data used on the current view session by
 * assets.
 *
 * Holds data registered for output on the current view session when
 * `wc-settings` is enqueued( directly or via dependency )
 *
 * @since 2.5.0
 */
class AssetDataRegistry {
	/**
	 * Contains registered data.
	 *
	 * @var array
	 */
	private $data = [];

	/**
	 * Contains preloaded API data.
	 *
	 * @var array
	 */
	private $preloaded_api_requests = [];

	/**
	 * Lazy data is an array of closures that will be invoked just before
	 * asset data is generated for the enqueued script.
	 *
	 * @var array
	 */
	private $lazy_data = [];

	/**
	 * Asset handle for registered data.
	 *
	 * @var string
	 */
	private $handle = 'wc-settings';

	/**
	 * Asset API interface for various asset registration.
	 *
	 * @var API
	 */
	private $api;

	/**
	 * Constructor
	 *
	 * @param Api $asset_api  Asset API interface for various asset registration.
	 */
	public function __construct( Api $asset_api ) {
		$this->api = $asset_api;
		$this->init();
	}

	/**
	 * Hook into WP asset registration for enqueueing asset data.
	 */
	protected function init() {
		if ( $this->is_site_editor() ) {
			add_action( 'enqueue_block_editor_assets', array( $this, 'register_data_script' ) );
		} else {
			add_action( 'init', array( $this, 'register_data_script' ) );
		}
		add_action( 'wp_print_footer_scripts', array( $this, 'enqueue_asset_data' ), 2 );
		add_action( 'admin_print_footer_scripts', array( $this, 'enqueue_asset_data' ), 2 );
	}

	/**
	 * Checks if the current URL is the Site Editor.
	 *
	 * @return boolean
	 */
	protected function is_site_editor() {
		$url_path = isset( $_SERVER['REQUEST_URI'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REQUEST_URI'] ) ) : '';
		return str_contains( $url_path, 'site-editor.php' );
	}

	/**
	 * Exposes core data via the wcSettings global. This data is shared throughout the client.
	 *
	 * Settings that are used by various components or multiple blocks should be added here. Note, that settings here are
	 * global so be sure not to add anything heavy if possible.
	 *
	 * @return array  An array containing core data.
	 */
	protected function get_core_data() {
		return [
			'adminUrl'           => admin_url(),
			'countries'          => WC()->countries->get_countries(),
			'currency'           => $this->get_currency_data(),
			'currentUserId'      => get_current_user_id(),
			'currentUserIsAdmin' => current_user_can( 'manage_woocommerce' ),
			'homeUrl'            => esc_url( home_url( '/' ) ),
			'locale'             => $this->get_locale_data(),
			'dashboardUrl'       => wc_get_account_endpoint_url( 'dashboard' ),
			'orderStatuses'      => $this->get_order_statuses(),
			'placeholderImgSrc'  => wc_placeholder_img_src(),
			'productsSettings'   => $this->get_products_settings(),
			'siteTitle'          => get_bloginfo( 'name' ),
			'storePages'         => $this->get_store_pages(),
			'wcAssetUrl'         => plugins_url( 'assets/', WC_PLUGIN_FILE ),
			'wcVersion'          => defined( 'WC_VERSION' ) ? WC_VERSION : '',
			'wpLoginUrl'         => wp_login_url(),
			'wpVersion'          => get_bloginfo( 'version' ),
		];
	}

	/**
	 * Get currency data to include in settings.
	 *
	 * @return array
	 */
	protected function get_currency_data() {
		$currency = get_woocommerce_currency();

		return [
			'code'              => $currency,
			'precision'         => wc_get_price_decimals(),
			'symbol'            => html_entity_decode( get_woocommerce_currency_symbol( $currency ) ),
			'symbolPosition'    => get_option( 'woocommerce_currency_pos' ),
			'decimalSeparator'  => wc_get_price_decimal_separator(),
			'thousandSeparator' => wc_get_price_thousand_separator(),
			'priceFormat'       => html_entity_decode( get_woocommerce_price_format() ),
		];
	}

	/**
	 * Get locale data to include in settings.
	 *
	 * @return array
	 */
	protected function get_locale_data() {
		global $wp_locale;

		return [
			'siteLocale'    => get_locale(),
			'userLocale'    => get_user_locale(),
			'weekdaysShort' => array_values( $wp_locale->weekday_abbrev ),
		];
	}

	/**
	 * Get store pages to include in settings.
	 *
	 * @return array
	 */
	protected function get_store_pages() {
		$store_pages = [
			'myaccount' => wc_get_page_id( 'myaccount' ),
			'shop'      => wc_get_page_id( 'shop' ),
			'cart'      => wc_get_page_id( 'cart' ),
			'checkout'  => wc_get_page_id( 'checkout' ),
			'privacy'   => wc_privacy_policy_page_id(),
			'terms'     => wc_terms_and_conditions_page_id(),
		];

		if ( is_callable( '_prime_post_caches' ) ) {
			_prime_post_caches( array_values( $store_pages ), false, false );
		}

		return array_map(
			[ $this, 'format_page_resource' ],
			$store_pages
		);
	}

	/**
	 * Get product related settings.
	 *
	 * Note: For the time being we are exposing only the settings that are used by blocks.
	 *
	 * @return array
	 */
	protected function get_products_settings() {
		return [
			'cartRedirectAfterAdd' => get_option( 'woocommerce_cart_redirect_after_add' ) === 'yes',
		];
	}

	/**
	 * Format a page object into a standard array of data.
	 *
	 * @param WP_Post|int $page Page object or ID.
	 * @return array
	 */
	protected function format_page_resource( $page ) {
		if ( is_numeric( $page ) && $page > 0 ) {
			$page = get_post( $page );
		}
		if ( ! is_a( $page, '\WP_Post' ) || 'publish' !== $page->post_status ) {
			return [
				'id'        => 0,
				'title'     => '',
				'permalink' => false,
			];
		}
		return [
			'id'        => $page->ID,
			'title'     => $page->post_title,
			'permalink' => get_permalink( $page->ID ),
		];
	}

	/**
	 * Returns block-related data for enqueued wc-settings script.
	 * Format order statuses by removing a leading 'wc-' if present.
	 *
	 * @return array formatted statuses.
	 */
	protected function get_order_statuses() {
		$formatted_statuses = array();
		foreach ( wc_get_order_statuses() as $key => $value ) {
			$formatted_key                        = preg_replace( '/^wc-/', '', $key );
			$formatted_statuses[ $formatted_key ] = $value;
		}
		return $formatted_statuses;
	}

	/**
	 * Used for on demand initialization of asset data and registering it with
	 * the internal data registry.
	 *
	 * Note: core data will overwrite any externally registered data via the api.
	 */
	protected function initialize_core_data() {
		/**
		 * Filters the array of shared settings.
		 *
		 * Low level hook for registration of new data late in the cycle. This is deprecated.
		 * Instead, use the data api:
		 *
		 * ```php
		 * Automattic\WooCommerce\Blocks\Package::container()->get( Automattic\WooCommerce\Blocks\Assets\AssetDataRegistry::class )->add( $key, $value )
		 * ```
		 *
		 * @since 5.0.0
		 *
		 * @deprecated
		 * @param array $data Settings data.
		 * @return array
		 */
		$settings = apply_filters( 'woocommerce_shared_settings', $this->data );

		// Surface a deprecation warning in the error console.
		if ( has_filter( 'woocommerce_shared_settings' ) ) {
			$error_handle  = 'deprecated-shared-settings-error';
			$error_message = '`woocommerce_shared_settings` filter in Blocks is deprecated. See https://github.com/woocommerce/woocommerce-gutenberg-products-block/blob/trunk/docs/contributors/block-assets.md';
			// phpcs:ignore WordPress.WP.EnqueuedResourceParameters.NotInFooter,WordPress.WP.EnqueuedResourceParameters.MissingVersion
			wp_register_script( $error_handle, '' );
			wp_enqueue_script( $error_handle );
			wp_add_inline_script(
				$error_handle,
				sprintf( 'console.warn( "%s" );', $error_message )
			);
		}

		// note this WILL wipe any data already registered to these keys because they are protected.
		$this->data = array_replace_recursive( $settings, $this->get_core_data() );
	}

	/**
	 * Loops through each registered lazy data callback and adds the returned
	 * value to the data array.
	 *
	 * This method is executed right before preparing the data for printing to
	 * the rendered screen.
	 *
	 * @return void
	 */
	protected function execute_lazy_data() {
		foreach ( $this->lazy_data as $key => $callback ) {
			$this->data[ $key ] = $callback();
		}
	}

	/**
	 * Exposes private registered data to child classes.
	 *
	 * @return array  The registered data on the private data property
	 */
	protected function get() {
		return $this->data;
	}

	/**
	 * Allows checking whether a key exists.
	 *
	 * @param string $key  The key to check if exists.
	 * @return bool  Whether the key exists in the current data registry.
	 */
	public function exists( $key ) {
		return array_key_exists( $key, $this->data );
	}

	/**
	 * Interface for adding data to the registry.
	 *
	 * You can only register data that is not already in the registry identified by the given key. If there is a
	 * duplicate found, unless $ignore_duplicates is true, an exception will be thrown.
	 *
	 * @param string  $key               The key used to reference the data being registered.
	 * @param mixed   $data              If not a function, registered to the registry as is. If a function, then the
	 *                                   callback is invoked right before output to the screen.
	 * @param boolean $check_key_exists If set to true, duplicate data will be ignored if the key exists.
	 *                                  If false, duplicate data will cause an exception.
	 *
	 * @throws InvalidArgumentException  Only throws when site is in debug mode. Always logs the error.
	 */
	public function add( $key, $data, $check_key_exists = false ) {
		if ( $check_key_exists && $this->exists( $key ) ) {
			return;
		}
		try {
			$this->add_data( $key, $data );
		} catch ( Exception $e ) {
			if ( $this->debug() ) {
				// bubble up.
				throw $e;
			}
			wc_caught_exception( $e, __METHOD__, [ $key, $data ] );
		}
	}

	/**
	 * Hydrate from API.
	 *
	 * @param string $path REST API path to preload.
	 */
	public function hydrate_api_request( $path ) {
		if ( ! isset( $this->preloaded_api_requests[ $path ] ) ) {
			$this->preloaded_api_requests = rest_preload_api_request( $this->preloaded_api_requests, $path );
		}
	}

	/**
	 * Adds a page permalink to the data registry.
	 *
	 * @param integer $page_id Page ID to add to the registry.
	 */
	public function register_page_id( $page_id ) {
		$permalink = $page_id ? get_permalink( $page_id ) : false;

		if ( $permalink ) {
			$this->data[ 'page-' . $page_id ] = $permalink;
		}
	}

	/**
	 * Callback for registering the data script via WordPress API.
	 *
	 * @return void
	 */
	public function register_data_script() {
		$this->api->register_script(
			$this->handle,
			'build/wc-settings.js',
			[ 'wp-api-fetch' ],
			true
		);
	}

	/**
	 * Callback for enqueuing asset data via the WP api.
	 *
	 * Note: while this is hooked into print/admin_print_scripts, it still only
	 * happens if the script attached to `wc-settings` handle is enqueued. This
	 * is done to allow for any potentially expensive data generation to only
	 * happen for routes that need it.
	 */
	public function enqueue_asset_data() {
		if ( wp_script_is( $this->handle, 'enqueued' ) ) {
			$this->initialize_core_data();
			$this->execute_lazy_data();

			$data                          = rawurlencode( wp_json_encode( $this->data ) );
			$wc_settings_script            = "var wcSettings = wcSettings || JSON.parse( decodeURIComponent( '" . esc_js( $data ) . "' ) );";
			$preloaded_api_requests_script = '';

			if ( count( $this->preloaded_api_requests ) > 0 ) {
				$preloaded_api_requests        = rawurlencode( wp_json_encode( $this->preloaded_api_requests ) );
				$preloaded_api_requests_script = "wp.apiFetch.use( wp.apiFetch.createPreloadingMiddleware( JSON.parse( decodeURIComponent( '" . esc_js( $preloaded_api_requests ) . "' ) ) ) );";
			}

			wp_add_inline_script(
				$this->handle,
				$wc_settings_script . $preloaded_api_requests_script,
				'before'
			);
		}
	}

	/**
	 * See self::add() for docs.
	 *
	 * @param   string $key   Key for the data.
	 * @param   mixed  $data  Value for the data.
	 *
	 * @throws InvalidArgumentException  If key is not a string or already
	 *                                   exists in internal data cache.
	 */
	protected function add_data( $key, $data ) {
		if ( ! is_string( $key ) ) {
			if ( $this->debug() ) {
				throw new InvalidArgumentException(
					'Key for the data being registered must be a string'
				);
			}
		}
		if ( isset( $this->data[ $key ] ) ) {
			if ( $this->debug() ) {
				throw new InvalidArgumentException(
					'Overriding existing data with an already registered key is not allowed'
				);
			}
			return;
		}
		if ( \is_callable( $data ) ) {
			$this->lazy_data[ $key ] = $data;
			return;
		}
		$this->data[ $key ] = $data;
	}

	/**
	 * Exposes whether the current site is in debug mode or not.
	 *
	 * @return boolean  True means the site is in debug mode.
	 */
	protected function debug() {
		return defined( 'WP_DEBUG' ) && WP_DEBUG;
	}
}
