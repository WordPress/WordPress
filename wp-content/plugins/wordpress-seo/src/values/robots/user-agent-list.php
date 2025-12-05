<?php

namespace Yoast\WP\SEO\Values\Robots;

/**
 * Class User_Agent_List
 */
class User_Agent_List {

	/**
	 * The list of user agents.
	 *
	 * @var array
	 */
	private $user_agent_list;

	/**
	 * User Agent list constructor.
	 */
	public function __construct() {
		$this->user_agent_list = [];
	}

	/**
	 * Checks if given user_agent is already registered.
	 *
	 * @param string $user_agent The user agent identifier.
	 *
	 * @return bool
	 */
	public function has_user_agent( $user_agent ) {
		return \array_key_exists( $user_agent, $this->user_agent_list );
	}

	/**
	 * Gets the user agent object. If it is not yet registered it creates it.
	 *
	 * @param string $user_agent The user agent identifier.
	 *
	 * @return User_Agent
	 */
	public function get_user_agent( $user_agent ) {
		if ( $this->has_user_agent( $user_agent ) ) {
			return $this->user_agent_list[ $user_agent ];
		}

		$this->user_agent_list[ $user_agent ] = new User_Agent( $user_agent );

		return $this->user_agent_list[ $user_agent ];
	}

	/**
	 * Gets the list of user agents.
	 *
	 * @return array
	 */
	public function get_user_agents() {
		return $this->user_agent_list;
	}

	/**
	 * Gets a list of all disallow directives by user agent.
	 *
	 * @return array
	 */
	public function get_disallow_directives() {
		$directives = [];
		foreach ( $this->user_agent_list as $user_agent ) {
			$directives[ $user_agent->get_user_agent() ] = $user_agent->get_disallow_paths();
		}

		return $directives;
	}

	/**
	 * Gets a list of all sallow directives by user agent.
	 *
	 * @return array
	 */
	public function get_allow_directives() {
		$directives = [];
		foreach ( $this->user_agent_list as $user_agent ) {
			$directives[ $user_agent->get_user_agent() ] = $user_agent->get_allow_paths();
		}

		return $directives;
	}
}
