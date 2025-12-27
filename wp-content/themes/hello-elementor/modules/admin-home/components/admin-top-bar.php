<?php

namespace HelloTheme\Modules\AdminHome\Components;

use HelloTheme\Includes\Script;
use HelloTheme\Includes\Utils;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class Admin_Top_Bar {

	private function render_admin_top_bar() {
		?>
		<div id="ehe-admin-top-bar-root" style="height: 50px">
		</div>
		<?php
	}

	private function is_top_bar_active() {
		$current_screen = get_current_screen();

		return ( false !== strpos( $current_screen->id ?? '', EHP_THEME_SLUG ) );
	}

	private function enqueue_scripts() {
		$script = new Script(
			'hello-elementor-topbar',
		);

		$script->enqueue();
	}

	public function __construct() {
		if ( ! is_admin() ) {
			return;
		}

		add_action( 'current_screen', function () {
			if ( ! $this->is_top_bar_active() ) {
				return;
			}

			add_action( 'in_admin_header', function () {
				$this->render_admin_top_bar();
			} );

			add_action( 'admin_enqueue_scripts', function () {
				$this->enqueue_scripts();
			} );

			add_action( 'elementor/admin-top-bar/is-active', '__return_false' );
		} );
	}
}
