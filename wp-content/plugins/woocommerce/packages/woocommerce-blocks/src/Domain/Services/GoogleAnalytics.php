<?php
namespace Automattic\WooCommerce\Blocks\Domain\Services;

use Automattic\WooCommerce\Blocks\Package;
use Automattic\WooCommerce\Blocks\Assets\Api as AssetApi;

/**
 * Service class to integrate Blocks with the Google Analytics extension,
 */
class GoogleAnalytics {
	/**
	 * Instance of the asset API.
	 *
	 * @var AssetApi
	 */
	protected $asset_api;

	/**
	 * Constructor.
	 *
	 * @param AssetApi $asset_api Instance of the asset API.
	 */
	public function __construct( AssetApi $asset_api ) {
		$this->asset_api = $asset_api;
		$this->init();
	}

	/**
	 * Hook into WP.
	 */
	protected function init() {
		add_action( 'init', array( $this, 'register_assets' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
		add_filter( 'script_loader_tag', array( $this, 'async_script_loader_tags' ), 10, 3 );
	}

	/**
	 * Register scripts.
	 */
	public function register_assets() {
		$this->asset_api->register_script( 'wc-blocks-google-analytics', 'build/wc-blocks-google-analytics.js', [ 'google-tag-manager' ] );
	}

	/**
	 * Enqueue the Google Tag Manager script if prerequisites are met.
	 */
	public function enqueue_scripts() {
		$settings = $this->get_google_analytics_settings();
		$prefix   = strstr( strtoupper( $settings['ga_id'] ), '-', true );

		// Require tracking to be enabled with a valid GA ID.
		if ( ! in_array( $prefix, [ 'G', 'GT' ], true ) ) {
			return;
		}

		/**
		 * Filter to disable Google Analytics tracking.
		 *
		 * @internal Matches filter name in GA extension.
		 * @since 4.9.0
		 *
		 * @param boolean $disable_tracking If true, tracking will be disabled.
		 */
		if ( apply_filters( 'woocommerce_ga_disable_tracking', ! wc_string_to_bool( $settings['ga_event_tracking_enabled'] ) ) ) {
			return;
		}

		if ( ! wp_script_is( 'google-tag-manager', 'registered' ) ) {
			// phpcs:ignore WordPress.WP.EnqueuedResourceParameters.MissingVersion
			wp_register_script( 'google-tag-manager', 'https://www.googletagmanager.com/gtag/js?id=' . $settings['ga_id'], [], null, false );
			wp_add_inline_script(
				'google-tag-manager',
				"
	window.dataLayer = window.dataLayer || [];
	function gtag(){dataLayer.push(arguments);}
	gtag('js', new Date());
	gtag('config', '" . esc_js( $settings['ga_id'] ) . "', { 'send_page_view': false });"
			);
		}
		wp_enqueue_script( 'wc-blocks-google-analytics' );
	}

	/**
	 * Get settings from the GA integration extension.
	 *
	 * @return array
	 */
	private function get_google_analytics_settings() {
		return wp_parse_args(
			get_option( 'woocommerce_google_analytics_settings' ),
			[
				'ga_id'                     => '',
				'ga_event_tracking_enabled' => 'no',
			]
		);
	}

	/**
	 * Add async to script tags with defined handles.
	 *
	 * @param string $tag HTML for the script tag.
	 * @param string $handle Handle of script.
	 * @param string $src Src of script.
	 * @return string
	 */
	public function async_script_loader_tags( $tag, $handle, $src ) {
		if ( ! in_array( $handle, array( 'google-tag-manager' ), true ) ) {
			return $tag;
		}
		// If script was output manually in wp_head, abort.
		if ( did_action( 'woocommerce_gtag_snippet' ) ) {
			return '';
		}
		return str_replace( '<script src', '<script async src', $tag );
	}
}
