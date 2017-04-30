<?php

/**
 * bbPress Search Template Tags
 *
 * @package bbPress
 * @subpackage TemplateTags
 */

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

/** Search Loop Functions *****************************************************/

/**
 * The main search loop. WordPress does the heavy lifting.
 *
 * @since bbPress (r4579)
 *
 * @param mixed $args All the arguments supported by {@link WP_Query}
 * @uses bbp_get_view_all() Are we showing all results?
 * @uses bbp_get_public_status_id() To get the public status id
 * @uses bbp_get_closed_status_id() To get the closed status id
 * @uses bbp_get_spam_status_id() To get the spam status id
 * @uses bbp_get_trash_status_id() To get the trash status id
 * @uses bbp_get_forum_post_type() To get the forum post type
 * @uses bbp_get_topic_post_type() To get the topic post type
 * @uses bbp_get_reply_post_type() To get the reply post type
 * @uses bbp_get_replies_per_page() To get the replies per page option
 * @uses bbp_get_paged() To get the current page value
 * @uses bbp_get_search_terms() To get the search terms
 * @uses WP_Query To make query and get the search results
 * @uses WP_Rewrite::using_permalinks() To check if the blog is using permalinks
 * @uses bbp_get_search_url() To get the forum search url
 * @uses paginate_links() To paginate search results
 * @uses apply_filters() Calls 'bbp_has_search_results' with
 *                        bbPress::search_query::have_posts()
 *                        and bbPress::reply_query
 * @return object Multidimensional array of search information
 */
function bbp_has_search_results( $args = '' ) {
	global $wp_rewrite;

	/** Defaults **************************************************************/

	$default_post_type = array( bbp_get_forum_post_type(), bbp_get_topic_post_type(), bbp_get_reply_post_type() );

	// Default query args
	$default = array(
		'post_type'           => $default_post_type,         // Forums, topics, and replies
		'posts_per_page'      => bbp_get_replies_per_page(), // This many
		'paged'               => bbp_get_paged(),            // On this page
		'orderby'             => 'date',                     // Sorted by date
		'order'               => 'DESC',                     // Most recent first
		'ignore_sticky_posts' => true,                       // Stickies not supported
		's'                   => bbp_get_search_terms(),     // This is a search
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

	/** Setup *****************************************************************/

	// Parse arguments against default values
	$r = bbp_parse_args( $args, $default, 'has_search_results' );

	// Get bbPress
	$bbp = bbpress();

	// Call the query
	if ( ! empty( $r['s'] ) ) {
		$bbp->search_query = new WP_Query( $r );
	}

	// Add pagination values to query object
	$bbp->search_query->posts_per_page = $r['posts_per_page'];
	$bbp->search_query->paged          = $r['paged'];

	// Never home, regardless of what parse_query says
	$bbp->search_query->is_home        = false;

	// Only add pagination is query returned results
	if ( ! empty( $bbp->search_query->found_posts ) && ! empty( $bbp->search_query->posts_per_page ) ) {

		// Array of arguments to add after pagination links
		$add_args = array();

		// If pretty permalinks are enabled, make our pagination pretty
		if ( $wp_rewrite->using_permalinks() ) {

			// Shortcode territory
			if ( is_page() || is_single() ) {
				$base = trailingslashit( get_permalink() );

			// Default search location
			} else {
				$base = trailingslashit( bbp_get_search_results_url() );
			}

			// Add pagination base
			$base = $base . user_trailingslashit( $wp_rewrite->pagination_base . '/%#%/' );

		// Unpretty permalinks
		} else {
			$base = add_query_arg( 'paged', '%#%' );
		}

		// Add args
		if ( bbp_get_view_all() ) {
			$add_args['view'] = 'all';
		}

		// Add pagination to query object
		$bbp->search_query->pagination_links = paginate_links(
			apply_filters( 'bbp_search_results_pagination', array(
				'base'      => $base,
				'format'    => '',
				'total'     => ceil( (int) $bbp->search_query->found_posts / (int) $r['posts_per_page'] ),
				'current'   => (int) $bbp->search_query->paged,
				'prev_text' => is_rtl() ? '&rarr;' : '&larr;',
				'next_text' => is_rtl() ? '&larr;' : '&rarr;',
				'mid_size'  => 1,
				'add_args'  => $add_args, 
			) )
		);

		// Remove first page from pagination
		if ( $wp_rewrite->using_permalinks() ) {
			$bbp->search_query->pagination_links = str_replace( $wp_rewrite->pagination_base . '/1/', '', $bbp->search_query->pagination_links );
		} else {
			$bbp->search_query->pagination_links = str_replace( '&#038;paged=1', '', $bbp->search_query->pagination_links );
		}
	}

	// Return object
	return apply_filters( 'bbp_has_search_results', $bbp->search_query->have_posts(), $bbp->search_query );
}

/**
 * Whether there are more search results available in the loop
 *
 * @since bbPress (r4579)
 *
 * @uses WP_Query bbPress::search_query::have_posts() To check if there are more
 *                                                     search results available
 * @return object Search information
 */
function bbp_search_results() {

	// Put into variable to check against next
	$have_posts = bbpress()->search_query->have_posts();

	// Reset the post data when finished
	if ( empty( $have_posts ) )
		wp_reset_postdata();

	return $have_posts;
}

/**
 * Loads up the current search result in the loop
 *
 * @since bbPress (r4579)
 *
 * @uses WP_Query bbPress::search_query::the_post() To get the current search result
 * @return object Search information
 */
function bbp_the_search_result() {
	$search_result = bbpress()->search_query->the_post();

	// Reset each current (forum|topic|reply) id
	bbpress()->current_forum_id = bbp_get_forum_id();
	bbpress()->current_topic_id = bbp_get_topic_id();
	bbpress()->current_reply_id = bbp_get_reply_id();

	return $search_result;
}

/**
 * Output the search page title
 *
 * @since bbPress (r4579)
 *
 * @uses bbp_get_search_title()
 */
function bbp_search_title() {
	echo bbp_get_search_title();
}

	/**
	 * Get the search page title
	 *
	 * @since bbPress (r4579)
	 *
	 * @uses bbp_get_search_terms()
	 */
	function bbp_get_search_title() {

		// Get search terms
		$search_terms = bbp_get_search_terms();

		// No search terms specified
		if ( empty( $search_terms ) ) {
			$title = esc_html__( 'Search', 'bbpress' );

		// Include search terms in title
		} else {
			$title = sprintf( esc_html__( "Search Results for '%s'", 'bbpress' ), esc_attr( $search_terms ) );
		}

		return apply_filters( 'bbp_get_search_title', $title, $search_terms );
	}

/**
 * Output the search url
 *
 * @since bbPress (r4579)
 *
 * @uses bbp_get_search_url() To get the search url
 */
function bbp_search_url() {
	echo esc_url( bbp_get_search_url() );
}
	/**
	 * Return the search url
	 *
	 * @since bbPress (r4579)
	 *
	 * @uses user_trailingslashit() To fix slashes
	 * @uses trailingslashit() To fix slashes
	 * @uses bbp_get_forums_url() To get the root forums url
	 * @uses bbp_get_search_slug() To get the search slug
	 * @uses add_query_arg() To help make unpretty permalinks
	 * @return string Search url
	 */
	function bbp_get_search_url() {
		global $wp_rewrite;

		// Pretty permalinks
		if ( $wp_rewrite->using_permalinks() ) {
			$url = $wp_rewrite->root . bbp_get_search_slug();
			$url = home_url( user_trailingslashit( $url ) );

		// Unpretty permalinks
		} else {
			$url = add_query_arg( array( bbp_get_search_rewrite_id() => '' ), home_url( '/' ) );
		}

		return apply_filters( 'bbp_get_search_url', $url );
	}

/**
 * Output the search results url
 *
 * @since bbPress (r4928)
 *
 * @uses bbp_get_search_url() To get the search url
 */
function bbp_search_results_url() {
	echo esc_url( bbp_get_search_results_url() );
}
	/**
	 * Return the search url
	 *
	 * @since bbPress (r4928)
	 *
	 * @uses user_trailingslashit() To fix slashes
	 * @uses trailingslashit() To fix slashes
	 * @uses bbp_get_forums_url() To get the root forums url
	 * @uses bbp_get_search_slug() To get the search slug
	 * @uses add_query_arg() To help make unpretty permalinks
	 * @return string Search url
	 */
	function bbp_get_search_results_url() {
		global $wp_rewrite;

		// Get the search terms
		$search_terms = bbp_get_search_terms();

		// Pretty permalinks
		if ( $wp_rewrite->using_permalinks() ) {

			// Root search URL
			$url = $wp_rewrite->root . bbp_get_search_slug();

			// Append search terms
			if ( !empty( $search_terms ) ) {
				$url = trailingslashit( $url ) . user_trailingslashit( urlencode( $search_terms ) );
			}

			// Run through home_url()
			$url = home_url( user_trailingslashit( $url ) );

		// Unpretty permalinks
		} else {
			$url = add_query_arg( array( bbp_get_search_rewrite_id() => urlencode( $search_terms ) ), home_url( '/' ) );
		}

		return apply_filters( 'bbp_get_search_results_url', $url );
	}

/**
 * Output the search terms
 *
 * @since bbPress (r4579)
 *
 * @param string $search_terms Optional. Search terms
 * @uses bbp_get_search_terms() To get the search terms
 */
function bbp_search_terms( $search_terms = '' ) {
	echo bbp_get_search_terms( $search_terms );
}

	/**
	 * Get the search terms
	 *
	 * @since bbPress (r4579)
	 *
	 * If search terms are supplied, those are used. Otherwise check the
	 * search rewrite id query var.
	 *
	 * @param string $passed_terms Optional. Search terms
	 * @uses sanitize_title() To sanitize the search terms
	 * @uses get_query_var() To get the search terms from query variable
	 * @return bool|string Search terms on success, false on failure
	 */
	function bbp_get_search_terms( $passed_terms = '' ) {

		// Sanitize terms if they were passed in
		if ( !empty( $passed_terms ) ) {
			$search_terms = sanitize_title( $passed_terms );

		// Use query variable if not
		} else {
			$search_terms = get_query_var( bbp_get_search_rewrite_id() );
		}

		// Trim whitespace and decode, or set explicitly to false if empty
		$search_terms = !empty( $search_terms ) ? urldecode( trim( $search_terms ) ) : false;

		return apply_filters( 'bbp_get_search_terms', $search_terms, $passed_terms );
	}

/**
 * Output the search result pagination count
 *
 * @since bbPress (r4579)
 *
 * @uses bbp_get_search_pagination_count() To get the search result pagination count
 */
function bbp_search_pagination_count() {
	echo bbp_get_search_pagination_count();
}

	/**
	 * Return the search results pagination count
	 *
	 * @since bbPress (r4579)
	 *
	 * @uses bbp_number_format() To format the number value
	 * @uses apply_filters() Calls 'bbp_get_search_pagination_count' with the
	 *                        pagination count
	 * @return string Search pagination count
	 */
	function bbp_get_search_pagination_count() {
		$bbp = bbpress();

		// Define local variable(s)
		$retstr = '';

		// Set pagination values
		$start_num = intval( ( $bbp->search_query->paged - 1 ) * $bbp->search_query->posts_per_page ) + 1;
		$from_num  = bbp_number_format( $start_num );
		$to_num    = bbp_number_format( ( $start_num + ( $bbp->search_query->posts_per_page - 1 ) > $bbp->search_query->found_posts ) ? $bbp->search_query->found_posts : $start_num + ( $bbp->search_query->posts_per_page - 1 ) );
		$total_int = (int) $bbp->search_query->found_posts;
		$total     = bbp_number_format( $total_int );

		// Single page of results
		if ( empty( $to_num ) ) {
			$retstr = sprintf( _n( 'Viewing %1$s result', 'Viewing %1$s results', $total_int, 'bbpress' ), $total );

		// Several pages of results
		} else {
			$retstr = sprintf( _n( 'Viewing %2$s results (of %4$s total)', 'Viewing %1$s results - %2$s through %3$s (of %4$s total)', $bbp->search_query->post_count, 'bbpress' ), $bbp->search_query->post_count, $from_num, $to_num, $total );

		}

		// Filter and return
		return apply_filters( 'bbp_get_search_pagination_count', esc_html( $retstr ) );
	}

/**
 * Output search pagination links
 *
 * @since bbPress (r4579)
 *
 * @uses bbp_get_search_pagination_links() To get the search pagination links
 */
function bbp_search_pagination_links() {
	echo bbp_get_search_pagination_links();
}

	/**
	 * Return search pagination links
	 *
	 * @since bbPress (r4579)
	 *
	 * @uses apply_filters() Calls 'bbp_get_search_pagination_links' with the
	 *                        pagination links
	 * @return string Search pagination links
	 */
	function bbp_get_search_pagination_links() {
		$bbp = bbpress();

		if ( !isset( $bbp->search_query->pagination_links ) || empty( $bbp->search_query->pagination_links ) )
			return false;

		return apply_filters( 'bbp_get_search_pagination_links', $bbp->search_query->pagination_links );
	}
