<?php
// phpcs:disable Yoast.NamingConventions.NamespaceName.TooLong -- Needed in the folder structure.
namespace Yoast\WP\SEO\Dashboard\Infrastructure\Tracking;

use Yoast\WP\SEO\Helpers\Options_Helper;

/**
 * Stores and retrieves data about Site Kit usage.
 *
 * @phpcs:disable Yoast.NamingConventions.ObjectNameDepth.MaxExceeded
 */
class Setup_Steps_Tracking_Repository implements Setup_Steps_Tracking_Repository_Interface {

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
	 * Sets an option inside the Site Kit usage options array.
	 *
	 * @param string $element_name  The name of the option to set.
	 * @param string $element_value The value of the option to set.
	 *
	 * @return bool False when the update failed, true when the update succeeded.
	 */
	public function set_setup_steps_tracking_element( string $element_name, string $element_value ): bool {
		return $this->options_helper->set( 'site_kit_tracking_' . $element_name, $element_value );
	}

	/**
	 * Gets an option inside the Site Kit usage options array.
	 *
	 * @param string $element_name The name of the option to get.
	 *
	 * @return string The value if present, empty string if not.
	 */
	public function get_setup_steps_tracking_element( string $element_name ): string {
		return $this->options_helper->get( 'site_kit_tracking_' . $element_name, '' );
	}
}
