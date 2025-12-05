<?php
/**
 * WPSEO plugin file.
 *
 * @package WPSEO\Admin
 */

/**
 * Loads the MyYoast proxy.
 *
 * This class registers a proxy page on `admin.php`. Which is reached with the `page=PAGE_IDENTIFIER` parameter.
 * It will read external files and serves them like they are located locally.
 */
class WPSEO_MyYoast_Proxy implements WPSEO_WordPress_Integration {

	/**
	 * The page identifier used in WordPress to register the MyYoast proxy page.
	 *
	 * @var string
	 */
	public const PAGE_IDENTIFIER = 'wpseo_myyoast_proxy';

	/**
	 * The cache control's max age. Used in the header of a successful proxy response.
	 *
	 * @var int
	 */
	public const CACHE_CONTROL_MAX_AGE = DAY_IN_SECONDS;

	/**
	 * Registers the hooks when the user is on the right page.
	 *
	 * @codeCoverageIgnore
	 *
	 * @return void
	 */
	public function register_hooks() {
		if ( ! $this->is_proxy_page() ) {
			return;
		}

		// Register the page for the proxy.
		add_action( 'admin_menu', [ $this, 'add_proxy_page' ] );
		add_action( 'admin_init', [ $this, 'handle_proxy_page' ] );
	}

	/**
	 * Registers the proxy page. It does not actually add a link to the dashboard.
	 *
	 * @codeCoverageIgnore
	 *
	 * @return void
	 */
	public function add_proxy_page() {
		add_dashboard_page( '', '', 'read', self::PAGE_IDENTIFIER, '' );
	}

	/**
	 * Renders the requested proxy page and exits to prevent the WordPress UI from loading.
	 *
	 * @codeCoverageIgnore
	 *
	 * @return void
	 */
	public function handle_proxy_page() {
		$this->render_proxy_page();

		// Prevent the WordPress UI from loading.
		exit;
	}

	/**
	 * Renders the requested proxy page.
	 *
	 * This is separated from the exits to be able to test it.
	 *
	 * @return void
	 */
	public function render_proxy_page() {
		$proxy_options = $this->determine_proxy_options();
		if ( $proxy_options === [] ) {
			// Do not accept any other file than implemented.
			$this->set_header( 'HTTP/1.0 501 Requested file not implemented' );
			return;
		}

		// Set the headers before serving the remote file.
		$this->set_header( 'Content-Type: ' . $proxy_options['content_type'] );
		$this->set_header( 'Cache-Control: max-age=' . self::CACHE_CONTROL_MAX_AGE );

		try {
			echo $this->get_remote_url_body( $proxy_options['url'] );
		} catch ( Exception $e ) {
			/*
			 * Reset the file headers because the loading failed.
			 *
			 * Note: Due to supporting PHP 5.2 `header_remove` can not be used here.
			 * Overwrite the headers instead.
			 */
			$this->set_header( 'Content-Type: text/plain' );
			$this->set_header( 'Cache-Control: max-age=0' );

			$this->set_header( 'HTTP/1.0 500 ' . $e->getMessage() );
		}
	}

	/**
	 * Tries to load the given url via `wp_remote_get`.
	 *
	 * @codeCoverageIgnore
	 *
	 * @param string $url The url to load.
	 *
	 * @return string The body of the response.
	 *
	 * @throws Exception When `wp_remote_get` returned an error.
	 * @throws Exception When the response code is not 200.
	 */
	protected function get_remote_url_body( $url ) {
		$response = wp_remote_get( $url );

		if ( $response instanceof WP_Error ) {
			throw new Exception( 'Unable to retrieve file from MyYoast' );
		}

		if ( wp_remote_retrieve_response_code( $response ) !== 200 ) {
			throw new Exception( 'Received unexpected response from MyYoast' );
		}

		return wp_remote_retrieve_body( $response );
	}

	/**
	 * Determines the proxy options based on the file and plugin version arguments.
	 *
	 * When the file is known it returns an array like this:
	 * <code>
	 * $array = array(
	 *  'content_type' => 'the content type'
	 *  'url'          => 'the url, possibly with the plugin version'
	 * )
	 * </code>
	 *
	 * @return array Empty for an unknown file. See format above for known files.
	 */
	protected function determine_proxy_options() {
		if ( $this->get_proxy_file() === 'research-webworker' ) {
			return [
				'content_type' => 'text/javascript; charset=UTF-8',
				'url'          => 'https://my.yoast.com/api/downloads/file/analysis-worker?plugin_version=' . $this->get_plugin_version(),
			];
		}

		return [];
	}

	/**
	 * Checks if the current page is the MyYoast proxy page.
	 *
	 * @codeCoverageIgnore
	 *
	 * @return bool True when the page request parameter equals the proxy page.
	 */
	protected function is_proxy_page() {
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Reason: We are not processing form information.
		$page = isset( $_GET['page'] ) && is_string( $_GET['page'] ) ? sanitize_text_field( wp_unslash( $_GET['page'] ) ) : '';
		return $page === self::PAGE_IDENTIFIER;
	}

	/**
	 * Returns the proxy file from the HTTP request parameters.
	 *
	 * @codeCoverageIgnore
	 *
	 * @return string The sanitized file request parameter or an empty string if it does not exist.
	 */
	protected function get_proxy_file() {
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Reason: We are not processing form information.
		if ( isset( $_GET['file'] ) && is_string( $_GET['file'] ) ) {
			// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Reason: We are not processing form information.
			return sanitize_text_field( wp_unslash( $_GET['file'] ) );
		}
		return '';
	}

	/**
	 * Returns the plugin version from the HTTP request parameters.
	 *
	 * @codeCoverageIgnore
	 *
	 * @return string The sanitized plugin_version request parameter or an empty string if it does not exist.
	 */
	protected function get_plugin_version() {
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Reason: We are not processing form information.
		if ( isset( $_GET['plugin_version'] ) && is_string( $_GET['plugin_version'] ) ) {
			// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Reason: We are not processing form information.
			$plugin_version = sanitize_text_field( wp_unslash( $_GET['plugin_version'] ) );
			// Replace slashes to secure against requiring a file from another path.
			return str_replace( [ '/', '\\' ], '_', $plugin_version );
		}
		return '';
	}

	/**
	 * Sets the HTTP header.
	 *
	 * This is a tiny helper function to enable better testing.
	 *
	 * @codeCoverageIgnore
	 *
	 * @param string $header The header to set.
	 *
	 * @return void
	 */
	protected function set_header( $header ) {
		header( $header );
	}
}
