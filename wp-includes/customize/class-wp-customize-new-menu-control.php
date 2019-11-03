<?php
/**
 * Customize API: WP_Customize_New_Menu_Control class
 *
 * @package WordPress
 * @subpackage Customize
 * @since 4.4.0
 * @deprecated 4.9.0 This file is no longer used as of the menu creation UX introduced in #40104.
 */

_deprecated_file( basename( __FILE__ ), '4.9.0' );

/**
 * Customize control class for new menus.
 *
 * @since 4.3.0
 * @deprecated 4.9.0 This class is no longer used as of the menu creation UX introduced in #40104.
 *
 * @see WP_Customize_Control
 */
class WP_Customize_New_Menu_Control extends WP_Customize_Control {

	/**
	 * Control type.
	 *
	 * @since 4.3.0
	 * @var string
	 */
	public $type = 'new_menu';

	/**
	 * Constructor.
	 *
	 * @since 4.9.0
	 * @deprecated 4.9.0
	 *
	 * @param WP_Customize_Manager $manager Manager.
	 * @param string               $id      ID.
	 * @param array                $args    Args.
	 */
	public function __construct( WP_Customize_Manager $manager, $id, array $args = array() ) {
		_deprecated_function( __METHOD__, '4.9.0' );
		parent::__construct( $manager, $id, $args );
	}

	/**
	 * Render the control's content.
	 *
	 * @since 4.3.0
	 * @deprecated 4.9.0
	 */
	public function render_content() {
		_deprecated_function( __METHOD__, '4.9.0' );
		?>
		<button type="button" class="button button-primary" id="create-new-menu-submit"><?php _e( 'Create Menu' ); ?></button>
		<span class="spinner"></span>
		<?php
	}
}
