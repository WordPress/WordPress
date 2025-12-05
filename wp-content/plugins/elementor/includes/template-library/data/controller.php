<?php
namespace Elementor\Includes\TemplateLibrary\Data;

use Elementor\User;
use Elementor\TemplateLibrary\Source_Local;
use Elementor\Data\V2\Base\Controller as Controller_Base;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class Controller extends Controller_Base {

	public function get_name() {
		return 'template-library';
	}

	public function register_endpoints() {
		$this->register_endpoint( new Endpoints\Templates( $this ) );
	}

	protected function register_index_endpoint() {
		// Bypass, currently does not required.
	}

	public function get_permission_callback( $request ) {
		return User::is_current_user_can_edit_post_type( Source_Local::CPT );
	}
}
