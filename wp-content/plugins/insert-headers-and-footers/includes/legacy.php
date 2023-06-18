<?php
/**
 * Call new methods using the old class for backwards compatibility.
 *
 * @package WPCode
 */

if ( ! class_exists( 'InsertHeadersAndFooters' ) ) {
	/**
	 * Class InsertHeadersAndFooters used in the IHAF 1.x.x.
	 */
	class InsertHeadersAndFooters {

		/**
		 * Output the header code.
		 *
		 * @return void
		 */
		public function frontendHeader() {// phpcs:ignore WordPress.NamingConventions.ValidFunctionName.MethodNameInvalid
			wpcode_global_frontend_header();
		}

		/**
		 * Output the footer code.
		 *
		 * @return void
		 */
		public function frontendFooter() {// phpcs:ignore WordPress.NamingConventions.ValidFunctionName.MethodNameInvalid
			wpcode_global_frontend_footer();
		}

		/**
		 * Output the body code.
		 *
		 * @return void
		 */
		public function frontendBody() {// phpcs:ignore WordPress.NamingConventions.ValidFunctionName.MethodNameInvalid
			wpcode_global_frontend_body();
		}

	}
}
