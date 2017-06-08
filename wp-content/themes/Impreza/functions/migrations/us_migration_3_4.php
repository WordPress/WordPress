<?php

class us_migration_3_4 extends US_Migration_Translator {

	// Options
	public function translate_theme_options( &$options ) {
		$changed = FALSE;

		global $wp_registered_sidebars;
		$registered_sidebars_count = count( $wp_registered_sidebars );
		// $registered_sidebars_count = 5;

		$widget_areas = get_option( 'us_widget_areas' );
		if ( empty( $widget_areas ) ) {
			$widget_areas = array();
		}
		if ( class_exists( 'woocommerce' ) ) {
			$registered_sidebars_count ++;
			$widget_areas = array_merge( $widget_areas, array( 'shop_sidebar' => 'Shop Sidebar' ) );
			update_option( 'us_widget_areas', $widget_areas );

			$options['shop_sidebar_id'] = 'shop_sidebar';
			$options['product_sidebar_id'] = 'shop_sidebar';

			$changed = TRUE;
		}

		if ( class_exists( 'bbPress' ) ) {
			$registered_sidebars_count ++;
			$widget_areas = array_merge( $widget_areas, array( 'bbpress_sidebar' => 'bbPress Sidebar' ) );
			update_option( 'us_widget_areas', $widget_areas );

			$options['forum_sidebar_id'] = 'bbpress_sidebar';
			$changed = TRUE;
		}

		$old_sbg_sidebars = get_option( 'sbg_sidebars' );

		if ( is_array( $old_sbg_sidebars ) ) {
			foreach ( $old_sbg_sidebars as $sidebar ) {
				$registered_sidebars_count ++;

				$widget_areas = array_merge( $widget_areas, array( "sidebar-$registered_sidebars_count" => $sidebar ) );
			}
			update_option( 'us_widget_areas', $widget_areas );
			update_option( 'old_sbg_sidebars', $old_sbg_sidebars );
			update_option( 'sbg_sidebars', '' );
		}

		return $changed;
	}

	public function translate_meta( &$meta, $post_type ) {
		$changed = FALSE;

		$translate_meta_for = array(
			'post',
			'page',
			'us_portfolio',
		);

		if ( ! in_array( $post_type, $translate_meta_for ) ) {
			return $changed;
		}

		$sidebars_name_replacement = array(
			'Footer Column 1' => 'Footer First Widget',
			'Footer Column 2' => 'Footer Second Widget',
			'Footer Column 3' => 'Footer Third Widget',
			'Footer Column 4' => 'Footer Fourth Widget',
		);

		if ( ! empty( $meta['sbg_selected_sidebar_replacement'][0] ) ) {
			global $wp_registered_sidebars;
			if ( is_array( $wp_registered_sidebars ) && ! empty( $wp_registered_sidebars ) ) {
				foreach ( $wp_registered_sidebars as $sidebar ) {
					if ( isset( $sidebars_name_replacement[ $sidebar['name'] ] ) ) {
						$sidebar['name'] = $sidebars_name_replacement[ $sidebar['name'] ];
					}
					if ( $meta['sbg_selected_sidebar_replacement'][0] == $sidebar['name'] OR strpos( $meta['sbg_selected_sidebar_replacement'][0], '"' . $sidebar['name'] . '"' ) !== FALSE ) {
						$meta['us_sidebar_id'] = array( 0 => $sidebar['id'] );
						$changed = TRUE;
					}
				}
			}

			if ( ! $changed ) {
				$widget_areas = get_option( 'us_widget_areas' );
				if ( is_array( $widget_areas ) ) {
					foreach ( $widget_areas as $id => $sidebar ) {
						if ( isset( $sidebars_name_replacement[ $sidebar ] ) ) {
							$sidebar = $sidebars_name_replacement[ $sidebar ];
						}
						if ( $meta['sbg_selected_sidebar_replacement'][0] == $sidebar OR strpos( $meta['sbg_selected_sidebar_replacement'][0], '"' . $sidebar . '"' ) !== FALSE ) {
							$meta['us_sidebar_id'] = array( 0 => $id );
							$changed = TRUE;
						}
					}
				}
			}
		}

		return $changed;
	}
}
