<?php

namespace Yoast\WP\SEO\Integrations\Admin;

use Yoast\WP\SEO\Conditionals\Admin_Conditional;
use Yoast\WP\SEO\Helpers\Redirect_Helper;
use Yoast\WP\SEO\Integrations\Integration_Interface;

/**
 * Redirections_Tools_Page class
 */
class Redirections_Tools_Page implements Integration_Interface {

	/**
	 * The redirect helper.
	 *
	 * @var Redirect_Helper
	 */
	private $redirect_helper;

	/**
	 * Returns the conditionals based in which this loadable should be active.
	 *
	 * @return array<string>
	 */
	public static function get_conditionals() {
		return [
			Admin_Conditional::class,
		];
	}

	/**
	 * Constructor.
	 *
	 * @param Redirect_Helper $redirect_helper The redirect helper.
	 */
	public function __construct(
		Redirect_Helper $redirect_helper
	) {
		$this->redirect_helper = $redirect_helper;
	}

	/**
	 * Registers all hooks to WordPress.
	 *
	 * @return void
	 */
	public function register_hooks() {
		\add_action( 'admin_menu', [ $this, 'register_admin_menu' ] );
	}

	/**
	 * Registers the admin menu.
	 *
	 * @return void
	 */
	public function register_admin_menu() {
		$page_title = \sprintf(
			/* translators: %s: expands to Yoast */
			\esc_html__( '%s Redirects', 'wordpress-seo' ),
			'Yoast'
		);

		\add_management_page(
			$page_title,
			$page_title,
			'edit_others_posts',
			'wpseo_redirects_tools',
			[ $this, 'show_redirects_page' ]
		);
	}

	/**
	 * The redirects tools page render function, noop.
	 *
	 * @return void
	 */
	public function show_redirects_page() {
		// Do nothing and let the redirect happen from the redirect integration.
	}
}
