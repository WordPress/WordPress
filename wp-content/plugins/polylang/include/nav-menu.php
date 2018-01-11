<?php

/**
 * manages custom menus translations
 * common to admin and frontend for the customizer
 *
 * @since 1.7.7
 */
class PLL_Nav_Menu {
	public $model, $options;

	/**
	 * constructor: setups filters and actions
	 *
	 * @since 1.7.7
	 *
	 * @param object $polylang
	 */
	public function __construct( &$polylang ) {
		$this->model = &$polylang->model;
		$this->options = &$polylang->options;

		// integration with WP customizer
		add_action( 'customize_register', array( $this, 'create_nav_menu_locations' ), 5 );
	}

	/**
	 * create temporary nav menu locations ( one per location and per language ) for all non-default language
	 * to do only one time
	 *
	 * @since 1.2
	 */
	public function create_nav_menu_locations() {
		static $once;
		global $_wp_registered_nav_menus;

		if ( isset( $_wp_registered_nav_menus ) && ! $once ) {
			foreach ( $_wp_registered_nav_menus as $loc => $name ) {
				foreach ( $this->model->get_languages_list() as $lang ) {
					$arr[ $this->combine_location( $loc, $lang ) ] = $name . ' ' . $lang->name;
				}
			}

			$_wp_registered_nav_menus = $arr;
			$once = true;
		}
	}

	/**
	 * creates a temporary nav menu location from a location and a language
	 *
	 * @since 1.8
	 *
	 * @param string $loc nav menu location
	 * @param object $lang
	 * @return string
	 */
	public function combine_location( $loc, $lang ) {
		return $loc . ( strpos( $loc, '___' ) || $this->options['default_lang'] === $lang->slug ? '' : '___' . $lang->slug );
	}

	/**
	 * get nav menu locations and language from a temporary location
	 *
	 * @since 1.8
	 *
	 * @param string $loc temporary location
	 * @return array
	 *   'location' => nav menu location
	 *   'lang'     => language slug
	 */
	public function explode_location( $loc ) {
		$infos = explode( '___', $loc );
		if ( 1 == count( $infos ) ) {
			$infos[] = $this->options['default_lang'];
		}
		return array_combine( array( 'location', 'lang' ), $infos );
	}
}
