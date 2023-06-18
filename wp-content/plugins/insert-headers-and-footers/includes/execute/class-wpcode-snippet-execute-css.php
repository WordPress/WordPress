<?php
/**
 * Execute CSS snippets and return their output.
 *
 * @package wpcode
 */

/**
 * WPCode_Snippet_Execute_Text class.
 */
class WPCode_Snippet_Execute_CSS extends WPCode_Snippet_Execute_Type {

	/**
	 * The snippet type, JavaScript for this one.
	 *
	 * @var string
	 */
	public $type = 'css';

	/**
	 * Grab snippet code and return its output.
	 *
	 * @return string
	 */
	protected function prepare_snippet_output() {
		$code = $this->get_snippet_code();

		if ( ! empty( $code ) ) {
			// Wrap our code in a style tag.
			$code = '<style class="wpcode-css-snippet">' . $code . '</style>';
		}

		return $code;
	}
}
