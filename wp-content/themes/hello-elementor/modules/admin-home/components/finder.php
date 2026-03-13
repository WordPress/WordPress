<?php

namespace HelloTheme\Modules\AdminHome\Components;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Finder {

	public function add_hello_theme_finder_entry( $categories_data ) {
		if ( isset( $categories_data['site'] ) && isset( $categories_data['site']['items'] ) ) {
			$categories_data['site']['items']['hello-elementor-home'] = [
				'title' => esc_html__( 'Hello Theme Home', 'hello-elementor' ),
				'icon' => 'paint-brush',
				'url' => admin_url( 'admin.php?page=hello-elementor' ),
				'keywords' => [ 'theme', 'hello', 'home', 'plus', '+' ],
			];
		}

		return $categories_data;
	}

	public function __construct() {
		add_filter( 'elementor/finder/categories', [ $this, 'add_hello_theme_finder_entry' ] );
	}
}
