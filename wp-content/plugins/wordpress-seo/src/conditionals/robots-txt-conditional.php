<?php

namespace Yoast\WP\SEO\Conditionals;

/**
 * Conditional that is only met when on the front end or Yoast file editor page.
 */
class Robots_Txt_Conditional implements Conditional {

	/**
	 * Holds the Front_End_Conditional instance.
	 *
	 * @var Front_End_Conditional
	 */
	protected $front_end_conditional;

	/**
	 * Constructs the class.
	 *
	 * @param Front_End_Conditional $front_end_conditional The front end conditional.
	 */
	public function __construct( Front_End_Conditional $front_end_conditional ) {
		$this->front_end_conditional = $front_end_conditional;
	}

	/**
	 * Returns whether or not this conditional is met.
	 *
	 * @return bool Whether or not the conditional is met.
	 */
	public function is_met() {
		return $this->front_end_conditional->is_met() || $this->is_file_editor_page();
	}

	/**
	 * Returns whether the current page is the file editor page.
	 *
	 * This checks for two locations:
	 * - Multisite network admin file editor page
	 * - Single site file editor page (under tools)
	 *
	 * @return bool
	 */
	protected function is_file_editor_page() {
		global $pagenow;

		if ( $pagenow !== 'admin.php' ) {
			return false;
		}

		// phpcs:ignore WordPress.Security.NonceVerification -- This is not a form.
		if ( isset( $_GET['page'] ) && $_GET['page'] === 'wpseo_files' && \is_multisite() && \is_network_admin() ) {
			return true;
		}

		// phpcs:ignore WordPress.Security.NonceVerification -- This is not a form.
		if ( ! ( isset( $_GET['page'] ) && $_GET['page'] === 'wpseo_tools' ) ) {
			return false;
		}

		// phpcs:ignore WordPress.Security.NonceVerification -- This is not a form.
		if ( isset( $_GET['tool'] ) && $_GET['tool'] === 'file-editor' ) {
			return true;
		}

		return false;
	}
}
