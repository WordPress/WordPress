<?php
/**
 * Class that handles conditional logic for scheduling snippets.
 *
 * @package WPCode
 */

/**
 * The WPCode_Conditional_Schedule_Lite class.
 */
class WPCode_Conditional_Schedule_Lite extends WPCode_Conditional_Type {

	/**
	 * The type unique name (slug).
	 *
	 * @var string
	 */
	public $name = 'schedule';

	/**
	 * Set the translatable label.
	 *
	 * @return void
	 */
	protected function set_label() {
		$this->label = __( 'Schedule', 'insert-headers-and-footers' ) . ' (PRO)';
	}

	/**
	 * Set the type options for the admin mainly.
	 *
	 * @return void
	 */
	public function load_type_options() {
		$this->options = array(
			'date_is' => array(
				'label' => __( 'Date', 'insert-headers-and-footers' ),
				'type'  => 'date',
			),
			'time_is' => array(
				'label' => __( 'Date & Time', 'insert-headers-and-footers' ),
				'type'  => 'datetime',
			),
			'weekday' => array(
				'label'    => __( 'Day of the Week', 'insert-headers-and-footers' ),
				'type'     => 'select',
				'multiple' => true,
			),
			'time'    => array(
				'label' => __( 'Current time', 'insert-headers-and-footers' ),
				'type'  => 'time',
			),
		);

		foreach ( $this->options as $key => $value ) {
			$this->options[ $key ]['upgrade'] = array(
				'title' => __( 'Scheduling rules are a Pro Feature', 'insert-headers-and-footers' ),
				'text'  => __( 'Upgrade today and get access to advanced scheduling conditional logic rules.', 'insert-headers-and-footers' ),
				'link'  => wpcode_utm_url( 'https://wpcode.com/lite/', 'edit-snippet', 'conditional-logic', 'schedule' ),
			);
		}
	}
}

new WPCode_Conditional_Schedule_Lite();
