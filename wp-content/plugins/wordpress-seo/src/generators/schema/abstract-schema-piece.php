<?php

namespace Yoast\WP\SEO\Generators\Schema;

use Yoast\WP\SEO\Context\Meta_Tags_Context;
use Yoast\WP\SEO\Surfaces\Helpers_Surface;

/**
 * Class Abstract_Schema_Piece.
 */
abstract class Abstract_Schema_Piece {

	/**
	 * The meta tags context.
	 *
	 * @var Meta_Tags_Context
	 */
	public $context;

	/**
	 * The helpers surface
	 *
	 * @var Helpers_Surface
	 */
	public $helpers;

	/**
	 * Optional identifier for this schema piece.
	 *
	 * Used in the `Schema_Generator::filter_graph_pieces_to_generate()` method.
	 *
	 * @var string
	 */
	public $identifier;

	/**
	 * Generates the schema piece.
	 *
	 * @return mixed
	 */
	abstract public function generate();

	/**
	 * Determines whether the schema piece is needed.
	 *
	 * @return bool
	 */
	abstract public function is_needed();
}
