<?php
namespace Elementor\Core\Common\Modules\EventTracker;

use Elementor\Core\Base\Base_Object;
use Elementor\Core\Common\Modules\EventTracker\DB as Events_DB_Manager;
use Elementor\Plugin;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class Personal_Data extends Base_Object {

	const WP_KEY = 'elementor-event-tracker';

	/**
	 * Get Title
	 *
	 * @since 3.6.0
	 *
	 * @return string
	 */
	private function get_title() {
		return esc_html__( 'Elementor Event Tracker', 'elementor' );
	}

	/**
	 * Erase all the submissions related to specific email.
	 *
	 * Since event data is saved globally per site and not per user, we remove all saved events from the DB upon a
	 * user's data deletion request.
	 *
	 * @return array
	 */
	private function erase_data() {
		// Get number of events saved in the DB.
		/** @var Events_DB_Manager $event_tracker_db_manager */
		$event_tracker_db_manager = Plugin::$instance->common
			->get_component( 'event-tracker' )
			->get_component( 'events-db' );

		$events = $event_tracker_db_manager->get_event_ids_from_db();
		$events_count = count( $events );

		DB::reset_table();

		// Validate table deleted
		$updated_events = $event_tracker_db_manager->get_event_ids_from_db();
		$updated_events_count = count( $updated_events );

		return [
			'items_removed' => $events_count - $updated_events_count,
			'items_retained' => 0,
			'messages' => [],
			'done' => 0 === $updated_events_count,
		];
	}

	/**
	 * Add eraser to the list of erasers.
	 *
	 * @param $erasers
	 *
	 * @return array[]
	 */
	private function add_eraser( $erasers ) {
		return $erasers + [
			self::WP_KEY => [
				'eraser_friendly_name' => $this->get_title(),
				'callback' => function () {
					return $this->erase_data();
				},
			],
		];
	}

	/**
	 * Personal_Data constructor.
	 */
	public function __construct() {
		add_filter( 'wp_privacy_personal_data_erasers', function ( $exporters ) {
			return $this->add_eraser( $exporters );
		} );
	}
}
