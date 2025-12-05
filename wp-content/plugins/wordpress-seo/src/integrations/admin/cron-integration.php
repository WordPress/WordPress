<?php

namespace Yoast\WP\SEO\Integrations\Admin;

use Yoast\WP\SEO\Conditionals\Admin_Conditional;
use Yoast\WP\SEO\Helpers\Date_Helper;
use Yoast\WP\SEO\Integrations\Integration_Interface;

/**
 * Cron_Integration class.
 */
class Cron_Integration implements Integration_Interface {

	/**
	 * The indexing notification integration.
	 *
	 * @var Date_Helper
	 */
	protected $date_helper;

	/**
	 * {@inheritDoc}
	 */
	public static function get_conditionals() {
		return [ Admin_Conditional::class ];
	}

	/**
	 * Cron_Integration constructor
	 *
	 * @param Date_Helper $date_helper The date helper.
	 */
	public function __construct( Date_Helper $date_helper ) {
		$this->date_helper = $date_helper;
	}

	/**
	 * {@inheritDoc}
	 */
	public function register_hooks() {
		if ( ! \wp_next_scheduled( Indexing_Notification_Integration::NOTIFICATION_ID ) ) {
			\wp_schedule_event(
				$this->date_helper->current_time(),
				'daily',
				Indexing_Notification_Integration::NOTIFICATION_ID
			);
		}
	}
}
