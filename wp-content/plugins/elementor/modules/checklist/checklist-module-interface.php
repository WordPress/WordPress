<?php

namespace Elementor\Modules\Checklist;

use Elementor\Core\Isolation\Wordpress_Adapter;
use Elementor\Core\Isolation\Elementor_Adapter;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

interface Checklist_Module_Interface {
	public function get_name(): string;

	public function get_user_progress_from_db(): array;

	public function get_step_progress( $step_id ): ?array;

	public function set_step_progress( $step_id, $step_progress ): void;

	public function get_steps_manager(): Steps_Manager;

	public function get_wordpress_adapter(): Wordpress_Adapter;

	public function get_elementor_adapter(): Elementor_Adapter;
}
