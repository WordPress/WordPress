<?php
namespace Elementor\Modules\Checklist\Data\Endpoints;

use Elementor\Data\V2\Base\Endpoint as Endpoint_Base;
use Elementor\Modules\Checklist\Module as Checklist_Module;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class User_Progress extends Endpoint_Base {
	protected function register() {
		parent::register();

		$this->register_items_route( \WP_REST_Server::EDITABLE );
	}

	public function get_name(): string {
		return 'user-progress';
	}

	public function get_format(): string {
		return 'checklist';
	}

	public function get_items( $request ) {
		return $this->get_checklist_data();
	}

	public function update_items( $request ) {
		Checklist_Module::instance()->update_user_progress( $request->get_json_params() );

		return [
			'data' => 'success',
		];
	}

	private function get_checklist_data(): array {
		$checklist_module = Checklist_Module::instance();
		$progress_data = $checklist_module->get_user_progress_from_db();

		return [
			'data' => $progress_data,
		];
	}
}
