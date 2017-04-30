<?php
/**
 * Customer invoice email (plain text)
 *
 * @author		WooThemes
 * @package		WooCommerce/Templates/Emails/Plain
 * @version		2.0.0
 */
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

echo $email_heading . "\n\n";

if ( $order->status == 'pending' )
	echo sprintf( __( 'An order has been created for you on %s. To pay for this order please use the following link: %s', 'woocommerce' ), get_bloginfo( 'name' ), $order->get_checkout_payment_url() ) . "\n\n";

echo "****************************************************\n\n";

do_action( 'woocommerce_email_before_order_table', $order, $sent_to_admin, $plain_text );

echo sprintf( __( 'Order number: %s', 'woocommerce'), $order->get_order_number() ) . "\n";
echo sprintf( __( 'Order date: %s', 'woocommerce'), date_i18n( wc_date_format(), strtotime( $order->order_date ) ) ) . "\n";

do_action( 'woocommerce_email_order_meta', $order, $sent_to_admin, $plain_text );

echo "\n";

switch ( $order->status ) {
	case "completed" :
		echo $order->email_order_items_table( $order->is_download_permitted(), false, true, '', '', true );
	break;
	case "processing" :
		echo $order->email_order_items_table( $order->is_download_permitted(), true, true, '', '', true );
	break;
	default :
		echo $order->email_order_items_table( $order->is_download_permitted(), true, false, '', '', true );
	break;
}

echo "----------\n\n";

if ( $totals = $order->get_order_item_totals() ) {
	foreach ( $totals as $total ) {
		echo $total['label'] . "\t " . $total['value'] . "\n";
	}
}

echo "\n****************************************************\n\n";

do_action( 'woocommerce_email_after_order_table', $order, $sent_to_admin, $plain_text );

echo apply_filters( 'woocommerce_email_footer_text', get_option( 'woocommerce_email_footer_text' ) );