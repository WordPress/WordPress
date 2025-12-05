<?php // phpcs:ignore Yoast.Files.FileName.InvalidClassFileName -- Reason: this explicitly concerns the Yoast admin and dashboard.

namespace Yoast\WP\SEO\Conditionals;

/**
 * Conditional that is only met when in the admin dashboard, update or Yoast SEO pages.
 */
class Yoast_Admin_And_Dashboard_Conditional implements Conditional {

	/**
	 * Returns `true` when on the admin dashboard, update or Yoast SEO pages.
	 *
	 * @return bool `true` when on the admin dashboard, update or Yoast SEO pages.
	 */
	public function is_met() {
		global $pagenow;

		// Bail out early if we're not on the front-end.
		if ( ! \is_admin() ) {
			return false;
		}

		// Do not output on plugin / theme upgrade pages or when WordPress is upgrading.
		if ( ( \defined( 'IFRAME_REQUEST' ) && \IFRAME_REQUEST ) || $this->on_upgrade_page() || \wp_installing() ) {
			return false;
		}

		if ( $pagenow === 'admin.php' && isset( $_GET['page'] ) && \strpos( $_GET['page'], 'wpseo' ) === 0 ) {
			return true;
		}

		$target_pages = [
			'index.php',
			'plugins.php',
			'update-core.php',
			'options-permalink.php',
		];

		return \in_array( $pagenow, $target_pages, true );
	}

	/**
	 * Checks if we are on a theme or plugin upgrade page.
	 *
	 * @return bool Whether we are on a theme or plugin upgrade page.
	 */
	private function on_upgrade_page() {
		/*
		 * IFRAME_REQUEST is not defined on these pages,
		 * though these action pages do show when upgrading themes or plugins.
		 */
		$actions = [ 'do-theme-upgrade', 'do-plugin-upgrade', 'do-core-upgrade', 'do-core-reinstall' ];
		return isset( $_GET['action'] ) && \in_array( $_GET['action'], $actions, true );
	}
}
