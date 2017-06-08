<?php

class us_migration_3_0 extends US_Migration_Translator {



	// Options
	public function translate_theme_options( &$options ) {
		$changed = FALSE;

		if ( isset( $options['color_header_bg'] ) ) {
			$options['color_header_middle_bg'] = $options['color_header_bg'];
			unset( $options['color_header_bg'] );
			$changed = TRUE;
		}

		if ( isset( $options['color_header_text'] ) ) {
			$options['color_header_middle_text'] = $options['color_header_text'];
			unset( $options['color_header_text'] );
			$changed = TRUE;
		}

		if ( isset( $options['color_header_text_hover'] ) ) {
			$options['color_header_middle_text_hover'] = $options['color_header_text_hover'];
			unset( $options['color_header_text_hover'] );
			$changed = TRUE;
		}

		if ( isset( $options['color_header_ext_bg'] ) ) {
			$options['color_header_top_bg'] = $options['color_header_ext_bg'];
			$options['color_header_bottom_bg'] = $options['color_header_ext_bg'];
			unset( $options['color_header_ext_bg'] );
			$changed = TRUE;
		}

		if ( isset( $options['color_header_ext_text'] ) ) {
			$options['color_header_top_text'] = $options['color_header_ext_text'];
			$options['color_header_bottom_text'] = $options['color_header_ext_text'];
			unset( $options['color_header_ext_text'] );
			$changed = TRUE;
		}

		if ( isset( $options['color_header_ext_text_hover'] ) ) {
			$options['color_header_top_text_hover'] = $options['color_header_ext_text_hover'];
			$options['color_header_bottom_text_hover'] = $options['color_header_ext_text_hover'];
			unset( $options['color_header_ext_text_hover'] );
			$changed = TRUE;
		}

		if ( isset( $options['header_layout'] ) ) {
			$new_value = null;
			switch ($options['header_layout'] ) {
				case 'standard': $new_value = 'simple_1';
					break;
				case 'extended': $new_value = 'extended_1';
					break;
				case 'advanced': $new_value = 'extended_2';
					break;
				case 'centered': $new_value = 'centered_1';
					break;
				case 'sided': $new_value = 'vertical_1';
					break;
			}
			if ($new_value) {
				$options['header_layout'] = $new_value;
				$changed = TRUE;
			}
		}

		if ( isset( $options['header_sticky'] ) AND $options['header_sticky'] ) {
			$options['header_sticky'] = array( 'default' );
			if ( isset( $options['header_sticky_disable_width'] ) ) {
				if ( $options['header_sticky_disable_width'] < 901 ) {
					$options['header_sticky'][] = 'tablets';
				}
				if ( $options['header_sticky_disable_width'] < 601 ) {
					$options['header_sticky'][] = 'mobiles';
				}
			}
			$changed = TRUE;
		}

		if ( isset( $options['header_main_height'] ) ) {
			$options['header_middle_height'] = $options['header_main_height'];
			unset( $options['header_main_height'] );
			$changed = TRUE;
		}

		if ( isset( $options['header_main_sticky_height_1'] ) AND ( $options['header_layout'] == 'simple_1' OR $options['header_layout'] == 'extended_1') ) {
			$options['header_middle_sticky_height'] = $options['header_main_sticky_height_1'];
			unset( $options['header_main_sticky_height_1'] );
			$changed = TRUE;
		}

		if ( isset( $options['header_main_sticky_height_2'] ) AND ( $options['header_layout'] == 'extended_2' OR $options['header_layout'] == 'centered_1') ) {
			$options['header_middle_sticky_height'] = $options['header_main_sticky_height_2'];
			unset( $options['header_main_sticky_height_2'] );
			$changed = TRUE;
		}

		if ( isset( $options['header_extra_height'] ) ) {
			$options['header_top_height'] = $options['header_extra_height'];
			$options['header_bottom_height'] = $options['header_extra_height'];
			unset( $options['header_extra_height'] );
			$changed = TRUE;
		}

		if ( isset( $options['header_extra_sticky_height_1'] ) ) {
			$options['header_top_sticky_height'] = $options['header_extra_sticky_height_1'];
			unset( $options['header_extra_sticky_height_1'] );
			$changed = TRUE;
		}

		if ( isset( $options['header_extra_sticky_height_2'] ) ) {
			$options['header_bottom_sticky_height'] = $options['header_extra_sticky_height_2'];
			unset( $options['header_extra_sticky_height_2'] );
			$changed = TRUE;
		}

		if ( isset( $options['header_layout'] ) AND $options['header_layout'] == 'vertical_1' AND isset( $options['logo_width'] ) AND isset( $options['logo_image'] ) ) {
			$img = usof_get_image_src( $options['logo_image'] );
			if ( $img AND ( ! empty( $img[1] ) AND ! empty( $img[2] ) ) ) {
				$logo_height = round( $options['logo_width'] / $img[1] * $img[2] );
				$options['logo_height'] = intval( $logo_height );

			}
			$changed = TRUE;
		}

		if ( isset( $options['page_comments'] ) AND $options['page_comments'] == 0 ) {
			$all_pages = get_posts( array(
				'posts_per_page' => -1,
				'post_type' => 'page',
				'numberposts' => -1,
			) );
			foreach ( $all_pages as $_page ) {
				wp_update_post( array(
					'ID' => $_page->ID,
					'comment_status' => 'closed',
				) );
			}
		}

		return $changed;
	}

	public function translate_meta( &$meta, $post_type ) {
		$changed = FALSE;

		$translate_meta_for = array(
			'post',
			'page',
			'us_portfolio',
			'forum',
			'topic',
		);

		if ( ! in_array( $post_type, $translate_meta_for ) ) {
			return $changed;
		}

		if ( isset( $meta['us_header_show'][0] ) AND $meta['us_header_show'][0] == "onscroll" ) {
			$meta['us_header_show_onscroll'][0] = 1;
			unset( $meta['us_header_show'] );
			$changed = TRUE;
		}

		return $changed;

	}
}
