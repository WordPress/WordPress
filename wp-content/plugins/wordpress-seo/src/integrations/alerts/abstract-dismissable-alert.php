<?php

namespace Yoast\WP\SEO\Integrations\Alerts;

use Yoast\WP\SEO\Integrations\Integration_Interface;

/**
 * Dismissable_Alert class.
 */
abstract class Abstract_Dismissable_Alert implements Integration_Interface {

	/**
	 * Holds the alert identifier.
	 *
	 * @var string
	 */
	protected $alert_identifier;

	/**
	 * {@inheritDoc}
	 */
	public static function get_conditionals() {
		return [];
	}

	/**
	 * {@inheritDoc}
	 */
	public function register_hooks() {
		\add_filter( 'wpseo_allowed_dismissable_alerts', [ $this, 'register_dismissable_alert' ] );
	}

	/**
	 * Registers the dismissable alert.
	 *
	 * @param string[] $allowed_dismissable_alerts The allowed dismissable alerts.
	 *
	 * @return string[] The allowed dismissable alerts.
	 */
	public function register_dismissable_alert( $allowed_dismissable_alerts ) {
		$allowed_dismissable_alerts[] = $this->alert_identifier;

		return $allowed_dismissable_alerts;
	}
}
