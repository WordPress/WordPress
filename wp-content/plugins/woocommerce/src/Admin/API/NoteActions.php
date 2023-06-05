<?php
/**
 * REST API Admin Note Action controller
 *
 * Handles requests to the admin note action endpoint.
 */

namespace Automattic\WooCommerce\Admin\API;

defined( 'ABSPATH' ) || exit;

use Automattic\WooCommerce\Admin\Notes\Note;
use Automattic\WooCommerce\Admin\Notes\Notes as NotesFactory;

/**
 * REST API Admin Note Action controller class.
 *
 * @internal
 * @extends WC_REST_CRUD_Controller
 */
class NoteActions extends Notes {

	/**
	 * Register the routes for admin notes.
	 */
	public function register_routes() {
		register_rest_route(
			$this->namespace,
			'/' . $this->rest_base . '/(?P<note_id>[\d-]+)/action/(?P<action_id>[\d-]+)',
			array(
				'args'   => array(
					'note_id'   => array(
						'description' => __( 'Unique ID for the Note.', 'woocommerce' ),
						'type'        => 'integer',
					),
					'action_id' => array(
						'description' => __( 'Unique ID for the Note Action.', 'woocommerce' ),
						'type'        => 'integer',
					),
				),
				array(
					'methods'             => \WP_REST_Server::EDITABLE,
					'callback'            => array( $this, 'trigger_note_action' ),
					// @todo - double check these permissions for taking note actions.
					'permission_callback' => array( $this, 'get_item_permissions_check' ),
				),
				'schema' => array( $this, 'get_public_item_schema' ),
			)
		);
	}

	/**
	 * Trigger a note action.
	 *
	 * @param WP_REST_Request $request Full details about the request.
	 * @return WP_REST_Request|WP_Error
	 */
	public function trigger_note_action( $request ) {
		$note = NotesFactory::get_note( $request->get_param( 'note_id' ) );

		if ( ! $note ) {
			return new \WP_Error(
				'woocommerce_note_invalid_id',
				__( 'Sorry, there is no resource with that ID.', 'woocommerce' ),
				array( 'status' => 404 )
			);
		}

		$note->set_is_read( true );
		$note->save();

		$triggered_action = NotesFactory::get_action_by_id( $note, $request->get_param( 'action_id' ) );

		if ( ! $triggered_action ) {
			return new \WP_Error(
				'woocommerce_note_action_invalid_id',
				__( 'Sorry, there is no resource with that ID.', 'woocommerce' ),
				array( 'status' => 404 )
			);
		}

		$triggered_note = NotesFactory::trigger_note_action( $note, $triggered_action );

		$data = $triggered_note->get_data();
		$data = $this->prepare_item_for_response( $data, $request );
		$data = $this->prepare_response_for_collection( $data );

		return rest_ensure_response( $data );
	}
}
