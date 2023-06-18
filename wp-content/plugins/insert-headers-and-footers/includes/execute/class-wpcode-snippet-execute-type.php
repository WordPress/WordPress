<?php
/**
 * Abstract class for executing different type of snippets.
 *
 * @package wpcode
 */

/**
 * WPCode_Snippet_Execute_Type class.
 */
abstract class WPCode_Snippet_Execute_Type {

	/**
	 * The type of snippet.
	 *
	 * @var string
	 */
	public $type;

	/**
	 * Loaded post data.
	 *
	 * @var WPCode_Snippet
	 */
	public $snippet;

	/**
	 * Load the snippet by id or post object.
	 *
	 * @param WPCode_Snippet $snippet The snippet post or the id.
	 */
	public function __construct( $snippet ) {
		if ( empty( $snippet->attributes ) ) {
			$shortcode_attributes = $snippet->get_shortcode_attributes();
			foreach ( $shortcode_attributes as $attribute ) {
				$snippet->set_attribute( $attribute, '' );
			}
		}

		$this->snippet = $snippet;

	}

	/**
	 * Get the snippet prepared code and run it through a filter
	 * before returning it.
	 *
	 * @return string
	 */
	public function get_output() {
		if ( ! $this->has_snippet() ) {
			return '';
		}

		$code = $this->prepare_snippet_output();

		return apply_filters( "wpcode_snippet_output_{$this->type}", $code, $this->snippet );
	}

	/**
	 * Check if the snippet object is set.
	 *
	 * @return bool
	 */
	public function has_snippet() {
		return isset( $this->snippet );
	}

	/**
	 * Override this in child classes to add specific logic for each snippet type.
	 *
	 * @return string
	 */
	protected function prepare_snippet_output() {
		return '';
	}

	/**
	 * Get the snippet code.
	 *
	 * @return string
	 */
	public function get_snippet_code() {
		return $this->snippet->get_code();
	}

}
