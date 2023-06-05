<?php
/**
 * InstalledExtensions class file.
 */

namespace Automattic\WooCommerce\Admin\Marketing;

use Automattic\WooCommerce\Admin\PluginsHelper;

/**
 * Installed Marketing Extensions class.
 */
class InstalledExtensions {

	/**
	 * Gets an array of plugin data for the "Installed marketing extensions" card.
	 *
	 * Valid extensions statuses are: installed, activated, configured
	 */
	public static function get_data() {
		$data = [];

		$automatewoo   = self::get_automatewoo_extension_data();
		$aw_referral   = self::get_aw_referral_extension_data();
		$aw_birthdays  = self::get_aw_birthdays_extension_data();
		$mailchimp     = self::get_mailchimp_extension_data();
		$facebook      = self::get_facebook_extension_data();
		$pinterest     = self::get_pinterest_extension_data();
		$google        = self::get_google_extension_data();
		$amazon_ebay   = self::get_amazon_ebay_extension_data();
		$mailpoet      = self::get_mailpoet_extension_data();
		$creative_mail = self::get_creative_mail_extension_data();
		$tiktok        = self::get_tiktok_extension_data();
		$jetpack_crm   = self::get_jetpack_crm_extension_data();
		$zapier        = self::get_zapier_extension_data();
		$salesforce    = self::get_salesforce_extension_data();
		$vimeo         = self::get_vimeo_extension_data();
		$trustpilot    = self::get_trustpilot_extension_data();

		if ( $automatewoo ) {
			$data[] = $automatewoo;
		}

		if ( $aw_referral ) {
			$data[] = $aw_referral;
		}

		if ( $aw_birthdays ) {
			$data[] = $aw_birthdays;
		}

		if ( $mailchimp ) {
			$data[] = $mailchimp;
		}

		if ( $facebook ) {
			$data[] = $facebook;
		}

		if ( $pinterest ) {
			$data[] = $pinterest;
		}

		if ( $google ) {
			$data[] = $google;
		}

		if ( $amazon_ebay ) {
			$data[] = $amazon_ebay;
		}

		if ( $mailpoet ) {
			$data[] = $mailpoet;
		}

		if ( $creative_mail ) {
			$data[] = $creative_mail;
		}

		if ( $tiktok ) {
			$data[] = $tiktok;
		}

		if ( $jetpack_crm ) {
			$data[] = $jetpack_crm;
		}

		if ( $zapier ) {
			$data[] = $zapier;
		}

		if ( $salesforce ) {
			$data[] = $salesforce;
		}

		if ( $vimeo ) {
			$data[] = $vimeo;
		}

		if ( $trustpilot ) {
			$data[] = $trustpilot;
		}

		return $data;
	}

	/**
	 * Get allowed plugins.
	 *
	 * @return array
	 */
	public static function get_allowed_plugins() {
		return [
			'automatewoo',
			'mailchimp-for-woocommerce',
			'creative-mail-by-constant-contact',
			'facebook-for-woocommerce',
			'pinterest-for-woocommerce',
			'google-listings-and-ads',
			'hubspot-for-woocommerce',
			'woocommerce-amazon-ebay-integration',
			'mailpoet',
		];
	}

	/**
	 * Get AutomateWoo extension data.
	 *
	 * @return array|bool
	 */
	protected static function get_automatewoo_extension_data() {
		$slug = 'automatewoo';

		if ( ! PluginsHelper::is_plugin_installed( $slug ) ) {
			return false;
		}

		$data         = self::get_extension_base_data( $slug );
		$data['icon'] = WC_ADMIN_IMAGES_FOLDER_URL . '/marketing/automatewoo.svg';

		if ( 'activated' === $data['status'] && function_exists( 'AW' ) ) {
			$data['settingsUrl'] = admin_url( 'admin.php?page=automatewoo-settings' );
			$data['docsUrl']     = 'https://automatewoo.com/docs/';
			$data['status']      = 'configured'; // Currently no configuration step.
		}

		return $data;
	}

	/**
	 * Get AutomateWoo Refer a Friend extension data.
	 *
	 * @return array|bool
	 */
	protected static function get_aw_referral_extension_data() {
		$slug = 'automatewoo-referrals';

		if ( ! PluginsHelper::is_plugin_installed( $slug ) ) {
			return false;
		}

		$data         = self::get_extension_base_data( $slug );
		$data['icon'] = WC_ADMIN_IMAGES_FOLDER_URL . '/marketing/automatewoo.svg';

		if ( 'activated' === $data['status'] ) {
			$data['docsUrl'] = 'https://automatewoo.com/docs/refer-a-friend/';
			$data['status']  = 'configured';
			if ( function_exists( 'AW_Referrals' ) ) {
				$data['settingsUrl'] = admin_url( 'admin.php?page=automatewoo-settings&tab=referrals' );
			}
		}

		return $data;
	}

	/**
	 * Get AutomateWoo Birthdays extension data.
	 *
	 * @return array|bool
	 */
	protected static function get_aw_birthdays_extension_data() {
		$slug = 'automatewoo-birthdays';

		if ( ! PluginsHelper::is_plugin_installed( $slug ) ) {
			return false;
		}

		$data         = self::get_extension_base_data( $slug );
		$data['icon'] = WC_ADMIN_IMAGES_FOLDER_URL . '/marketing/automatewoo.svg';

		if ( 'activated' === $data['status'] ) {
			$data['docsUrl'] = 'https://automatewoo.com/docs/getting-started-with-birthdays/';
			$data['status']  = 'configured';
			if ( function_exists( 'AW_Birthdays' ) ) {
				$data['settingsUrl'] = admin_url( 'admin.php?page=automatewoo-settings&tab=birthdays' );
			}
		}

		return $data;
	}

	/**
	 * Get MailChimp extension data.
	 *
	 * @return array|bool
	 */
	protected static function get_mailchimp_extension_data() {
		$slug = 'mailchimp-for-woocommerce';

		if ( ! PluginsHelper::is_plugin_installed( $slug ) ) {
			return false;
		}

		$data         = self::get_extension_base_data( $slug );
		$data['icon'] = WC_ADMIN_IMAGES_FOLDER_URL . '/marketing/mailchimp.svg';

		if ( 'activated' === $data['status'] && function_exists( 'mailchimp_is_configured' ) ) {
			$data['docsUrl']     = 'https://mailchimp.com/help/connect-or-disconnect-mailchimp-for-woocommerce/';
			$data['settingsUrl'] = admin_url( 'admin.php?page=mailchimp-woocommerce' );

			if ( mailchimp_is_configured() ) {
				$data['status'] = 'configured';
			}
		}

		return $data;
	}

	/**
	 * Get Facebook extension data.
	 *
	 * @return array|bool
	 */
	protected static function get_facebook_extension_data() {
		$slug = 'facebook-for-woocommerce';

		if ( ! PluginsHelper::is_plugin_installed( $slug ) ) {
			return false;
		}

		$data         = self::get_extension_base_data( $slug );
		$data['icon'] = WC_ADMIN_IMAGES_FOLDER_URL . '/marketing/facebook-icon.svg';

		if ( 'activated' === $data['status'] && function_exists( 'facebook_for_woocommerce' ) ) {
			$integration = facebook_for_woocommerce()->get_integration();

			if ( $integration->is_configured() ) {
				$data['status'] = 'configured';
			}

			$data['settingsUrl'] = facebook_for_woocommerce()->get_settings_url();
			$data['docsUrl']     = facebook_for_woocommerce()->get_documentation_url();
		}

		return $data;
	}

	/**
	 * Get Pinterest extension data.
	 *
	 * @return array|bool
	 */
	protected static function get_pinterest_extension_data() {
		$slug = 'pinterest-for-woocommerce';

		if ( ! PluginsHelper::is_plugin_installed( $slug ) ) {
			return false;
		}

		$data         = self::get_extension_base_data( $slug );
		$data['icon'] = WC_ADMIN_IMAGES_FOLDER_URL . '/marketing/pinterest.svg';

		$data['docsUrl'] = 'https://woocommerce.com/document/pinterest-for-woocommerce/?utm_medium=product';

		if ( 'activated' === $data['status'] && class_exists( 'Pinterest_For_Woocommerce' ) ) {
			$pinterest_onboarding_completed = Pinterest_For_Woocommerce()::is_setup_complete();
			if ( $pinterest_onboarding_completed ) {
				$data['status']      = 'configured';
				$data['settingsUrl'] = admin_url( 'admin.php?page=wc-admin&path=/pinterest/settings' );
			} else {
				$data['settingsUrl'] = admin_url( 'admin.php?page=wc-admin&path=/pinterest/landing' );
			}
		}

		return $data;
	}

	/**
	 * Get Google extension data.
	 *
	 * @return array|bool
	 */
	protected static function get_google_extension_data() {
		$slug = 'google-listings-and-ads';

		if ( ! PluginsHelper::is_plugin_installed( $slug ) ) {
			return false;
		}

		$data         = self::get_extension_base_data( $slug );
		$data['icon'] = WC_ADMIN_IMAGES_FOLDER_URL . '/marketing/google.svg';

		if ( 'activated' === $data['status'] && function_exists( 'woogle_get_container' ) && class_exists( '\Automattic\WooCommerce\GoogleListingsAndAds\MerchantCenter\MerchantCenterService' ) ) {

			$merchant_center = woogle_get_container()->get( \Automattic\WooCommerce\GoogleListingsAndAds\MerchantCenter\MerchantCenterService::class );

			if ( $merchant_center->is_setup_complete() ) {
				$data['status']      = 'configured';
				$data['settingsUrl'] = admin_url( 'admin.php?page=wc-admin&path=/google/settings' );
			} else {
				$data['settingsUrl'] = admin_url( 'admin.php?page=wc-admin&path=/google/start' );
			}

			$data['docsUrl'] = 'https://woocommerce.com/document/google-listings-and-ads/?utm_medium=product';
		}

		return $data;
	}

	/**
	 * Get Amazon / Ebay extension data.
	 *
	 * @return array|bool
	 */
	protected static function get_amazon_ebay_extension_data() {
		$slug = 'woocommerce-amazon-ebay-integration';

		if ( ! PluginsHelper::is_plugin_installed( $slug ) ) {
			return false;
		}

		$data         = self::get_extension_base_data( $slug );
		$data['icon'] = WC_ADMIN_IMAGES_FOLDER_URL . '/marketing/amazon-ebay.svg';

		if ( 'activated' === $data['status'] && class_exists( '\CodistoConnect' ) ) {

			$codisto_merchantid = get_option( 'codisto_merchantid' );

			// Use same check as codisto admin tabs.
			if ( is_numeric( $codisto_merchantid ) ) {
				$data['status'] = 'configured';
			}

			$data['settingsUrl'] = admin_url( 'admin.php?page=codisto-settings' );
			$data['docsUrl']     = 'https://woocommerce.com/document/multichannel-for-woocommerce-google-amazon-ebay-walmart-integration/?utm_medium=product';
		}

		return $data;
	}

	/**
	 * Get MailPoet extension data.
	 *
	 * @return array|bool
	 */
	protected static function get_mailpoet_extension_data() {
		$slug = 'mailpoet';

		if ( ! PluginsHelper::is_plugin_installed( $slug ) ) {
			return false;
		}

		$data         = self::get_extension_base_data( $slug );
		$data['icon'] = WC_ADMIN_IMAGES_FOLDER_URL . '/marketing/mailpoet.svg';

		if ( 'activated' === $data['status'] && class_exists( '\MailPoet\API\API' ) ) {
			$mailpoet_api = \MailPoet\API\API::MP( 'v1' );

			if ( ! method_exists( $mailpoet_api, 'isSetupComplete' ) || $mailpoet_api->isSetupComplete() ) {
				$data['status']      = 'configured';
				$data['settingsUrl'] = admin_url( 'admin.php?page=mailpoet-settings' );
			} else {
				$data['settingsUrl'] = admin_url( 'admin.php?page=mailpoet-newsletters' );
			}

			$data['docsUrl']    = 'https://kb.mailpoet.com/';
			$data['supportUrl'] = 'https://www.mailpoet.com/support/';
		}

		return $data;
	}

	/**
	 * Get Creative Mail for WooCommerce extension data.
	 *
	 * @return array|bool
	 */
	protected static function get_creative_mail_extension_data() {
		$slug = 'creative-mail-by-constant-contact';

		if ( ! PluginsHelper::is_plugin_installed( $slug ) ) {
			return false;
		}

		$data         = self::get_extension_base_data( $slug );
		$data['icon'] = WC_ADMIN_IMAGES_FOLDER_URL . '/marketing/creative-mail-by-constant-contact.png';

		if ( 'activated' === $data['status'] && class_exists( '\CreativeMail\Helpers\OptionsHelper' ) ) {
			if ( ! method_exists( '\CreativeMail\Helpers\OptionsHelper', 'get_instance_id' ) || \CreativeMail\Helpers\OptionsHelper::get_instance_id() !== null ) {
				$data['status']      = 'configured';
				$data['settingsUrl'] = admin_url( 'admin.php?page=creativemail_settings' );
			} else {
				$data['settingsUrl'] = admin_url( 'admin.php?page=creativemail' );
			}

			$data['docsUrl']    = 'https://app.creativemail.com/kb/help/WooCommerce';
			$data['supportUrl'] = 'https://app.creativemail.com/kb/help/';
		}

		return $data;
	}

	/**
	 * Get TikTok for WooCommerce extension data.
	 *
	 * @return array|bool
	 */
	protected static function get_tiktok_extension_data() {
		$slug = 'tiktok-for-business';

		if ( ! PluginsHelper::is_plugin_installed( $slug ) ) {
			return false;
		}

		$data         = self::get_extension_base_data( $slug );
		$data['icon'] = WC_ADMIN_IMAGES_FOLDER_URL . '/marketing/tiktok.jpg';

		if ( 'activated' === $data['status'] ) {
			if ( false !== get_option( 'tt4b_access_token' ) ) {
				$data['status'] = 'configured';
			}

			$data['settingsUrl'] = admin_url( 'admin.php?page=tiktok' );
			$data['docsUrl']     = 'https://woocommerce.com/document/tiktok-for-woocommerce/';
			$data['supportUrl']  = 'https://ads.tiktok.com/athena/user-feedback/?identify_key=6a1e079024806640c5e1e695d13db80949525168a052299b4970f9c99cb5ac78';
		}

		return $data;
	}

	/**
	 * Get Jetpack CRM for WooCommerce extension data.
	 *
	 * @return array|bool
	 */
	protected static function get_jetpack_crm_extension_data() {
		$slug = 'zero-bs-crm';

		if ( ! PluginsHelper::is_plugin_installed( $slug ) ) {
			return false;
		}

		$data         = self::get_extension_base_data( $slug );
		$data['icon'] = WC_ADMIN_IMAGES_FOLDER_URL . '/marketing/jetpack-crm.png';

		if ( 'activated' === $data['status'] ) {
			$data['status']      = 'configured';
			$data['settingsUrl'] = admin_url( 'admin.php?page=zerobscrm-plugin-settings' );
			$data['docsUrl']     = 'https://kb.jetpackcrm.com/';
			$data['supportUrl']  = 'https://kb.jetpackcrm.com/crm-support/';
		}

		return $data;
	}

	/**
	 * Get WooCommerce Zapier extension data.
	 *
	 * @return array|bool
	 */
	protected static function get_zapier_extension_data() {
		$slug = 'woocommerce-zapier';

		if ( ! PluginsHelper::is_plugin_installed( $slug ) ) {
			return false;
		}

		$data         = self::get_extension_base_data( $slug );
		$data['icon'] = WC_ADMIN_IMAGES_FOLDER_URL . '/marketing/zapier.png';

		if ( 'activated' === $data['status'] ) {
			$data['status']      = 'configured';
			$data['settingsUrl'] = admin_url( 'admin.php?page=wc-settings&tab=wc_zapier' );
			$data['docsUrl']     = 'https://docs.om4.io/woocommerce-zapier/';
		}

		return $data;
	}

	/**
	 * Get Salesforce extension data.
	 *
	 * @return array|bool
	 */
	protected static function get_salesforce_extension_data() {
		$slug = 'integration-with-salesforce';

		if ( ! PluginsHelper::is_plugin_installed( $slug ) ) {
			return false;
		}

		$data         = self::get_extension_base_data( $slug );
		$data['icon'] = WC_ADMIN_IMAGES_FOLDER_URL . '/marketing/salesforce.jpg';

		if ( 'activated' === $data['status'] && class_exists( '\Integration_With_Salesforce_Admin' ) ) {
			if ( ! method_exists( '\Integration_With_Salesforce_Admin', 'get_connection_status' ) || \Integration_With_Salesforce_Admin::get_connection_status() ) {
				$data['status'] = 'configured';
			}

			$data['settingsUrl'] = admin_url( 'admin.php?page=integration-with-salesforce' );
			$data['docsUrl']     = 'https://woocommerce.com/document/salesforce-integration/';
			$data['supportUrl']  = 'https://wpswings.com/submit-query/';
		}

		return $data;
	}

	/**
	 * Get Vimeo extension data.
	 *
	 * @return array|bool
	 */
	protected static function get_vimeo_extension_data() {
		$slug = 'vimeo';

		if ( ! PluginsHelper::is_plugin_installed( $slug ) ) {
			return false;
		}

		$data         = self::get_extension_base_data( $slug );
		$data['icon'] = WC_ADMIN_IMAGES_FOLDER_URL . '/marketing/vimeo.png';

		if ( 'activated' === $data['status'] && class_exists( '\Tribe\Vimeo_WP\Vimeo\Vimeo_Auth' ) ) {
			if ( method_exists( '\Tribe\Vimeo_WP\Vimeo\Vimeo_Auth', 'has_access_token' ) ) {
				$vimeo_auth = new \Tribe\Vimeo_WP\Vimeo\Vimeo_Auth();
				if ( $vimeo_auth->has_access_token() ) {
					$data['status'] = 'configured';
				}
			} else {
				$data['status'] = 'configured';
			}

			$data['settingsUrl'] = admin_url( 'options-general.php?page=vimeo_settings' );
			$data['docsUrl']     = 'https://woocommerce.com/document/vimeo/';
			$data['supportUrl']  = 'https://vimeo.com/help/contact';
		}

		return $data;
	}

	/**
	 * Get Trustpilot extension data.
	 *
	 * @return array|bool
	 */
	protected static function get_trustpilot_extension_data() {
		$slug = 'trustpilot-reviews';

		if ( ! PluginsHelper::is_plugin_installed( $slug ) ) {
			return false;
		}

		$data         = self::get_extension_base_data( $slug );
		$data['icon'] = WC_ADMIN_IMAGES_FOLDER_URL . '/marketing/trustpilot.png';

		if ( 'activated' === $data['status'] ) {
			$data['status']      = 'configured';
			$data['settingsUrl'] = admin_url( 'admin.php?page=woocommerce-trustpilot-settings-page' );
			$data['docsUrl']     = 'https://woocommerce.com/document/trustpilot-reviews/';
			$data['supportUrl']  = 'https://support.trustpilot.com/hc/en-us/requests/new';
		}

		return $data;
	}


	/**
	 * Get an array of basic data for a given extension.
	 *
	 * @param string $slug Plugin slug.
	 *
	 * @return array|false
	 */
	protected static function get_extension_base_data( $slug ) {
		$status      = PluginsHelper::is_plugin_active( $slug ) ? 'activated' : 'installed';
		$plugin_data = PluginsHelper::get_plugin_data( $slug );

		if ( ! $plugin_data ) {
			return false;
		}

		return [
			'slug'        => $slug,
			'status'      => $status,
			'name'        => $plugin_data['Name'],
			'description' => html_entity_decode( wp_trim_words( $plugin_data['Description'], 20 ) ),
			'supportUrl'  => 'https://woocommerce.com/my-account/create-a-ticket/?utm_medium=product',
		];
	}

}
