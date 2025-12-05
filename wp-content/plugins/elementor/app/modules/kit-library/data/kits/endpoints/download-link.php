<?php
namespace Elementor\App\Modules\KitLibrary\Data\Kits\Endpoints;

use Elementor\Data\V2\Base\Endpoint;
use Elementor\App\Modules\KitLibrary\Data\Kits\Controller;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * @property Controller $controller
 */
class Download_Link extends Endpoint {
	public function get_name() {
		return 'download-link';
	}

	public function get_format() {
		return 'kits/download-link/{id}';
	}

	protected function register() {
		$this->register_item_route( \WP_REST_Server::READABLE, [
			'id_arg_type_regex' => '[\w]+',
		] );
	}

	public function get_item( $id, $request ) {
		$repository = $this->controller->get_repository();
		$data = $repository->get_download_link( $id );

		return [
			'data' => $data,
			'meta' => [
				'nonce' => wp_create_nonce( 'kit-library-import' ),
			],
		];
	}
}
