<?php
namespace Elementor\Core\Upgrade;

use Elementor\Core\Base\Background_Task_Manager;
use Elementor\Plugin;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class Custom_Tasks_Manager extends Background_Task_Manager {
	const TASKS_OPTION_KEY = 'elementor_custom_tasks';

	const QUERY_LIMIT = 100;

	public function get_name() {
		return 'custom-task-manager';
	}

	public function get_action() {
		return 'custom_task_manger';
	}

	public function get_plugin_name() {
		return 'elementor';
	}

	public function get_plugin_label() {
		return esc_html__( 'Elementor', 'elementor' );
	}

	public function get_task_runner_class() {
		return Task::class;
	}

	public function get_query_limit() {
		return self::QUERY_LIMIT;
	}

	protected function start_run() {
		$custom_tasks_callbacks = $this->get_custom_tasks();

		if ( empty( $custom_tasks_callbacks ) ) {
			return;
		}

		$task_runner = $this->get_task_runner();

		foreach ( $custom_tasks_callbacks as $callback ) {
			$task_runner->push_to_queue( [
				'callback' => $callback,
			] );
		}

		$this->clear_tasks_requested_to_run();

		Plugin::$instance->logger->get_logger()->info( 'Elementor custom task(s) process has been queued.', [
			'meta' => [ $custom_tasks_callbacks ],
		] );

		$task_runner->save()->dispatch();
	}

	public function get_tasks_class() {
		return Custom_Tasks::class;
	}

	public function get_tasks_requested_to_run() {
		return get_option( self::TASKS_OPTION_KEY, [] );
	}

	public function clear_tasks_requested_to_run() {
		return update_option( self::TASKS_OPTION_KEY, [], false );
	}

	public function add_tasks_requested_to_run( $tasks = [] ) {
		$current_tasks = $this->get_tasks_requested_to_run();
		$current_tasks = array_merge( $current_tasks, $tasks );

		update_option( self::TASKS_OPTION_KEY, $current_tasks, false );
	}

	private function get_custom_tasks() {
		$tasks_requested_to_run = $this->get_tasks_requested_to_run();

		$tasks_class = $this->get_tasks_class();
		$tasks_reflection = new \ReflectionClass( $tasks_class );

		$callbacks = [];

		foreach ( $tasks_reflection->getMethods() as $method ) {
			$method_name = $method->getName();

			if ( in_array( $method_name, $tasks_requested_to_run, true ) ) {
				$callbacks[] = [ $tasks_class, $method_name ];
			}
		}

		return $callbacks;
	}

	public function __construct() {
		$task_runner = $this->get_task_runner();

		if ( $task_runner->is_running() ) {
			return;
		}

		$this->start_run();
	}
}
