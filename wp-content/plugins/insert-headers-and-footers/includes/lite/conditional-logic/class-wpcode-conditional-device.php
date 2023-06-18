<?php
/**
 * Placeholder Class that handles conditional logic for device type
 *
 * @package WPCode
 */

/**
 * The WPCode_Conditional_Device_Lite class.
 */
class WPCode_Conditional_Device_Lite extends WPCode_Conditional_Type {

	/**
	 * The type unique name (slug).
	 *
	 * @var string
	 */
	public $name = 'device';

	/**
	 * Set the translatable label.
	 *
	 * @return void
	 */
	protected function set_label() {
		$this->label = __( 'Device Type', 'insert-headers-and-footers' ) . ' (PRO)';
	}

	/**
	 * Set the type options for the admin mainly.
	 *
	 * @return void
	 */
	public function load_type_options() {
		$this->options = array(
			'device_type' => array(
				'label'   => __( 'Device Type', 'insert-headers-and-footers' ),
				'type'    => 'select',
				'upgrade' => array(
					'title' => __( 'Device Type Rules are a Pro Feature', 'insert-headers-and-footers' ),
					'text'  => __( 'Get access to advanced device type conditional logic rules by upgrading to PRO today.', 'insert-headers-and-footers' ),
					'link'  => wpcode_utm_url( 'https://wpcode.com/lite/', 'edit-snippet', 'conditional-logic', 'device-type' ),
				),
				'options' => array(
					array(
						'label'    => __( 'Desktop', 'insert-headers-and-footers' ),
						'value'    => '',
						'disabled' => true,
					),
					array(
						'label'    => __( 'Mobile', 'insert-headers-and-footers' ),
						'value'    => '',
						'disabled' => true,
					),
				),
			),
		);
	}
}

new WPCode_Conditional_Device_Lite();
