<?php

namespace Automattic\WooCommerce\Admin\Features\ShippingPartnerSuggestions;

use Automattic\WooCommerce\Admin\DataSourcePoller;

/**
 * Specs data source poller class for shipping partner suggestions.
 */
class ShippingPartnerSuggestionsDataSourcePoller extends DataSourcePoller {

	/**
	 * Data Source Poller ID.
	 */
	const ID = 'shipping_partner_suggestions';

	/**
	 * Default data sources array.
	 */
	const DATA_SOURCES = array(
		'https://woocommerce.com/wp-json/wccom/shipping-partner-suggestions/1.0/suggestions.json',
	);

	/**
	 * Class instance.
	 *
	 * @var ShippingPartnerSuggestionsDataSourcePoller instance
	 */
	protected static $instance = null;

	/**
	 * Get class instance.
	 */
	public static function get_instance() {
		if ( ! self::$instance ) {
			self::$instance = new self( self::ID, self::DATA_SOURCES );
		}
		return self::$instance;
	}
}
