<?php

/**
 * bbPress Topic Capabilites
 *
 * Used to map topic capabilities to WordPress's existing capabilities.
 *
 * @package bbPress
 * @subpackage Capabilities
 */

/**
 * Return topic capabilities
 *
 * @since bbPress (r2593)
 *
 * @uses apply_filters() Calls 'bbp_get_topic_caps' with the capabilities
 * @return array Topic capabilities
 */
function bbp_get_topic_caps() {
	return apply_filters( 'bbp_get_topic_caps', array (
		'edit_posts'          => 'edit_topics',
		'edit_others_posts'   => 'edit_others_topics',
		'publish_posts'       => 'publish_topics',
		'read_private_posts'  => 'read_private_topics',
		'read_hidden_posts'   => 'read_hidden_topics',
		'delete_posts'        => 'delete_topics',
		'delete_others_posts' => 'delete_others_topics'
	) );
}

/**
 * Return topic tag capabilities
 *
 * @since bbPress (r2593)
 *
 * @uses apply_filters() Calls 'bbp_get_topic_tag_caps' with the capabilities
 * @return array Topic tag capabilities
 */
function bbp_get_topic_tag_caps() {
	return apply_filters( 'bbp_get_topic_tag_caps', array (
		'manage_terms' => 'manage_topic_tags',
		'edit_terms'   => 'edit_topic_tags',
		'delete_terms' => 'delete_topic_tags',
		'assign_terms' => 'assign_topic_tags'
	) );
}

/**
 * Maps topic capabilities
 *
 * @since bbPress (r4242)
 *
 * @param array $caps Capabilities for meta capability
 * @param string $cap Capability name
 * @param int $user_id User id
 * @param mixed $args Arguments
 * @uses get_post() To get the post
 * @uses get_post_type_object() To get the post type object
 * @uses apply_filters() Filter capability map results
 * @return array Actual capabilities for meta capability
 */
function bbp_map_topic_meta_caps( $caps = array(), $cap = '', $user_id = 0, $args = array() ) {

	// What capability is being checked?
	switch ( $cap ) {

		/** Reading ***********************************************************/

		case 'read_topic' :

			// User cannot spectate
			if ( ! user_can( $user_id, 'spectate' ) ) {
				$caps = array( 'do_not_allow' );

			// Do some post ID based logic
			} else {

				// Get the post
				$_post = get_post( $args[0] );
				if ( !empty( $_post ) ) {

					// Get caps for post type object
					$post_type = get_post_type_object( $_post->post_type );

					// Post is public
					if ( bbp_get_public_status_id() === $_post->post_status ) {
						$caps = array( 'spectate' );

					// User is author so allow read
					} elseif ( (int) $user_id === (int) $_post->post_author ) {
						$caps = array( 'spectate' );

					// Unknown so map to private posts
					} else {
						$caps = array( $post_type->cap->read_private_posts );
					}
				}
			}

			break;

		/** Publishing ********************************************************/

		case 'publish_topics'  :

			// Moderators can always publish
			if ( user_can( $user_id, 'moderate' ) ) {
				$caps = array( 'moderate' );
			}

			break;

		/** Editing ***********************************************************/

		// Used primarily in wp-admin
		case 'edit_topics'        :
		case 'edit_others_topics' :

			// Moderators can always edit
			if ( user_can( $user_id, 'moderate' ) ) {
				$caps = array( $cap );

			// Otherwise, block
			} else {
				$caps = array( 'do_not_allow' );
			}

			break;

		// Used everywhere
		case 'edit_topic' :

			// Get the post
			$_post = get_post( $args[0] );
			if ( !empty( $_post ) ) {

				// Get caps for post type object
				$post_type = get_post_type_object( $_post->post_type );
				$caps      = array();

				// Add 'do_not_allow' cap if user is spam or deleted
				if ( bbp_is_user_inactive( $user_id ) ) {
					$caps[] = 'do_not_allow';

				// User is author so allow edit if not in admin
				} elseif ( !is_admin() && ( (int) $user_id === (int) $_post->post_author ) ) {
					$caps[] = $post_type->cap->edit_posts;

				// Unknown, so map to edit_others_posts
				} else {
					$caps[] = $post_type->cap->edit_others_posts;
				}
			}

			break;

		/** Deleting **********************************************************/

		case 'delete_topic' :

			// Get the post
			$_post = get_post( $args[0] );
			if ( !empty( $_post ) ) {

				// Get caps for post type object
				$post_type = get_post_type_object( $_post->post_type );
				$caps      = array();

				// Add 'do_not_allow' cap if user is spam or deleted
				if ( bbp_is_user_inactive( $user_id ) ) {
					$caps[] = 'do_not_allow';

				// Moderators can always edit forum content
				} elseif ( user_can( $user_id, 'moderate' ) ) {
					$caps[] = 'moderate';

				// Unknown so map to delete_others_posts
				} else {
					$caps[] = $post_type->cap->delete_others_posts;
				}
			}

			break;

		// Moderation override
		case 'delete_topics'         :
		case 'delete_others_topics'  :

			// Moderators can always delete
			if ( user_can( $user_id, 'moderate' ) ) {
				$caps = array( $cap );
			}

			break;

		/** Admin *************************************************************/

		case 'bbp_topics_admin' :
			$caps = array( 'moderate' );
			break;
	}

	return apply_filters( 'bbp_map_topic_meta_caps', $caps, $cap, $user_id, $args );
}

/**
 * Maps topic tag capabilities
 *
 * @since bbPress (r4242)
 *
 * @param array $caps Capabilities for meta capability
 * @param string $cap Capability name
 * @param int $user_id User id
 * @param mixed $args Arguments
 * @uses apply_filters() Filter capability map results
 * @return array Actual capabilities for meta capability
 */
function bbp_map_topic_tag_meta_caps( $caps, $cap, $user_id, $args ) {

	// What capability is being checked?
	switch ( $cap ) {
		case 'manage_topic_tags'    :
		case 'edit_topic_tags'      :
		case 'delete_topic_tags'    :
		case 'assign_topic_tags'    :
		case 'bbp_topic_tags_admin' :

			// Moderators can always edit
			if ( user_can( $user_id, 'moderate' ) ) {
				$caps = array( 'moderate' );
			}
	}

	return apply_filters( 'bbp_map_topic_tag_meta_caps', $caps, $cap, $user_id, $args );
}
