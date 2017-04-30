<?php

/**
 * bbPress Reply Template Tags
 *
 * @package bbPress
 * @subpackage TemplateTags
 */

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

/** Post Type *****************************************************************/

/**
 * Return the unique id of the custom post type for replies
 *
 * @since bbPress (r2857)
 *
 * @uses bbp_get_reply_post_type() To get the reply post type
 */
function bbp_reply_post_type() {
	echo bbp_get_reply_post_type();
}
	/**
	 * Return the unique id of the custom post type for replies
	 *
	 * @since bbPress (r2857)
	 *
	 * @uses apply_filters() Calls 'bbp_get_forum_post_type' with the forum
	 *                        post type id
	 * @return string The unique reply post type id
	 */
	function bbp_get_reply_post_type() {
		return apply_filters( 'bbp_get_reply_post_type', bbpress()->reply_post_type );
	}

/**
 * Return array of labels used by the reply post type
 *
 * @since bbPress (r5129)
 *
 * @return array
 */
function bbp_get_reply_post_type_labels() {
	return apply_filters( 'bbp_get_reply_post_type_labels', array(
		'name'               => __( 'Replies',                   'bbpress' ),
		'menu_name'          => __( 'Replies',                   'bbpress' ),
		'singular_name'      => __( 'Reply',                     'bbpress' ),
		'all_items'          => __( 'All Replies',               'bbpress' ),
		'add_new'            => __( 'New Reply',                 'bbpress' ),
		'add_new_item'       => __( 'Create New Reply',          'bbpress' ),
		'edit'               => __( 'Edit',                      'bbpress' ),
		'edit_item'          => __( 'Edit Reply',                'bbpress' ),
		'new_item'           => __( 'New Reply',                 'bbpress' ),
		'view'               => __( 'View Reply',                'bbpress' ),
		'view_item'          => __( 'View Reply',                'bbpress' ),
		'search_items'       => __( 'Search Replies',            'bbpress' ),
		'not_found'          => __( 'No replies found',          'bbpress' ),
		'not_found_in_trash' => __( 'No replies found in Trash', 'bbpress' ),
		'parent_item_colon'  => __( 'Topic:',                    'bbpress' )
	) );
}

/**
 * Return array of reply post type rewrite settings
 *
 * @since bbPress (r5129)
 *
 * @return array
 */
function bbp_get_reply_post_type_rewrite() {
	return apply_filters( 'bbp_get_reply_post_type_rewrite', array(
		'slug'       => bbp_get_reply_slug(),
		'with_front' => false
	) );
}

/**
 * Return array of features the reply post type supports
 *
 * @since bbPress (rx5129)
 *
 * @return array
 */
function bbp_get_reply_post_type_supports() {
	return apply_filters( 'bbp_get_reply_post_type_supports', array(
		'title',
		'editor',
		'revisions'
	) );
}

/** Reply Loop Functions ******************************************************/

/**
 * The main reply loop. WordPress makes this easy for us
 *
 * @since bbPress (r2553)
 *
 * @param mixed $args All the arguments supported by {@link WP_Query}
 * @uses bbp_show_lead_topic() Are we showing the topic as a lead?
 * @uses bbp_get_topic_id() To get the topic id
 * @uses bbp_get_reply_post_type() To get the reply post type
 * @uses bbp_get_topic_post_type() To get the topic post type
 * @uses get_option() To get the replies per page option
 * @uses bbp_get_paged() To get the current page value
 * @uses current_user_can() To check if the current user is capable of editing
 *                           others' replies
 * @uses WP_Query To make query and get the replies
 * @uses WP_Rewrite::using_permalinks() To check if the blog is using permalinks
 * @uses get_permalink() To get the permalink
 * @uses add_query_arg() To add custom args to the url
 * @uses apply_filters() Calls 'bbp_replies_pagination' with the pagination args
 * @uses paginate_links() To paginate the links
 * @uses apply_filters() Calls 'bbp_has_replies' with
 *                        bbPres::reply_query::have_posts()
 *                        and bbPres::reply_query
 * @return object Multidimensional array of reply information
 */
function bbp_has_replies( $args = '' ) {
	global $wp_rewrite;

	/** Defaults **************************************************************/

	// Other defaults
	$default_reply_search   = !empty( $_REQUEST['rs'] ) ? $_REQUEST['rs']    : false;
	$default_post_parent    = ( bbp_is_single_topic() ) ? bbp_get_topic_id() : 'any';
	$default_post_type      = ( bbp_is_single_topic() && bbp_show_lead_topic() ) ? bbp_get_reply_post_type() : array( bbp_get_topic_post_type(), bbp_get_reply_post_type() );
	$default_thread_replies = (bool) ( bbp_is_single_topic() && bbp_thread_replies() );

	// Default query args
	$default = array(
		'post_type'           => $default_post_type,         // Only replies
		'post_parent'         => $default_post_parent,       // Of this topic
		'posts_per_page'      => bbp_get_replies_per_page(), // This many
		'paged'               => bbp_get_paged(),            // On this page
		'orderby'             => 'date',                     // Sorted by date
		'order'               => 'ASC',                      // Oldest to newest
		'hierarchical'        => $default_thread_replies,    // Hierarchical replies
		'ignore_sticky_posts' => true,                       // Stickies not supported
		's'                   => $default_reply_search,      // Maybe search
	);

	// What are the default allowed statuses (based on user caps)
	if ( bbp_get_view_all() ) {

		// Default view=all statuses
		$post_statuses = array(
			bbp_get_public_status_id(),
			bbp_get_closed_status_id(),
			bbp_get_spam_status_id(),
			bbp_get_trash_status_id()
		);

		// Add support for private status
		if ( current_user_can( 'read_private_replies' ) ) {
			$post_statuses[] = bbp_get_private_status_id();
		}

		// Join post statuses together
		$default['post_status'] = implode( ',', $post_statuses );

	// Lean on the 'perm' query var value of 'readable' to provide statuses
	} else {
		$default['perm'] = 'readable';
	}

	/** Setup *****************************************************************/

	// Parse arguments against default values
	$r = bbp_parse_args( $args, $default, 'has_replies' );

	// Set posts_per_page value if replies are threaded
	$replies_per_page = $r['posts_per_page'];
	if ( true === $r['hierarchical'] ) {
		$r['posts_per_page'] = -1;
	}

	// Get bbPress
	$bbp = bbpress();

	// Call the query
	$bbp->reply_query = new WP_Query( $r );

	// Add pagination values to query object
	$bbp->reply_query->posts_per_page = $replies_per_page;
	$bbp->reply_query->paged          = $r['paged'];

	// Never home, regardless of what parse_query says
	$bbp->reply_query->is_home        = false;

	// Reset is_single if single topic
	if ( bbp_is_single_topic() ) {
		$bbp->reply_query->is_single = true;
	}

	// Only add reply to if query returned results
	if ( (int) $bbp->reply_query->found_posts ) {

		// Get reply to for each reply
		foreach ( $bbp->reply_query->posts as &$post ) {

			// Check for reply post type
			if ( bbp_get_reply_post_type() === $post->post_type ) {
				$reply_to = bbp_get_reply_to( $post->ID );

				// Make sure it's a reply to a reply
				if ( empty( $reply_to ) || ( bbp_get_reply_topic_id( $post->ID ) === $reply_to ) ) {
					$reply_to = 0;
				}

				// Add reply_to to the post object so we can walk it later
				$post->reply_to = $reply_to;
			}
		}
	}

	// Only add pagination if query returned results
	if ( (int) $bbp->reply_query->found_posts && (int) $bbp->reply_query->posts_per_page ) {

		// If pretty permalinks are enabled, make our pagination pretty
		if ( $wp_rewrite->using_permalinks() ) {

			// User's replies
			if ( bbp_is_single_user_replies() ) {
				$base = bbp_get_user_replies_created_url( bbp_get_displayed_user_id() );

			// Root profile page
			} elseif ( bbp_is_single_user() ) {
				$base = bbp_get_user_profile_url( bbp_get_displayed_user_id() );

			// Page or single post
			} elseif ( is_page() || is_single() ) {
				$base = get_permalink();

			// Single topic
			} else {
				$base = get_permalink( bbp_get_topic_id() );
			}

			$base = trailingslashit( $base ) . user_trailingslashit( $wp_rewrite->pagination_base . '/%#%/' );

		// Unpretty permalinks
		} else {
			$base = add_query_arg( 'paged', '%#%' );
		}

		// Figure out total pages
		if ( true === $r['hierarchical'] ) {
			$walker      = new BBP_Walker_Reply;
			$total_pages = ceil( (int) $walker->get_number_of_root_elements( $bbp->reply_query->posts ) / (int) $replies_per_page );
		} else {
			$total_pages = ceil( (int) $bbp->reply_query->found_posts / (int) $replies_per_page );

			// Add pagination to query object
			$bbp->reply_query->pagination_links = paginate_links( apply_filters( 'bbp_replies_pagination', array(
				'base'      => $base,
				'format'    => '',
				'total'     => $total_pages,
				'current'   => (int) $bbp->reply_query->paged,
				'prev_text' => is_rtl() ? '&rarr;' : '&larr;',
				'next_text' => is_rtl() ? '&larr;' : '&rarr;',
				'mid_size'  => 1,
				'add_args'  => ( bbp_get_view_all() ) ? array( 'view' => 'all' ) : false
			) ) );

			// Remove first page from pagination
			if ( $wp_rewrite->using_permalinks() ) {
				$bbp->reply_query->pagination_links = str_replace( $wp_rewrite->pagination_base . '/1/', '', $bbp->reply_query->pagination_links );
			} else {
				$bbp->reply_query->pagination_links = str_replace( '&#038;paged=1', '', $bbp->reply_query->pagination_links );
			}
		}
	}

	// Return object
	return apply_filters( 'bbp_has_replies', $bbp->reply_query->have_posts(), $bbp->reply_query );
}

/**
 * Whether there are more replies available in the loop
 *
 * @since bbPress (r2553)
 *
 * @uses WP_Query bbPress::reply_query::have_posts() To check if there are more
 *                                                    replies available
 * @return object Replies information
 */
function bbp_replies() {

	// Put into variable to check against next
	$have_posts = bbpress()->reply_query->have_posts();

	// Reset the post data when finished
	if ( empty( $have_posts ) )
		wp_reset_postdata();

	return $have_posts;
}

/**
 * Loads up the current reply in the loop
 *
 * @since bbPress (r2553)
 *
 * @uses WP_Query bbPress::reply_query::the_post() To get the current reply
 * @return object Reply information
 */
function bbp_the_reply() {
	return bbpress()->reply_query->the_post();
}

/**
 * Output reply id
 *
 * @since bbPress (r2553)
 *
 * @param $reply_id Optional. Used to check emptiness
 * @uses bbp_get_reply_id() To get the reply id
 */
function bbp_reply_id( $reply_id = 0 ) {
	echo bbp_get_reply_id( $reply_id );
}
	/**
	 * Return the id of the reply in a replies loop
	 *
	 * @since bbPress (r2553)
	 *
	 * @param $reply_id Optional. Used to check emptiness
	 * @uses bbPress::reply_query::post::ID To get the reply id
	 * @uses bbp_is_reply() To check if the search result is a reply
	 * @uses bbp_is_single_reply() To check if it's a reply page
	 * @uses bbp_is_reply_edit() To check if it's a reply edit page
	 * @uses get_post_field() To get the post's post type
	 * @uses WP_Query::post::ID To get the reply id
	 * @uses bbp_get_reply_post_type() To get the reply post type
	 * @uses apply_filters() Calls 'bbp_get_reply_id' with the reply id and
	 *                        supplied reply id
	 * @return int The reply id
	 */
	function bbp_get_reply_id( $reply_id = 0 ) {
		global $wp_query;

		$bbp = bbpress();

		// Easy empty checking
		if ( !empty( $reply_id ) && is_numeric( $reply_id ) ) {
			$bbp_reply_id = $reply_id;

		// Currently inside a replies loop
		} elseif ( !empty( $bbp->reply_query->in_the_loop ) && isset( $bbp->reply_query->post->ID ) ) {
			$bbp_reply_id = $bbp->reply_query->post->ID;

		// Currently inside a search loop
		} elseif ( !empty( $bbp->search_query->in_the_loop ) && isset( $bbp->search_query->post->ID ) && bbp_is_reply( $bbp->search_query->post->ID ) ) {
			$bbp_reply_id = $bbp->search_query->post->ID;

		// Currently viewing a forum
		} elseif ( ( bbp_is_single_reply() || bbp_is_reply_edit() ) && !empty( $bbp->current_reply_id ) ) {
			$bbp_reply_id = $bbp->current_reply_id;

		// Currently viewing a reply
		} elseif ( ( bbp_is_single_reply() || bbp_is_reply_edit() ) && isset( $wp_query->post->ID ) ) {
			$bbp_reply_id = $wp_query->post->ID;

		// Fallback
		} else {
			$bbp_reply_id = 0;
		}

		return (int) apply_filters( 'bbp_get_reply_id', $bbp_reply_id, $reply_id );
	}

/**
 * Gets a reply
 *
 * @since bbPress (r2787)
 *
 * @param int|object $reply reply id or reply object
 * @param string $output Optional. OBJECT, ARRAY_A, or ARRAY_N. Default = OBJECT
 * @param string $filter Optional Sanitation filter. See {@link sanitize_post()}
 * @uses get_post() To get the reply
 * @uses bbp_get_reply_post_type() To get the reply post type
 * @uses apply_filters() Calls 'bbp_get_reply' with the reply, output type and
 *                        sanitation filter
 * @return mixed Null if error or reply (in specified form) if success
 */
function bbp_get_reply( $reply, $output = OBJECT, $filter = 'raw' ) {
	if ( empty( $reply ) || is_numeric( $reply ) )
		$reply = bbp_get_reply_id( $reply );

	$reply = get_post( $reply, OBJECT, $filter );
	if ( empty( $reply ) )
		return $reply;

	if ( $reply->post_type !== bbp_get_reply_post_type() )
		return null;

	if ( $output === OBJECT ) {
		return $reply;

	} elseif ( $output === ARRAY_A ) {
		$_reply = get_object_vars( $reply );
		return $_reply;

	} elseif ( $output === ARRAY_N ) {
		$_reply = array_values( get_object_vars( $reply ) );
		return $_reply;

	}

	return apply_filters( 'bbp_get_reply', $reply, $output, $filter );
}

/**
 * Output the link to the reply in the reply loop
 *
 * @since bbPress (r2553)
 *
 * @param int $reply_id Optional. Reply id
 * @uses bbp_get_reply_permalink() To get the reply permalink
 */
function bbp_reply_permalink( $reply_id = 0 ) {
	echo esc_url( bbp_get_reply_permalink( $reply_id ) );
}
	/**
	 * Return the link to the reply
	 *
	 * @since bbPress (r2553)
	 *
	 * @param int $reply_id Optional. Reply id
	 * @uses bbp_get_reply_id() To get the reply id
	 * @uses get_permalink() To get the permalink of the reply
	 * @uses apply_filters() Calls 'bbp_get_reply_permalink' with the link
	 *                        and reply id
	 * @return string Permanent link to reply
	 */
	function bbp_get_reply_permalink( $reply_id = 0 ) {
		$reply_id = bbp_get_reply_id( $reply_id );

		return apply_filters( 'bbp_get_reply_permalink', get_permalink( $reply_id ), $reply_id );
	}
/**
 * Output the paginated url to the reply in the reply loop
 *
 * @since bbPress (r2679)
 *
 * @param int $reply_id Optional. Reply id
 * @uses bbp_get_reply_url() To get the reply url
 */
function bbp_reply_url( $reply_id = 0 ) {
	echo esc_url( bbp_get_reply_url( $reply_id ) );
}
	/**
	 * Return the paginated url to the reply in the reply loop
	 *
	 * @since bbPress (r2679)
	 *
	 * @param int $reply_id Optional. Reply id
	 * @param $string $redirect_to Optional. Pass a redirect value for use with
	 *                              shortcodes and other fun things.
	 * @uses bbp_get_reply_id() To get the reply id
	 * @uses bbp_get_reply_topic_id() To get the reply topic id
	 * @uses bbp_get_topic_permalink() To get the topic permalink
	 * @uses bbp_get_reply_position() To get the reply position
	 * @uses get_option() To get the replies per page option
	 * @uses WP_Rewrite::using_permalinks() To check if the blog uses
	 *                                       permalinks
	 * @uses add_query_arg() To add custom args to the url
	 * @uses apply_filters() Calls 'bbp_get_reply_url' with the reply url,
	 *                        reply id and bool count hidden
	 * @return string Link to reply relative to paginated topic
	 */
	function bbp_get_reply_url( $reply_id = 0, $redirect_to = '' ) {

		// Set needed variables
		$reply_id   = bbp_get_reply_id      ( $reply_id );
		$topic_id   = bbp_get_reply_topic_id( $reply_id );

		// Hierarchical reply page
		if ( bbp_thread_replies() ) {
			$reply_page = 1;

		// Standard reply page
		} else {
			$reply_page = ceil( (int) bbp_get_reply_position( $reply_id, $topic_id ) / (int) bbp_get_replies_per_page() );
		}

		$reply_hash = '#post-' . $reply_id;
		$topic_link = bbp_get_topic_permalink( $topic_id, $redirect_to );
		$topic_url  = remove_query_arg( 'view', $topic_link );

		// Don't include pagination if on first page
		if ( 1 >= $reply_page ) {
			$url = trailingslashit( $topic_url ) . $reply_hash;

		// Include pagination
		} else {
			global $wp_rewrite;

			// Pretty permalinks
			if ( $wp_rewrite->using_permalinks() ) {
				$url = trailingslashit( $topic_url ) . trailingslashit( $wp_rewrite->pagination_base ) . trailingslashit( $reply_page ) . $reply_hash;

			// Yucky links
			} else {
				$url = add_query_arg( 'paged', $reply_page, $topic_url ) . $reply_hash;
			}
		}

		// Add topic view query arg back to end if it is set
		if ( bbp_get_view_all() )
			$url = bbp_add_view_all( $url );

		return apply_filters( 'bbp_get_reply_url', $url, $reply_id, $redirect_to );
	}

/**
 * Output the title of the reply
 *
 * @since bbPress (r2553)
 *
 * @param int $reply_id Optional. Reply id
 * @uses bbp_get_reply_title() To get the reply title
 */
function bbp_reply_title( $reply_id = 0 ) {
	echo bbp_get_reply_title( $reply_id );
}

	/**
	 * Return the title of the reply
	 *
	 * @since bbPress (r2553)
	 *
	 * @param int $reply_id Optional. Reply id
	 * @uses bbp_get_reply_id() To get the reply id
	 * @uses get_the_title() To get the reply title
	 * @uses apply_filters() Calls 'bbp_get_reply_title' with the title and
	 *                        reply id
	 * @return string Title of reply
	 */
	function bbp_get_reply_title( $reply_id = 0 ) {
		$reply_id = bbp_get_reply_id( $reply_id );

		return apply_filters( 'bbp_get_reply_title', get_the_title( $reply_id ), $reply_id );
	}

	/**
	 * Get empty reply title fallback.
	 *
	 * @since bbPress (r5177)
	 *
	 * @param string $reply_title Required. Reply Title
	 * @param int $reply_id Required. Reply ID
	 * @uses bbp_get_reply_topic_title() To get the reply topic title
	 * @uses apply_filters() Calls 'bbp_get_reply_title_fallback' with the title and reply ID
	 * @return string Title of reply
	 */
	function bbp_get_reply_title_fallback( $post_title = '', $post_id = 0 ) {

		// Bail if title not empty, or post is not a reply
		if ( ! empty( $post_title ) || ! bbp_is_reply( $post_id ) ) {
			return $post_title;
		}

		// Get reply topic title.
		$topic_title = bbp_get_reply_topic_title( $post_id );

		// Get empty reply title fallback.
		$reply_title = sprintf( __( 'Reply To: %s', 'bbpress' ), $topic_title );

		return apply_filters( 'bbp_get_reply_title_fallback', $reply_title, $post_id, $topic_title );
	}

/**
 * Output the content of the reply
 *
 * @since bbPress (r2553)
 *
 * @param int $reply_id Optional. reply id
 * @uses bbp_get_reply_content() To get the reply content
 */
function bbp_reply_content( $reply_id = 0 ) {
	echo bbp_get_reply_content( $reply_id );
}
	/**
	 * Return the content of the reply
	 *
	 * @since bbPress (r2780)
	 *
	 * @param int $reply_id Optional. reply id
	 * @uses bbp_get_reply_id() To get the reply id
	 * @uses post_password_required() To check if the reply requires pass
	 * @uses get_the_password_form() To get the password form
	 * @uses get_post_field() To get the content post field
	 * @uses apply_filters() Calls 'bbp_get_reply_content' with the content
	 *                        and reply id
	 * @return string Content of the reply
	 */
	function bbp_get_reply_content( $reply_id = 0 ) {
		$reply_id = bbp_get_reply_id( $reply_id );

		// Check if password is required
		if ( post_password_required( $reply_id ) )
			return get_the_password_form();

		$content = get_post_field( 'post_content', $reply_id );

		return apply_filters( 'bbp_get_reply_content', $content, $reply_id );
	}

/**
 * Output the excerpt of the reply
 *
 * @since bbPress (r2751)
 *
 * @param int $reply_id Optional. Reply id
 * @param int $length Optional. Length of the excerpt. Defaults to 100 letters
 * @uses bbp_get_reply_excerpt() To get the reply excerpt
 */
function bbp_reply_excerpt( $reply_id = 0, $length = 100 ) {
	echo bbp_get_reply_excerpt( $reply_id, $length );
}
	/**
	 * Return the excerpt of the reply
	 *
	 * @since bbPress (r2751)
	 *
	 * @param int $reply_id Optional. Reply id
	 * @param int $length Optional. Length of the excerpt. Defaults to 100
	 *                     letters
	 * @uses bbp_get_reply_id() To get the reply id
	 * @uses get_post_field() To get the excerpt
	 * @uses bbp_get_reply_content() To get the reply content
	 * @uses apply_filters() Calls 'bbp_get_reply_excerpt' with the excerpt,
	 *                        reply id and length
	 * @return string Reply Excerpt
	 */
	function bbp_get_reply_excerpt( $reply_id = 0, $length = 100 ) {
		$reply_id = bbp_get_reply_id( $reply_id );
		$length   = (int) $length;
		$excerpt  = get_post_field( 'post_excerpt', $reply_id );

		if ( empty( $excerpt ) ) {
			$excerpt = bbp_get_reply_content( $reply_id );
		}

		$excerpt = trim ( strip_tags( $excerpt ) );

		// Multibyte support
		if ( function_exists( 'mb_strlen' ) ) {
			$excerpt_length = mb_strlen( $excerpt );
		} else {
			$excerpt_length = strlen( $excerpt );
		}

		if ( !empty( $length ) && ( $excerpt_length > $length ) ) {
			$excerpt  = substr( $excerpt, 0, $length - 1 );
			$excerpt .= '&hellip;';
		}

		return apply_filters( 'bbp_get_reply_excerpt', $excerpt, $reply_id, $length );
	}

/**
 * Output the post date and time of a reply
 *
 * @since bbPress (r4155)
 *
 * @param int $reply_id Optional. Reply id.
 * @param bool $humanize Optional. Humanize output using time_since
 * @param bool $gmt Optional. Use GMT
 * @uses bbp_get_reply_post_date() to get the output
 */
function bbp_reply_post_date( $reply_id = 0, $humanize = false, $gmt = false ) {
	echo bbp_get_reply_post_date( $reply_id, $humanize, $gmt );
}
	/**
	 * Return the post date and time of a reply
	 *
	 * @since bbPress (r4155)
	 *
	 * @param int $reply_id Optional. Reply id.
	 * @param bool $humanize Optional. Humanize output using time_since
	 * @param bool $gmt Optional. Use GMT
	 * @uses bbp_get_reply_id() To get the reply id
	 * @uses get_post_time() to get the reply post time
	 * @uses bbp_get_time_since() to maybe humanize the reply post time
	 * @return string
	 */
	function bbp_get_reply_post_date( $reply_id = 0, $humanize = false, $gmt = false ) {
		$reply_id = bbp_get_reply_id( $reply_id );

		// 4 days, 4 hours ago
		if ( !empty( $humanize ) ) {
			$gmt_s  = !empty( $gmt ) ? 'G' : 'U';
			$date   = get_post_time( $gmt_s, $gmt, $reply_id );
			$time   = false; // For filter below
			$result = bbp_get_time_since( $date );

		// August 4, 2012 at 2:37 pm
		} else {
			$date   = get_post_time( get_option( 'date_format' ), $gmt, $reply_id, true );
			$time   = get_post_time( get_option( 'time_format' ), $gmt, $reply_id, true );
			$result = sprintf( _x( '%1$s at %2$s', 'date at time', 'bbpress' ), $date, $time );
		}

		return apply_filters( 'bbp_get_reply_post_date', $result, $reply_id, $humanize, $gmt, $date, $time );
	}

/**
 * Append revisions to the reply content
 *
 * @since bbPress (r2782)
 *
 * @param string $content Optional. Content to which we need to append the revisions to
 * @param int $reply_id Optional. Reply id
 * @uses bbp_get_reply_revision_log() To get the reply revision log
 * @uses apply_filters() Calls 'bbp_reply_append_revisions' with the processed
 *                        content, original content and reply id
 * @return string Content with the revisions appended
 */
function bbp_reply_content_append_revisions( $content = '', $reply_id = 0 ) {

	// Bail if in admin or feed
	if ( is_admin() || is_feed() )
		return $content;

	// Validate the ID
	$reply_id = bbp_get_reply_id( $reply_id );

	return apply_filters( 'bbp_reply_append_revisions', $content . bbp_get_reply_revision_log( $reply_id ), $content, $reply_id );
}

/**
 * Output the revision log of the reply
 *
 * @since bbPress (r2782)
 *
 * @param int $reply_id Optional. Reply id
 * @uses bbp_get_reply_revision_log() To get the reply revision log
 */
function bbp_reply_revision_log( $reply_id = 0 ) {
	echo bbp_get_reply_revision_log( $reply_id );
}
	/**
	 * Return the formatted revision log of the reply
	 *
	 * @since bbPress (r2782)
	 *
	 * @param int $reply_id Optional. Reply id
	 * @uses bbp_get_reply_id() To get the reply id
	 * @uses bbp_get_reply_revisions() To get the reply revisions
	 * @uses bbp_get_reply_raw_revision_log() To get the raw revision log
	 * @uses bbp_get_reply_author_display_name() To get the reply author
	 * @uses bbp_get_reply_author_link() To get the reply author link
	 * @uses bbp_convert_date() To convert the date
	 * @uses bbp_get_time_since() To get the time in since format
	 * @uses apply_filters() Calls 'bbp_get_reply_revision_log' with the
	 *                        log and reply id
	 * @return string Revision log of the reply
	 */
	function bbp_get_reply_revision_log( $reply_id = 0 ) {

		// Create necessary variables
		$reply_id = bbp_get_reply_id( $reply_id );

		// Show the topic reply log if this is a topic in a reply loop
		if ( bbp_is_topic( $reply_id ) ) {
			return bbp_get_topic_revision_log( $reply_id );
		}

		// Get the reply revision log (out of post meta
		$revision_log = bbp_get_reply_raw_revision_log( $reply_id );

		// Check reply and revision log exist
		if ( empty( $reply_id ) || empty( $revision_log ) || !is_array( $revision_log ) )
			return false;

		// Get the actual revisions
		$revisions = bbp_get_reply_revisions( $reply_id );
		if ( empty( $revisions ) )
			return false;

		$r = "\n\n" . '<ul id="bbp-reply-revision-log-' . esc_attr( $reply_id ) . '" class="bbp-reply-revision-log">' . "\n\n";

		// Loop through revisions
		foreach ( (array) $revisions as $revision ) {

			if ( empty( $revision_log[$revision->ID] ) ) {
				$author_id = $revision->post_author;
				$reason    = '';
			} else {
				$author_id = $revision_log[$revision->ID]['author'];
				$reason    = $revision_log[$revision->ID]['reason'];
			}

			$author = bbp_get_author_link( array( 'size' => 14, 'link_text' => bbp_get_reply_author_display_name( $revision->ID ), 'post_id' => $revision->ID ) );
			$since  = bbp_get_time_since( bbp_convert_date( $revision->post_modified ) );

			$r .= "\t" . '<li id="bbp-reply-revision-log-' . esc_attr( $reply_id ) . '-item-' . esc_attr( $revision->ID ) . '" class="bbp-reply-revision-log-item">' . "\n";
			if ( !empty( $reason ) ) {
				$r .= "\t\t" . sprintf( esc_html__( 'This reply was modified %1$s by %2$s. Reason: %3$s', 'bbpress' ), esc_html( $since ), $author, esc_html( $reason ) ) . "\n";
			} else {
				$r .= "\t\t" . sprintf( esc_html__( 'This reply was modified %1$s by %2$s.', 'bbpress' ), esc_html( $since ), $author ) . "\n";
			}
			$r .= "\t" . '</li>' . "\n";

		}

		$r .= "\n" . '</ul>' . "\n\n";

		return apply_filters( 'bbp_get_reply_revision_log', $r, $reply_id );
	}
		/**
		 * Return the raw revision log of the reply
		 *
		 * @since bbPress (r2782)
		 *
		 * @param int $reply_id Optional. Reply id
		 * @uses bbp_get_reply_id() To get the reply id
		 * @uses get_post_meta() To get the revision log meta
		 * @uses apply_filters() Calls 'bbp_get_reply_raw_revision_log'
		 *                        with the log and reply id
		 * @return string Raw revision log of the reply
		 */
		function bbp_get_reply_raw_revision_log( $reply_id = 0 ) {
			$reply_id     = bbp_get_reply_id( $reply_id );
			$revision_log = get_post_meta( $reply_id, '_bbp_revision_log', true );
			$revision_log = empty( $revision_log ) ? array() : $revision_log;

			return apply_filters( 'bbp_get_reply_raw_revision_log', $revision_log, $reply_id );
		}

/**
 * Return the revisions of the reply
 *
 * @since bbPress (r2782)
 *
 * @param int $reply_id Optional. Reply id
 * @uses bbp_get_reply_id() To get the reply id
 * @uses wp_get_post_revisions() To get the reply revisions
 * @uses apply_filters() Calls 'bbp_get_reply_revisions'
 *                        with the revisions and reply id
 * @return string reply revisions
 */
function bbp_get_reply_revisions( $reply_id = 0 ) {
	$reply_id  = bbp_get_reply_id( $reply_id );
	$revisions = wp_get_post_revisions( $reply_id, array( 'order' => 'ASC' ) );

	return apply_filters( 'bbp_get_reply_revisions', $revisions, $reply_id );
}

/**
 * Return the revision count of the reply
 *
 * @since bbPress (r2782)
 *
 * @param int $reply_id Optional. Reply id
 * @param boolean $integer Optional. Whether or not to format the result
 * @uses bbp_get_reply_revisions() To get the reply revisions
 * @uses apply_filters() Calls 'bbp_get_reply_revision_count'
 *                        with the revision count and reply id
 * @return string reply revision count
 */
function bbp_get_reply_revision_count( $reply_id = 0, $integer = false ) {
	$count  = (int) count( bbp_get_reply_revisions( $reply_id ) );
	$filter = ( true === $integer ) ? 'bbp_get_reply_revision_count_int' : 'bbp_get_reply_revision_count';

	return apply_filters( $filter, $count, $reply_id );
}

/**
 * Output the status of the reply
 *
 * @since bbPress (r2667)
 *
 * @param int $reply_id Optional. Reply id
 * @uses bbp_get_reply_status() To get the reply status
 */
function bbp_reply_status( $reply_id = 0 ) {
	echo bbp_get_reply_status( $reply_id );
}
	/**
	 * Return the status of the reply
	 *
	 * @since bbPress (r2667)
	 *
	 * @param int $reply_id Optional. Reply id
	 * @uses bbp_get_reply_id() To get the reply id
	 * @uses get_post_status() To get the reply status
	 * @uses apply_filters() Calls 'bbp_get_reply_status' with the reply id
	 * @return string Status of reply
	 */
	function bbp_get_reply_status( $reply_id = 0 ) {
		$reply_id = bbp_get_reply_id( $reply_id );
		return apply_filters( 'bbp_get_reply_status', get_post_status( $reply_id ), $reply_id );
	}

/**
 * Is the reply not spam or deleted?
 *
 * @since bbPress (r3496)
 *
 * @param int $reply_id Optional. Topic id
 * @uses bbp_get_reply_id() To get the reply id
 * @uses bbp_get_reply_status() To get the reply status
 * @return bool True if published, false if not.
 */
function bbp_is_reply_published( $reply_id = 0 ) {
	$reply_status = bbp_get_reply_status( bbp_get_reply_id( $reply_id ) ) === bbp_get_public_status_id();
	return (bool) apply_filters( 'bbp_is_reply_published', (bool) $reply_status, $reply_id );
}

/**
 * Is the reply marked as spam?
 *
 * @since bbPress (r2740)
 *
 * @param int $reply_id Optional. Reply id
 * @uses bbp_get_reply_id() To get the reply id
 * @uses bbp_get_reply_status() To get the reply status
 * @return bool True if spam, false if not.
 */
function bbp_is_reply_spam( $reply_id = 0 ) {
	$reply_status = bbp_get_reply_status( bbp_get_reply_id( $reply_id ) ) === bbp_get_spam_status_id();
	return (bool) apply_filters( 'bbp_is_reply_spam', (bool) $reply_status, $reply_id );
}

/**
 * Is the reply trashed?
 *
 * @since bbPress (r2884)
 *
 * @param int $reply_id Optional. Topic id
 * @uses bbp_get_reply_id() To get the reply id
 * @uses bbp_get_reply_status() To get the reply status
 * @return bool True if spam, false if not.
 */
function bbp_is_reply_trash( $reply_id = 0 ) {
	$reply_status = bbp_get_reply_status( bbp_get_reply_id( $reply_id ) ) === bbp_get_trash_status_id();
	return (bool) apply_filters( 'bbp_is_reply_trash', (bool) $reply_status, $reply_id );
}

/**
 * Is the reply by an anonymous user?
 *
 * @since bbPress (r2753)
 *
 * @param int $reply_id Optional. Reply id
 * @uses bbp_get_reply_id() To get the reply id
 * @uses bbp_get_reply_author_id() To get the reply author id
 * @uses get_post_meta() To get the anonymous name and email metas
 * @return bool True if the post is by an anonymous user, false if not.
 */
function bbp_is_reply_anonymous( $reply_id = 0 ) {
	$reply_id = bbp_get_reply_id( $reply_id );
	$retval   = false;

	if ( !bbp_get_reply_author_id( $reply_id ) )
		$retval = true;

	elseif ( get_post_meta( $reply_id, '_bbp_anonymous_name', true ) )
		$retval = true;

	elseif ( get_post_meta( $reply_id, '_bbp_anonymous_email', true ) )
		$retval = true;

	return (bool) apply_filters( 'bbp_is_reply_anonymous', $retval, $reply_id );
}

/**
 * Deprecated. Use bbp_reply_author_display_name() instead.
 *
 * Output the author of the reply
 *
 * @since bbPress (r2667)
 * @deprecated bbPress (r5119)
 *
 * @param int $reply_id Optional. Reply id
 * @uses bbp_get_reply_author() To get the reply author
 */
function bbp_reply_author( $reply_id = 0 ) {
	echo bbp_get_reply_author( $reply_id );
}
	/**
	 * Deprecated. Use bbp_get_reply_author_display_name() instead.
	 *
	 * Return the author of the reply
	 *
	 * @since bbPress (r2667)
	 * @deprecated bbPress (r5119)
	 *
	 * @param int $reply_id Optional. Reply id
	 * @uses bbp_get_reply_id() To get the reply id
	 * @uses bbp_is_reply_anonymous() To check if the reply is by an
	 *                                 anonymous user
	 * @uses get_the_author_meta() To get the reply author display name
	 * @uses get_post_meta() To get the anonymous poster name
	 * @uses apply_filters() Calls 'bbp_get_reply_author' with the reply
	 *                        author and reply id
	 * @return string Author of reply
	 */
	function bbp_get_reply_author( $reply_id = 0 ) {
		$reply_id = bbp_get_reply_id( $reply_id );

		if ( !bbp_is_reply_anonymous( $reply_id ) ) {
			$author = get_the_author_meta( 'display_name', bbp_get_reply_author_id( $reply_id ) );
		} else {
			$author = get_post_meta( $reply_id, '_bbp_anonymous_name', true );
		}

		return apply_filters( 'bbp_get_reply_author', $author, $reply_id );
	}

/**
 * Output the author ID of the reply
 *
 * @since bbPress (r2667)
 *
 * @param int $reply_id Optional. Reply id
 * @uses bbp_get_reply_author_id() To get the reply author id
 */
function bbp_reply_author_id( $reply_id = 0 ) {
	echo bbp_get_reply_author_id( $reply_id );
}
	/**
	 * Return the author ID of the reply
	 *
	 * @since bbPress (r2667)
	 *
	 * @param int $reply_id Optional. Reply id
	 * @uses bbp_get_reply_id() To get the reply id
	 * @uses get_post_field() To get the reply author id
	 * @uses apply_filters() Calls 'bbp_get_reply_author_id' with the author
	 *                        id and reply id
	 * @return string Author id of reply
	 */
	function bbp_get_reply_author_id( $reply_id = 0 ) {
		$reply_id  = bbp_get_reply_id( $reply_id );
		$author_id = get_post_field( 'post_author', $reply_id );

		return (int) apply_filters( 'bbp_get_reply_author_id', $author_id, $reply_id );
	}

/**
 * Output the author display_name of the reply
 *
 * @since bbPress (r2667)
 *
 * @param int $reply_id Optional. Reply id
 * @uses bbp_get_reply_author_display_name()
 */
function bbp_reply_author_display_name( $reply_id = 0 ) {
	echo bbp_get_reply_author_display_name( $reply_id );
}
	/**
	 * Return the author display_name of the reply
	 *
	 * @since bbPress (r2667)
	 *
	 * @param int $reply_id Optional. Reply id
	 * @uses bbp_get_reply_id() To get the reply id
	 * @uses bbp_is_reply_anonymous() To check if the reply is by an
	 *                                 anonymous user
	 * @uses bbp_get_reply_author_id() To get the reply author id
	 * @uses get_the_author_meta() To get the reply author's display name
	 * @uses get_post_meta() To get the anonymous poster's name
	 * @uses apply_filters() Calls 'bbp_get_reply_author_display_name' with
	 *                        the author display name and reply id
	 * @return string Reply's author's display name
	 */
	function bbp_get_reply_author_display_name( $reply_id = 0 ) {
		$reply_id = bbp_get_reply_id( $reply_id );

		// User is not a guest
		if ( !bbp_is_reply_anonymous( $reply_id ) ) {

			// Get the author ID
			$author_id = bbp_get_reply_author_id( $reply_id );

			// Try to get a display name
			$author_name = get_the_author_meta( 'display_name', $author_id );

			// Fall back to user login
			if ( empty( $author_name ) ) {
				$author_name = get_the_author_meta( 'user_login', $author_id );
			}

		// User does not have an account
		} else {
			$author_name = get_post_meta( $reply_id, '_bbp_anonymous_name', true );
		}

		// If nothing could be found anywhere, use Anonymous
		if ( empty( $author_name ) )
			$author_name = __( 'Anonymous', 'bbpress' );

		// Encode possible UTF8 display names
		if ( seems_utf8( $author_name ) === false )
			$author_name = utf8_encode( $author_name );

		return apply_filters( 'bbp_get_reply_author_display_name', $author_name, $reply_id );
	}

/**
 * Output the author avatar of the reply
 *
 * @since bbPress (r2667)
 *
 * @param int $reply_id Optional. Reply id
 * @param int $size Optional. Size of the avatar. Defaults to 40
 * @uses bbp_get_reply_author_avatar() To get the reply author id
 */
function bbp_reply_author_avatar( $reply_id = 0, $size = 40 ) {
	echo bbp_get_reply_author_avatar( $reply_id, $size );
}
	/**
	 * Return the author avatar of the reply
	 *
	 * @since bbPress (r2667)
	 *
	 * @param int $reply_id Optional. Reply id
	 * @param int $size Optional. Size of the avatar. Defaults to 40
	 * @uses bbp_get_reply_id() To get the reply id
	 * @uses bbp_is_reply_anonymous() To check if the reply is by an
	 *                                 anonymous user
	 * @uses bbp_get_reply_author_id() To get the reply author id
	 * @uses get_post_meta() To get the anonymous poster's email id
	 * @uses get_avatar() To get the avatar
	 * @uses apply_filters() Calls 'bbp_get_reply_author_avatar' with the
	 *                        author avatar, reply id and size
	 * @return string Avatar of author of the reply
	 */
	function bbp_get_reply_author_avatar( $reply_id = 0, $size = 40 ) {
		$reply_id = bbp_get_reply_id( $reply_id );
		if ( !empty( $reply_id ) ) {
			// Check for anonymous user
			if ( !bbp_is_reply_anonymous( $reply_id ) ) {
				$author_avatar = get_avatar( bbp_get_reply_author_id( $reply_id ), $size );
			} else {
				$author_avatar = get_avatar( get_post_meta( $reply_id, '_bbp_anonymous_email', true ), $size );
			}
		} else {
			$author_avatar = '';
		}

		return apply_filters( 'bbp_get_reply_author_avatar', $author_avatar, $reply_id, $size );
	}

/**
 * Output the author link of the reply
 *
 * @since bbPress (r2717)
 *
 * @param mixed $args Optional. If it is an integer, it is used as reply id.
 * @uses bbp_get_reply_author_link() To get the reply author link
 */
function bbp_reply_author_link( $args = '' ) {
	echo bbp_get_reply_author_link( $args );
}
	/**
	 * Return the author link of the reply
	 *
	 * @since bbPress (r2717)
	 *
	 * @param mixed $args Optional. If an integer, it is used as reply id.
	 * @uses bbp_get_reply_id() To get the reply id
	 * @uses bbp_is_reply_anonymous() To check if the reply is by an
	 *                                 anonymous user
	 * @uses bbp_get_reply_author_url() To get the reply author url
	 * @uses bbp_get_reply_author_avatar() To get the reply author avatar
	 * @uses bbp_get_reply_author_display_name() To get the reply author display
	 *                                      name
	 * @uses bbp_get_user_display_role() To get the reply author display role
	 * @uses bbp_get_reply_author_id() To get the reply author id
	 * @uses apply_filters() Calls 'bbp_get_reply_author_link' with the
	 *                        author link and args
	 * @return string Author link of reply
	 */
	function bbp_get_reply_author_link( $args = '' ) {

		// Parse arguments against default values
		$r = bbp_parse_args( $args, array(
			'post_id'    => 0,
			'link_title' => '',
			'type'       => 'both',
			'size'       => 80,
			'sep'        => '&nbsp;',
			'show_role'  => false
		), 'get_reply_author_link' );

		// Used as reply_id
		if ( is_numeric( $args ) ) {
			$reply_id = bbp_get_reply_id( $args );
		} else {
			$reply_id = bbp_get_reply_id( $r['post_id'] );
		}

		// Reply ID is good
		if ( !empty( $reply_id ) ) {

			// Get some useful reply information
			$author_url = bbp_get_reply_author_url( $reply_id );
			$anonymous  = bbp_is_reply_anonymous( $reply_id );

			// Tweak link title if empty
			if ( empty( $r['link_title'] ) ) {
				$link_title = sprintf( empty( $anonymous ) ? __( 'View %s\'s profile', 'bbpress' ) : __( 'Visit %s\'s website', 'bbpress' ), bbp_get_reply_author_display_name( $reply_id ) );

			// Use what was passed if not
			} else {
				$link_title = $r['link_title'];
			}

			// Setup title and author_links array
			$link_title   = !empty( $link_title ) ? ' title="' . esc_attr( $link_title ) . '"' : '';
			$author_links = array();

			// Get avatar
			if ( 'avatar' === $r['type'] || 'both' === $r['type'] ) {
				$author_links['avatar'] = bbp_get_reply_author_avatar( $reply_id, $r['size'] );
			}

			// Get display name
			if ( 'name' === $r['type']   || 'both' === $r['type'] ) {
				$author_links['name'] = bbp_get_reply_author_display_name( $reply_id );
			}

			// Link class
			$link_class = ' class="bbp-author-' . esc_attr( $r['type'] ) . '"';

			// Add links if not anonymous and existing user
			if ( empty( $anonymous ) && bbp_user_has_profile( bbp_get_reply_author_id( $reply_id ) ) ) {

				// Assemble the links
				foreach ( $author_links as $link => $link_text ) {
					$link_class = ' class="bbp-author-' . $link . '"';
					$author_link[] = sprintf( '<a href="%1$s"%2$s%3$s>%4$s</a>', esc_url( $author_url ), $link_title, $link_class, $link_text );
				}

				if ( true === $r['show_role'] ) {
					$author_link[] = bbp_get_reply_author_role( array( 'reply_id' => $reply_id ) );
				}

				$author_link = implode( $r['sep'], $author_link );

			// No links if anonymous
			} else {
				$author_link = implode( $r['sep'], $author_links );
			}

		// No replies so link is empty
		} else {
			$author_link = '';
		}

		return apply_filters( 'bbp_get_reply_author_link', $author_link, $r );
	}

/**
 * Output the author url of the reply
 *
 * @since bbPress (r2667)
 *
 * @param int $reply_id Optional. Reply id
 * @uses bbp_get_reply_author_url() To get the reply author url
 */
function bbp_reply_author_url( $reply_id = 0 ) {
	echo esc_url( bbp_get_reply_author_url( $reply_id ) );
}
	/**
	 * Return the author url of the reply
	 *
	 * @since bbPress (r2667)
	 *
	 * @param int $reply_id Optional. Reply id
	 * @uses bbp_get_reply_id() To get the reply id
	 * @uses bbp_is_reply_anonymous() To check if the reply is by an anonymous
	 *                                 user
	 * @uses bbp_user_has_profile() To check if the user has a profile
	 * @uses bbp_get_reply_author_id() To get the reply author id
	 * @uses bbp_get_user_profile_url() To get the user profile url
	 * @uses get_post_meta() To get the anonymous poster's website url
	 * @uses apply_filters() Calls bbp_get_reply_author_url with the author
	 *                        url & reply id
	 * @return string Author URL of the reply
	 */
	function bbp_get_reply_author_url( $reply_id = 0 ) {
		$reply_id = bbp_get_reply_id( $reply_id );

		// Check for anonymous user or non-existant user
		if ( !bbp_is_reply_anonymous( $reply_id ) && bbp_user_has_profile( bbp_get_reply_author_id( $reply_id ) ) ) {
			$author_url = bbp_get_user_profile_url( bbp_get_reply_author_id( $reply_id ) );
		} else {
			$author_url = get_post_meta( $reply_id, '_bbp_anonymous_website', true );
			if ( empty( $author_url ) ) {
				$author_url = '';
			}
		}

		return apply_filters( 'bbp_get_reply_author_url', $author_url, $reply_id );
	}

/**
 * Output the reply author email address
 *
 * @since bbPress (r3445)
 *
 * @param int $reply_id Optional. Reply id
 * @uses bbp_get_reply_author_email() To get the reply author email
 */
function bbp_reply_author_email( $reply_id = 0 ) {
	echo bbp_get_reply_author_email( $reply_id );
}
	/**
	 * Return the reply author email address
	 *
	 * @since bbPress (r3445)
	 *
	 * @param int $reply_id Optional. Reply id
	 * @uses bbp_get_reply_id() To get the reply id
	 * @uses bbp_is_reply_anonymous() To check if the reply is by an anonymous
	 *                                 user
	 * @uses bbp_get_reply_author_id() To get the reply author id
	 * @uses get_userdata() To get the user data
	 * @uses get_post_meta() To get the anonymous poster's website email
	 * @uses apply_filters() Calls bbp_get_reply_author_email with the author
	 *                        email & reply id
	 * @return string Reply author email address
	 */
	function bbp_get_reply_author_email( $reply_id = 0 ) {
		$reply_id = bbp_get_reply_id( $reply_id );

		// Not anonymous
		if ( !bbp_is_reply_anonymous( $reply_id ) ) {

			// Use reply author email address
			$user_id      = bbp_get_reply_author_id( $reply_id );
			$user         = get_userdata( $user_id );
			$author_email = !empty( $user->user_email ) ? $user->user_email : '';

		// Anonymous
		} else {

			// Get email from post meta
			$author_email = get_post_meta( $reply_id, '_bbp_anonymous_email', true );

			// Sanity check for missing email address
			if ( empty( $author_email ) ) {
				$author_email = '';
			}
		}

		return apply_filters( 'bbp_get_reply_author_email', $author_email, $reply_id );
	}

/**
 * Output the reply author role
 *
 * @since bbPress (r3860)
 *
 * @param array $args Optional.
 * @uses bbp_get_reply_author_role() To get the reply author role
 */
function bbp_reply_author_role( $args = array() ) {
	echo bbp_get_reply_author_role( $args );
}
	/**
	 * Return the reply author role
	 *
	 * @since bbPress (r3860)
	 *
	 * @param array $args Optional.
	 * @uses bbp_get_reply_id() To get the reply id
	 * @uses bbp_get_user_display_role() To get the user display role
	 * @uses bbp_get_reply_author_id() To get the reply author id
	 * @uses apply_filters() Calls bbp_get_reply_author_role with the author
	 *                        role & args
	 * @return string Reply author role
	 */
	function bbp_get_reply_author_role( $args = array() ) {

		// Parse arguments against default values
		$r = bbp_parse_args( $args, array(
			'reply_id' => 0,
			'class'    => 'bbp-author-role',
			'before'   => '',
			'after'    => ''
		), 'get_reply_author_role' );

		$reply_id    = bbp_get_reply_id( $r['reply_id'] );
		$role        = bbp_get_user_display_role( bbp_get_reply_author_id( $reply_id ) );
		$author_role = sprintf( '%1$s<div class="%2$s">%3$s</div>%4$s', $r['before'], esc_attr( $r['class'] ), esc_html( $role ), $r['after'] );

		return apply_filters( 'bbp_get_reply_author_role', $author_role, $r );
	}

/**
 * Output the topic title a reply belongs to
 *
 * @since bbPress (r2553)
 *
 * @param int $reply_id Optional. Reply id
 * @uses bbp_get_reply_topic_title() To get the reply topic title
 */
function bbp_reply_topic_title( $reply_id = 0 ) {
	echo bbp_get_reply_topic_title( $reply_id );
}
	/**
	 * Return the topic title a reply belongs to
	 *
	 * @since bbPress (r2553)
	 *
	 * @param int $reply_id Optional. Reply id
	 * @uses bbp_get_reply_id() To get the reply id
	 * @uses bbp_get_reply_topic_id() To get the reply topic id
	 * @uses bbp_get_topic_title() To get the reply topic title
	 * @uses apply_filters() Calls 'bbp_get_reply_topic_title' with the
	 *                        topic title and reply id
	 * @return string Reply's topic's title
	 */
	function bbp_get_reply_topic_title( $reply_id = 0 ) {
		$reply_id = bbp_get_reply_id( $reply_id );
		$topic_id = bbp_get_reply_topic_id( $reply_id );

		return apply_filters( 'bbp_get_reply_topic_title', bbp_get_topic_title( $topic_id ), $reply_id );
	}

/**
 * Output the topic id a reply belongs to
 *
 * @since bbPress (r2553)
 *
 * @param int $reply_id Optional. Reply id
 * @uses bbp_get_reply_topic_id() To get the reply topic id
 */
function bbp_reply_topic_id( $reply_id = 0 ) {
	echo bbp_get_reply_topic_id( $reply_id );
}
	/**
	 * Return the topic id a reply belongs to
	 *
	 * @since bbPress (r2553)
	 *
	 * @param int $reply_id Optional. Reply id
	 * @uses bbp_get_reply_id() To get the reply id
	 * @uses get_post_meta() To get the reply topic id from meta
	 * @uses bbp_get_topic_id() To get the topic id
	 * @uses apply_filters() Calls 'bbp_get_reply_topic_id' with the topic
	 *                        id and reply id
	 * @return int Reply's topic id
	 */
	function bbp_get_reply_topic_id( $reply_id = 0 ) {

		// Assume there is no topic id
		$topic_id = 0;

		// Check that reply_id is valid
		if ( $reply_id = bbp_get_reply_id( $reply_id ) )

			// Get topic_id from reply
			if ( $topic_id = get_post_meta( $reply_id, '_bbp_topic_id', true ) )

				// Validate the topic_id
				$topic_id = bbp_get_topic_id( $topic_id );

		return (int) apply_filters( 'bbp_get_reply_topic_id', $topic_id, $reply_id );
	}

/**
 * Output the forum id a reply belongs to
 *
 * @since bbPress (r2679)
 *
 * @param int $reply_id Optional. Reply id
 * @uses bbp_get_reply_forum_id() To get the reply forum id
 */
function bbp_reply_forum_id( $reply_id = 0 ) {
	echo bbp_get_reply_forum_id( $reply_id );
}
	/**
	 * Return the forum id a reply belongs to
	 *
	 * @since bbPress (r2679)
	 *
	 * @param int $reply_id Optional. Reply id
	 * @uses bbp_get_reply_id() To get the reply id
	 * @uses get_post_meta() To get the reply forum id
	 * @uses apply_filters() Calls 'bbp_get_reply_forum_id' with the forum
	 *                        id and reply id
	 * @return int Reply's forum id
	 */
	function bbp_get_reply_forum_id( $reply_id = 0 ) {

		// Assume there is no forum
		$forum_id = 0;

		// Check that reply_id is valid
		if ( $reply_id = bbp_get_reply_id( $reply_id ) )

			// Get forum_id from reply
			if ( $forum_id = get_post_meta( $reply_id, '_bbp_forum_id', true ) )

				// Validate the forum_id
				$forum_id = bbp_get_forum_id( $forum_id );

		return (int) apply_filters( 'bbp_get_reply_forum_id', $forum_id, $reply_id );
	}

/**
 * Output the reply's ancestor reply id
 *
 * @since bbPress (r4944)
 *
 * @param int $reply_id Optional. Reply id
 * @uses bbp_get_reply_ancestor_id() To get the reply's ancestor id
 */
function bbp_reply_ancestor_id( $reply_id = 0 ) {
	echo bbp_get_reply_ancestor_id( $reply_id );
}
	/**
	 * Return the reply's ancestor reply id
	 *
	 * @since bbPress (r4944)
	 *
	 * @param in $reply_id Reply id
	 * @uses bbp_get_reply_id() To get the reply id
	 */
	function bbp_get_reply_ancestor_id( $reply_id = 0 ) {

		// Validation
		$reply_id = bbp_get_reply_id( $reply_id );
		if ( empty( $reply_id ) )
			return false;

		// Find highest reply ancestor
		$ancestor_id = $reply_id;
		while ( $parent_id = bbp_get_reply_to( $ancestor_id ) ) {
			if ( empty( $parent_id ) || ( $parent_id === $ancestor_id ) || ( bbp_get_reply_topic_id( $reply_id ) === $parent_id ) || ( $parent_id === $reply_id ) ) {
				break;
			}
			$ancestor_id = $parent_id;
		}

		return (int) $ancestor_id;
	}

/**
 * Output the reply to id of a reply
 *
 * @since bbPress (r4944)
 *
 * @param int $reply_id Optional. Reply id
 * @uses bbp_get_reply_to() To get the reply to id
 */
function bbp_reply_to( $reply_id = 0 ) {
	echo bbp_get_reply_to( $reply_id );
}
	/**
	 * Return the reply to id of a reply
	 *
 	 * @since bbPress (r4944)
	 *
	 * @param int $reply_id Optional. Reply id
	 * @uses bbp_get_reply_id() To get the reply id
	 * @uses get_post_meta() To get the reply to id
	 * @uses apply_filters() Calls 'bbp_get_reply_to' with the reply to id and
	 *                        reply id
	 * @return int Reply's reply to id
	 */
	function bbp_get_reply_to( $reply_id = 0 ) {

		// Assume there is no reply_to set
		$reply_to = 0;

		// Check that reply_id is valid
		$reply_id = bbp_get_reply_id( $reply_id );

		// Get reply_to value
		if ( !empty( $reply_id ) ) {
			$reply_to = (int) get_post_meta( $reply_id, '_bbp_reply_to', true );
		}

		return (int) apply_filters( 'bbp_get_reply_to', $reply_to, $reply_id );
	}

/**
 * Output the link for the reply to
 *
 * @since bbPress (r4944)
 *
 * @param array $args
 * @uses bbp_get_reply_to_link() To get the reply to link
 */
function bbp_reply_to_link( $args = array() ) {
	echo bbp_get_reply_to_link( $args );
}

	/**
	 * Return the link for a reply to a reply
	 *
	 * @since bbPress (r4944)
	 *
	 * @param array $args Arguments
	 * @uses bbp_current_user_can_access_create_reply_form() To check permissions
	 * @uses bbp_get_reply_id() To validate the reply id
	 * @uses bbp_get_reply() To get the reply
	 * @uses apply_filters() Calls 'bbp_get_reply_to_link' with the formatted link,
	 *                        the arguments array, and the reply
	 * @return string Link for a reply to a reply
	 */
	function bbp_get_reply_to_link( $args = array() ) {

		// Parse arguments against default values
		$r = bbp_parse_args( $args, array(
			'id'           => 0,
			'link_before'  => '',
			'link_after'   => '',
			'reply_text'   => __( 'Reply', 'bbpress' ),
			'depth'        => 0,
			'add_below'    => 'post',
			'respond_id'   => 'new-reply-' . bbp_get_topic_id(),
		), 'get_reply_to_link' );

		// Get the reply to use it's ID and post_parent
		$reply = bbp_get_reply( bbp_get_reply_id( (int) $r['id'] ) );

		// Bail if no reply or user cannot reply
		if ( empty( $reply ) || ! bbp_current_user_can_access_create_reply_form() )
			return;

		// Build the URI and return value
		$uri = remove_query_arg( array( 'bbp_reply_to' ) );
		$uri = add_query_arg( array( 'bbp_reply_to' => $reply->ID ) );
		$uri = wp_nonce_url( $uri, 'respond_id_' . $reply->ID );
		$uri = $uri . '#new-post';

		// Only add onclick if replies are threaded
		if ( bbp_thread_replies() ) {

			// Array of classes to pass to moveForm
			$move_form = array(
				$r['add_below'] . '-' . $reply->ID,
				$reply->ID,
				$r['respond_id'],
				$reply->post_parent
			);

			// Build the onclick
			$onclick  = ' onclick="return addReply.moveForm(\'' . implode( "','", $move_form ) . '\');"';

		// No onclick if replies are not threaded
		} else {
			$onclick  = '';
		}

		// Add $uri to the array, to be passed through the filter
		$r['uri'] = $uri;
		$retval   = $r['link_before'] . '<a href="' . esc_url( $r['uri'] ) . '" class="bbp-reply-to-link"' . $onclick . '>' . esc_html( $r['reply_text'] ) . '</a>' . $r['link_after'];

		return apply_filters( 'bbp_get_reply_to_link', $retval, $r, $args );
	}

/**
 * Output the reply to a reply cancellation link
 *
 * @since bbPress (r4944)
 *
 * @uses bbp_get_cancel_reply_to_link() To get the reply cancellation link
 */
function bbp_cancel_reply_to_link( $text = '' ) {
	echo bbp_get_cancel_reply_to_link( $text );
}
	/**
	 * Return the cancellation link for a reply to a reply
	 *
	 * @since bbPress (r4944)
	 *
	 * @param string $text The cancel text
	 * @uses apply_filters() Calls 'bbp_get_cancel_reply_to_link' with the cancellation
	 *                        link and the cancel text
	 * @return string The cancellation link
	 */
	function bbp_get_cancel_reply_to_link( $text = '' ) {

		// Bail if not hierarchical or editing a reply
		if ( ! bbp_thread_replies() || bbp_is_reply_edit() ) {
			return;
		}

		// Set default text
		if ( empty( $text ) ) {
			$text = __( 'Cancel', 'bbpress' );
		}

		$reply_to = isset( $_GET['bbp_reply_to'] ) ? (int) $_GET['bbp_reply_to'] : 0;

		// Set visibility
		$style  = !empty( $reply_to ) ? '' : ' style="display:none;"';
		$link   = remove_query_arg( array( 'bbp_reply_to', '_wpnonce' ) ) . '#post-' . $reply_to;
		$retval = '<a rel="nofollow" id="bbp-cancel-reply-to-link" href="' . esc_url( $link ) . '"' . $style . '>' . esc_html( $text ) . '</a>';

		return apply_filters( 'bbp_get_cancel_reply_to_link', $retval, $link, $text );
	}

/**
 * Output the numeric position of a reply within a topic
 *
 * @since bbPress (r2984)
 *
 * @param int $reply_id Optional. Reply id
 * @param int $topic_id Optional. Topic id
 * @uses bbp_get_reply_position() To get the reply position
 */
function bbp_reply_position( $reply_id = 0, $topic_id = 0 ) {
	echo bbp_get_reply_position( $reply_id, $topic_id );
}
	/**
	 * Return the numeric position of a reply within a topic
	 *
	 * @since bbPress (r2984)
	 *
	 * @param int $reply_id Optional. Reply id
	 * @param int $topic_id Optional. Topic id
	 * @uses bbp_get_reply_id() To get the reply id
	 * @uses bbp_get_reply_topic_id() Get the topic id of the reply id
	 * @uses bbp_get_topic_reply_count() To get the topic reply count
	 * @uses bbp_get_reply_post_type() To get the reply post type
	 * @uses bbp_get_reply_position_raw() To get calculate the reply position
	 * @uses bbp_update_reply_position() To update the reply position
	 * @uses bbp_show_lead_topic() Bump the count if lead topic is included
	 * @uses apply_filters() Calls 'bbp_get_reply_position' with the reply
	 *                        position, reply id and topic id
	 * @return int Reply position
	 */
	function bbp_get_reply_position( $reply_id = 0, $topic_id = 0 ) {

		// Get required data
		$reply_id       = bbp_get_reply_id( $reply_id );
		$reply_position = get_post_field( 'menu_order', $reply_id );

		// Reply doesn't have a position so get the raw value
		if ( empty( $reply_position ) ) {
			$topic_id = !empty( $topic_id ) ? bbp_get_topic_id( $topic_id ) : bbp_get_reply_topic_id( $reply_id );

			// Post is not the topic
			if ( $reply_id !== $topic_id ) {
				$reply_position = bbp_get_reply_position_raw( $reply_id, $topic_id );

				// Update the reply position in the posts table so we'll never have
				// to hit the DB again.
				if ( !empty( $reply_position ) ) {
					bbp_update_reply_position( $reply_id, $reply_position );
				}

			// Topic's position is always 0
			} else {
				$reply_position = 0;
			}
		}

		// Bump the position by one if the lead topic is in the replies loop
		if ( ! bbp_show_lead_topic() )
			$reply_position++;

		return (int) apply_filters( 'bbp_get_reply_position', $reply_position, $reply_id, $topic_id );
	}

/** Reply Admin Links *********************************************************/

/**
 * Output admin links for reply
 *
 * @since bbPress (r2667)
 *
 * @param array $args See {@link bbp_get_reply_admin_links()}
 * @uses bbp_get_reply_admin_links() To get the reply admin links
 */
function bbp_reply_admin_links( $args = array() ) {
	echo bbp_get_reply_admin_links( $args );
}
	/**
	 * Return admin links for reply
	 *
	 * @since bbPress (r2667)
	 *
	 * @param array $args This function supports these arguments:
	 *  - id: Optional. Reply id
	 *  - before: HTML before the links. Defaults to
	 *             '<span class="bbp-admin-links">'
	 *  - after: HTML after the links. Defaults to '</span>'
	 *  - sep: Separator. Defaults to ' | '
	 *  - links: Array of the links to display. By default, edit, trash,
	 *            spam, reply move, and topic split links are displayed
	 * @uses bbp_is_topic() To check if it's the topic page
	 * @uses bbp_is_reply() To check if it's the reply page
	 * @uses bbp_get_reply_id() To get the reply id
	 * @uses bbp_get_reply_edit_link() To get the reply edit link
	 * @uses bbp_get_reply_trash_link() To get the reply trash link
	 * @uses bbp_get_reply_spam_link() To get the reply spam link
	 * @uses bbp_get_reply_move_link() To get the reply move link
	 * @uses bbp_get_topic_split_link() To get the topic split link
	 * @uses current_user_can() To check if the current user can edit or
	 *                           delete the reply
	 * @uses apply_filters() Calls 'bbp_get_reply_admin_links' with the
	 *                        reply admin links and args
	 * @return string Reply admin links
	 */
	function bbp_get_reply_admin_links( $args = array() ) {

		// Parse arguments against default values
		$r = bbp_parse_args( $args, array(
			'id'     => 0,
			'before' => '<span class="bbp-admin-links">',
			'after'  => '</span>',
			'sep'    => ' | ',
			'links'  => array()
		), 'get_reply_admin_links' );

		$r['id'] = bbp_get_reply_id( (int) $r['id'] );

		// If post is a topic, return the topic admin links instead
		if ( bbp_is_topic( $r['id'] ) ) {
			return bbp_get_topic_admin_links( $args );
		}

		// If post is not a reply, return
		if ( !bbp_is_reply( $r['id'] ) ) {
			return;
		}

		// If topic is trashed, do not show admin links
		if ( bbp_is_topic_trash( bbp_get_reply_topic_id( $r['id'] ) ) ) {
			return;
		}

		// If no links were passed, default to the standard
		if ( empty( $r['links'] ) ) {
			$r['links'] = apply_filters( 'bbp_reply_admin_links', array(
				'edit'  => bbp_get_reply_edit_link ( $r ),
				'move'  => bbp_get_reply_move_link ( $r ),
				'split' => bbp_get_topic_split_link( $r ),
				'trash' => bbp_get_reply_trash_link( $r ),
				'spam'  => bbp_get_reply_spam_link ( $r ),
				'reply' => bbp_get_reply_to_link   ( $r )
			), $r['id'] );
		}

		// See if links need to be unset
		$reply_status = bbp_get_reply_status( $r['id'] );
		if ( in_array( $reply_status, array( bbp_get_spam_status_id(), bbp_get_trash_status_id() ) ) ) {

			// Spam link shouldn't be visible on trashed topics
			if ( bbp_get_trash_status_id() === $reply_status ) {
				unset( $r['links']['spam'] );

			// Trash link shouldn't be visible on spam topics
			} elseif ( bbp_get_spam_status_id() === $reply_status ) {
				unset( $r['links']['trash'] );
			}
		}

		// Process the admin links
		$links  = implode( $r['sep'], array_filter( $r['links'] ) );
		$retval = $r['before'] . $links . $r['after'];

		return apply_filters( 'bbp_get_reply_admin_links', $retval, $r, $args );
	}

/**
 * Output the edit link of the reply
 *
 * @since bbPress (r2740)
 *
 * @param mixed $args See {@link bbp_get_reply_edit_link()}
 * @uses bbp_get_reply_edit_link() To get the reply edit link
 */
function bbp_reply_edit_link( $args = '' ) {
	echo bbp_get_reply_edit_link( $args );
}

	/**
	 * Return the edit link of the reply
	 *
	 * @since bbPress (r2740)
	 *
	 * @param mixed $args This function supports these arguments:
	 *  - id: Reply id
	 *  - link_before: HTML before the link
	 *  - link_after: HTML after the link
	 *  - edit_text: Edit text. Defaults to 'Edit'
	 * @uses bbp_get_reply_id() To get the reply id
	 * @uses bbp_get_reply() To get the reply
	 * @uses current_user_can() To check if the current user can edit the
	 *                           reply
	 * @uses bbp_get_reply_edit_url() To get the reply edit url
	 * @uses apply_filters() Calls 'bbp_get_reply_edit_link' with the reply
	 *                        edit link and args
	 * @return string Reply edit link
	 */
	function bbp_get_reply_edit_link( $args = '' ) {

		// Parse arguments against default values
		$r = bbp_parse_args( $args, array(
			'id'           => 0,
			'link_before'  => '',
			'link_after'   => '',
			'edit_text'    => esc_html__( 'Edit', 'bbpress' )
		), 'get_reply_edit_link' );

		$reply = bbp_get_reply( bbp_get_reply_id( (int) $r['id'] ) );

		// Bypass check if user has caps
		if ( !current_user_can( 'edit_others_replies' ) ) {

			// User cannot edit or it is past the lock time
			if ( empty( $reply ) || !current_user_can( 'edit_reply', $reply->ID ) || bbp_past_edit_lock( $reply->post_date_gmt ) ) {
				return;
			}
		}

		// Get uri
		$uri = bbp_get_reply_edit_url( $r['id'] );

		// Bail if no uri
		if ( empty( $uri ) )
			return;

		$retval = $r['link_before'] . '<a href="' . esc_url( $uri ) . '" class="bbp-reply-edit-link">' . $r['edit_text'] . '</a>' . $r['link_after'];

		return apply_filters( 'bbp_get_reply_edit_link', $retval, $r );
	}

/**
 * Output URL to the reply edit page
 *
 * @since bbPress (r2753)
 *
 * @param int $reply_id Optional. Reply id
 * @uses bbp_get_reply_edit_url() To get the reply edit url
 */
function bbp_reply_edit_url( $reply_id = 0 ) {
	echo esc_url( bbp_get_reply_edit_url( $reply_id ) );
}
	/**
	 * Return URL to the reply edit page
	 *
	 * @since bbPress (r2753)
	 *
	 * @param int $reply_id Optional. Reply id
	 * @uses bbp_get_reply_id() To get the reply id
	 * @uses bbp_get_reply() To get the reply
	 * @uses bbp_get_reply_post_type() To get the reply post type
	 * @uses add_query_arg() To add custom args to the url
	 * @uses apply_filters() Calls 'bbp_get_reply_edit_url' with the edit
	 *                        url and reply id
	 * @return string Reply edit url
	 */
	function bbp_get_reply_edit_url( $reply_id = 0 ) {
		global $wp_rewrite;

		$bbp   = bbpress();
		$reply = bbp_get_reply( bbp_get_reply_id( $reply_id ) );
		if ( empty( $reply ) )
			return;

		$reply_link = bbp_remove_view_all( bbp_get_reply_permalink( $reply_id ) );

		// Pretty permalinks
		if ( $wp_rewrite->using_permalinks() ) {
			$url = trailingslashit( $reply_link ) . $bbp->edit_id;
			$url = trailingslashit( $url );

		// Unpretty permalinks
		} else {
			$url = add_query_arg( array( bbp_get_reply_post_type() => $reply->post_name, $bbp->edit_id => '1' ), $reply_link );
		}

		// Maybe add view all
		$url = bbp_add_view_all( $url );

		return apply_filters( 'bbp_get_reply_edit_url', $url, $reply_id );
	}

/**
 * Output the trash link of the reply
 *
 * @since bbPress (r2740)
 *
 * @param mixed $args See {@link bbp_get_reply_trash_link()}
 * @uses bbp_get_reply_trash_link() To get the reply trash link
 */
function bbp_reply_trash_link( $args = '' ) {
	echo bbp_get_reply_trash_link( $args );
}

	/**
	 * Return the trash link of the reply
	 *
	 * @since bbPress (r2740)
	 *
	 * @param mixed $args This function supports these arguments:
	 *  - id: Reply id
	 *  - link_before: HTML before the link
	 *  - link_after: HTML after the link
	 *  - sep: Separator
	 *  - trash_text: Trash text
	 *  - restore_text: Restore text
	 *  - delete_text: Delete text
	 * @uses bbp_get_reply_id() To get the reply id
	 * @uses bbp_get_reply() To get the reply
	 * @uses current_user_can() To check if the current user can delete the
	 *                           reply
	 * @uses bbp_is_reply_trash() To check if the reply is trashed
	 * @uses bbp_get_reply_status() To get the reply status
	 * @uses add_query_arg() To add custom args to the url
	 * @uses wp_nonce_url() To nonce the url
	 * @uses esc_url() To escape the url
	 * @uses bbp_get_reply_edit_url() To get the reply edit url
	 * @uses apply_filters() Calls 'bbp_get_reply_trash_link' with the reply
	 *                        trash link and args
	 * @return string Reply trash link
	 */
	function bbp_get_reply_trash_link( $args = '' ) {

		// Parse arguments against default values
		$r = bbp_parse_args( $args, array(
			'id'           => 0,
			'link_before'  => '',
			'link_after'   => '',
			'sep'          => ' | ',
			'trash_text'   => esc_html__( 'Trash',   'bbpress' ),
			'restore_text' => esc_html__( 'Restore', 'bbpress' ),
			'delete_text'  => esc_html__( 'Delete',  'bbpress' )
		), 'get_reply_trash_link' );

		$actions = array();
		$reply   = bbp_get_reply( bbp_get_reply_id( (int) $r['id'] ) );

		if ( empty( $reply ) || !current_user_can( 'delete_reply', $reply->ID ) ) {
			return;
		}

		if ( bbp_is_reply_trash( $reply->ID ) ) {
			$actions['untrash'] = '<a title="' . esc_attr__( 'Restore this item from the Trash', 'bbpress' ) . '" href="' . esc_url( wp_nonce_url( add_query_arg( array( 'action' => 'bbp_toggle_reply_trash', 'sub_action' => 'untrash', 'reply_id' => $reply->ID ) ), 'untrash-' . $reply->post_type . '_' . $reply->ID ) ) . '" class="bbp-reply-restore-link">' . $r['restore_text'] . '</a>';
		} elseif ( EMPTY_TRASH_DAYS ) {
			$actions['trash']   = '<a title="' . esc_attr__( 'Move this item to the Trash',      'bbpress' ) . '" href="' . esc_url( wp_nonce_url( add_query_arg( array( 'action' => 'bbp_toggle_reply_trash', 'sub_action' => 'trash',   'reply_id' => $reply->ID ) ), 'trash-'   . $reply->post_type . '_' . $reply->ID ) ) . '" class="bbp-reply-trash-link">'   . $r['trash_text']   . '</a>';
		}

		if ( bbp_is_reply_trash( $reply->ID ) || !EMPTY_TRASH_DAYS ) {
			$actions['delete']  = '<a title="' . esc_attr__( 'Delete this item permanently',     'bbpress' ) . '" href="' . esc_url( wp_nonce_url( add_query_arg( array( 'action' => 'bbp_toggle_reply_trash', 'sub_action' => 'delete',  'reply_id' => $reply->ID ) ), 'delete-'  . $reply->post_type . '_' . $reply->ID ) ) . '" onclick="return confirm(\'' . esc_js( __( 'Are you sure you want to delete that permanently?', 'bbpress' ) ) . '\' );" class="bbp-reply-delete-link">' . $r['delete_text'] . '</a>';
		}

		// Process the admin links
		$retval = $r['link_before'] . implode( $r['sep'], $actions ) . $r['link_after'];

		return apply_filters( 'bbp_get_reply_trash_link', $retval, $r );
	}

/**
 * Output the spam link of the reply
 *
 * @since bbPress (r2740)
 *
 * @param mixed $args See {@link bbp_get_reply_spam_link()}
 * @uses bbp_get_reply_spam_link() To get the reply spam link
 */
function bbp_reply_spam_link( $args = '' ) {
	echo bbp_get_reply_spam_link( $args );
}

	/**
	 * Return the spam link of the reply
	 *
	 * @since bbPress (r2740)
	 *
	 * @param mixed $args This function supports these arguments:
	 *  - id: Reply id
	 *  - link_before: HTML before the link
	 *  - link_after: HTML after the link
	 *  - spam_text: Spam text
	 *  - unspam_text: Unspam text
	 * @uses bbp_get_reply_id() To get the reply id
	 * @uses bbp_get_reply() To get the reply
	 * @uses current_user_can() To check if the current user can edit the
	 *                           reply
	 * @uses bbp_is_reply_spam() To check if the reply is marked as spam
	 * @uses add_query_arg() To add custom args to the url
	 * @uses wp_nonce_url() To nonce the url
	 * @uses esc_url() To escape the url
	 * @uses bbp_get_reply_edit_url() To get the reply edit url
	 * @uses apply_filters() Calls 'bbp_get_reply_spam_link' with the reply
	 *                        spam link and args
	 * @return string Reply spam link
	 */
	function bbp_get_reply_spam_link( $args = '' ) {

		// Parse arguments against default values
		$r = bbp_parse_args( $args, array(
			'id'           => 0,
			'link_before'  => '',
			'link_after'   => '',
			'spam_text'    => esc_html__( 'Spam',   'bbpress' ),
			'unspam_text'  => esc_html__( 'Unspam', 'bbpress' )
		), 'get_reply_spam_link' );

		$reply = bbp_get_reply( bbp_get_reply_id( (int) $r['id'] ) );

		if ( empty( $reply ) || !current_user_can( 'moderate', $reply->ID ) )
			return;

		$display  = bbp_is_reply_spam( $reply->ID ) ? $r['unspam_text'] : $r['spam_text'];
		$uri      = add_query_arg( array( 'action' => 'bbp_toggle_reply_spam', 'reply_id' => $reply->ID ) );
		$uri      = wp_nonce_url( $uri, 'spam-reply_' . $reply->ID );
		$retval   = $r['link_before'] . '<a href="' . esc_url( $uri ) . '" class="bbp-reply-spam-link">' . $display . '</a>' . $r['link_after'];

		return apply_filters( 'bbp_get_reply_spam_link', $retval, $r );
	}

/**
 * Move reply link
 *
 * Output the move link of the reply
 *
 * @since bbPress (r4521)
 *
 * @param mixed $args See {@link bbp_get_reply_move_link()}
 * @uses bbp_get_reply_move_link() To get the reply move link
 */
function bbp_reply_move_link( $args = '' ) {
	echo bbp_get_reply_move_link( $args );
}

	/**
	 * Get move reply link
	 *
	 * Return the move link of the reply
	 *
	 * @since bbPress (r4521)
	 *
	 * @param mixed $args This function supports these arguments:
	 *  - id: Reply id
	 *  - link_before: HTML before the link
	 *  - link_after: HTML after the link
	 *  - move_text: Move text
	 *  - move_title: Move title attribute
	 * @uses bbp_get_reply_id() To get the reply id
	 * @uses bbp_get_reply() To get the reply
	 * @uses current_user_can() To check if the current user can edit the
	 *                           topic
	 * @uses bbp_get_reply_topic_id() To get the reply topic id
	 * @uses bbp_get_reply_edit_url() To get the reply edit url
	 * @uses add_query_arg() To add custom args to the url
	 * @uses wp_nonce_url() To nonce the url
	 * @uses esc_url() To escape the url
	 * @uses apply_filters() Calls 'bbp_get_reply_move_link' with the reply
	 *                        move link and args
	 * @return string Reply move link
	 */
	function bbp_get_reply_move_link( $args = '' ) {

		// Parse arguments against default values
		$r = bbp_parse_args( $args, array(
			'id'          => 0,
			'link_before' => '',
			'link_after'  => '',
			'split_text'  => esc_html__( 'Move',            'bbpress' ),
			'split_title' => esc_attr__( 'Move this reply', 'bbpress' )
		), 'get_reply_move_link' );

		$reply_id = bbp_get_reply_id( $r['id'] );
		$topic_id = bbp_get_reply_topic_id( $reply_id );

		if ( empty( $reply_id ) || !current_user_can( 'moderate', $topic_id ) )
			return;

		$uri = add_query_arg( array(
			'action'   => 'move',
			'reply_id' => $reply_id
		), bbp_get_reply_edit_url( $reply_id ) );

		$retval = $r['link_before'] . '<a href="' . esc_url( $uri ) . '" title="' . $r['split_title'] . '" class="bbp-reply-move-link">' . $r['split_text'] . '</a>' . $r['link_after'];

		return apply_filters( 'bbp_get_reply_move_link', $retval, $r );
	}

/**
 * Split topic link
 *
 * Output the split link of the topic (but is bundled with each reply)
 *
 * @since bbPress (r2756)
 *
 * @param mixed $args See {@link bbp_get_topic_split_link()}
 * @uses bbp_get_topic_split_link() To get the topic split link
 */
function bbp_topic_split_link( $args = '' ) {
	echo bbp_get_topic_split_link( $args );
}

	/**
	 * Get split topic link
	 *
	 * Return the split link of the topic (but is bundled with each reply)
	 *
	 * @since bbPress (r2756)
	 *
	 * @param mixed $args This function supports these arguments:
	 *  - id: Reply id
	 *  - link_before: HTML before the link
	 *  - link_after: HTML after the link
	 *  - split_text: Split text
	 *  - split_title: Split title attribute
	 * @uses bbp_get_reply_id() To get the reply id
	 * @uses bbp_get_reply() To get the reply
	 * @uses current_user_can() To check if the current user can edit the
	 *                           topic
	 * @uses bbp_get_reply_topic_id() To get the reply topic id
	 * @uses bbp_get_topic_edit_url() To get the topic edit url
	 * @uses add_query_arg() To add custom args to the url
	 * @uses wp_nonce_url() To nonce the url
	 * @uses esc_url() To escape the url
	 * @uses apply_filters() Calls 'bbp_get_topic_split_link' with the topic
	 *                        split link and args
	 * @return string Topic split link
	 */
	function bbp_get_topic_split_link( $args = '' ) {

		// Parse arguments against default values
		$r = bbp_parse_args( $args, array(
			'id'          => 0,
			'link_before' => '',
			'link_after'  => '',
			'split_text'  => esc_html__( 'Split',                           'bbpress' ),
			'split_title' => esc_attr__( 'Split the topic from this reply', 'bbpress' )
		), 'get_topic_split_link' );

		$reply_id = bbp_get_reply_id( $r['id'] );
		$topic_id = bbp_get_reply_topic_id( $reply_id );

		if ( empty( $reply_id ) || !current_user_can( 'moderate', $topic_id ) )
			return;

		$uri =  add_query_arg( array(
			'action'   => 'split',
			'reply_id' => $reply_id
		), bbp_get_topic_edit_url( $topic_id ) );

		$retval = $r['link_before'] . '<a href="' . esc_url( $uri ) . '" title="' . $r['split_title'] . '" class="bbp-topic-split-link">' . $r['split_text'] . '</a>' . $r['link_after'];

		return apply_filters( 'bbp_get_topic_split_link', $retval, $r );
	}

/**
 * Output the row class of a reply
 *
 * @since bbPress (r2678)
 *
 * @param int $reply_id Optional. Reply ID
 * @param array Extra classes you can pass when calling this function
 * @uses bbp_get_reply_class() To get the reply class
 */
function bbp_reply_class( $reply_id = 0, $classes = array() ) {
	echo bbp_get_reply_class( $reply_id, $classes );
}
	/**
	 * Return the row class of a reply
	 *
	 * @since bbPress (r2678)
	 *
	 * @param int $reply_id Optional. Reply ID
	 * @param array Extra classes you can pass when calling this function
	 * @uses bbp_get_reply_id() To validate the reply id
	 * @uses bbp_get_reply_forum_id() To get the reply's forum id
	 * @uses bbp_get_reply_topic_id() To get the reply's topic id
	 * @uses get_post_class() To get all the classes including ours
	 * @uses apply_filters() Calls 'bbp_get_reply_class' with the classes
	 * @return string Row class of the reply
	 */
	function bbp_get_reply_class( $reply_id = 0, $classes = array() ) {
		$bbp       = bbpress();
		$reply_id  = bbp_get_reply_id( $reply_id );
		$count     = isset( $bbp->reply_query->current_post ) ? $bbp->reply_query->current_post : 1;
		$classes   = (array) $classes;
		$classes[] = ( (int) $count % 2 ) ? 'even' : 'odd';
		$classes[] = 'bbp-parent-forum-'   . bbp_get_reply_forum_id( $reply_id );
		$classes[] = 'bbp-parent-topic-'   . bbp_get_reply_topic_id( $reply_id );
		$classes[] = 'bbp-reply-position-' . bbp_get_reply_position( $reply_id );
		$classes[] = 'user-id-' . bbp_get_reply_author_id( $reply_id );
		$classes[] = ( bbp_get_reply_author_id( $reply_id ) === bbp_get_topic_author_id( bbp_get_reply_topic_id( $reply_id ) ) ? 'topic-author' : '' );
		$classes   = array_filter( $classes );
		$classes   = get_post_class( $classes, $reply_id );
		$classes   = apply_filters( 'bbp_get_reply_class', $classes, $reply_id );
		$retval    = 'class="' . implode( ' ', $classes ) . '"';

		return $retval;
	}

/**
 * Output the topic pagination count
 *
 * @since bbPress (r2519)
 *
 * @uses bbp_get_topic_pagination_count() To get the topic pagination count
 */
function bbp_topic_pagination_count() {
	echo bbp_get_topic_pagination_count();
}
	/**
	 * Return the topic pagination count
	 *
	 * @since bbPress (r2519)
	 *
	 * @uses bbp_number_format() To format the number value
	 * @uses bbp_show_lead_topic() Are we showing the topic as a lead?
	 * @uses apply_filters() Calls 'bbp_get_topic_pagination_count' with the
	 *                        pagination count
	 * @return string Topic pagination count
	 */
	function bbp_get_topic_pagination_count() {
		$bbp = bbpress();

		// Define local variable(s)
		$retstr = '';

		// Set pagination values
		$start_num = intval( ( $bbp->reply_query->paged - 1 ) * $bbp->reply_query->posts_per_page ) + 1;
		$from_num  = bbp_number_format( $start_num );
		$to_num    = bbp_number_format( ( $start_num + ( $bbp->reply_query->posts_per_page - 1 ) > $bbp->reply_query->found_posts ) ? $bbp->reply_query->found_posts : $start_num + ( $bbp->reply_query->posts_per_page - 1 ) );
		$total_int = (int) $bbp->reply_query->found_posts;
		$total     = bbp_number_format( $total_int );

		// We are threading replies
		if ( bbp_thread_replies() && bbp_is_single_topic() ) {
			return;
			$walker  = new BBP_Walker_Reply;
			$threads = (int) $walker->get_number_of_root_elements( $bbp->reply_query->posts );

			// Adjust for topic
			$threads--;
			$retstr  = sprintf( _n( 'Viewing %1$s reply thread', 'Viewing %1$s reply threads', $threads, 'bbbpress' ), bbp_number_format( $threads ) );

		// We are not including the lead topic
		} elseif ( bbp_show_lead_topic() ) {

			// Several replies in a topic with a single page
			if ( empty( $to_num ) ) {
				$retstr = sprintf( _n( 'Viewing %1$s reply', 'Viewing %1$s replies', $total_int, 'bbpress' ), $total );

			// Several replies in a topic with several pages
			} else {
				$retstr = sprintf( _n( 'Viewing %2$s replies (of %4$s total)', 'Viewing %1$s replies - %2$s through %3$s (of %4$s total)', $bbp->reply_query->post_count, 'bbpress' ), $bbp->reply_query->post_count, $from_num, $to_num, $total );
			}

		// We are including the lead topic
		} else {

			// Several posts in a topic with a single page
			if ( empty( $to_num ) ) {
				$retstr = sprintf( _n( 'Viewing %1$s post', 'Viewing %1$s posts', $total_int, 'bbpress' ), $total );

			// Several posts in a topic with several pages
			} else {
				$retstr = sprintf( _n( 'Viewing %2$s post (of %4$s total)', 'Viewing %1$s posts - %2$s through %3$s (of %4$s total)', $bbp->reply_query->post_count, 'bbpress' ), $bbp->reply_query->post_count, $from_num, $to_num, $total );
			}
		}

		// Filter and return
		return apply_filters( 'bbp_get_topic_pagination_count', esc_html( $retstr ) );
	}

/**
 * Output topic pagination links
 *
 * @since bbPress (r2519)
 *
 * @uses bbp_get_topic_pagination_links() To get the topic pagination links
 */
function bbp_topic_pagination_links() {
	echo bbp_get_topic_pagination_links();
}
	/**
	 * Return topic pagination links
	 *
	 * @since bbPress (r2519)
	 *
	 * @uses apply_filters() Calls 'bbp_get_topic_pagination_links' with the
	 *                        pagination links
	 * @return string Topic pagination links
	 */
	function bbp_get_topic_pagination_links() {
		$bbp = bbpress();

		if ( !isset( $bbp->reply_query->pagination_links ) || empty( $bbp->reply_query->pagination_links ) )
			return false;

		return apply_filters( 'bbp_get_topic_pagination_links', $bbp->reply_query->pagination_links );
	}

/** Forms *********************************************************************/

/**
 * Output the value of reply content field
 *
 * @since bbPress (r31301)
 *
 * @uses bbp_get_form_reply_content() To get value of reply content field
 */
function bbp_form_reply_content() {
	echo bbp_get_form_reply_content();
}
	/**
	 * Return the value of reply content field
	 *
	 * @since bbPress (r31301)
	 *
	 * @uses bbp_is_reply_edit() To check if it's the reply edit page
	 * @uses apply_filters() Calls 'bbp_get_form_reply_content' with the content
	 * @return string Value of reply content field
	 */
	function bbp_get_form_reply_content() {

		// Get _POST data
		if ( bbp_is_post_request() && isset( $_POST['bbp_reply_content'] ) ) {
			$reply_content = stripslashes( $_POST['bbp_reply_content'] );

		// Get edit data
		} elseif ( bbp_is_reply_edit() ) {
			$reply_content = bbp_get_global_post_field( 'post_content', 'raw' );

		// No data
		} else {
			$reply_content = '';
		}

		return apply_filters( 'bbp_get_form_reply_content', $reply_content );
	}

/**
 * Output the value of the reply to field
 *
 * @since bbPress (r4944)
 *
 * @uses bbp_get_form_reply_to() To get value of the reply to field
 */
function bbp_form_reply_to() {
	echo bbp_get_form_reply_to();
}

	/**
	 * Return the value of reply to field
	 *
	 * @since bbPress (r4944)
	 *
	 * @uses bbp_get_reply_id() To validate the reply to
	 * @uses apply_filters() Calls 'bbp_get_form_reply_to' with the reply to
	 * @return string Value of reply to field
	 */
	function bbp_get_form_reply_to() {

		// Set initial value
		$reply_to = 0;

		// Get $_REQUEST data
		if ( isset( $_REQUEST['bbp_reply_to'] ) ) {
			$reply_to = bbp_validate_reply_to( $_REQUEST['bbp_reply_to'] );
		}

		// If empty, get from meta
		if ( empty( $reply_to ) ) {
			$reply_to = bbp_get_reply_to();
		}

		return (int) apply_filters( 'bbp_get_form_reply_to', $reply_to );
	}

/**
 * Output checked value of reply log edit field
 *
 * @since bbPress (r31301)
 *
 * @uses bbp_get_form_reply_log_edit() To get the reply log edit value
 */
function bbp_form_reply_log_edit() {
	echo bbp_get_form_reply_log_edit();
}
	/**
	 * Return checked value of reply log edit field
	 *
	 * @since bbPress (r31301)
	 *
	 * @uses apply_filters() Calls 'bbp_get_form_reply_log_edit' with the
	 *                        log edit value
	 * @return string Reply log edit checked value
	 */
	function bbp_get_form_reply_log_edit() {

		// Get _POST data
		if ( bbp_is_post_request() && isset( $_POST['bbp_log_reply_edit'] ) ) {
			$reply_revision = $_POST['bbp_log_reply_edit'];

		// No data
		} else {
			$reply_revision = 1;
		}

		return apply_filters( 'bbp_get_form_reply_log_edit', checked( $reply_revision, true, false ) );
	}

/**
 * Output the value of the reply edit reason
 *
 * @since bbPress (r31301)
 *
 * @uses bbp_get_form_reply_edit_reason() To get the reply edit reason value
 */
function bbp_form_reply_edit_reason() {
	echo bbp_get_form_reply_edit_reason();
}
	/**
	 * Return the value of the reply edit reason
	 *
	 * @since bbPress (r31301)
	 *
	 * @uses apply_filters() Calls 'bbp_get_form_reply_edit_reason' with the
	 *                        reply edit reason value
	 * @return string Reply edit reason value
	 */
	function bbp_get_form_reply_edit_reason() {

		// Get _POST data
		if ( bbp_is_post_request() && isset( $_POST['bbp_reply_edit_reason'] ) ) {
			$reply_edit_reason = $_POST['bbp_reply_edit_reason'];

		// No data
		} else {
			$reply_edit_reason = '';
		}

		return apply_filters( 'bbp_get_form_reply_edit_reason', esc_attr( $reply_edit_reason ) );
	}
