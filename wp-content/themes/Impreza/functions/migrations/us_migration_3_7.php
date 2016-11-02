<?php

class us_migration_3_7 extends US_Migration_Translator {

	// Content
	public function translate_content( &$content ) {
		return $this->_translate_content( $content );
	}

	public function translate_vc_row( &$name, &$params, &$content ) {
		$changed = FALSE;

		if ( ! empty( $params['us_bg_video'] ) AND $params['us_bg_video'] == 1) {
			if ( isset( $params['video_mp4'] ) AND $params['video_mp4'] != '' ) {
				$params['us_bg_video'] = $params['video_mp4'];
			} elseif ( isset( $params['video_webm'] ) AND $params['video_webm'] != '' ) {
				$params['us_bg_video'] = $params['video_webm'];
			} elseif ( isset( $params['video_ogg'] ) AND $params['video_ogg'] != '' ) {
				$params['us_bg_video'] = $params['video_ogg'];
			} else {
				unset( $params['us_bg_video'] );
			}

			unset( $params['video_mp4'] );
			unset( $params['video_webm'] );
			unset( $params['video_ogg'] );

			$changed = TRUE;
		}

		return $changed;
	}

	// Options
	public function translate_theme_options( &$options ) {
		$changed = FALSE;

		if ( isset( $options['rounded_corners'] ) AND $options['rounded_corners'] == FALSE ) {
			$options['button_border_radius'] = 0;

			$changed = TRUE;
		}

		return $changed;
	}

	// Meta
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

		if ( isset( $meta['us_header_show_onscroll'][0] ) AND $meta['us_header_show_onscroll'][0] == TRUE ) {
			$meta['us_header_sticky_pos'][0] = 'above';
			$changed = TRUE;
		}

		return $changed;
	}
}
