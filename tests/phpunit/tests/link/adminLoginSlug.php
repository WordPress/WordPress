<?php
/**
 * Tests for the administrator login slug helpers.
 *
 * @package WordPress\Tests\Link
 */

/**
 * @group link
 */
class Tests_Link_AdminLoginSlug extends WP_UnitTestCase {

        public function setUp(): void {
                parent::setUp();

                global $wp_rewrite;

                update_option( 'permalink_structure', '/%postname%/' );
                update_option( 'admin_login_slug', 'secure-slug' );

                if ( $wp_rewrite instanceof WP_Rewrite ) {
                        $wp_rewrite->set_permalink_structure( '/%postname%/' );
                }
        }

        public function test_wp_login_url_uses_pretty_slug() {
                $login_url = wp_login_url();

                $this->assertStringContainsString( '/secure-slug/', $login_url );
        }

        public function test_admin_url_appends_slug_query_arg() {
                $admin_url = admin_url();

                $this->assertStringContainsString( 'admin_slug=secure-slug', $admin_url );
        }

        public function test_wp_login_url_with_index_permalinks_includes_index_front() {
                global $wp_rewrite;

                if ( $wp_rewrite instanceof WP_Rewrite ) {
                        $wp_rewrite->set_permalink_structure( '/index.php/%postname%/' );
                }

                update_option( 'permalink_structure', '/index.php/%postname%/' );

                $login_url = wp_login_url();

                $this->assertStringContainsString( '/index.php/secure-slug/', $login_url );
        }

        public function test_wp_maybe_redirect_admin_login_slug_honors_index_permalinks() {
                global $wp_rewrite;

                if ( $wp_rewrite instanceof WP_Rewrite ) {
                        $wp_rewrite->set_permalink_structure( '/index.php/%postname%/' );
                }

                update_option( 'permalink_structure', '/index.php/%postname%/' );

                $original_request_uri  = $_SERVER['REQUEST_URI'] ?? null;
                $original_query_string = $_SERVER['QUERY_STRING'] ?? null;

                $_SERVER['REQUEST_URI']  = '/index.php/secure-slug/';
                $_SERVER['QUERY_STRING'] = '';

                $redirect_location = null;
                $redirect_filter   = static function ( $location ) use ( &$redirect_location ) {
                        $redirect_location = $location;

                        throw new \Exception( 'redirect' );
                };

                add_filter( 'wp_redirect', $redirect_filter );

                try {
                        wp_maybe_redirect_admin_login_slug();
                        $this->fail( 'wp_maybe_redirect_admin_login_slug() did not redirect.' );
                } catch ( \Exception $exception ) {
                        $this->assertSame( 'redirect', $exception->getMessage() );
                } finally {
                        remove_filter( 'wp_redirect', $redirect_filter );
                }

                if ( null === $original_request_uri ) {
                        unset( $_SERVER['REQUEST_URI'] );
                } else {
                        $_SERVER['REQUEST_URI'] = $original_request_uri;
                }

                if ( null === $original_query_string ) {
                        unset( $_SERVER['QUERY_STRING'] );
                } else {
                        $_SERVER['QUERY_STRING'] = $original_query_string;
                }

                $expected_redirect = add_query_arg(
                        wp_admin_login_slug_query_arg(),
                        'secure-slug',
                        site_url( 'wp-login.php', 'login' )
                );

                $this->assertSame( $expected_redirect, $redirect_location );
        }

        public function test_retrieve_password_email_contains_slug() {
                $user_id = self::factory()->user->create(
                        array(
                                'user_login' => 'login-slug-user',
                                'user_pass'  => 'pass',
                                'user_email' => 'login-slug-user@example.com',
                        )
                );

                $captured   = array();
                $mock_mailer = static function ( $args ) use ( &$captured ) {
                        $captured[] = $args;

                        return $args;
                };

                add_filter( 'wp_mail', $mock_mailer );

                $result = retrieve_password( 'login-slug-user' );

                remove_filter( 'wp_mail', $mock_mailer );

                $this->assertTrue( $result );
                $this->assertNotEmpty( $captured );
                $this->assertStringContainsString( 'admin_slug=secure-slug', $captured[0]['message'] );

                wp_delete_user( $user_id );
        }
}
