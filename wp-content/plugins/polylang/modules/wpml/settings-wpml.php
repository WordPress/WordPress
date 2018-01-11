<?php

/**
 * A class to inform about the WPML compatibility module in Polylang settings
 *
 * @since 1.8
 */
class PLL_Settings_WPML extends PLL_Settings_Module {

	/**
	 * constructor
	 *
	 * @since 1.8
	 *
	 * @param object $polylang polylang object
	 */
	public function __construct( &$polylang ) {
		parent::__construct( $polylang, array(
			'module'      => 'wpml',
			'title'       => __( 'WPML Compatibility', 'polylang' ),
			'description' => __( 'WPML compatibility mode of Polylang', 'polylang' ),
		) );
	}

	/**
	 * tells if the module is active
	 *
	 * @since 1.8
	 *
	 * @return bool
	 */
	public function is_active() {
		return ! defined( 'PLL_WPML_COMPAT' ) || PLL_WPML_COMPAT;
	}
}
