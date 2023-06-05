<?php
/**
 * Handles storage and retrieval of a task list section
 */

namespace Automattic\WooCommerce\Admin\Features\OnboardingTasks;

/**
 * Task List section class.
 *
 * @deprecated 7.2.0
 */
class TaskListSection {

	/**
	 * Title.
	 *
	 * @var string
	 */
	public $id = '';

	/**
	 * Title.
	 *
	 * @var string
	 */
	public $title = '';

	/**
	 * Description.
	 *
	 * @var string
	 */
	public $description = '';

	/**
	 * Image.
	 *
	 * @var string
	 */
	public $image = '';

	/**
	 * Tasks.
	 *
	 * @var array
	 */
	public $task_names = array();

	/**
	 * Parent task list.
	 *
	 * @var TaskList
	 */
	protected $task_list;

	/**
	 * Constructor
	 *
	 * @param array         $data Task list data.
	 * @param TaskList|null $task_list Parent task list.
	 */
	public function __construct( $data = array(), $task_list = null ) {
		$defaults = array(
			'id'          => '',
			'title'       => '',
			'description' => '',
			'image'       => '',
			'tasks'       => array(),
		);

		$data = wp_parse_args( $data, $defaults );

		$this->task_list   = $task_list;
		$this->id          = $data['id'];
		$this->title       = $data['title'];
		$this->description = $data['description'];
		$this->image       = $data['image'];
		$this->task_names  = $data['task_names'];
	}

	/**
	 * Returns if section is complete.
	 *
	 * @return boolean;
	 */
	private function is_complete() {
		$complete = true;
		foreach ( $this->task_names as $task_name ) {
			if ( null !== $this->task_list && isset( $this->task_list->task_class_id_map[ $task_name ] ) ) {
				$task = $this->task_list->get_task( $this->task_list->task_class_id_map[ $task_name ] );
				if ( $task->can_view() && ! $task->is_complete() ) {
					$complete = false;
					break;
				}
			}
		}
		return $complete;
	}

	/**
	 * Get the list for use in JSON.
	 *
	 * @return array
	 */
	public function get_json() {
		return array(
			'id'          => $this->id,
			'title'       => $this->title,
			'description' => $this->description,
			'image'       => $this->image,
			'tasks'       => array_map(
				function( $task_name ) {
					if ( null !== $this->task_list && isset( $this->task_list->task_class_id_map[ $task_name ] ) ) {
						return $this->task_list->task_class_id_map[ $task_name ];
					}
					return '';
				},
				$this->task_names
			),
			'isComplete'  => $this->is_complete(),
		);
	}
}
