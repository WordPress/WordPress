<?php

namespace Yoast\WP\SEO\Helpers;

/**
 * A helper object for the user.
 */
class User_Helper {

	/**
	 * Retrieves user meta field for a user.
	 *
	 * @param int    $user_id User ID.
	 * @param string $key     Optional. The meta key to retrieve. By default, returns data for all keys.
	 * @param bool   $single  Whether to return a single value.
	 *
	 * @return mixed Will be an array if $single is false. Will be value of meta data field if $single is true.
	 */
	public function get_meta( $user_id, $key = '', $single = false ) {
		return \get_user_meta( $user_id, $key, $single );
	}

	/**
	 * Counts the number of posts the user has written in this post type.
	 *
	 * @param int          $user_id   User ID.
	 * @param array|string $post_type Optional. Single post type or array of post types to count the number of posts
	 *                                for. Default 'post'.
	 *
	 * @return int The number of posts the user has written in this post type.
	 */
	public function count_posts( $user_id, $post_type = 'post' ) {
		return (int) \count_user_posts( $user_id, $post_type, true );
	}

	/**
	 * Retrieves the requested data of the author.
	 *
	 * @param string    $field   The user field to retrieve.
	 * @param int|false $user_id User ID.
	 *
	 * @return string The author's field from the current author's DB object.
	 */
	public function get_the_author_meta( $field, $user_id ) {
		return \get_the_author_meta( $field, $user_id );
	}

	/**
	 * Retrieves the archive url of the user.
	 *
	 * @param int|false $user_id User ID.
	 *
	 * @return string The author's archive url.
	 */
	public function get_the_author_posts_url( $user_id ) {
		return \get_author_posts_url( $user_id );
	}

	/**
	 * Retrieves the current user ID.
	 *
	 * @return int The current user's ID, or 0 if no user is logged in.
	 */
	public function get_current_user_id() {
		return \get_current_user_id();
	}

	/**
	 * Returns the current users display_name.
	 *
	 * @return string
	 */
	public function get_current_user_display_name(): string {
		$user = \wp_get_current_user();
		if ( $user && $user->display_name ) {
			return $user->display_name;
		}

		return '';
	}

	/**
	 * Updates user meta field for a user.
	 *
	 * Use the $prev_value parameter to differentiate between meta fields with the
	 * same key and user ID.
	 *
	 * If the meta field for the user does not exist, it will be added.
	 *
	 * @param int    $user_id    User ID.
	 * @param string $meta_key   Metadata key.
	 * @param mixed  $meta_value Metadata value. Must be serializable if non-scalar.
	 * @param mixed  $prev_value Optional. Previous value to check before updating.
	 *                           If specified, only update existing metadata entries with
	 *                           this value. Otherwise, update all entries. Default empty.
	 *
	 * @return int|bool Meta ID if the key didn't exist, true on successful update,
	 *                  false on failure or if the value passed to the function
	 *                  is the same as the one that is already in the database.
	 */
	public function update_meta( $user_id, $meta_key, $meta_value, $prev_value = '' ) {
		return \update_user_meta( $user_id, $meta_key, $meta_value, $prev_value );
	}

	/**
	 * Removes metadata matching criteria from a user.
	 *
	 * You can match based on the key, or key and value. Removing based on key and
	 * value, will keep from removing duplicate metadata with the same key. It also
	 * allows removing all metadata matching key, if needed.
	 *
	 * @param int    $user_id    User ID.
	 * @param string $meta_key   Metadata name.
	 * @param mixed  $meta_value Optional. Metadata value. If provided,
	 *                           rows will only be removed that match the value.
	 *                           Must be serializable if non-scalar. Default empty.
	 *
	 * @return bool True on success, false on failure.
	 */
	public function delete_meta( $user_id, $meta_key, $meta_value = '' ) {
		return \delete_user_meta( $user_id, $meta_key, $meta_value );
	}
}
