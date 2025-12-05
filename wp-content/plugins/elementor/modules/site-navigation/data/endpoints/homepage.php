<?php

namespace Elementor\Modules\SiteNavigation\Data\Endpoints;

use Elementor\Data\V2\Base\Endpoint;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class Homepage extends Endpoint {

	public function get_permission_callback( $request ) {
		return current_user_can( 'edit_posts' );
	}

	public function get_name() {
		return 'homepage';
	}

	public function get_format() {
		return 'site-navigation/homepage';
	}

	public function get_items( $request ) {
		$homepage_id = get_option( 'page_on_front' );
		$show_on_front = get_option( 'show_on_front' );

		return 'page' === $show_on_front ? intval( $homepage_id ) : 0;
	}
}
