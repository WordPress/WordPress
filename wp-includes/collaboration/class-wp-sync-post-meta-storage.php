<?php
/**
 * WP_Sync_Post_Meta_Storage class
 *
 * @package WordPress
 */

/**
 * Core class that provides an interface for storing and retrieving sync
 * updates and awareness data during a collaborative session.
 *
 * Data is stored as post meta on a dedicated post per room of a custom post type.
 *
 * @since 7.0.0
 *
 * @access private
 */
class WP_Sync_Post_Meta_Storage implements WP_Sync_Storage {
	/**
	 * Post type for sync storage.
	 *
	 * @since 7.0.0
	 * @var string
	 */
	const POST_TYPE = 'wp_sync_storage';

	/**
	 * Meta key for awareness state.
	 *
	 * @since 7.0.0
	 * @var string
	 */
	const AWARENESS_META_KEY = 'wp_sync_awareness';

	/**
	 * Meta key for sync updates.
	 *
	 * @since 7.0.0
	 * @var string
	 */
	const SYNC_UPDATE_META_KEY = 'wp_sync_update';

	/**
	 * Cache of cursors by room.
	 *
	 * @since 7.0.0
	 * @var array<string, int>
	 */
	private array $room_cursors = array();

	/**
	 * Cache of update counts by room.
	 *
	 * @since 7.0.0
	 * @var array<string, int>
	 */
	private array $room_update_counts = array();

	/**
	 * Cache of storage post IDs by room hash.
	 *
	 * @since 7.0.0
	 * @var array<string, int>
	 */
	private static array $storage_post_ids = array();

	/**
	 * Adds a sync update to a given room.
	 *
	 * @since 7.0.0
	 *
	 * @param string $room   Room identifier.
	 * @param mixed  $update Sync update.
	 * @return bool True on success, false on failure.
	 */
	public function add_update( string $room, $update ): bool {
		$post_id = $this->get_storage_post_id( $room );
		if ( null === $post_id ) {
			return false;
		}

		// Create an envelope and stamp each update to enable cursor-based filtering.
		$envelope = array(
			'timestamp' => $this->get_time_marker(),
			'value'     => $update,
		);

		return (bool) add_post_meta( $post_id, self::SYNC_UPDATE_META_KEY, $envelope, false );
	}

	/**
	 * Retrieves all sync updates for a given room.
	 *
	 * @since 7.0.0
	 *
	 * @param string $room Room identifier.
	 * @return array<int, array{ timestamp: int, value: mixed }> Sync updates.
	 */
	private function get_all_updates( string $room ): array {
		$this->room_cursors[ $room ] = $this->get_time_marker() - 100; // Small buffer to ensure consistency.

		$post_id = $this->get_storage_post_id( $room );
		if ( null === $post_id ) {
			return array();
		}

		$updates = get_post_meta( $post_id, self::SYNC_UPDATE_META_KEY, false );

		if ( ! is_array( $updates ) ) {
			$updates = array();
		}

		// Filter out any updates that don't have the expected structure.
		$updates = array_filter(
			$updates,
			static function ( $update ): bool {
				return is_array( $update ) && isset( $update['timestamp'], $update['value'] ) && is_int( $update['timestamp'] );
			}
		);

		$this->room_update_counts[ $room ] = count( $updates );

		return $updates;
	}

	/**
	 * Gets awareness state for a given room.
	 *
	 * @since 7.0.0
	 *
	 * @param string $room Room identifier.
	 * @return array<int, mixed> Awareness state.
	 */
	public function get_awareness_state( string $room ): array {
		$post_id = $this->get_storage_post_id( $room );
		if ( null === $post_id ) {
			return array();
		}

		$awareness = get_post_meta( $post_id, self::AWARENESS_META_KEY, true );

		if ( ! is_array( $awareness ) ) {
			return array();
		}

		return array_values( $awareness );
	}

	/**
	 * Sets awareness state for a given room.
	 *
	 * @since 7.0.0
	 *
	 * @param string            $room      Room identifier.
	 * @param array<int, mixed> $awareness Serializable awareness state.
	 * @return bool True on success, false on failure.
	 */
	public function set_awareness_state( string $room, array $awareness ): bool {
		$post_id = $this->get_storage_post_id( $room );
		if ( null === $post_id ) {
			return false;
		}

		// update_post_meta returns false if the value is the same as the existing value.
		update_post_meta( $post_id, self::AWARENESS_META_KEY, $awareness );
		return true;
	}

	/**
	 * Gets the current cursor for a given room.
	 *
	 * The cursor is set during get_updates_after_cursor() and represents the
	 * point in time just before the updates were retrieved, with a small buffer
	 * to ensure consistency.
	 *
	 * @since 7.0.0
	 *
	 * @param string $room Room identifier.
	 * @return int Current cursor for the room.
	 */
	public function get_cursor( string $room ): int {
		return $this->room_cursors[ $room ] ?? 0;
	}

	/**
	 * Gets or creates the storage post for a given room.
	 *
	 * Each room gets its own dedicated post so that post meta cache
	 * invalidation is scoped to a single room rather than all of them.
	 *
	 * @since 7.0.0
	 *
	 * @param string $room Room identifier.
	 * @return int|null Post ID.
	 */
	private function get_storage_post_id( string $room ): ?int {
		$room_hash = md5( $room );

		if ( isset( self::$storage_post_ids[ $room_hash ] ) ) {
			return self::$storage_post_ids[ $room_hash ];
		}

		// Try to find an existing post for this room.
		$posts = get_posts(
			array(
				'post_type'      => self::POST_TYPE,
				'posts_per_page' => 1,
				'post_status'    => 'publish',
				'name'           => $room_hash,
				'fields'         => 'ids',
			)
		);

		$post_id = array_first( $posts );
		if ( is_int( $post_id ) ) {
			self::$storage_post_ids[ $room_hash ] = $post_id;
			return $post_id;
		}

		// Create new post for this room.
		$post_id = wp_insert_post(
			array(
				'post_type'   => self::POST_TYPE,
				'post_status' => 'publish',
				'post_title'  => 'Sync Storage',
				'post_name'   => $room_hash,
			)
		);

		if ( is_int( $post_id ) ) {
			self::$storage_post_ids[ $room_hash ] = $post_id;
			return $post_id;
		}

		return null;
	}

	/**
	 * Gets the current time in milliseconds as a comparable time marker.
	 *
	 * @since 7.0.0
	 *
	 * @return int Current time in milliseconds.
	 */
	private function get_time_marker(): int {
		return (int) floor( microtime( true ) * 1000 );
	}

	/**
	 * Gets the number of updates stored for a given room.
	 *
	 * @since 7.0.0
	 *
	 * @param string $room Room identifier.
	 * @return int Number of updates stored for the room.
	 */
	public function get_update_count( string $room ): int {
		return $this->room_update_counts[ $room ] ?? 0;
	}

	/**
	 * Retrieves sync updates from a room for a given client and cursor. Updates
	 * from the specified client should be excluded.
	 *
	 * @since 7.0.0
	 *
	 * @param string $room   Room identifier.
	 * @param int    $cursor Return updates after this cursor.
	 * @return array<int, mixed> Sync updates.
	 */
	public function get_updates_after_cursor( string $room, int $cursor ): array {
		$all_updates = $this->get_all_updates( $room );
		$updates     = array();

		foreach ( $all_updates as $update ) {
			if ( $update['timestamp'] > $cursor ) {
				$updates[] = $update;
			}
		}

		// Sort by timestamp to ensure order.
		usort(
			$updates,
			fn ( $a, $b ) => $a['timestamp'] <=> $b['timestamp']
		);

		return wp_list_pluck( $updates, 'value' );
	}

	/**
	 * Removes updates from a room that are older than the given cursor.
	 *
	 * @since 7.0.0
	 *
	 * @param string $room   Room identifier.
	 * @param int    $cursor Remove updates with markers < this cursor.
	 * @return bool True on success, false on failure.
	 */
	public function remove_updates_before_cursor( string $room, int $cursor ): bool {
		$post_id = $this->get_storage_post_id( $room );
		if ( null === $post_id ) {
			return false;
		}

		$all_updates = $this->get_all_updates( $room );

		// Remove all updates for the room and re-store only those that are newer than the cursor.
		if ( ! delete_post_meta( $post_id, self::SYNC_UPDATE_META_KEY ) ) {
			return false;
		}

		// Re-store envelopes directly to avoid double-wrapping by add_update().
		$add_result = true;
		foreach ( $all_updates as $envelope ) {
			if ( $add_result && $envelope['timestamp'] >= $cursor ) {
				$add_result = (bool) add_post_meta( $post_id, self::SYNC_UPDATE_META_KEY, $envelope, false );
			}
		}

		return $add_result;
	}
}
