<?php

namespace Yoast\WP\SEO\Helpers;

use Yoast\WP\SEO\Values\Robots\User_Agent_List;

/**
 * A helper object for the robots txt file.
 */
class Robots_Txt_Helper {

	/**
	 * Holds a list of user agents with directives.
	 *
	 * @var User_Agent_List
	 */
	protected $robots_txt_user_agents;

	/**
	 * Holds an array with absolute URLs of sitemaps.
	 *
	 * @var array
	 */
	protected $robots_txt_sitemaps;

	/**
	 * Constructor for Robots_Txt_Helper.
	 */
	public function __construct() {
		$this->robots_txt_user_agents = new User_Agent_List();
		$this->robots_txt_sitemaps    = [];
	}

	/**
	 * Add a disallow rule for a specific user agent if it does not exist yet.
	 *
	 * @param string $user_agent The user agent to add the disallow rule to.
	 * @param string $path       The path to add as a disallow rule.
	 *
	 * @return void
	 */
	public function add_disallow( $user_agent, $path ) {
		$user_agent_container = $this->robots_txt_user_agents->get_user_agent( $user_agent );
		$user_agent_container->add_disallow_directive( $path );
	}

	/**
	 * Add an allow rule for a specific user agent if it does not exist yet.
	 *
	 * @param string $user_agent The user agent to add the allow rule to.
	 * @param string $path       The path to add as a allow rule.
	 *
	 * @return void
	 */
	public function add_allow( $user_agent, $path ) {
		$user_agent_container = $this->robots_txt_user_agents->get_user_agent( $user_agent );
		$user_agent_container->add_allow_directive( $path );
	}

	/**
	 * Add sitemap to robots.txt if it does not exist yet.
	 *
	 * @param string $absolute_path The absolute path to the sitemap to add.
	 *
	 * @return void
	 */
	public function add_sitemap( $absolute_path ) {
		if ( ! \in_array( $absolute_path, $this->robots_txt_sitemaps, true ) ) {
			$this->robots_txt_sitemaps[] = $absolute_path;
		}
	}

	/**
	 * Get all registered disallow directives per user agent.
	 *
	 * @return array The registered disallow directives per user agent.
	 */
	public function get_disallow_directives() {
		return $this->robots_txt_user_agents->get_disallow_directives();
	}

	/**
	 * Get all registered allow directives per user agent.
	 *
	 * @return array The registered allow directives per user agent.
	 */
	public function get_allow_directives() {
		return $this->robots_txt_user_agents->get_allow_directives();
	}

	/**
	 * Get all registered sitemap rules.
	 *
	 * @return array The registered sitemap rules.
	 */
	public function get_sitemap_rules() {
		return $this->robots_txt_sitemaps;
	}

	/**
	 * Get all registered user agents
	 *
	 * @return array The registered user agents.
	 */
	public function get_robots_txt_user_agents() {
		return $this->robots_txt_user_agents->get_user_agents();
	}
}
