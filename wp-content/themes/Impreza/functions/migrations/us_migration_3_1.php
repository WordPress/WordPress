<?php

class us_migration_3_1 extends US_Migration_Translator {

	// Options
	public function translate_theme_options( &$options ) {
		$changed = FALSE;

		if ( isset( $options['color_content_primary'] ) ) {
			$options['color_content_link'] = $options['color_content_primary'];

			$changed = TRUE;
		}

		if ( isset( $options['color_content_secondary'] ) ) {
			$options['color_content_link_hover'] = $options['color_content_secondary'];

			$changed = TRUE;
		}

		if ( isset( $options['color_alt_content_primary'] ) ) {
			$options['color_alt_content_link'] = $options['color_alt_content_primary'];

			$changed = TRUE;
		}

		if ( isset( $options['color_alt_content_secondary'] ) ) {
			$options['color_alt_content_link_hover'] = $options['color_alt_content_secondary'];

			$changed = TRUE;
		}

		return $changed;

	}

	// Content
	public function translate_content( &$content ) {
		return $this->_translate_content( $content );
	}

	public function translate_us_btn( &$name, &$params, &$content ) {
		$changed = FALSE;

		if ( ! empty( $params['size'] ) ) {
			if ( $params['size'] == 'small' ) {
				$params['size'] = '13px';

				$changed = TRUE;
			} elseif ( $params['size'] == 'large' ) {
				$params['size'] = '18px';

				$changed = TRUE;
			}
		}

		return $changed;

	}

	public function translate_us_cta( &$name, &$params, &$content ) {
		$changed = FALSE;

		if ( ! empty( $params['btn_size'] ) ) {
			if ( $params['btn_size'] == 'small' ) {
				$params['btn_size'] = '13px';

				$changed = TRUE;
			} elseif ( $params['btn_size'] == 'large' ) {
				$params['btn_size'] = '18px';

				$changed = TRUE;
			}
		}

		if ( ! empty( $params['btn2_size'] ) ) {
			if ( $params['btn2_size'] == 'small' ) {
				$params['btn2_size'] = '13px';

				$changed = TRUE;
			} elseif ( $params['btn2_size'] == 'large' ) {
				$params['btn2_size'] = '18px';

				$changed = TRUE;
			}
		}

		return $changed;

	}

	public function translate_us_pricing( &$name, &$params, &$content ) {
		$changed = FALSE;

		if ( ! empty( $params['items'] ) ) {
			$items = json_decode( urldecode( $params['items'] ), TRUE );
			if ( is_array( $items ) ) {
				foreach ( $items as $index => $item ) {
					if ( ! empty( $item['btn_size'] ) ) {
						if ( $item['btn_size'] == 'small' ) {
							$items[$index]['btn_size'] = '13px';

							$changed = TRUE;
						} elseif ( $item['btn_size'] == 'large' ) {
							$items[$index]['btn_size'] = '18px';

							$changed = TRUE;
						}
					}
				}

				if ( $changed ) {
					$params['items'] = urlencode( json_encode( $items ) );
				}
			}
		}

		return $changed;

	}

	public function translate_us_social_links( &$name, &$params, &$content ) {
		$changed = FALSE;

		if ( ! empty( $params['size'] ) ) {
			if ( $params['size'] == 'small' ) {
				$params['size'] = '17px';

				$changed = TRUE;
			} elseif ( $params['size'] == 'medium' ) {
				$params['size'] = '20px';

				$changed = TRUE;
			} elseif ( $params['size'] == 'large' ) {
				$params['size'] = '24px';

				$changed = TRUE;
			}
		} else {
			$params['size'] = '17px';

			$changed = TRUE;
		}

		return $changed;

	}

	public function translate_widgets( &$name, &$instance ) {
		if ( $name == 'us_socials' ) {
			if ( isset( $instance['size'] ) ) {
				if ( $instance['size'] == 'small ' ) {
					$instance['size'] = '17px';
				} elseif ( $instance['size'] == 'medium' ) {
					$instance['size'] = '20px';
				} elseif ( $instance['size'] == 'large' ) {
					$instance['size'] = '24px';
				}

				return TRUE;
			}
		}
	}

}
