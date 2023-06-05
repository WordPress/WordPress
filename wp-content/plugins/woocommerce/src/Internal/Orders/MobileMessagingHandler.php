<?php

namespace Automattic\WooCommerce\Internal\Orders;

use DateTime;
use Exception;
use WC_Order;
use WC_Tracker;

/**
 * Prepares formatted mobile deep link navigation link for order mails.
 */
class MobileMessagingHandler {

	private const OPEN_ORDER_INTERVAL_DAYS = 30;

	/**
	 * Prepares mobile messaging with a deep link.
	 *
	 * @param WC_Order $order order that mobile message is created for.
	 * @param ?int     $blog_id  of blog to make a deep link for (will be null if Jetpack is not enabled).
	 * @param DateTime $now      current DateTime.
	 * @param string   $domain URL of the current site.
	 *
	 * @return ?string
	 */
	public static function prepare_mobile_message(
		WC_Order $order,
		?int $blog_id,
		DateTime $now,
		string $domain
	): ?string {
		try {
			$last_mobile_used = self::get_closer_mobile_usage_date();

			$used_app_in_last_month = null !== $last_mobile_used && $last_mobile_used->diff( $now )->days <= self::OPEN_ORDER_INTERVAL_DAYS;
			$has_jetpack            = null !== $blog_id;

			if ( IppFunctions::is_store_in_person_payment_eligible() && IppFunctions::is_order_in_person_payment_eligible( $order ) ) {
				return self::accept_payment_message( $blog_id, $domain );
			} else {
				if ( $used_app_in_last_month && $has_jetpack ) {
					return self::manage_order_message( $blog_id, $order->get_id(), $domain );
				} else {
					return self::no_app_message( $blog_id, $domain );
				}
			}
		} catch ( Exception $e ) {
			return null;
		}
	}

	/**
	 * Returns the closest date of last usage of any mobile app platform.
	 *
	 * @return ?DateTime
	 */
	private static function get_closer_mobile_usage_date(): ?DateTime {
		$mobile_usage = WC_Tracker::get_woocommerce_mobile_usage();

		if ( ! $mobile_usage ) {
			return null;
		}

		$last_ios_used     = self::get_last_used_or_null( 'ios', $mobile_usage );
		$last_android_used = self::get_last_used_or_null( 'android', $mobile_usage );

		return max( $last_android_used, $last_ios_used );
	}

	/**
	 * Returns last used date of specified mobile app platform.
	 *
	 * @param string $platform     mobile platform to check.
	 * @param array  $mobile_usage mobile apps usage data.
	 *
	 * @return ?DateTime last used date of specified mobile app
	 */
	private static function get_last_used_or_null(
		string $platform, array $mobile_usage
	): ?DateTime {
		try {
			if ( array_key_exists( $platform, $mobile_usage ) ) {
				return new DateTime( $mobile_usage[ $platform ]['last_used'] );
			} else {
				return null;
			}
		} catch ( Exception $e ) {
			return null;
		}
	}

	/**
	 * Prepares message with a deep link to mobile payment.
	 *
	 * @param ?int   $blog_id blog id to deep link to.
	 * @param string $domain URL of the current site.
	 *
	 * @return string formatted message
	 */
	private static function accept_payment_message( ?int $blog_id, $domain ): string {
		$deep_link_url = add_query_arg(
			array_merge(
				array(
					'blog_id' => absint( $blog_id ),
				),
				self::prepare_utm_parameters( 'deeplinks_payments', $blog_id, $domain )
			),
			'https://woocommerce.com/mobile/payments'
		);

		return sprintf(
			/* translators: 1: opening link tag 2: closing link tag. */
			esc_html__(
				'%1$sCollect payments easily%2$s from your customers anywhere with our mobile app.',
				'woocommerce'
			),
			'<a href="' . esc_url( $deep_link_url ) . '">',
			'</a>'
		);
	}

	/**
	 * Prepares message with a deep link to manage order details.
	 *
	 * @param int    $blog_id blog id to deep link to.
	 * @param int    $order_id order id to deep link to.
	 * @param string $domain URL of the current site.
	 *
	 * @return string formatted message
	 */
	private static function manage_order_message( int $blog_id, int $order_id, string $domain ): string {
		$deep_link_url = add_query_arg(
			array_merge(
				array(
					'blog_id'  => absint( $blog_id ),
					'order_id' => absint( $order_id ),
				),
				self::prepare_utm_parameters( 'deeplinks_orders_details', $blog_id, $domain )
			),
			'https://woocommerce.com/mobile/orders/details'
		);

		return sprintf(
			/* translators: 1: opening link tag 2: closing link tag. */
			esc_html__(
				'%1$sManage the order%2$s with the app.',
				'woocommerce'
			),
			'<a href="' . esc_url( $deep_link_url ) . '">',
			'</a>'
		);
	}

	/**
	 * Prepares message with a deep link to learn more about mobile app.
	 *
	 * @param ?int   $blog_id blog id used for tracking.
	 * @param string $domain URL of the current site.
	 *
	 * @return string formatted message
	 */
	private static function no_app_message( ?int $blog_id, string $domain ): string {
		$deep_link_url = add_query_arg(
			array_merge(
				array(
					'blog_id' => absint( $blog_id ),
				),
				self::prepare_utm_parameters( 'deeplinks_promote_app', $blog_id, $domain )
			),
			'https://woocommerce.com/mobile'
		);
		return sprintf(
			/* translators: 1: opening link tag 2: closing link tag. */
			esc_html__(
				'Process your orders on the go. %1$sGet the app%2$s.',
				'woocommerce'
			),
			'<a href="' . esc_url( $deep_link_url ) . '">',
			'</a>'
		);
	}

	/**
	 * Prepares array of parameters used by WooCommerce.com for tracking.
	 *
	 * @param string   $campaign name of the deep link campaign.
	 * @param int|null $blog_id blog id of the current site.
	 * @param string   $domain URL of the current site.
	 *
	 * @return array
	 */
	private static function prepare_utm_parameters(
		string $campaign,
		?int $blog_id,
		string $domain
	): array {
		return array(
			'utm_campaign' => $campaign,
			'utm_medium'   => 'email',
			'utm_source'   => $domain,
			'utm_term'     => absint( $blog_id ),
		);
	}
}
