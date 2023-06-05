<?php
/**
 * WCCOM Site Installer Error Codes Class
 *
 * @package WooCommerce\WCCom\API
 * @since   7.7.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * WCCOM Site Installer Error Codes Class
 *
 * Stores data for errors, returned by installer API.
 */
class WC_REST_WCCOM_Site_Installer_Error_Codes {

	const NOT_AUTHENTICATED                   = 'not_authenticated';
	const NO_ACCESS_TOKEN                     = 'no_access_token';
	const NO_SIGNATURE                        = 'no_signature';
	const SITE_NOT_CONNECTED                  = 'site_not_connnected';
	const INVALID_TOKEN                       = 'invalid_token';
	const REQUEST_VERIFICATION_FAILED         = 'request_verification_failed';
	const USER_NOT_FOUND                      = 'user_not_found';
	const NO_PERMISSION                       = 'forbidden';
	const IDEMPOTENCY_KEY_MISMATCH            = 'idempotency_key_mismatch';
	const NO_INITIATED_INSTALLATION_FOUND     = 'no_initiated_installation_found';
	const ALL_INSTALLATION_STEPS_RUN          = 'all_installation_steps_run';
	const REQUESTED_STEP_ALREADY_RUN          = 'requested_step_already_run';
	const PLUGIN_ALREADY_INSTALLED            = 'plugin_already_installed';
	const INSTALLATION_ALREADY_RUNNING        = 'installation_already_running';
	const INSTALLATION_FAILED                 = 'installation_failed';
	const FILESYSTEM_REQUIREMENTS_NOT_MET     = 'filesystem_requirements_not_met';
	const FAILED_GETTING_PRODUCT_INFO         = 'product_info_failed';
	const INVALID_PRODUCT_INFO_RESPONSE       = 'invalid_product_info_response';
	const WCCOM_PRODUCT_MISSING_SUBSCRIPTION  = 'wccom_product_missing_subscription';
	const WCCOM_PRODUCT_MISSING_PACKAGE       = 'wccom_product_missing_package';
	const WPORG_PRODUCT_MISSING_DOWNLOAD_LINK = 'wporg_product_missing_download_link';
	const MISSING_DOWNLOAD_PATH               = 'missing_download_path';
	const MISSING_UNPACKED_PATH               = 'missing_unpacked_path';
	const UNKNOWN_FILENAME                    = 'unknown_filename';
	const PLUGIN_ACTIVATION_ERROR             = 'plugin_activation_error';
	const UNEXPECTED_ERROR                    = 'unexpected_error';
	const FAILED_TO_RESET_INSTALLATION_STATE  = 'failed_to_reset_installation_state';

	const ERROR_MESSAGES = array(
		self::NOT_AUTHENTICATED                  => 'Authentication required',
		self::NO_ACCESS_TOKEN                    => 'No access token provided',
		self::NO_SIGNATURE                       => 'No signature provided',
		self::SITE_NOT_CONNECTED                 => 'Site not connected to WooCommerce.com',
		self::INVALID_TOKEN                      => 'Invalid access token provided',
		self::REQUEST_VERIFICATION_FAILED        => 'Request verification by signature failed',
		self::USER_NOT_FOUND                     => 'Token owning user not found',
		self::NO_PERMISSION                      => 'You do not have permission to install plugin or theme',
		self::IDEMPOTENCY_KEY_MISMATCH           => 'Idempotency key mismatch',
		self::NO_INITIATED_INSTALLATION_FOUND    => 'No initiated installation for the product found',
		self::ALL_INSTALLATION_STEPS_RUN         => 'All installation steps have been run',
		self::REQUESTED_STEP_ALREADY_RUN         => 'Requested step has already been run',
		self::PLUGIN_ALREADY_INSTALLED           => 'The plugin has already been installed',
		self::INSTALLATION_ALREADY_RUNNING       => 'The installation of the plugin is already running',
		self::INSTALLATION_FAILED                => 'The installation of the plugin failed',
		self::FILESYSTEM_REQUIREMENTS_NOT_MET    => 'The filesystem requirements are not met',
		self::FAILED_GETTING_PRODUCT_INFO        => 'Failed to retrieve product info from woocommerce.com',
		self::INVALID_PRODUCT_INFO_RESPONSE      => 'Invalid product info response from woocommerce.com',
		self::WCCOM_PRODUCT_MISSING_SUBSCRIPTION => 'Product subscription is missing',
		self::WCCOM_PRODUCT_MISSING_PACKAGE      => 'Could not find product package',
		self::MISSING_DOWNLOAD_PATH              => 'Download path is missing',
		self::MISSING_UNPACKED_PATH              => 'Unpacked path is missing',
		self::UNKNOWN_FILENAME                   => 'Unknown product filename',
		self::PLUGIN_ACTIVATION_ERROR            => 'Plugin activation error',
		self::UNEXPECTED_ERROR                   => 'Unexpected error',
		self::FAILED_TO_RESET_INSTALLATION_STATE => 'Failed to reset installation state',
	);

	const HTTP_CODES = array(
		self::NOT_AUTHENTICATED               => 401,
		self::NO_ACCESS_TOKEN                 => 400,
		self::NO_SIGNATURE                    => 400,
		self::SITE_NOT_CONNECTED              => 401,
		self::INVALID_TOKEN                   => 401,
		self::REQUEST_VERIFICATION_FAILED     => 400,
		self::USER_NOT_FOUND                  => 401,
		self::NO_PERMISSION                   => 403,
		self::IDEMPOTENCY_KEY_MISMATCH        => 400,
		self::NO_INITIATED_INSTALLATION_FOUND => 400,
		self::ALL_INSTALLATION_STEPS_RUN      => 400,
		self::REQUESTED_STEP_ALREADY_RUN      => 400,
		self::UNEXPECTED_ERROR                => 500,
	);
}
