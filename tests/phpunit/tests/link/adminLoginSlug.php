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

                update_option( 'permalink_structure', '/%postname%/' );
                update_option( 'admin_login_slug', 'secure-slug' );
        }

        public function test_wp_login_url_uses_pretty_slug() {
                $login_url = wp_login_url();

                $this->assertStringContainsString( '/secure-slug/', $login_url );
        }

        public function test_admin_url_appends_slug_query_arg() {
                $admin_url = admin_url();

                $this->assertStringContainsString( 'admin_slug=secure-slug', $admin_url );
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
