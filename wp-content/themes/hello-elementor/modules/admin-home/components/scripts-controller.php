<?php

namespace HelloTheme\Modules\AdminHome\Components;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

use HelloTheme\Includes\Script;

class Scripts_Controller {

	public function __construct() {
		add_action( 'admin_enqueue_scripts', [ $this, 'admin_enqueue_scripts' ] );
	}

	public function admin_enqueue_scripts() {
		$screen = get_current_screen();

		if ( 'toplevel_page_' . EHP_THEME_SLUG !== $screen->id ) {
			return;
		}

		$script = new Script(
			'hello-home-app',
			[ 'wp-util' ]
		);

		$script->enqueue();
	}
}
