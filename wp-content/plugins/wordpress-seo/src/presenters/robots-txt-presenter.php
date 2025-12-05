<?php

namespace Yoast\WP\SEO\Presenters;

use Yoast\WP\SEO\Helpers\Robots_Txt_Helper;

/**
 * Presenter class for the robots.txt file helper.
 */
class Robots_Txt_Presenter extends Abstract_Presenter {

	public const YOAST_OUTPUT_BEFORE_COMMENT = '# START YOAST BLOCK' . \PHP_EOL . '# ---------------------------' . \PHP_EOL;

	public const YOAST_OUTPUT_AFTER_COMMENT = '# ---------------------------' . \PHP_EOL . '# END YOAST BLOCK';

	/**
	 * Text to be outputted for the allow directive.
	 *
	 * @var string
	 */
	public const ALLOW_DIRECTIVE = 'Allow';

	/**
	 * Text to be outputted for the disallow directive.
	 *
	 * @var string
	 */
	public const DISALLOW_DIRECTIVE = 'Disallow';

	/**
	 * Text to be outputted for the user-agent rule.
	 *
	 * @var string
	 */
	public const USER_AGENT_FIELD = 'User-agent';

	/**
	 * Text to be outputted for the sitemap rule.
	 *
	 * @var string
	 */
	public const SITEMAP_FIELD = 'Sitemap';

	/**
	 * Holds the Robots_Txt_Helper.
	 *
	 * @var Robots_Txt_Helper
	 */
	protected $robots_txt_helper;

	/**
	 * Constructor.
	 *
	 * @param Robots_Txt_Helper $robots_txt_helper The robots txt helper.
	 */
	public function __construct( Robots_Txt_Helper $robots_txt_helper ) {
		$this->robots_txt_helper = $robots_txt_helper;
	}

	/**
	 * Generate content to be placed in a robots.txt file.
	 *
	 * @return string Content to be placed in a robots.txt file.
	 */
	public function present() {
		$robots_txt_content = self::YOAST_OUTPUT_BEFORE_COMMENT;
		$robots_txt_content = $this->handle_user_agents( $robots_txt_content );

		$robots_txt_content = $this->handle_site_maps( $robots_txt_content );

		return $robots_txt_content . self::YOAST_OUTPUT_AFTER_COMMENT;
	}

	/**
	 * Adds user agent directives to the robots txt output string.
	 *
	 * @param array  $user_agents        The list if available user agents.
	 * @param string $robots_txt_content The current working robots txt string.
	 *
	 * @return string
	 */
	private function add_user_agent_directives( $user_agents, $robots_txt_content ) {
		foreach ( $user_agents as $user_agent ) {
			$robots_txt_content .= self::USER_AGENT_FIELD . ': ' . $user_agent->get_user_agent() . \PHP_EOL;

			$robots_txt_content = $this->add_directive_path( $robots_txt_content, $user_agent->get_disallow_paths(), self::DISALLOW_DIRECTIVE );
			$robots_txt_content = $this->add_directive_path( $robots_txt_content, $user_agent->get_allow_paths(), self::ALLOW_DIRECTIVE );

			$robots_txt_content .= \PHP_EOL;
		}

		return $robots_txt_content;
	}

	/**
	 *  Adds user agent directives path content to the robots txt output string.
	 *
	 * @param string $robots_txt_content   The current working robots txt string.
	 * @param array  $paths                The list of paths for which to add a txt entry.
	 * @param string $directive_identifier The identifier for the directives. (Disallow of Allow).
	 *
	 * @return string
	 */
	private function add_directive_path( $robots_txt_content, $paths, $directive_identifier ) {
		if ( \count( $paths ) > 0 ) {
			foreach ( $paths as $path ) {
				$robots_txt_content .= $directive_identifier . ': ' . $path . \PHP_EOL;
			}
		}

		return $robots_txt_content;
	}

	/**
	 * Handles adding user agent content to the robots txt content if there is any.
	 *
	 * @param string $robots_txt_content The current working robots txt string.
	 *
	 * @return string
	 */
	private function handle_user_agents( $robots_txt_content ) {
		$user_agents = $this->robots_txt_helper->get_robots_txt_user_agents();

		if ( ! isset( $user_agents['*'] ) ) {
			$robots_txt_content .= 'User-agent: *' . \PHP_EOL;
			$robots_txt_content .= 'Disallow:' . \PHP_EOL . \PHP_EOL;
		}

		$robots_txt_content = $this->add_user_agent_directives( $user_agents, $robots_txt_content );

		return $robots_txt_content;
	}

	/**
	 * Handles adding sitemap content to the robots txt content.
	 *
	 * @param string $robots_txt_content The current working robots txt string.
	 *
	 * @return string
	 */
	private function handle_site_maps( $robots_txt_content ) {
		$registered_sitemaps = $this->robots_txt_helper->get_sitemap_rules();

		foreach ( $registered_sitemaps as $sitemap ) {
			$robots_txt_content .= self::SITEMAP_FIELD . ': ' . $sitemap . \PHP_EOL;
		}

		return $robots_txt_content;
	}
}
