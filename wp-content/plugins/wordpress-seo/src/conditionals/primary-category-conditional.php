<?php

namespace Yoast\WP\SEO\Conditionals;

use Yoast\WP\SEO\Helpers\Current_Page_Helper;

/**
 * Conditional that is only met when in frontend or page is a post overview or post add/edit form.
 */
class Primary_Category_Conditional implements Conditional {

	/**
	 * The current page helper.
	 *
	 * @var Current_Page_Helper
	 */
	private $current_page;

	/**
	 * Primary_Category_Conditional constructor.
	 *
	 * @param Current_Page_Helper $current_page The current page helper.
	 */
	public function __construct( Current_Page_Helper $current_page ) {
		$this->current_page = $current_page;
	}

	/**
	 * Returns `true` when on the frontend,
	 * or when on the post overview, post edit or new post admin page,
	 * or when on additional admin pages, allowed by filter.
	 *
	 * @return bool `true` when on the frontend, or when on the post overview,
	 *          post edit, new post admin page or additional admin pages, allowed by filter.
	 */
	public function is_met() {

		if ( ! \is_admin() ) {
			return true;
		}

		$current_page = $this->current_page->get_current_admin_page();
		// phpcs:ignore WordPress.Security.NonceVerification.Missing -- Reason: We are not processing form information.
		if ( $current_page === 'admin-ajax.php' && isset( $_POST['action'] ) && $_POST['action'] === 'wp-link-ajax' ) {
			return true;
		}

		/**
		 * Filter: Adds the possibility to use primary category at additional admin pages.
		 *
		 * @param array $admin_pages List of additional admin pages.
		 */
		$additional_pages = \apply_filters( 'wpseo_primary_category_admin_pages', [] );
		return \in_array( $current_page, \array_merge( [ 'edit.php', 'post.php', 'post-new.php' ], $additional_pages ), true );
	}
}
