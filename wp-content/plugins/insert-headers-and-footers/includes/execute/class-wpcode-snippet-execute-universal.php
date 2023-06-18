<?php
/**
 * Execute universal snippets and return their output.
 * This type handles both HTML and PHP at the same time in the same way
 * you can write both in a .php file.
 *
 * @package wpcode
 */

/**
 * WPCode_Snippet_Execute_Universal class.
 */
class WPCode_Snippet_Execute_Universal extends WPCode_Snippet_Execute_Type {

	/**
	 * The snippet type, Universal for this one.
	 *
	 * @var string
	 */
	public $type = 'universal';

	/**
	 * Grab snippet code and return its output.
	 *
	 * @return string
	 */
	protected function prepare_snippet_output() {

		$code = $this->get_snippet_code();
		// If we're doing the activation, unslash the code similar to how it will be unslashed before saving in wp_insert_post.
		if ( wpcode()->execute->is_doing_activation() && isset( $_POST['wpcode_snippet_code'] ) ) {
			$code = wp_unslash( $code );
		}

		// Wrap code with PHP tags, so it gets executed correctly.
		return wpcode()->execute->safe_execute_php( '?>' . $code . '<?php ', $this->snippet );
	}
}
