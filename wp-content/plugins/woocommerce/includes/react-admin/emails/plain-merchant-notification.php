<?php
/**
 * Merchant notification email (plain text)
 *
 * @package WooCommerce\Admin\Templates\Emails\HTML
 */

defined( 'ABSPATH' ) || exit;

echo "=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=\n";
echo esc_html( wp_strip_all_tags( $email_heading ) );
echo "\n=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=\n\n";

echo wp_kses_post( $email_content );

foreach ( $email_actions as $an_action ) {
	echo "\n";
	/* translators: %1$s: action label, %2$s: action URL */
	echo wp_kses_post( sprintf( __( '%1$s: %2$s', 'woocommerce' ), $an_action->label, $trigger_note_action_url . $an_action->id ) );
}
echo "\n\n----------------------------------------\n\n";

echo wp_kses_post( apply_filters( 'woocommerce_email_footer_text', get_option( 'woocommerce_email_footer_text' ) ) );
