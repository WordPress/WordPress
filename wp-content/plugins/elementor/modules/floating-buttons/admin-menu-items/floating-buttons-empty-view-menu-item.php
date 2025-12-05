<?php
namespace Elementor\Modules\FloatingButtons\AdminMenuItems;

use Elementor\Core\Admin\Menu\Interfaces\Admin_Menu_Item_With_Page;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class Floating_Buttons_Empty_View_Menu_Item extends Floating_Buttons_Menu_Item implements Admin_Menu_Item_With_Page {

	private $render_callback;

	public function __construct( callable $render_callback ) {
		$this->render_callback = $render_callback;
	}

	public function render() {
		( $this->render_callback )();
	}
}
