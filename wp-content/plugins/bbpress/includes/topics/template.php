<?php

/**
 * bbPress Topic Template Tags
 *
 * @package bbPress
 * @subpackage TemplateTags
 */

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

/** Post Type *****************************************************************/

/**
 * Output the unique id of the custom post type for topics
 *
 * @since bbPress (r2857)
 *
 * @uses bbp_get_topic_post_type() To get the topic post type
 */
function bbp_topic_post_type() {
	echo bbp_get_topic_post_type();
}
	/**
	 * Return the unique id of the custom post type for topics
	 *
	 * @since bbPress (r2857)
	 *
	 * @uses apply_filters() Calls 'bbp_get_topic_post_type' with the topic
	 *                        post type id
	 * @return string The unique topic post type id
	 */
	function bbp_get_topic_post_type() {
		return apply_filters( 'bbp_get_topic_post_type', bbpress()->topic_post_type );
	}

/**
 * Return array of labels used by the topic post type
 *
 * @since bbPress (r5129)
 *
 * @return array
 */
function bbp_get_topic_post_type_labels() {
	return apply_filters( 'bbp_get_topic_post_type_labels', array(
		'name'               => __( 'Topics',                   'bbpress' ),
		'menu_name'          => __( 'Topics',                   'bbpress' ),
		'singular_name'      => __( 'Topic',                    'bbpress' ),
		'all_items'          => __( 'All Topics',               'bbpress' ),
		'add_new'            => __( 'New Topic',                'bbpress' ),
		'add_new_item'       => __( 'Create New Topic',         'bbpress' ),
		'edit'               => __( 'Edit',                     'bbpress' ),
		'edit_item'          => __( 'Edit Topic',               'bbpress' ),
		'new_item'           => __( 'New Topic',                'bbpress' ),
		'view'               => __( 'View Topic',               'bbpress' ),
		'view_item'          => __( 'View Topic',               'bbpress' ),
		'search_items'       => __( 'Search Topics',            'bbpress' ),
		'not_found'          => __( 'No topics found',          'bbpress' ),
		'not_found_in_trash' => __( 'No topics found in Trash', 'bbpress' ),
		'parent_item_colon'  => __( 'Forum:',                   'bbpress' )
	) );
}

/**
 * Return array of topic post type rewrite settings
 *
 * @since bbPress (r5129)
 *
 * @return array
 */
function bbp_get_topic_post_type_rewrite() {
	return apply_filters( 'bbp_get_topic_post_type_rewrite', array(
		'slug'       => bbp_get_topic_slug(),
		'with_front' => false
	) );
}

/**
 * Return array of features the topic post type supports
 *
 * @since bbPress (r5129)
 *
 * @return array
 */
function bbp_get_topic_post_type_supports() {
	return apply_filters( 'bbp_get_topic_post_type_supports', array(
		'title',
		'editor',
		'revisions'
	) );
}

/**
 * The plugin version of bbPress comes with two topic display options:
 * - Traditional: Topics are included in the reply loop (default)
 * - New Style: Topics appear as "lead" posts, ahead of replies
 *
 * @since bbPress (r2954)
 * @param $show_lead Optional. Default false
 * @return bool Yes if the topic appears as a lead, otherwise false
 */
function bbp_show_lead_topic( $show_lead = false ) {

	// Never separate the lead topic in feeds
	if ( is_feed() )
		return false;

	return (bool) apply_filters( 'bbp_show_lead_topic', (bool) $show_lead );
}

/** Topic Loop ****************************************************************/

/**
 * The main topic loop. WordPress makes this easy for us
 *
 * @since bbPress (r2485)
 *
 * @param mixed $args All the arguments supported by {@link WP_Query}
 * @uses current_user_can() To check if the current user can edit other's topics
 * @uses bbp_get_topic_post_type() To get the topic post type
 * @uses WP_Query To make query and get the topics
 * @uses is_page() To check if it's a page
 * @uses bbp_is_single_forum() To check if it's a forum
 * @uses bbp_get_forum_id() To get the forum id
 * @uses bbp_get_paged() To get the current page value
 * @uses bbp_get_super_stickies() To get the super stickies
 * @uses bbp_get_stickies() To get the forum stickies
 * @uses wpdb::get_results() To execute our query and get the results
 * @uses WP_Rewrite::using_permalinks() To check if the blog is using permalinks
 * @uses get_permalink() To get the permalink
 * @uses add_query_arg() To add custom args to the url
 * @uses apply_filters() Calls 'bbp_topics_pagination' with the pagination args
 * @uses paginate_links() To paginate the links
 * @uses apply_filters() Calls 'bbp_has_topics' with
 *                        bbPres::topic_query::have_posts()
 *                        and bbPres::topic_query
 * @return object Multidimensional array of topic information
 */
function bbp_has_topics( $args = '' ) {
	global $wp_rewrite;

	/** Defaults **************************************************************/

	// Other defaults
	$default_topic_search  = !empty( $_REQUEST['ts'] ) ? $_REQUEST['ts'] : false;
	$default_show_stickies = (bool) ( bbp_is_single_forum() || bbp_is_topic_archive() ) && ( false === $default_topic_search );
	$default_post_parent   = bbp_is_single_forum() ? bbp_get_forum_id() : 'any';

	// Default argument array
	$default = array(
		'post_type'      => bbp_get_topic_post_type(), // Narrow query down to bbPress topics
		'post_parent'    => $default_post_parent,      // Forum ID
		'meta_key'       => '_bbp_last_active_time',   // Make sure topic has some last activity time
		'orderby'        => 'meta_value',              // 'meta_value', 'author', 'date', 'title', 'modified', 'parent', rand',
		'order'          => 'DESC',                    // 'ASC', 'DESC'
		'posts_per_page' => bbp_get_topics_per_page(), // Topics per page
		'paged'          => bbp_get_paged(),           // Page Number
		's'              => $default_topic_search,     // Topic Search
		'show_stickies'  => $default_show_stickies,    // Ignore sticky topics?
		'max_num_pages'  => false,                     // Maximum number of pages to show
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
		if ( current_user_can( 'read_private_topics' ) ) {
			$post_statuses[] = bbp_get_private_status_id();
		}

		// Join post statuses together
		$default['post_status'] = implode( ',', $post_statuses );

	// Lean on the 'perm' query var value of 'readable' to provide statuses
	} else {
		$default['perm'] = 'readable';
	}

	// Maybe query for topic tags
	if ( bbp_is_topic_tag() ) {
		$default['term']     = bbp_get_topic_tag_slug();
		$default['taxonomy'] = bbp_get_topic_tag_tax_id();
	}

	/** Setup *****************************************************************/

	// Parse arguments against default values
	$r = bbp_parse_args( $args, $default, 'has_topics' );

	// Get bbPress
	$bbp = bbpress();

	// Call the query
	$bbp->topic_query = new WP_Query( $r );

	// Set post_parent back to 0 if originally set to 'any'
	if ( 'any' === $r['post_parent'] )
		$r['post_parent'] = 0;

	// Limited the number of pages shown
	if ( !empty( $r['max_num_pages'] ) )
		$bbp->topic_query->max_num_pages = $r['max_num_pages'];

	/** Stickies **************************************************************/

	// Put sticky posts at the top of the posts array
	if ( !empty( $r['show_stickies'] ) && $r['paged'] <= 1 ) {

		// Get super stickies and stickies in this forum
		$stickies = bbp_get_super_stickies();

		// Get stickies for current forum
		if ( !empty( $r['post_parent'] ) ) {
			$stickies = array_merge( $stickies, bbp_get_stickies( $r['post_parent'] ) );
		}

		// Remove any duplicate stickies
		$stickies = array_unique( $stickies );

		// We have stickies
		if ( is_array( $stickies ) && !empty( $stickies ) ) {

			// Start the offset at -1 so first sticky is at correct 0 offset
			$sticky_offset = -1;

			// Loop over topics and relocate stickies to the front.
			foreach ( $stickies as $sticky_index => $sticky_ID ) {

				// Get the post offset from the posts array
				$post_offsets = wp_filter_object_list( $bbp->topic_query->posts, array( 'ID' => $sticky_ID ), 'OR', 'ID' );

				// Continue if no post offsets
				if ( empty( $post_offsets ) ) {
					continue;
				}

				// Loop over posts in current query and splice them into position
				foreach ( array_keys( $post_offsets ) as $post_offset ) {
					$sticky_offset++;

					$sticky = $bbp->topic_query->posts[$post_offset];

					// Remove sticky from current position
					array_splice( $bbp->topic_query->posts, $post_offset, 1 );

					// Move to front, after other stickies
					array_splice( $bbp->topic_query->posts, $sticky_offset, 0, array( $sticky ) );

					// Cleanup
					unset( $stickies[$sticky_index] );
					unset( $sticky );
				}

				// Cleanup
				unset( $post_offsets );
			}

			// Cleanup
			unset( $sticky_offset );

			// If any posts have been excluded specifically, Ignore those that are sticky.
			if ( !empty( $stickies ) && !empty( $r['post__not_in'] ) ) {
				$stickies = array_diff( $stickies, $r['post__not_in'] );
			}

			// Fetch sticky posts that weren't in the query results
			if ( !empty( $stickies ) ) {

				// Query to use in get_posts to get sticky posts
				$sticky_query = array(
					'post_type'   => bbp_get_topic_post_type(),
					'post_parent' => 'any',
					'meta_key'    => '_bbp_last_active_time',
					'orderby'     => 'meta_value',
					'order'       => 'DESC',
					'include'     => $stickies
				);

				// Cleanup
				unset( $stickies );

				// Conditionally exclude private/hidden forum ID's
				$exclude_forum_ids = bbp_exclude_forum_ids( 'array' );
				if ( ! empty( $exclude_forum_ids ) ) {
					$sticky_query['post_parent__not_in'] = $exclude_forum_ids;
				}

				// What are the default allowed statuses (based on user caps)
				if ( bbp_get_view_all() ) {
					$sticky_query['post_status'] = $r['post_status'];

				// Lean on the 'perm' query var value of 'readable' to provide statuses
				} else {
					$sticky_query['post_status'] = $r['perm'];
				}

				// Get all stickies
				$sticky_posts = get_posts( $sticky_query );
				if ( !empty( $sticky_posts ) ) {

					// Get a count of the visible stickies
					$sticky_count = count( $sticky_posts );

					// Merge the stickies topics with the query topics .
					$bbp->topic_query->posts       = array_merge( $sticky_posts, $bbp->topic_query->posts );

					// Adjust loop and counts for new sticky positions
					$bbp->topic_query->found_posts = (int) $bbp->topic_query->found_posts + (int) $sticky_count;
					$bbp->topic_query->post_count  = (int) $bbp->topic_query->post_count  + (int) $sticky_count;

					// Cleanup
					unset( $sticky_posts );
				}
			}
		}
	}

	// If no limit to posts per page, set it to the current post_count
	if ( -1 === $r['posts_per_page'] )
		$r['posts_per_page'] = $bbp->topic_query->post_count;

	// Add pagination values to query object
	$bbp->topic_query->posts_per_page = $r['posts_per_page'];
	$bbp->topic_query->paged          = $r['paged'];

	// Only add pagination if query returned results
	if ( ( (int) $bbp->topic_query->post_count || (int) $bbp->topic_query->found_posts ) && (int) $bbp->topic_query->posts_per_page ) {

		// Limit the number of topics shown based on maximum allowed pages
		if ( ( !empty( $r['max_num_pages'] ) ) && $bbp->topic_query->found_posts > $bbp->topic_query->max_num_pages * $bbp->topic_query->post_count )
			$bbp->topic_query->found_posts = $bbp->topic_query->max_num_pages * $bbp->topic_query->post_count;

		// If pretty permalinks are enabled, make our pagination pretty
		if ( $wp_rewrite->using_permalinks() ) {

			// User's topics
			if ( bbp_is_single_user_topics() ) {
				$base = bbp_get_user_topics_created_url( bbp_get_displayed_user_id() );

			// User's favorites
			} elseif ( bbp_is_favorites() ) {
				$base = bbp_get_favorites_permalink( bbp_get_displayed_user_id() );

			// User's subscriptions
			} elseif ( bbp_is_subscriptions() ) {
				$base = bbp_get_subscriptions_permalink( bbp_get_displayed_user_id() );

			// Root profile page
			} elseif ( bbp_is_single_user() ) {
				$base = bbp_get_user_profile_url( bbp_get_displayed_user_id() );

			// View
			} elseif ( bbp_is_single_view() ) {
				$base = bbp_get_view_url();

			// Topic tag
			} elseif ( bbp_is_topic_tag() ) {
				$base = bbp_get_topic_tag_link();

			// Page or single post
			} elseif ( is_page() || is_single() ) {
				$base = get_permalink();

			// Forum archive
			} elseif ( bbp_is_forum_archive() ) {
				$base = bbp_get_forums_url();

			// Topic archive
			} elseif ( bbp_is_topic_archive() ) {
				$base = bbp_get_topics_url();

			// Default
			} else {
				$base = get_permalink( (int) $r['post_parent'] );
			}

			// Use pagination base
			$base = trailingslashit( $base ) . user_trailingslashit( $wp_rewrite->pagination_base . '/%#%/' );

		// Unpretty pagination
		} else {
			$base = add_query_arg( 'paged', '%#%' );
		}

		// Pagination settings with filter
		$bbp_topic_pagination = apply_filters( 'bbp_topic_pagination', array (
			'base'      => $base,
			'format'    => '',
			'total'     => $r['posts_per_page'] === $bbp->topic_query->found_posts ? 1 : ceil( (int) $bbp->topic_query->found_posts / (int) $r['posts_per_page'] ),
			'current'   => (int) $bbp->topic_query->paged,
			'prev_text' => is_rtl() ? '&rarr;' : '&larr;',
			'next_text' => is_rtl() ? '&larr;' : '&rarr;',
			'mid_size'  => 1
		) );

		// Add pagination to query object
		$bbp->topic_query->pagination_links = paginate_links( $bbp_topic_pagination );

		// Remove first page from pagination
		$bbp->topic_query->pagination_links = str_replace( $wp_rewrite->pagination_base . "/1/'", "'", $bbp->topic_query->pagination_links );
	}

	// Return object
	return apply_filters( 'bbp_has_topics', $bbp->topic_query->have_posts(), $bbp->topic_query );
}

/**
 * Whether there are more topics available in the loop
 *
 * @since bbPress (r2485)
 *
 * @uses WP_Query bbPress::topic_query::have_posts()
 * @return object Topic information
 */
function bbp_topics() {

	// Put into variable to check against next
	$have_posts = bbpress()->topic_query->have_posts();

	// Reset the post data when finished
	if ( empty( $have_posts ) )
		wp_reset_postdata();

	return $have_posts;
}

/**
 * Loads up the current topic in the loop
 *
 * @since bbPress (r2485)
 *
 * @uses WP_Query bbPress::topic_query::the_post()
 * @return object Topic information
 */
function bbp_the_topic() {
	return bbpress()->topic_query->the_post();
}

/**
 * Output the topic id
 *
 * @since bbPress (r2485)
 *
 * @uses bbp_get_topic_id() To get the topic id
 */
function bbp_topic_id( $topic_id = 0) {
	echo bbp_get_topic_id( $topic_id );
}
	/**
	 * Return the topic id
	 *
	 * @since bbPress (r2485)
	 *
	 * @param $topic_id Optional. Used to check emptiness
	 * @uses bbPress::topic_query::post::ID To get the topic id
	 * @uses bbp_is_topic() To check if the search result is a topic
	 * @uses bbp_is_single_topic() To check if it's a topic page
	 * @uses bbp_is_topic_edit() To check if it's a topic edit page
	 * @uses bbp_is_single_reply() To check if it it's a reply page
	 * @uses bbp_is_reply_edit() To check if it's a reply edit page
	 * @uses bbp_get_reply_topic_edit() To get the reply topic id
	 * @uses get_post_field() To get the post's post type
	 * @uses WP_Query::post::ID To get the topic id
	 * @uses bbp_get_topic_post_type() To get the topic post type
	 * @uses apply_filters() Calls 'bbp_get_topic_id' with the topic id and
	 *                        supplied topic id
	 * @return int The topic id
	 */
	function bbp_get_topic_id( $topic_id = 0 ) {
		global $wp_query;

		$bbp = bbpress();

		// Easy empty checking
		if ( !empty( $topic_id ) && is_numeric( $topic_id ) ) {
			$bbp_topic_id = $topic_id;

		// Currently inside a topic loop
		} elseif ( !empty( $bbp->topic_query->in_the_loop ) && isset( $bbp->topic_query->post->ID ) ) {
			$bbp_topic_id = $bbp->topic_query->post->ID;

		// Currently inside a search loop
		} elseif ( !empty( $bbp->search_query->in_the_loop ) && isset( $bbp->search_query->post->ID ) && bbp_is_topic( $bbp->search_query->post->ID ) ) {
			$bbp_topic_id = $bbp->search_query->post->ID;

		// Currently viewing/editing a topic, likely alone
		} elseif ( ( bbp_is_single_topic() || bbp_is_topic_edit() ) && !empty( $bbp->current_topic_id ) ) {
			$bbp_topic_id = $bbp->current_topic_id;

		// Currently viewing/editing a topic, likely in a loop
		} elseif ( ( bbp_is_single_topic() || bbp_is_topic_edit() ) && isset( $wp_query->post->ID ) ) {
			$bbp_topic_id = $wp_query->post->ID;

		// Currently viewing/editing a reply
		} elseif ( bbp_is_single_reply() || bbp_is_reply_edit() ) {
			$bbp_topic_id = bbp_get_reply_topic_id();

		// Fallback
		} else {
			$bbp_topic_id = 0;
		}

		return (int) apply_filters( 'bbp_get_topic_id', (int) $bbp_topic_id, $topic_id );
	}

/**
 * Gets a topic
 *
 * @since bbPress (r2787)
 *
 * @param int|object $topic Topic id or topic object
 * @param string $output Optional. OBJECT, ARRAY_A, or ARRAY_N. Default = OBJECT
 * @param string $filter Optional Sanitation filter. See {@link sanitize_post()}
 * @uses get_post() To get the topic
 * @uses apply_filters() Calls 'bbp_get_topic' with the topic, output type and
 *                        sanitation filter
 * @return mixed Null if error or topic (in specified form) if success
 */
function bbp_get_topic( $topic, $output = OBJECT, $filter = 'raw' ) {

	// Use topic ID
	if ( empty( $topic ) || is_numeric( $topic ) )
		$topic = bbp_get_topic_id( $topic );

	// Attempt to load the topic
	$topic = get_post( $topic, OBJECT, $filter );
	if ( empty( $topic ) )
		return $topic;

	// Bail if post_type is not a topic
	if ( $topic->post_type !== bbp_get_topic_post_type() )
		return null;

	// Tweak the data type to return
	if ( $output === OBJECT ) {
		return $topic;

	} elseif ( $output === ARRAY_A ) {
		$_topic = get_object_vars( $topic );
		return $_topic;

	} elseif ( $output === ARRAY_N ) {
		$_topic = array_values( get_object_vars( $topic ) );
		return $_topic;

	}

	return apply_filters( 'bbp_get_topic', $topic, $output, $filter );
}

/**
 * Output the link to the topic in the topic loop
 *
 * @since bbPress (r2485)
 *
 * @param int $topic_id Optional. Topic id
 * @param $string $redirect_to Optional. Pass a redirect value for use with
 *                              shortcodes and other fun things.
 * @uses bbp_get_topic_permalink() To get the topic permalink
 */
function bbp_topic_permalink( $topic_id = 0, $redirect_to = '' ) {
	echo esc_url( bbp_get_topic_permalink( $topic_id, $redirect_to ) );
}
	/**
	 * Return the link to the topic
	 *
	 * @since bbPress (r2485)
	 *
	 * @param int $topic_id Optional. Topic id
	 * @param $string $redirect_to Optional. Pass a redirect value for use with
	 *                              shortcodes and other fun things.
	 * @uses bbp_get_topic_id() To get the topic id
	 * @uses get_permalink() To get the topic permalink
	 * @uses esc_url_raw() To clean the redirect_to url
	 * @uses apply_filters() Calls 'bbp_get_topic_permalink' with the link
	 *                        and topic id
	 * @return string Permanent link to topic
	 */
	function bbp_get_topic_permalink( $topic_id = 0, $redirect_to = '' ) {
		$topic_id = bbp_get_topic_id( $topic_id );

		// Use the redirect address
		if ( !empty( $redirect_to ) ) {
			$topic_permalink = esc_url_raw( $redirect_to );

		// Use the topic permalink
		} else {
			$topic_permalink = get_permalink( $topic_id );
		}

		return apply_filters( 'bbp_get_topic_permalink', $topic_permalink, $topic_id );
	}

/**
 * Output the title of the topic
 *
 * @since bbPress (r2485)
 *
 * @param int $topic_id Optional. Topic id
 * @uses bbp_get_topic_title() To get the topic title
 */
function bbp_topic_title( $topic_id = 0 ) {
	echo bbp_get_topic_title( $topic_id );
}
	/**
	 * Return the title of the topic
	 *
	 * @since bbPress (r2485)
	 *
	 * @param int $topic_id Optional. Topic id
	 * @uses bbp_get_topic_id() To get the topic id
	 * @uses get_the_title() To get the title
	 * @uses apply_filters() Calls 'bbp_get_topic_title' with the title and
	 *                        topic id
	 * @return string Title of topic
	 */
	function bbp_get_topic_title( $topic_id = 0 ) {
		$topic_id = bbp_get_topic_id( $topic_id );
		$title    = get_the_title( $topic_id );

		return apply_filters( 'bbp_get_topic_title', $title, $topic_id );
	}

/**
 * Output the topic archive title
 *
 * @since bbPress (r3249)
 *
 * @param string $title Default text to use as title
 */
function bbp_topic_archive_title( $title = '' ) {
	echo bbp_get_topic_archive_title( $title );
}
	/**
	 * Return the topic archive title
	 *
	 * @since bbPress (r3249)
	 *
	 * @param string $title Default text to use as title
	 *
	 * @uses bbp_get_page_by_path() Check if page exists at root path
	 * @uses get_the_title() Use the page title at the root path
	 * @uses get_post_type_object() Load the post type object
	 * @uses bbp_get_topic_post_type() Get the topic post type ID
	 * @uses get_post_type_labels() Get labels for topic post type
	 * @uses apply_filters() Allow output to be manipulated
	 *
	 * @return string The topic archive title
	 */
	function bbp_get_topic_archive_title( $title = '' ) {

		// If no title was passed
		if ( empty( $title ) ) {

			// Set root text to page title
			$page = bbp_get_page_by_path( bbp_get_topic_archive_slug() );
			if ( !empty( $page ) ) {
				$title = get_the_title( $page->ID );

			// Default to topic post type name label
			} else {
				$tto    = get_post_type_object( bbp_get_topic_post_type() );
				$title  = $tto->labels->name;
			}
		}

		return apply_filters( 'bbp_get_topic_archive_title', $title );
	}

/**
 * Output the content of the topic
 *
 * @since bbPress (r2780)
 *
 * @param int $topic_id Optional. Topic id
 * @uses bbp_get_topic_content() To get the topic content
 */
function bbp_topic_content( $topic_id = 0 ) {
	echo bbp_get_topic_content( $topic_id );
}
	/**
	 * Return the content of the topic
	 *
	 * @since bbPress (r2780)
	 *
	 * @param int $topic_id Optional. Topic id
	 * @uses bbp_get_topic_id() To get the topic id
	 * @uses post_password_required() To check if the topic requires pass
	 * @uses get_the_password_form() To get the password form
	 * @uses get_post_field() To get the content post field
	 * @uses apply_filters() Calls 'bbp_get_topic_content' with the content
	 *                        and topic id
	 * @return string Content of the topic
	 */
	function bbp_get_topic_content( $topic_id = 0 ) {
		$topic_id = bbp_get_topic_id( $topic_id );

		// Check if password is required
		if ( post_password_required( $topic_id ) )
			return get_the_password_form();

		$content = get_post_field( 'post_content', $topic_id );

		return apply_filters( 'bbp_get_topic_content', $content, $topic_id );
	}

/**
 * Output the excerpt of the topic
 *
 * @since bbPress (r2780)
 *
 * @param int $topic_id Optional. Topic id
 * @param int $length Optional. Length of the excerpt. Defaults to 100 letters
 * @uses bbp_get_topic_excerpt() To get the topic excerpt
 */
function bbp_topic_excerpt( $topic_id = 0, $length = 100 ) {
	echo bbp_get_topic_excerpt( $topic_id, $length );
}
	/**
	 * Return the excerpt of the topic
	 *
	 * @since bbPress (r2780)
	 *
	 * @param int $topic_id Optional. topic id
	 * @param int $length Optional. Length of the excerpt. Defaults to 100
	 *                     letters
	 * @uses bbp_get_topic_id() To get the topic id
	 * @uses get_post_field() To get the excerpt
	 * @uses bbp_get_topic_content() To get the topic content
	 * @uses apply_filters() Calls 'bbp_get_topic_excerpt' with the excerpt,
	 *                        topic id and length
	 * @return string topic Excerpt
	 */
	function bbp_get_topic_excerpt( $topic_id = 0, $length = 100 ) {
		$topic_id = bbp_get_topic_id( $topic_id );
		$length   = (int) $length;
		$excerpt  = get_post_field( 'post_excerpt', $topic_id );

		if ( empty( $excerpt ) ) {
			$excerpt = bbp_get_topic_content( $topic_id );
		}

		$excerpt = trim( strip_tags( $excerpt ) );

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

		return apply_filters( 'bbp_get_topic_excerpt', $excerpt, $topic_id, $length );
	}

/**
 * Output the post date and time of a topic
 *
 * @since bbPress (r4155)
 *
 * @param int $topic_id Optional. Topic id.
 * @param bool $humanize Optional. Humanize output using time_since
 * @param bool $gmt Optional. Use GMT
 * @uses bbp_get_topic_post_date() to get the output
 */
function bbp_topic_post_date( $topic_id = 0, $humanize = false, $gmt = false ) {
	echo bbp_get_topic_post_date( $topic_id, $humanize, $gmt );
}
	/**
	 * Return the post date and time of a topic
	 *
	 * @since bbPress (r4155)
	 *
	 * @param int $topic_id Optional. Topic id.
	 * @param bool $humanize Optional. Humanize output using time_since
	 * @param bool $gmt Optional. Use GMT
	 * @uses bbp_get_topic_id() To get the topic id
	 * @uses get_post_time() to get the topic post time
	 * @uses bbp_get_time_since() to maybe humanize the topic post time
	 * @return string
	 */
	function bbp_get_topic_post_date( $topic_id = 0, $humanize = false, $gmt = false ) {
		$topic_id = bbp_get_topic_id( $topic_id );

		// 4 days, 4 hours ago
		if ( !empty( $humanize ) ) {
			$gmt_s  = !empty( $gmt ) ? 'U' : 'G';
			$date   = get_post_time( $gmt_s, $gmt, $topic_id );
			$time   = false; // For filter below
			$result = bbp_get_time_since( $date );

		// August 4, 2012 at 2:37 pm
		} else {
			$date   = get_post_time( get_option( 'date_format' ), $gmt, $topic_id, true );
			$time   = get_post_time( get_option( 'time_format' ), $gmt, $topic_id, true );
			$result = sprintf( _x( '%1$s at %2$s', 'date at time', 'bbpress' ), $date, $time );
		}

		return apply_filters( 'bbp_get_topic_post_date', $result, $topic_id, $humanize, $gmt, $date, $time );
	}

/**
 * Output pagination links of a topic within the topic loop
 *
 * @since bbPress (r2966)
 *
 * @param mixed $args See {@link bbp_get_topic_pagination()}
 * @uses bbp_get_topic_pagination() To get the topic pagination links
 */
function bbp_topic_pagination( $args = '' ) {
	echo bbp_get_topic_pagination( $args );
}
	/**
	 * Returns pagination links of a topic within the topic loop
	 *
	 * @since bbPress (r2966)
	 *
	 * @param mixed $args This function supports these arguments:
	 *  - topic_id: Topic id
	 *  - before: Before the links
	 *  - after: After the links
	 * @uses bbp_get_topic_id() To get the topic id
	 * @uses WP_Rewrite::using_permalinks() To check if the blog is using
	 *                                       permalinks
	 * @uses user_trailingslashit() To add a trailing slash
	 * @uses trailingslashit() To add a trailing slash
	 * @uses get_permalink() To get the permalink of the topic
	 * @uses add_query_arg() To add query args
	 * @uses bbp_get_topic_reply_count() To get topic reply count
	 * @uses bbp_show_topic_lead() Are we showing the topic as a lead?
	 * @uses get_option() To get replies per page option
	 * @uses paginate_links() To paginate the links
	 * @uses apply_filters() Calls 'bbp_get_topic_pagination' with the links
	 *                        and arguments
	 * @return string Pagination links
	 */
	function bbp_get_topic_pagination( $args = '' ) {
		global $wp_rewrite;

		// Bail if threading replies
		if ( bbp_thread_replies() ) {
			return;
		}

		// Parse arguments against default values
		$r = bbp_parse_args( $args, array(
			'topic_id' => bbp_get_topic_id(),
			'before'   => '<span class="bbp-topic-pagination">',
			'after'    => '</span>',
		), 'get_topic_pagination' );

		// If pretty permalinks are enabled, make our pagination pretty
		if ( $wp_rewrite->using_permalinks() ) {
			$base = trailingslashit( get_permalink( $r['topic_id'] ) ) . user_trailingslashit( $wp_rewrite->pagination_base . '/%#%/' );
		} else {
			$base = add_query_arg( 'paged', '%#%', get_permalink( $r['topic_id'] ) );
		}

		// Get total and add 1 if topic is included in the reply loop
		$total = bbp_get_topic_reply_count( $r['topic_id'], true );

		// Bump if topic is in loop
		if ( !bbp_show_lead_topic() )
			$total++;

		// Pagination settings
		$pagination = array(
			'base'      => $base,
			'format'    => '',
			'total'     => ceil( (int) $total / (int) bbp_get_replies_per_page() ),
			'current'   => 0,
			'prev_next' => false,
			'mid_size'  => 2,
			'end_size'  => 3,
			'add_args'  => ( bbp_get_view_all() ) ? array( 'view' => 'all' ) : false
		);

		// Add pagination to query object
		$pagination_links = paginate_links( $pagination );
		if ( !empty( $pagination_links ) ) {

			// Remove first page from pagination
			if ( $wp_rewrite->using_permalinks() ) {
				$pagination_links = str_replace( $wp_rewrite->pagination_base . '/1/', '', $pagination_links );
			} else {
				$pagination_links = str_replace( '&#038;paged=1', '', $pagination_links );
			}

			// Add before and after to pagination links
			$pagination_links = $r['before'] . $pagination_links . $r['after'];
		}

		return apply_filters( 'bbp_get_topic_pagination', $pagination_links, $args );
	}

/**
 * Append revisions to the topic content
 *
 * @since bbPress (r2782)
 *
 * @param string $content Optional. Content to which we need to append the revisions to
 * @param int $topic_id Optional. Topic id
 * @uses bbp_get_topic_revision_log() To get the topic revision log
 * @uses apply_filters() Calls 'bbp_topic_append_revisions' with the processed
 *                        content, original content and topic id
 * @return string Content with the revisions appended
 */
function bbp_topic_content_append_revisions( $content = '', $topic_id = 0 ) {

	// Bail if in admin or feed
	if ( is_admin() || is_feed() )
		return;

	// Validate the ID
	$topic_id = bbp_get_topic_id( $topic_id );

	return apply_filters( 'bbp_topic_append_revisions', $content . bbp_get_topic_revision_log( $topic_id ), $content, $topic_id );
}

/**
 * Output the revision log of the topic
 *
 * @since bbPress (r2782)
 *
 * @param int $topic_id Optional. Topic id
 * @uses bbp_get_topic_revision_log() To get the topic revision log
 */
function bbp_topic_revision_log( $topic_id = 0 ) {
	echo bbp_get_topic_revision_log( $topic_id );
}
	/**
	 * Return the formatted revision log of the topic
	 *
	 * @since bbPress (r2782)
	 *
	 * @param int $topic_id Optional. Topic id
	 * @uses bbp_get_topic_id() To get the topic id
	 * @uses bbp_get_topic_revisions() To get the topic revisions
	 * @uses bbp_get_topic_raw_revision_log() To get the raw revision log
	 * @uses bbp_get_topic_author_display_name() To get the topic author
	 * @uses bbp_get_author_link() To get the topic author link
	 * @uses bbp_convert_date() To convert the date
	 * @uses bbp_get_time_since() To get the time in since format
	 * @uses apply_filters() Calls 'bbp_get_topic_revision_log' with the
	 *                        log and topic id
	 * @return string Revision log of the topic
	 */
	function bbp_get_topic_revision_log( $topic_id = 0 ) {
		// Create necessary variables
		$topic_id     = bbp_get_topic_id( $topic_id );
		$revision_log = bbp_get_topic_raw_revision_log( $topic_id );

		if ( empty( $topic_id ) || empty( $revision_log ) || !is_array( $revision_log ) )
			return false;

		$revisions = bbp_get_topic_revisions( $topic_id );
		if ( empty( $revisions ) )
			return false;

		$r = "\n\n" . '<ul id="bbp-topic-revision-log-' . esc_attr( $topic_id ) . '" class="bbp-topic-revision-log">' . "\n\n";

		// Loop through revisions
		foreach ( (array) $revisions as $revision ) {

			if ( empty( $revision_log[$revision->ID] ) ) {
				$author_id = $revision->post_author;
				$reason    = '';
			} else {
				$author_id = $revision_log[$revision->ID]['author'];
				$reason    = $revision_log[$revision->ID]['reason'];
			}

			$author = bbp_get_author_link( array( 'size' => 14, 'link_text' => bbp_get_topic_author_display_name( $revision->ID ), 'post_id' => $revision->ID ) );
			$since  = bbp_get_time_since( bbp_convert_date( $revision->post_modified ) );

			$r .= "\t" . '<li id="bbp-topic-revision-log-' . esc_attr( $topic_id ) . '-item-' . esc_attr( $revision->ID ) . '" class="bbp-topic-revision-log-item">' . "\n";
			if ( !empty( $reason ) ) {
				$r .= "\t\t" . sprintf( __( 'This topic was modified %1$s by %2$s. Reason: %3$s', 'bbpress' ), esc_html( $since ), $author, esc_html( $reason ) ) . "\n";
			} else {
				$r .= "\t\t" . sprintf( __( 'This topic was modified %1$s by %2$s.', 'bbpress' ), esc_html( $since ), $author ) . "\n";
			}
			$r .= "\t" . '</li>' . "\n";

		}

		$r .= "\n" . '</ul>' . "\n\n";

		return apply_filters( 'bbp_get_topic_revision_log', $r, $topic_id );
	}
		/**
		 * Return the raw revision log of the topic
		 *
		 * @since bbPress (r2782)
		 *
		 * @param int $topic_id Optional. Topic id
		 * @uses bbp_get_topic_id() To get the topic id
		 * @uses get_post_meta() To get the revision log meta
		 * @uses apply_filters() Calls 'bbp_get_topic_raw_revision_log'
		 *                        with the log and topic id
		 * @return string Raw revision log of the topic
		 */
		function bbp_get_topic_raw_revision_log( $topic_id = 0 ) {
			$topic_id = bbp_get_topic_id( $topic_id );

			$revision_log = get_post_meta( $topic_id, '_bbp_revision_log', true );
			$revision_log = empty( $revision_log ) ? array() : $revision_log;

			return apply_filters( 'bbp_get_topic_raw_revision_log', $revision_log, $topic_id );
		}

/**
 * Return the revisions of the topic
 *
 * @since bbPress (r2782)
 *
 * @param int $topic_id Optional. Topic id
 * @uses bbp_get_topic_id() To get the topic id
 * @uses wp_get_post_revisions() To get the topic revisions
 * @uses apply_filters() Calls 'bbp_get_topic_revisions'
 *                        with the revisions and topic id
 * @return string Topic revisions
 */
function bbp_get_topic_revisions( $topic_id = 0 ) {
	$topic_id  = bbp_get_topic_id( $topic_id );
	$revisions = wp_get_post_revisions( $topic_id, array( 'order' => 'ASC' ) );

	return apply_filters( 'bbp_get_topic_revisions', $revisions, $topic_id );
}

/**
 * Return the revision count of the topic
 *
 * @since bbPress (r2782)
 *
 * @param int $topic_id Optional. Topic id
 * @uses bbp_get_topic_revisions() To get the topic revisions
 * @uses apply_filters() Calls 'bbp_get_topic_revision_count'
 *                        with the revision count and topic id
 * @return string Topic revision count
 */
function bbp_get_topic_revision_count( $topic_id = 0, $integer = false ) {
	$count  = (int) count( bbp_get_topic_revisions( $topic_id ) );
	$filter = ( true === $integer ) ? 'bbp_get_topic_revision_count_int' : 'bbp_get_topic_revision_count';

	return apply_filters( $filter, $count, $topic_id );
}

/**
 * Output the status of the topic
 *
 * @since bbPress (r2667)
 *
 * @param int $topic_id Optional. Topic id
 * @uses bbp_get_topic_status() To get the topic status
 */
function bbp_topic_status( $topic_id = 0 ) {
	echo bbp_get_topic_status( $topic_id );
}
	/**
	 * Return the status of the topic
	 *
	 * @since bbPress (r2667)
	 *
	 * @param int $topic_id Optional. Topic id
	 * @uses bbp_get_topic_id() To get the topic id
	 * @uses get_post_status() To get the topic status
	 * @uses apply_filters() Calls 'bbp_get_topic_status' with the status
	 *                        and topic id
	 * @return string Status of topic
	 */
	function bbp_get_topic_status( $topic_id = 0 ) {
		$topic_id = bbp_get_topic_id( $topic_id );

		return apply_filters( 'bbp_get_topic_status', get_post_status( $topic_id ), $topic_id );
	}

/**
 * Is the topic open to new replies?
 *
 * @since bbPress (r2727)
 *
 * @uses bbp_get_topic_status()
 *
 * @param int $topic_id Optional. Topic id
 * @uses bbp_is_topic_closed() To check if the topic is closed
 * @return bool True if open, false if closed.
 */
function bbp_is_topic_open( $topic_id = 0 ) {
	return !bbp_is_topic_closed( $topic_id );
}

	/**
	 * Is the topic closed to new replies?
	 *
	 * @since bbPress (r2746)
	 *
	 * @param int $topic_id Optional. Topic id
	 * @uses bbp_get_topic_status() To get the topic status
	 * @uses apply_filters() Calls 'bbp_is_topic_closed' with the topic id
	 *
	 * @return bool True if closed, false if not.
	 */
	function bbp_is_topic_closed( $topic_id = 0 ) {
		$closed = bbp_get_topic_status( $topic_id ) === bbp_get_closed_status_id();
		return (bool) apply_filters( 'bbp_is_topic_closed', (bool) $closed, $topic_id );
	}

/**
 * Is the topic a sticky or super sticky?
 *
 * @since bbPress (r2754)
 *
 * @param int $topic_id Optional. Topic id
 * @param int $check_super Optional. If set to true and if the topic is not a
 *                           normal sticky, it is checked if it is a super
 *                           sticky or not. Defaults to true.
 * @uses bbp_get_topic_id() To get the topic id
 * @uses bbp_get_topic_forum_id() To get the topic forum id
 * @uses bbp_get_stickies() To get the stickies
 * @uses bbp_is_topic_super_sticky() To check if the topic is a super sticky
 * @return bool True if sticky or super sticky, false if not.
 */
function bbp_is_topic_sticky( $topic_id = 0, $check_super = true ) {
	$topic_id = bbp_get_topic_id( $topic_id );
	$forum_id = bbp_get_topic_forum_id( $topic_id );
	$stickies = bbp_get_stickies( $forum_id );

	if ( in_array( $topic_id, $stickies ) || ( !empty( $check_super ) && bbp_is_topic_super_sticky( $topic_id ) ) )
		return true;

	return false;
}

/**
 * Is the topic a super sticky?
 *
 * @since bbPress (r2754)
 *
 * @param int $topic_id Optional. Topic id
 * @uses bbp_get_topic_id() To get the topic id
 * @uses bbp_get_super_stickies() To get the super stickies
 * @return bool True if super sticky, false if not.
 */
function bbp_is_topic_super_sticky( $topic_id = 0 ) {
	$topic_id = bbp_get_topic_id( $topic_id );
	$stickies = bbp_get_super_stickies( $topic_id );

	return in_array( $topic_id, $stickies );
}

/**
 * Is the topic not spam or deleted?
 *
 * @since bbPress (r3496)
 *
 * @param int $topic_id Optional. Topic id
 * @uses bbp_get_topic_id() To get the topic id
 * @uses bbp_get_topic_status() To get the topic status
 * @uses apply_filters() Calls 'bbp_is_topic_published' with the topic id
 * @return bool True if published, false if not.
 */
function bbp_is_topic_published( $topic_id = 0 ) {
	$topic_status = bbp_get_topic_status( bbp_get_topic_id( $topic_id ) ) === bbp_get_public_status_id();
	return (bool) apply_filters( 'bbp_is_topic_published', (bool) $topic_status, $topic_id );
}

/**
 * Is the topic marked as spam?
 *
 * @since bbPress (r2727)
 *
 * @param int $topic_id Optional. Topic id
 * @uses bbp_get_topic_id() To get the topic id
 * @uses bbp_get_topic_status() To get the topic status
 * @uses apply_filters() Calls 'bbp_is_topic_spam' with the topic id
 * @return bool True if spam, false if not.
 */
function bbp_is_topic_spam( $topic_id = 0 ) {
	$topic_status = bbp_get_topic_status( bbp_get_topic_id( $topic_id ) ) === bbp_get_spam_status_id();
	return (bool) apply_filters( 'bbp_is_topic_spam', (bool) $topic_status, $topic_id );
}

/**
 * Is the topic trashed?
 *
 * @since bbPress (r2888)
 *
 * @param int $topic_id Optional. Topic id
 * @uses bbp_get_topic_id() To get the topic id
 * @uses bbp_get_topic_status() To get the topic status
 * @uses apply_filters() Calls 'bbp_is_topic_trash' with the topic id
 * @return bool True if trashed, false if not.
 */
function bbp_is_topic_trash( $topic_id = 0 ) {
	$topic_status = bbp_get_topic_status( bbp_get_topic_id( $topic_id ) ) === bbp_get_trash_status_id();
	return (bool) apply_filters( 'bbp_is_topic_trash', (bool) $topic_status, $topic_id );
}

/**
 * Is the posted by an anonymous user?
 *
 * @since bbPress (r2753)
 *
 * @param int $topic_id Optional. Topic id
 * @uses bbp_get_topic_id() To get the topic id
 * @uses bbp_get_topic_author_id() To get the topic author id
 * @uses get_post_meta() To get the anonymous user name and email meta
 * @uses apply_filters() Calls 'bbp_is_topic_anonymous' with the topic id
 * @return bool True if the post is by an anonymous user, false if not.
 */
function bbp_is_topic_anonymous( $topic_id = 0 ) {
	$topic_id = bbp_get_topic_id( $topic_id );
	$retval   = false;

	if ( !bbp_get_topic_author_id( $topic_id ) )
		$retval = true;

	elseif ( get_post_meta( $topic_id, '_bbp_anonymous_name',  true ) )
		$retval = true;

	elseif ( get_post_meta( $topic_id, '_bbp_anonymous_email', true ) )
		$retval = true;

	// The topic is by an anonymous user
	return (bool) apply_filters( 'bbp_is_topic_anonymous', $retval, $topic_id );
}

/**
 * Deprecated. Use bbp_topic_author_display_name() instead.
 *
 * Output the author of the topic.
 *
 * @since bbPress (r2590)
 * @deprecated bbPress (r5119)
 *
 * @param int $topic_id Optional. Topic id
 * @uses bbp_get_topic_author() To get the topic author
 */
function bbp_topic_author( $topic_id = 0 ) {
	echo bbp_get_topic_author( $topic_id );
}
	/**
	 * Deprecated. Use bbp_get_topic_author_display_name() instead.
	 *
	 * Return the author of the topic
	 *
	 * @since bbPress (r2590)
	 * @deprecated bbPress (r5119)
	 *
	 * @param int $topic_id Optional. Topic id
	 * @uses bbp_get_topic_id() To get the topic id
	 * @uses bbp_is_topic_anonymous() To check if the topic is by an
	 *                                 anonymous user
	 * @uses bbp_get_topic_author_id() To get the topic author id
	 * @uses get_the_author_meta() To get the display name of the author
	 * @uses get_post_meta() To get the name of the anonymous poster
	 * @uses apply_filters() Calls 'bbp_get_topic_author' with the author
	 *                        and topic id
	 * @return string Author of topic
	 */
	function bbp_get_topic_author( $topic_id = 0 ) {
		$topic_id = bbp_get_topic_id( $topic_id );

		if ( !bbp_is_topic_anonymous( $topic_id ) ) {
			$author = get_the_author_meta( 'display_name', bbp_get_topic_author_id( $topic_id ) );
		} else {
			$author = get_post_meta( $topic_id, '_bbp_anonymous_name', true );
		}

		return apply_filters( 'bbp_get_topic_author', $author, $topic_id );
	}

/**
 * Output the author ID of the topic
 *
 * @since bbPress (r2590)
 *
 * @param int $topic_id Optional. Topic id
 * @uses bbp_get_topic_author_id() To get the topic author id
 */
function bbp_topic_author_id( $topic_id = 0 ) {
	echo bbp_get_topic_author_id( $topic_id );
}
	/**
	 * Return the author ID of the topic
	 *
	 * @since bbPress (r2590)
	 *
	 * @param int $topic_id Optional. Topic id
	 * @uses bbp_get_topic_id() To get the topic id
	 * @uses get_post_field() To get the topic author id
	 * @uses apply_filters() Calls 'bbp_get_topic_author_id' with the author
	 *                        id and topic id
	 * @return string Author of topic
	 */
	function bbp_get_topic_author_id( $topic_id = 0 ) {
		$topic_id  = bbp_get_topic_id( $topic_id );
		$author_id = get_post_field( 'post_author', $topic_id );

		return (int) apply_filters( 'bbp_get_topic_author_id', (int) $author_id, $topic_id );
	}

/**
 * Output the author display_name of the topic
 *
 * @since bbPress (r2590)
 *
 * @param int $topic_id Optional. Topic id
 * @uses bbp_get_topic_author_display_name() To get the topic author's display
 *                                            name
 */
function bbp_topic_author_display_name( $topic_id = 0 ) {
	echo bbp_get_topic_author_display_name( $topic_id );
}
	/**
	 * Return the author display_name of the topic
	 *
	 * @since bbPress (r2485)
	 *
	 * @param int $topic_id Optional. Topic id
	 * @uses bbp_get_topic_id() To get the topic id
	 * @uses bbp_is_topic_anonymous() To check if the topic is by an
	 *                                 anonymous user
	 * @uses bbp_get_topic_author_id() To get the topic author id
	 * @uses get_the_author_meta() To get the author meta
	 * @uses get_post_meta() To get the anonymous user name
	 * @uses apply_filters() Calls 'bbp_get_topic_author_id' with the
	 *                        display name and topic id
	 * @return string Topic's author's display name
	 */
	function bbp_get_topic_author_display_name( $topic_id = 0 ) {
		$topic_id = bbp_get_topic_id( $topic_id );

		// Check for anonymous user
		if ( !bbp_is_topic_anonymous( $topic_id ) ) {

			// Get the author ID
			$author_id = bbp_get_topic_author_id( $topic_id );

			// Try to get a display name
			$author_name = get_the_author_meta( 'display_name', $author_id );

			// Fall back to user login
			if ( empty( $author_name ) ) {
				$author_name = get_the_author_meta( 'user_login', $author_id );
			}

		// User does not have an account
		} else {
			$author_name = get_post_meta( $topic_id, '_bbp_anonymous_name', true );
		}

		// If nothing could be found anywhere, use Anonymous
		if ( empty( $author_name ) )
			$author_name = __( 'Anonymous', 'bbpress' );

		// Encode possible UTF8 display names
		if ( seems_utf8( $author_name ) === false )
			$author_name = utf8_encode( $author_name );

		return apply_filters( 'bbp_get_topic_author_display_name', $author_name, $topic_id );
	}

/**
 * Output the author avatar of the topic
 *
 * @since bbPress (r2590)
 *
 * @param int $topic_id Optional. Topic id
 * @param int $size Optional. Avatar size. Defaults to 40
 * @uses bbp_get_topic_author_avatar() To get the topic author avatar
 */
function bbp_topic_author_avatar( $topic_id = 0, $size = 40 ) {
	echo bbp_get_topic_author_avatar( $topic_id, $size );
}
	/**
	 * Return the author avatar of the topic
	 *
	 * @since bbPress (r2590)
	 *
	 * @param int $topic_id Optional. Topic id
	 * @param int $size Optional. Avatar size. Defaults to 40
	 * @uses bbp_get_topic_id() To get the topic id
	 * @uses bbp_is_topic_anonymous() To check if the topic is by an
	 *                                 anonymous user
	 * @uses bbp_get_topic_author_id() To get the topic author id
	 * @uses get_post_meta() To get the anonymous user's email
	 * @uses get_avatar() To get the avatar
	 * @uses apply_filters() Calls 'bbp_get_topic_author_avatar' with the
	 *                        avatar, topic id and size
	 * @return string Avatar of the author of the topic
	 */
	function bbp_get_topic_author_avatar( $topic_id = 0, $size = 40 ) {
		$author_avatar = '';

		$topic_id = bbp_get_topic_id( $topic_id );
		if ( !empty( $topic_id ) ) {
			if ( !bbp_is_topic_anonymous( $topic_id ) ) {
				$author_avatar = get_avatar( bbp_get_topic_author_id( $topic_id ), $size );
			} else {
				$author_avatar = get_avatar( get_post_meta( $topic_id, '_bbp_anonymous_email', true ), $size );
			}
		}

		return apply_filters( 'bbp_get_topic_author_avatar', $author_avatar, $topic_id, $size );
	}

/**
 * Output the author link of the topic
 *
 * @since bbPress (r2717)
 *
 * @param mixed|int $args If it is an integer, it is used as topic_id. Optional.
 * @uses bbp_get_topic_author_link() To get the topic author link
 */
function bbp_topic_author_link( $args = '' ) {
	echo bbp_get_topic_author_link( $args );
}
	/**
	 * Return the author link of the topic
	 *
	 * @since bbPress (r2717)
	 *
	 * @param mixed|int $args If it is an integer, it is used as topic id.
	 *                         Optional.
	 * @uses bbp_get_topic_id() To get the topic id
	 * @uses bbp_get_topic_author_display_name() To get the topic author
	 * @uses bbp_is_topic_anonymous() To check if the topic is by an
	 *                                 anonymous user
	 * @uses bbp_get_topic_author_url() To get the topic author url
	 * @uses bbp_get_topic_author_avatar() To get the topic author avatar
	 * @uses bbp_get_topic_author_display_name() To get the topic author display
	 *                                      name
	 * @uses bbp_get_user_display_role() To get the topic author display role
	 * @uses bbp_get_topic_author_id() To get the topic author id
	 * @uses apply_filters() Calls 'bbp_get_topic_author_link' with the link
	 *                        and args
	 * @return string Author link of topic
	 */
	function bbp_get_topic_author_link( $args = '' ) {

		// Parse arguments against default values
		$r = bbp_parse_args( $args, array(
			'post_id'    => 0,
			'link_title' => '',
			'type'       => 'both',
			'size'       => 80,
			'sep'        => '&nbsp;',
			'show_role'  => false
		), 'get_topic_author_link' );

		// Used as topic_id
		if ( is_numeric( $args ) ) {
			$topic_id = bbp_get_topic_id( $args );
		} else {
			$topic_id = bbp_get_topic_id( $r['post_id'] );
		}

		// Topic ID is good
		if ( !empty( $topic_id ) ) {

			// Get some useful topic information
			$author_url = bbp_get_topic_author_url( $topic_id );
			$anonymous  = bbp_is_topic_anonymous( $topic_id );

			// Tweak link title if empty
			if ( empty( $r['link_title'] ) ) {
				$link_title = sprintf( empty( $anonymous ) ? __( 'View %s\'s profile', 'bbpress' ) : __( 'Visit %s\'s website', 'bbpress' ), bbp_get_topic_author_display_name( $topic_id ) );

			// Use what was passed if not
			} else {
				$link_title = $r['link_title'];
			}

			// Setup title and author_links array
			$link_title   = !empty( $link_title ) ? ' title="' . esc_attr( $link_title ) . '"' : '';
			$author_links = array();

			// Get avatar
			if ( 'avatar' === $r['type'] || 'both' === $r['type'] ) {
				$author_links['avatar'] = bbp_get_topic_author_avatar( $topic_id, $r['size'] );
			}

			// Get display name
			if ( 'name' === $r['type'] || 'both' === $r['type'] ) {
				$author_links['name'] = bbp_get_topic_author_display_name( $topic_id );
			}

			// Link class
			$link_class = ' class="bbp-author-' . esc_attr( $r['type'] ) . '"';

			// Add links if not anonymous
			if ( empty( $anonymous ) && bbp_user_has_profile( bbp_get_topic_author_id( $topic_id ) ) ) {

				// Assemble the links
				foreach ( $author_links as $link => $link_text ) {
					$link_class = ' class="bbp-author-' . esc_attr( $link ) . '"';
					$author_link[] = sprintf( '<a href="%1$s"%2$s%3$s>%4$s</a>', esc_url( $author_url ), $link_title, $link_class, $link_text );
				}

				if ( true === $r['show_role'] ) {
					$author_link[] = bbp_get_topic_author_role( array( 'topic_id' => $topic_id ) );
				}

				$author_link = implode( $r['sep'], $author_link );

			// No links if anonymous
			} else {
				$author_link = implode( $r['sep'], $author_links );
			}

		} else {
			$author_link = '';
		}

		return apply_filters( 'bbp_get_topic_author_link', $author_link, $args );
	}

/**
 * Output the author url of the topic
 *
 * @since bbPress (r2590)
 *
 * @param int $topic_id Optional. Topic id
 * @uses bbp_get_topic_author_url() To get the topic author url
 */
function bbp_topic_author_url( $topic_id = 0 ) {
	echo esc_url( bbp_get_topic_author_url( $topic_id ) );
}

	/**
	 * Return the author url of the topic
	 *
	 * @since bbPress (r2590)
	 *
	 * @param int $topic_id Optional. Topic id
	 * @uses bbp_get_topic_id() To get the topic id
	 * @uses bbp_is_topic_anonymous() To check if the topic is by an anonymous
	 *                                 user or not
	 * @uses bbp_user_has_profile() To check if the user has a profile
	 * @uses bbp_get_topic_author_id() To get topic author id
	 * @uses bbp_get_user_profile_url() To get profile url
	 * @uses get_post_meta() To get anonmous user's website
	 * @uses apply_filters() Calls 'bbp_get_topic_author_url' with the link &
	 *                        topic id
	 * @return string Author URL of topic
	 */
	function bbp_get_topic_author_url( $topic_id = 0 ) {
		$topic_id = bbp_get_topic_id( $topic_id );

		// Check for anonymous user or non-existant user
		if ( !bbp_is_topic_anonymous( $topic_id ) && bbp_user_has_profile( bbp_get_topic_author_id( $topic_id ) ) ) {
			$author_url = bbp_get_user_profile_url( bbp_get_topic_author_id( $topic_id ) );
		} else {
			$author_url = get_post_meta( $topic_id, '_bbp_anonymous_website', true );

			// Set empty author_url as empty string
			if ( empty( $author_url ) ) {
				$author_url = '';
			}
		}

		return apply_filters( 'bbp_get_topic_author_url', $author_url, $topic_id );
	}

/**
 * Output the topic author email address
 *
 * @since bbPress (r3445)
 *
 * @param int $topic_id Optional. Reply id
 * @uses bbp_get_topic_author_email() To get the topic author email
 */
function bbp_topic_author_email( $topic_id = 0 ) {
	echo bbp_get_topic_author_email( $topic_id );
}
	/**
	 * Return the topic author email address
	 *
	 * @since bbPress (r3445)
	 *
	 * @param int $topic_id Optional. Reply id
	 * @uses bbp_get_topic_id() To get the topic id
	 * @uses bbp_is_topic_anonymous() To check if the topic is by an anonymous
	 *                                 user
	 * @uses bbp_get_topic_author_id() To get the topic author id
	 * @uses get_userdata() To get the user data
	 * @uses get_post_meta() To get the anonymous poster's email
	 * @uses apply_filters() Calls bbp_get_topic_author_email with the author
	 *                        email & topic id
	 * @return string Topic author email address
	 */
	function bbp_get_topic_author_email( $topic_id = 0 ) {
		$topic_id = bbp_get_topic_id( $topic_id );

		// Not anonymous user
		if ( !bbp_is_topic_anonymous( $topic_id ) ) {

			// Use topic author email address
			$user_id      = bbp_get_topic_author_id( $topic_id );
			$user         = get_userdata( $user_id );
			$author_email = !empty( $user->user_email ) ? $user->user_email : '';

		// Anonymous
		} else {

			// Get email from post meta
			$author_email = get_post_meta( $topic_id, '_bbp_anonymous_email', true );

			// Sanity check for missing email address
			if ( empty( $author_email ) ) {
				$author_email = '';
			}
		}

		return apply_filters( 'bbp_get_topic_author_email', $author_email, $topic_id );
	}

/**
 * Output the topic author role
 *
 * @since bbPress (r3860)
 *
 * @param array $args Optional.
 * @uses bbp_get_topic_author_role() To get the topic author role
 */
function bbp_topic_author_role( $args = array() ) {
	echo bbp_get_topic_author_role( $args );
}
	/**
	 * Return the topic author role
	 *
	 * @since bbPress (r3860)
	 *
	 * @param array $args Optional.
	 * @uses bbp_get_topic_id() To get the topic id
	 * @uses bbp_get_user_display_role() To get the user display role
	 * @uses bbp_get_topic_author_id() To get the topic author id
	 * @uses apply_filters() Calls bbp_get_topic_author_role with the author
	 *                        role & args
	 * @return string topic author role
	 */
	function bbp_get_topic_author_role( $args = array() ) {

		// Parse arguments against default values
		$r = bbp_parse_args( $args, array(
			'topic_id' => 0,
			'class'    => 'bbp-author-role',
			'before'   => '',
			'after'    => ''
		), 'get_topic_author_role' );

		$topic_id    = bbp_get_topic_id( $r['topic_id'] );
		$role        = bbp_get_user_display_role( bbp_get_topic_author_id( $topic_id ) );
		$author_role = sprintf( '%1$s<div class="%2$s">%3$s</div>%4$s', $r['before'], $r['class'], $role, $r['after'] );

		return apply_filters( 'bbp_get_topic_author_role', $author_role, $r );
	}


/**
 * Output the title of the forum a topic belongs to
 *
 * @since bbPress (r2485)
 *
 * @param int $topic_id Optional. Topic id
 * @uses bbp_get_topic_forum_title() To get the topic's forum title
 */
function bbp_topic_forum_title( $topic_id = 0 ) {
	echo bbp_get_topic_forum_title( $topic_id );
}
	/**
	 * Return the title of the forum a topic belongs to
	 *
	 * @since bbPress (r2485)
	 *
	 * @param int $topic_id Optional. Topic id
	 * @uses bbp_get_topic_id() To get topic id
	 * @uses bbp_get_topic_forum_id() To get topic's forum id
	 * @uses apply_filters() Calls 'bbp_get_topic_forum' with the forum
	 *                        title and topic id
	 * @return string Topic forum title
	 */
	function bbp_get_topic_forum_title( $topic_id = 0 ) {
		$topic_id = bbp_get_topic_id( $topic_id );
		$forum_id = bbp_get_topic_forum_id( $topic_id );

		return apply_filters( 'bbp_get_topic_forum', bbp_get_forum_title( $forum_id ), $topic_id, $forum_id );
	}

/**
 * Output the forum id a topic belongs to
 *
 * @since bbPress (r2491)
 *
 * @param int $topic_id Optional. Topic id
 * @uses bbp_get_topic_forum_id()
 */
function bbp_topic_forum_id( $topic_id = 0 ) {
	echo bbp_get_topic_forum_id( $topic_id );
}
	/**
	 * Return the forum id a topic belongs to
	 *
	 * @since bbPress (r2491)
	 *
	 * @param int $topic_id Optional. Topic id
	 * @uses bbp_get_topic_id() To get topic id
	 * @uses get_post_meta() To retrieve get topic's forum id meta
	 * @uses apply_filters() Calls 'bbp_get_topic_forum_id' with the forum
	 *                        id and topic id
	 * @return int Topic forum id
	 */
	function bbp_get_topic_forum_id( $topic_id = 0 ) {
		$topic_id = bbp_get_topic_id( $topic_id );
		$forum_id = get_post_meta( $topic_id, '_bbp_forum_id', true );

		return (int) apply_filters( 'bbp_get_topic_forum_id', (int) $forum_id, $topic_id );
	}

/**
 * Output the topics last active ID
 *
 * @since bbPress (r2860)
 *
 * @param int $topic_id Optional. Forum id
 * @uses bbp_get_topic_last_active_id() To get the topic's last active id
 */
function bbp_topic_last_active_id( $topic_id = 0 ) {
	echo bbp_get_topic_last_active_id( $topic_id );
}
	/**
	 * Return the topics last active ID
	 *
	 * @since bbPress (r2860)
	 *
	 * @param int $topic_id Optional. Forum id
	 * @uses bbp_get_topic_id() To get the topic id
	 * @uses get_post_meta() To get the topic's last active id
	 * @uses apply_filters() Calls 'bbp_get_topic_last_active_id' with
	 *                        the last active id and topic id
	 * @return int Forum's last active id
	 */
	function bbp_get_topic_last_active_id( $topic_id = 0 ) {
		$topic_id  = bbp_get_topic_id( $topic_id );
		$active_id = get_post_meta( $topic_id, '_bbp_last_active_id', true );

		return (int) apply_filters( 'bbp_get_topic_last_active_id', (int) $active_id, $topic_id );
	}

/**
 * Output the topics last update date/time (aka freshness)
 *
 * @since bbPress (r2625)
 *
 * @param int $topic_id Optional. Topic id
 * @uses bbp_get_topic_last_active_time() To get topic freshness
 */
function bbp_topic_last_active_time( $topic_id = 0 ) {
	echo bbp_get_topic_last_active_time( $topic_id );
}
	/**
	 * Return the topics last update date/time (aka freshness)
	 *
	 * @since bbPress (r2625)
	 *
	 * @param int $topic_id Optional. Topic id
	 * @uses bbp_get_topic_id() To get topic id
	 * @uses get_post_meta() To get the topic lst active meta
	 * @uses bbp_get_topic_last_reply_id() To get topic last reply id
	 * @uses get_post_field() To get the post date of topic/reply
	 * @uses bbp_convert_date() To convert date
	 * @uses bbp_get_time_since() To get time in since format
	 * @uses apply_filters() Calls 'bbp_get_topic_last_active' with topic
	 *                        freshness and topic id
	 * @return string Topic freshness
	 */
	function bbp_get_topic_last_active_time( $topic_id = 0 ) {
		$topic_id = bbp_get_topic_id( $topic_id );

		// Try to get the most accurate freshness time possible
		$last_active = get_post_meta( $topic_id, '_bbp_last_active_time', true );
		if ( empty( $last_active ) ) {
			$reply_id = bbp_get_topic_last_reply_id( $topic_id );
			if ( !empty( $reply_id ) ) {
				$last_active = get_post_field( 'post_date', $reply_id );
			} else {
				$last_active = get_post_field( 'post_date', $topic_id );
			}
		}

		$last_active = !empty( $last_active ) ? bbp_get_time_since( bbp_convert_date( $last_active ) ) : '';

		// Return the time since
		return apply_filters( 'bbp_get_topic_last_active', $last_active, $topic_id );
	}

/** Topic Subscriptions *******************************************************/

/**
 * Output the topic subscription link
 *
 * @since bbPress (r5156)
 *
 * @uses bbp_get_topic_subscription_link()
 */
function bbp_topic_subscription_link( $args = array() ) {
	echo bbp_get_topic_subscription_link( $args );
}

	/**
	 * Get the forum subscription link
	 *
	 * A custom wrapper for bbp_get_user_subscribe_link()
	 *
	 * @since bbPress (r5156)
	 *
	 * @uses bbp_parse_args()
	 * @uses bbp_get_user_subscribe_link()
	 * @uses apply_filters() Calls 'bbp_get_topic_subscribe_link'
	 */
	function bbp_get_topic_subscription_link( $args = array() ) {

		// No link
		$retval = false;

		// Parse the arguments
		$r = bbp_parse_args( $args, array(
			'user_id'     => 0,
			'topic_id'    => 0,
			'before'      => '&nbsp;|&nbsp;',
			'after'       => '',
			'subscribe'   => __( 'Subscribe',   'bbpress' ),
			'unsubscribe' => __( 'Unsubscribe', 'bbpress' )
		), 'get_forum_subscribe_link' );

		// Get the link
		$retval = bbp_get_user_subscribe_link( $r );

		return apply_filters( 'bbp_get_topic_subscribe_link', $retval, $r );
	}

/** Topic Favorites ***********************************************************/

/**
 * Output the topic favorite link
 *
 * @since bbPress (r5156)
 *
 * @uses bbp_get_topic_favorite_link()
 */
function bbp_topic_favorite_link( $args = array() ) {
	echo bbp_get_topic_favorite_link( $args );
}

	/**
	 * Get the forum favorite link
	 *
	 * A custom wrapper for bbp_get_user_favorite_link()
	 *
	 * @since bbPress (r5156)
	 *
	 * @uses bbp_parse_args()
	 * @uses bbp_get_user_favorites_link()
	 * @uses apply_filters() Calls 'bbp_get_topic_favorite_link'
	 */
	function bbp_get_topic_favorite_link( $args = array() ) {

		// No link
		$retval = false;

		// Parse the arguments
		$r = bbp_parse_args( $args, array(
			'user_id'   => 0,
			'topic_id'  => 0,
			'before'    => '',
			'after'     => '',
			'favorite'  => __( 'Favorite',   'bbpress' ),
			'favorited' => __( 'Unfavorite', 'bbpress' )
		), 'get_forum_favorite_link' );

		// Get the link
		$retval = bbp_get_user_favorites_link( $r );

		return apply_filters( 'bbp_get_topic_favorite_link', $retval, $r );
	}

/** Topic Last Reply **********************************************************/

/**
 * Output the id of the topics last reply
 *
 * @since bbPress (r2625)
 *
 * @param int $topic_id Optional. Topic id
 * @uses bbp_get_topic_last_reply_id() To get the topic last reply id
 */
function bbp_topic_last_reply_id( $topic_id = 0 ) {
	echo bbp_get_topic_last_reply_id( $topic_id );
}
	/**
	 * Return the topics last update date/time (aka freshness)
	 *
	 * @since bbPress (r2625)
	 *
	 * @param int $topic_id Optional. Topic id
	 * @uses bbp_get_topic_id() To get the topic id
	 * @uses get_post_meta() To get the last reply id meta
	 * @uses apply_filters() Calls 'bbp_get_topic_last_reply_id' with the
	 *                        last reply id and topic id
	 * @return int Topic last reply id
	 */
	function bbp_get_topic_last_reply_id( $topic_id = 0 ) {
		$topic_id = bbp_get_topic_id( $topic_id );
		$reply_id = get_post_meta( $topic_id, '_bbp_last_reply_id', true );

		if ( empty( $reply_id ) )
			$reply_id = $topic_id;

		return (int) apply_filters( 'bbp_get_topic_last_reply_id', (int) $reply_id, $topic_id );
	}

/**
 * Output the title of the last reply inside a topic
 *
 * @param int $topic_id Optional. Topic id
 * @uses bbp_get_topic_last_reply_title() To get the topic last reply title
 */
function bbp_topic_last_reply_title( $topic_id = 0 ) {
	echo bbp_get_topic_last_reply_title( $topic_id );
}
	/**
	 * Return the title of the last reply inside a topic
	 *
	 * @param int $topic_id Optional. Topic id
	 * @uses bbp_get_topic_id() To get the topic id
	 * @uses bbp_get_topic_last_reply_id() To get the topic last reply id
	 * @uses bbp_get_reply_title() To get the reply title
	 * @uses apply_filters() Calls 'bbp_get_topic_last_topic_title' with
	 *                        the reply title and topic id
	 * @return string Topic last reply title
	 */
	function bbp_get_topic_last_reply_title( $topic_id = 0 ) {
		$topic_id = bbp_get_topic_id( $topic_id );
		return apply_filters( 'bbp_get_topic_last_topic_title', bbp_get_reply_title( bbp_get_topic_last_reply_id( $topic_id ) ), $topic_id );
	}

/**
 * Output the link to the last reply in a topic
 *
 * @since bbPress (r2464)
 *
 * @param int $topic_id Optional. Topic id
 * @uses bbp_get_topic_last_reply_permalink() To get the topic's last reply link
 */
function bbp_topic_last_reply_permalink( $topic_id = 0 ) {
	echo esc_url( bbp_get_topic_last_reply_permalink( $topic_id ) );
}
	/**
	 * Return the link to the last reply in a topic
	 *
	 * @since bbPress (r2464)
	 *
	 * @param int $topic_id Optional. Topic id
	 * @uses bbp_get_topic_id() To get the topic id
	 * @uses bbp_get_topic_last_reply_id() To get the topic last reply id
	 * @uses bbp_get_reply_permalink() To get the reply permalink
	 * @uses apply_filters() Calls 'bbp_get_topic_last_topic_permalink' with
	 *                        the reply permalink and topic id
	 * @return string Permanent link to the reply
	 */
	function bbp_get_topic_last_reply_permalink( $topic_id = 0 ) {
		$topic_id = bbp_get_topic_id( $topic_id );
		return apply_filters( 'bbp_get_topic_last_reply_permalink', bbp_get_reply_permalink( bbp_get_topic_last_reply_id( $topic_id ) ) );
	}

/**
 * Output the link to the last reply in a topic
 *
 * @since bbPress (r2683)
 *
 * @param int $topic_id Optional. Topic id
 * @uses bbp_get_topic_last_reply_url() To get the topic last reply url
 */
function bbp_topic_last_reply_url( $topic_id = 0 ) {
	echo esc_url( bbp_get_topic_last_reply_url( $topic_id ) );
}
	/**
	 * Return the link to the last reply in a topic
	 *
	 * @since bbPress (r2683)
	 *
	 * @param int $topic_id Optional. Topic id
	 * @uses bbp_get_topic_id() To get the topic id
	 * @uses bbp_get_topic_last_reply_id() To get the topic last reply id
	 * @uses bbp_get_reply_url() To get the reply url
	 * @uses bbp_get_reply_permalink() To get the reply permalink
	 * @uses apply_filters() Calls 'bbp_get_topic_last_topic_url' with
	 *                        the reply url and topic id
	 * @return string Topic last reply url
	 */
	function bbp_get_topic_last_reply_url( $topic_id = 0 ) {
		$topic_id = bbp_get_topic_id( $topic_id );
		$reply_id = bbp_get_topic_last_reply_id( $topic_id );

		if ( !empty( $reply_id ) && ( $reply_id !== $topic_id ) ) {
			$reply_url = bbp_get_reply_url( $reply_id );
		} else {
			$reply_url = bbp_get_topic_permalink( $topic_id );
		}

		return apply_filters( 'bbp_get_topic_last_reply_url', $reply_url );
	}

/**
 * Output link to the most recent activity inside a topic, complete with link
 * attributes and content.
 *
 * @since bbPress (r2625)
 *
 * @param int $topic_id Optional. Topic id
 * @uses bbp_get_topic_freshness_link() To get the topic freshness link
 */
function bbp_topic_freshness_link( $topic_id = 0 ) {
	echo bbp_get_topic_freshness_link( $topic_id );
}
	/**
	 * Returns link to the most recent activity inside a topic, complete
	 * with link attributes and content.
	 *
	 * @since bbPress (r2625)
	 *
	 * @param int $topic_id Optional. Topic id
	 * @uses bbp_get_topic_id() To get the topic id
	 * @uses bbp_get_topic_last_reply_url() To get the topic last reply url
	 * @uses bbp_get_topic_last_reply_title() To get the reply title
	 * @uses bbp_get_topic_last_active_time() To get the topic freshness
	 * @uses apply_filters() Calls 'bbp_get_topic_freshness_link' with the
	 *                        link and topic id
	 * @return string Topic freshness link
	 */
	function bbp_get_topic_freshness_link( $topic_id = 0 ) {
		$topic_id   = bbp_get_topic_id( $topic_id );
		$link_url   = bbp_get_topic_last_reply_url( $topic_id );
		$title      = bbp_get_topic_last_reply_title( $topic_id );
		$time_since = bbp_get_topic_last_active_time( $topic_id );

		if ( !empty( $time_since ) )
			$anchor = '<a href="' . esc_url( $link_url ) . '" title="' . esc_attr( $title ) . '">' . esc_html( $time_since ) . '</a>';
		else
			$anchor = __( 'No Replies', 'bbpress' );

		return apply_filters( 'bbp_get_topic_freshness_link', $anchor, $topic_id, $time_since, $link_url, $title );
	}

/**
 * Output the replies link of the topic
 *
 * @since bbPress (r2740)
 *
 * @param int $topic_id Optional. Topic id
 * @uses bbp_get_topic_replies_link() To get the topic replies link
 */
function bbp_topic_replies_link( $topic_id = 0 ) {
	echo bbp_get_topic_replies_link( $topic_id );
}

	/**
	 * Return the replies link of the topic
	 *
	 * @since bbPress (r2740)
	 *
	 * @param int $topic_id Optional. Topic id
	 * @uses bbp_get_topic_id() To get the topic id
	 * @uses bbp_get_topic() To get the topic
	 * @uses bbp_get_topic_reply_count() To get the topic reply count
	 * @uses bbp_get_topic_permalink() To get the topic permalink
	 * @uses remove_query_arg() To remove args from the url
	 * @uses bbp_get_topic_reply_count_hidden() To get the topic hidden
	 *                                           reply count
	 * @uses current_user_can() To check if the current user can edit others
	 *                           replies
	 * @uses add_query_arg() To add custom args to the url
	 * @uses apply_filters() Calls 'bbp_get_topic_replies_link' with the
	 *                        replies link and topic id
	 */
	function bbp_get_topic_replies_link( $topic_id = 0 ) {

		$topic    = bbp_get_topic( bbp_get_topic_id( (int) $topic_id ) );
		$topic_id = $topic->ID;
		$replies  = sprintf( _n( '%s reply', '%s replies', bbp_get_topic_reply_count( $topic_id, true ), 'bbpress' ), bbp_get_topic_reply_count( $topic_id ) );
		$retval   = '';

		// First link never has view=all
		if ( bbp_get_view_all( 'edit_others_replies' ) )
			$retval .= "<a href='" . esc_url( bbp_remove_view_all( bbp_get_topic_permalink( $topic_id ) ) ) . "'>$replies</a>";
		else
			$retval .= $replies;

		// Any deleted replies?
		$deleted = bbp_get_topic_reply_count_hidden( $topic_id );

		// This forum has hidden topics
		if ( !empty( $deleted ) && current_user_can( 'edit_others_replies' ) ) {

			// Extra text
			$extra = sprintf( __( ' (+ %d hidden)', 'bbpress' ), $deleted );

			// No link
			if ( bbp_get_view_all() ) {
				$retval .= " $extra";

			// Link
			} else {
				$retval .= " <a href='" . esc_url( bbp_add_view_all( bbp_get_topic_permalink( $topic_id ), true ) ) . "'>$extra</a>";
			}
		}

		return apply_filters( 'bbp_get_topic_replies_link', $retval, $topic_id );
	}

/**
 * Output total reply count of a topic
 *
 * @since bbPress (r2485)
 *
 * @param int $topic_id Optional. Topic id
 * @param boolean $integer Optional. Whether or not to format the result
 * @uses bbp_get_topic_reply_count() To get the topic reply count
 */
function bbp_topic_reply_count( $topic_id = 0, $integer = false ) {
	echo bbp_get_topic_reply_count( $topic_id, $integer );
}
	/**
	 * Return total reply count of a topic
	 *
	 * @since bbPress (r2485)
	 *
	 * @param int $topic_id Optional. Topic id
	 * @param boolean $integer Optional. Whether or not to format the result
	 * @uses bbp_get_topic_id() To get the topic id
	 * @uses get_post_meta() To get the topic reply count meta
	 * @uses apply_filters() Calls 'bbp_get_topic_reply_count' with the
	 *                        reply count and topic id
	 * @return int Reply count
	 */
	function bbp_get_topic_reply_count( $topic_id = 0, $integer = false ) {
		$topic_id = bbp_get_topic_id( $topic_id );
		$replies  = (int) get_post_meta( $topic_id, '_bbp_reply_count', true );
		$filter   = ( true === $integer ) ? 'bbp_get_topic_reply_count_int' : 'bbp_get_topic_reply_count';

		return apply_filters( $filter, $replies, $topic_id );
	}

/**
 * Output total post count of a topic
 *
 * @since bbPress (r2954)
 *
 * @param int $topic_id Optional. Topic id
 * @param boolean $integer Optional. Whether or not to format the result
 * @uses bbp_get_topic_post_count() To get the topic post count
 */
function bbp_topic_post_count( $topic_id = 0, $integer = false ) {
	echo bbp_get_topic_post_count( $topic_id, $integer );
}
	/**
	 * Return total post count of a topic
	 *
	 * @since bbPress (r2954)
	 *
	 * @param int $topic_id Optional. Topic id
	 * @param boolean $integer Optional. Whether or not to format the result
	 * @uses bbp_get_topic_id() To get the topic id
	 * @uses get_post_meta() To get the topic post count meta
	 * @uses apply_filters() Calls 'bbp_get_topic_post_count' with the
	 *                        post count and topic id
	 * @return int Post count
	 */
	function bbp_get_topic_post_count( $topic_id = 0, $integer = false ) {
		$topic_id = bbp_get_topic_id( $topic_id );
		$replies  = (int) get_post_meta( $topic_id, '_bbp_reply_count', true ) + 1;
		$filter   = ( true === $integer ) ? 'bbp_get_topic_post_count_int' : 'bbp_get_topic_post_count';

		return apply_filters( $filter, $replies, $topic_id );
	}

/**
 * Output total hidden reply count of a topic (hidden includes trashed and
 * spammed replies)
 *
 * @since bbPress (r2740)
 *
 * @param int $topic_id Optional. Topic id
 * @param boolean $integer Optional. Whether or not to format the result
 * @uses bbp_get_topic_reply_count_hidden() To get the topic hidden reply count
 */
function bbp_topic_reply_count_hidden( $topic_id = 0, $integer = false ) {
	echo bbp_get_topic_reply_count_hidden( $topic_id, $integer );
}
	/**
	 * Return total hidden reply count of a topic (hidden includes trashed
	 * and spammed replies)
	 *
	 * @since bbPress (r2740)
	 *
	 * @param int $topic_id Optional. Topic id
	 * @param boolean $integer Optional. Whether or not to format the result
	 * @uses bbp_get_topic_id() To get the topic id
	 * @uses get_post_meta() To get the hidden reply count
	 * @uses apply_filters() Calls 'bbp_get_topic_reply_count_hidden' with
	 *                        the hidden reply count and topic id
	 * @return int Topic hidden reply count
	 */
	function bbp_get_topic_reply_count_hidden( $topic_id = 0, $integer = false ) {
		$topic_id = bbp_get_topic_id( $topic_id );
		$replies  = (int) get_post_meta( $topic_id, '_bbp_reply_count_hidden', true );
		$filter   = ( true === $integer ) ? 'bbp_get_topic_reply_count_hidden_int' : 'bbp_get_topic_reply_count_hidden';

		return apply_filters( $filter, $replies, $topic_id );
	}

/**
 * Output total voice count of a topic
 *
 * @since bbPress (r2567)
 *
 * @param int $topic_id Optional. Topic id
 * @uses bbp_get_topic_voice_count() To get the topic voice count
 */
function bbp_topic_voice_count( $topic_id = 0, $integer = false ) {
	echo bbp_get_topic_voice_count( $topic_id, $integer );
}
	/**
	 * Return total voice count of a topic
	 *
	 * @since bbPress (r2567)
	 *
	 * @param int $topic_id Optional. Topic id
	 * @uses bbp_get_topic_id() To get the topic id
	 * @uses get_post_meta() To get the voice count meta
	 * @uses apply_filters() Calls 'bbp_get_topic_voice_count' with the
	 *                        voice count and topic id
	 * @return int Voice count of the topic
	 */
	function bbp_get_topic_voice_count( $topic_id = 0, $integer = false ) {
		$topic_id = bbp_get_topic_id( $topic_id );
		$voices   = (int) get_post_meta( $topic_id, '_bbp_voice_count', true );
		$filter   = ( true === $integer ) ? 'bbp_get_topic_voice_count_int' : 'bbp_get_topic_voice_count';

		return apply_filters( $filter, $voices, $topic_id );
	}

/**
 * Output a the tags of a topic
 *
 * @param int $topic_id Optional. Topic id
 * @param mixed $args See {@link bbp_get_topic_tag_list()}
 * @uses bbp_get_topic_tag_list() To get the topic tag list
 */
function bbp_topic_tag_list( $topic_id = 0, $args = '' ) {
	echo bbp_get_topic_tag_list( $topic_id, $args );
}
	/**
	 * Return the tags of a topic
	 *
	 * @param int $topic_id Optional. Topic id
	 * @param array $args This function supports these arguments:
	 *  - before: Before the tag list
	 *  - sep: Tag separator
	 *  - after: After the tag list
	 * @uses bbp_get_topic_id() To get the topic id
	 * @uses get_the_term_list() To get the tags list
	 * @return string Tag list of the topic
	 */
	function bbp_get_topic_tag_list( $topic_id = 0, $args = '' ) {

		// Bail if topic-tags are off
		if ( ! bbp_allow_topic_tags() )
			return;

		// Parse arguments against default values
		$r = bbp_parse_args( $args, array(
			'before' => '<div class="bbp-topic-tags"><p>' . esc_html__( 'Tagged:', 'bbpress' ) . '&nbsp;',
			'sep'    => ', ',
			'after'  => '</p></div>'
		), 'get_topic_tag_list' );

		$topic_id = bbp_get_topic_id( $topic_id );

		// Topic is spammed, so display pre-spam terms
		if ( bbp_is_topic_spam( $topic_id ) ) {

			// Get pre-spam terms
			$terms = get_post_meta( $topic_id, '_bbp_spam_topic_tags', true );

			// If terms exist, explode them and compile the return value
			if ( !empty( $terms ) ) {
				$terms  = implode( $r['sep'], $terms );
				$retval = $r['before'] . $terms . $r['after'];

			// No terms so return emty string
			} else {
				$retval = '';
			}

		// Topic is not spam so display a clickable term list
		} else {
			$retval = get_the_term_list( $topic_id, bbp_get_topic_tag_tax_id(), $r['before'], $r['sep'], $r['after'] );
		}

		return $retval;
	}

/**
 * Output the row class of a topic
 *
 * @since bbPress (r2667)
 *
 * @param int $topic_id Optional. Topic id
 * @param array Extra classes you can pass when calling this function
 * @uses bbp_get_topic_class() To get the topic class
 */
function bbp_topic_class( $topic_id = 0, $classes = array() ) {
	echo bbp_get_topic_class( $topic_id, $classes );
}
	/**
	 * Return the row class of a topic
	 *
	 * @since bbPress (r2667)
	 *
	 * @param int $topic_id Optional. Topic id
	 * @param array Extra classes you can pass when calling this function
	 * @uses bbp_is_topic_sticky() To check if the topic is a sticky
	 * @uses bbp_is_topic_super_sticky() To check if the topic is a super sticky
	 * @uses bbp_get_topic_forum_id() To get the topic forum id
	 * @uses get_post_class() To get the topic classes
	 * @uses apply_filters() Calls 'bbp_get_topic_class' with the classes
	 *                        and topic id
	 * @return string Row class of a topic
	 */
	function bbp_get_topic_class( $topic_id = 0, $classes = array() ) {
		$bbp       = bbpress();
		$topic_id  = bbp_get_topic_id( $topic_id );
		$count     = isset( $bbp->topic_query->current_post ) ? $bbp->topic_query->current_post : 1;
		$classes   = (array) $classes;
		$classes[] = ( (int) $count % 2 )                    ? 'even'         : 'odd';
		$classes[] = bbp_is_topic_sticky( $topic_id, false ) ? 'sticky'       : '';
		$classes[] = bbp_is_topic_super_sticky( $topic_id  ) ? 'super-sticky' : '';
		$classes[] = 'bbp-parent-forum-' . bbp_get_topic_forum_id( $topic_id );
		$classes[] = 'user-id-' . bbp_get_topic_author_id( $topic_id );
		$classes   = array_filter( $classes );
		$classes   = get_post_class( $classes, $topic_id );
		$classes   = apply_filters( 'bbp_get_topic_class', $classes, $topic_id );
		$retval    = 'class="' . implode( ' ', $classes ) . '"';

		return $retval;
	}

/** Topic Admin Links *********************************************************/

/**
 * Output admin links for topic
 *
 * @param array $args See {@link bbp_get_topic_admin_links()}
 * @uses bbp_get_topic_admin_links() To get the topic admin links
 */
function bbp_topic_admin_links( $args = array() ) {
	echo bbp_get_topic_admin_links( $args );
}
	/**
	 * Return admin links for topic.
	 *
	 * Move topic functionality is handled by the edit topic page.
	 *
	 * @param array $args This function supports these arguments:
	 *  - id: Optional. Topic id
	 *  - before: Before the links
	 *  - after: After the links
	 *  - sep: Links separator
	 *  - links: Topic admin links array
	 * @uses current_user_can() To check if the current user can edit/delete
	 *                           the topic
	 * @uses bbp_get_topic_edit_link() To get the topic edit link
	 * @uses bbp_get_topic_trash_link() To get the topic trash link
	 * @uses bbp_get_topic_close_link() To get the topic close link
	 * @uses bbp_get_topic_spam_link() To get the topic spam link
	 * @uses bbp_get_topic_stick_link() To get the topic stick link
	 * @uses bbp_get_topic_merge_link() To get the topic merge link
	 * @uses bbp_get_topic_status() To get the topic status
	 * @uses apply_filters() Calls 'bbp_get_topic_admin_links' with the
	 *                        topic admin links and args
	 * @return string Topic admin links
	 */
	function bbp_get_topic_admin_links( $args = array() ) {

		// Parse arguments against default values
		$r = bbp_parse_args( $args, array (
			'id'     => bbp_get_topic_id(),
			'before' => '<span class="bbp-admin-links">',
			'after'  => '</span>',
			'sep'    => ' | ',
			'links'  => array()
		), 'get_topic_admin_links' );

		if ( empty( $r['links'] ) ) {
			$r['links'] = apply_filters( 'bbp_topic_admin_links', array(
				'edit'  => bbp_get_topic_edit_link ( $r ),
				'close' => bbp_get_topic_close_link( $r ),
				'stick' => bbp_get_topic_stick_link( $r ),
				'merge' => bbp_get_topic_merge_link( $r ),
				'trash' => bbp_get_topic_trash_link( $r ),
				'spam'  => bbp_get_topic_spam_link ( $r ),
				'reply' => bbp_get_topic_reply_link( $r )
			), $r['id'] );
		}

		// See if links need to be unset
		$topic_status = bbp_get_topic_status( $r['id'] );
		if ( in_array( $topic_status, array( bbp_get_spam_status_id(), bbp_get_trash_status_id() ) ) ) {

			// Close link shouldn't be visible on trashed/spammed topics
			unset( $r['links']['close'] );

			// Spam link shouldn't be visible on trashed topics
			if ( bbp_get_trash_status_id() === $topic_status ) {
				unset( $r['links']['spam'] );

			// Trash link shouldn't be visible on spam topics
			} elseif ( bbp_get_spam_status_id() === $topic_status ) {
				unset( $r['links']['trash'] );
			}
		}

		// Process the admin links
		$links  = implode( $r['sep'], array_filter( $r['links'] ) );
		$retval = $r['before'] . $links . $r['after'];

		return apply_filters( 'bbp_get_topic_admin_links', $retval, $r, $args );
	}

/**
 * Output the edit link of the topic
 *
 * @since bbPress (r2727)
 *
 * @param mixed $args See {@link bbp_get_topic_edit_link()}
 * @uses bbp_get_topic_edit_link() To get the topic edit link
 */
function bbp_topic_edit_link( $args = '' ) {
	echo bbp_get_topic_edit_link( $args );
}

	/**
	 * Return the edit link of the topic
	 *
	 * @since bbPress (r2727)
	 *
	 * @param mixed $args This function supports these args:
	 *  - id: Optional. Topic id
	 *  - link_before: Before the link
	 *  - link_after: After the link
	 *  - edit_text: Edit text
	 * @uses bbp_get_topic_id() To get the topic id
	 * @uses bbp_get_topic() To get the topic
	 * @uses current_user_can() To check if the current user can edit the
	 *                           topic
	 * @uses bbp_get_topic_edit_url() To get the topic edit url
	 * @uses apply_filters() Calls 'bbp_get_topic_edit_link' with the link
	 *                        and args
	 * @return string Topic edit link
	 */
	function bbp_get_topic_edit_link( $args = '' ) {

		// Parse arguments against default values
		$r = bbp_parse_args( $args, array(
			'id'           => 0,
			'link_before'  => '',
			'link_after'   => '',
			'edit_text'    => esc_html__( 'Edit', 'bbpress' )
		), 'get_topic_edit_link' );

		$topic = bbp_get_topic( bbp_get_topic_id( (int) $r['id'] ) );

		// Bypass check if user has caps
		if ( !current_user_can( 'edit_others_topics' ) ) {

			// User cannot edit or it is past the lock time
			if ( empty( $topic ) || !current_user_can( 'edit_topic', $topic->ID ) || bbp_past_edit_lock( $topic->post_date_gmt ) ) {
				return;
			}
		}

		// Get uri
		$uri = bbp_get_topic_edit_url( $r['id'] );

		// Bail if no uri
		if ( empty( $uri ) )
			return;

		$retval = $r['link_before'] . '<a href="' . esc_url( $uri ) . '" class="bbp-topic-edit-link">' . $r['edit_text'] . '</a>' . $r['link_after'];

		return apply_filters( 'bbp_get_topic_edit_link', $retval, $r );
	}

/**
 * Output URL to the topic edit page
 *
 * @since bbPress (r2753)
 *
 * @param int $topic_id Optional. Topic id
 * @uses bbp_get_topic_edit_url() To get the topic edit url
 */
function bbp_topic_edit_url( $topic_id = 0 ) {
	echo esc_url( bbp_get_topic_edit_url( $topic_id ) );
}
	/**
	 * Return URL to the topic edit page
	 *
	 * @since bbPress (r2753)
	 *
	 * @param int $topic_id Optional. Topic id
	 * @uses bbp_get_topic_id() To get the topic id
	 * @uses bbp_get_topic() To get the topic
	 * @uses add_query_arg() To add custom args to the url
	 * @uses apply_filters() Calls 'bbp_get_topic_edit_url' with the edit
	 *                        url and topic id
	 * @return string Topic edit url
	 */
	function bbp_get_topic_edit_url( $topic_id = 0 ) {
		global $wp_rewrite;

		$bbp = bbpress();

		$topic = bbp_get_topic( bbp_get_topic_id( $topic_id ) );
		if ( empty( $topic ) )
			return;

		// Remove view=all link from edit
		$topic_link = bbp_remove_view_all( bbp_get_topic_permalink( $topic_id ) );

		// Pretty permalinks
		if ( $wp_rewrite->using_permalinks() ) {
			$url = trailingslashit( $topic_link ) . $bbp->edit_id;
			$url = trailingslashit( $url );

		// Unpretty permalinks
		} else {
			$url = add_query_arg( array( bbp_get_topic_post_type() => $topic->post_name, $bbp->edit_id => '1' ), $topic_link );
		}

		// Maybe add view=all
		$url = bbp_add_view_all( $url );

		return apply_filters( 'bbp_get_topic_edit_url', $url, $topic_id );
	}

/**
 * Output the trash link of the topic
 *
 * @since bbPress (r2727)
 *
 * @param mixed $args See {@link bbp_get_topic_trash_link()}
 * @uses bbp_get_topic_trash_link() To get the topic trash link
 */
function bbp_topic_trash_link( $args = '' ) {
	echo bbp_get_topic_trash_link( $args );
}

	/**
	 * Return the trash link of the topic
	 *
	 * @since bbPress (r2727)
	 *
	 * @param mixed $args This function supports these args:
	 *  - id: Optional. Topic id
	 *  - link_before: Before the link
	 *  - link_after: After the link
	 *  - sep: Links separator
	 *  - trash_text: Trash text
	 *  - restore_text: Restore text
	 *  - delete_text: Delete text
	 * @uses bbp_get_topic_id() To get the topic id
	 * @uses bbp_get_topic() To get the topic
	 * @uses current_user_can() To check if the current user can delete the
	 *                           topic
	 * @uses bbp_is_topic_trash() To check if the topic is trashed
	 * @uses bbp_get_topic_status() To get the topic status
	 * @uses add_query_arg() To add custom args to the url
	 * @uses wp_nonce_url() To nonce the url
	 * @uses esc_url() To escape the url
	 * @uses apply_filters() Calls 'bbp_get_topic_trash_link' with the link
	 *                        and args
	 * @return string Topic trash link
	 */
	function bbp_get_topic_trash_link( $args = '' ) {

		// Parse arguments against default values
		$r = bbp_parse_args( $args, array(
			'id'           => 0,
			'link_before'  => '',
			'link_after'   => '',
			'sep'          => ' | ',
			'trash_text'   => esc_html__( 'Trash',   'bbpress' ),
			'restore_text' => esc_html__( 'Restore', 'bbpress' ),
			'delete_text'  => esc_html__( 'Delete',  'bbpress' )
		), 'get_topic_trash_link' );

		$actions = array();
		$topic   = bbp_get_topic( bbp_get_topic_id( (int) $r['id'] ) );

		if ( empty( $topic ) || !current_user_can( 'delete_topic', $topic->ID ) ) {
			return;
		}

		if ( bbp_is_topic_trash( $topic->ID ) ) {
			$actions['untrash'] = '<a title="' . esc_attr__( 'Restore this item from the Trash', 'bbpress' ) . '" href="' . esc_url( wp_nonce_url( add_query_arg( array( 'action' => 'bbp_toggle_topic_trash', 'sub_action' => 'untrash', 'topic_id' => $topic->ID ) ), 'untrash-' . $topic->post_type . '_' . $topic->ID ) ) . '" class="bbp-topic-restore-link">' . $r['restore_text'] . '</a>';
		} elseif ( EMPTY_TRASH_DAYS ) {
			$actions['trash']   = '<a title="' . esc_attr__( 'Move this item to the Trash',      'bbpress' ) . '" href="' . esc_url( wp_nonce_url( add_query_arg( array( 'action' => 'bbp_toggle_topic_trash', 'sub_action' => 'trash',   'topic_id' => $topic->ID ) ), 'trash-'   . $topic->post_type . '_' . $topic->ID ) ) . '" class="bbp-topic-trash-link">'   . $r['trash_text']   . '</a>';
		}

		if ( bbp_is_topic_trash( $topic->ID ) || !EMPTY_TRASH_DAYS ) {
			$actions['delete']  = '<a title="' . esc_attr__( 'Delete this item permanently',     'bbpress' ) . '" href="' . esc_url( wp_nonce_url( add_query_arg( array( 'action' => 'bbp_toggle_topic_trash', 'sub_action' => 'delete',  'topic_id' => $topic->ID ) ), 'delete-'  . $topic->post_type . '_' . $topic->ID ) ) . '" onclick="return confirm(\'' . esc_js( __( 'Are you sure you want to delete that permanently?', 'bbpress' ) ) . '\' );" class="bbp-topic-delete-link">' . $r['delete_text'] . '</a>';
		}

		// Process the admin links
		$retval = $r['link_before'] . implode( $r['sep'], $actions ) . $r['link_after'];

		return apply_filters( 'bbp_get_topic_trash_link', $retval, $r );
	}

/**
 * Output the close link of the topic
 *
 * @since bbPress (r2727)
 *
 * @param mixed $args See {@link bbp_get_topic_close_link()}
 * @uses bbp_get_topic_close_link() To get the topic close link
 */
function bbp_topic_close_link( $args = '' ) {
	echo bbp_get_topic_close_link( $args );
}

	/**
	 * Return the close link of the topic
	 *
	 * @since bbPress (r2727)
	 *
	 * @param mixed $args This function supports these args:
	 *  - id: Optional. Topic id
	 *  - link_before: Before the link
	 *  - link_after: After the link
	 *  - close_text: Close text
	 *  - open_text: Open text
	 * @uses bbp_get_topic_id() To get the topic id
	 * @uses bbp_get_topic() To get the topic
	 * @uses current_user_can() To check if the current user can edit the topic
	 * @uses bbp_is_topic_open() To check if the topic is open
	 * @uses add_query_arg() To add custom args to the url
	 * @uses wp_nonce_url() To nonce the url
	 * @uses esc_url() To escape the url
	 * @uses apply_filters() Calls 'bbp_get_topic_close_link' with the link
	 *                        and args
	 * @return string Topic close link
	 */
	function bbp_get_topic_close_link( $args = '' ) {

		// Parse arguments against default values
		$r = bbp_parse_args( $args, array(
			'id'          => 0,
			'link_before' => '',
			'link_after'  => '',
			'sep'         => ' | ',
			'close_text'  => _x( 'Close', 'Topic Status', 'bbpress' ),
			'open_text'   => _x( 'Open',  'Topic Status', 'bbpress' )
		), 'get_topic_close_link' );

		$topic = bbp_get_topic( bbp_get_topic_id( (int) $r['id'] ) );

		if ( empty( $topic ) || !current_user_can( 'moderate', $topic->ID ) )
			return;

		$display = bbp_is_topic_open( $topic->ID ) ? $r['close_text'] : $r['open_text'];
		$uri     = add_query_arg( array( 'action' => 'bbp_toggle_topic_close', 'topic_id' => $topic->ID ) );
		$uri     = wp_nonce_url( $uri, 'close-topic_' . $topic->ID );
		$retval  = $r['link_before'] . '<a href="' . esc_url( $uri ) . '" class="bbp-topic-close-link">' . $display . '</a>' . $r['link_after'];

		return apply_filters( 'bbp_get_topic_close_link', $retval, $r );
	}

/**
 * Output the stick link of the topic
 *
 * @since bbPress (r2754)
 *
 * @param mixed $args See {@link bbp_get_topic_stick_link()}
 * @uses bbp_get_topic_stick_link() To get the topic stick link
 */
function bbp_topic_stick_link( $args = '' ) {
	echo bbp_get_topic_stick_link( $args );
}

	/**
	 * Return the stick link of the topic
	 *
	 * @since bbPress (r2754)
	 *
	 * @param mixed $args This function supports these args:
	 *  - id: Optional. Topic id
	 *  - link_before: Before the link
	 *  - link_after: After the link
	 *  - stick_text: Stick text
	 *  - unstick_text: Unstick text
	 *  - super_text: Stick to front text
	 * @uses bbp_get_topic_id() To get the topic id
	 * @uses bbp_get_topic() To get the topic
	 * @uses current_user_can() To check if the current user can edit the
	 *                           topic
	 * @uses bbp_is_topic_sticky() To check if the topic is a sticky
	 * @uses add_query_arg() To add custom args to the url
	 * @uses wp_nonce_url() To nonce the url
	 * @uses esc_url() To escape the url
	 * @uses apply_filters() Calls 'bbp_get_topic_stick_link' with the link
	 *                        and args
	 * @return string Topic stick link
	 */
	function bbp_get_topic_stick_link( $args = '' ) {

		// Parse arguments against default values
		$r = bbp_parse_args( $args, array(
			'id'           => 0,
			'link_before'  => '',
			'link_after'   => '',
			'stick_text'   => esc_html__( 'Stick',      'bbpress' ),
			'unstick_text' => esc_html__( 'Unstick',    'bbpress' ),
			'super_text'   => esc_html__( '(to front)', 'bbpress' ),
		), 'get_topic_stick_link' );

		$topic = bbp_get_topic( bbp_get_topic_id( (int) $r['id'] ) );

		if ( empty( $topic ) || !current_user_can( 'moderate', $topic->ID ) )
			return;

		$is_sticky = bbp_is_topic_sticky( $topic->ID );

		$stick_uri = add_query_arg( array( 'action' => 'bbp_toggle_topic_stick', 'topic_id' => $topic->ID ) );
		$stick_uri = wp_nonce_url( $stick_uri, 'stick-topic_' . $topic->ID );

		$stick_display = ( true === $is_sticky ) ? $r['unstick_text'] : $r['stick_text'];
		$stick_display = '<a href="' . esc_url( $stick_uri ) . '" class="bbp-topic-sticky-link">' . $stick_display . '</a>';

		if ( empty( $is_sticky ) ) {
			$super_uri = add_query_arg( array( 'action' => 'bbp_toggle_topic_stick', 'topic_id' => $topic->ID, 'super' => 1 ) );
			$super_uri = wp_nonce_url( $super_uri, 'stick-topic_' . $topic->ID );

			$super_display = ' <a href="' . esc_url( $super_uri ) . '" class="bbp-topic-super-sticky-link">' . $r['super_text'] . '</a>';
		} else {
			$super_display = '';
		}

		// Combine the HTML into 1 string
		$retval = $r['link_before'] . $stick_display . $super_display . $r['link_after'];

		return apply_filters( 'bbp_get_topic_stick_link', $retval, $r );
	}

/**
 * Output the merge link of the topic
 *
 * @since bbPress (r2756)
 *
 * @param mixed $args
 * @uses bbp_get_topic_merge_link() To get the topic merge link
 */
function bbp_topic_merge_link( $args = '' ) {
	echo bbp_get_topic_merge_link( $args );
}

	/**
	 * Return the merge link of the topic
	 *
	 * @since bbPress (r2756)
	 *
	 * @param mixed $args This function supports these args:
	 *  - id: Optional. Topic id
	 *  - link_before: Before the link
	 *  - link_after: After the link
	 *  - merge_text: Merge text
	 * @uses bbp_get_topic_id() To get the topic id
	 * @uses bbp_get_topic() To get the topic
	 * @uses bbp_get_topic_edit_url() To get the topic edit url
	 * @uses add_query_arg() To add custom args to the url
	 * @uses esc_url() To escape the url
	 * @uses apply_filters() Calls 'bbp_get_topic_merge_link' with the link
	 *                        and args
	 * @return string Topic merge link
	 */
	function bbp_get_topic_merge_link( $args = '' ) {

		// Parse arguments against default values
		$r = bbp_parse_args( $args, array(
			'id'           => 0,
			'link_before'  => '',
			'link_after'   => '',
			'merge_text'   => esc_html__( 'Merge', 'bbpress' ),
		), 'get_topic_merge_link' );

		$topic = bbp_get_topic( bbp_get_topic_id( (int) $r['id'] ) );

		if ( empty( $topic ) || !current_user_can( 'moderate', $topic->ID ) )
			return;

		$uri    = add_query_arg( array( 'action' => 'merge' ), bbp_get_topic_edit_url( $topic->ID ) );
		$retval = $r['link_before'] . '<a href="' . esc_url( $uri ) . '" class="bbp-topic-merge-link">' . $r['merge_text'] . '</a>' . $r['link_after'];

		return apply_filters( 'bbp_get_topic_merge_link', $retval, $args );
	}

/**
 * Output the spam link of the topic
 *
 * @since bbPress (r2727)
 *
 * @param mixed $args See {@link bbp_get_topic_spam_link()}
 * @uses bbp_get_topic_spam_link() Topic spam link
 */
function bbp_topic_spam_link( $args = '' ) {
	echo bbp_get_topic_spam_link( $args );
}

	/**
	 * Return the spam link of the topic
	 *
	 * @since bbPress (r2727)
	 *
	 * @param mixed $args This function supports these args:
	 *  - id: Optional. Topic id
	 *  - link_before: Before the link
	 *  - link_after: After the link
	 *  - spam_text: Spam text
	 *  - unspam_text: Unspam text
	 * @uses bbp_get_topic_id() To get the topic id
	 * @uses bbp_get_topic() To get the topic
	 * @uses current_user_can() To check if the current user can edit the topic
	 * @uses bbp_is_topic_spam() To check if the topic is marked as spam
	 * @uses add_query_arg() To add custom args to the url
	 * @uses wp_nonce_url() To nonce the url
	 * @uses esc_url() To escape the url
	 * @uses apply_filters() Calls 'bbp_get_topic_spam_link' with the link
	 *                        and args
	 * @return string Topic spam link
	 */
	function bbp_get_topic_spam_link( $args = '' ) {

		// Parse arguments against default values
		$r = bbp_parse_args( $args, array(
			'id'           => 0,
			'link_before'  => '',
			'link_after'   => '',
			'sep'          => ' | ',
			'spam_text'    => esc_html__( 'Spam',   'bbpress' ),
			'unspam_text'  => esc_html__( 'Unspam', 'bbpress' )
		), 'get_topic_spam_link' );

		$topic = bbp_get_topic( bbp_get_topic_id( (int) $r['id'] ) );

		if ( empty( $topic ) || !current_user_can( 'moderate', $topic->ID ) )
			return;

		$display = bbp_is_topic_spam( $topic->ID ) ? $r['unspam_text'] : $r['spam_text'];
		$uri     = add_query_arg( array( 'action' => 'bbp_toggle_topic_spam', 'topic_id' => $topic->ID ) );
		$uri     = wp_nonce_url( $uri, 'spam-topic_' . $topic->ID );
		$retval  = $r['link_before'] . '<a href="' . esc_url( $uri ) . '" class="bbp-topic-spam-link">' . $display . '</a>' . $r['link_after'];

		return apply_filters( 'bbp_get_topic_spam_link', $retval, $r );
	}

/**
 * Output the link to go directly to the reply form
 *
 * @since bbPress (r4966)
 *
 * @param array $args
 * @uses bbp_get_reply_to_link() To get the reply to link
 */
function bbp_topic_reply_link( $args = array() ) {
	echo bbp_get_topic_reply_link( $args );
}

	/**
	 * Return the link to go directly to the reply form
	 *
	 * @since bbPress (r4966)
	 *
	 * @param array $args Arguments
	 * @uses bbp_current_user_can_access_create_reply_form() To check permissions
	 * @uses bbp_get_topic_id() To validate the topic id
	 * @uses bbp_get_topic() To get the topic
	 * @uses apply_filters() Calls 'bbp_get_topic_reply_link' with the formatted link,
	 *                        the arguments array, and the topic
	 * @return string Link for a reply to a topic
	 */
	function bbp_get_topic_reply_link( $args = array() ) {

		// Parse arguments against default values
		$r = bbp_parse_args( $args, array(
			'id'           => 0,
			'link_before'  => '',
			'link_after'   => '',
			'reply_text'   => esc_html__( 'Reply', 'bbpress' ),
		), 'get_topic_reply_link' );

		// Get the reply to use it's ID and post_parent
		$topic = bbp_get_topic( bbp_get_topic_id( (int) $r['id'] ) );

		// Bail if no reply or user cannot reply
		if ( empty( $topic ) || ! bbp_current_user_can_access_create_reply_form() )
			return;

		$uri = '#new-post';

		// Add $uri to the array, to be passed through the filter
		$r['uri'] = $uri;
		$retval   = $r['link_before'] . '<a href="' . esc_url( $r['uri'] ) . '" class="bbp-topic-reply-link">' . $r['reply_text'] . '</a>' . $r['link_after'];

		return apply_filters( 'bbp_get_topic_reply_link', $retval, $r, $args );
	}

/** Topic Pagination **********************************************************/

/**
 * Output the pagination count
 *
 * @since bbPress (r2519)
 *
 * @uses bbp_get_forum_pagination_count() To get the forum pagination count
 */
function bbp_forum_pagination_count() {
	echo bbp_get_forum_pagination_count();
}
	/**
	 * Return the pagination count
	 *
	 * @since bbPress (r2519)
	 *
	 * @uses bbp_number_format() To format the number value
	 * @uses apply_filters() Calls 'bbp_get_forum_pagination_count' with the
	 *                        pagination count
	 * @return string Forum Pagintion count
	 */
	function bbp_get_forum_pagination_count() {
		$bbp = bbpress();

		if ( empty( $bbp->topic_query ) )
			return false;

		// Set pagination values
		$start_num = intval( ( $bbp->topic_query->paged - 1 ) * $bbp->topic_query->posts_per_page ) + 1;
		$from_num  = bbp_number_format( $start_num );
		$to_num    = bbp_number_format( ( $start_num + ( $bbp->topic_query->posts_per_page - 1 ) > $bbp->topic_query->found_posts ) ? $bbp->topic_query->found_posts : $start_num + ( $bbp->topic_query->posts_per_page - 1 ) );
		$total_int = (int) !empty( $bbp->topic_query->found_posts ) ? $bbp->topic_query->found_posts : $bbp->topic_query->post_count;
		$total     = bbp_number_format( $total_int );

		// Several topics in a forum with a single page
		if ( empty( $to_num ) ) {
			$retstr = sprintf( _n( 'Viewing %1$s topic', 'Viewing %1$s topics', $total_int, 'bbpress' ), $total );

		// Several topics in a forum with several pages
		} else {
			$retstr = sprintf( _n( 'Viewing topic %2$s (of %4$s total)', 'Viewing %1$s topics - %2$s through %3$s (of %4$s total)', $total_int, 'bbpress' ), $bbp->topic_query->post_count, $from_num, $to_num, $total );
		}

		// Filter and return
		return apply_filters( 'bbp_get_forum_pagination_count', esc_html( $retstr ) );
	}

/**
 * Output pagination links
 *
 * @since bbPress (r2519)
 *
 * @uses bbp_get_forum_pagination_links() To get the pagination links
 */
function bbp_forum_pagination_links() {
	echo bbp_get_forum_pagination_links();
}
	/**
	 * Return pagination links
	 *
	 * @since bbPress (r2519)
	 *
	 * @uses bbPress::topic_query::pagination_links To get the links
	 * @return string Pagination links
	 */
	function bbp_get_forum_pagination_links() {
		$bbp = bbpress();

		if ( empty( $bbp->topic_query ) )
			return false;

		return apply_filters( 'bbp_get_forum_pagination_links', $bbp->topic_query->pagination_links );
	}

/**
 * Displays topic notices
 *
 * @since bbPress (r2744)
 *
 * @uses bbp_is_single_topic() To check if it's a topic page
 * @uses bbp_get_topic_status() To get the topic status
 * @uses bbp_get_topic_id() To get the topic id
 * @uses apply_filters() Calls 'bbp_topic_notices' with the notice text, topic
 *                        status and topic id
 * @uses bbp_add_error() To add an error message
 */
function bbp_topic_notices() {

	// Bail if not viewing a topic
	if ( !bbp_is_single_topic() )
		return;

	// Get the topic_status
	$topic_status = bbp_get_topic_status();

	// Get the topic status
	switch ( $topic_status ) {

		// Spam notice
		case bbp_get_spam_status_id() :
			$notice_text = __( 'This topic is marked as spam.', 'bbpress' );
			break;

		// Trashed notice
		case bbp_get_trash_status_id() :
			$notice_text = __( 'This topic is in the trash.',   'bbpress' );
			break;

		// Standard status
		default :
			$notice_text = '';
			break;
	}

	// Filter notice text and bail if empty
	$notice_text = apply_filters( 'bbp_topic_notices', $notice_text, $topic_status, bbp_get_topic_id() );
	if ( empty( $notice_text ) )
		return;

	bbp_add_error( 'topic_notice', $notice_text, 'message' );
}

/**
 * Displays topic type select box (normal/sticky/super sticky)
 *
 * @since bbPress (r5059)
 * @deprecated since bbPress (r5059)
 *
 * @param $args This function supports these arguments:
 *  - select_id: Select id. Defaults to bbp_stick_topic
 *  - tab: Tabindex
 *  - topic_id: Topic id
 *  - selected: Override the selected option
 */
function bbp_topic_type_select( $args = '' ) {
	echo bbp_get_form_topic_type_dropdown( $args );
}

/**
 * Displays topic type select box (normal/sticky/super sticky)
 *
 * @since bbPress (r5059)
 *
 * @param $args This function supports these arguments:
 *  - select_id: Select id. Defaults to bbp_stick_topic
 *  - tab: Tabindex
 *  - topic_id: Topic id
 *  - selected: Override the selected option
 */
function bbp_form_topic_type_dropdown( $args = '' ) {
	echo bbp_get_form_topic_type_dropdown( $args );
}
	/**
	 * Returns topic type select box (normal/sticky/super sticky)
	 *
	 * @since bbPress (r5059)
	 *
	 * @param $args This function supports these arguments:
	 *  - select_id: Select id. Defaults to bbp_stick_topic
	 *  - tab: Tabindex
	 *  - topic_id: Topic id
	 *  - selected: Override the selected option
	 * @uses bbp_get_topic_id() To get the topic id
	 * @uses bbp_is_single_topic() To check if we're viewing a single topic
	 * @uses bbp_is_topic_edit() To check if it is the topic edit page
	 * @uses bbp_is_topic_super_sticky() To check if the topic is a super sticky
	 * @uses bbp_is_topic_sticky() To check if the topic is a sticky
	 */
	function bbp_get_form_topic_type_dropdown( $args = '' ) {

		// Parse arguments against default values
		$r = bbp_parse_args( $args, array(
			'select_id'    => 'bbp_stick_topic',
			'tab'          => bbp_get_tab_index(),
			'topic_id'     => 0,
			'selected'     => false
		), 'topic_type_select' );

		// No specific selected value passed
		if ( empty( $r['selected'] ) ) {

			// Post value is passed
			if ( bbp_is_post_request() && isset( $_POST[ $r['select_id'] ] ) ) {
				$r['selected'] = $_POST[ $r['select_id'] ];

			// No Post value passed
			} else {

				// Edit topic
				if ( bbp_is_single_topic() || bbp_is_topic_edit() ) {

					// Get current topic id
					$topic_id = bbp_get_topic_id( $r['topic_id'] );

					// Topic is super sticky
					if ( bbp_is_topic_super_sticky( $topic_id ) ) {
						$r['selected'] = 'super';

					// Topic is sticky or normal
					} else {
						$r['selected'] = bbp_is_topic_sticky( $topic_id, false ) ? 'stick' : 'unstick';
					}
				}
			}
		}

		// Used variables
		$tab = !empty( $r['tab'] ) ? ' tabindex="' . (int) $r['tab'] . '"' : '';

		// Start an output buffer, we'll finish it after the select loop
		ob_start(); ?>

		<select name="<?php echo esc_attr( $r['select_id'] ); ?>" id="<?php echo esc_attr( $r['select_id'] ); ?>_select"<?php echo $tab; ?>>

			<?php foreach ( bbp_get_topic_types() as $key => $label ) : ?>

				<option value="<?php echo esc_attr( $key ); ?>"<?php selected( $key, $r['selected'] ); ?>><?php echo esc_html( $label ); ?></option>

			<?php endforeach; ?>

		</select>

		<?php

		// Return the results
		return apply_filters( 'bbp_get_form_topic_type_dropdown', ob_get_clean(), $r );
	}

/**
 * Output value topic status dropdown
 *
 * @since bbPress (r5059)
 *
 * @param int $topic_id The topic id to use
 */
function bbp_form_topic_status_dropdown( $args = '' ) {
	echo bbp_get_form_topic_status_dropdown( $args );
}
	/**
	 * Returns topic status downdown
	 *
	 * This dropdown is only intended to be seen by users with the 'moderate'
	 * capability. Because of this, no additional capablitiy checks are performed
	 * within this function to check available topic statuses.
	 *
	 * @since bbPress (r5059)
	 *
	 * @param $args This function supports these arguments:
	 *  - select_id: Select id. Defaults to bbp_open_close_topic
	 *  - tab: Tabindex
	 *  - topic_id: Topic id
	 *  - selected: Override the selected option
	 */
	function bbp_get_form_topic_status_dropdown( $args = '' ) {

		// Parse arguments against default values
		$r = bbp_parse_args( $args, array(
			'select_id' => 'bbp_topic_status',
			'tab'       => bbp_get_tab_index(),
			'topic_id'  => 0,
			'selected'  => false
		), 'topic_open_close_select' );

		// No specific selected value passed
		if ( empty( $r['selected'] ) ) {

			// Post value is passed
			if ( bbp_is_post_request() && isset( $_POST[ $r['select_id'] ] ) ) {
				$r['selected'] = $_POST[ $r['select_id'] ];

			// No Post value was passed
			} else {

				// Edit topic
				if ( bbp_is_topic_edit() ) {
					$r['topic_id'] = bbp_get_topic_id( $r['topic_id'] );
					$r['selected'] = bbp_get_topic_status( $r['topic_id'] );

				// New topic
				} else {
					$r['selected'] = bbp_get_public_status_id();
				}
			}
		}

		// Used variables
		$tab = ! empty( $r['tab'] ) ? ' tabindex="' . (int) $r['tab'] . '"' : '';

		// Start an output buffer, we'll finish it after the select loop
		ob_start(); ?>

		<select name="<?php echo esc_attr( $r['select_id'] ) ?>" id="<?php echo esc_attr( $r['select_id'] ); ?>_select"<?php echo $tab; ?>>

			<?php foreach ( bbp_get_topic_statuses( $r['topic_id'] ) as $key => $label ) : ?>

				<option value="<?php echo esc_attr( $key ); ?>"<?php selected( $key, $r['selected'] ); ?>><?php echo esc_html( $label ); ?></option>

			<?php endforeach; ?>

		</select>

		<?php

		// Return the results
		return apply_filters( 'bbp_get_form_topic_status_dropdown', ob_get_clean(), $r );
	}

/** Single Topic **************************************************************/

/**
 * Output a fancy description of the current topic, including total topics,
 * total replies, and last activity.
 *
 * @since bbPress (r2860)
 *
 * @param array $args See {@link bbp_get_single_topic_description()}
 * @uses bbp_get_single_topic_description() Return the eventual output
 */
function bbp_single_topic_description( $args = '' ) {
	echo bbp_get_single_topic_description( $args );
}
	/**
	 * Return a fancy description of the current topic, including total topics,
	 * total replies, and last activity.
	 *
	 * @since bbPress (r2860)
	 *
	 * @param mixed $args This function supports these arguments:
	 *  - topic_id: Topic id
	 *  - before: Before the text
	 *  - after: After the text
	 *  - size: Size of the avatar
	 * @uses bbp_get_topic_id() To get the topic id
	 * @uses bbp_get_topic_voice_count() To get the topic voice count
	 * @uses bbp_get_topic_reply_count() To get the topic reply count
	 * @uses bbp_get_topic_freshness_link() To get the topic freshness link
	 * @uses bbp_get_topic_last_active_id() To get the topic last active id
	 * @uses bbp_get_reply_author_link() To get the reply author link
	 * @uses apply_filters() Calls 'bbp_get_single_topic_description' with
	 *                        the description and args
	 * @return string Filtered topic description
	 */
	function bbp_get_single_topic_description( $args = '' ) {

		// Parse arguments against default values
		$r = bbp_parse_args( $args, array(
			'topic_id'  => 0,
			'before'    => '<div class="bbp-template-notice info"><p class="bbp-topic-description">',
			'after'     => '</p></div>',
			'size'      => 14
		), 'get_single_topic_description' );

		// Validate topic_id
		$topic_id = bbp_get_topic_id( $r['topic_id'] );

		// Unhook the 'view all' query var adder
		remove_filter( 'bbp_get_topic_permalink', 'bbp_add_view_all' );

		// Build the topic description
		$vc_int      = bbp_get_topic_voice_count   ( $topic_id, true  );
		$voice_count = bbp_get_topic_voice_count   ( $topic_id, false );
		$reply_count = bbp_get_topic_replies_link  ( $topic_id        );
		$time_since  = bbp_get_topic_freshness_link( $topic_id        );

		// Singular/Plural
		$voice_count = sprintf( _n( '%s voice', '%s voices', $vc_int, 'bbpress' ), $voice_count );

		// Topic has replies
		$last_reply = bbp_get_topic_last_reply_id( $topic_id );
		if ( !empty( $last_reply ) ) {
			$last_updated_by = bbp_get_author_link( array( 'post_id' => $last_reply, 'size' => $r['size'] ) );
			$retstr          = sprintf( esc_html__( 'This topic contains %1$s, has %2$s, and was last updated by %3$s %4$s.', 'bbpress' ), $reply_count, $voice_count, $last_updated_by, $time_since );

		// Topic has no replies
		} elseif ( ! empty( $voice_count ) && ! empty( $reply_count ) ) {
			$retstr = sprintf( esc_html__( 'This topic contains %1$s and has %2$s.', 'bbpress' ), $voice_count, $reply_count );

		// Topic has no replies and no voices
		} elseif ( empty( $voice_count ) && empty( $reply_count ) ) {
			$retstr = sprintf( esc_html__( 'This topic has no replies.', 'bbpress' ), $voice_count, $reply_count );
		}

		// Add the 'view all' filter back
		add_filter( 'bbp_get_topic_permalink', 'bbp_add_view_all' );

		// Combine the elements together
		$retstr = $r['before'] . $retstr . $r['after'];

		// Return filtered result
		return apply_filters( 'bbp_get_single_topic_description', $retstr, $r );
	}

/** Topic Tags ****************************************************************/

/**
 * Output the unique id of the topic tag taxonomy
 *
 * @since bbPress (r3348)
 *
 * @uses bbp_get_topic_post_type() To get the topic post type
 */
function bbp_topic_tag_tax_id() {
	echo bbp_get_topic_tag_tax_id();
}
	/**
	 * Return the unique id of the topic tag taxonomy
	 *
	 * @since bbPress (r3348)
	 *
	 * @uses apply_filters() Calls 'bbp_get_topic_tag_tax_id' with the topic tax id
	 * @return string The unique topic tag taxonomy
	 */
	function bbp_get_topic_tag_tax_id() {
		return apply_filters( 'bbp_get_topic_tag_tax_id', bbpress()->topic_tag_tax_id );
	}

/**
 * Return array of labels used by the topic-tag taxonomy
 *
 * @since bbPress (r5129)
 *
 * @return array
 */
function bbp_get_topic_tag_tax_labels() {
	return apply_filters( 'bbp_get_topic_tag_tax_labels', array(
		'name'          => __( 'Topic Tags',     'bbpress' ),
		'singular_name' => __( 'Topic Tag',      'bbpress' ),
		'search_items'  => __( 'Search Tags',    'bbpress' ),
		'popular_items' => __( 'Popular Tags',   'bbpress' ),
		'all_items'     => __( 'All Tags',       'bbpress' ),
		'edit_item'     => __( 'Edit Tag',       'bbpress' ),
		'update_item'   => __( 'Update Tag',     'bbpress' ),
		'add_new_item'  => __( 'Add New Tag',    'bbpress' ),
		'new_item_name' => __( 'New Tag Name',   'bbpress' ),
		'view_item'     => __( 'View Topic Tag', 'bbpress' )
	) );
}

/**
 * Return an array of topic-tag taxonomy rewrite settings
 *
 * @since bbPress (r5129)
 *
 * @return array
 */
function bbp_get_topic_tag_tax_rewrite() {
	return apply_filters( 'bbp_get_topic_tag_tax_rewrite', array(
		'slug'       => bbp_get_topic_tag_tax_slug(),
		'with_front' => false
	) );
}

/**
 * Output the id of the current tag
 *
 * @since bbPress (r3109)
 *
 * @uses bbp_get_topic_tag_id()
 */
function bbp_topic_tag_id( $tag = '' ) {
	echo bbp_get_topic_tag_id( $tag );
}
	/**
	 * Return the id of the current tag
	 *
	 * @since bbPress (r3109)
	 *
	 * @uses get_term_by()
	 * @uses get_queried_object()
	 * @uses get_query_var()
	 * @uses apply_filters()
	 *
	 * @return string Term Name
	 */
	function bbp_get_topic_tag_id( $tag = '' ) {

		// Get the term
		if ( ! empty( $tag ) ) {
			$term = get_term_by( 'slug', $tag, bbp_get_topic_tag_tax_id() );
		} else {
			$tag  = get_query_var( 'term' );
			$term = get_queried_object();
		}

		// Add before and after if description exists
		if ( !empty( $term->term_id ) ) {
			$retval = $term->term_id;

		// No id
		} else {
			$retval = '';
		}

		return (int) apply_filters( 'bbp_get_topic_tag_id', (int) $retval, $tag );
	}

/**
 * Output the name of the current tag
 *
 * @since bbPress (r3109)
 *
 * @uses bbp_get_topic_tag_name()
 */
function bbp_topic_tag_name( $tag = '' ) {
	echo bbp_get_topic_tag_name( $tag );
}
	/**
	 * Return the name of the current tag
	 *
	 * @since bbPress (r3109)
	 *
	 * @uses get_term_by()
	 * @uses get_queried_object()
	 * @uses get_query_var()
	 * @uses apply_filters()
	 *
	 * @return string Term Name
	 */
	function bbp_get_topic_tag_name( $tag = '' ) {

		// Get the term
		if ( ! empty( $tag ) ) {
			$term = get_term_by( 'slug', $tag, bbp_get_topic_tag_tax_id() );
		} else {
			$tag  = get_query_var( 'term' );
			$term = get_queried_object();
		}

		// Add before and after if description exists
		if ( !empty( $term->name ) ) {
			$retval = $term->name;

		// No name
		} else {
			$retval = '';
		}

		return apply_filters( 'bbp_get_topic_tag_name', $retval );
	}

/**
 * Output the slug of the current tag
 *
 * @since bbPress (r3109)
 *
 * @uses bbp_get_topic_tag_slug()
 */
function bbp_topic_tag_slug( $tag = '' ) {
	echo bbp_get_topic_tag_slug( $tag );
}
	/**
	 * Return the slug of the current tag
	 *
	 * @since bbPress (r3109)
	 *
	 * @uses get_term_by()
	 * @uses get_queried_object()
	 * @uses get_query_var()
	 * @uses apply_filters()
	 *
	 * @return string Term Name
	 */
	function bbp_get_topic_tag_slug( $tag = '' ) {

		// Get the term
		if ( ! empty( $tag ) ) {
			$term = get_term_by( 'slug', $tag, bbp_get_topic_tag_tax_id() );
		} else {
			$tag  = get_query_var( 'term' );
			$term = get_queried_object();
		}

		// Add before and after if description exists
		if ( !empty( $term->slug ) ) {
			$retval = $term->slug;

		// No slug
		} else {
			$retval = '';
		}

		return apply_filters( 'bbp_get_topic_tag_slug', $retval );
	}

/**
 * Output the link of the current tag
 *
 * @since bbPress (r3348)
 *
 * @uses bbp_get_topic_tag_link()
 */
function bbp_topic_tag_link( $tag = '' ) {
	echo esc_url( bbp_get_topic_tag_link( $tag ) );
}
	/**
	 * Return the link of the current tag
	 *
	 * @since bbPress (r3348)
	 *
	 * @uses get_term_by()
	 * @uses get_queried_object()
	 * @uses get_query_var()
	 * @uses apply_filters()
	 *
	 * @return string Term Name
	 */
	function bbp_get_topic_tag_link( $tag = '' ) {

		// Get the term
		if ( ! empty( $tag ) ) {
			$term = get_term_by( 'slug', $tag, bbp_get_topic_tag_tax_id() );
		} else {
			$tag  = get_query_var( 'term' );
			$term = get_queried_object();
		}

		// Add before and after if description exists
		if ( !empty( $term->term_id ) ) {
			$retval = get_term_link( $term, bbp_get_topic_tag_tax_id() );

		// No link
		} else {
			$retval = '';
		}

		return apply_filters( 'bbp_get_topic_tag_link', $retval, $tag );
	}

/**
 * Output the link of the current tag
 *
 * @since bbPress (r3348)
 *
 * @uses bbp_get_topic_tag_edit_link()
 */
function bbp_topic_tag_edit_link( $tag = '' ) {
	echo esc_url( bbp_get_topic_tag_edit_link( $tag ) );
}
	/**
	 * Return the link of the current tag
	 *
	 * @since bbPress (r3348)
	 *
	 * @uses get_term_by()
	 * @uses get_queried_object()
	 * @uses get_query_var()
	 * @uses apply_filters()
	 *
	 * @return string Term Name
	 */
	function bbp_get_topic_tag_edit_link( $tag = '' ) {
		global $wp_rewrite;

		// Get the term
		if ( ! empty( $tag ) ) {
			$term = get_term_by( 'slug', $tag, bbp_get_topic_tag_tax_id() );
		} else {
			$tag  = get_query_var( 'term' );
			$term = get_queried_object();
		}

		// Add before and after if description exists
		if ( !empty( $term->term_id ) ) {

			$bbp = bbpress();

			// Pretty
			if ( $wp_rewrite->using_permalinks() ) {
				$retval = user_trailingslashit( trailingslashit( bbp_get_topic_tag_link() ) . $bbp->edit_id );

			// Ugly
			} else {
				$retval = add_query_arg( array( $bbp->edit_id => '1' ), bbp_get_topic_tag_link() );
			}

		// No link
		} else {
			$retval = '';
		}

		return apply_filters( 'bbp_get_topic_tag_edit_link', $retval, $tag );
	}

/**
 * Output the description of the current tag
 *
 * @since bbPress (r3109)
 *
 * @uses bbp_get_topic_tag_description()
 */
function bbp_topic_tag_description( $args = array() ) {
	echo bbp_get_topic_tag_description( $args );
}
	/**
	 * Return the description of the current tag
	 *
	 * @since bbPress (r3109)
	 *
	 * @uses get_term_by()
	 * @uses get_queried_object()
	 * @uses get_query_var()
	 * @uses apply_filters()
	 * @param array $args before|after|tag
	 *
	 * @return string Term Name
	 */
	function bbp_get_topic_tag_description( $args = array() ) {

		// Parse arguments against default values
		$r = bbp_parse_args( $args, array(
			'before' => '<div class="bbp-topic-tag-description"><p>',
			'after'  => '</p></div>',
			'tag'    => ''
		), 'get_topic_tag_description' );

		// Get the term
		if ( ! empty( $r['tag'] ) ) {
			$term = get_term_by( 'slug', $r['tag'], bbp_get_topic_tag_tax_id() );
		} else {
			$tag      = get_query_var( 'term' );
			$r['tag'] = $tag;
			$term     = get_queried_object();
		}

		// Add before and after if description exists
		if ( !empty( $term->description ) ) {
			$retval = $r['before'] . $term->description . $r['after'];

		// No description, no HTML
		} else {
			$retval = '';
		}

		return apply_filters( 'bbp_get_topic_tag_description', $retval, $r );
	}

/** Forms *********************************************************************/

/**
 * Output the value of topic title field
 *
 * @since bbPress (r2976)
 *
 * @uses bbp_get_form_topic_title() To get the value of topic title field
 */
function bbp_form_topic_title() {
	echo bbp_get_form_topic_title();
}
	/**
	 * Return the value of topic title field
	 *
	 * @since bbPress (r2976)
	 *
	 * @uses bbp_is_topic_edit() To check if it's topic edit page
	 * @uses apply_filters() Calls 'bbp_get_form_topic_title' with the title
	 * @return string Value of topic title field
	 */
	function bbp_get_form_topic_title() {

		// Get _POST data
		if ( bbp_is_post_request() && isset( $_POST['bbp_topic_title'] ) ) {
			$topic_title = $_POST['bbp_topic_title'];

		// Get edit data
		} elseif ( bbp_is_topic_edit() ) {
			$topic_title = bbp_get_global_post_field( 'post_title', 'raw' );

		// No data
		} else {
			$topic_title = '';
		}

		return apply_filters( 'bbp_get_form_topic_title', esc_attr( $topic_title ) );
	}

/**
 * Output the value of topic content field
 *
 * @since bbPress (r2976)
 *
 * @uses bbp_get_form_topic_content() To get value of topic content field
 */
function bbp_form_topic_content() {
	echo bbp_get_form_topic_content();
}
	/**
	 * Return the value of topic content field
	 *
	 * @since bbPress (r2976)
	 *
	 * @uses bbp_is_topic_edit() To check if it's the topic edit page
	 * @uses apply_filters() Calls 'bbp_get_form_topic_content' with the content
	 * @return string Value of topic content field
	 */
	function bbp_get_form_topic_content() {

		// Get _POST data
		if ( bbp_is_post_request() && isset( $_POST['bbp_topic_content'] ) ) {
			$topic_content = stripslashes( $_POST['bbp_topic_content'] );

		// Get edit data
		} elseif ( bbp_is_topic_edit() ) {
			$topic_content = bbp_get_global_post_field( 'post_content', 'raw' );

		// No data
		} else {
			$topic_content = '';
		}

		return apply_filters( 'bbp_get_form_topic_content', $topic_content );
	}

/**
 * Allow topic rows to have adminstrative actions
 *
 * @since bbPress (r3653)
 * @uses do_action()
 * @todo Links and filter
 */
function bbp_topic_row_actions() {
	do_action( 'bbp_topic_row_actions' );
}

/**
 * Output value of topic tags field
 *
 * @since bbPress (r2976)
 * @uses bbp_get_form_topic_tags() To get the value of topic tags field
 */
function bbp_form_topic_tags() {
	echo bbp_get_form_topic_tags();
}
	/**
	 * Return value of topic tags field
	 *
	 * @since bbPress (r2976)
	 *
	 * @uses bbp_is_topic_edit() To check if it's the topic edit page
	 * @uses apply_filters() Calls 'bbp_get_form_topic_tags' with the tags
	 * @return string Value of topic tags field
	 */
	function bbp_get_form_topic_tags() {

		// Get _POST data
		if ( bbp_is_post_request() && isset( $_POST['bbp_topic_tags'] ) ) {
			$topic_tags = $_POST['bbp_topic_tags'];

		// Get edit data
		} elseif ( bbp_is_single_topic() || bbp_is_single_reply() || bbp_is_topic_edit() || bbp_is_reply_edit() ) {

			// Determine the topic id based on the post type
			switch ( get_post_type() ) {

				// Post is a topic
				case bbp_get_topic_post_type() :
					$topic_id = get_the_ID();
					break;

				// Post is a reply
				case bbp_get_reply_post_type() :
					$topic_id = bbp_get_reply_topic_id( get_the_ID() );
					break;
			}

			// Topic exists
			if ( !empty( $topic_id ) ) {

				// Topic is spammed so display pre-spam terms
				if ( bbp_is_topic_spam( $topic_id ) ) {

					// Get pre-spam terms
					$new_terms = get_post_meta( $topic_id, '_bbp_spam_topic_tags', true );

					// If terms exist, explode them and compile the return value
					if ( empty( $new_terms ) ) {
						$new_terms = '';
					}

				// Topic is not spam so get real terms
				} else {
					$terms = array_filter( (array) get_the_terms( $topic_id, bbp_get_topic_tag_tax_id() ) );

					// Loop through them
					foreach ( $terms as $term ) {
						$new_terms[] = $term->name;
					}
				}

			// Define local variable(s)
			} else {
				$new_terms = '';
			}

			// Set the return value
			$topic_tags = ( !empty( $new_terms ) ) ? implode( ', ', $new_terms ) : '';

		// No data
		} else {
			$topic_tags = '';
		}

		return apply_filters( 'bbp_get_form_topic_tags', esc_attr( $topic_tags ) );
	}

/**
 * Output value of topic forum
 *
 * @since bbPress (r2976)
 *
 * @uses bbp_get_form_topic_forum() To get the topic's forum id
 */
function bbp_form_topic_forum() {
	echo bbp_get_form_topic_forum();
}
	/**
	 * Return value of topic forum
	 *
	 * @since bbPress (r2976)
	 *
	 * @uses bbp_is_topic_edit() To check if it's the topic edit page
	 * @uses bbp_get_topic_forum_id() To get the topic forum id
	 * @uses apply_filters() Calls 'bbp_get_form_topic_forum' with the forum
	 * @return string Value of topic content field
	 */
	function bbp_get_form_topic_forum() {

		// Get _POST data
		if ( bbp_is_post_request() && isset( $_POST['bbp_forum_id'] ) ) {
			$topic_forum = (int) $_POST['bbp_forum_id'];

		// Get edit data
		} elseif ( bbp_is_topic_edit() ) {
			$topic_forum = bbp_get_topic_forum_id();

		// No data
		} else {
			$topic_forum = 0;
		}

		return apply_filters( 'bbp_get_form_topic_forum', $topic_forum );
	}

/**
 * Output checked value of topic subscription
 *
 * @since bbPress (r2976)
 *
 * @uses bbp_get_form_topic_subscribed() To get the subscribed checkbox value
 */
function bbp_form_topic_subscribed() {
	echo bbp_get_form_topic_subscribed();
}
	/**
	 * Return checked value of topic subscription
	 *
	 * @since bbPress (r2976)
	 *
	 * @uses bbp_is_topic_edit() To check if it's the topic edit page
	 * @uses bbp_is_user_subscribed_to_topic() To check if the user is
	 *                                          subscribed to the topic
	 * @uses apply_filters() Calls 'bbp_get_form_topic_subscribed' with the
	 *                        option
	 * @return string Checked value of topic subscription
	 */
	function bbp_get_form_topic_subscribed() {

		// Get _POST data
		if ( bbp_is_post_request() && isset( $_POST['bbp_topic_subscription'] ) ) {
			$topic_subscribed = (bool) $_POST['bbp_topic_subscription'];

		// Get edit data
		} elseif ( bbp_is_topic_edit() || bbp_is_reply_edit() ) {

			// Get current posts author
			$post_author = bbp_get_global_post_field( 'post_author', 'raw' );

			// Post author is not the current user
			if ( bbp_get_current_user_id() !== $post_author ) {
				$topic_subscribed = bbp_is_user_subscribed_to_topic( $post_author );

			// Post author is the current user
			} else {
				$topic_subscribed = bbp_is_user_subscribed_to_topic( bbp_get_current_user_id() );
			}

		// Get current status
		} elseif ( bbp_is_single_topic() ) {
			$topic_subscribed = bbp_is_user_subscribed_to_topic( bbp_get_current_user_id() );

		// No data
		} else {
			$topic_subscribed = false;
		}

		// Get checked output
		$checked = checked( $topic_subscribed, true, false );

		return apply_filters( 'bbp_get_form_topic_subscribed', $checked, $topic_subscribed );
	}

/**
 * Output checked value of topic log edit field
 *
 * @since bbPress (r2976)
 *
 * @uses bbp_get_form_topic_log_edit() To get the topic log edit value
 */
function bbp_form_topic_log_edit() {
	echo bbp_get_form_topic_log_edit();
}
	/**
	 * Return checked value of topic log edit field
	 *
	 * @since bbPress (r2976)
	 *
	 * @uses apply_filters() Calls 'bbp_get_form_topic_log_edit' with the
	 *                        log edit value
	 * @return string Topic log edit checked value
	 */
	function bbp_get_form_topic_log_edit() {

		// Get _POST data
		if ( bbp_is_post_request() && isset( $_POST['bbp_log_topic_edit'] ) ) {
			$topic_revision = (int) $_POST['bbp_log_topic_edit'];

		// No data
		} else {
			$topic_revision = 1;
		}

		// Get checked output
		$checked = checked( $topic_revision, true, false );

		return apply_filters( 'bbp_get_form_topic_log_edit', $checked, $topic_revision );
	}

/**
 * Output the value of the topic edit reason
 *
 * @since bbPress (r2976)
 *
 * @uses bbp_get_form_topic_edit_reason() To get the topic edit reason value
 */
function bbp_form_topic_edit_reason() {
	echo bbp_get_form_topic_edit_reason();
}
	/**
	 * Return the value of the topic edit reason
	 *
	 * @since bbPress (r2976)
	 *
	 * @uses apply_filters() Calls 'bbp_get_form_topic_edit_reason' with the
	 *                        topic edit reason value
	 * @return string Topic edit reason value
	 */
	function bbp_get_form_topic_edit_reason() {

		// Get _POST data
		if ( bbp_is_post_request() && isset( $_POST['bbp_topic_edit_reason'] ) ) {
			$topic_edit_reason = $_POST['bbp_topic_edit_reason'];

		// No data
		} else {
			$topic_edit_reason = '';
		}

		return apply_filters( 'bbp_get_form_topic_edit_reason', esc_attr( $topic_edit_reason ) );
	}
