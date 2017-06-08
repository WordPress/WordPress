<?php

class us_migration_3_3 extends US_Migration_Translator {

	// Content
	public function translate_content( &$content ) {
		return $this->_translate_content( $content );
	}

	public function translate_us_blog( &$name, &$params, &$content ) {
		$changed = FALSE;

		if ( ! empty( $params['layout'] ) AND $params['layout'] == 'masonry' ) {
			$params['layout'] = 'flat';
			$params['masonry'] = 1;
			$changed = TRUE;
		}

		if ( ! empty( $params['layout'] ) AND $params['layout'] == 'cards' ) {
			$params['masonry'] = 1;
			$changed = TRUE;
		}

		return $changed;
	}

	// Options
	public function translate_theme_options( &$options ) {
		$changed = FALSE;

		if ( isset( $options['blog_layout'] ) AND $options['blog_layout'] == 'masonry' ) {
			$options['blog_layout'] = 'flat';
			$options['blog_masonry'] = 1;

			$changed = TRUE;
		}

		if ( isset( $options['blog_layout'] ) AND $options['blog_layout'] == 'cards' ) {
			$options['blog_masonry'] = 1;

			$changed = TRUE;
		}

		if ( isset( $options['archive_layout'] ) AND $options['archive_layout'] == 'masonry' ) {
			$options['archive_layout'] = 'flat';
			$options['archive_masonry'] = 1;

			$changed = TRUE;
		}

		if ( isset( $options['archive_layout'] ) AND $options['archive_layout'] == 'cards' ) {
			$options['archive_masonry'] = 1;

			$changed = TRUE;
		}

		if ( isset( $options['search_layout'] ) AND $options['search_layout'] == 'masonry' ) {
			$options['search_layout'] = 'flat';
			$options['search_masonry'] = 1;

			$changed = TRUE;
		}

		if ( isset( $options['search_layout'] ) AND $options['search_layout'] == 'cards' ) {
			$options['search_masonry'] = 1;

			$changed = TRUE;
		}

		return $changed;

	}

	public function translate_meta( &$meta, $post_type ) {
		$changed = FALSE;

		$translate_meta_for = array(
			'post',
			'page',
			'us_portfolio',
			'product',
		);

		if ( ! in_array( $post_type, $translate_meta_for ) ) {
			return $changed;
		}

		// Titlebar image overlay
		if ( isset( $meta['us_titlebar_overlay_color'][0] ) AND isset( $meta['us_titlebar_overlay_opacity'][0] ) AND ( $meta['us_titlebar_overlay_opacity'][0] > 0 ) ) {
			$meta['us_titlebar_overlay_color'][0] = $this->hex2rgba( $meta['us_titlebar_overlay_color'][0], $meta['us_titlebar_overlay_opacity'][0]/100 );
			$meta['us_titlebar_overlay_opacity'][0] = 0;
			$changed = TRUE;
		}

		// Portfolio lighbox
		if ( isset( $meta['us_lightbox'][0] ) AND $meta['us_lightbox'][0] == TRUE ) {
			$meta['us_tile_action'][0] = 'lightbox';
			$meta['us_lightbox'][0] = FALSE;
			$changed = TRUE;
		}

		// Portfolio custom link
		if ( isset( $meta['us_custom_link'][0] ) AND $meta['us_custom_link'][0] != '' ) {
			$meta['us_tile_action'][0] = 'link';

			$value = array(
				'url' => $meta['us_custom_link'][0],
				'target' => '',
			);

			if ( isset( $meta['us_custom_link_blank'][0] ) AND $meta['us_custom_link_blank'][0] == TRUE ) {
				$value['target'] = '_blank';
			}

			$meta['us_tile_link'][0] = json_encode( $value );

			$meta['us_custom_link'][0] = '';
			$meta['us_custom_link_blank'][0] = FALSE;

			$changed = TRUE;
		}

		return $changed;

	}

	/**
	 * Convert HEX value of color to RGBA format. Credits: http://mekshq.com/how-to-convert-hexadecimal-color-code-to-rgb-or-rgba-using-php/
	 * @param $color
	 * @param bool|FALSE $opacity
	 *
	 * @return string
	 */
	private function hex2rgba($color, $opacity = false) {

		$default = 'rgb(0,0,0)';

		//Return default if no color provided
		if(empty($color))
			return $default;

		//Sanitize $color if "#" is provided
		if ($color[0] == '#' ) {
			$color = substr( $color, 1 );
		}

		//Check if color has 6 or 3 characters and get values
		if (strlen($color) == 6) {
			$hex = array( $color[0] . $color[1], $color[2] . $color[3], $color[4] . $color[5] );
		} elseif ( strlen( $color ) == 3 ) {
			$hex = array( $color[0] . $color[0], $color[1] . $color[1], $color[2] . $color[2] );
		} else {
			return $default;
		}

		//Convert hexadec to rgb
		$rgb =  array_map('hexdec', $hex);

		//Check if opacity is set(rgba or rgb)
		if($opacity){
			if(abs($opacity) > 1)
				$opacity = 1.0;
			$output = 'rgba('.implode(",",$rgb).','.$opacity.')';
		} else {
			$output = 'rgb('.implode(",",$rgb).')';
		}

		//Return rgb(a) color string
		return $output;
	}
}
