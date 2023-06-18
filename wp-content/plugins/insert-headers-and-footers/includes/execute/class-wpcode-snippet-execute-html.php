<?php
/**
 * Execute html snippets and return their output.
 * This is probably the simplest one.
 *
 * @package wpcode
 */

/**
 * WPCode_Snippet_Execute_HTML class.
 */
class WPCode_Snippet_Execute_HTML extends WPCode_Snippet_Execute_Type {

	/**
	 * The snippet type, HTML for this one.
	 *
	 * @var string
	 */
	public $type = 'html';

	/**
	 * Grab snippet code and return its output.
	 *
	 * @return string
	 */
	protected function prepare_snippet_output() {
		// There's nothing to prepare here at this point.
		return $this->get_snippet_code();
	}
}
