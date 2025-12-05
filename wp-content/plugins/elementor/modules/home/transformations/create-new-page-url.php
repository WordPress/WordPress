<?php
namespace Elementor\Modules\Home\Transformations;

use Elementor\Modules\Home\Transformations\Base\Transformations_Abstract;
use Elementor\Plugin;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class Create_New_Page_Url extends Transformations_Abstract {

	public function transform( array $home_screen_data ): array {
		$home_screen_data['button_cta_url'] = Plugin::$instance->documents->get_create_new_post_url( 'page' );

		return $home_screen_data;
	}
}
