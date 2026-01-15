<?php
/**
 * Interactivity API: WP_Interactivity_API_Directives_Processor class.
 *
 * @package WordPress
 * @subpackage Interactivity API
 * @since 6.5.0
 */

/**
 * Class used to iterate over the tags of an HTML string and help process the
 * directive attributes.
 *
 * @since 6.5.0
 *
 * @access private
 */
final class WP_Interactivity_API_Directives_Processor extends WP_HTML_Tag_Processor {
	/**
	 * List of tags whose closer tag is not visited by the WP_HTML_Tag_Processor.
	 *
	 * @since 6.5.0
	 * @var string[]
	 */
	const TAGS_THAT_DONT_VISIT_CLOSER_TAG = array(
		'SCRIPT',
		'IFRAME',
		'NOEMBED',
		'NOFRAMES',
		'STYLE',
		'TEXTAREA',
		'TITLE',
		'XMP',
	);

	/**
	 * Returns the content between two balanced template tags.
	 *
	 * It positions the cursor in the closer tag of the balanced template tag,
	 * if it exists.
	 *
	 * @since 6.5.0
	 *
	 * @access private
	 *
	 * @return string|null The content between the current opener template tag and its matching closer tag or null if it
	 *                     doesn't find the matching closing tag or the current tag is not a template opener tag.
	 */
	public function get_content_between_balanced_template_tags() {
		if ( 'TEMPLATE' !== $this->get_tag() ) {
			return null;
		}

		$positions = $this->get_after_opener_tag_and_before_closer_tag_positions();
		if ( ! $positions ) {
			return null;
		}
		list( $after_opener_tag, $before_closer_tag ) = $positions;

		return substr( $this->html, $after_opener_tag, $before_closer_tag - $after_opener_tag );
	}

	/**
	 * Sets the content between two balanced tags.
	 *
	 * @since 6.5.0
	 *
	 * @access private
	 *
	 * @param string $new_content The string to replace the content between the matching tags.
	 * @return bool Whether the content was successfully replaced.
	 */
	public function set_content_between_balanced_tags( string $new_content ): bool {
		$positions = $this->get_after_opener_tag_and_before_closer_tag_positions( true );
		if ( ! $positions ) {
			return false;
		}
		list( $after_opener_tag, $before_closer_tag ) = $positions;

		$this->lexical_updates[] = new WP_HTML_Text_Replacement(
			$after_opener_tag,
			$before_closer_tag - $after_opener_tag,
			esc_html( $new_content )
		);

		return true;
	}

	/**
	 * Appends content after the closing tag of a template tag.
	 *
	 * It positions the cursor in the closer tag of the balanced template tag,
	 * if it exists.
	 *
	 * @since 6.5.0
	 *
	 * @access private
	 *
	 * @param string $new_content The string to append after the closing template tag.
	 * @return bool Whether the content was successfully appended.
	 */
	public function append_content_after_template_tag_closer( string $new_content ): bool {
		if ( empty( $new_content ) || 'TEMPLATE' !== $this->get_tag() || ! $this->is_tag_closer() ) {
			return false;
		}

		// Flushes any changes.
		$this->get_updated_html();

		$bookmark = 'append_content_after_template_tag_closer';
		$this->set_bookmark( $bookmark );
		$after_closing_tag = $this->bookmarks[ $bookmark ]->start + $this->bookmarks[ $bookmark ]->length;
		$this->release_bookmark( $bookmark );

		// Appends the new content.
		$this->lexical_updates[] = new WP_HTML_Text_Replacement( $after_closing_tag, 0, $new_content );

		return true;
	}

	/**
	 * Gets the positions right after the opener tag and right before the closer
	 * tag in a balanced tag.
	 *
	 * By default, it positions the cursor in the closer tag of the balanced tag.
	 * If $rewind is true, it seeks back to the opener tag.
	 *
	 * @since 6.5.0
	 *
	 * @access private
	 *
	 * @param bool $rewind Optional. Whether to seek back to the opener tag after finding the positions. Defaults to false.
	 * @return array|null Start and end byte position, or null when no balanced tag bookmarks.
	 */
	private function get_after_opener_tag_and_before_closer_tag_positions( bool $rewind = false ) {
		// Flushes any changes.
		$this->get_updated_html();

		$bookmarks = $this->get_balanced_tag_bookmarks();
		if ( ! $bookmarks ) {
			return null;
		}
		list( $opener_tag, $closer_tag ) = $bookmarks;

		$after_opener_tag  = $this->bookmarks[ $opener_tag ]->start + $this->bookmarks[ $opener_tag ]->length;
		$before_closer_tag = $this->bookmarks[ $closer_tag ]->start;

		if ( $rewind ) {
			$this->seek( $opener_tag );
		}

		$this->release_bookmark( $opener_tag );
		$this->release_bookmark( $closer_tag );

		return array( $after_opener_tag, $before_closer_tag );
	}

	/**
	 * Returns a pair of bookmarks for the current opener tag and the matching
	 * closer tag.
	 *
	 * It positions the cursor in the closer tag of the balanced tag, if it
	 * exists.
	 *
	 * @since 6.5.0
	 *
	 * @return array|null A pair of bookmarks, or null if there's no matching closing tag.
	 */
	private function get_balanced_tag_bookmarks() {
		static $i   = 0;
		$opener_tag = 'opener_tag_of_balanced_tag_' . ++$i;

		$this->set_bookmark( $opener_tag );
		if ( ! $this->next_balanced_tag_closer_tag() ) {
			$this->release_bookmark( $opener_tag );
			return null;
		}

		$closer_tag = 'closer_tag_of_balanced_tag_' . ++$i;
		$this->set_bookmark( $closer_tag );

		return array( $opener_tag, $closer_tag );
	}

	/**
	 * Skips processing the content between tags.
	 *
	 * It positions the cursor in the closer tag of the foreign element, if it
	 * exists.
	 *
	 * This function is intended to skip processing SVG and MathML inner content
	 * instead of bailing out the whole processing.
	 *
	 * @since 6.5.0
	 *
	 * @access private
	 *
	 * @return bool Whether the foreign content was successfully skipped.
	 */
	public function skip_to_tag_closer(): bool {
		$depth    = 1;
		$tag_name = $this->get_tag();

		while ( $depth > 0 && $this->next_tag( array( 'tag_closers' => 'visit' ) ) ) {
			if ( ! $this->is_tag_closer() && $this->get_attribute_names_with_prefix( 'data-wp-' ) ) {
				/* translators: 1: SVG or MATH HTML tag. */
				$message = sprintf( __( 'Interactivity directives were detected inside an incompatible %1$s tag. These directives will be ignored in the server side render.' ), $tag_name );
				_doing_it_wrong( __METHOD__, $message, '6.6.0' );
			}
			if ( $this->get_tag() === $tag_name ) {
				if ( $this->has_self_closing_flag() ) {
					continue;
				}
				$depth += $this->is_tag_closer() ? -1 : 1;
			}
		}

		return 0 === $depth;
	}

	/**
	 * Finds the matching closing tag for an opening tag.
	 *
	 * When called while the processor is on an open tag, it traverses the HTML
	 * until it finds the matching closer tag, respecting any in-between content,
	 * including nested tags of the same name. Returns false when called on a
	 * closer tag, a tag that doesn't have a closer tag (void), a tag that
	 * doesn't visit the closer tag, or if no matching closing tag was found.
	 *
	 * @since 6.5.0
	 *
	 * @access private
	 *
	 * @return bool Whether a matching closing tag was found.
	 */
	public function next_balanced_tag_closer_tag(): bool {
		$depth    = 0;
		$tag_name = $this->get_tag();

		if ( ! $this->has_and_visits_its_closer_tag() ) {
			return false;
		}

		while ( $this->next_tag(
			array(
				'tag_name'    => $tag_name,
				'tag_closers' => 'visit',
			)
		) ) {
			if ( ! $this->is_tag_closer() ) {
				++$depth;
				continue;
			}

			if ( 0 === $depth ) {
				return true;
			}

			--$depth;
		}

		return false;
	}

	/**
	 * Checks whether the current tag has and will visit its matching closer tag.
	 *
	 * @since 6.5.0
	 *
	 * @access private
	 *
	 * @return bool Whether the current tag has a closer tag.
	 */
	public function has_and_visits_its_closer_tag(): bool {
		$tag_name = $this->get_tag();

		return null !== $tag_name && (
			! WP_HTML_Processor::is_void( $tag_name ) &&
			! in_array( $tag_name, self::TAGS_THAT_DONT_VISIT_CLOSER_TAG, true )
		);
	}
}
