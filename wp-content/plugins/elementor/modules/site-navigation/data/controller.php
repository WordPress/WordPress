<?php

namespace Elementor\Modules\SiteNavigation\Data;

use Elementor\Plugin;
use Elementor\Data\V2\Base\Controller as Base_Controller;
use Elementor\Modules\SiteNavigation\Data\Endpoints\Add_New_Post;
use Elementor\Modules\SiteNavigation\Data\Endpoints\Duplicate_Post;
use Elementor\Modules\SiteNavigation\Data\Endpoints\Homepage;
use Elementor\Modules\SiteNavigation\Data\Endpoints\Recent_Posts;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class Controller extends Base_Controller {

	public function get_name() {
		return 'site-navigation';
	}

	public function get_items_permissions_check( $request ) {
		return current_user_can( 'edit_posts' );
	}

	public function create_items_permissions_check( $request ): bool {
		// Permissions check is located in the endpoint
		return true;
	}

	public function get_item_permissions_check( $request ) {
		return $this->get_items_permissions_check( $request );
	}

	public function create_item_permissions_check( $request ): bool {
		return $this->create_items_permissions_check( $request );
	}

	public function register_endpoints() {
		$this->register_endpoint( new Recent_Posts( $this ) );
		$this->register_endpoint( new Add_New_Post( $this ) );

		if ( Plugin::$instance->experiments->is_feature_active( 'pages_panel' ) ) {
			$this->register_endpoint( new Duplicate_Post( $this ) );
			$this->register_endpoint( new Homepage( $this ) );
		}
	}

	protected function register_index_endpoint() {
		// Bypass, currently does not required.
	}
}
