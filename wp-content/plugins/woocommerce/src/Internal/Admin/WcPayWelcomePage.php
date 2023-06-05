<?php

namespace Automattic\WooCommerce\Internal\Admin;

use Automattic\WooCommerce\Admin\Features\OnboardingTasks\Tasks\WooCommercePayments;
use Automattic\WooCommerce\Admin\WCAdminHelper;

/**
 * Class WCPayWelcomePage
 *
 * @package Automattic\WooCommerce\Admin\Features
 */
class WcPayWelcomePage {

	const EXPERIMENT_NAME = 'woocommerce_payments_menu_promo_us_2022';
	const OTHER_GATEWAYS  = [
		'affirm',
		'afterpay',
		'amazon_payments_advanced_express',
		'amazon_payments_advanced',
		'authorize_net_cim_credit_card',
		'authorize_net_cim_echeck',
		'bacs',
		'bambora_credit_card',
		'braintree_credit_card',
		'braintree_paypal',
		'chase_paymentech',
		'cybersource_credit_card',
		'elavon_converge_credit_card',
		'elavon_converge_echeck',
		'gocardless',
		'intuit_payments_credit_card',
		'intuit_payments_echeck',
		'kco',
		'klarna_payments',
		'payfast',
		'paypal',
		'paytrace',
		'ppcp-gateway',
		'psigate',
		'sagepaymentsusaapi',
		'square_credit_card',
		'stripe_alipay',
		'stripe_multibanco',
		'stripe',
		'trustcommerce',
		'usa_epay_credit_card',
	];

	/**
	 * WCPayWelcomePage constructor.
	 */
	public function __construct() {
		add_action( 'admin_menu', array( $this, 'register_payments_welcome_page' ) );
	}

	/**
	 * Registers the WooCommerce Payments welcome page.
	 */
	public function register_payments_welcome_page() {
		global $menu;

		// WC Payment must not be installed.
		if ( WooCommercePayments::is_installed() ) {
			return;
		}

		// Live store for at least 90 days.
		if ( ! WCAdminHelper::is_wc_admin_active_for( DAY_IN_SECONDS * 90 ) ) {
			return;
		}

		// Must be a US based business.
		if ( WC()->countries->get_base_country() !== 'US' ) {
			return;
		}

		// Has another payment gateway installed.
		if ( ! $this->is_another_payment_gateway_installed() ) {
			return;
		}

		// No existing WCPay account.
		if ( $this->has_wcpay_account() ) {
			return;
		}

		// Suggestions may be disabled via a setting.
		if ( get_option( 'woocommerce_show_marketplace_suggestions', 'yes' ) === 'no' ) {
			return;
		}

		/**
		 * Filter allow marketplace suggestions.
		 *
		 * User can disabled all suggestions via filter.
		 *
		 * @since 3.6.0
		 */
		if ( ! apply_filters( 'woocommerce_allow_marketplace_suggestions', true ) ) {
			return;
		}

		// Manually dismissed.
		if ( get_option( 'wc_calypso_bridge_payments_dismissed', 'no' ) === 'yes' ) {
			return;
		}

		// Users must be in the experiment.
		if ( ! $this->is_user_in_treatment_mode() ) {
			return;
		}

		$menu_icon = 'data:image/svg+xml;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0iVVRGLTgiIHN0YW5kYWxvbmU9Im5vIj8+CjxzdmcKICAgdmVyc2lvbj0iMS4xIgogICBpZD0ic3ZnNjciCiAgIHNvZGlwb2RpOmRvY25hbWU9IndjcGF5X21lbnVfaWNvbi5zdmciCiAgIHdpZHRoPSI4NTIiCiAgIGhlaWdodD0iNjg0IgogICBpbmtzY2FwZTp2ZXJzaW9uPSIxLjEgKGM0ZThmOWUsIDIwMjEtMDUtMjQpIgogICB4bWxuczppbmtzY2FwZT0iaHR0cDovL3d3dy5pbmtzY2FwZS5vcmcvbmFtZXNwYWNlcy9pbmtzY2FwZSIKICAgeG1sbnM6c29kaXBvZGk9Imh0dHA6Ly9zb2RpcG9kaS5zb3VyY2Vmb3JnZS5uZXQvRFREL3NvZGlwb2RpLTAuZHRkIgogICB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciCiAgIHhtbG5zOnN2Zz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciPgogIDxkZWZzCiAgICAgaWQ9ImRlZnM3MSIgLz4KICA8c29kaXBvZGk6bmFtZWR2aWV3CiAgICAgaWQ9Im5hbWVkdmlldzY5IgogICAgIHBhZ2Vjb2xvcj0iI2ZmZmZmZiIKICAgICBib3JkZXJjb2xvcj0iIzY2NjY2NiIKICAgICBib3JkZXJvcGFjaXR5PSIxLjAiCiAgICAgaW5rc2NhcGU6cGFnZXNoYWRvdz0iMiIKICAgICBpbmtzY2FwZTpwYWdlb3BhY2l0eT0iMC4wIgogICAgIGlua3NjYXBlOnBhZ2VjaGVja2VyYm9hcmQ9IjAiCiAgICAgc2hvd2dyaWQ9ImZhbHNlIgogICAgIGZpdC1tYXJnaW4tdG9wPSIwIgogICAgIGZpdC1tYXJnaW4tbGVmdD0iMCIKICAgICBmaXQtbWFyZ2luLXJpZ2h0PSIwIgogICAgIGZpdC1tYXJnaW4tYm90dG9tPSIwIgogICAgIGlua3NjYXBlOnpvb209IjI1NiIKICAgICBpbmtzY2FwZTpjeD0iLTg0Ljg1NzQyMiIKICAgICBpbmtzY2FwZTpjeT0iLTgzLjI5NDkyMiIKICAgICBpbmtzY2FwZTp3aW5kb3ctd2lkdGg9IjEzMTIiCiAgICAgaW5rc2NhcGU6d2luZG93LWhlaWdodD0iMTA4MSIKICAgICBpbmtzY2FwZTp3aW5kb3cteD0iMTE2IgogICAgIGlua3NjYXBlOndpbmRvdy15PSIyMDIiCiAgICAgaW5rc2NhcGU6d2luZG93LW1heGltaXplZD0iMCIKICAgICBpbmtzY2FwZTpjdXJyZW50LWxheWVyPSJzdmc2NyIgLz4KICA8cGF0aAogICAgIHRyYW5zZm9ybT0ic2NhbGUoLTEsIDEpIHRyYW5zbGF0ZSgtODUwLCAwKSIKICAgICBkPSJNIDc2OCw4NiBWIDU5OCBIIDg0IFYgODYgWiBtIDAsNTk4IGMgNDgsMCA4NCwtMzggODQsLTg2IFYgODYgQyA4NTIsMzggODE2LDAgNzY4LDAgSCA4NCBDIDM2LDAgMCwzOCAwLDg2IHYgNTEyIGMgMCw0OCAzNiw4NiA4NCw4NiB6IE0gMzg0LDEyOCB2IDQ0IGggLTg2IHYgODQgaCAxNzAgdiA0NCBIIDM0MCBjIC0yNCwwIC00MiwxOCAtNDIsNDIgdiAxMjggYyAwLDI0IDE4LDQyIDQyLDQyIGggNDQgdiA0NCBoIDg0IHYgLTQ0IGggODYgViA0MjggSCAzODQgdiAtNDQgaCAxMjggYyAyNCwwIDQyLC0xOCA0MiwtNDIgViAyMTQgYyAwLC0yNCAtMTgsLTQyIC00MiwtNDIgaCAtNDQgdiAtNDQgeiIKICAgICBmaWxsPSIjYTJhYWIyIgogICAgIGlkPSJwYXRoNjUiIC8+Cjwvc3ZnPgo=';

		$menu_data = array(
			'id'       => 'wc-calypso-bridge-payments-welcome-page',
			'title'    => __( 'Payments', 'woocommerce' ),
			'path'     => '/wc-pay-welcome-page',
			'position' => '56',
			'nav_args' => [
				'title'        => __( 'WooCommerce Payments', 'woocommerce' ),
				'is_category'  => false,
				'menuId'       => 'plugins',
				'is_top_level' => true,
			],
			'icon'     => $menu_icon,
		);

		wc_admin_register_page( $menu_data );

		// Registering a top level menu via wc_admin_register_page doesn't work when the new
		// nav is enabled. The new nav disabled everything, except the 'WooCommerce' menu.
		// We need to register this menu via add_menu_page so that it doesn't become a child of
		// WooCommerce menu.
		if ( get_option( 'woocommerce_navigation_enabled', 'no' ) === 'yes' ) {
			$menu_with_nav_data = array(
				__( 'Payments', 'woocommerce' ),
				__( 'Payments', 'woocommerce' ),
				'view_woocommerce_reports',
				'admin.php?page=wc-admin&path=/wc-pay-welcome-page',
				null,
				$menu_icon,
				56,
			);

			call_user_func_array( 'add_menu_page', $menu_with_nav_data );
		}

		// Add badge.
		foreach ( $menu as $index => $menu_item ) {
			if ( 'wc-admin&path=/wc-pay-welcome-page' === $menu_item[2]
					|| 'admin.php?page=wc-admin&path=/wc-pay-welcome-page' === $menu_item[2] ) {
				//phpcs:ignore
				$menu[ $index ][0] .= ' <span class="wcpay-menu-badge awaiting-mod count-1"><span class="plugin-count">1</span></span>';
			}
		}
	}

	/**
	 * Whether a WCPay account exists. By checking account data cache.
	 *
	 * @return boolean
	 */
	private function has_wcpay_account(): bool {
		$account_data = get_option( 'wcpay_account_data' );
		return isset( $account_data['data'] ) && is_array( $account_data['data'] ) && ! empty( $account_data['data'] );
	}

	/**
	 * Checks if user is in the experiment.
	 *
	 * @return bool Whether the user is in the treatment group.
	 */
	private function is_user_in_treatment_mode() {
		$anon_id        = isset( $_COOKIE['tk_ai'] ) ? sanitize_text_field( wp_unslash( $_COOKIE['tk_ai'] ) ) : '';
		$allow_tracking = get_option( 'woocommerce_allow_tracking' ) === 'yes';
		$abtest         = new \WooCommerce\Admin\Experimental_Abtest(
			$anon_id,
			'woocommerce',
			$allow_tracking
		);

		return $abtest->get_variation( self::EXPERIMENT_NAME ) === 'treatment';
	}

	/**
	 * Checks if there is another payment gateway installed using a static list of US gateways from WC Store.
	 *
	 * @return bool Whether there is another payment gateway installed.
	 */
	private function is_another_payment_gateway_installed() {
		$available_gateways = wp_list_pluck( WC()->payment_gateways()->get_available_payment_gateways(), 'id' );

		foreach ( $available_gateways as $gateway ) {
			if ( in_array( $gateway, self::OTHER_GATEWAYS, true ) ) {
				return true;
			}
		}

		return false;
	}

}
