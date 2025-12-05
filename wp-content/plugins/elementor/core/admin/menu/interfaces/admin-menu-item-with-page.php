<?php

namespace Elementor\Core\Admin\Menu\Interfaces;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

interface Admin_Menu_Item_With_Page extends Admin_Menu_Item {
	public function get_page_title();

	public function render();
}
