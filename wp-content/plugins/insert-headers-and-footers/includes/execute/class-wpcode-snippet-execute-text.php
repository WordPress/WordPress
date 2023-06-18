<?php
/**
 * Execute text snippets and return their output.
 *
 * @package wpcode
 */

/**
 * WPCode_Snippet_Execute_Text class.
 */
class WPCode_Snippet_Execute_Text extends WPCode_Snippet_Execute_Type {

	/**
	 * The snippet type, Text for this one.
	 *
	 * @var string
	 */
	public $type = 'text';

	/**
	 * Grab snippet code and return its output.
	 *
	 * @return string
	 */
	protected function prepare_snippet_output() {
		// There's nothing to prepare here at this point.
		if ( apply_filters( 'wpcode_text_execute_shortcodes', true ) ) {
			return do_shortcode( $this->get_snippet_code() );
		}

		return $this->get_snippet_code();
	}
}
