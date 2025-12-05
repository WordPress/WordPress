<?php

namespace Yoast\WP\SEO\Integrations\Admin;

use WP_Screen;
use WPSEO_Admin_Asset_Manager;
use Yoast\WP\SEO\Conditionals\Admin_Conditional;
use Yoast\WP\SEO\Conditionals\News_Conditional;
use Yoast\WP\SEO\Integrations\Integration_Interface;

/**
 * Fix_News_Dependencies_Integration class.
 */
class Fix_News_Dependencies_Integration implements Integration_Interface {

	/**
	 * Returns the conditionals based in which this loadable should be active.
	 *
	 * In this case: when on an admin page.
	 *
	 * @return array The conditionals.
	 */
	public static function get_conditionals() {
		return [ Admin_Conditional::class, News_Conditional::class ];
	}

	/**
	 * Registers an action to disable script concatenation.
	 *
	 * @return void
	 */
	public function register_hooks() {
		global $pagenow;

		// Load the editor script when on an edit post or new post page.
		$is_post_edit_page = $pagenow === 'post.php' || $pagenow === 'post-new.php';
		if ( $is_post_edit_page ) {
			\add_action( 'admin_enqueue_scripts', [ $this, 'add_news_script_dependency' ], 11 );
		}
	}

	/**
	 * Fixes the news script dependency.
	 *
	 * @return void
	 */
	public function add_news_script_dependency() {
		$scripts = \wp_scripts();

		if ( ! isset( $scripts->registered['wpseo-news-editor'] ) ) {
			return;
		}

		$is_block_editor  = WP_Screen::get()->is_block_editor();
		$post_edit_handle = 'post-edit';
		if ( ! $is_block_editor ) {
			$post_edit_handle = 'post-edit-classic';
		}

		$scripts->registered['wpseo-news-editor']->deps[] = WPSEO_Admin_Asset_Manager::PREFIX . $post_edit_handle;
	}
}
