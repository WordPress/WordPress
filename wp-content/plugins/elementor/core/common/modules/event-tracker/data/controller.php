<?php
namespace Elementor\Core\Common\Modules\EventTracker\Data;

use Elementor\Core\Common\Modules\EventTracker\DB as Events_DB_Manager;
use Elementor\Plugin;
use WP_REST_Server;
use Elementor\Data\V2\Base\Controller as Controller_Base;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class Controller extends Controller_Base {

	public function get_name() {
		return 'send-event';
	}

	public function register_endpoints() {
		$this->index_endpoint->register_items_route( \WP_REST_Server::CREATABLE, [
			'event_data' => [
				'description' => 'All the recorded event data in JSON format',
				'type' => 'object',
				'required' => true,
			],
		] );
	}

	/**
	 * Get Permissions Callback
	 *
	 * This endpoint should only accept POST requests, and currently we only track site administrator actions.
	 *
	 * @since 3.6.0
	 *
	 * @param \WP_REST_Request $request
	 * @return bool
	 */
	public function get_permission_callback( $request ) {
		if ( WP_REST_Server::CREATABLE !== $request->get_method() ) {
			return false;
		}

		return current_user_can( 'manage_options' );
	}

	/**
	 * Create Items
	 *
	 * Receives a request for adding an event data entry into the database. If the request contains event data, this
	 * method initiates creation of a database entry with the event data in the Events DB table.
	 *
	 * @since 3.6.0
	 *
	 * @param \WP_REST_Request $request
	 * @return bool
	 */
	public function create_items( $request ) {
		$request_body = $request->get_json_params();

		if ( empty( $request_body['event_data'] ) ) {
			return false;
		}

		/** @var Events_DB_Manager $event_tracker_db_manager */
		$event_tracker_db_manager = Plugin::$instance->common
			->get_component( 'event-tracker' )
			->get_component( 'events-db' );

		$event_tracker_db_manager->create_entry( $request_body['event_data'] );

		return true;
	}
}
