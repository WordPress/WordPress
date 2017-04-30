<?php
/**
 * Functions and filters for handling the output of post formats.  Most of this file is for continuing the 
 * use of previous Hybrid Core functionality related to post formats as well as fixing the backwards-
 * compatibility issues that WordPress 3.6 created with its new post format functionality.
 *
 * This file is only loaded if themes declare support for 'post-formats'.  If a theme declares support for 
 * 'post-formats', the content filters will not run for the individual formats that the theme 
 * supports.
 *
 * @package    HybridCore
 * @subpackage Functions
 * @author     Justin Tadlock <justin@justintadlock.com>
 * @copyright  Copyright (c) 2008 - 2014, Justin Tadlock
 * @link       http://themehybrid.com/hybrid-core
 * @license    http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

/* Add support for structured post formats. */
add_action( 'wp_loaded', 'hybrid_structured_post_formats', 1 );

/**
 * Theme compatibility for post formats.  This function adds appropriate filters to 'the_content' for 
 * the various post formats that a theme supports.
 *
 * @note   This function may change drastically in the future depending on the direction of the WP post format UI.
 * @since  1.6.0
 * @access public
 * @return void
 */
function hybrid_structured_post_formats() {

	/* Add infinity symbol to aside posts. */
	if ( current_theme_supports( 'post-formats', 'aside' ) )
		add_filter( 'the_content', 'hybrid_aside_infinity', 9 ); // run before wpautop

	/* Adds the link to the content if it's not in the post. */
	if ( current_theme_supports( 'post-formats', 'link' ) )
		add_filter( 'the_content', 'hybrid_link_content', 9 ); // run before wpautop

	/* Wraps <blockquote> around quote posts. */
	if ( current_theme_supports( 'post-formats', 'quote' ) )
		add_filter( 'the_content', 'hybrid_quote_content' );

	/* Filter the content of chat posts. */
	if ( current_theme_supports( 'post-formats', 'chat' ) ) {
		add_filter( 'the_content', 'hybrid_chat_content' );

		/* Auto-add paragraphs to the chat text. */
		add_filter( 'hybrid_post_format_chat_text', 'wpautop' );
	}
}

/**
 * Strips the 'post-format-' prefix from a post format (term) slug.
 *
 * @since  1.6.0
 * @access public
 * @param  string $slug The slug of the post format.
 * @return string
 */
function hybrid_clean_post_format_slug( $slug ) {
	return str_replace( 'post-format-', '', $slug );
}

/* === Asides === */

/**
 * Adds an infinity character "&#8734;" to the end of the post content on 'aside' posts.
 *
 * @since  1.6.0
 * @access public
 * @param  string $content The post content.
 * @return string $content
 */
function hybrid_aside_infinity( $content ) {

	if ( has_post_format( 'aside' ) && !is_singular() && !post_password_required() ) {
		$infinity = '<a class="permalink" href="' . get_permalink() . '" title="' . the_title_attribute( array( 'echo' => false ) ) . '">&#8734;</a>';
		$content .= ' ' . apply_filters( 'hybrid_aside_infinity', $infinity );
	}

	return $content;
}

/* === Images === */

/**
 * Adds the post format image to the content if no image is found in the post content.  Note, this is not run 
 * by default.  To use, add the filter to 'the_content'.
 *
 * @since  1.6.0
 * @access public
 * @param  string  $content
 * @return string
 */
function hybrid_image_content( $content ) {

	if ( has_post_format( 'image' ) && !post_password_required() ) {
		preg_match( '/<img.*?>/', $content, $matches );

		if ( empty( $matches ) && function_exists( 'get_the_image' ) )
			$content = get_the_image( array( 'meta_key' => false, 'size' => 'large', 'link_to_post' => false, 'echo' => false ) ) . $content;

		elseif ( empty( $matches ) )
			$content = get_the_post_thumbnail( get_the_ID(), 'large' ) . $content;
	}

	return $content;
}

/* === Links === */

/**
 * Filters the content of the link format posts.  Wraps the content in the make_clickable() function 
 * so that users can enter just a URL into the post content editor.
 *
 * @since  1.6.0
 * @access public
 * @param  string $content The post content.
 * @return string $content
 */
function hybrid_link_content( $content ) {

	if ( has_post_format( 'link' ) && !preg_match( '/<a\s[^>]*?href=[\'"](.+?)[\'"]/is', $content ) )
		$content = make_clickable( $content );

	return $content;
}

/* === Quotes === */

/**
 * Checks if the quote post has a <blockquote> tag within the content.  If not, wraps the entire post 
 * content with one.
 *
 * @since  1.6.0
 * @access public
 * @param  string $content The post content.
 * @return string $content
 */
function hybrid_quote_content( $content ) {

	if ( has_post_format( 'quote' ) && !post_password_required() ) {
		preg_match( '/<blockquote.*?>/', $content, $matches );

		if ( empty( $matches ) )
			$content = "<blockquote>{$content}</blockquote>";
	}

	return $content;
}

/* === Chats === */

/**
 * Separates the post content into an array of arrays for further formatting of the chat content.
 *
 * @since  1.6.0
 * @access public
 * @param  string $content
 * @return array
 */
function hybrid_get_the_post_format_chat( $content ) {

	/* Allow the separator (separator for speaker/text) to be filtered. */
	$separator = apply_filters( 'hybrid_post_format_chat_separator', ':' );

	/* Split the content to get individual chat rows. */
	$chat_rows = preg_split( "/(\r?\n)+|(<br\s*\/?>\s*)+/", $content );

	/* Loop through each row and format the output. */
	foreach ( $chat_rows as $chat_row ) {

		/* Set up a new, empty array of this stanza. */
		$stanza = array();

		/* If a speaker is found, create a new chat row with speaker and text. */
		if ( preg_match( '/(?<!http|https)' . $separator . '/', $chat_row ) ) {

			/* Set up a new, empty array for this row. */
			$row = array();

			/* Split the chat row into author/text. */
			$chat_row_split = explode( $separator, trim( $chat_row ), 2 );

			/* Get the chat author and strip tags. */
			$row['author'] = strip_tags( trim( $chat_row_split[0] ) );

			/* Get the chat text. */
			$row['message'] = trim( $chat_row_split[1] );

			/* Add the row to the stanza. */
			$stanza[] = $row;
		}

		/* If no speaker is found. */
		else {

			/* Make sure we have text. */
			if ( !empty( $chat_row ) ) {
				$stanza[] = array( 'message' => $chat_row );
			}
		}

		$stanzas[] = $stanza;
	}

	return $stanzas;
}

/**
 * This function filters the post content when viewing a post with the "chat" post format.  It formats 
 * the content with structured HTML markup to make it easy for theme developers to style chat posts. 
 * The advantage of this solution is that it allows for more than two speakers (like most solutions). 
 * You can have 100s of speakers in your chat post, each with their own, unique classes for styling.
 *
 * @author    David Chandra <david.warna@gmail.com>
 * @author    Justin Tadlock <justin@justintadlock.com>
 * @copyright Copyright (c) 2012
 * @link      http://justintadlock.com/archives/2012/08/21/post-formats-chat
 *
 * @since  1.6.0
 * @access public
 * @global array   $_hybrid_post_chat_ids  An array of IDs for the chat rows based on the author.
 * @param  string  $content                The content of the post.
 * @return string  $chat_output            The formatted content of the post.
 */
function hybrid_chat_content( $content ) {

	/* If this isn't a chat, return. */
	if ( !has_post_format( 'chat' ) || post_password_required() )
		return $content;

	/* Open the chat transcript div and give it a unique ID based on the post ID. */
	$chat_output = "\n\t\t\t" . '<div id="chat-transcript-' . esc_attr( get_the_ID() ) . '" class="chat-transcript">';

	/* Allow the separator (separator for speaker/text) to be filtered. */
	$separator = apply_filters( 'hybrid_post_format_chat_separator', ':' );

	/* Get the stanzas from the post content. */
	$stanzas = hybrid_get_the_post_format_chat( $content );

	/* Loop through the stanzas that were returned. */
	foreach ( $stanzas as $stanza ) {

		/* Loop through each row of the stanza and format. */
		foreach ( $stanza as $row ) {

			/* Get the chat author and message. */
			$chat_author = !empty( $row['author'] ) ? $row['author'] : '';
			$chat_text   = $row['message'];

			/* Get the speaker/row ID. */
			$speaker_id = hybrid_chat_row_id( $chat_author );

			/* Format the time if there was one given. */
			$time = empty( $row['time'] ) ? '' : '<time class="chat-timestamp">' . esc_html( $row['time'] ) . '</time> ';

			/* Open the chat row. */
			$chat_output .= "\n\t\t\t\t" . '<div class="chat-row ' . sanitize_html_class( "chat-speaker-{$speaker_id}" ) . '">';

			/* Add the chat row author. */
			if ( !empty( $chat_author ) )
				$chat_output .= "\n\t\t\t\t\t" . '<div class="chat-author ' . sanitize_html_class( strtolower( "chat-author-{$chat_author}" ) ) . ' vcard">' . $time . '<cite class="fn">' . apply_filters( 'hybrid_post_format_chat_author', $chat_author, $speaker_id ) . '</cite>:</div>';

			/* Add the chat row text. */
			$chat_output .= "\n\t\t\t\t\t" . '<div class="chat-text">' . str_replace( array( "\r", "\n", "\t" ), '', apply_filters( 'hybrid_post_format_chat_text', $chat_text, $chat_author, $speaker_id ) ) . '</div>';

			/* Close the chat row. */
			$chat_output .= "\n\t\t\t\t" . '</div><!-- .chat-row -->';
		}
	}

	/* Close the chat transcript div. */
	$chat_output .= "\n\t\t\t</div><!-- .chat-transcript -->\n";

	/* Return the chat content. */
	return $chat_output;
}


/**
 * This function returns an ID based on the provided chat author name.  It keeps these IDs in a global 
 * array and makes sure we have a unique set of IDs.  The purpose of this function is to provide an "ID"
 * that will be used in an HTML class for individual chat rows so they can be styled.  So, speaker "John" 
 * will always have the same class each time he speaks.  And, speaker "Mary" will have a different class 
 * from "John" but will have the same class each time she speaks.
 *
 * @author    David Chandra <david.warna@gmail.com>
 * @author    Justin Tadlock <justin@justintadlock.com>
 * @copyright Copyright (c) 2012
 * @link      http://justintadlock.com/archives/2012/08/21/post-formats-chat
 *
 * @since  1.6.0
 * @access public
 * @global array   $_hybrid_post_chat_ids  An array of IDs for the chat rows based on the author.
 * @param  string  $chat_author            Author of the current chat row.
 * @return int                             The ID for the chat row based on the author.
 */
function hybrid_chat_row_id( $chat_author ) {
	global $_hybrid_post_chat_ids;

	/* Let's sanitize the chat author to avoid craziness and differences like "John" and "john". */
	$chat_author = strtolower( strip_tags( $chat_author ) );

	/* Add the chat author to the array. */
	$_hybrid_post_chat_ids[] = $chat_author;

	/* Make sure the array only holds unique values. */
	$_hybrid_post_chat_ids = array_unique( $_hybrid_post_chat_ids );

	/* Return the array key for the chat author and add "1" to avoid an ID of "0". */
	return absint( array_search( $chat_author, $_hybrid_post_chat_ids ) ) + 1;
}
