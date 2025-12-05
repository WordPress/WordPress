<?php

namespace Elementor\Modules\Favorites;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

use Elementor\Data\V2\Base\Controller as Controller_Base;
use Elementor\Plugin;

class Controller extends Controller_Base {

	public function get_name() {
		return 'favorites';
	}

	public function create_item( $request ) {
		$module = $this->get_module();
		$type = $request->get_param( 'id' );
		$favorite = $request->get_param( 'favorite' );

		$module->update( $type, $favorite, $module::ACTION_MERGE );

		return $module->get( $type );
	}

	public function delete_item( $request ) {
		$module = $this->get_module();
		$type = $request->get_param( 'id' );
		$favorite = $request->get_param( 'favorite' );

		$module->update( $type, $favorite, $module::ACTION_DELETE );

		return $module->get( $type );
	}

	public function create_item_permissions_check( $request ) {
		return current_user_can( 'edit_posts' );
	}

	public function delete_item_permissions_check( $request ) {
		return $this->create_item_permissions_check( $request );
	}

	/**
	 * Get the favorites module instance.
	 *
	 * @return Module
	 */
	protected function get_module() {
		return Plugin::instance()->modules_manager->get_modules( 'favorites' );
	}

	public function register_endpoints() {
		$this->index_endpoint->register_item_route( \WP_REST_Server::CREATABLE, [
			'id_arg_type_regex' => '[\w]+',
			'id' => [
				'description' => 'Type of favorites.',
				'type' => 'string',
				'required' => true,
			],
			'favorite' => [
				'description' => 'The favorite slug to create.',
				'type' => 'string',
				'required' => true,
			],
		] );

		$this->index_endpoint->register_item_route( \WP_REST_Server::DELETABLE, [
			'id_arg_type_regex' => '[\w]+',
			'id' => [
				'description' => 'Type of favorites.',
				'type' => 'string',
				'required' => true,
			],
			'favorite' => [
				'description' => 'The favorite slug to delete.',
				'type' => 'string',
				'required' => true,
			],
		] );
	}
}
