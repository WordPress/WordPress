<?php

namespace Elementor\App\Modules\ImportExportCustomization\Data\Routes;

abstract class Base_Route {
	public function __construct() {}

	public function register_route( $name_space, $base_route ): void {
		register_rest_route( $name_space, '/' . $base_route . '/' . $this->get_route(), [
			[
				'methods' => $this->get_method(),
				'callback' => fn( $request ) => $this->callback( $request ),
				'permission_callback' => $this->permission_callback(),
				'args' => $this->get_args(),
			],
		] );
	}

	abstract protected function get_route(): string;

	abstract protected function get_method(): string;

	abstract protected function callback( $request ): \WP_REST_Response;

	protected function permission_callback(): callable {
		return fn() => current_user_can( 'manage_options' );
	}

	abstract protected function get_args(): array;
}
