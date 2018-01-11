<?php

/**
 * Settings class for media language and translation management
 *
 * @since 1.8
 */
class PLL_Settings_Media extends PLL_Settings_Module {

	/**
	 * constructor
	 *
	 * @since 1.8
	 *
	 * @param object $polylang polylang object
	 */
	public function __construct( &$polylang ) {
		parent::__construct( $polylang, array(
			'module'        => 'media',
			'title'         => __( 'Media' ),
			'description'   => __( 'Activate languages and translations for media', 'polylang' ),
			'active_option' => 'media_support',
		) );
	}
}
