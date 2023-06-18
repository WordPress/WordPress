<?php
/**
 * Class that handles conditional logic related to Easy Digital Downloads.
 *
 * @package WPCode
 */

/**
 * The WPCode_Conditional_EDD class.
 */
class WPCode_Conditional_EDD_Lite extends WPCode_Conditional_Type {

	/**
	 * The type unique name (slug).
	 *
	 * @var string
	 */
	public $name = 'edd';

	/**
	 * Set the translatable label.
	 *
	 * @return void
	 */
	protected function set_label() {
		$this->label = 'Easy Digital Downloads (PRO)';
	}

	/**
	 * Set the type options for the admin mainly.
	 *
	 * @return void
	 */
	public function load_type_options() {
		$this->options = array(
			'edd_page' => array(
				'label'   => __( 'EDD Page', 'insert-headers-and-footers' ),
				'type'    => 'select',
				'upgrade' => array(
					'title' => __( 'Easy Digital Downloads Page Rules is a Pro Feature', 'insert-headers-and-footers' ),
					'text'  => __( 'Get access to advanced conditional logic rules for Easy Digital Downloads by upgrading to PRO today.', 'insert-headers-and-footers' ),
					'link'  => wpcode_utm_url( 'https://wpcode.com/lite/', 'edit-snippet', 'conditional-logic', 'edd-page' ),
				),
				'options' => array(
					array(
						'label'    => __( 'Checkout Page', 'insert-headers-and-footers' ),
						'value'    => '',
						'disabled' => true,
					),
					array(
						'label'    => __( 'Success Page', 'insert-headers-and-footers' ),
						'value'    => '',
						'disabled' => true,
					),
					array(
						'label'    => __( 'Single Download Page', 'insert-headers-and-footers' ),
						'value'    => '',
						'disabled' => true,
					),
					array(
						'label'    => __( 'Download Category Page', 'insert-headers-and-footers' ),
						'value'    => '',
						'disabled' => true,
					),
					array(
						'label'    => __( 'Download Tag Page', 'insert-headers-and-footers' ),
						'value'    => '',
						'disabled' => true,
					),
				),
			),
		);
	}
}

new WPCode_Conditional_EDD_Lite();
