<?php

namespace Yoast\WP\SEO\Values\Robots;

/**
 * Class Directive
 */
class User_Agent {

	/**
	 * The user agent identifier.
	 *
	 * @var string
	 */
	private $user_agent;

	/**
	 * All directives that are allowed for this user agent.
	 *
	 * @var Directive
	 */
	private $allow_directive;

	/**
	 * All directives that are disallowed for this user agent.
	 *
	 * @var Directive
	 */
	private $disallow_directive;

	/**
	 * Constructor of the user agent value object.
	 *
	 * @param string $user_agent The user agent identifier.
	 */
	public function __construct( $user_agent ) {
		$this->user_agent         = $user_agent;
		$this->allow_directive    = new Directive();
		$this->disallow_directive = new Directive();
	}

	/**
	 * Gets the user agent identifier.
	 *
	 * @return string
	 */
	public function get_user_agent() {
		return $this->user_agent;
	}

	/**
	 * Adds a path to the directive object.
	 *
	 * @param string $path The path to add to the disallow directive.
	 *
	 * @return void
	 */
	public function add_disallow_directive( $path ) {
		$this->disallow_directive->add_path( $path );
	}

	/**
	 * Adds a path to the directive object.
	 *
	 * @param string $path The path to add to the allow directive.
	 *
	 * @return void
	 */
	public function add_allow_directive( $path ) {
		$this->allow_directive->add_path( $path );
	}

	/**
	 * Gets all disallow paths for this user agent.
	 *
	 * @return array
	 */
	public function get_disallow_paths() {
		return $this->disallow_directive->get_paths();
	}

	/**
	 * Gets all sallow paths for this user agent.
	 *
	 * @return array
	 */
	public function get_allow_paths() {
		return $this->allow_directive->get_paths();
	}
}
