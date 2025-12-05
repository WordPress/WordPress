<?php
namespace Elementor\Modules\KitElementsDefaults\Data;

use Elementor\Core\Frontend\Performance;
use Elementor\Modules\KitElementsDefaults\Module;
use Elementor\Modules\KitElementsDefaults\Utils\Settings_Sanitizer;
use Elementor\Plugin;
use Elementor\Data\V2\Base\Exceptions\Error_404;
use Elementor\Data\V2\Base\Controller as Base_Controller;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class Controller extends Base_Controller {

	public function get_name() {
		return 'kit-elements-defaults';
	}

	public function register_endpoints() {
		$this->index_endpoint->register_item_route(\WP_REST_Server::EDITABLE, [
			'id_arg_name' => 'type',
			'id_arg_type_regex' => '[\w\-\_]+',
			'type' => [
				'type' => 'string',
				'description' => 'The type of the element.',
				'required' => true,
				'validate_callback' => function( $type ) {
					return $this->validate_type( $type );
				},
			],
			'settings' => [
				'description' => 'All the default values for the requested type',
				'required' => true,
				'type' => 'object',
				'validate_callback' => function( $settings ) {
					return is_array( $settings );
				},
				'sanitize_callback' => function( $settings, \WP_REST_Request $request ) {
					Performance::set_use_style_controls( true );

					$sanitizer = new Settings_Sanitizer(
						Plugin::$instance->elements_manager,
						array_keys( Plugin::$instance->widgets_manager->get_widget_types() )
					);

					$sanitized_data = $sanitizer
						->for( $request->get_param( 'type' ) )
						->using( $settings )
						->remove_invalid_settings()
						->kses_deep()
						->get();

					Performance::set_use_style_controls( false );

					return $sanitized_data;
				},
			],
		] );

		$this->index_endpoint->register_item_route(\WP_REST_Server::DELETABLE, [
			'id_arg_name' => 'type',
			'id_arg_type_regex' => '[\w\-\_]+',
			'type' => [
				'type' => 'string',
				'description' => 'The type of the element.',
				'required' => true,
				'validate_callback' => function( $type ) {
					return $this->validate_type( $type );
				},
			],
		] );
	}

	public function get_collection_params() {
		return [];
	}

	public function get_items( $request ) {
		$this->validate_kit();

		$kit = Plugin::$instance->kits_manager->get_active_kit();

		return (object) $kit->get_json_meta( Module::META_KEY );
	}

	public function update_item( $request ) {
		$this->validate_kit();

		$kit = Plugin::$instance->kits_manager->get_active_kit();

		$data = $kit->get_json_meta( Module::META_KEY );

		$data[ $request->get_param( 'type' ) ] = $request->get_param( 'settings' );

		$kit->update_json_meta( Module::META_KEY, $data );

		return (object) [];
	}

	public function delete_item( $request ) {
		$this->validate_kit();

		$kit = Plugin::$instance->kits_manager->get_active_kit();

		$data = $kit->get_json_meta( Module::META_KEY );

		unset( $data[ $request->get_param( 'type' ) ] );

		$kit->update_json_meta( Module::META_KEY, $data );

		return (object) [];
	}

	private function validate_kit() {
		$kit = Plugin::$instance->kits_manager->get_active_kit();
		$is_valid_kit = $kit && $kit->get_main_id();

		if ( ! $is_valid_kit ) {
			throw new Error_404( 'Kit doesn\'t exist.' );
		}
	}

	private function validate_type( $param ) {
		$element_types = array_keys( Plugin::$instance->elements_manager->get_element_types() );
		$widget_types  = array_keys( Plugin::$instance->widgets_manager->get_widget_types() );

		return in_array(
			$param,
			array_merge( $element_types, $widget_types ),
			true
		);
	}

	public function get_items_permissions_check( $request ) {
		return current_user_can( 'edit_posts' );
	}

	/**
	 * TODO: Should be removed once the infra will support it.
	 */
	public function get_item_permissions_check( $request ) {
		return $this->get_items_permissions_check( $request );
	}

	public function update_item_permissions_check( $request ) {
		return current_user_can( 'manage_options' );
	}

	public function delete_item_permissions_check( $request ) {
		return current_user_can( 'manage_options' );
	}
}
