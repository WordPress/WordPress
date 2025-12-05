<?php // phpcs:ignore Yoast.Files.FileName.InvalidClassFileName -- Reason: this explicitly concerns the Yoast admin.

namespace Yoast\WP\SEO\Conditionals\Admin;

use Yoast\WP\SEO\Conditionals\Conditional;
use Yoast\WP\SEO\Helpers\Current_Page_Helper;

/**
 * Conditional that is only met when on a Yoast SEO admin page.
 */
class Yoast_Admin_Conditional implements Conditional {

	/**
	 * Holds the Current_Page_Helper.
	 *
	 * @var Current_Page_Helper
	 */
	private $current_page_helper;

	/**
	 * Constructs the conditional.
	 *
	 * @param Current_Page_Helper $current_page_helper The current page helper.
	 */
	public function __construct( Current_Page_Helper $current_page_helper ) {
		$this->current_page_helper = $current_page_helper;
	}

	/**
	 * Returns `true` when on the admin dashboard, update or Yoast SEO pages.
	 *
	 * @return bool `true` when on the admin dashboard, update or Yoast SEO pages.
	 */
	public function is_met() {
		if ( ! \is_admin() ) {
			return false;
		}

		return $this->current_page_helper->is_yoast_seo_page();
	}
}
