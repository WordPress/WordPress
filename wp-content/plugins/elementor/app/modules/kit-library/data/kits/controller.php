<?php
namespace Elementor\App\Modules\KitLibrary\Data\Kits;

use Elementor\App\Modules\KitLibrary\Data\Base_Controller;
use Elementor\Data\V2\Base\Exceptions\Error_404;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class Controller extends Base_Controller {

	public function get_name() {
		return 'kits';
	}

	public function get_items( $request ) {
		$data = $this->get_repository()->get_all( $request->get_param( 'force' ) );

		return [
			'data' => $data->values(),
		];
	}

	public function get_item( $request ) {
		$data = $this->get_repository()->find( $request->get_param( 'id' ) );

		if ( ! $data ) {
			return new Error_404( esc_html__( 'Kit not exists.', 'elementor' ), 'kit_not_exists' );
		}

		return [
			'data' => $data,
		];
	}

	public function get_collection_params() {
		return [
			'force' => [
				'description' => 'Force an API request and skip the cache.',
				'required' => false,
				'default' => false,
				'type' => 'boolean',
			],
		];
	}

	public function register_endpoints() {
		$this->index_endpoint->register_item_route( \WP_REST_Server::READABLE, [
			'id' => [
				'description' => 'Unique identifier for the object.',
				'type' => 'string',
				'required' => true,
			],
			'id_arg_type_regex' => '[\w]+',
		] );

		$this->register_endpoint( new Endpoints\Download_Link( $this ) );
		$this->register_endpoint( new Endpoints\Favorites( $this ) );
	}

	public function get_permission_callback( $request ) {
		return current_user_can( 'manage_options' );
	}
}
