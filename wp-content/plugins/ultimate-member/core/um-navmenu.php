<?php

if ( ! class_exists( 'UM_Menu_Item_Custom_Fields' ) ) :

	class UM_Menu_Item_Custom_Fields {

		public static function load() {
			if ( um_get_option('disable_menu') == 0 ) {
				add_filter( 'wp_edit_nav_menu_walker', array( __CLASS__, '_filter_walker' ), 200 );
			}
		}

		public static function _filter_walker( $walker ) {
			$walker = 'UM_Menu_Item_Custom_Fields_Walker';
			if ( ! class_exists( $walker ) ) {
				require_once dirname( __FILE__ ) . '/um-navmenu-walker.php';
			}

			return $walker;
		}
	}
	add_action( 'wp_loaded', array( 'UM_Menu_Item_Custom_Fields', 'load' ), 9 );
endif;

require_once dirname( __FILE__ ) . '/um-navmenu-walker-edit.php';