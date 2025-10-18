<?php
/**
 * Tests for request locale detection before installation.
 *
 * @package WordPress\Tests\Load
 */

if ( ! class_exists( 'WP_Redirect_Exception' ) ) {
	class WP_Redirect_Exception extends Exception {}
}

/**
 * @group load
 */
class Tests_Load_Translation_Request_Locale extends WP_UnitTestCase {

	/**
	 * Original global wpdb instance.
	 *
	 * @var wpdb|null
	 */
	private $original_wpdb = null;

	public function tearDown(): void {
		remove_filter( 'wp_redirect', array( $this, 'intercept_redirect_to_install' ), 10 );

		$this->restore_original_wpdb();

		wp_cache_delete( 'is_blog_installed' );

		parent::tearDown();
	}

	/**
	 * Ensures that locale detection bails and the installer is reached when the schema is absent.
	 */
	public function test_empty_database_request_reaches_installer() {
		$this->switch_to_temporary_wpdb();

		wp_cache_delete( 'is_blog_installed' );

		add_filter( 'wp_redirect', array( $this, 'intercept_redirect_to_install' ), 10, 2 );

		try {
			$this->assertNull(
				wp_translation_detect_request_locale(),
				'Locale detection should bail out before installation.'
			);

			wp_not_installed();

			$this->fail( 'wp_not_installed() did not redirect to the installer.' );
		} catch ( WP_Redirect_Exception $exception ) {
			$this->assertStringContainsString( 'install.php', $exception->getMessage() );
		}
	}

	/**
	 * Converts the redirect to an exception for easier assertions.
	 *
	 * @param string $location Redirect location.
	 * @param int    $status   HTTP status code.
	 * @return string Redirect location.
	 *
	 * @throws WP_Redirect_Exception Intercepts the redirect during the test.
	 */
	public function intercept_redirect_to_install( $location, $status ) {
		throw new WP_Redirect_Exception( $location );

		return $location;
	}

	/**
	 * Switches the global wpdb instance to an empty temporary prefix.
	 */
	private function switch_to_temporary_wpdb() {
		global $wpdb;

		if ( null !== $this->original_wpdb ) {
			return;
		}

		$prefix = 'wptemp_' . uniqid() . '_';

		$this->original_wpdb = $wpdb;
		$wpdb                = new wpdb( DB_USER, DB_PASSWORD, DB_NAME, DB_HOST );
		$wpdb->set_prefix( $prefix );
		$wpdb->suppress_errors( true );
		$wpdb->show_errors( false );
	}

	/**
	 * Restores the original wpdb instance after the test runs.
	 */
	private function restore_original_wpdb() {
		if ( null === $this->original_wpdb ) {
			return;
		}

		global $wpdb;

		$wpdb = $this->original_wpdb;

		$this->original_wpdb = null;
	}
}
