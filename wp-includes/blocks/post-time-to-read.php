<?php
/**
 * Server-side rendering of the `core/post-time-to-read` block.
 *
 * @package WordPress
 */

/**
 * Counts words or characters in a provided text string.
 *
 * This function currently employs an array of regular expressions
 * to parse HTML and count words, which may result in inaccurate
 * word counts. However, it is designed primarily to agree with the
 * corresponding JavaScript function.
 *
 * Any improvements in the word counting, for example with the HTML API
 * and {@see \IntlBreakIterator::createWordInstance()} should coordinate
 * with changes to the JavaScript implementation to ensure consistency
 * between the editor and the rendered page.
 *
 * @since 6.9.0
 *
 * @param string $text Text to count elements in.
 * @param string $type The type of count. Accepts 'words', 'characters_excluding_spaces', or 'characters_including_spaces'.
 *
 * @return string The rendered word count.
 */
function block_core_post_time_to_read_word_count( $text, $type ) {
	$settings = array(
		'html_regexp'                        => '/<\/?[a-z][^>]*?>/i',
		'html_comment_regexp'                => '/<!--[\s\S]*?-->/',
		'space_regexp'                       => '/&nbsp;|&#160;/i',
		'html_entity_regexp'                 => '/&\S+?;/',
		'connector_regexp'                   => "/--|\x{2014}/u",
		'remove_regexp'                      => "/[\x{0021}-\x{0040}\x{005B}-\x{0060}\x{007B}-\x{007E}\x{0080}-\x{00BF}\x{00D7}\x{00F7}\x{2000}-\x{2BFF}\x{2E00}-\x{2E7F}]/u",
		'astral_regexp'                      => "/[\x{010000}-\x{10FFFF}]/u",
		'words_regexp'                       => '/\S\s+/u',
		'characters_excluding_spaces_regexp' => '/\S/u',
		'characters_including_spaces_regexp' => "/[^\f\n\r\t\v\x{00AD}\x{2028}\x{2029}]/u",
	);

	$count = 0;

	if ( '' === trim( $text ) ) {
		return $count;
	}

	// Sanitize type to one of three possibilities: 'words', 'characters_excluding_spaces' or 'characters_including_spaces'.
	if ( 'characters_excluding_spaces' !== $type && 'characters_including_spaces' !== $type ) {
		$type = 'words';
	}

	$text .= "\n";

	// Replace all HTML with a new-line.
	$text = preg_replace( $settings['html_regexp'], "\n", $text );

	// Remove all HTML comments.
	$text = preg_replace( $settings['html_comment_regexp'], '', $text );

	// If a shortcode regular expression has been provided use it to remove shortcodes.
	if ( ! empty( $settings['shortcodes_regexp'] ) ) {
		$text = preg_replace( $settings['shortcodes_regexp'], "\n", $text );
	}

	// Normalize non-breaking space to a normal space.
	$text = preg_replace( $settings['space_regexp'], ' ', $text );

	if ( 'words' === $type ) {
		// Remove HTML Entities.
		$text = preg_replace( $settings['html_entity_regexp'], '', $text );

		// Convert connectors to spaces to count attached text as words.
		$text = preg_replace( $settings['connector_regexp'], ' ', $text );

		// Remove unwanted characters.
		$text = preg_replace( $settings['remove_regexp'], '', $text );
	} else {
		// Convert HTML Entities to "a".
		$text = preg_replace( $settings['html_entity_regexp'], 'a', $text );

		// Remove surrogate points.
		$text = preg_replace( $settings['astral_regexp'], 'a', $text );
	}

	// Match with the selected type regular expression to count the items.
	return (int) preg_match_all( $settings[ $type . '_regexp' ], $text );
}

/**
 * Renders the `core/post-time-to-read` block on the server.
 *
 * @since 6.9.0
 *
 * @param  array    $attributes Block attributes.
 * @param  string   $content    Block default content.
 * @param  WP_Block $block      Block instance.
 * @return string Returns the rendered post author name block.
 */
function render_block_core_post_time_to_read( $attributes, $content, $block ) {
	if ( ! isset( $block->context['postId'] ) ) {
		return '';
	}

	$content              = get_the_content();
	$average_reading_rate = isset( $attributes['averageReadingSpeed'] ) ? $attributes['averageReadingSpeed'] : 189;

	$display_mode = isset( $attributes['displayMode'] ) ? $attributes['displayMode'] : 'time';

	$word_count_type = wp_get_word_count_type();
	$total_words     = block_core_post_time_to_read_word_count( $content, $word_count_type );

	$parts = array();

	// Add "time to read" part, if enabled.
	if ( 'time' === $display_mode ) {
		if ( ! empty( $attributes['displayAsRange'] ) ) {
			// Calculate faster reading rate with 20% speed = lower minutes,
			// and slower reading rate with 20% speed = higher minutes.
			$min_minutes = max( 1, (int) round( $total_words / $average_reading_rate * 0.8 ) );
			$max_minutes = max( 1, (int) round( $total_words / $average_reading_rate * 1.2 ) );
			if ( $min_minutes === $max_minutes ) {
				$max_minutes = $min_minutes + 1;
			}
			/* translators: 1: minimum minutes, 2: maximum minutes to read the post. */
			$time_string = sprintf(
				/* translators: 1: minimum minutes, 2: maximum minutes to read the post. */
				_x( '%1$s–%2$s minutes', 'Range of minutes to read' ),
				$min_minutes,
				$max_minutes
			);
		} else {
			$minutes_to_read = max( 1, (int) round( $total_words / $average_reading_rate ) );
			$time_string     = sprintf(
				/* translators: %s: the number of minutes to read the post. */
				_n( '%s minute', '%s minutes', $minutes_to_read ),
				$minutes_to_read
			);
		}
		$parts[] = $time_string;
	}

	// Add "word count" part, if enabled.
	if ( 'words' === $display_mode ) {
		$word_count_string = 'words' === $word_count_type ? sprintf(
			/* translators: %s: the number of words in the post. */
			_n( '%s word', '%s words', $total_words ),
			number_format_i18n( $total_words )
		) : sprintf(
			/* translators: %s: the number of characters in the post. */
			_n( '%s character', '%s characters', $total_words ),
			number_format_i18n( $total_words )
		);
		$parts[] = $word_count_string;
	}

	$display_string = implode( '<br>', $parts );

	$align_class_name = empty( $attributes['textAlign'] ) ? '' : "has-text-align-{$attributes['textAlign']}";

	$wrapper_attributes = get_block_wrapper_attributes( array( 'class' => $align_class_name ) );

	return sprintf(
		'<div %1$s>%2$s</div>',
		$wrapper_attributes,
		$display_string
	);
}


/**
 * Registers the `core/post-time-to-read` block on the server.
 *
 * @since 6.9.0
 */
function register_block_core_post_time_to_read() {
	register_block_type_from_metadata(
		__DIR__ . '/post-time-to-read',
		array(
			'render_callback' => 'render_block_core_post_time_to_read',
		)
	);
}

add_action( 'init', 'register_block_core_post_time_to_read' );
