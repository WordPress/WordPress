<?php
namespace Elementor\Data\V2\Base\Endpoint;

use Elementor\Data\V2\Base\Endpoint;
use WP_REST_Server;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class Index extends Endpoint {
	public function get_name() {
		return 'index';
	}

	public function get_format() {
		return "{$this->controller->get_full_name()}/{id}";
	}

	public function get_public_name() {
		return '';
	}

	public function get_items( $request ) {
		return $this->controller->get_items( $request );
	}

	public function get_item( $id, $request ) {
		return $this->controller->get_item( $request );
	}

	public function create_items( $request ) {
		return $this->controller->create_items( $request );
	}

	public function create_item( $id, $request ) {
		return $this->controller->create_item( $request );
	}

	public function update_items( $request ) {
		return $this->controller->update_items( $request );
	}

	public function update_item( $id, $request ) {
		return $this->controller->update_item( $request );
	}

	public function delete_items( $request ) {
		return $this->controller->delete_items( $request );
	}

	public function delete_item( $id, $request ) {
		return $this->controller->delete_item( $request );
	}

	public function register_items_route( $methods = WP_REST_Server::READABLE, $args = [] ) {
		parent::register_items_route( $methods, array_merge( $this->controller->get_items_args( $methods ), $args ) );
	}

	public function register_item_route( $methods = WP_REST_Server::READABLE, $args = [], $route = '/' ) {
		parent::register_item_route( $methods, array_merge( $this->controller->get_item_args( $methods ), $args ), $route );
	}
}
