<?php

/**
 * bbPress User Template Tags
 *
 * @package bbPress
 * @subpackage TemplateTags
 */

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

/** Users *********************************************************************/

/**
 * Output a validated user id
 *
 * @since bbPress (r2729)
 *
 * @param int $user_id Optional. User id
 * @param bool $displayed_user_fallback Fallback on displayed user?
 * @param bool $current_user_fallback Fallback on current user?
 * @uses bbp_get_user_id() To get the user id
 */
function bbp_user_id( $user_id = 0, $displayed_user_fallback = true, $current_user_fallback = false ) {
	echo bbp_get_user_id( $user_id, $displayed_user_fallback, $current_user_fallback );
}
	/**
	 * Return a validated user id
	 *
	 * @since bbPress (r2729)
	 *
	 * @param int $user_id Optional. User id
	 * @param bool $displayed_user_fallback Fallback on displayed user?
	 * @param bool $current_user_fallback Fallback on current user?
	 * @uses get_query_var() To get the 'bbp_user_id' query var
	 * @uses apply_filters() Calls 'bbp_get_user_id' with the user id
	 * @return int Validated user id
	 */
	function bbp_get_user_id( $user_id = 0, $displayed_user_fallback = true, $current_user_fallback = false ) {
		$bbp = bbpress();

		// Easy empty checking
		if ( !empty( $user_id ) && is_numeric( $user_id ) ) {
			$bbp_user_id = $user_id;

		// Currently viewing or editing a user
		} elseif ( ( true === $displayed_user_fallback ) && !empty( $bbp->displayed_user->ID ) ) {
			$bbp_user_id = $bbp->displayed_user->ID;

		// Maybe fallback on the current_user ID
		} elseif ( ( true === $current_user_fallback ) && !empty( $bbp->current_user->ID ) ) {
			$bbp_user_id = $bbp->current_user->ID;

		// Failsafe
		} else {
			$bbp_user_id = 0;
		}

		return (int) apply_filters( 'bbp_get_user_id', (int) $bbp_user_id, $displayed_user_fallback, $current_user_fallback );
	}

/**
 * Output ID of current user
 *
 * @since bbPress (r2574)
 *
 * @uses bbp_get_current_user_id() To get the current user id
 */
function bbp_current_user_id() {
	echo bbp_get_current_user_id();
}
	/**
	 * Return ID of current user
	 *
	 * @since bbPress (r2574)
	 *
	 * @uses bbp_get_user_id() To get the current user id
	 * @uses apply_filters() Calls 'bbp_get_current_user_id' with the id
	 * @return int Current user id
	 */
	function bbp_get_current_user_id() {
		return apply_filters( 'bbp_get_current_user_id', bbp_get_user_id( 0, false, true ) );
	}

/**
 * Output ID of displayed user
 *
 * @since bbPress (r2688)
 *
 * @uses bbp_get_displayed_user_id() To get the displayed user id
 */
function bbp_displayed_user_id() {
	echo bbp_get_displayed_user_id();
}
	/**
	 * Return ID of displayed user
	 *
	 * @since bbPress (r2688)
	 *
	 * @uses bbp_get_user_id() To get the displayed user id
	 * @uses apply_filters() Calls 'bbp_get_displayed_user_id' with the id
	 * @return int Displayed user id
	 */
	function bbp_get_displayed_user_id() {
		return apply_filters( 'bbp_get_displayed_user_id', bbp_get_user_id( 0, true, false ) );
	}

/**
 * Output a sanitized user field value
 *
 * This function relies on the $filter parameter to decide how to sanitize
 * the field value that it finds. Since it uses the WP_User object's magic
 * __get() method, it can also be used to get user_meta values.
 *
 * @since bbPress (r2688)
 *
 * @param string $field Field to get
 * @param string $filter How to filter the field value (null|raw|db|display|edit)
 * @uses bbp_get_displayed_user_field() To get the field
 */
function bbp_displayed_user_field( $field = '', $filter = 'display' ) {
	echo bbp_get_displayed_user_field( $field, $filter );
}
	/**
	 * Return a sanitized user field value
	 *
	 * This function relies on the $filter parameter to decide how to sanitize
	 * the field value that it finds. Since it uses the WP_User object's magic
	 * __get() method, it can also be used to get user_meta values.
	 *
	 * @since bbPress (r2688)
	 *
	 * @param string $field Field to get
	 * @param string $filter How to filter the field value (null|raw|db|display|edit)
	 * @see WP_User::__get() for more on how the value is retrieved
	 * @see sanitize_user_field() for more on how the value is sanitized
	 * @uses apply_filters() Calls 'bbp_get_displayed_user_field' with the value
	 * @return string|bool Value of the field if it exists, else false
	 */
	function bbp_get_displayed_user_field( $field = '', $filter = 'display' ) {

		// Get the displayed user
		$user         = bbpress()->displayed_user;

		// Juggle the user filter property because we don't want to muck up how
		// other code might interact with this object.
		$old_filter   = $user->filter;
		$user->filter = $filter;

		// Get the field value from the WP_User object. We don't need to perform
		// an isset() because the WP_User::__get() does it for us.
		$value        = $user->$field;

		// Put back the user filter property that was previously juggled above.
		$user->filter = $old_filter;

		// Return empty
		return apply_filters( 'bbp_get_displayed_user_field', $value, $field, $filter );
	}

/**
 * Output name of current user
 *
 * @since bbPress (r2574)
 *
 * @uses bbp_get_current_user_name() To get the current user name
 */
function bbp_current_user_name() {
	echo bbp_get_current_user_name();
}
	/**
	 * Return name of current user
	 *
	 * @since bbPress (r2574)
	 *
	 * @uses apply_filters() Calls 'bbp_get_current_user_name' with the
	 *                        current user name
	 * @return string
	 */
	function bbp_get_current_user_name() {
		global $user_identity;

		$current_user_name = is_user_logged_in() ? $user_identity : __( 'Anonymous', 'bbpress' );

		return apply_filters( 'bbp_get_current_user_name', $current_user_name );
	}

/**
 * Output avatar of current user
 *
 * @since bbPress (r2574)
 *
 * @param int $size Size of the avatar. Defaults to 40
 * @uses bbp_get_current_user_avatar() To get the current user avatar
 */
function bbp_current_user_avatar( $size = 40 ) {
	echo bbp_get_current_user_avatar( $size );
}

	/**
	 * Return avatar of current user
	 *
	 * @since bbPress (r2574)
	 *
	 * @param int $size Size of the avatar. Defaults to 40
	 * @uses bbp_get_current_user_id() To get the current user id
	 * @uses bbp_get_current_anonymous_user_data() To get the current
	 *                                              anonymous user's email
	 * @uses get_avatar() To get the avatar
	 * @uses apply_filters() Calls 'bbp_get_current_user_avatar' with the
	 *                        avatar and size
	 * @return string Current user avatar
	 */
	function bbp_get_current_user_avatar( $size = 40 ) {

		$user = bbp_get_current_user_id();
		if ( empty( $user ) )
			$user = bbp_get_current_anonymous_user_data( 'email' );

		$avatar = get_avatar( $user, $size );

		return apply_filters( 'bbp_get_current_user_avatar', $avatar, $size );
	}

/**
 * Output link to the profile page of a user
 *
 * @since bbPress (r2688)
 *
 * @param int $user_id Optional. User id
 * @uses bbp_get_user_profile_link() To get user profile link
 */
function bbp_user_profile_link( $user_id = 0 ) {
	echo bbp_get_user_profile_link( $user_id );
}
	/**
	 * Return link to the profile page of a user
	 *
	 * @since bbPress (r2688)
	 *
	 * @param int $user_id Optional. User id
	 * @uses bbp_get_user_id() To get user id
	 * @uses get_userdata() To get user data
	 * @uses bbp_get_user_profile_url() To get user profile url
	 * @uses apply_filters() Calls 'bbp_get_user_profile_link' with the user
	 *                        profile link and user id
	 * @return string User profile link
	 */
	function bbp_get_user_profile_link( $user_id = 0 ) {

		// Validate user id
		$user_id = bbp_get_user_id( $user_id );
		if ( empty( $user_id ) )
			return false;

		$user      = get_userdata( $user_id );
		$user_link = '<a href="' . esc_url( bbp_get_user_profile_url( $user_id ) ) . '">' . esc_html( $user->display_name ) . '</a>';

		return apply_filters( 'bbp_get_user_profile_link', $user_link, $user_id );
	}

/**
 * Output a users nicename to the screen
 *
 * @since bbPress (r4671)
 *
 * @param int $user_id User ID whose nicename to get
 * @param array $args before|after|user_id|force
 */
function bbp_user_nicename( $user_id = 0, $args = array() ) {
	echo bbp_get_user_nicename( $user_id, $args );
}
	/**
	 * Return a users nicename to the screen
	 *
	 * @since bbPress (r4671)
	 *
	 * @param int $user_id User ID whose nicename to get
	 * @param array $args before|after|user_id|force
	 * @return string User nicename, maybe wrapped in before/after strings
	 */
	function bbp_get_user_nicename( $user_id = 0, $args = array() ) {

		// Bail if no user ID passed
		$user_id = bbp_get_user_id( $user_id );
		if ( empty( $user_id ) )
			return false;

		// Parse default arguments
		$r = bbp_parse_args( $args, array(
			'user_id' => $user_id,
			'before'  => '',
			'after'   => '',
			'force'   => ''
		), 'get_user_nicename' );

		// Get the user data and nicename
		if ( empty( $r['force'] ) ) {
			$user     = get_userdata( $user_id );
			$nicename = $user->user_nicename;

		// Force the nicename to something else
		} else {
			$nicename = (string) $r['force'];
		}

		// Maybe wrap the nicename
		$retval = !empty( $nicename ) ? ( $r['before'] . $nicename . $r['after'] ) : '';

		// Filter and return
		return (string) apply_filters( 'bbp_get_user_nicename', $retval, $user_id, $r );
	}

/**
 * Output URL to the profile page of a user
 *
 * @since bbPress (r2688)
 *
 * @param int $user_id Optional. User id
 * @param string $user_nicename Optional. User nicename
 * @uses bbp_get_user_profile_url() To get user profile url
 */
function bbp_user_profile_url( $user_id = 0, $user_nicename = '' ) {
	echo esc_url( bbp_get_user_profile_url( $user_id, $user_nicename ) );
}
	/**
	 * Return URL to the profile page of a user
	 *
	 * @since bbPress (r2688)
	 *
	 * @param int $user_id Optional. User id
	 * @param string $user_nicename Optional. User nicename
	 * @uses bbp_get_user_id() To get user id
	 * @uses WP_Rewrite::using_permalinks() To check if the blog is using
	 *                                       permalinks
	 * @uses add_query_arg() To add custom args to the url
	 * @uses home_url() To get blog home url
	 * @uses apply_filters() Calls 'bbp_get_user_profile_url' with the user
	 *                        profile url, user id and user nicename
	 * @return string User profile url
	 */
	function bbp_get_user_profile_url( $user_id = 0, $user_nicename = '' ) {
		global $wp_rewrite;

		// Use displayed user ID if there is one, and one isn't requested
		$user_id = bbp_get_user_id( $user_id );
		if ( empty( $user_id ) )
			return false;

		// Allow early overriding of the profile URL to cut down on processing
		$early_profile_url = apply_filters( 'bbp_pre_get_user_profile_url', (int) $user_id );
		if ( is_string( $early_profile_url ) )
			return $early_profile_url;

		// Pretty permalinks
		if ( $wp_rewrite->using_permalinks() ) {
			$url = $wp_rewrite->root . bbp_get_user_slug() . '/%' . bbp_get_user_rewrite_id() . '%';

			// Get username if not passed
			if ( empty( $user_nicename ) ) {
				$user_nicename = bbp_get_user_nicename( $user_id );
			}

			$url = str_replace( '%' . bbp_get_user_rewrite_id() . '%', $user_nicename, $url );
			$url = home_url( user_trailingslashit( $url ) );

		// Unpretty permalinks
		} else {
			$url = add_query_arg( array( bbp_get_user_rewrite_id() => $user_id ), home_url( '/' ) );
		}

		return apply_filters( 'bbp_get_user_profile_url', $url, $user_id, $user_nicename );
	}

/**
 * Output link to the profile edit page of a user
 *
 * @since bbPress (r2688)
 *
 * @param int $user_id Optional. User id
 * @uses bbp_get_user_profile_edit_link() To get user profile edit link
 */
function bbp_user_profile_edit_link( $user_id = 0 ) {
	echo bbp_get_user_profile_edit_link( $user_id );
}
	/**
	 * Return link to the profile edit page of a user
	 *
	 * @since bbPress (r2688)
	 *
	 * @param int $user_id Optional. User id
	 * @uses bbp_get_user_id() To get user id
	 * @uses get_userdata() To get user data
	 * @uses bbp_get_user_profile_edit_url() To get user profile edit url
	 * @uses apply_filters() Calls 'bbp_get_user_profile_link' with the edit
	 *                        link and user id
	 * @return string User profile edit link
	 */
	function bbp_get_user_profile_edit_link( $user_id = 0 ) {

		// Validate user id
		$user_id = bbp_get_user_id( $user_id );
		if ( empty( $user_id ) )
			return false;

		$user      = get_userdata( $user_id );
		$edit_link = '<a href="' . esc_url( bbp_get_user_profile_url( $user_id ) ) . '">' . esc_html( $user->display_name ) . '</a>';
		return apply_filters( 'bbp_get_user_profile_edit_link', $edit_link, $user_id );
	}

/**
 * Output URL to the profile edit page of a user
 *
 * @since bbPress (r2688)
 *
 * @param int $user_id Optional. User id
 * @param string $user_nicename Optional. User nicename
 * @uses bbp_get_user_profile_edit_url() To get user profile edit url
 */
function bbp_user_profile_edit_url( $user_id = 0, $user_nicename = '' ) {
	echo esc_url( bbp_get_user_profile_edit_url( $user_id, $user_nicename ) );
}
	/**
	 * Return URL to the profile edit page of a user
	 *
	 * @since bbPress (r2688)
	 *
	 * @param int $user_id Optional. User id
	 * @param string $user_nicename Optional. User nicename
	 * @uses bbp_get_user_id() To get user id
	 * @uses WP_Rewrite::using_permalinks() To check if the blog is using
	 *                                       permalinks
	 * @uses add_query_arg() To add custom args to the url
	 * @uses home_url() To get blog home url
	 * @uses apply_filters() Calls 'bbp_get_user_edit_profile_url' with the
	 *                        edit profile url, user id and user nicename
	 * @return string
	 */
	function bbp_get_user_profile_edit_url( $user_id = 0, $user_nicename = '' ) {
		global $wp_rewrite;

		$bbp     = bbpress();
		$user_id = bbp_get_user_id( $user_id );
		if ( empty( $user_id ) )
			return false;

		// Pretty permalinks
		if ( $wp_rewrite->using_permalinks() ) {
			$url = $wp_rewrite->root . bbp_get_user_slug() . '/%' . $bbp->user_id . '%/' . $bbp->edit_id;

			// Get username if not passed
			if ( empty( $user_nicename ) ) {
				$user = get_userdata( $user_id );
				if ( !empty( $user->user_nicename ) ) {
					$user_nicename = $user->user_nicename;
				}
			}

			$url = str_replace( '%' . $bbp->user_id . '%', $user_nicename, $url );
			$url = home_url( user_trailingslashit( $url ) );

		// Unpretty permalinks
		} else {
			$url = add_query_arg( array( $bbp->user_id => $user_id, $bbp->edit_id => '1' ), home_url( '/' ) );
		}

		return apply_filters( 'bbp_get_user_edit_profile_url', $url, $user_id, $user_nicename );

	}

/**
 * Output a user's main role for display
 *
 * @since bbPress (r3860)
 *
 * @param int $user_id
 * @uses bbp_get_user_display_role To get the user display role
 */
function bbp_user_display_role( $user_id = 0 ) {
	echo bbp_get_user_display_role( $user_id );
}
	/**
	 * Return a user's main role for display
	 *
	 * @since bbPress (r3860)
	 *
	 * @param int $user_id
	 * @uses bbp_get_user_id() to verify the user ID
	 * @uses bbp_is_user_inactive() to check if user is inactive
	 * @uses user_can() to check if user has special capabilities
	 * @uses apply_filters() Calls 'bbp_get_user_display_role' with the
	 *                        display role, user id, and user role
	 * @return string
	 */
	function bbp_get_user_display_role( $user_id = 0 ) {

		// Validate user id
		$user_id = bbp_get_user_id( $user_id );

		// User is not registered
		if ( empty( $user_id ) ) {
			$role = __( 'Guest', 'bbpress' );

		// User is not active
		} elseif ( bbp_is_user_inactive( $user_id ) ) {
			$role = __( 'Inactive', 'bbpress' );

		// User have a role
		} else {
			$role_id = bbp_get_user_role( $user_id );
			$role    = bbp_get_dynamic_role_name( $role_id );
		}

		// No role found so default to generic "Member"
		if ( empty( $role ) ) {
			$role = __( 'Member', 'bbpress' );
		}

		return apply_filters( 'bbp_get_user_display_role', $role, $user_id );
	}

/**
 * Output the link to the admin section
 *
 * @since bbPress (r2827)
 *
 * @param mixed $args Optional. See {@link bbp_get_admin_link()}
 * @uses bbp_get_admin_link() To get the admin link
 */
function bbp_admin_link( $args = '' ) {
	echo bbp_get_admin_link( $args );
}
	/**
	 * Return the link to the admin section
	 *
	 * @since bbPress (r2827)
	 *
	 * @param mixed $args Optional. This function supports these arguments:
	 *  - text: The text
	 *  - before: Before the lnk
	 *  - after: After the link
	 * @uses current_user_can() To check if the current user can moderate
	 * @uses admin_url() To get the admin url
	 * @uses apply_filters() Calls 'bbp_get_admin_link' with the link & args
	 * @return The link
	 */
	function bbp_get_admin_link( $args = '' ) {
		if ( !current_user_can( 'moderate' ) )
			return;

		if ( !empty( $args ) && is_string( $args ) && ( false === strpos( $args, '=' ) ) )
			$args = array( 'text' => $args );

		// Parse arguments against default values
		$r = bbp_parse_args( $args, array(
			'text'   => __( 'Admin', 'bbpress' ),
			'before' => '',
			'after'  => ''
		), 'get_admin_link' );

		$retval = $r['before'] . '<a href="' . esc_url( admin_url() ) . '">' . $r['text'] . '</a>' . $r['after'];

		return apply_filters( 'bbp_get_admin_link', $retval, $r );
	}

/** User IP *******************************************************************/

/**
 * Output the author IP address of a post
 *
 * @since bbPress (r3120)
 *
 * @param mixed $args Optional. If it is an integer, it is used as post id.
 * @uses bbp_get_author_ip() To get the post author link
 */
function bbp_author_ip( $args = '' ) {
	echo bbp_get_author_ip( $args );
}
	/**
	 * Return the author IP address of a post
	 *
	 * @since bbPress (r3120)
	 *
	 * @param mixed $args Optional. If an integer, it is used as reply id.
	 * @uses get_post_meta() To check if it's a topic page
	 * @return string Author link of reply
	 */
	function bbp_get_author_ip( $args = '' ) {

		// Used as post id
		$post_id = is_numeric( $args ) ? (int) $args : 0;

		// Parse arguments against default values
		$r = bbp_parse_args( $args, array(
			'post_id' => $post_id,
			'before'  => '<span class="bbp-author-ip">(',
			'after'   => ')</span>'
		), 'get_author_ip' );

		// Get the author IP meta value
		$author_ip = get_post_meta( $r['post_id'], '_bbp_author_ip', true );
		if ( !empty( $author_ip ) ) {
			$author_ip = $r['before'] . $author_ip . $r['after'];

		// No IP address
		} else {
			$author_ip = '';
		}

		return apply_filters( 'bbp_get_author_ip', $author_ip, $r );
	}

/** Anonymous Fields **********************************************************/

/**
 * Output the author disylay-name of a topic or reply.
 *
 * Convenience function to ensure proper template functions are called
 * and correct filters are executed. Used primarily to display topic
 * and reply author information in the anonymous form template-part.
 *
 * @since bbPress (r5119)
 *
 * @param int $post_id
 * @uses bbp_get_author_display_name() to get the author name
 */
function bbp_author_display_name( $post_id = 0 ) {
	echo bbp_get_author_display_name( $post_id );
}

	/**
	 * Return the author name of a topic or reply.
	 *
	 * Convenience function to ensure proper template functions are called
	 * and correct filters are executed. Used primarily to display topic
	 * and reply author information in the anonymous form template-part.
	 *
	 * @since bbPress (r5119)
	 *
	 * @param int $post_id
	 *
	 * @uses bbp_is_topic_edit()
	 * @uses bbp_get_topic_author_display_name()
	 * @uses bbp_is_reply_edit()
	 * @uses bbp_get_reply_author_display_name()
	 * @uses bbp_current_anonymous_user_data()
	 *
	 * @return string The name of the author
	 */
	function bbp_get_author_display_name( $post_id = 0 ) {

		// Define local variable(s)
		$retval = '';

		// Topic edit
		if ( bbp_is_topic_edit() ) {
			$retval = bbp_get_topic_author_display_name( $post_id );

		// Reply edit
		} elseif ( bbp_is_reply_edit() ) {
			$retval = bbp_get_reply_author_display_name( $post_id );

		// Not an edit, so rely on current user cookie data
		} else {
			$retval = bbp_current_anonymous_user_data( 'name' );
		}

		return apply_filters( 'bbp_get_author_display_name', $retval, $post_id );
	}

/**
 * Output the author email of a topic or reply.
 *
 * Convenience function to ensure proper template functions are called
 * and correct filters are executed. Used primarily to display topic
 * and reply author information in the anonymous user form template-part.
 *
 * @since bbPress (r5119)
 *
 * @param int $post_id
 * @uses bbp_get_author_email() to get the author email
 */
function bbp_author_email( $post_id = 0 ) {
	echo bbp_get_author_email( $post_id );
}

	/**
	 * Return the author email of a topic or reply.
	 *
	 * Convenience function to ensure proper template functions are called
	 * and correct filters are executed. Used primarily to display topic
	 * and reply author information in the anonymous user form template-part.
	 *
	 * @since bbPress (r5119)
	 *
	 * @param int $post_id
	 *
	 * @uses bbp_is_topic_edit()
	 * @uses bbp_get_topic_author_email()
	 * @uses bbp_is_reply_edit()
	 * @uses bbp_get_reply_author_email()
	 * @uses bbp_current_anonymous_user_data()
	 *
	 * @return string The email of the author
	 */
	function bbp_get_author_email( $post_id = 0 ) {

		// Define local variable(s)
		$retval = '';

		// Topic edit
		if ( bbp_is_topic_edit() ) {
			$retval = bbp_get_topic_author_email( $post_id );

		// Reply edit
		} elseif ( bbp_is_reply_edit() ) {
			$retval = bbp_get_reply_author_email( $post_id );

		// Not an edit, so rely on current user cookie data
		} else {
			$retval = bbp_current_anonymous_user_data( 'email' );
		}

		return apply_filters( 'bbp_get_author_email', $retval, $post_id );
	}

/**
 * Output the author url of a topic or reply.
 *
 * Convenience function to ensure proper template functions are called
 * and correct filters are executed. Used primarily to display topic
 * and reply author information in the anonymous user form template-part.
 *
 * @since bbPress (r5119)
 *
 * @param int $post_id
 * @uses bbp_get_author_url() to get the author url
 */
function bbp_author_url( $post_id = 0 ) {
	echo bbp_get_author_url( $post_id );
}

	/**
	 * Return the author url of a topic or reply.
	 *
	 * Convenience function to ensure proper template functions are called
	 * and correct filters are executed. Used primarily to display topic
	 * and reply author information in the anonymous user form template-part.
	 *
	 * @since bbPress (r5119)
	 *
	 * @param int $post_id
	 *
	 * @uses bbp_is_topic_edit()
	 * @uses bbp_get_topic_author_url()
	 * @uses bbp_is_reply_edit()
	 * @uses bbp_get_reply_author_url()
	 * @uses bbp_current_anonymous_user_data()
	 *
	 * @return string The url of the author
	 */
	function bbp_get_author_url( $post_id = 0 ) {

		// Define local variable(s)
		$retval = '';

		// Topic edit
		if ( bbp_is_topic_edit() ) {
			$retval = bbp_get_topic_author_url( $post_id );

		// Reply edit
		} elseif ( bbp_is_reply_edit() ) {
			$retval = bbp_get_reply_author_url( $post_id );

		// Not an edit, so rely on current user cookie data
		} else {
			$retval = bbp_current_anonymous_user_data( 'url' );
		}

		return apply_filters( 'bbp_get_author_url', $retval, $post_id );
	}

/** Favorites *****************************************************************/

/**
 * Output the link to the user's favorites page (profile page)
 *
 * @since bbPress (r2652)
 *
 * @param int $user_id Optional. User id
 * @uses bbp_get_favorites_permalink() To get the favorites permalink
 */
function bbp_favorites_permalink( $user_id = 0 ) {
	echo esc_url( bbp_get_favorites_permalink( $user_id ) );
}
	/**
	 * Return the link to the user's favorites page (profile page)
	 *
	 * @since bbPress (r2652)
	 *
	 * @param int $user_id Optional. User id
	 * @uses bbp_get_user_profile_url() To get the user profile url
	 * @uses apply_filters() Calls 'bbp_get_favorites_permalink' with the
	 *                        user profile url and user id
	 * @return string Permanent link to user profile page
	 */
	function bbp_get_favorites_permalink( $user_id = 0 ) {
		global $wp_rewrite;

		// Use displayed user ID if there is one, and one isn't requested
		$user_id = bbp_get_user_id( $user_id );
		if ( empty( $user_id ) )
			return false;

		// Allow early overriding of the profile URL to cut down on processing
		$early_profile_url = apply_filters( 'bbp_pre_get_favorites_permalink', (int) $user_id );
		if ( is_string( $early_profile_url ) )
			return $early_profile_url;

		// Pretty permalinks
		if ( $wp_rewrite->using_permalinks() ) {
			$url = $wp_rewrite->root . bbp_get_user_slug() . '/%' . bbp_get_user_rewrite_id() . '%/%' . bbp_get_user_favorites_rewrite_id() . '%';
			$user = get_userdata( $user_id );
			if ( ! empty( $user->user_nicename ) ) {
				$user_nicename = $user->user_nicename;
			} else {
				$user_nicename = $user->user_login;
			}
			$url = str_replace( '%' . bbp_get_user_rewrite_id() . '%', $user_nicename, $url );
			$url = str_replace( '%' . bbp_get_user_favorites_rewrite_id() . '%', bbp_get_user_favorites_slug(), $url );
			$url = home_url( user_trailingslashit( $url ) );

		// Unpretty permalinks
		} else {
			$url = add_query_arg( array(
				bbp_get_user_rewrite_id()           => $user_id,
				bbp_get_user_favorites_rewrite_id() => bbp_get_user_favorites_slug(),
			), home_url( '/' ) );
		}

		return apply_filters( 'bbp_get_favorites_permalink', $url, $user_id );
	}

/**
 * Output the link to make a topic favorite/remove a topic from favorites
 *
 * @since bbPress (r2652)
 *
 * @param mixed $args See {@link bbp_get_user_favorites_link()}
 * @param int $user_id Optional. User id
 * @param bool $wrap Optional. If you want to wrap the link in <span id="favorite-toggle">.
 * @uses bbp_get_user_favorites_link() To get the user favorites link
 */
function bbp_user_favorites_link( $args = array(), $user_id = 0, $wrap = true ) {
	echo bbp_get_user_favorites_link( $args, $user_id, $wrap );
}
	/**
	 * User favorites link
	 *
	 * Return the link to make a topic favorite/remove a topic from
	 * favorites
	 *
	 * @since bbPress (r2652)
	 *
	 * @param mixed $args This function supports these arguments:
	 *  - subscribe: Favorite text
	 *  - unsubscribe: Unfavorite text
	 *  - user_id: User id
	 *  - topic_id: Topic id
	 *  - before: Before the link
	 *  - after: After the link
	 * @param int $user_id Optional. User id
	 * @param int $topic_id Optional. Topic id
	 * @param bool $wrap Optional. If you want to wrap the link in <span id="favorite-toggle">. See ajax_favorite()
	 * @uses bbp_get_user_id() To get the user id
	 * @uses current_user_can() If the current user can edit the user
	 * @uses bbp_get_topic_id() To get the topic id
	 * @uses bbp_is_user_favorite() To check if the topic is user's favorite
	 * @uses bbp_get_favorites_permalink() To get the favorites permalink
	 * @uses bbp_get_topic_permalink() To get the topic permalink
	 * @uses bbp_is_favorites() Is it the favorites page?
	 * @uses apply_filters() Calls 'bbp_get_user_favorites_link' with the
	 *                        html, add args, remove args, user & topic id
	 * @return string User favorites link
	 */
	function bbp_get_user_favorites_link( $args = '', $user_id = 0, $wrap = true ) {
		if ( ! bbp_is_favorites_active() ) {
			return false;
		}

		// Parse arguments against default values
		$r = bbp_parse_args( $args, array(
			'favorite'  => __( 'Favorite',  'bbpress' ),
			'favorited' => __( 'Favorited', 'bbpress' ),
			'user_id'   => 0,
			'topic_id'  => 0,
			'before'    => '',
			'after'     => ''
		), 'get_user_favorites_link' );

		// Validate user and topic ID's
		$user_id  = bbp_get_user_id( $r['user_id'], true, true );
		$topic_id = bbp_get_topic_id( $r['topic_id'] );
		if ( empty( $user_id ) || empty( $topic_id ) ) {
			return false;
		}

		// No link if you can't edit yourself
		if ( ! current_user_can( 'edit_user', (int) $user_id ) ) {
			return false;
		}

		// Decide which link to show
		$is_fav = bbp_is_user_favorite( $user_id, $topic_id );
		if ( ! empty( $is_fav ) ) {
			$text       = $r['favorited'];
			$query_args = array( 'action' => 'bbp_favorite_remove', 'topic_id' => $topic_id );
		} else {
			$text       = $r['favorite'];
			$query_args = array( 'action' => 'bbp_favorite_add',    'topic_id' => $topic_id );
		}

		// Create the link based where the user is and if the topic is
		// already the user's favorite
		if ( bbp_is_favorites() ) {
			$permalink = bbp_get_favorites_permalink( $user_id );
		} elseif ( bbp_is_single_topic() || bbp_is_single_reply() ) {
			$permalink = bbp_get_topic_permalink( $topic_id );
		} else {
			$permalink = get_permalink();
		}

		$url  = esc_url( wp_nonce_url( add_query_arg( $query_args, $permalink ), 'toggle-favorite_' . $topic_id ) );
		$sub  = $is_fav ? ' class="is-favorite"' : '';
		$html = sprintf( '%s<span id="favorite-%d"  %s><a href="%s" class="favorite-toggle" data-topic="%d">%s</a></span>%s', $r['before'], $topic_id, $sub, $url, $topic_id, $text, $r['after'] );

		// Initial output is wrapped in a span, ajax output is hooked to this
		if ( ! empty( $wrap ) ) {
			$html = '<span id="favorite-toggle">' . $html . '</span>';
		}

		// Return the link
		return apply_filters( 'bbp_get_user_favorites_link', $html, $r, $user_id, $topic_id );
	}

/** Subscriptions *************************************************************/

/**
 * Output the link to the user's subscriptions page (profile page)
 *
 * @since bbPress (r2688)
 *
 * @param int $user_id Optional. User id
 * @uses bbp_get_subscriptions_permalink() To get the subscriptions link
 */
function bbp_subscriptions_permalink( $user_id = 0 ) {
	echo esc_url( bbp_get_subscriptions_permalink( $user_id ) );
}
	/**
	 * Return the link to the user's subscriptions page (profile page)
	 *
	 * @since bbPress (r2688)
	 *
	 * @param int $user_id Optional. User id
	 * @uses bbp_get_user_profile_url() To get the user profile url
	 * @uses apply_filters() Calls 'bbp_get_subscriptions_permalink' with
	 *                        the user profile url and user id
	 * @return string Permanent link to user subscriptions page
	 */
	function bbp_get_subscriptions_permalink( $user_id = 0 ) {
		global $wp_rewrite;

		// Use displayed user ID if there is one, and one isn't requested
		$user_id = bbp_get_user_id( $user_id );
		if ( empty( $user_id ) )
			return false;

		// Allow early overriding of the profile URL to cut down on processing
		$early_profile_url = apply_filters( 'bbp_pre_get_subscriptions_permalink', (int) $user_id );
		if ( is_string( $early_profile_url ) )
			return $early_profile_url;

		// Pretty permalinks
		if ( $wp_rewrite->using_permalinks() ) {
			$url  = $wp_rewrite->root . bbp_get_user_slug() . '/%' . bbp_get_user_rewrite_id() . '%/%' . bbp_get_user_subscriptions_rewrite_id() . '%';
			$user = get_userdata( $user_id );
			if ( ! empty( $user->user_nicename ) ) {
				$user_nicename = $user->user_nicename;
			} else {
				$user_nicename = $user->user_login;
			}
			$url = str_replace( '%' . bbp_get_user_rewrite_id()               . '%', $user_nicename,                    $url );
			$url = str_replace( '%' . bbp_get_user_subscriptions_rewrite_id() . '%', bbp_get_user_subscriptions_slug(), $url );
			$url = home_url( user_trailingslashit( $url ) );

		// Unpretty permalinks
		} else {
			$url = add_query_arg( array(
				bbp_get_user_rewrite_id()           => $user_id,
				bbp_get_user_subscriptions_rewrite_id() => bbp_get_user_subscriptions_slug(),
			), home_url( '/' ) );
		}

		return apply_filters( 'bbp_get_subscriptions_permalink', $url, $user_id );
	}

/**
 * Output the link to subscribe/unsubscribe from a topic
 *
 * @since bbPress (r2668)
 *
 * @param mixed $args See {@link bbp_get_user_subscribe_link()}
 * @param int $user_id Optional. User id
 * @param bool $wrap Optional. If you want to wrap the link in <span id="subscription-toggle">.
 * @uses bbp_get_user_subscribe_link() To get the subscribe link
 */
function bbp_user_subscribe_link( $args = '', $user_id = 0, $wrap = true ) {
	echo bbp_get_user_subscribe_link( $args, $user_id, $wrap );
}
	/**
	 * Return the link to subscribe/unsubscribe from a forum or topic
	 *
	 * @since bbPress (r2668)
	 *
	 * @param mixed $args This function supports these arguments:
	 *  - subscribe: Subscribe text
	 *  - unsubscribe: Unsubscribe text
	 *  - user_id: User id
	 *  - topic_id: Topic id
	 *  - forum_id: Forum id
	 *  - before: Before the link
	 *  - after: After the link
	 * @param int $user_id Optional. User id
	 * @param bool $wrap Optional. If you want to wrap the link in <span id="subscription-toggle">.
	 * @uses bbp_is_subscriptions_active() to check if subscriptions are active
	 * @uses bbp_get_user_id() To get the user id
	 * @uses bbp_get_user_id() To get the user id
	 * @uses bbp_get_topic_id() To get the topic id
	 * @uses bbp_get_forum_id() To get the forum id
	 * @uses current_user_can() To check if the current user can edit user
	 * @uses bbp_is_user_subscribed_to_forum() To check if the user is subscribed to the forum
	 * @uses bbp_is_user_subscribed_to_topic() To check if the user is subscribed to the topic
	 * @uses bbp_is_subscriptions() To check if it's the subscriptions page
	 * @uses bbp_get_subscriptions_permalink() To get subscriptions link
	 * @uses bbp_get_topic_permalink() To get topic link
	 * @uses apply_filters() Calls 'bbp_get_user_subscribe_link' with the
	 *                        link, args, user id & topic id
	 * @return string Permanent link to topic
	 */
	function bbp_get_user_subscribe_link( $args = '', $user_id = 0, $wrap = true ) {
		if ( ! bbp_is_subscriptions_active() ) {
			return;
		}

		// Parse arguments against default values
		$r = bbp_parse_args( $args, array(
			'subscribe'   => __( 'Subscribe',   'bbpress' ),
			'unsubscribe' => __( 'Unsubscribe', 'bbpress' ),
			'user_id'     => 0,
			'topic_id'    => 0,
			'forum_id'    => 0,
			'before'      => '&nbsp;|&nbsp;',
			'after'       => ''
		), 'get_user_subscribe_link' );

		// Validate user and object ID's
		$user_id  = bbp_get_user_id( $r['user_id'], true, true );
		$topic_id = bbp_get_topic_id( $r['topic_id'] );
		$forum_id = bbp_get_forum_id( $r['forum_id'] );
		if ( empty( $user_id ) || ( empty( $topic_id ) && empty( $forum_id ) ) ) {
			return false;
		}

		// No link if you can't edit yourself
		if ( ! current_user_can( 'edit_user', (int) $user_id ) ) {
			return false;
		}

		// Check if viewing a single forum
		if ( empty( $topic_id ) && ! empty( $forum_id ) ) {

			// Decide which link to show
			$is_subscribed = bbp_is_user_subscribed_to_forum( $user_id, $forum_id );
			if ( ! empty( $is_subscribed ) ) {
				$text       = $r['unsubscribe'];
				$query_args = array( 'action' => 'bbp_unsubscribe', 'forum_id' => $forum_id );
			} else {
				$text       = $r['subscribe'];
				$query_args = array( 'action' => 'bbp_subscribe',   'forum_id' => $forum_id );
			}

			// Create the link based where the user is and if the user is
			// subscribed already
			if ( bbp_is_subscriptions() ) {
				$permalink = bbp_get_subscriptions_permalink( $user_id );
			} elseif ( bbp_is_single_forum() || bbp_is_single_reply() ) {
				$permalink = bbp_get_forum_permalink( $forum_id );
			} else {
				$permalink = get_permalink();
			}

			$url  = esc_url( wp_nonce_url( add_query_arg( $query_args, $permalink ), 'toggle-subscription_' . $forum_id ) );
			$sub  = $is_subscribed ? ' class="is-subscribed"' : '';
			$html = sprintf( '%s<span id="subscribe-%d"  %s><a href="%s" class="subscription-toggle" data-forum="%d">%s</a></span>%s', $r['before'], $forum_id, $sub, $url, $forum_id, $text, $r['after'] );

			// Initial output is wrapped in a span, ajax output is hooked to this
			if ( !empty( $wrap ) ) {
				$html = '<span id="subscription-toggle">' . $html . '</span>';
			}

		} else {

			// Decide which link to show
			$is_subscribed = bbp_is_user_subscribed_to_topic( $user_id, $topic_id );
			if ( ! empty( $is_subscribed ) ) {
				$text       = $r['unsubscribe'];
				$query_args = array( 'action' => 'bbp_unsubscribe', 'topic_id' => $topic_id );
			} else {
				$text       = $r['subscribe'];
				$query_args = array( 'action' => 'bbp_subscribe',   'topic_id' => $topic_id );
			}

			// Create the link based where the user is and if the user is
			// subscribed already
			if ( bbp_is_subscriptions() ) {
				$permalink = bbp_get_subscriptions_permalink( $user_id );
			} elseif ( bbp_is_single_topic() || bbp_is_single_reply() ) {
				$permalink = bbp_get_topic_permalink( $topic_id );
			} else {
				$permalink = get_permalink();
			}

			$url  = esc_url( wp_nonce_url( add_query_arg( $query_args, $permalink ), 'toggle-subscription_' . $topic_id ) );
			$sub  = $is_subscribed ? ' class="is-subscribed"' : '';
			$html = sprintf( '%s<span id="subscribe-%d"  %s><a href="%s" class="subscription-toggle" data-topic="%d">%s</a></span>%s', $r['before'], $topic_id, $sub, $url, $topic_id, $text, $r['after'] );

			// Initial output is wrapped in a span, ajax output is hooked to this
			if ( !empty( $wrap ) ) {
				$html = '<span id="subscription-toggle">' . $html . '</span>';
			}
		}

		// Return the link
		return apply_filters( 'bbp_get_user_subscribe_link', $html, $r, $user_id, $topic_id );
	}


/** Edit User *****************************************************************/

/**
 * Edit profile success message
 *
 * @since bbPress (r2688)
 *
 * @uses bbp_is_single_user() To check if it's the profile page
 * @uses bbp_is_single_user_edit() To check if it's the profile edit page
 */
function bbp_notice_edit_user_success() {
	if ( isset( $_GET['updated'] ) && ( bbp_is_single_user() || bbp_is_single_user_edit() ) ) : ?>

	<div class="bbp-template-notice updated">
		<p><?php esc_html_e( 'User updated.', 'bbpress' ); ?></p>
	</div>

	<?php endif;
}

/**
 * Super admin privileges notice
 *
 * @since bbPress (r2688)
 *
 * @uses is_multisite() To check if the blog is multisite
 * @uses bbp_is_single_user() To check if it's the profile page
 * @uses bbp_is_single_user_edit() To check if it's the profile edit page
 * @uses current_user_can() To check if the current user can manage network
 *                           options
 * @uses bbp_get_displayed_user_id() To get the displayed user id
 * @uses is_super_admin() To check if the user is super admin
 * @uses bbp_is_user_home() To check if it's the user home
 * @uses bbp_is_user_home_edit() To check if it's the user home edit
 */
function bbp_notice_edit_user_is_super_admin() {
	if ( is_multisite() && ( bbp_is_single_user() || bbp_is_single_user_edit() ) && current_user_can( 'manage_network_options' ) && is_super_admin( bbp_get_displayed_user_id() ) ) : ?>

	<div class="bbp-template-notice important">
		<p><?php bbp_is_user_home() || bbp_is_user_home_edit() ? esc_html_e( 'You have super admin privileges.', 'bbpress' ) : esc_html_e( 'This user has super admin privileges.', 'bbpress' ); ?></p>
	</div>

<?php endif;
}

/**
 * Drop down for selecting the user's display name
 *
 * @since bbPress (r2688)
 */
function bbp_edit_user_display_name() {
	$bbp            = bbpress();
	$public_display = array();
	$public_display['display_username'] = $bbp->displayed_user->user_login;

	if ( !empty( $bbp->displayed_user->nickname ) )
		$public_display['display_nickname']  = $bbp->displayed_user->nickname;

	if ( !empty( $bbp->displayed_user->first_name ) )
		$public_display['display_firstname'] = $bbp->displayed_user->first_name;

	if ( !empty( $bbp->displayed_user->last_name ) )
		$public_display['display_lastname']  = $bbp->displayed_user->last_name;

	if ( !empty( $bbp->displayed_user->first_name ) && !empty( $bbp->displayed_user->last_name ) ) {
		$public_display['display_firstlast'] = $bbp->displayed_user->first_name . ' ' . $bbp->displayed_user->last_name;
		$public_display['display_lastfirst'] = $bbp->displayed_user->last_name  . ' ' . $bbp->displayed_user->first_name;
	}

	if ( !in_array( $bbp->displayed_user->display_name, $public_display ) ) // Only add this if it isn't duplicated elsewhere
		$public_display = array( 'display_displayname' => $bbp->displayed_user->display_name ) + $public_display;

	$public_display = array_map( 'trim', $public_display );
	$public_display = array_unique( $public_display ); ?>

	<select name="display_name" id="display_name">

	<?php foreach ( $public_display as $id => $item ) : ?>

		<option id="<?php echo $id; ?>" value="<?php echo esc_attr( $item ); ?>"<?php selected( $bbp->displayed_user->display_name, $item ); ?>><?php echo $item; ?></option>

	<?php endforeach; ?>

	</select>

<?php
}

/**
 * Output blog role selector (for user edit)
 *
 * @since bbPress (r2688)
 */
function bbp_edit_user_blog_role() {

	// Return if no user is being edited
	if ( ! bbp_is_single_user_edit() )
		return;

	// Get users current blog role
	$user_role  = bbp_get_user_blog_role( bbp_get_displayed_user_id() );

	// Get the blog roles
	$blog_roles = bbp_get_blog_roles(); ?>

	<select name="role" id="role">
		<option value=""><?php esc_html_e( '&mdash; No role for this site &mdash;', 'bbpress' ); ?></option>

		<?php foreach ( $blog_roles as $role => $details ) : ?>

			<option <?php selected( $user_role, $role ); ?> value="<?php echo esc_attr( $role ); ?>"><?php echo translate_user_role( $details['name'] ); ?></option>

		<?php endforeach; ?>

	</select>

	<?php
}

/**
 * Output forum role selector (for user edit)
 *
 * @since bbPress (r4284)
 */
function bbp_edit_user_forums_role() {

	// Return if no user is being edited
	if ( ! bbp_is_single_user_edit() )
		return;

	// Get the user's current forum role
	$user_role     = bbp_get_user_role( bbp_get_displayed_user_id() );

	// Get the folum roles
	$dynamic_roles = bbp_get_dynamic_roles();

	// Only keymasters can set other keymasters
	if ( ! bbp_is_user_keymaster() )
		unset( $dynamic_roles[ bbp_get_keymaster_role() ] ); ?>

	<select name="bbp-forums-role" id="bbp-forums-role">
		<option value=""><?php esc_html_e( '&mdash; No role for these forums &mdash;', 'bbpress' ); ?></option>

		<?php foreach ( $dynamic_roles as $role => $details ) : ?>

			<option <?php selected( $user_role, $role ); ?> value="<?php echo esc_attr( $role ); ?>"><?php echo translate_user_role( $details['name'] ); ?></option>

		<?php endforeach; ?>

	</select>

	<?php
}

/**
 * Return user contact methods Selectbox
 *
 * @since bbPress (r2688)
 *
 * @uses _wp_get_user_contactmethods() To get the contact methods
 * @uses apply_filters() Calls 'bbp_edit_user_contact_methods' with the methods
 * @return string User contact methods
 */
function bbp_edit_user_contact_methods() {

	// Get the core WordPress contact methods
	$contact_methods = _wp_get_user_contactmethods( bbpress()->displayed_user );

	return apply_filters( 'bbp_edit_user_contact_methods', $contact_methods );
}

/** Topics Created ************************************************************/

/**
 * Output the link to the user's topics
 *
 * @since bbPress (r4225)
 *
 * @param int $user_id Optional. User id
 * @uses bbp_get_favorites_permalink() To get the favorites permalink
 */
function bbp_user_topics_created_url( $user_id = 0 ) {
	echo esc_url( bbp_get_user_topics_created_url( $user_id ) );
}
	/**
	 * Return the link to the user's topics
	 *
	 * @since bbPress (r4225)
	 *
	 * @param int $user_id Optional. User id
	 * @uses bbp_get_user_profile_url() To get the user profile url
	 * @uses apply_filters() Calls 'bbp_get_favorites_permalink' with the
	 *                        user profile url and user id
	 * @return string Permanent link to user profile page
	 */
	function bbp_get_user_topics_created_url( $user_id = 0 ) {
		global $wp_rewrite;

		// Use displayed user ID if there is one, and one isn't requested
		$user_id = bbp_get_user_id( $user_id );
		if ( empty( $user_id ) )
			return false;

		// Allow early overriding of the profile URL to cut down on processing
		$early_url = apply_filters( 'bbp_pre_get_user_topics_created_url', (int) $user_id );
		if ( is_string( $early_url ) )
			return $early_url;

		// Pretty permalinks
		if ( $wp_rewrite->using_permalinks() ) {
			$url  = $wp_rewrite->root . bbp_get_user_slug() . '/%' . bbp_get_user_rewrite_id() . '%/' . bbp_get_topic_archive_slug();
			$user = get_userdata( $user_id );
			if ( ! empty( $user->user_nicename ) ) {
				$user_nicename = $user->user_nicename;
			} else {
				$user_nicename = $user->user_login;
			}
			$url = str_replace( '%' . bbp_get_user_rewrite_id() . '%', $user_nicename, $url );
			$url = home_url( user_trailingslashit( $url ) );

		// Unpretty permalinks
		} else {
			$url = add_query_arg( array(
				bbp_get_user_rewrite_id()        => $user_id,
				bbp_get_user_topics_rewrite_id() => '1',
			), home_url( '/' ) );
		}

		return apply_filters( 'bbp_get_user_topics_created_url', $url, $user_id );
	}

/** Topics Created ************************************************************/

/**
 * Output the link to the user's replies
 *
 * @since bbPress (r4225)
 *
 * @param int $user_id Optional. User id
 * @uses bbp_get_favorites_permalink() To get the favorites permalink
 */
function bbp_user_replies_created_url( $user_id = 0 ) {
	echo esc_url( bbp_get_user_replies_created_url( $user_id ) );
}
	/**
	 * Return the link to the user's replies
	 *
	 * @since bbPress (r4225)
	 *
	 * @param int $user_id Optional. User id
	 * @uses bbp_get_user_profile_url() To get the user profile url
	 * @uses apply_filters() Calls 'bbp_get_favorites_permalink' with the
	 *                        user profile url and user id
	 * @return string Permanent link to user profile page
	 */
	function bbp_get_user_replies_created_url( $user_id = 0 ) {
		global $wp_rewrite;

		// Use displayed user ID if there is one, and one isn't requested
		$user_id = bbp_get_user_id( $user_id );
		if ( empty( $user_id ) )
			return false;

		// Allow early overriding of the profile URL to cut down on processing
		$early_url = apply_filters( 'bbp_pre_get_user_replies_created_url', (int) $user_id );
		if ( is_string( $early_url ) )
			return $early_url;

		// Pretty permalinks
		if ( $wp_rewrite->using_permalinks() ) {
			$url  = $wp_rewrite->root . bbp_get_user_slug() . '/%' . bbp_get_user_rewrite_id() . '%/' . bbp_get_reply_archive_slug();
			$user = get_userdata( $user_id );
			if ( ! empty( $user->user_nicename ) ) {
				$user_nicename = $user->user_nicename;
			} else {
				$user_nicename = $user->user_login;
			}
			$url = str_replace( '%' . bbp_get_user_rewrite_id() . '%', $user_nicename, $url );
			$url = home_url( user_trailingslashit( $url ) );

		// Unpretty permalinks
		} else {
			$url = add_query_arg( array(
				bbp_get_user_rewrite_id()         => $user_id,
				bbp_get_user_replies_rewrite_id() => '1',
			), home_url( '/' ) );
		}

		return apply_filters( 'bbp_get_user_replies_created_url', $url, $user_id );
	}

/** Login *********************************************************************/

/**
 * Handle the login and registration template notices
 *
 * @since bbPress (r2970)
 *
 * @uses WP_Error bbPress::errors::add() To add an error or message
 */
function bbp_login_notices() {

	// loggedout was passed
	if ( !empty( $_GET['loggedout'] ) && ( true === $_GET['loggedout'] ) ) {
		bbp_add_error( 'loggedout', __( 'You are now logged out.', 'bbpress' ), 'message' );

	// registration is disabled
	} elseif ( !empty( $_GET['registration'] ) && ( 'disabled' === $_GET['registration'] ) ) {
		bbp_add_error( 'registerdisabled', __( 'New user registration is currently not allowed.', 'bbpress' ) );

	// Prompt user to check their email
	} elseif ( !empty( $_GET['checkemail'] ) && in_array( $_GET['checkemail'], array( 'confirm', 'newpass', 'registered' ) ) ) {

		switch ( $_GET['checkemail'] ) {

			// Email needs confirmation
			case 'confirm' :
				bbp_add_error( 'confirm',    __( 'Check your e-mail for the confirmation link.',     'bbpress' ), 'message' );
				break;

			// User requested a new password
			case 'newpass' :
				bbp_add_error( 'newpass',    __( 'Check your e-mail for your new password.',         'bbpress' ), 'message' );
				break;

			// User is newly registered
			case 'registered' :
				bbp_add_error( 'registered', __( 'Registration complete. Please check your e-mail.', 'bbpress' ), 'message' );
				break;
		}
	}
}

/**
 * Redirect a user back to their profile if they are already logged in.
 *
 * This should be used before {@link get_header()} is called in template files
 * where the user should never have access to the contents of that file.
 *
 * @since bbPress (r2815)
 *
 * @param string $url The URL to redirect to
 * @uses is_user_logged_in() Check if user is logged in
 * @uses wp_safe_redirect() To safely redirect
 * @uses bbp_get_user_profile_url() To get the profile url of the user
 * @uses bbp_get_current_user_id() To get the current user id
 */
function bbp_logged_in_redirect( $url = '' ) {

	// Bail if user is not logged in
	if ( !is_user_logged_in() )
		return;

	// Setup the profile page to redirect to
	$redirect_to = !empty( $url ) ? $url : bbp_get_user_profile_url( bbp_get_current_user_id() );

	// Do a safe redirect and exit
	wp_safe_redirect( $redirect_to );
	exit;
}

/**
 * Output the required hidden fields when logging in
 *
 * @since bbPress (r2815)
 *
 * @uses apply_filters() To allow custom redirection
 * @uses bbp_redirect_to_field() To output the hidden request url field
 * @uses wp_nonce_field() To generate hidden nonce fields
 */
function bbp_user_login_fields() {
?>

		<input type="hidden" name="user-cookie" value="1" />

		<?php

		// Allow custom login redirection
		$redirect_to = apply_filters( 'bbp_user_login_redirect_to', '' );
		bbp_redirect_to_field( $redirect_to );

		// Prevent intention hi-jacking of log-in form
		wp_nonce_field( 'bbp-user-login' );
}

/** Register ******************************************************************/

/**
 * Output the required hidden fields when registering
 *
 * @since bbPress (r2815)
 *
 * @uses add_query_arg() To add query args
 * @uses bbp_login_url() To get the login url
 * @uses apply_filters() To allow custom redirection
 * @uses bbp_redirect_to_field() To output the redirect to field
 * @uses wp_nonce_field() To generate hidden nonce fields
 */
function bbp_user_register_fields() {
?>

		<input type="hidden" name="action"      value="register" />
		<input type="hidden" name="user-cookie" value="1" />

		<?php

		// Allow custom registration redirection
		$redirect_to = apply_filters( 'bbp_user_register_redirect_to', '' );
		bbp_redirect_to_field( add_query_arg( array( 'checkemail' => 'registered' ), $redirect_to ) );

		// Prevent intention hi-jacking of sign-up form
		wp_nonce_field( 'bbp-user-register' );
}

/** Lost Password *************************************************************/

/**
 * Output the required hidden fields when user lost password
 *
 * @since bbPress (r2815)
 *
 * @uses apply_filters() To allow custom redirection
 * @uses bbp_redirect_to_field() Set referer
 * @uses wp_nonce_field() To generate hidden nonce fields
 */
function bbp_user_lost_pass_fields() {
?>

		<input type="hidden" name="user-cookie" value="1" />

		<?php

		// Allow custom lost pass redirection
		$redirect_to = apply_filters( 'bbp_user_lost_pass_redirect_to', get_permalink() );
		bbp_redirect_to_field( add_query_arg( array( 'checkemail' => 'confirm' ), $redirect_to ) );

		// Prevent intention hi-jacking of lost pass form
		wp_nonce_field( 'bbp-user-lost-pass' );
}

/** Author Avatar *************************************************************/

/**
 * Output the author link of a post
 *
 * @since bbPress (r2875)
 *
 * @param mixed $args Optional. If it is an integer, it is used as post id.
 * @uses bbp_get_author_link() To get the post author link
 */
function bbp_author_link( $args = '' ) {
	echo bbp_get_author_link( $args );
}
	/**
	 * Return the author link of the post
	 *
	 * @since bbPress (r2875)
	 *
	 * @param mixed $args Optional. If an integer, it is used as reply id.
	 * @uses bbp_is_topic() To check if it's a topic page
	 * @uses bbp_get_topic_author_link() To get the topic author link
	 * @uses bbp_is_reply() To check if it's a reply page
	 * @uses bbp_get_reply_author_link() To get the reply author link
	 * @uses get_post_field() To get the post author
	 * @uses bbp_is_reply_anonymous() To check if the reply is by an
	 *                                 anonymous user
	 * @uses get_the_author_meta() To get the author name
	 * @uses bbp_get_user_profile_url() To get the author profile url
	 * @uses get_avatar() To get the author avatar
	 * @uses apply_filters() Calls 'bbp_get_reply_author_link' with the
	 *                        author link and args
	 * @return string Author link of reply
	 */
	function bbp_get_author_link( $args = '' ) {

		$post_id = is_numeric( $args ) ? (int) $args : 0;

		// Parse arguments against default values
		$r = bbp_parse_args( $args, array(
			'post_id'    => $post_id,
			'link_title' => '',
			'type'       => 'both',
			'size'       => 80
		), 'get_author_link' );

		// Confirmed topic
		if ( bbp_is_topic( $r['post_id'] ) ) {
			return bbp_get_topic_author_link( $r );

		// Confirmed reply
		} elseif ( bbp_is_reply( $r['post_id'] ) ) {
			return bbp_get_reply_author_link( $r );
		}

		// Get the post author and proceed
		$user_id = get_post_field( 'post_author', $r['post_id'] );

		// Neither a reply nor a topic, so could be a revision
		if ( !empty( $r['post_id'] ) ) {

			// Generate title with the display name of the author
			if ( empty( $r['link_title'] ) ) {
				$r['link_title'] = sprintf( !bbp_is_reply_anonymous( $r['post_id'] ) ? __( 'View %s\'s profile', 'bbpress' ) : __( 'Visit %s\'s website', 'bbpress' ), get_the_author_meta( 'display_name', $user_id ) );
			}

			// Assemble some link bits
			$link_title = !empty( $r['link_title'] ) ? ' title="' . $r['link_title'] . '"' : '';
			$anonymous  = bbp_is_reply_anonymous( $r['post_id'] );

			// Get avatar
			if ( 'avatar' === $r['type'] || 'both' === $r['type'] ) {
				$author_links[] = get_avatar( $user_id, $r['size'] );
			}

			// Get display name
			if ( 'name' === $r['type'] || 'both' === $r['type'] ) {
				$author_links[] = get_the_author_meta( 'display_name', $user_id );
			}

			// Add links if not anonymous
			if ( empty( $anonymous ) && bbp_user_has_profile( $user_id ) ) {
				$author_url = bbp_get_user_profile_url( $user_id );
				foreach ( $author_links as $link_text ) {
					$author_link[] = sprintf( '<a href="%1$s"%2$s>%3$s</a>', $author_url, $link_title, $link_text );
				}
				$author_link = implode( '&nbsp;', $author_link );

			// No links if anonymous
			} else {
				$author_link = implode( '&nbsp;', $author_links );
			}

		// No post so link is empty
		} else {
			$author_link = '';
		}

		return apply_filters( 'bbp_get_author_link', $author_link, $r );
	}

/** Capabilities **************************************************************/

/**
 * Check if the user can access a specific forum
 *
 * @since bbPress (r3127)
 *
 * @uses bbp_get_current_user_id()
 * @uses bbp_get_forum_id()
 * @uses bbp_allow_anonymous()
 * @uses bbp_parse_args()
 * @uses bbp_get_user_id()
 * @uses current_user_can()
 * @uses bbp_is_user_keymaster()
 * @uses bbp_is_forum_public()
 * @uses bbp_is_forum_private()
 * @uses bbp_is_forum_hidden()
 * @uses current_user_can()
 * @uses apply_filters()
 *
 * @return bool
 */
function bbp_user_can_view_forum( $args = '' ) {

	// Parse arguments against default values
	$r = bbp_parse_args( $args, array(
		'user_id'         => bbp_get_current_user_id(),
		'forum_id'        => bbp_get_forum_id(),
		'check_ancestors' => false
	), 'user_can_view_forum' );

	// Validate parsed values
	$user_id  = bbp_get_user_id( $r['user_id'], false, false );
	$forum_id = bbp_get_forum_id( $r['forum_id'] );
	$retval   = false;

	// User is a keymaster
	if ( !empty( $user_id ) && bbp_is_user_keymaster( $user_id ) ) {
		$retval = true;

	// Forum is public, and user can read forums or is not logged in
	} elseif ( bbp_is_forum_public( $forum_id, $r['check_ancestors'] ) ) {
		$retval = true;

	// Forum is private, and user can see it
	} elseif ( bbp_is_forum_private( $forum_id, $r['check_ancestors'] ) && user_can( $user_id, 'read_private_forums' ) ) {
		$retval = true;

	// Forum is hidden, and user can see it
	} elseif ( bbp_is_forum_hidden ( $forum_id, $r['check_ancestors'] ) && user_can( $user_id, 'read_hidden_forums'  ) ) {
		$retval = true;
	}

	return apply_filters( 'bbp_user_can_view_forum', $retval, $forum_id, $user_id );
}

/**
 * Check if the current user can publish topics
 *
 * @since bbPress (r3127)
 *
 * @uses bbp_is_user_keymaster()
 * @uses is_user_logged_in()
 * @uses bbp_allow_anonymous()
 * @uses bbp_is_user_active()
 * @uses current_user_can()
 * @uses apply_filters()
 *
 * @return bool
 */
function bbp_current_user_can_publish_topics() {

	// Users need to earn access
	$retval = false;

	// Always allow keymasters
	if ( bbp_is_user_keymaster() ) {
		$retval = true;

	// Do not allow anonymous if not enabled
	} elseif ( !is_user_logged_in() && bbp_allow_anonymous() ) {
		$retval = true;

	// User is logged in
	} elseif ( current_user_can( 'publish_topics' ) ) {
		$retval = true;
	}

	// Allow access to be filtered
	return (bool) apply_filters( 'bbp_current_user_can_publish_topics', $retval );
}

/**
 * Check if the current user can publish forums
 *
 * @since bbPress (r3549)
 *
 * @uses bbp_is_user_keymaster()
 * @uses bbp_is_user_active()
 * @uses current_user_can()
 * @uses apply_filters()
 *
 * @return bool
 */
function bbp_current_user_can_publish_forums() {

	// Users need to earn access
	$retval = false;

	// Always allow keymasters
	if ( bbp_is_user_keymaster() ) {
		$retval = true;

	// User is logged in
	} elseif ( current_user_can( 'publish_forums' ) ) {
		$retval = true;
	}

	// Allow access to be filtered
	return (bool) apply_filters( 'bbp_current_user_can_publish_forums', $retval );
}

/**
 * Check if the current user can publish replies
 *
 * @since bbPress (r3127)
 *
 * @uses bbp_is_user_keymaster()
 * @uses is_user_logged_in()
 * @uses bbp_allow_anonymous()
 * @uses bbp_is_user_active()
 * @uses current_user_can()
 * @uses apply_filters()
 *
 * @return bool
 */
function bbp_current_user_can_publish_replies() {

	// Users need to earn access
	$retval = false;

	// Always allow keymasters
	if ( bbp_is_user_keymaster() ) {
		$retval = true;

	// Do not allow anonymous if not enabled
	} elseif ( !is_user_logged_in() && bbp_allow_anonymous() ) {
		$retval = true;

	// User is logged in
	} elseif ( current_user_can( 'publish_replies' ) ) {
		$retval = true;
	}

	// Allow access to be filtered
	return (bool) apply_filters( 'bbp_current_user_can_publish_replies', $retval );
}

/** Forms *********************************************************************/

/**
 * The following functions should be turned into mapped meta capabilities in a
 * future version. They exist only to remove complex logistical capability
 * checks from within template parts.
 */

/**
 * Get the forums the current user has the ability to see and post to
 *
 * @since bbPress (r3127)
 *
 * @uses bbp_get_forum_post_type()
 * @uses get_posts()
 *
 * @param type $args
 * @return type
 */
function bbp_get_forums_for_current_user( $args = array() ) {

	// Setup arrays
	$private = $hidden = $post__not_in = array();

	// Private forums
	if ( !current_user_can( 'read_private_forums' ) )
		$private = bbp_get_private_forum_ids();

	// Hidden forums
	if ( !current_user_can( 'read_hidden_forums' ) )
		$hidden  = bbp_get_hidden_forum_ids();

	// Merge private and hidden forums together and remove any empties
	$forum_ids = (array) array_filter( wp_parse_id_list( array_merge( $private, $hidden ) ) );

	// There are forums that need to be ex
	if ( !empty( $forum_ids ) )
		$post__not_in = implode( ',', $forum_ids );

	// Parse arguments against default values
	$r = bbp_parse_args( $args, array(
		'post_type'   => bbp_get_forum_post_type(),
		'post_status' => bbp_get_public_status_id(),
		'numberposts' => -1,
		'exclude'     => $post__not_in
	), 'get_forums_for_current_user' );

	// Get the forums
	$forums = get_posts( $r );

	// No availabe forums
	if ( empty( $forums ) )
		$forums = false;

	return apply_filters( 'bbp_get_forums_for_current_user', $forums );
}

/**
 * Performs a series of checks to ensure the current user can create forums.
 *
 * @since bbPress (r3549)
 *
 * @uses bbp_is_user_keymaster()
 * @uses bbp_is_forum_edit()
 * @uses current_user_can()
 * @uses bbp_get_forum_id()
 *
 * @return bool
 */
function bbp_current_user_can_access_create_forum_form() {

	// Users need to earn access
	$retval = false;

	// Always allow keymasters
	if ( bbp_is_user_keymaster() ) {
		$retval = true;

	// Looking at a single forum & forum is open
	} elseif ( ( is_page() || is_single() ) && bbp_is_forum_open() ) {
		$retval = bbp_current_user_can_publish_forums();

	// User can edit this topic
	} elseif ( bbp_is_forum_edit() ) {
		$retval = current_user_can( 'edit_forum', bbp_get_forum_id() );
	}

	// Allow access to be filtered
	return (bool) apply_filters( 'bbp_current_user_can_access_create_forum_form', (bool) $retval );
}

/**
 * Performs a series of checks to ensure the current user can create topics.
 *
 * @since bbPress (r3127)
 *
 * @uses bbp_is_user_keymaster()
 * @uses bbp_is_topic_edit()
 * @uses current_user_can()
 * @uses bbp_get_topic_id()
 * @uses bbp_allow_anonymous()
 * @uses is_user_logged_in()
 *
 * @return bool
 */
function bbp_current_user_can_access_create_topic_form() {

	// Users need to earn access
	$retval = false;

	// Always allow keymasters
	if ( bbp_is_user_keymaster() ) {
		$retval = true;

	// Looking at a single forum & forum is open
	} elseif ( ( bbp_is_single_forum() || is_page() || is_single() ) && bbp_is_forum_open() ) {
		$retval = bbp_current_user_can_publish_topics();

	// User can edit this topic
	} elseif ( bbp_is_topic_edit() ) {
		$retval = current_user_can( 'edit_topic', bbp_get_topic_id() );
	}

	// Allow access to be filtered
	return (bool) apply_filters( 'bbp_current_user_can_access_create_topic_form', (bool) $retval );
}

/**
 * Performs a series of checks to ensure the current user can create replies.
 *
 * @since bbPress (r3127)
 *
 * @uses bbp_is_user_keymaster()
 * @uses bbp_is_topic_edit()
 * @uses current_user_can()
 * @uses bbp_get_topic_id()
 * @uses bbp_allow_anonymous()
 * @uses is_user_logged_in()
 *
 * @return bool
 */
function bbp_current_user_can_access_create_reply_form() {

	// Users need to earn access
	$retval = false;

	// Always allow keymasters
	if ( bbp_is_user_keymaster() ) {
		$retval = true;

	// Looking at a single topic, topic is open, and forum is open
	} elseif ( ( bbp_is_single_topic() || is_page() || is_single() ) && bbp_is_topic_open() && bbp_is_forum_open() ) {
		$retval = bbp_current_user_can_publish_replies();

	// User can edit this topic
	} elseif ( bbp_is_reply_edit() ) {
		$retval = current_user_can( 'edit_reply', bbp_get_reply_id() );
	}

	// Allow access to be filtered
	return (bool) apply_filters( 'bbp_current_user_can_access_create_reply_form', (bool) $retval );
}

/**
 * Performs a series of checks to ensure the current user should see the
 * anonymous user form fields.
 *
 * @since bbPress (r5119)
 *
 * @uses bbp_is_anonymous()
 * @uses bbp_is_topic_edit()
 * @uses bbp_is_topic_anonymous()
 * @uses bbp_is_reply_edit()
 * @uses bbp_is_reply_anonymous()
 *
 * @return bool
 */
function bbp_current_user_can_access_anonymous_user_form() {

	// Users need to earn access
	$retval = false;

	// User is not logged in, and anonymous posting is allowed
	if ( bbp_is_anonymous() ) {
		$retval = true;

	// User is editing a topic, and topic is authored by anonymous user
	} elseif ( bbp_is_topic_edit() && bbp_is_topic_anonymous() ) {
		$retval = true;

	// User is editing a reply, and reply is authored by anonymous user
	} elseif ( bbp_is_reply_edit() && bbp_is_reply_anonymous() ) {
		$retval = true;
	}

	// Allow access to be filtered
	return (bool) apply_filters( 'bbp_current_user_can_access_anonymous_user_form', (bool) $retval );
}
