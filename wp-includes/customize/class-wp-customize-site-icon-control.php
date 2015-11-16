<?php
/**
 * Customize API: WP_Customize_Site_Icon_Control class
 *
 * @package WordPress
 * @subpackage Customize
 * @since 4.4.0
 */

/**
 * Customize Site Icon control class.
 *
 * Used only for custom functionality in JavaScript.
 *
 * @since 4.3.0
 *
 * @see WP_Customize_Cropped_Image_Control
 */
class WP_Customize_Site_Icon_Control extends WP_Customize_Cropped_Image_Control {

	/**
	 * Control type.
	 *
	 * @since 4.3.0
	 * @access public
	 * @var string
	 */
	public $type = 'site_icon';

	/**
	 * Constructor.
	 *
	 * @since 4.3.0
	 * @access public
	 *
	 * @param WP_Customize_Manager $manager Customizer bootstrap instance.
	 * @param string               $id      Control ID.
	 * @param array                $args    Optional. Arguments to override class property defaults.
	 */
	public function __construct( $manager, $id, $args = array() ) {
		parent::__construct( $manager, $id, $args );
		add_action( 'customize_controls_print_styles', 'wp_site_icon', 99 );
	}
}
