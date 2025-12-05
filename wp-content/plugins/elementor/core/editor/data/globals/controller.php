<?php
namespace Elementor\Core\Editor\Data\Globals;

use Elementor\Data\V2\Base\Controller as Controller_Base;
use Elementor\Data\V2\Base\Endpoint;
use Elementor\Plugin;

class Controller extends Controller_Base {
	public function get_name() {
		return 'globals';
	}

	public function register_endpoints() {
		$this->register_endpoint( new Endpoints\Colors( $this ) );
		$this->register_endpoint( new Endpoints\Typography( $this ) );
	}

	public function get_collection_params() {
		// Does not have 'get_items' args (OPTIONS).
		// Maybe TODO: try `$this->get_index_endpoint()->get_collection_params()`.
		return [];
	}

	public function get_permission_callback( $request ) {
		// Allow internal get global values. (e.g render global.css for a visitor)
		if ( 'GET' === $request->get_method() && Plugin::$instance->data_manager_v2->is_internal() ) {
			return true;
		}

		return current_user_can( 'edit_posts' );
	}

	protected function register_index_endpoint() {
		$this->register_endpoint( new Endpoint\Index\AllChildren( $this ) );
	}
}
