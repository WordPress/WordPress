<?php

namespace Yoast\WP\SEO\Integrations\Alerts;

/**
 * Registers a dismissible alert.
 *
 * @phpcs:disable Yoast.NamingConventions.ObjectNameDepth.MaxExceeded
 */
class Ai_Generator_Tip_Notification extends Abstract_Dismissable_Alert {

	/**
	 * Holds the alert identifier.
	 *
	 * @var string
	 */
	protected $alert_identifier = 'ai_generator_tip_notification';
}
