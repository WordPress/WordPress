<?php
/**
 * WooCommerce Webhook functions
 *
 * @package WooCommerce\Functions
 * @version 3.3.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * Process the web hooks at the end of the request.
 *
 * @since 4.4.0
 */
function wc_webhook_execute_queue() {
	global $wc_queued_webhooks;
	if ( empty( $wc_queued_webhooks ) ) {
		return;
	}

	foreach ( $wc_queued_webhooks as $data ) {
		// Webhooks are processed in the background by default
		// so as to avoid delays or failures in delivery from affecting the
		// user who triggered it.
		if ( apply_filters( 'woocommerce_webhook_deliver_async', true, $data['webhook'], $data['arg'] ) ) {

			$queue_args = array(
				'webhook_id' => $data['webhook']->get_id(),
				'arg'        => $data['arg'],
			);

			$next_scheduled_date = WC()->queue()->get_next( 'woocommerce_deliver_webhook_async', $queue_args, 'woocommerce-webhooks' );

			// Make webhooks unique - only schedule one webhook every 10 minutes to maintain backward compatibility with WP Cron behaviour seen in WC < 3.5.0.
			if ( is_null( $next_scheduled_date ) || $next_scheduled_date->getTimestamp() >= ( 600 + gmdate( 'U' ) ) ) {
				WC()->queue()->add( 'woocommerce_deliver_webhook_async', $queue_args, 'woocommerce-webhooks' );
			}
		} else {
			// Deliver immediately.
			$data['webhook']->deliver( $data['arg'] );
		}
	}
}
add_action( 'shutdown', 'wc_webhook_execute_queue' );

/**
 * Process webhook delivery.
 *
 * @since 3.3.0
 * @param WC_Webhook $webhook Webhook instance.
 * @param array      $arg     Delivery arguments.
 */
function wc_webhook_process_delivery( $webhook, $arg ) {
	// We need to queue the webhook so that it can be ran after the request has finished processing.
	global $wc_queued_webhooks;
	if ( ! isset( $wc_queued_webhooks ) ) {
		$wc_queued_webhooks = array();
	}
	$wc_queued_webhooks[] = array(
		'webhook' => $webhook,
		'arg'     => $arg,
	);
}
add_action( 'woocommerce_webhook_process_delivery', 'wc_webhook_process_delivery', 10, 2 );

/**
 * Wrapper function to execute the `woocommerce_deliver_webhook_async` cron.
 * hook, see WC_Webhook::process().
 *
 * @since 2.2.0
 * @param int   $webhook_id Webhook ID to deliver.
 * @throws Exception        If webhook cannot be read/found and $data parameter of WC_Webhook class constructor is set.
 * @param mixed $arg        Hook argument.
 */
function wc_deliver_webhook_async( $webhook_id, $arg ) {
	$webhook = new WC_Webhook( $webhook_id );
	$webhook->deliver( $arg );
}
add_action( 'woocommerce_deliver_webhook_async', 'wc_deliver_webhook_async', 10, 2 );

/**
 * Check if the given topic is a valid webhook topic, a topic is valid if:
 *
 * + starts with `action.woocommerce_` or `action.wc_`.
 * + it has a valid resource & event.
 *
 * @since  2.2.0
 * @param  string $topic Webhook topic.
 * @return bool
 */
function wc_is_webhook_valid_topic( $topic ) {
	$invalid_topics = array(
		'action.woocommerce_login_credentials',
		'action.woocommerce_product_csv_importer_check_import_file_path',
		'action.woocommerce_webhook_should_deliver',
	);

	if ( in_array( $topic, $invalid_topics, true ) ) {
		return false;
	}

	// Custom topics are prefixed with woocommerce_ or wc_ are valid.
	if ( 0 === strpos( $topic, 'action.woocommerce_' ) || 0 === strpos( $topic, 'action.wc_' ) ) {
		return true;
	}

	$data = explode( '.', $topic );

	if ( ! isset( $data[0] ) || ! isset( $data[1] ) ) {
		return false;
	}

	$valid_resources = apply_filters( 'woocommerce_valid_webhook_resources', array( 'coupon', 'customer', 'order', 'product' ) );
	$valid_events    = apply_filters( 'woocommerce_valid_webhook_events', array( 'created', 'updated', 'deleted', 'restored' ) );

	if ( in_array( $data[0], $valid_resources, true ) && in_array( $data[1], $valid_events, true ) ) {
		return true;
	}

	return false;
}

/**
 * Check if given status is a valid webhook status.
 *
 * @since 3.5.3
 * @param string $status Status to check.
 * @return bool
 */
function wc_is_webhook_valid_status( $status ) {
	return in_array( $status, array_keys( wc_get_webhook_statuses() ), true );
}

/**
 * Get Webhook statuses.
 *
 * @since  2.3.0
 * @return array
 */
function wc_get_webhook_statuses() {
	return apply_filters(
		'woocommerce_webhook_statuses',
		array(
			'active'   => __( 'Active', 'woocommerce' ),
			'paused'   => __( 'Paused', 'woocommerce' ),
			'disabled' => __( 'Disabled', 'woocommerce' ),
		)
	);
}

/**
 * Load webhooks.
 *
 * @since  3.3.0
 * @throws Exception If webhook cannot be read/found and $data parameter of WC_Webhook class constructor is set.
 * @param  string   $status Optional - status to filter results by. Must be a key in return value of @see wc_get_webhook_statuses(). @since 3.5.0.
 * @param  null|int $limit Limit number of webhooks loaded. @since 3.6.0.
 * @return bool
 */
function wc_load_webhooks( $status = '', $limit = null ) {
	$data_store = WC_Data_Store::load( 'webhook' );
	$webhooks   = $data_store->get_webhooks_ids( $status );
	$loaded     = 0;

	foreach ( $webhooks as $webhook_id ) {
		if ( ! is_null( $limit ) && $loaded >= $limit ) {
			break;
		}

		$webhook = new WC_Webhook( $webhook_id );
		$webhook->enqueue();
		$loaded ++;
	}

	return 0 < $loaded;
}

/**
 * Get webhook.
 *
 * @param  int|WC_Webhook $id Webhook ID or object.
 * @throws Exception          If webhook cannot be read/found and $data parameter of WC_Webhook class constructor is set.
 * @return WC_Webhook|null
 */
function wc_get_webhook( $id ) {
	$webhook = new WC_Webhook( $id );

	return 0 !== $webhook->get_id() ? $webhook : null;
}

/**
 * Get webhoook REST API versions.
 *
 * @since 3.5.1
 * @return array
 */
function wc_get_webhook_rest_api_versions() {
	return array(
		'wp_api_v1',
		'wp_api_v2',
		'wp_api_v3',
	);
}
