<?php

/**
 * Settings class for tools
 *
 * @since 1.8
 */
class PLL_Settings_Tools extends PLL_Settings_Module {

	/**
	 * constructor
	 *
	 * @since 1.8
	 *
	 * @param object $polylang polylang object
	 */
	public function __construct( &$polylang ) {
		parent::__construct( $polylang, array(
			'module'        => 'tools',
			'title'         => __( 'Tools', 'polylang' ),
			'description'   => __( 'Decide whether to remove all data when deleting Polylang.', 'polylang' ),
		) );
	}

	/**
	 * displays the settings form
	 *
	 * @since 1.8
	 */
	protected function form() {
		printf(
			'<label for="uninstall"><input id="uninstall" name="uninstall" type="checkbox" value="1" %s /> %s</label>',
			empty( $this->options['uninstall'] ) ? '' : 'checked="checked"',
			esc_html__( 'Remove all Polylang data when using the "Delete" link on the plugins screen.', 'polylang' )
		);
	}

	/**
	 * sanitizes the settings before saving
	 *
	 * @since 1.8
	 *
	 * @param array $options
	 */
	protected function update( $options ) {
		$newoptions['uninstall'] = isset( $options['uninstall'] ) ? 1 : 0;
		return $newoptions; // take care to return only validated options
	}
}
