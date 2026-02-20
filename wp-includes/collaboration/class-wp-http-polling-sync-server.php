<?php
/**
 * WP_HTTP_Polling_Sync_Server class
 *
 * @package WordPress
 */

/**
 * Core class that contains an HTTP server used for collaborative editing.
 *
 * @since 7.0.0
 * @access private
 */
class WP_HTTP_Polling_Sync_Server {
	/**
	 * REST API namespace.
	 *
	 * @since 7.0.0
	 * @var string
	 */
	const REST_NAMESPACE = 'wp-sync/v1';

	/**
	 * Awareness timeout in seconds. Clients that haven't updated
	 * their awareness state within this time are considered disconnected.
	 *
	 * @since 7.0.0
	 * @var int
	 */
	const AWARENESS_TIMEOUT = 30;

	/**
	 * Threshold used to signal clients to send a compaction update.
	 *
	 * @since 7.0.0
	 * @var int
	 */
	const COMPACTION_THRESHOLD = 50;

	/**
	 * Sync update type: compaction.
	 *
	 * @since 7.0.0
	 * @var string
	 */
	const UPDATE_TYPE_COMPACTION = 'compaction';

	/**
	 * Sync update type: sync step 1.
	 *
	 * @since 7.0.0
	 * @var string
	 */
	const UPDATE_TYPE_SYNC_STEP1 = 'sync_step1';

	/**
	 * Sync update type: sync step 2.
	 *
	 * @since 7.0.0
	 * @var string
	 */
	const UPDATE_TYPE_SYNC_STEP2 = 'sync_step2';

	/**
	 * Sync update type: regular update.
	 *
	 * @since 7.0.0
	 * @var string
	 */
	const UPDATE_TYPE_UPDATE = 'update';

	/**
	 * Storage backend for sync updates.
	 *
	 * @since 7.0.0
	 */
	private WP_Sync_Storage $storage;

	/**
	 * Constructor.
	 *
	 * @since 7.0.0
	 *
	 * @param WP_Sync_Storage $storage Storage backend for sync updates.
	 */
	public function __construct( WP_Sync_Storage $storage ) {
		$this->storage = $storage;
	}

	/**
	 * Registers REST API routes.
	 *
	 * @since 7.0.0
	 */
	public function register_routes(): void {
		$typed_update_args = array(
			'properties' => array(
				'data' => array(
					'type'     => 'string',
					'required' => true,
				),
				'type' => array(
					'type'     => 'string',
					'required' => true,
					'enum'     => array(
						self::UPDATE_TYPE_COMPACTION,
						self::UPDATE_TYPE_SYNC_STEP1,
						self::UPDATE_TYPE_SYNC_STEP2,
						self::UPDATE_TYPE_UPDATE,
					),
				),
			),
			'required'   => true,
			'type'       => 'object',
		);

		$room_args = array(
			'after'     => array(
				'minimum'  => 0,
				'required' => true,
				'type'     => 'integer',
			),
			'awareness' => array(
				'required' => true,
				'type'     => 'object',
			),
			'client_id' => array(
				'minimum'  => 1,
				'required' => true,
				'type'     => 'integer',
			),
			'room'      => array(
				'required' => true,
				'type'     => 'string',
				'pattern'  => '^[^/]+/[^/:]+(?::\\S+)?$',
			),
			'updates'   => array(
				'items'    => $typed_update_args,
				'minItems' => 0,
				'required' => true,
				'type'     => 'array',
			),
		);

		register_rest_route(
			self::REST_NAMESPACE,
			'/updates',
			array(
				'methods'             => array( WP_REST_Server::CREATABLE ),
				'callback'            => array( $this, 'handle_request' ),
				'permission_callback' => array( $this, 'check_permissions' ),
				'args'                => array(
					'rooms' => array(
						'items'    => array(
							'properties' => $room_args,
							'type'       => 'object',
						),
						'required' => true,
						'type'     => 'array',
					),
				),
			)
		);
	}

	/**
	 * Checks if the current user has permission to access a room.
	 *
	 * @since 7.0.0
	 *
	 * @param WP_REST_Request $request The REST request.
	 * @return bool|WP_Error True if user has permission, otherwise WP_Error with details.
	 */
	public function check_permissions( WP_REST_Request $request ) {
		// Minimum cap check. Is user logged in with a contributor role or higher?
		if ( ! current_user_can( 'edit_posts' ) ) {
			return new WP_Error(
				'rest_cannot_edit',
				__( 'You do not have permission to perform this action' ),
				array( 'status' => rest_authorization_required_code() )
			);
		}

		$rooms = $request['rooms'];

		foreach ( $rooms as $room ) {
			$room         = $room['room'];
			$type_parts   = explode( '/', $room, 2 );
			$object_parts = explode( ':', $type_parts[1] ?? '', 2 );

			$entity_kind = $type_parts[0];
			$entity_name = $object_parts[0];
			$object_id   = $object_parts[1] ?? null;

			if ( ! $this->can_user_sync_entity_type( $entity_kind, $entity_name, $object_id ) ) {
				return new WP_Error(
					'rest_cannot_edit',
					sprintf(
						/* translators: %s: The room name encodes the current entity being synced. */
						__( 'You do not have permission to sync this entity: %s.' ),
						$room
					),
					array( 'status' => rest_authorization_required_code() )
				);
			}
		}

		return true;
	}

	/**
	 * Handles request: stores sync updates and awareness data, and returns
	 * updates the client is missing.
	 *
	 * @since 7.0.0
	 *
	 * @param WP_REST_Request $request The REST request.
	 * @return WP_REST_Response|WP_Error Response object or error.
	 */
	public function handle_request( WP_REST_Request $request ) {
		$rooms    = $request['rooms'];
		$response = array(
			'rooms' => array(),
		);

		foreach ( $rooms as $room_request ) {
			$awareness = $room_request['awareness'];
			$client_id = $room_request['client_id'];
			$cursor    = $room_request['after'];
			$room      = $room_request['room'];

			// Merge awareness state.
			$merged_awareness = $this->process_awareness_update( $room, $client_id, $awareness );

			// The lowest client ID is nominated to perform compaction when needed.
			$is_compactor = false;
			if ( count( $merged_awareness ) > 0 ) {
				$is_compactor = min( array_keys( $merged_awareness ) ) === $client_id;
			}

			// Process each update according to its type.
			foreach ( $room_request['updates'] as $update ) {
				$result = $this->process_sync_update( $room, $client_id, $cursor, $update );
				if ( is_wp_error( $result ) ) {
					return $result;
				}
			}

			// Get updates for this client.
			$room_response              = $this->get_updates( $room, $client_id, $cursor, $is_compactor );
			$room_response['awareness'] = $merged_awareness;

			$response['rooms'][] = $room_response;
		}

		return new WP_REST_Response( $response, 200 );
	}

	/**
	 * Checks if the current user can sync a specific entity type.
	 *
	 * @since 7.0.0
	 *
	 * @param string      $entity_kind The entity kind, e.g. 'postType', 'taxonomy', 'root'.
	 * @param string      $entity_name The entity name, e.g. 'post', 'category', 'site'.
	 * @param string|null $object_id   The object ID / entity key for single entities, null for collections.
	 * @return bool True if user has permission, otherwise false.
	 */
	private function can_user_sync_entity_type( string $entity_kind, string $entity_name, ?string $object_id ): bool {
		// Handle single post type entities with a defined object ID.
		if ( 'postType' === $entity_kind && is_numeric( $object_id ) ) {
			return current_user_can( 'edit_post', (int) $object_id );
		}

		// Handle single taxonomy term entities with a defined object ID.
		if ( 'taxonomy' === $entity_kind && is_numeric( $object_id ) ) {
			$taxonomy = get_taxonomy( $entity_name );
			return isset( $taxonomy->cap->assign_terms ) && current_user_can( $taxonomy->cap->assign_terms );
		}

		// Handle single comment entities with a defined object ID.
		if ( 'root' === $entity_kind && 'comment' === $entity_name && is_numeric( $object_id ) ) {
			return current_user_can( 'edit_comment', (int) $object_id );
		}

		// All the remaining checks are for collections. If an object ID is provided,
		// reject the request.
		if ( null !== $object_id ) {
			return false;
		}

		// For postType collections, check if the user can edit posts of this type.
		if ( 'postType' === $entity_kind ) {
			$post_type_object = get_post_type_object( $entity_name );
			if ( ! isset( $post_type_object->cap->edit_posts ) ) {
				return false;
			}

			return current_user_can( $post_type_object->cap->edit_posts );
		}

		// Collection syncing does not exchange entity data. It only signals if
		// another user has updated an entity in the collection. Therefore, we only
		// compare against an allow list of collection types.
		$allowed_collection_entity_kinds = array(
			'postType',
			'root',
			'taxonomy',
		);

		return in_array( $entity_kind, $allowed_collection_entity_kinds, true );
	}

	/**
	 * Processes and stores an awareness update from a client.
	 *
	 * @since 7.0.0
	 *
	 * @param string                    $room             Room identifier.
	 * @param int                       $client_id        Client identifier.
	 * @param array<string, mixed>|null $awareness_update Awareness state sent by the client.
	 * @return array<int, array<string, mixed>> Map of client ID to awareness state.
	 */
	private function process_awareness_update( string $room, int $client_id, ?array $awareness_update ): array {
		$existing_awareness = $this->storage->get_awareness_state( $room );
		$updated_awareness  = array();
		$current_time       = time();

		foreach ( $existing_awareness as $entry ) {
			// Remove this client's entry (it will be updated below).
			if ( $client_id === $entry['client_id'] ) {
				continue;
			}

			// Remove entries that have expired.
			if ( $current_time - $entry['updated_at'] >= self::AWARENESS_TIMEOUT ) {
				continue;
			}

			$updated_awareness[] = $entry;
		}

		// Add this client's awareness state.
		if ( null !== $awareness_update ) {
			$updated_awareness[] = array(
				'client_id'  => $client_id,
				'state'      => $awareness_update,
				'updated_at' => $current_time,
			);
		}

		// This action can fail, but it shouldn't fail the entire request.
		$this->storage->set_awareness_state( $room, $updated_awareness );

		// Convert to client_id => state map for response.
		$response = array();
		foreach ( $updated_awareness as $entry ) {
			$response[ $entry['client_id'] ] = $entry['state'];
		}

		return $response;
	}

	/**
	 * Processes a sync update based on its type.
	 *
	 * @since 7.0.0
	 *
	 * @param string                            $room      Room identifier.
	 * @param int                               $client_id Client identifier.
	 * @param int                               $cursor    Client cursor (marker of last seen update).
	 * @param array{data: string, type: string} $update    Sync update.
	 * @return true|WP_Error True on success, WP_Error on storage failure.
	 */
	private function process_sync_update( string $room, int $client_id, int $cursor, array $update ) {
		$data = $update['data'];
		$type = $update['type'];

		switch ( $type ) {
			case self::UPDATE_TYPE_COMPACTION:
				/*
				 * Compaction replaces updates the client has already seen. Only remove
				 * updates with markers before the client's cursor to preserve updates
				 * that arrived since the client's last sync.
				 *
				 * Check for a newer compaction update first. If one exists, skip this
				 * compaction to avoid overwriting it.
				 */
				$updates_after_cursor = $this->storage->get_updates_after_cursor( $room, $cursor );
				$has_newer_compaction = false;

				foreach ( $updates_after_cursor as $existing ) {
					if ( self::UPDATE_TYPE_COMPACTION === $existing['type'] ) {
						$has_newer_compaction = true;
						break;
					}
				}

				if ( ! $has_newer_compaction ) {
					if ( ! $this->storage->remove_updates_before_cursor( $room, $cursor ) ) {
						return new WP_Error(
							'rest_sync_storage_error',
							__( 'Failed to remove updates during compaction.' ),
							array( 'status' => 500 )
						);
					}

					return $this->add_update( $room, $client_id, $type, $data );
				}
				break;

			case self::UPDATE_TYPE_SYNC_STEP1:
			case self::UPDATE_TYPE_SYNC_STEP2:
			case self::UPDATE_TYPE_UPDATE:
				/*
				 * Sync step 1 announces a client's state vector. Other clients need
				 * to see it so they can respond with sync_step2 containing missing
				 * updates. The cursor-based filtering prevents re-delivery.
				 *
				 * Sync step 2 contains updates for a specific client.
				 *
				 * All updates are stored persistently.
				 */
				return $this->add_update( $room, $client_id, $type, $data );
		}

		return new WP_Error(
			'rest_invalid_update_type',
			__( 'Invalid sync update type.' ),
			array( 'status' => 400 )
		);
	}

	/**
	 * Adds an update to a room's update list via storage.
	 *
	 * @since 7.0.0
	 *
	 * @param string $room      Room identifier.
	 * @param int    $client_id Client identifier.
	 * @param string $type      Update type (sync_step1, sync_step2, update, compaction).
	 * @param string $data      Base64-encoded update data.
	 * @return true|WP_Error True on success, WP_Error on storage failure.
	 */
	private function add_update( string $room, int $client_id, string $type, string $data ) {
		$update = array(
			'client_id' => $client_id,
			'data'      => $data,
			'type'      => $type,
		);

		if ( ! $this->storage->add_update( $room, $update ) ) {
			return new WP_Error(
				'rest_sync_storage_error',
				__( 'Failed to store sync update.' ),
				array( 'status' => 500 )
			);
		}

		return true;
	}

	/**
	 * Gets sync updates for a specific client from a room after a given cursor.
	 *
	 * Delegates cursor-based retrieval to the storage layer, then applies
	 * client-specific filtering and compaction logic.
	 *
	 * @since 7.0.0
	 *
	 * @param string $room         Room identifier.
	 * @param int    $client_id    Client identifier.
	 * @param int    $cursor       Return updates after this cursor.
	 * @param bool   $is_compactor True if this client is nominated to perform compaction.
	 * @return array{
	 *   end_cursor: int,
	 *   should_compact: bool,
	 *   room: string,
	 *   total_updates: int,
	 *   updates: array<int, array{data: string, type: string}>,
	 * } Response data for this room.
	 */
	private function get_updates( string $room, int $client_id, int $cursor, bool $is_compactor ): array {
		$updates_after_cursor = $this->storage->get_updates_after_cursor( $room, $cursor );
		$total_updates        = $this->storage->get_update_count( $room );

		// Filter out this client's updates, except compaction updates.
		$typed_updates = array();
		foreach ( $updates_after_cursor as $update ) {
			if ( $client_id === $update['client_id'] && self::UPDATE_TYPE_COMPACTION !== $update['type'] ) {
				continue;
			}

			$typed_updates[] = array(
				'data' => $update['data'],
				'type' => $update['type'],
			);
		}

		$should_compact = $is_compactor && $total_updates > self::COMPACTION_THRESHOLD;

		return array(
			'end_cursor'     => $this->storage->get_cursor( $room ),
			'room'           => $room,
			'should_compact' => $should_compact,
			'total_updates'  => $total_updates,
			'updates'        => $typed_updates,
		);
	}
}
