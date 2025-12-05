<?php

namespace Elementor\Modules\WpRest\Classes;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class Elementor_User_Meta {

	private function get_meta_config(): array {
		return [
			'elementor_introduction' => [
				'schema' => [
					'description' => 'Elementor user meta data',
					'type' => 'object',
					'properties' => [
						'ai_get_started' => [
							'type' => 'boolean',
						],
					],
					'additionalProperties' => true,
					'context' => [ 'view', 'edit' ],
				],
			],
		];
	}

	public function register(): void {
		foreach ( $this->get_meta_config() as $key => $config ) {
			$config['get_callback'] = function( $user, $field_name, $request ) {
				return get_user_meta( $user['id'], $field_name, true );
			};

			$config['update_callback'] = function( $meta_value, \WP_User $user, $field_name, $request ) {
				if ( 'PATCH' === $request->get_method() ) {
					$existing = get_user_meta( $user->ID, $field_name, true );
					if ( is_array( $existing ) && is_array( $meta_value ) ) {
						$meta_value = array_merge( $existing, $meta_value );
					}
				}

				return update_user_meta( $user->ID, $field_name, $meta_value );
			};

			register_rest_field( 'user', $key, $config );
		}
	}
}
