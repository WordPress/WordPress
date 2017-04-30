<?php

add_action( 'cs_init', array( 'CustomSidebarsWidgets', 'instance' ) );

/**
 * Extends the widgets section to add the custom sidebars UI elements.
 */
class CustomSidebarsWidgets extends CustomSidebars {

	/**
	 * Returns the singleton object.
	 *
	 * @since  2.0
	 */
	public static function instance() {
		static $Inst = null;

		if ( null === $Inst ) {
			$Inst = new CustomSidebarsWidgets();
		}

		return $Inst;
	}

	/**
	 * Constructor is private -> singleton.
	 *
	 * @since  2.0
	 */
	private function __construct() {
		if ( is_admin() ) {
			add_action(
				'widgets_admin_page',
				array( $this, 'widget_sidebar_content' )
			);
		}
	}

	/**
	 * Adds the additional HTML code to the widgets section.
	 */
	public function widget_sidebar_content() {
		include CSB_VIEWS_DIR . 'widgets.php';
	}

};