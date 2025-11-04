<?php
/**
 * Tests for multi-factor authentication helpers.
 *
 * @group user
 * @group authentication
 */
class Tests_Authentication_MFA extends WP_UnitTestCase {

        public function test_register_user_auth_factor_persists_factor() {
                $user_id = self::factory()->user->create();

                $factor_id = 'email-' . wp_generate_password( 6, false, false );
                $factor    = array(
                        'id'    => $factor_id,
                        'type'  => 'email',
                        'label' => 'Email codes',
                        'added' => time(),
                );

                wp_register_user_auth_factor( $user_id, $factor );

                $stored = wp_get_user_auth_factors( $user_id );

                $this->assertArrayHasKey( $factor_id, $stored );
                $this->assertSame( 'email', $stored[ $factor_id ]['type'] );
        }

        public function test_verify_totp_factor_matches_code() {
                $user_id = self::factory()->user->create();
                $factor_id = 'totp-test';
                $secret    = 'JBSWY3DPEHPK3PXP';

                wp_register_user_auth_factor(
                        $user_id,
                        array(
                                'id'     => $factor_id,
                                'type'   => 'totp',
                                'label'  => 'Authenticator',
                                'added'  => time(),
                                'secret' => wp_encrypt_user_mfa_secret( $secret, $factor_id ),
                        )
                );

                $bytes = wp_mfa_base32_decode( $secret );
                $code  = wp_generate_totp_from_secret( $bytes, time() );

                $this->assertTrue( wp_verify_user_totp_factor( $user_id, $code ) );
        }

        public function test_sanitize_mfa_policy_converts_minutes() {
                $raw = array(
                        'required_roles'   => array( 'administrator', ' ', 'editor' ),
                        'email_provider'   => 'Mailgun',
                        'email_rate_limit' => '10',
                        'email_window'     => '3',
                        'recovery_codes'   => "code-one\ncode-two\n",
                );

                $sanitized = wp_sanitize_mfa_policy( $raw );

                $this->assertSame( array( 'administrator', 'editor' ), $sanitized['required_roles'] );
                $this->assertSame( 'Mailgun', $sanitized['email_provider'] );
                $this->assertSame( 10, $sanitized['email_rate_limit'] );
                $this->assertSame( 3 * MINUTE_IN_SECONDS, $sanitized['email_window'] );
                $this->assertSame( array( 'code-one', 'code-two' ), $sanitized['recovery_codes'] );
        }

        public function test_email_rate_limit_cleanup_purges_transient() {
                $user_id = self::factory()->user->create();
                set_transient( 'wp_mfa_email_' . $user_id, array( time() - ( 10 * MINUTE_IN_SECONDS ) ), 15 * MINUTE_IN_SECONDS );
                update_option( 'wp_mfa_rate_limit_users', array( $user_id => time() - ( 10 * MINUTE_IN_SECONDS ) ) );

                wp_mfa_cleanup_email_rate_limits();

                $this->assertFalse( get_transient( 'wp_mfa_email_' . $user_id ) );
                delete_option( 'wp_mfa_rate_limit_users' );
        }
}
