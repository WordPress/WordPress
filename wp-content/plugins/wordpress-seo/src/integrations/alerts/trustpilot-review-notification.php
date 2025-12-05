<?php

namespace Yoast\WP\SEO\Integrations\Alerts;

/**
 * Trustpilot_Review_Notification class.
 */
class Trustpilot_Review_Notification extends Abstract_Dismissable_Alert {

	/**
	 * Holds the alert identifier.
	 *
	 * @var string
	 */
	protected $alert_identifier = 'trustpilot-review-notification';
}
