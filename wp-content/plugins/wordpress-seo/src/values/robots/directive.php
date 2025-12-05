<?php

namespace Yoast\WP\SEO\Values\Robots;

/**
 * Class Directive
 */
class Directive {

	/**
	 * Paths list.
	 *
	 * @var array All paths affected by this directive.
	 */
	private $paths;

	/**
	 * Sets up the path array
	 */
	public function __construct() {
		$this->paths = [];
	}

	/**
	 * Adds a path to the directive path list.
	 *
	 * @param string $path A path to add in the path list.
	 *
	 * @return void
	 */
	public function add_path( $path ) {
		if ( ! \in_array( $path, $this->paths, true ) ) {
			$this->paths[] = $path;
		}
	}

	/**
	 * Returns all paths.
	 *
	 * @return array
	 */
	public function get_paths() {
		return $this->paths;
	}
}
