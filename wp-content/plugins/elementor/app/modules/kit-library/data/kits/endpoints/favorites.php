<?php
namespace Elementor\App\Modules\KitLibrary\Data\Kits\Endpoints;

use Elementor\App\Modules\KitLibrary\Data\Kits\Controller;
use Elementor\Data\V2\Base\Endpoint;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * @property Controller $controller
 */
class Favorites extends Endpoint {
	public function get_name() {
		return 'favorites';
	}

	public function get_format() {
		return 'kits/favorites/{id}';
	}

	protected function register() {
		$args = [
			'id_arg_type_regex' => '[\w]+',
		];

		$this->register_item_route( \WP_REST_Server::CREATABLE, $args );
		$this->register_item_route( \WP_REST_Server::DELETABLE, $args );
	}

	public function create_item( $id, $request ) {
		$repository = $this->controller->get_repository();
		$kit = $repository->add_to_favorites( $id );

		return [
			'data' => $kit,
		];
	}

	public function delete_item( $id, $request ) {
		$repository = $this->controller->get_repository();

		$kit = $repository->remove_from_favorites( $id );

		return [
			'data' => $kit,
		];
	}
}
