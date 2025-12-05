<?php
namespace Elementor\Core\Common\Modules\Connect;

use Elementor\Core\Admin\Menu\Interfaces\Admin_Menu_Item_With_Page;
use Elementor\Core\Common\Modules\Connect\Apps\Base_App;
use Elementor\Plugin;
use Elementor\Settings;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class Connect_Menu_Item implements Admin_Menu_Item_With_Page {

	public function is_visible() {
		return false;
	}

	public function get_parent_slug() {
		return Settings::PAGE_ID;
	}

	public function get_label() {
		return esc_html__( 'Connect', 'elementor' );
	}

	public function get_page_title() {
		return esc_html__( 'Connect', 'elementor' );
	}

	public function get_capability() {
		return 'edit_posts';
	}

	public function render() {
		$apps = Plugin::$instance->common->get_component( 'connect' )->get_apps();
		?>
		<style>
			.elementor-connect-app-wrapper{
				margin-bottom: 50px;
				overflow: hidden;
			}
		</style>
		<div class="wrap">
			<?php

			/** @var Base_App $app */
			foreach ( $apps as $app ) {
				echo '<div class="elementor-connect-app-wrapper">';
				$app->render_admin_widget();
				echo '</div>';
			}

			?>
		</div><!-- /.wrap -->
		<?php
	}
}
