<?php
// @phpcs:disable Yoast.NamingConventions.NamespaceName.TooLong -- This namespace should reflect the namespace of the original class.
namespace Yoast\WP\SEO\Editors\Framework\Integrations;

use Yoast\WP\SEO\Conditionals\WooCommerce_Conditional;
use Yoast\WP\SEO\Editors\Domain\Integrations\Integration_Data_Provider_Interface;

/**
 * Describes if the Woocommerce plugin is enabled.
 */
class WooCommerce implements Integration_Data_Provider_Interface {

	/**
	 * The WooCommerce conditional.
	 *
	 * @var WooCommerce_Conditional
	 */
	private $woocommerce_conditional;

	/**
	 * The constructor.
	 *
	 * @param WooCommerce_Conditional $woocommerce_conditional The WooCommerce conditional.
	 */
	public function __construct( WooCommerce_Conditional $woocommerce_conditional ) {
		$this->woocommerce_conditional = $woocommerce_conditional;
	}

	/**
	 * If the plugin is activated.
	 *
	 * @return bool If the plugin is activated.
	 */
	public function is_enabled(): bool {
		return $this->woocommerce_conditional->is_met();
	}

	/**
	 * Return this object represented by a key value array.
	 *
	 * @return array<string, bool> Returns the name and if the feature is enabled.
	 */
	public function to_array(): array {
		return [ 'isWooCommerceActive' => $this->is_enabled() ];
	}

	/**
	 * Returns this object represented by a key value structure that is compliant with the script data array.
	 *
	 * @return array<string, bool> Returns the legacy key and if the feature is enabled.
	 */
	public function to_legacy_array(): array {
		return [ 'isWooCommerceActive' => $this->is_enabled() ];
	}
}
