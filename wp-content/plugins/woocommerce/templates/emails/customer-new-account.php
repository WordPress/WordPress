<?php
/**
 * Customer new account email
 *
 * @author 		WooThemes
 * @package 	WooCommerce/Templates/Emails
 * @version     1.6.4
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly ?>

<?php do_action( 'woocommerce_email_header', $email_heading ); ?>

<p><?php printf( __( "Thanks for creating an account on %s. Your username is <strong>%s</strong>.", 'woocommerce' ), esc_html( $blogname ), esc_html( $user_login ) ); ?></p>

<?php if ( get_option( 'woocommerce_registration_generate_password' ) == 'yes' && $password_generated ) : ?>

	<p><?php printf( __( "Your password has been automatically generated: <strong>%s</strong>", 'woocommerce' ), esc_html( $user_pass ) ); ?></p>

<?php endif; ?>

<p><?php printf( __( 'You can access your account area to view your orders and change your password here: %s.', 'woocommerce' ), get_permalink( wc_get_page_id( 'myaccount' ) ) ); ?></p>

<?php do_action( 'woocommerce_email_footer' ); ?>