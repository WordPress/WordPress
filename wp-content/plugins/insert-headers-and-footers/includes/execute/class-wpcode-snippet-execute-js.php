<?php
/**
 * Execute JavaScript snippets and return their output.
 *
 * @package wpcode
 */

/**
 * WPCode_Snippet_Execute_Text class.
 */
class WPCode_Snippet_Execute_JS extends WPCode_Snippet_Execute_Type {

	/**
	 * The snippet type, JavaScript for this one.
	 *
	 * @var string
	 */
	public $type = 'js';

	/**
	 * Grab snippet code and return its output.
	 *
	 * @return string
	 */
	protected function prepare_snippet_output() {
		$code = $this->get_snippet_code();

		if ( ! empty( $code ) ) {
			// Wrap our code in a script tag.
			$code = '<script>' . $code . '</script>';
		}

		return $code;
	}
}
