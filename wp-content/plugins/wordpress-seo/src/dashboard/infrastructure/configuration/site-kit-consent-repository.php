<?php
// phpcs:disable Yoast.NamingConventions.NamespaceName.TooLong -- Needed in the folder structure.
namespace Yoast\WP\SEO\Dashboard\Infrastructure\Configuration;

use Yoast\WP\SEO\Helpers\Options_Helper;

/**
 * Stores and retrieves the Site Kit consent status.
 *
 * @phpcs:disable Yoast.NamingConventions.ObjectNameDepth.MaxExceeded
 */
class Site_Kit_Consent_Repository implements Site_Kit_Consent_Repository_Interface {

	/**
	 * Holds the Options_Helper instance.
	 *
	 * @var Options_Helper
	 */
	private $options_helper;

	/**
	 * Constructs the class.
	 *
	 * @param Options_Helper $options_helper The options helper.
	 */
	public function __construct( Options_Helper $options_helper ) {
		$this->options_helper = $options_helper;
	}

	/**
	 * Sets the Site Kit consent value.
	 *
	 * @param bool $consent The consent status.
	 *
	 * @return bool False when the update failed, true when the update succeeded.
	 */
	public function set_site_kit_consent( bool $consent ): bool {
		return $this->options_helper->set( 'site_kit_connected', $consent );
	}

	/**
	 * Checks if consent has ben given for Site Kit.
	 * *
	 *
	 * @return bool True when consent has been given, false when it is not.
	 */
	public function is_consent_granted(): bool {
		return $this->options_helper->get( 'site_kit_connected', false );
	}
}
