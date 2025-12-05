<?php

namespace Yoast\WP\SEO\Helpers;

/**
 * Helper to get shortlinks for Yoast SEO.
 */
class Short_Link_Helper {

	/**
	 * The options helper.
	 *
	 * @var Options_Helper
	 */
	protected $options_helper;

	/**
	 * The product helper.
	 *
	 * @var Product_Helper
	 */
	protected $product_helper;

	/**
	 * Short_Link_Helper constructor.
	 *
	 * @param Options_Helper $options_helper The options helper.
	 * @param Product_Helper $product_helper The product helper.
	 */
	public function __construct( Options_Helper $options_helper, Product_Helper $product_helper ) {
		$this->options_helper = $options_helper;
		$this->product_helper = $product_helper;
	}

	/**
	 * Builds a URL to use in the plugin as shortlink.
	 *
	 * @param string $url The URL to build upon.
	 *
	 * @return string The final URL.
	 */
	public function build( $url ) {
		return \add_query_arg( $this->collect_additional_shortlink_data(), $url );
	}

	/**
	 * Returns a version of the URL with a utm_content with the current version.
	 *
	 * @param string $url The URL to build upon.
	 *
	 * @return string The final URL.
	 */
	public function get( $url ) {
		return $this->build( $url );
	}

	/**
	 * Echoes a version of the URL with a utm_content with the current version.
	 *
	 * @param string $url The URL to build upon.
	 *
	 * @return void
	 */
	public function show( $url ) {
		echo \esc_url( $this->get( $url ) );
	}

	/**
	 * Gets the shortlink's query params.
	 *
	 * @return array The shortlink's query params.
	 */
	public function get_query_params() {
		return $this->collect_additional_shortlink_data();
	}

	/**
	 * Gets the current site's PHP version, without the extra info.
	 *
	 * @return string The PHP version.
	 */
	private function get_php_version() {
		$version = \explode( '.', \PHP_VERSION );

		return (int) $version[0] . '.' . (int) $version[1];
	}

	/**
	 * Gets the current site's platform version.
	 *
	 * @return string The wp_version.
	 */
	protected function get_platform_version() {
		return $GLOBALS['wp_version'];
	}

	/**
	 * Collects the additional data necessary for the shortlink.
	 *
	 * @return array The shortlink data.
	 */
	protected function collect_additional_shortlink_data() {
		$data = [
			'php_version'      => $this->get_php_version(),
			'platform'         => 'wordpress',
			'platform_version' => $this->get_platform_version(),
			'software'         => $this->get_software(),
			'software_version' => \WPSEO_VERSION,
			'days_active'      => $this->get_days_active(),
			'user_language'    => \get_user_locale(),
		];

		// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Reason: We are not processing form information.
		if ( isset( $_GET['page'] ) && \is_string( $_GET['page'] ) ) {
			// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Reason: We are not processing form information.
			$admin_page = \sanitize_text_field( \wp_unslash( $_GET['page'] ) );
			if ( ! empty( $admin_page ) ) {
				$data['screen'] = $admin_page;
			}
		}

		return $data;
	}

	/**
	 * Get our software and whether it's active or not.
	 *
	 * @return string The software name.
	 */
	protected function get_software() {
		if ( $this->product_helper->is_premium() ) {
			return 'premium';
		}

		return 'free';
	}

	/**
	 * Gets the number of days the plugin has been active.
	 *
	 * @return int The number of days the plugin is active.
	 */
	protected function get_days_active() {
		$date_activated = $this->options_helper->get( 'first_activated_on' );
		$datediff       = ( \time() - $date_activated );

		return (int) \round( $datediff / \DAY_IN_SECONDS );
	}
}
