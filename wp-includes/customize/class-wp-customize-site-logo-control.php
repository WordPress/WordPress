<?php
/**
 * Customize API: WP_Customize_Site_Logo_Control class
 *
 * @package WordPress
 * @subpackage Customize
 * @since 4.5.0
 */

/**
 * Customize Site Logo control class.
 *
 * Used only for custom functionality in JavaScript.
 *
 * @since 4.5.0
 *
 * @see WP_Customize_Image_Control
 */
class WP_Customize_Site_Logo_Control extends WP_Customize_Image_Control {

	/**
	 * Control type.
	 *
	 * @since 4.5.0
	 * @access public
	 * @var string
	 */
	public $type = 'site_logo';

	/**
	 * Constructor.
	 *
	 * @since 4.5.0
	 * @access public
	 *
	 * @param WP_Customize_Manager $manager Customizer bootstrap instance.
	 * @param string               $id      Control ID.
	 * @param array                $args    Optional. Arguments to override class property defaults.
	 */
	public function __construct( $manager, $id, $args = array() ) {
		parent::__construct( $manager, $id, $args );

		$this->button_labels = array(
			'select'       => __( 'Select logo' ),
			'change'       => __( 'Change logo' ),
			'remove'       => __( 'Remove' ),
			'default'      => __( 'Default' ),
			'placeholder'  => __( 'No logo selected' ),
			'frame_title'  => __( 'Select logo' ),
			'frame_button' => __( 'Choose logo' ),
		);
	}
}
