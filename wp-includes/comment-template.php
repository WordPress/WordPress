<?php
/**
 * Comment template functions
 *
 * These functions are meant to live inside of the WordPress loop.
 *
 * @package WordPress
 * @subpackage Template
 */

/**
 * Retrieves the author of the current comment.
 *
 * If the comment has an empty comment_author field, then 'Anonymous' person is
 * assumed.
 *
 * @since 1.5.0
 * @since 4.4.0 Added the ability for `$comment_ID` to also accept a WP_Comment object.
 *
 * @param int|WP_Comment $comment_ID Optional. WP_Comment or the ID of the comment for which to retrieve the author.
 *                                   Default current comment.
 * @return string The comment author
 */
function get_comment_author( $comment_ID = 0 ) {
	$comment = get_comment( $comment_ID );

	if ( empty( $comment->comment_author ) ) {
		$user = $comment->user_id ? get_userdata( $comment->user_id ) : false;
		if ( $user ) {
			$author = $user->display_name;
		} else {
			$author = __( 'Anonymous' );
		}
	} else {
		$author = $comment->comment_author;
	}

	/**
	 * Filters the returned comment author name.
	 *
	 * @since 1.5.0
	 * @since 4.1.0 The `$comment_ID` and `$comment` parameters were added.
	 *
	 * @param string     $author     The comment author's username.
	 * @param int        $comment_ID The comment ID.
	 * @param WP_Comment $comment    The comment object.
	 */
	return apply_filters( 'get_comment_author', $author, $comment->comment_ID, $comment );
}

/**
 * Displays the author of the current comment.
 *
 * @since 0.71
 * @since 4.4.0 Added the ability for `$comment_ID` to also accept a WP_Comment object.
 *
 * @param int|WP_Comment $comment_ID Optional. WP_Comment or the ID of the comment for which to print the author.
 *                                   Default current comment.
 */
function comment_author( $comment_ID = 0 ) {
	$comment = get_comment( $comment_ID );
	$author  = get_comment_author( $comment );

	/**
	 * Filters the comment author's name for display.
	 *
	 * @since 1.2.0
	 * @since 4.1.0 The `$comment_ID` parameter was added.
	 *
	 * @param string $author     The comment author's username.
	 * @param int    $comment_ID The comment ID.
	 */
	echo apply_filters( 'comment_author', $author, $comment->comment_ID );
}

/**
 * Retrieves the email of the author of the current comment.
 *
 * @since 1.5.0
 * @since 4.4.0 Added the ability for `$comment_ID` to also accept a WP_Comment object.
 *
 * @param int|WP_Comment $comment_ID Optional. WP_Comment or the ID of the comment for which to get the author's email.
 *                                   Default current comment.
 * @return string The current comment author's email
 */
function get_comment_author_email( $comment_ID = 0 ) {
	$comment = get_comment( $comment_ID );

	/**
	 * Filters the comment author's returned email address.
	 *
	 * @since 1.5.0
	 * @since 4.1.0 The `$comment_ID` and `$comment` parameters were added.
	 *
	 * @param string     $comment_author_email The comment author's email address.
	 * @param int        $comment_ID           The comment ID.
	 * @param WP_Comment $comment              The comment object.
	 */
	return apply_filters( 'get_comment_author_email', $comment->comment_author_email, $comment->comment_ID, $comment );
}

/**
 * Displays the email of the author of the current global $comment.
 *
 * Care should be taken to protect the email address and assure that email
 * harvesters do not capture your commenter's email address. Most assume that
 * their email address will not appear in raw form on the site. Doing so will
 * enable anyone, including those that people don't want to get the email
 * address and use it for their own means good and bad.
 *
 * @since 0.71
 * @since 4.4.0 Added the ability for `$comment_ID` to also accept a WP_Comment object.
 *
 * @param int|WP_Comment $comment_ID Optional. WP_Comment or the ID of the comment for which to print the author's email.
 *                                   Default current comment.
 */
function comment_author_email( $comment_ID = 0 ) {
	$comment      = get_comment( $comment_ID );
	$author_email = get_comment_author_email( $comment );

	/**
	 * Filters the comment author's email for display.
	 *
	 * @since 1.2.0
	 * @since 4.1.0 The `$comment_ID` parameter was added.
	 *
	 * @param string $author_email The comment author's email address.
	 * @param int    $comment_ID   The comment ID.
	 */
	echo apply_filters( 'author_email', $author_email, $comment->comment_ID );
}

/**
 * Displays the HTML email link to the author of the current comment.
 *
 * Care should be taken to protect the email address and assure that email
 * harvesters do not capture your commenter's email address. Most assume that
 * their email address will not appear in raw form on the site. Doing so will
 * enable anyone, including those that people don't want to get the email
 * address and use it for their own means good and bad.
 *
 * @since 0.71
 * @since 4.6.0 Added the `$comment` parameter.
 *
 * @param string         $linktext Optional. Text to display instead of the comment author's email address.
 *                                 Default empty.
 * @param string         $before   Optional. Text or HTML to display before the email link. Default empty.
 * @param string         $after    Optional. Text or HTML to display after the email link. Default empty.
 * @param int|WP_Comment $comment  Optional. Comment ID or WP_Comment object. Default is the current comment.
 */
function comment_author_email_link( $linktext = '', $before = '', $after = '', $comment = null ) {
	$link = get_comment_author_email_link( $linktext, $before, $after, $comment );
	if ( $link ) {
		echo $link;
	}
}

/**
 * Returns the HTML email link to the author of the current comment.
 *
 * Care should be taken to protect the email address and assure that email
 * harvesters do not capture your commenter's email address. Most assume that
 * their email address will not appear in raw form on the site. Doing so will
 * enable anyone, including those that people don't want to get the email
 * address and use it for their own means good and bad.
 *
 * @since 2.7.0
 * @since 4.6.0 Added the `$comment` parameter.
 *
 * @param string         $linktext Optional. Text to display instead of the comment author's email address.
 *                                 Default empty.
 * @param string         $before   Optional. Text or HTML to display before the email link. Default empty.
 * @param string         $after    Optional. Text or HTML to display after the email link. Default empty.
 * @param int|WP_Comment $comment  Optional. Comment ID or WP_Comment object. Default is the current comment.
 * @return string HTML markup for the comment author email link. By default, the email address is obfuscated
 *                via the {@see 'comment_email'} filter with antispambot().
 */
function get_comment_author_email_link( $linktext = '', $before = '', $after = '', $comment = null ) {
	$comment = get_comment( $comment );

	/**
	 * Filters the comment author's email for display.
	 *
	 * Care should be taken to protect the email address and assure that email
	 * harvesters do not capture your commenter's email address.
	 *
	 * @since 1.2.0
	 * @since 4.1.0 The `$comment` parameter was added.
	 *
	 * @param string     $comment_author_email The comment author's email address.
	 * @param WP_Comment $comment              The comment object.
	 */
	$email = apply_filters( 'comment_email', $comment->comment_author_email, $comment );

	if ( ( ! empty( $email ) ) && ( '@' !== $email ) ) {
		$display = ( '' !== $linktext ) ? $linktext : $email;
		$return  = $before;
		$return .= sprintf( '<a href="%1$s">%2$s</a>', esc_url( 'mailto:' . $email ), esc_html( $display ) );
		$return .= $after;
		return $return;
	} else {
		return '';
	}
}

/**
 * Retrieves the HTML link to the URL of the author of the current comment.
 *
 * Both get_comment_author_url() and get_comment_author() rely on get_comment(),
 * which falls back to the global comment variable if the $comment_ID argument is empty.
 *
 * @since 1.5.0
 * @since 4.4.0 Added the ability for `$comment_ID` to also accept a WP_Comment object.
 *
 * @param int|WP_Comment $comment_ID Optional. WP_Comment or the ID of the comment for which to get the author's link.
 *                                   Default current comment.
 * @return string The comment author name or HTML link for author's URL.
 */
function get_comment_author_link( $comment_ID = 0 ) {
	$comment = get_comment( $comment_ID );
	$url     = get_comment_author_url( $comment );
	$author  = get_comment_author( $comment );

	if ( empty( $url ) || 'http://' === $url ) {
		$return = $author;
	} else {
		$return = "<a href='$url' rel='external nofollow ugc' class='url'>$author</a>";
	}

	/**
	 * Filters the comment author's link for display.
	 *
	 * @since 1.5.0
	 * @since 4.1.0 The `$author` and `$comment_ID` parameters were added.
	 *
	 * @param string $return     The HTML-formatted comment author link.
	 *                           Empty for an invalid URL.
	 * @param string $author     The comment author's username.
	 * @param int    $comment_ID The comment ID.
	 */
	return apply_filters( 'get_comment_author_link', $return, $author, $comment->comment_ID );
}

/**
 * Displays the HTML link to the URL of the author of the current comment.
 *
 * @since 0.71
 * @since 4.4.0 Added the ability for `$comment_ID` to also accept a WP_Comment object.
 *
 * @param int|WP_Comment $comment_ID Optional. WP_Comment or the ID of the comment for which to print the author's link.
 *                                   Default current comment.
 */
function comment_author_link( $comment_ID = 0 ) {
	echo get_comment_author_link( $comment_ID );
}

/**
 * Retrieve the IP address of the author of the current comment.
 *
 * @since 1.5.0
 * @since 4.4.0 Added the ability for `$comment_ID` to also accept a WP_Comment object.
 *
 * @param int|WP_Comment $comment_ID Optional. WP_Comment or the ID of the comment for which to get the author's IP address.
 *                                   Default current comment.
 * @return string Comment author's IP address.
 */
function get_comment_author_IP( $comment_ID = 0 ) { // phpcs:ignore WordPress.NamingConventions.ValidFunctionName.FunctionNameInvalid
	$comment = get_comment( $comment_ID );

	/**
	 * Filters the comment author's returned IP address.
	 *
	 * @since 1.5.0
	 * @since 4.1.0 The `$comment_ID` and `$comment` parameters were added.
	 *
	 * @param string     $comment_author_IP The comment author's IP address.
	 * @param int        $comment_ID        The comment ID.
	 * @param WP_Comment $comment           The comment object.
	 */
	return apply_filters( 'get_comment_author_IP', $comment->comment_author_IP, $comment->comment_ID, $comment );  // phpcs:ignore WordPress.NamingConventions.ValidHookName.NotLowercase
}

/**
 * Displays the IP address of the author of the current comment.
 *
 * @since 0.71
 * @since 4.4.0 Added the ability for `$comment_ID` to also accept a WP_Comment object.
 *
 * @param int|WP_Comment $comment_ID Optional. WP_Comment or the ID of the comment for which to print the author's IP address.
 *                                   Default current comment.
 */
function comment_author_IP( $comment_ID = 0 ) { // phpcs:ignore WordPress.NamingConventions.ValidFunctionName.FunctionNameInvalid
	echo esc_html( get_comment_author_IP( $comment_ID ) );
}

/**
 * Retrieves the URL of the author of the current comment, not linked.
 *
 * @since 1.5.0
 * @since 4.4.0 Added the ability for `$comment_ID` to also accept a WP_Comment object.
 *
 * @param int|WP_Comment $comment_ID Optional. WP_Comment or the ID of the comment for which to get the author's URL.
 *                                   Default current comment.
 * @return string Comment author URL, if provided, an empty string otherwise.
 */
function get_comment_author_url( $comment_ID = 0 ) {
	$comment = get_comment( $comment_ID );
	$url     = '';
	$id      = 0;

	if ( ! empty( $comment ) ) {
		$author_url = ( 'http://' === $comment->comment_author_url ) ? '' : $comment->comment_author_url;
		$url        = esc_url( $author_url, array( 'http', 'https' ) );
		$id         = $comment->comment_ID;
	}

	/**
	 * Filters the comment author's URL.
	 *
	 * @since 1.5.0
	 * @since 4.1.0 The `$comment_ID` and `$comment` parameters were added.
	 *
	 * @param string     $url        The comment author's URL.
	 * @param int        $comment_ID The comment ID.
	 * @param WP_Comment $comment    The comment object.
	 */
	return apply_filters( 'get_comment_author_url', $url, $id, $comment );
}

/**
 * Displays the URL of the author of the current comment, not linked.
 *
 * @since 0.71
 * @since 4.4.0 Added the ability for `$comment_ID` to also accept a WP_Comment object.
 *
 * @param int|WP_Comment $comment_ID Optional. WP_Comment or the ID of the comment for which to print the author's URL.
 *                                   Default current comment.
 */
function comment_author_url( $comment_ID = 0 ) {
	$comment    = get_comment( $comment_ID );
	$author_url = get_comment_author_url( $comment );

	/**
	 * Filters the comment author's URL for display.
	 *
	 * @since 1.2.0
	 * @since 4.1.0 The `$comment_ID` parameter was added.
	 *
	 * @param string $author_url The comment author's URL.
	 * @param int    $comment_ID The comment ID.
	 */
	echo apply_filters( 'comment_url', $author_url, $comment->comment_ID );
}

/**
 * Retrieves the HTML link of the URL of the author of the current comment.
 *
 * $linktext parameter is only used if the URL does not exist for the comment
 * author. If the URL does exist then the URL will be used and the $linktext
 * will be ignored.
 *
 * Encapsulate the HTML link between the $before and $after. So it will appear
 * in the order of $before, link, and finally $after.
 *
 * @since 1.5.0
 * @since 4.6.0 Added the `$comment` parameter.
 *
 * @param string         $linktext Optional. The text to display instead of the comment
 *                                 author's email address. Default empty.
 * @param string         $before   Optional. The text or HTML to display before the email link.
 *                                 Default empty.
 * @param string         $after    Optional. The text or HTML to display after the email link.
 *                                 Default empty.
 * @param int|WP_Comment $comment  Optional. Comment ID or WP_Comment object.
 *                                 Default is the current comment.
 * @return string The HTML link between the $before and $after parameters.
 */
function get_comment_author_url_link( $linktext = '', $before = '', $after = '', $comment = 0 ) {
	$url     = get_comment_author_url( $comment );
	$display = ( '' !== $linktext ) ? $linktext : $url;
	$display = str_replace( 'http://www.', '', $display );
	$display = str_replace( 'http://', '', $display );

	if ( '/' === substr( $display, -1 ) ) {
		$display = substr( $display, 0, -1 );
	}

	$return = "$before<a href='$url' rel='external'>$display</a>$after";

	/**
	 * Filters the comment author's returned URL link.
	 *
	 * @since 1.5.0
	 *
	 * @param string $return The HTML-formatted comment author URL link.
	 */
	return apply_filters( 'get_comment_author_url_link', $return );
}

/**
 * Displays the HTML link of the URL of the author of the current comment.
 *
 * @since 0.71
 * @since 4.6.0 Added the `$comment` parameter.
 *
 * @param string         $linktext Optional. Text to display instead of the comment author's
 *                                 email address. Default empty.
 * @param string         $before   Optional. Text or HTML to display before the email link.
 *                                 Default empty.
 * @param string         $after    Optional. Text or HTML to display after the email link.
 *                                 Default empty.
 * @param int|WP_Comment $comment  Optional. Comment ID or WP_Comment object.
 *                                 Default is the current comment.
 */
function comment_author_url_link( $linktext = '', $before = '', $after = '', $comment = 0 ) {
	echo get_comment_author_url_link( $linktext, $before, $after, $comment );
}

/**
 * Generates semantic classes for each comment element.
 *
 * @since 2.7.0
 * @since 4.4.0 Added the ability for `$comment` to also accept a WP_Comment object.
 *
 * @param string|array   $class    Optional. One or more classes to add to the class list.
 *                                 Default empty.
 * @param int|WP_Comment $comment  Comment ID or WP_Comment object. Default current comment.
 * @param int|WP_Post    $post_id  Post ID or WP_Post object. Default current post.
 * @param bool           $echo     Optional. Whether to echo or return the output.
 *                                 Default true.
 * @return void|string Void if `$echo` argument is true, comment classes if `$echo` is false.
 */
function comment_class( $class = '', $comment = null, $post_id = null, $echo = true ) {
	// Separates classes with a single space, collates classes for comment DIV.
	$class = 'class="' . join( ' ', get_comment_class( $class, $comment, $post_id ) ) . '"';

	if ( $echo ) {
		echo $class;
	} else {
		return $class;
	}
}

/**
 * Returns the classes for the comment div as an array.
 *
 * @since 2.7.0
 * @since 4.4.0 Added the ability for `$comment_id` to also accept a WP_Comment object.
 *
 * @global int $comment_alt
 * @global int $comment_depth
 * @global int $comment_thread_alt
 *
 * @param string|array   $class      Optional. One or more classes to add to the class list. Default empty.
 * @param int|WP_Comment $comment_id Comment ID or WP_Comment object. Default current comment.
 * @param int|WP_Post    $post_id    Post ID or WP_Post object. Default current post.
 * @return string[] An array of classes.
 */
function get_comment_class( $class = '', $comment_id = null, $post_id = null ) {
	global $comment_alt, $comment_depth, $comment_thread_alt;

	$classes = array();

	$comment = get_comment( $comment_id );
	if ( ! $comment ) {
		return $classes;
	}

	// Get the comment type (comment, trackback).
	$classes[] = ( empty( $comment->comment_type ) ) ? 'comment' : $comment->comment_type;

	// Add classes for comment authors that are registered users.
	$user = $comment->user_id ? get_userdata( $comment->user_id ) : false;
	if ( $user ) {
		$classes[] = 'byuser';
		$classes[] = 'comment-author-' . sanitize_html_class( $user->user_nicename, $comment->user_id );
		// For comment authors who are the author of the post.
		$post = get_post( $post_id );
		if ( $post ) {
			if ( $comment->user_id === $post->post_author ) {
				$classes[] = 'bypostauthor';
			}
		}
	}

	if ( empty( $comment_alt ) ) {
		$comment_alt = 0;
	}
	if ( empty( $comment_depth ) ) {
		$comment_depth = 1;
	}
	if ( empty( $comment_thread_alt ) ) {
		$comment_thread_alt = 0;
	}

	if ( $comment_alt % 2 ) {
		$classes[] = 'odd';
		$classes[] = 'alt';
	} else {
		$classes[] = 'even';
	}

	$comment_alt++;

	// Alt for top-level comments.
	if ( 1 == $comment_depth ) {
		if ( $comment_thread_alt % 2 ) {
			$classes[] = 'thread-odd';
			$classes[] = 'thread-alt';
		} else {
			$classes[] = 'thread-even';
		}
		$comment_thread_alt++;
	}

	$classes[] = "depth-$comment_depth";

	if ( ! empty( $class ) ) {
		if ( ! is_array( $class ) ) {
			$class = preg_split( '#\s+#', $class );
		}
		$classes = array_merge( $classes, $class );
	}

	$classes = array_map( 'esc_attr', $classes );

	/**
	 * Filters the returned CSS classes for the current comment.
	 *
	 * @since 2.7.0
	 *
	 * @param string[]    $classes    An array of comment classes.
	 * @param string      $class      A comma-separated list of additional classes added to the list.
	 * @param int         $comment_id The comment ID.
	 * @param WP_Comment  $comment    The comment object.
	 * @param int|WP_Post $post_id    The post ID or WP_Post object.
	 */
	return apply_filters( 'comment_class', $classes, $class, $comment->comment_ID, $comment, $post_id );
}

/**
 * Retrieves the comment date of the current comment.
 *
 * @since 1.5.0
 * @since 4.4.0 Added the ability for `$comment_ID` to also accept a WP_Comment object.
 *
 * @param string         $format     Optional. PHP date format. Defaults to the 'date_format' option.
 * @param int|WP_Comment $comment_ID WP_Comment or ID of the comment for which to get the date.
 *                                   Default current comment.
 * @return string The comment's date.
 */
function get_comment_date( $format = '', $comment_ID = 0 ) {
	$comment = get_comment( $comment_ID );

	$_format = ! empty( $format ) ? $format : get_option( 'date_format' );

	$date = mysql2date( $_format, $comment->comment_date );

	/**
	 * Filters the returned comment date.
	 *
	 * @since 1.5.0
	 *
	 * @param string|int $date    Formatted date string or Unix timestamp.
	 * @param string     $format  PHP date format.
	 * @param WP_Comment $comment The comment object.
	 */
	return apply_filters( 'get_comment_date', $date, $format, $comment );
}

/**
 * Displays the comment date of the current comment.
 *
 * @since 0.71
 * @since 4.4.0 Added the ability for `$comment_ID` to also accept a WP_Comment object.
 *
 * @param string         $format     Optional. PHP date format. Defaults to the 'date_format' option.
 * @param int|WP_Comment $comment_ID WP_Comment or ID of the comment for which to print the date.
 *                                   Default current comment.
 */
function comment_date( $format = '', $comment_ID = 0 ) {
	echo get_comment_date( $format, $comment_ID );
}

/**
 * Retrieves the excerpt of the given comment.
 *
 * Returns a maximum of 20 words with an ellipsis appended if necessary.
 *
 * @since 1.5.0
 * @since 4.4.0 Added the ability for `$comment_ID` to also accept a WP_Comment object.
 *
 * @param int|WP_Comment $comment_ID  WP_Comment or ID of the comment for which to get the excerpt.
 *                                    Default current comment.
 * @return string The possibly truncated comment excerpt.
 */
function get_comment_excerpt( $comment_ID = 0 ) {
	$comment = get_comment( $comment_ID );

	if ( ! post_password_required( $comment->comment_post_ID ) ) {
		$comment_text = strip_tags( str_replace( array( "\n", "\r" ), ' ', $comment->comment_content ) );
	} else {
		$comment_text = __( 'Password protected' );
	}

	/* translators: Maximum number of words used in a comment excerpt. */
	$comment_excerpt_length = intval( _x( '20', 'comment_excerpt_length' ) );

	/**
	 * Filters the maximum number of words used in the comment excerpt.
	 *
	 * @since 4.4.0
	 *
	 * @param int $comment_excerpt_length The amount of words you want to display in the comment excerpt.
	 */
	$comment_excerpt_length = apply_filters( 'comment_excerpt_length', $comment_excerpt_length );

	$excerpt = wp_trim_words( $comment_text, $comment_excerpt_length, '&hellip;' );

	/**
	 * Filters the retrieved comment excerpt.
	 *
	 * @since 1.5.0
	 * @since 4.1.0 The `$comment_ID` and `$comment` parameters were added.
	 *
	 * @param string     $excerpt    The comment excerpt text.
	 * @param int        $comment_ID The comment ID.
	 * @param WP_Comment $comment    The comment object.
	 */
	return apply_filters( 'get_comment_excerpt', $excerpt, $comment->comment_ID, $comment );
}

/**
 * Displays the excerpt of the current comment.
 *
 * @since 1.2.0
 * @since 4.4.0 Added the ability for `$comment_ID` to also accept a WP_Comment object.
 *
 * @param int|WP_Comment $comment_ID  WP_Comment or ID of the comment for which to print the excerpt.
 *                                    Default current comment.
 */
function comment_excerpt( $comment_ID = 0 ) {
	$comment         = get_comment( $comment_ID );
	$comment_excerpt = get_comment_excerpt( $comment );

	/**
	 * Filters the comment excerpt for display.
	 *
	 * @since 1.2.0
	 * @since 4.1.0 The `$comment_ID` parameter was added.
	 *
	 * @param string $comment_excerpt The comment excerpt text.
	 * @param int    $comment_ID      The comment ID.
	 */
	echo apply_filters( 'comment_excerpt', $comment_excerpt, $comment->comment_ID );
}

/**
 * Retrieves the comment ID of the current comment.
 *
 * @since 1.5.0
 *
 * @return int The comment ID.
 */
function get_comment_ID() { // phpcs:ignore WordPress.NamingConventions.ValidFunctionName.FunctionNameInvalid
	$comment = get_comment();

	/**
	 * Filters the returned comment ID.
	 *
	 * @since 1.5.0
	 * @since 4.1.0 The `$comment_ID` parameter was added.
	 *
	 * @param int        $comment_ID The current comment ID.
	 * @param WP_Comment $comment    The comment object.
	 */
	return apply_filters( 'get_comment_ID', $comment->comment_ID, $comment );  // phpcs:ignore WordPress.NamingConventions.ValidHookName.NotLowercase
}

/**
 * Displays the comment ID of the current comment.
 *
 * @since 0.71
 */
function comment_ID() { // phpcs:ignore WordPress.NamingConventions.ValidFunctionName.FunctionNameInvalid
	echo get_comment_ID();
}

/**
 * Retrieves the link to a given comment.
 *
 * @since 1.5.0
 * @since 4.4.0 Added the ability for `$comment` to also accept a WP_Comment object. Added `$cpage` argument.
 *
 * @see get_page_of_comment()
 *
 * @global WP_Rewrite $wp_rewrite      WordPress rewrite component.
 * @global bool       $in_comment_loop
 *
 * @param WP_Comment|int|null $comment Comment to retrieve. Default current comment.
 * @param array               $args {
 *     An array of optional arguments to override the defaults.
 *
 *     @type string     $type      Passed to get_page_of_comment().
 *     @type int        $page      Current page of comments, for calculating comment pagination.
 *     @type int        $per_page  Per-page value for comment pagination.
 *     @type int        $max_depth Passed to get_page_of_comment().
 *     @type int|string $cpage     Value to use for the comment's "comment-page" or "cpage" value.
 *                                 If provided, this value overrides any value calculated from `$page`
 *                                 and `$per_page`.
 * }
 * @return string The permalink to the given comment.
 */
function get_comment_link( $comment = null, $args = array() ) {
	global $wp_rewrite, $in_comment_loop;

	$comment = get_comment( $comment );

	// Back-compat.
	if ( ! is_array( $args ) ) {
		$args = array( 'page' => $args );
	}

	$defaults = array(
		'type'      => 'all',
		'page'      => '',
		'per_page'  => '',
		'max_depth' => '',
		'cpage'     => null,
	);
	$args     = wp_parse_args( $args, $defaults );

	$link = get_permalink( $comment->comment_post_ID );

	// The 'cpage' param takes precedence.
	if ( ! is_null( $args['cpage'] ) ) {
		$cpage = $args['cpage'];

		// No 'cpage' is provided, so we calculate one.
	} else {
		if ( '' === $args['per_page'] && get_option( 'page_comments' ) ) {
			$args['per_page'] = get_option( 'comments_per_page' );
		}

		if ( empty( $args['per_page'] ) ) {
			$args['per_page'] = 0;
			$args['page']     = 0;
		}

		$cpage = $args['page'];

		if ( '' == $cpage ) {
			if ( ! empty( $in_comment_loop ) ) {
				$cpage = get_query_var( 'cpage' );
			} else {
				// Requires a database hit, so we only do it when we can't figure out from context.
				$cpage = get_page_of_comment( $comment->comment_ID, $args );
			}
		}

		/*
		 * If the default page displays the oldest comments, the permalinks for comments on the default page
		 * do not need a 'cpage' query var.
		 */
		if ( 'oldest' === get_option( 'default_comments_page' ) && 1 === $cpage ) {
			$cpage = '';
		}
	}

	if ( $cpage && get_option( 'page_comments' ) ) {
		if ( $wp_rewrite->using_permalinks() ) {
			if ( $cpage ) {
				$link = trailingslashit( $link ) . $wp_rewrite->comments_pagination_base . '-' . $cpage;
			}

			$link = user_trailingslashit( $link, 'comment' );
		} elseif ( $cpage ) {
			$link = add_query_arg( 'cpage', $cpage, $link );
		}
	}

	if ( $wp_rewrite->using_permalinks() ) {
		$link = user_trailingslashit( $link, 'comment' );
	}

	$link = $link . '#comment-' . $comment->comment_ID;

	/**
	 * Filters the returned single comment permalink.
	 *
	 * @since 2.8.0
	 * @since 4.4.0 Added the `$cpage` parameter.
	 *
	 * @see get_page_of_comment()
	 *
	 * @param string     $link    The comment permalink with '#comment-$id' appended.
	 * @param WP_Comment $comment The current comment object.
	 * @param array      $args    An array of arguments to override the defaults.
	 * @param int        $cpage   The calculated 'cpage' value.
	 */
	return apply_filters( 'get_comment_link', $link, $comment, $args, $cpage );
}

/**
 * Retrieves the link to the current post comments.
 *
 * @since 1.5.0
 *
 * @param int|WP_Post $post_id Optional. Post ID or WP_Post object. Default is global $post.
 * @return string The link to the comments.
 */
function get_comments_link( $post_id = 0 ) {
	$hash          = get_comments_number( $post_id ) ? '#comments' : '#respond';
	$comments_link = get_permalink( $post_id ) . $hash;

	/**
	 * Filters the returned post comments permalink.
	 *
	 * @since 3.6.0
	 *
	 * @param string      $comments_link Post comments permalink with '#comments' appended.
	 * @param int|WP_Post $post_id       Post ID or WP_Post object.
	 */
	return apply_filters( 'get_comments_link', $comments_link, $post_id );
}

/**
 * Displays the link to the current post comments.
 *
 * @since 0.71
 *
 * @param string $deprecated   Not Used.
 * @param string $deprecated_2 Not Used.
 */
function comments_link( $deprecated = '', $deprecated_2 = '' ) {
	if ( ! empty( $deprecated ) ) {
		_deprecated_argument( __FUNCTION__, '0.72' );
	}
	if ( ! empty( $deprecated_2 ) ) {
		_deprecated_argument( __FUNCTION__, '1.3.0' );
	}
	echo esc_url( get_comments_link() );
}

/**
 * Retrieves the amount of comments a post has.
 *
 * @since 1.5.0
 *
 * @param int|WP_Post $post_id Optional. Post ID or WP_Post object. Default is the global `$post`.
 * @return string|int If the post exists, a numeric string representing the number of comments
 *                    the post has, otherwise 0.
 */
function get_comments_number( $post_id = 0 ) {
	$post = get_post( $post_id );

	if ( ! $post ) {
		$count = 0;
	} else {
		$count   = $post->comment_count;
		$post_id = $post->ID;
	}

	/**
	 * Filters the returned comment count for a post.
	 *
	 * @since 1.5.0
	 *
	 * @param string|int $count   A string representing the number of comments a post has, otherwise 0.
	 * @param int        $post_id Post ID.
	 */
	return apply_filters( 'get_comments_number', $count, $post_id );
}

/**
 * Displays the language string for the number of comments the current post has.
 *
 * @since 0.71
 * @since 5.4.0 The `$deprecated` parameter was changed to `$post_id`.
 *
 * @param string      $zero       Optional. Text for no comments. Default false.
 * @param string      $one        Optional. Text for one comment. Default false.
 * @param string      $more       Optional. Text for more than one comment. Default false.
 * @param int|WP_Post $post_id    Optional. Post ID or WP_Post object. Default is the global `$post`.
 */
function comments_number( $zero = false, $one = false, $more = false, $post_id = 0 ) {
	echo get_comments_number_text( $zero, $one, $more, $post_id );
}

/**
 * Displays the language string for the number of comments the current post has.
 *
 * @since 4.0.0
 * @since 5.4.0 Added the `$post_id` parameter to allow using the function outside of the loop.
 *
 * @param string      $zero    Optional. Text for no comments. Default false.
 * @param string      $one     Optional. Text for one comment. Default false.
 * @param string      $more    Optional. Text for more than one comment. Default false.
 * @param int|WP_Post $post_id Optional. Post ID or WP_Post object. Default is the global `$post`.
 * @return string Language string for the number of comments a post has.
 */
function get_comments_number_text( $zero = false, $one = false, $more = false, $post_id = 0 ) {
	$number = get_comments_number( $post_id );

	if ( $number > 1 ) {
		if ( false === $more ) {
			/* translators: %s: Number of comments. */
			$output = sprintf( _n( '%s Comment', '%s Comments', $number ), number_format_i18n( $number ) );
		} else {
			// % Comments
			/*
			 * translators: If comment number in your language requires declension,
			 * translate this to 'on'. Do not translate into your own language.
			 */
			if ( 'on' === _x( 'off', 'Comment number declension: on or off' ) ) {
				$text = preg_replace( '#<span class="screen-reader-text">.+?</span>#', '', $more );
				$text = preg_replace( '/&.+?;/', '', $text ); // Kill entities.
				$text = trim( strip_tags( $text ), '% ' );

				// Replace '% Comments' with a proper plural form.
				if ( $text && ! preg_match( '/[0-9]+/', $text ) && false !== strpos( $more, '%' ) ) {
					/* translators: %s: Number of comments. */
					$new_text = _n( '%s Comment', '%s Comments', $number );
					$new_text = trim( sprintf( $new_text, '' ) );

					$more = str_replace( $text, $new_text, $more );
					if ( false === strpos( $more, '%' ) ) {
						$more = '% ' . $more;
					}
				}
			}

			$output = str_replace( '%', number_format_i18n( $number ), $more );
		}
	} elseif ( 0 == $number ) {
		$output = ( false === $zero ) ? __( 'No Comments' ) : $zero;
	} else { // Must be one.
		$output = ( false === $one ) ? __( '1 Comment' ) : $one;
	}
	/**
	 * Filters the comments count for display.
	 *
	 * @since 1.5.0
	 *
	 * @see _n()
	 *
	 * @param string $output A translatable string formatted based on whether the count
	 *                       is equal to 0, 1, or 1+.
	 * @param int    $number The number of post comments.
	 */
	return apply_filters( 'comments_number', $output, $number );
}

/**
 * Retrieves the text of the current comment.
 *
 * @since 1.5.0
 * @since 4.4.0 Added the ability for `$comment_ID` to also accept a WP_Comment object.
 * @since 5.4.0 Added 'In reply to %s.' prefix to child comments in comments feed.
 *
 * @see Walker_Comment::comment()
 *
 * @param int|WP_Comment $comment_ID WP_Comment or ID of the comment for which to get the text.
 *                                   Default current comment.
 * @param array          $args       Optional. An array of arguments. Default empty array.
 * @return string The comment content.
 */
function get_comment_text( $comment_ID = 0, $args = array() ) {
	$comment = get_comment( $comment_ID );

	$comment_content = $comment->comment_content;

	if ( is_comment_feed() && $comment->comment_parent ) {
		$parent = get_comment( $comment->comment_parent );
		if ( $parent ) {
			$parent_link = esc_url( get_comment_link( $parent ) );
			$name        = get_comment_author( $parent );

			$comment_content = sprintf(
				/* translators: %s: Comment link. */
				ent2ncr( __( 'In reply to %s.' ) ),
				'<a href="' . $parent_link . '">' . $name . '</a>'
			) . "\n\n" . $comment_content;
		}
	}

	/**
	 * Filters the text of a comment.
	 *
	 * @since 1.5.0
	 *
	 * @see Walker_Comment::comment()
	 *
	 * @param string     $comment_content Text of the comment.
	 * @param WP_Comment $comment         The comment object.
	 * @param array      $args            An array of arguments.
	 */
	return apply_filters( 'get_comment_text', $comment_content, $comment, $args );
}

/**
 * Displays the text of the current comment.
 *
 * @since 0.71
 * @since 4.4.0 Added the ability for `$comment_ID` to also accept a WP_Comment object.
 *
 * @see Walker_Comment::comment()
 *
 * @param int|WP_Comment $comment_ID WP_Comment or ID of the comment for which to print the text.
 *                                   Default current comment.
 * @param array          $args       Optional. An array of arguments. Default empty array.
 */
function comment_text( $comment_ID = 0, $args = array() ) {
	$comment = get_comment( $comment_ID );

	$comment_text = get_comment_text( $comment, $args );
	/**
	 * Filters the text of a comment to be displayed.
	 *
	 * @since 1.2.0
	 *
	 * @see Walker_Comment::comment()
	 *
	 * @param string          $comment_text Text of the current comment.
	 * @param WP_Comment|null $comment      The comment object. Null if not found.
	 * @param array           $args         An array of arguments.
	 */
	echo apply_filters( 'comment_text', $comment_text, $comment, $args );
}

/**
 * Retrieves the comment time of the current comment.
 *
 * @since 1.5.0
 *
 * @param string $format    Optional. PHP date format. Defaults to the 'time_format' option.
 * @param bool   $gmt       Optional. Whether to use the GMT date. Default false.
 * @param bool   $translate Optional. Whether to translate the time (for use in feeds).
 *                          Default true.
 * @return string The formatted time.
 */
function get_comment_time( $format = '', $gmt = false, $translate = true ) {
	$comment = get_comment();

	$comment_date = $gmt ? $comment->comment_date_gmt : $comment->comment_date;

	$_format = ! empty( $format ) ? $format : get_option( 'time_format' );

	$date = mysql2date( $_format, $comment_date, $translate );

	/**
	 * Filters the returned comment time.
	 *
	 * @since 1.5.0
	 *
	 * @param string|int $date      The comment time, formatted as a date string or Unix timestamp.
	 * @param string     $format    PHP date format.
	 * @param bool       $gmt       Whether the GMT date is in use.
	 * @param bool       $translate Whether the time is translated.
	 * @param WP_Comment $comment   The comment object.
	 */
	return apply_filters( 'get_comment_time', $date, $format, $gmt, $translate, $comment );
}

/**
 * Displays the comment time of the current comment.
 *
 * @since 0.71
 *
 * @param string $format Optional. PHP date format. Defaults to the 'time_format' option.
 */
function comment_time( $format = '' ) {
	echo get_comment_time( $format );
}

/**
 * Retrieves the comment type of the current comment.
 *
 * @since 1.5.0
 * @since 4.4.0 Added the ability for `$comment_ID` to also accept a WP_Comment object.
 *
 * @param int|WP_Comment $comment_ID Optional. WP_Comment or ID of the comment for which to get the type.
 *                                   Default current comment.
 * @return string The comment type.
 */
function get_comment_type( $comment_ID = 0 ) {
	$comment = get_comment( $comment_ID );

	if ( '' === $comment->comment_type ) {
		$comment->comment_type = 'comment';
	}

	/**
	 * Filters the returned comment type.
	 *
	 * @since 1.5.0
	 * @since 4.1.0 The `$comment_ID` and `$comment` parameters were added.
	 *
	 * @param string     $comment_type The type of comment, such as 'comment', 'pingback', or 'trackback'.
	 * @param int        $comment_ID   The comment ID.
	 * @param WP_Comment $comment      The comment object.
	 */
	return apply_filters( 'get_comment_type', $comment->comment_type, $comment->comment_ID, $comment );
}

/**
 * Displays the comment type of the current comment.
 *
 * @since 0.71
 *
 * @param string $commenttxt   Optional. String to display for comment type. Default false.
 * @param string $trackbacktxt Optional. String to display for trackback type. Default false.
 * @param string $pingbacktxt  Optional. String to display for pingback type. Default false.
 */
function comment_type( $commenttxt = false, $trackbacktxt = false, $pingbacktxt = false ) {
	if ( false === $commenttxt ) {
		$commenttxt = _x( 'Comment', 'noun' );
	}
	if ( false === $trackbacktxt ) {
		$trackbacktxt = __( 'Trackback' );
	}
	if ( false === $pingbacktxt ) {
		$pingbacktxt = __( 'Pingback' );
	}
	$type = get_comment_type();
	switch ( $type ) {
		case 'trackback':
			echo $trackbacktxt;
			break;
		case 'pingback':
			echo $pingbacktxt;
			break;
		default:
			echo $commenttxt;
	}
}

/**
 * Retrieves the current post's trackback URL.
 *
 * There is a check to see if permalink's have been enabled and if so, will
 * retrieve the pretty path. If permalinks weren't enabled, the ID of the
 * current post is used and appended to the correct page to go to.
 *
 * @since 1.5.0
 *
 * @return string The trackback URL after being filtered.
 */
function get_trackback_url() {
	if ( get_option( 'permalink_structure' ) ) {
		$tb_url = trailingslashit( get_permalink() ) . user_trailingslashit( 'trackback', 'single_trackback' );
	} else {
		$tb_url = get_option( 'siteurl' ) . '/wp-trackback.php?p=' . get_the_ID();
	}

	/**
	 * Filters the returned trackback URL.
	 *
	 * @since 2.2.0
	 *
	 * @param string $tb_url The trackback URL.
	 */
	return apply_filters( 'trackback_url', $tb_url );
}

/**
 * Displays the current post's trackback URL.
 *
 * @since 0.71
 *
 * @param bool $deprecated_echo Not used.
 * @return void|string Should only be used to echo the trackback URL, use get_trackback_url()
 *                     for the result instead.
 */
function trackback_url( $deprecated_echo = true ) {
	if ( true !== $deprecated_echo ) {
		_deprecated_argument(
			__FUNCTION__,
			'2.5.0',
			sprintf(
				/* translators: %s: get_trackback_url() */
				__( 'Use %s instead if you do not want the value echoed.' ),
				'<code>get_trackback_url()</code>'
			)
		);
	}

	if ( $deprecated_echo ) {
		echo get_trackback_url();
	} else {
		return get_trackback_url();
	}
}

/**
 * Generates and displays the RDF for the trackback information of current post.
 *
 * Deprecated in 3.0.0, and restored in 3.0.1.
 *
 * @since 0.71
 *
 * @param int $deprecated Not used (Was $timezone = 0).
 */
function trackback_rdf( $deprecated = '' ) {
	if ( ! empty( $deprecated ) ) {
		_deprecated_argument( __FUNCTION__, '2.5.0' );
	}

	if ( isset( $_SERVER['HTTP_USER_AGENT'] ) && false !== stripos( $_SERVER['HTTP_USER_AGENT'], 'W3C_Validator' ) ) {
		return;
	}

	echo '<rdf:RDF xmlns:rdf="http://www.w3.org/1999/02/22-rdf-syntax-ns#"
			xmlns:dc="http://purl.org/dc/elements/1.1/"
			xmlns:trackback="http://madskills.com/public/xml/rss/module/trackback/">
		<rdf:Description rdf:about="';
	the_permalink();
	echo '"' . "\n";
	echo '    dc:identifier="';
	the_permalink();
	echo '"' . "\n";
	echo '    dc:title="' . str_replace( '--', '&#x2d;&#x2d;', wptexturize( strip_tags( get_the_title() ) ) ) . '"' . "\n";
	echo '    trackback:ping="' . get_trackback_url() . '"' . " />\n";
	echo '</rdf:RDF>';
}

/**
 * Determines whether the current post is open for comments.
 *
 * For more information on this and similar theme functions, check out
 * the {@link https://developer.wordpress.org/themes/basics/conditional-tags/
 * Conditional Tags} article in the Theme Developer Handbook.
 *
 * @since 1.5.0
 *
 * @param int|WP_Post $post_id Post ID or WP_Post object. Default current post.
 * @return bool True if the comments are open.
 */
function comments_open( $post_id = null ) {

	$_post = get_post( $post_id );

	$post_id = $_post ? $_post->ID : 0;
	$open    = ( 'open' === $_post->comment_status );

	/**
	 * Filters whether the current post is open for comments.
	 *
	 * @since 2.5.0
	 *
	 * @param bool $open    Whether the current post is open for comments.
	 * @param int  $post_id The post ID.
	 */
	return apply_filters( 'comments_open', $open, $post_id );
}

/**
 * Determines whether the current post is open for pings.
 *
 * For more information on this and similar theme functions, check out
 * the {@link https://developer.wordpress.org/themes/basics/conditional-tags/
 * Conditional Tags} article in the Theme Developer Handbook.
 *
 * @since 1.5.0
 *
 * @param int|WP_Post $post_id Post ID or WP_Post object. Default current post.
 * @return bool True if pings are accepted
 */
function pings_open( $post_id = null ) {

	$_post = get_post( $post_id );

	$post_id = $_post ? $_post->ID : 0;
	$open    = ( 'open' === $_post->ping_status );

	/**
	 * Filters whether the current post is open for pings.
	 *
	 * @since 2.5.0
	 *
	 * @param bool $open    Whether the current post is open for pings.
	 * @param int  $post_id The post ID.
	 */
	return apply_filters( 'pings_open', $open, $post_id );
}

/**
 * Displays form token for unfiltered comments.
 *
 * Will only display nonce token if the current user has permissions for
 * unfiltered html. Won't display the token for other users.
 *
 * The function was backported to 2.0.10 and was added to versions 2.1.3 and
 * above. Does not exist in versions prior to 2.0.10 in the 2.0 branch and in
 * the 2.1 branch, prior to 2.1.3. Technically added in 2.2.0.
 *
 * Backported to 2.0.10.
 *
 * @since 2.1.3
 */
function wp_comment_form_unfiltered_html_nonce() {
	$post    = get_post();
	$post_id = $post ? $post->ID : 0;

	if ( current_user_can( 'unfiltered_html' ) ) {
		wp_nonce_field( 'unfiltered-html-comment_' . $post_id, '_wp_unfiltered_html_comment_disabled', false );
		echo "<script>(function(){if(window===window.parent){document.getElementById('_wp_unfiltered_html_comment_disabled').name='_wp_unfiltered_html_comment';}})();</script>\n";
	}
}

/**
 * Loads the comment template specified in $file.
 *
 * Will not display the comments template if not on single post or page, or if
 * the post does not have comments.
 *
 * Uses the WordPress database object to query for the comments. The comments
 * are passed through the {@see 'comments_array'} filter hook with the list of comments
 * and the post ID respectively.
 *
 * The `$file` path is passed through a filter hook called {@see 'comments_template'},
 * which includes the TEMPLATEPATH and $file combined. Tries the $filtered path
 * first and if it fails it will require the default comment template from the
 * default theme. If either does not exist, then the WordPress process will be
 * halted. It is advised for that reason, that the default theme is not deleted.
 *
 * Will not try to get the comments if the post has none.
 *
 * @since 1.5.0
 *
 * @global WP_Query   $wp_query         WordPress Query object.
 * @global WP_Post    $post             Global post object.
 * @global wpdb       $wpdb             WordPress database abstraction object.
 * @global int        $id
 * @global WP_Comment $comment          Global comment object.
 * @global string     $user_login
 * @global string     $user_identity
 * @global bool       $overridden_cpage
 * @global bool       $withcomments
 *
 * @param string $file              Optional. The file to load. Default '/comments.php'.
 * @param bool   $separate_comments Optional. Whether to separate the comments by comment type.
 *                                  Default false.
 */
function comments_template( $file = '/comments.php', $separate_comments = false ) {
	global $wp_query, $withcomments, $post, $wpdb, $id, $comment, $user_login, $user_identity, $overridden_cpage;

	if ( ! ( is_single() || is_page() || $withcomments ) || empty( $post ) ) {
		return;
	}

	if ( empty( $file ) ) {
		$file = '/comments.php';
	}

	$req = get_option( 'require_name_email' );

	/*
	 * Comment author information fetched from the comment cookies.
	 */
	$commenter = wp_get_current_commenter();

	/*
	 * The name of the current comment author escaped for use in attributes.
	 * Escaped by sanitize_comment_cookies().
	 */
	$comment_author = $commenter['comment_author'];

	/*
	 * The email address of the current comment author escaped for use in attributes.
	 * Escaped by sanitize_comment_cookies().
	 */
	$comment_author_email = $commenter['comment_author_email'];

	/*
	 * The URL of the current comment author escaped for use in attributes.
	 */
	$comment_author_url = esc_url( $commenter['comment_author_url'] );

	$comment_args = array(
		'orderby'                   => 'comment_date_gmt',
		'order'                     => 'ASC',
		'status'                    => 'approve',
		'post_id'                   => $post->ID,
		'no_found_rows'             => false,
		'update_comment_meta_cache' => false, // We lazy-load comment meta for performance.
	);

	if ( get_option( 'thread_comments' ) ) {
		$comment_args['hierarchical'] = 'threaded';
	} else {
		$comment_args['hierarchical'] = false;
	}

	if ( is_user_logged_in() ) {
		$comment_args['include_unapproved'] = array( get_current_user_id() );
	} else {
		$unapproved_email = wp_get_unapproved_comment_author_email();

		if ( $unapproved_email ) {
			$comment_args['include_unapproved'] = array( $unapproved_email );
		}
	}

	$per_page = 0;
	if ( get_option( 'page_comments' ) ) {
		$per_page = (int) get_query_var( 'comments_per_page' );
		if ( 0 === $per_page ) {
			$per_page = (int) get_option( 'comments_per_page' );
		}

		$comment_args['number'] = $per_page;
		$page                   = (int) get_query_var( 'cpage' );

		if ( $page ) {
			$comment_args['offset'] = ( $page - 1 ) * $per_page;
		} elseif ( 'oldest' === get_option( 'default_comments_page' ) ) {
			$comment_args['offset'] = 0;
		} else {
			// If fetching the first page of 'newest', we need a top-level comment count.
			$top_level_query = new WP_Comment_Query();
			$top_level_args  = array(
				'count'   => true,
				'orderby' => false,
				'post_id' => $post->ID,
				'status'  => 'approve',
			);

			if ( $comment_args['hierarchical'] ) {
				$top_level_args['parent'] = 0;
			}

			if ( isset( $comment_args['include_unapproved'] ) ) {
				$top_level_args['include_unapproved'] = $comment_args['include_unapproved'];
			}

			$top_level_count = $top_level_query->query( $top_level_args );

			$comment_args['offset'] = ( ceil( $top_level_count / $per_page ) - 1 ) * $per_page;
		}
	}

	/**
	 * Filters the arguments used to query comments in comments_template().
	 *
	 * @since 4.5.0
	 *
	 * @see WP_Comment_Query::__construct()
	 *
	 * @param array $comment_args {
	 *     Array of WP_Comment_Query arguments.
	 *
	 *     @type string|array $orderby                   Field(s) to order by.
	 *     @type string       $order                     Order of results. Accepts 'ASC' or 'DESC'.
	 *     @type string       $status                    Comment status.
	 *     @type array        $include_unapproved        Array of IDs or email addresses whose unapproved comments
	 *                                                   will be included in results.
	 *     @type int          $post_id                   ID of the post.
	 *     @type bool         $no_found_rows             Whether to refrain from querying for found rows.
	 *     @type bool         $update_comment_meta_cache Whether to prime cache for comment meta.
	 *     @type bool|string  $hierarchical              Whether to query for comments hierarchically.
	 *     @type int          $offset                    Comment offset.
	 *     @type int          $number                    Number of comments to fetch.
	 * }
	 */
	$comment_args  = apply_filters( 'comments_template_query_args', $comment_args );
	$comment_query = new WP_Comment_Query( $comment_args );
	$_comments     = $comment_query->comments;

	// Trees must be flattened before they're passed to the walker.
	if ( $comment_args['hierarchical'] ) {
		$comments_flat = array();
		foreach ( $_comments as $_comment ) {
			$comments_flat[]  = $_comment;
			$comment_children = $_comment->get_children(
				array(
					'format'  => 'flat',
					'status'  => $comment_args['status'],
					'orderby' => $comment_args['orderby'],
				)
			);

			foreach ( $comment_children as $comment_child ) {
				$comments_flat[] = $comment_child;
			}
		}
	} else {
		$comments_flat = $_comments;
	}

	/**
	 * Filters the comments array.
	 *
	 * @since 2.1.0
	 *
	 * @param array $comments Array of comments supplied to the comments template.
	 * @param int   $post_ID  Post ID.
	 */
	$wp_query->comments = apply_filters( 'comments_array', $comments_flat, $post->ID );

	$comments                        = &$wp_query->comments;
	$wp_query->comment_count         = count( $wp_query->comments );
	$wp_query->max_num_comment_pages = $comment_query->max_num_pages;

	if ( $separate_comments ) {
		$wp_query->comments_by_type = separate_comments( $comments );
		$comments_by_type           = &$wp_query->comments_by_type;
	} else {
		$wp_query->comments_by_type = array();
	}

	$overridden_cpage = false;

	if ( '' == get_query_var( 'cpage' ) && $wp_query->max_num_comment_pages > 1 ) {
		set_query_var( 'cpage', 'newest' === get_option( 'default_comments_page' ) ? get_comment_pages_count() : 1 );
		$overridden_cpage = true;
	}

	if ( ! defined( 'COMMENTS_TEMPLATE' ) ) {
		define( 'COMMENTS_TEMPLATE', true );
	}

	$theme_template = STYLESHEETPATH . $file;

	/**
	 * Filters the path to the theme template file used for the comments template.
	 *
	 * @since 1.5.1
	 *
	 * @param string $theme_template The path to the theme template file.
	 */
	$include = apply_filters( 'comments_template', $theme_template );

	if ( file_exists( $include ) ) {
		require $include;
	} elseif ( file_exists( TEMPLATEPATH . $file ) ) {
		require TEMPLATEPATH . $file;
	} else { // Backward compat code will be removed in a future release.
		require ABSPATH . WPINC . '/theme-compat/comments.php';
	}
}

/**
 * Displays the link to the comments for the current post ID.
 *
 * @since 0.71
 *
 * @param false|string $zero      Optional. String to display when no comments. Default false.
 * @param false|string $one       Optional. String to display when only one comment is available. Default false.
 * @param false|string $more      Optional. String to display when there are more than one comment. Default false.
 * @param string       $css_class Optional. CSS class to use for comments. Default empty.
 * @param false|string $none      Optional. String to display when comments have been turned off. Default false.
 */
function comments_popup_link( $zero = false, $one = false, $more = false, $css_class = '', $none = false ) {
	$post_id    = get_the_ID();
	$post_title = get_the_title();
	$number     = get_comments_number( $post_id );

	if ( false === $zero ) {
		/* translators: %s: Post title. */
		$zero = sprintf( __( 'No Comments<span class="screen-reader-text"> on %s</span>' ), $post_title );
	}

	if ( false === $one ) {
		/* translators: %s: Post title. */
		$one = sprintf( __( '1 Comment<span class="screen-reader-text"> on %s</span>' ), $post_title );
	}

	if ( false === $more ) {
		/* translators: 1: Number of comments, 2: Post title. */
		$more = _n( '%1$s Comment<span class="screen-reader-text"> on %2$s</span>', '%1$s Comments<span class="screen-reader-text"> on %2$s</span>', $number );
		$more = sprintf( $more, number_format_i18n( $number ), $post_title );
	}

	if ( false === $none ) {
		/* translators: %s: Post title. */
		$none = sprintf( __( 'Comments Off<span class="screen-reader-text"> on %s</span>' ), $post_title );
	}

	if ( 0 == $number && ! comments_open() && ! pings_open() ) {
		echo '<span' . ( ( ! empty( $css_class ) ) ? ' class="' . esc_attr( $css_class ) . '"' : '' ) . '>' . $none . '</span>';
		return;
	}

	if ( post_password_required() ) {
		_e( 'Enter your password to view comments.' );
		return;
	}

	echo '<a href="';
	if ( 0 == $number ) {
		$respond_link = get_permalink() . '#respond';
		/**
		 * Filters the respond link when a post has no comments.
		 *
		 * @since 4.4.0
		 *
		 * @param string $respond_link The default response link.
		 * @param int    $post_id      The post ID.
		 */
		echo apply_filters( 'respond_link', $respond_link, $post_id );
	} else {
		comments_link();
	}
	echo '"';

	if ( ! empty( $css_class ) ) {
		echo ' class="' . $css_class . '" ';
	}

	$attributes = '';
	/**
	 * Filters the comments link attributes for display.
	 *
	 * @since 2.5.0
	 *
	 * @param string $attributes The comments link attributes. Default empty.
	 */
	echo apply_filters( 'comments_popup_link_attributes', $attributes );

	echo '>';
	comments_number( $zero, $one, $more );
	echo '</a>';
}

/**
 * Retrieves HTML content for reply to comment link.
 *
 * @since 2.7.0
 * @since 4.4.0 Added the ability for `$comment` to also accept a WP_Comment object.
 *
 * @param array          $args {
 *     Optional. Override default arguments.
 *
 *     @type string $add_below  The first part of the selector used to identify the comment to respond below.
 *                              The resulting value is passed as the first parameter to addComment.moveForm(),
 *                              concatenated as $add_below-$comment->comment_ID. Default 'comment'.
 *     @type string $respond_id The selector identifying the responding comment. Passed as the third parameter
 *                              to addComment.moveForm(), and appended to the link URL as a hash value.
 *                              Default 'respond'.
 *     @type string $reply_text The text of the Reply link. Default 'Reply'.
 *     @type string $login_text The text of the link to reply if logged out. Default 'Log in to Reply'.
 *     @type int    $max_depth  The max depth of the comment tree. Default 0.
 *     @type int    $depth      The depth of the new comment. Must be greater than 0 and less than the value
 *                              of the 'thread_comments_depth' option set in Settings > Discussion. Default 0.
 *     @type string $before     The text or HTML to add before the reply link. Default empty.
 *     @type string $after      The text or HTML to add after the reply link. Default empty.
 * }
 * @param int|WP_Comment $comment Comment being replied to. Default current comment.
 * @param int|WP_Post    $post    Post ID or WP_Post object the comment is going to be displayed on.
 *                                Default current post.
 * @return string|false|null Link to show comment form, if successful. False, if comments are closed.
 */
function get_comment_reply_link( $args = array(), $comment = null, $post = null ) {
	$defaults = array(
		'add_below'     => 'comment',
		'respond_id'    => 'respond',
		'reply_text'    => __( 'Reply' ),
		/* translators: Comment reply button text. %s: Comment author name. */
		'reply_to_text' => __( 'Reply to %s' ),
		'login_text'    => __( 'Log in to Reply' ),
		'max_depth'     => 0,
		'depth'         => 0,
		'before'        => '',
		'after'         => '',
	);

	$args = wp_parse_args( $args, $defaults );

	if ( 0 == $args['depth'] || $args['max_depth'] <= $args['depth'] ) {
		return;
	}

	$comment = get_comment( $comment );

	if ( empty( $comment ) ) {
		return;
	}

	if ( empty( $post ) ) {
		$post = $comment->comment_post_ID;
	}

	$post = get_post( $post );

	if ( ! comments_open( $post->ID ) ) {
		return false;
	}

	/**
	 * Filters the comment reply link arguments.
	 *
	 * @since 4.1.0
	 *
	 * @param array      $args    Comment reply link arguments. See get_comment_reply_link()
	 *                            for more information on accepted arguments.
	 * @param WP_Comment $comment The object of the comment being replied to.
	 * @param WP_Post    $post    The WP_Post object.
	 */
	$args = apply_filters( 'comment_reply_link_args', $args, $comment, $post );

	if ( get_option( 'comment_registration' ) && ! is_user_logged_in() ) {
		$link = sprintf(
			'<a rel="nofollow" class="comment-reply-login" href="%s">%s</a>',
			esc_url( wp_login_url( get_permalink() ) ),
			$args['login_text']
		);
	} else {
		$data_attributes = array(
			'commentid'      => $comment->comment_ID,
			'postid'         => $post->ID,
			'belowelement'   => $args['add_below'] . '-' . $comment->comment_ID,
			'respondelement' => $args['respond_id'],
			'replyto'        => sprintf( $args['reply_to_text'], $comment->comment_author ),
		);

		$data_attribute_string = '';

		foreach ( $data_attributes as $name => $value ) {
			$data_attribute_string .= " data-${name}=\"" . esc_attr( $value ) . '"';
		}

		$data_attribute_string = trim( $data_attribute_string );

		$link = sprintf(
			"<a rel='nofollow' class='comment-reply-link' href='%s' %s aria-label='%s'>%s</a>",
			esc_url(
				add_query_arg(
					array(
						'replytocom'      => $comment->comment_ID,
						'unapproved'      => false,
						'moderation-hash' => false,
					),
					get_permalink( $post->ID )
				)
			) . '#' . $args['respond_id'],
			$data_attribute_string,
			esc_attr( sprintf( $args['reply_to_text'], $comment->comment_author ) ),
			$args['reply_text']
		);
	}

	/**
	 * Filters the comment reply link.
	 *
	 * @since 2.7.0
	 *
	 * @param string     $link    The HTML markup for the comment reply link.
	 * @param array      $args    An array of arguments overriding the defaults.
	 * @param WP_Comment $comment The object of the comment being replied.
	 * @param WP_Post    $post    The WP_Post object.
	 */
	return apply_filters( 'comment_reply_link', $args['before'] . $link . $args['after'], $args, $comment, $post );
}

/**
 * Displays the HTML content for reply to comment link.
 *
 * @since 2.7.0
 *
 * @see get_comment_reply_link()
 *
 * @param array          $args    Optional. Override default options. Default empty array.
 * @param int|WP_Comment $comment Comment being replied to. Default current comment.
 * @param int|WP_Post    $post    Post ID or WP_Post object the comment is going to be displayed on.
 *                                Default current post.
 */
function comment_reply_link( $args = array(), $comment = null, $post = null ) {
	echo get_comment_reply_link( $args, $comment, $post );
}

/**
 * Retrieves HTML content for reply to post link.
 *
 * @since 2.7.0
 *
 * @param array       $args {
 *     Optional. Override default arguments.
 *
 *     @type string $add_below  The first part of the selector used to identify the comment to respond below.
 *                              The resulting value is passed as the first parameter to addComment.moveForm(),
 *                              concatenated as $add_below-$comment->comment_ID. Default is 'post'.
 *     @type string $respond_id The selector identifying the responding comment. Passed as the third parameter
 *                              to addComment.moveForm(), and appended to the link URL as a hash value.
 *                              Default 'respond'.
 *     @type string $reply_text Text of the Reply link. Default is 'Leave a Comment'.
 *     @type string $login_text Text of the link to reply if logged out. Default is 'Log in to leave a Comment'.
 *     @type string $before     Text or HTML to add before the reply link. Default empty.
 *     @type string $after      Text or HTML to add after the reply link. Default empty.
 * }
 * @param int|WP_Post $post    Optional. Post ID or WP_Post object the comment is going to be displayed on.
 *                             Default current post.
 * @return string|false|null Link to show comment form, if successful. False, if comments are closed.
 */
function get_post_reply_link( $args = array(), $post = null ) {
	$defaults = array(
		'add_below'  => 'post',
		'respond_id' => 'respond',
		'reply_text' => __( 'Leave a Comment' ),
		'login_text' => __( 'Log in to leave a Comment' ),
		'before'     => '',
		'after'      => '',
	);

	$args = wp_parse_args( $args, $defaults );

	$post = get_post( $post );

	if ( ! comments_open( $post->ID ) ) {
		return false;
	}

	if ( get_option( 'comment_registration' ) && ! is_user_logged_in() ) {
		$link = sprintf(
			'<a rel="nofollow" class="comment-reply-login" href="%s">%s</a>',
			wp_login_url( get_permalink() ),
			$args['login_text']
		);
	} else {
		$onclick = sprintf(
			'return addComment.moveForm( "%1$s-%2$s", "0", "%3$s", "%2$s" )',
			$args['add_below'],
			$post->ID,
			$args['respond_id']
		);

		$link = sprintf(
			"<a rel='nofollow' class='comment-reply-link' href='%s' onclick='%s'>%s</a>",
			get_permalink( $post->ID ) . '#' . $args['respond_id'],
			$onclick,
			$args['reply_text']
		);
	}
	$formatted_link = $args['before'] . $link . $args['after'];

	/**
	 * Filters the formatted post comments link HTML.
	 *
	 * @since 2.7.0
	 *
	 * @param string      $formatted The HTML-formatted post comments link.
	 * @param int|WP_Post $post      The post ID or WP_Post object.
	 */
	return apply_filters( 'post_comments_link', $formatted_link, $post );
}

/**
 * Displays the HTML content for reply to post link.
 *
 * @since 2.7.0
 *
 * @see get_post_reply_link()
 *
 * @param array       $args Optional. Override default options. Default empty array.
 * @param int|WP_Post $post Post ID or WP_Post object the comment is going to be displayed on.
 *                          Default current post.
 */
function post_reply_link( $args = array(), $post = null ) {
	echo get_post_reply_link( $args, $post );
}

/**
 * Retrieves HTML content for cancel comment reply link.
 *
 * @since 2.7.0
 *
 * @param string $text Optional. Text to display for cancel reply link. If empty,
 *                     defaults to 'Click here to cancel reply'. Default empty.
 * @return string
 */
function get_cancel_comment_reply_link( $text = '' ) {
	if ( empty( $text ) ) {
		$text = __( 'Click here to cancel reply.' );
	}

	$style = isset( $_GET['replytocom'] ) ? '' : ' style="display:none;"';
	$link  = esc_html( remove_query_arg( array( 'replytocom', 'unapproved', 'moderation-hash' ) ) ) . '#respond';

	$formatted_link = '<a rel="nofollow" id="cancel-comment-reply-link" href="' . $link . '"' . $style . '>' . $text . '</a>';

	/**
	 * Filters the cancel comment reply link HTML.
	 *
	 * @since 2.7.0
	 *
	 * @param string $formatted_link The HTML-formatted cancel comment reply link.
	 * @param string $link           Cancel comment reply link URL.
	 * @param string $text           Cancel comment reply link text.
	 */
	return apply_filters( 'cancel_comment_reply_link', $formatted_link, $link, $text );
}

/**
 * Displays HTML content for cancel comment reply link.
 *
 * @since 2.7.0
 *
 * @param string $text Optional. Text to display for cancel reply link. If empty,
 *                     defaults to 'Click here to cancel reply'. Default empty.
 */
function cancel_comment_reply_link( $text = '' ) {
	echo get_cancel_comment_reply_link( $text );
}

/**
 * Retrieves hidden input HTML for replying to comments.
 *
 * @since 3.0.0
 *
 * @param int $post_id Optional. Post ID. Defaults to the current post ID.
 * @return string Hidden input HTML for replying to comments.
 */
function get_comment_id_fields( $post_id = 0 ) {
	if ( empty( $post_id ) ) {
		$post_id = get_the_ID();
	}

	$reply_to_id = isset( $_GET['replytocom'] ) ? (int) $_GET['replytocom'] : 0;
	$result      = "<input type='hidden' name='comment_post_ID' value='$post_id' id='comment_post_ID' />\n";
	$result     .= "<input type='hidden' name='comment_parent' id='comment_parent' value='$reply_to_id' />\n";

	/**
	 * Filters the returned comment ID fields.
	 *
	 * @since 3.0.0
	 *
	 * @param string $result      The HTML-formatted hidden ID field comment elements.
	 * @param int    $post_id     The post ID.
	 * @param int    $reply_to_id The ID of the comment being replied to.
	 */
	return apply_filters( 'comment_id_fields', $result, $post_id, $reply_to_id );
}

/**
 * Outputs hidden input HTML for replying to comments.
 *
 * Adds two hidden inputs to the comment form to identify the `comment_post_ID`
 * and `comment_parent` values for threaded comments.
 *
 * This tag must be within the `<form>` section of the `comments.php` template.
 *
 * @since 2.7.0
 *
 * @see get_comment_id_fields()
 *
 * @param int $post_id Optional. Post ID. Defaults to the current post ID.
 */
function comment_id_fields( $post_id = 0 ) {
	echo get_comment_id_fields( $post_id );
}

/**
 * Displays text based on comment reply status.
 *
 * Only affects users with JavaScript disabled.
 *
 * @internal The $comment global must be present to allow template tags access to the current
 *           comment. See https://core.trac.wordpress.org/changeset/36512.
 *
 * @since 2.7.0
 *
 * @global WP_Comment $comment Global comment object.
 *
 * @param string $no_reply_text  Optional. Text to display when not replying to a comment.
 *                               Default false.
 * @param string $reply_text     Optional. Text to display when replying to a comment.
 *                               Default false. Accepts "%s" for the author of the comment
 *                               being replied to.
 * @param string $link_to_parent Optional. Boolean to control making the author's name a link
 *                               to their comment. Default true.
 */
function comment_form_title( $no_reply_text = false, $reply_text = false, $link_to_parent = true ) {
	global $comment;

	if ( false === $no_reply_text ) {
		$no_reply_text = __( 'Leave a Reply' );
	}

	if ( false === $reply_text ) {
		/* translators: %s: Author of the comment being replied to. */
		$reply_text = __( 'Leave a Reply to %s' );
	}

	$reply_to_id = isset( $_GET['replytocom'] ) ? (int) $_GET['replytocom'] : 0;

	if ( 0 == $reply_to_id ) {
		echo $no_reply_text;
	} else {
		// Sets the global so that template tags can be used in the comment form.
		$comment = get_comment( $reply_to_id );

		if ( $link_to_parent ) {
			$author = '<a href="#comment-' . get_comment_ID() . '">' . get_comment_author( $comment ) . '</a>';
		} else {
			$author = get_comment_author( $comment );
		}

		printf( $reply_text, $author );
	}
}

/**
 * Displays a list of comments.
 *
 * Used in the comments.php template to list comments for a particular post.
 *
 * @since 2.7.0
 *
 * @see WP_Query->comments
 *
 * @global WP_Query $wp_query           WordPress Query object.
 * @global int      $comment_alt
 * @global int      $comment_depth
 * @global int      $comment_thread_alt
 * @global bool     $overridden_cpage
 * @global bool     $in_comment_loop
 *
 * @param string|array $args {
 *     Optional. Formatting options.
 *
 *     @type object   $walker            Instance of a Walker class to list comments. Default null.
 *     @type int      $max_depth         The maximum comments depth. Default empty.
 *     @type string   $style             The style of list ordering. Accepts 'ul', 'ol', or 'div'.
 *                                       'div' will result in no additional list markup. Default 'ul'.
 *     @type callable $callback          Callback function to use. Default null.
 *     @type callable $end-callback      Callback function to use at the end. Default null.
 *     @type string   $type              Type of comments to list. Accepts 'all', 'comment',
 *                                       'pingback', 'trackback', 'pings'. Default 'all'.
 *     @type int      $page              Page ID to list comments for. Default empty.
 *     @type int      $per_page          Number of comments to list per page. Default empty.
 *     @type int      $avatar_size       Height and width dimensions of the avatar size. Default 32.
 *     @type bool     $reverse_top_level Ordering of the listed comments. If true, will display
 *                                       newest comments first. Default null.
 *     @type bool     $reverse_children  Whether to reverse child comments in the list. Default null.
 *     @type string   $format            How to format the comments list. Accepts 'html5', 'xhtml'.
 *                                       Default 'html5' if the theme supports it.
 *     @type bool     $short_ping        Whether to output short pings. Default false.
 *     @type bool     $echo              Whether to echo the output or return it. Default true.
 * }
 * @param WP_Comment[] $comments Optional. Array of WP_Comment objects.
 * @return void|string Void if 'echo' argument is true, or no comments to list.
 *                     Otherwise, HTML list of comments.
 */
function wp_list_comments( $args = array(), $comments = null ) {
	global $wp_query, $comment_alt, $comment_depth, $comment_thread_alt, $overridden_cpage, $in_comment_loop;

	$in_comment_loop = true;

	$comment_alt        = 0;
	$comment_thread_alt = 0;
	$comment_depth      = 1;

	$defaults = array(
		'walker'            => null,
		'max_depth'         => '',
		'style'             => 'ul',
		'callback'          => null,
		'end-callback'      => null,
		'type'              => 'all',
		'page'              => '',
		'per_page'          => '',
		'avatar_size'       => 32,
		'reverse_top_level' => null,
		'reverse_children'  => '',
		'format'            => current_theme_supports( 'html5', 'comment-list' ) ? 'html5' : 'xhtml',
		'short_ping'        => false,
		'echo'              => true,
	);

	$parsed_args = wp_parse_args( $args, $defaults );

	/**
	 * Filters the arguments used in retrieving the comment list.
	 *
	 * @since 4.0.0
	 *
	 * @see wp_list_comments()
	 *
	 * @param array $parsed_args An array of arguments for displaying comments.
	 */
	$parsed_args = apply_filters( 'wp_list_comments_args', $parsed_args );

	// Figure out what comments we'll be looping through ($_comments).
	if ( null !== $comments ) {
		$comments = (array) $comments;
		if ( empty( $comments ) ) {
			return;
		}
		if ( 'all' !== $parsed_args['type'] ) {
			$comments_by_type = separate_comments( $comments );
			if ( empty( $comments_by_type[ $parsed_args['type'] ] ) ) {
				return;
			}
			$_comments = $comments_by_type[ $parsed_args['type'] ];
		} else {
			$_comments = $comments;
		}
	} else {
		/*
		 * If 'page' or 'per_page' has been passed, and does not match what's in $wp_query,
		 * perform a separate comment query and allow Walker_Comment to paginate.
		 */
		if ( $parsed_args['page'] || $parsed_args['per_page'] ) {
			$current_cpage = get_query_var( 'cpage' );
			if ( ! $current_cpage ) {
				$current_cpage = 'newest' === get_option( 'default_comments_page' ) ? 1 : $wp_query->max_num_comment_pages;
			}

			$current_per_page = get_query_var( 'comments_per_page' );
			if ( $parsed_args['page'] != $current_cpage || $parsed_args['per_page'] != $current_per_page ) {
				$comment_args = array(
					'post_id' => get_the_ID(),
					'orderby' => 'comment_date_gmt',
					'order'   => 'ASC',
					'status'  => 'approve',
				);

				if ( is_user_logged_in() ) {
					$comment_args['include_unapproved'] = array( get_current_user_id() );
				} else {
					$unapproved_email = wp_get_unapproved_comment_author_email();

					if ( $unapproved_email ) {
						$comment_args['include_unapproved'] = array( $unapproved_email );
					}
				}

				$comments = get_comments( $comment_args );

				if ( 'all' !== $parsed_args['type'] ) {
					$comments_by_type = separate_comments( $comments );
					if ( empty( $comments_by_type[ $parsed_args['type'] ] ) ) {
						return;
					}

					$_comments = $comments_by_type[ $parsed_args['type'] ];
				} else {
					$_comments = $comments;
				}
			}

			// Otherwise, fall back on the comments from `$wp_query->comments`.
		} else {
			if ( empty( $wp_query->comments ) ) {
				return;
			}
			if ( 'all' !== $parsed_args['type'] ) {
				if ( empty( $wp_query->comments_by_type ) ) {
					$wp_query->comments_by_type = separate_comments( $wp_query->comments );
				}
				if ( empty( $wp_query->comments_by_type[ $parsed_args['type'] ] ) ) {
					return;
				}
				$_comments = $wp_query->comments_by_type[ $parsed_args['type'] ];
			} else {
				$_comments = $wp_query->comments;
			}

			if ( $wp_query->max_num_comment_pages ) {
				$default_comments_page = get_option( 'default_comments_page' );
				$cpage                 = get_query_var( 'cpage' );
				if ( 'newest' === $default_comments_page ) {
					$parsed_args['cpage'] = $cpage;

					/*
					* When first page shows oldest comments, post permalink is the same as
					* the comment permalink.
					*/
				} elseif ( 1 == $cpage ) {
					$parsed_args['cpage'] = '';
				} else {
					$parsed_args['cpage'] = $cpage;
				}

				$parsed_args['page']     = 0;
				$parsed_args['per_page'] = 0;
			}
		}
	}

	if ( '' === $parsed_args['per_page'] && get_option( 'page_comments' ) ) {
		$parsed_args['per_page'] = get_query_var( 'comments_per_page' );
	}

	if ( empty( $parsed_args['per_page'] ) ) {
		$parsed_args['per_page'] = 0;
		$parsed_args['page']     = 0;
	}

	if ( '' === $parsed_args['max_depth'] ) {
		if ( get_option( 'thread_comments' ) ) {
			$parsed_args['max_depth'] = get_option( 'thread_comments_depth' );
		} else {
			$parsed_args['max_depth'] = -1;
		}
	}

	if ( '' === $parsed_args['page'] ) {
		if ( empty( $overridden_cpage ) ) {
			$parsed_args['page'] = get_query_var( 'cpage' );
		} else {
			$threaded            = ( -1 != $parsed_args['max_depth'] );
			$parsed_args['page'] = ( 'newest' === get_option( 'default_comments_page' ) ) ? get_comment_pages_count( $_comments, $parsed_args['per_page'], $threaded ) : 1;
			set_query_var( 'cpage', $parsed_args['page'] );
		}
	}
	// Validation check.
	$parsed_args['page'] = intval( $parsed_args['page'] );
	if ( 0 == $parsed_args['page'] && 0 != $parsed_args['per_page'] ) {
		$parsed_args['page'] = 1;
	}

	if ( null === $parsed_args['reverse_top_level'] ) {
		$parsed_args['reverse_top_level'] = ( 'desc' === get_option( 'comment_order' ) );
	}

	wp_queue_comments_for_comment_meta_lazyload( $_comments );

	if ( empty( $parsed_args['walker'] ) ) {
		$walker = new Walker_Comment;
	} else {
		$walker = $parsed_args['walker'];
	}

	$output = $walker->paged_walk( $_comments, $parsed_args['max_depth'], $parsed_args['page'], $parsed_args['per_page'], $parsed_args );

	$in_comment_loop = false;

	if ( $parsed_args['echo'] ) {
		echo $output;
	} else {
		return $output;
	}
}

/**
 * Outputs a complete commenting form for use within a template.
 *
 * Most strings and form fields may be controlled through the `$args` array passed
 * into the function, while you may also choose to use the {@see 'comment_form_default_fields'}
 * filter to modify the array of default fields if you'd just like to add a new
 * one or remove a single field. All fields are also individually passed through
 * a filter of the {@see 'comment_form_field_$name'} where `$name` is the key used
 * in the array of fields.
 *
 * @since 3.0.0
 * @since 4.1.0 Introduced the 'class_submit' argument.
 * @since 4.2.0 Introduced the 'submit_button' and 'submit_fields' arguments.
 * @since 4.4.0 Introduced the 'class_form', 'title_reply_before', 'title_reply_after',
 *              'cancel_reply_before', and 'cancel_reply_after' arguments.
 * @since 4.5.0 The 'author', 'email', and 'url' form fields are limited to 245, 100,
 *              and 200 characters, respectively.
 * @since 4.6.0 Introduced the 'action' argument.
 * @since 4.9.6 Introduced the 'cookies' default comment field.
 * @since 5.5.0 Introduced the 'class_container' argument.
 *
 * @param array       $args {
 *     Optional. Default arguments and form fields to override.
 *
 *     @type array $fields {
 *         Default comment fields, filterable by default via the {@see 'comment_form_default_fields'} hook.
 *
 *         @type string $author  Comment author field HTML.
 *         @type string $email   Comment author email field HTML.
 *         @type string $url     Comment author URL field HTML.
 *         @type string $cookies Comment cookie opt-in field HTML.
 *     }
 *     @type string $comment_field        The comment textarea field HTML.
 *     @type string $must_log_in          HTML element for a 'must be logged in to comment' message.
 *     @type string $logged_in_as         HTML element for a 'logged in as [user]' message.
 *     @type string $comment_notes_before HTML element for a message displayed before the comment fields
 *                                        if the user is not logged in.
 *                                        Default 'Your email address will not be published.'.
 *     @type string $comment_notes_after  HTML element for a message displayed after the textarea field.
 *     @type string $action               The comment form element action attribute. Default '/wp-comments-post.php'.
 *     @type string $id_form              The comment form element id attribute. Default 'commentform'.
 *     @type string $id_submit            The comment submit element id attribute. Default 'submit'.
 *     @type string $class_container      The comment form container class attribute. Default 'comment-respond'.
 *     @type string $class_form           The comment form element class attribute. Default 'comment-form'.
 *     @type string $class_submit         The comment submit element class attribute. Default 'submit'.
 *     @type string $name_submit          The comment submit element name attribute. Default 'submit'.
 *     @type string $title_reply          The translatable 'reply' button label. Default 'Leave a Reply'.
 *     @type string $title_reply_to       The translatable 'reply-to' button label. Default 'Leave a Reply to %s',
 *                                        where %s is the author of the comment being replied to.
 *     @type string $title_reply_before   HTML displayed before the comment form title.
 *                                        Default: '<h3 id="reply-title" class="comment-reply-title">'.
 *     @type string $title_reply_after    HTML displayed after the comment form title.
 *                                        Default: '</h3>'.
 *     @type string $cancel_reply_before  HTML displayed before the cancel reply link.
 *     @type string $cancel_reply_after   HTML displayed after the cancel reply link.
 *     @type string $cancel_reply_link    The translatable 'cancel reply' button label. Default 'Cancel reply'.
 *     @type string $label_submit         The translatable 'submit' button label. Default 'Post a comment'.
 *     @type string $submit_button        HTML format for the Submit button.
 *                                        Default: '<input name="%1$s" type="submit" id="%2$s" class="%3$s" value="%4$s" />'.
 *     @type string $submit_field         HTML format for the markup surrounding the Submit button and comment hidden
 *                                        fields. Default: '<p class="form-submit">%1$s %2$s</p>', where %1$s is the
 *                                        submit button markup and %2$s is the comment hidden fields.
 *     @type string $format               The comment form format. Default 'xhtml'. Accepts 'xhtml', 'html5'.
 * }
 * @param int|WP_Post $post_id Post ID or WP_Post object to generate the form for. Default current post.
 */
function comment_form( $args = array(), $post_id = null ) {
	if ( null === $post_id ) {
		$post_id = get_the_ID();
	}

	// Exit the function when comments for the post are closed.
	if ( ! comments_open( $post_id ) ) {
		/**
		 * Fires after the comment form if comments are closed.
		 *
		 * @since 3.0.0
		 */
		do_action( 'comment_form_comments_closed' );

		return;
	}

	$commenter     = wp_get_current_commenter();
	$user          = wp_get_current_user();
	$user_identity = $user->exists() ? $user->display_name : '';

	$args = wp_parse_args( $args );
	if ( ! isset( $args['format'] ) ) {
		$args['format'] = current_theme_supports( 'html5', 'comment-form' ) ? 'html5' : 'xhtml';
	}

	$req      = get_option( 'require_name_email' );
	$html_req = ( $req ? " required='required'" : '' );
	$html5    = 'html5' === $args['format'];

	$fields = array(
		'author' => sprintf(
			'<p class="comment-form-author">%s %s</p>',
			sprintf(
				'<label for="author">%s%s</label>',
				__( 'Name' ),
				( $req ? ' <span class="required">*</span>' : '' )
			),
			sprintf(
				'<input id="author" name="author" type="text" value="%s" size="30" maxlength="245"%s />',
				esc_attr( $commenter['comment_author'] ),
				$html_req
			)
		),
		'email'  => sprintf(
			'<p class="comment-form-email">%s %s</p>',
			sprintf(
				'<label for="email">%s%s</label>',
				__( 'Email' ),
				( $req ? ' <span class="required">*</span>' : '' )
			),
			sprintf(
				'<input id="email" name="email" %s value="%s" size="30" maxlength="100" aria-describedby="email-notes"%s />',
				( $html5 ? 'type="email"' : 'type="text"' ),
				esc_attr( $commenter['comment_author_email'] ),
				$html_req
			)
		),
		'url'    => sprintf(
			'<p class="comment-form-url">%s %s</p>',
			sprintf(
				'<label for="url">%s</label>',
				__( 'Website' )
			),
			sprintf(
				'<input id="url" name="url" %s value="%s" size="30" maxlength="200" />',
				( $html5 ? 'type="url"' : 'type="text"' ),
				esc_attr( $commenter['comment_author_url'] )
			)
		),
	);

	if ( has_action( 'set_comment_cookies', 'wp_set_comment_cookies' ) && get_option( 'show_comments_cookies_opt_in' ) ) {
		$consent = empty( $commenter['comment_author_email'] ) ? '' : ' checked="checked"';

		$fields['cookies'] = sprintf(
			'<p class="comment-form-cookies-consent">%s %s</p>',
			sprintf(
				'<input id="wp-comment-cookies-consent" name="wp-comment-cookies-consent" type="checkbox" value="yes"%s />',
				$consent
			),
			sprintf(
				'<label for="wp-comment-cookies-consent">%s</label>',
				__( 'Save my name, email, and website in this browser for the next time I comment.' )
			)
		);

		// Ensure that the passed fields include cookies consent.
		if ( isset( $args['fields'] ) && ! isset( $args['fields']['cookies'] ) ) {
			$args['fields']['cookies'] = $fields['cookies'];
		}
	}

	$required_text = sprintf(
		/* translators: %s: Asterisk symbol (*). */
		' ' . __( 'Required fields are marked %s' ),
		'<span class="required">*</span>'
	);

	/**
	 * Filters the default comment form fields.
	 *
	 * @since 3.0.0
	 *
	 * @param string[] $fields Array of the default comment fields.
	 */
	$fields = apply_filters( 'comment_form_default_fields', $fields );

	$defaults = array(
		'fields'               => $fields,
		'comment_field'        => sprintf(
			'<p class="comment-form-comment">%s %s</p>',
			sprintf(
				'<label for="comment">%s</label>',
				_x( 'Comment', 'noun' )
			),
			'<textarea id="comment" name="comment" cols="45" rows="8" maxlength="65525" required="required"></textarea>'
		),
		'must_log_in'          => sprintf(
			'<p class="must-log-in">%s</p>',
			sprintf(
				/* translators: %s: Login URL. */
				__( 'You must be <a href="%s">logged in</a> to post a comment.' ),
				/** This filter is documented in wp-includes/link-template.php */
				wp_login_url( apply_filters( 'the_permalink', get_permalink( $post_id ), $post_id ) )
			)
		),
		'logged_in_as'         => sprintf(
			'<p class="logged-in-as">%s</p>',
			sprintf(
				/* translators: 1: Edit user link, 2: Accessibility text, 3: User name, 4: Logout URL. */
				__( '<a href="%1$s" aria-label="%2$s">Logged in as %3$s</a>. <a href="%4$s">Log out?</a>' ),
				get_edit_user_link(),
				/* translators: %s: User name. */
				esc_attr( sprintf( __( 'Logged in as %s. Edit your profile.' ), $user_identity ) ),
				$user_identity,
				/** This filter is documented in wp-includes/link-template.php */
				wp_logout_url( apply_filters( 'the_permalink', get_permalink( $post_id ), $post_id ) )
			)
		),
		'comment_notes_before' => sprintf(
			'<p class="comment-notes">%s%s</p>',
			sprintf(
				'<span id="email-notes">%s</span>',
				__( 'Your email address will not be published.' )
			),
			( $req ? $required_text : '' )
		),
		'comment_notes_after'  => '',
		'action'               => site_url( '/wp-comments-post.php' ),
		'id_form'              => 'commentform',
		'id_submit'            => 'submit',
		'class_container'      => 'comment-respond',
		'class_form'           => 'comment-form',
		'class_submit'         => 'submit',
		'name_submit'          => 'submit',
		'title_reply'          => __( 'Leave a Reply' ),
		/* translators: %s: Author of the comment being replied to. */
		'title_reply_to'       => __( 'Leave a Reply to %s' ),
		'title_reply_before'   => '<h3 id="reply-title" class="comment-reply-title">',
		'title_reply_after'    => '</h3>',
		'cancel_reply_before'  => ' <small>',
		'cancel_reply_after'   => '</small>',
		'cancel_reply_link'    => __( 'Cancel reply' ),
		'label_submit'         => __( 'Post Comment' ),
		'submit_button'        => '<input name="%1$s" type="submit" id="%2$s" class="%3$s" value="%4$s" />',
		'submit_field'         => '<p class="form-submit">%1$s %2$s</p>',
		'format'               => 'xhtml',
	);

	/**
	 * Filters the comment form default arguments.
	 *
	 * Use {@see 'comment_form_default_fields'} to filter the comment fields.
	 *
	 * @since 3.0.0
	 *
	 * @param array $defaults The default comment form arguments.
	 */
	$args = wp_parse_args( $args, apply_filters( 'comment_form_defaults', $defaults ) );

	// Ensure that the filtered args contain all required default values.
	$args = array_merge( $defaults, $args );

	// Remove `aria-describedby` from the email field if there's no associated description.
	if ( isset( $args['fields']['email'] ) && false === strpos( $args['comment_notes_before'], 'id="email-notes"' ) ) {
		$args['fields']['email'] = str_replace(
			' aria-describedby="email-notes"',
			'',
			$args['fields']['email']
		);
	}

	/**
	 * Fires before the comment form.
	 *
	 * @since 3.0.0
	 */
	do_action( 'comment_form_before' );
	?>
	<div id="respond" class="<?php echo esc_attr( $args['class_container'] ); ?>">
		<?php
		echo $args['title_reply_before'];

		comment_form_title( $args['title_reply'], $args['title_reply_to'] );

		echo $args['cancel_reply_before'];

		cancel_comment_reply_link( $args['cancel_reply_link'] );

		echo $args['cancel_reply_after'];

		echo $args['title_reply_after'];

		if ( get_option( 'comment_registration' ) && ! is_user_logged_in() ) :

			echo $args['must_log_in'];
			/**
			 * Fires after the HTML-formatted 'must log in after' message in the comment form.
			 *
			 * @since 3.0.0
			 */
			do_action( 'comment_form_must_log_in_after' );

		else :

			printf(
				'<form action="%s" method="post" id="%s" class="%s"%s>',
				esc_url( $args['action'] ),
				esc_attr( $args['id_form'] ),
				esc_attr( $args['class_form'] ),
				( $html5 ? ' novalidate' : '' )
			);

			/**
			 * Fires at the top of the comment form, inside the form tag.
			 *
			 * @since 3.0.0
			 */
			do_action( 'comment_form_top' );

			if ( is_user_logged_in() ) :

				/**
				 * Filters the 'logged in' message for the comment form for display.
				 *
				 * @since 3.0.0
				 *
				 * @param string $args_logged_in The logged-in-as HTML-formatted message.
				 * @param array  $commenter      An array containing the comment author's
				 *                               username, email, and URL.
				 * @param string $user_identity  If the commenter is a registered user,
				 *                               the display name, blank otherwise.
				 */
				echo apply_filters( 'comment_form_logged_in', $args['logged_in_as'], $commenter, $user_identity );

				/**
				 * Fires after the is_user_logged_in() check in the comment form.
				 *
				 * @since 3.0.0
				 *
				 * @param array  $commenter     An array containing the comment author's
				 *                              username, email, and URL.
				 * @param string $user_identity If the commenter is a registered user,
				 *                              the display name, blank otherwise.
				 */
				do_action( 'comment_form_logged_in_after', $commenter, $user_identity );

			else :

				echo $args['comment_notes_before'];

			endif;

			// Prepare an array of all fields, including the textarea.
			$comment_fields = array( 'comment' => $args['comment_field'] ) + (array) $args['fields'];

			/**
			 * Filters the comment form fields, including the textarea.
			 *
			 * @since 4.4.0
			 *
			 * @param array $comment_fields The comment fields.
			 */
			$comment_fields = apply_filters( 'comment_form_fields', $comment_fields );

			// Get an array of field names, excluding the textarea.
			$comment_field_keys = array_diff( array_keys( $comment_fields ), array( 'comment' ) );

			// Get the first and the last field name, excluding the textarea.
			$first_field = reset( $comment_field_keys );
			$last_field  = end( $comment_field_keys );

			foreach ( $comment_fields as $name => $field ) {

				if ( 'comment' === $name ) {

					/**
					 * Filters the content of the comment textarea field for display.
					 *
					 * @since 3.0.0
					 *
					 * @param string $args_comment_field The content of the comment textarea field.
					 */
					echo apply_filters( 'comment_form_field_comment', $field );

					echo $args['comment_notes_after'];

				} elseif ( ! is_user_logged_in() ) {

					if ( $first_field === $name ) {
						/**
						 * Fires before the comment fields in the comment form, excluding the textarea.
						 *
						 * @since 3.0.0
						 */
						do_action( 'comment_form_before_fields' );
					}

					/**
					 * Filters a comment form field for display.
					 *
					 * The dynamic portion of the filter hook, `$name`, refers to the name
					 * of the comment form field. Such as 'author', 'email', or 'url'.
					 *
					 * @since 3.0.0
					 *
					 * @param string $field The HTML-formatted output of the comment form field.
					 */
					echo apply_filters( "comment_form_field_{$name}", $field ) . "\n";

					if ( $last_field === $name ) {
						/**
						 * Fires after the comment fields in the comment form, excluding the textarea.
						 *
						 * @since 3.0.0
						 */
						do_action( 'comment_form_after_fields' );
					}
				}
			}

			$submit_button = sprintf(
				$args['submit_button'],
				esc_attr( $args['name_submit'] ),
				esc_attr( $args['id_submit'] ),
				esc_attr( $args['class_submit'] ),
				esc_attr( $args['label_submit'] )
			);

			/**
			 * Filters the submit button for the comment form to display.
			 *
			 * @since 4.2.0
			 *
			 * @param string $submit_button HTML markup for the submit button.
			 * @param array  $args          Arguments passed to comment_form().
			 */
			$submit_button = apply_filters( 'comment_form_submit_button', $submit_button, $args );

			$submit_field = sprintf(
				$args['submit_field'],
				$submit_button,
				get_comment_id_fields( $post_id )
			);

			/**
			 * Filters the submit field for the comment form to display.
			 *
			 * The submit field includes the submit button, hidden fields for the
			 * comment form, and any wrapper markup.
			 *
			 * @since 4.2.0
			 *
			 * @param string $submit_field HTML markup for the submit field.
			 * @param array  $args         Arguments passed to comment_form().
			 */
			echo apply_filters( 'comment_form_submit_field', $submit_field, $args );

			/**
			 * Fires at the bottom of the comment form, inside the closing form tag.
			 *
			 * @since 1.5.0
			 *
			 * @param int $post_id The post ID.
			 */
			do_action( 'comment_form', $post_id );

			echo '</form>';

		endif;
		?>
	</div><!-- #respond -->
	<?php

	/**
	 * Fires after the comment form.
	 *
	 * @since 3.0.0
	 */
	do_action( 'comment_form_after' );
}
