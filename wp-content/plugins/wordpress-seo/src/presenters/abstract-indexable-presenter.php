<?php

namespace Yoast\WP\SEO\Presenters;

use WPSEO_Replace_Vars;
use Yoast\WP\SEO\Presentations\Indexable_Presentation;
use Yoast\WP\SEO\Surfaces\Helpers_Surface;

/**
 * Abstract presenter class for indexable presentations.
 */
abstract class Abstract_Indexable_Presenter extends Abstract_Presenter {

	/**
	 * The WPSEO Replace Vars object.
	 *
	 * @var WPSEO_Replace_Vars
	 */
	public $replace_vars;

	/**
	 * The indexable presentation.
	 *
	 * @var Indexable_Presentation
	 */
	public $presentation;

	/**
	 * The helpers surface
	 *
	 * @var Helpers_Surface
	 */
	public $helpers;

	/**
	 * The tag key name.
	 *
	 * @var string
	 */
	protected $key = 'NO KEY PROVIDED';

	/**
	 * Gets the raw value of a presentation.
	 *
	 * @return string|array The raw value.
	 */
	abstract public function get();

	/**
	 * Transforms an indexable presenter's key to a json safe key string.
	 *
	 * @return string|null
	 */
	public function escape_key() {
		if ( $this->key === 'NO KEY PROVIDED' ) {
			return null;
		}
		return \str_replace( [ ':', ' ', '-' ], '_', $this->key );
	}

	/**
	 * Returns the metafield's property key.
	 *
	 * @return string The property key.
	 */
	public function get_key() {
		return $this->key;
	}

	/**
	 * Replace replacement variables in a string.
	 *
	 * @param string $replacevar_string The string with replacement variables.
	 *
	 * @return string The string with replacement variables replaced.
	 */
	protected function replace_vars( $replacevar_string ) {
		return $this->replace_vars->replace( $replacevar_string, $this->presentation->source );
	}
}
