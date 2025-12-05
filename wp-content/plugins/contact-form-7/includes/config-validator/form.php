<?php

trait WPCF7_ConfigValidator_Form {

	/**
	 * Runs error detection for the form section.
	 */
	public function validate_form() {
		$section = 'form.body';
		$form = $this->contact_form->prop( 'form' );

		if ( $this->supports( 'multiple_controls_in_label' ) ) {
			if ( $this->detect_multiple_controls_in_label( $section, $form ) ) {
				$this->add_error( $section, 'multiple_controls_in_label',
					array(
						'message' => __( 'Multiple form controls are in a single label element.', 'contact-form-7' ),
					)
				);
			} else {
				$this->remove_error( $section, 'multiple_controls_in_label' );
			}
		}

		if ( $this->supports( 'unavailable_names' ) ) {
			$ng_names = $this->detect_unavailable_names( $section, $form );

			if ( $ng_names ) {
				$this->add_error( $section, 'unavailable_names',
					array(
						'message' =>
							/* translators: %names%: a list of form control names */
							__( 'Unavailable names (%names%) are used for form controls.', 'contact-form-7' ),
						'params' => array( 'names' => implode( ', ', $ng_names ) ),
					)
				);
			} else {
				$this->remove_error( $section, 'unavailable_names' );
			}
		}

		if ( $this->supports( 'unavailable_html_elements' ) ) {
			if ( $this->detect_unavailable_html_elements( $section, $form ) ) {
				$this->add_error( $section, 'unavailable_html_elements',
					array(
						'message' => __( 'Unavailable HTML elements are used in the form template.', 'contact-form-7' ),
					)
				);
			} else {
				$this->remove_error( $section, 'unavailable_html_elements' );
			}
		}

		if ( $this->supports( 'dots_in_names' ) ) {
			if ( $this->detect_dots_in_names( $section, $form ) ) {
				$this->add_error( $section, 'dots_in_names',
					array(
						'message' => __( 'Dots are used in form-tag names.', 'contact-form-7' ),
					)
				);
			} else {
				$this->remove_error( $section, 'dots_in_names' );
			}
		}

		if ( $this->supports( 'colons_in_names' ) ) {
			if ( $this->detect_colons_in_names( $section, $form ) ) {
				$this->add_error( $section, 'colons_in_names',
					array(
						'message' => __( 'Colons are used in form-tag names.', 'contact-form-7' ),
					)
				);
			} else {
				$this->remove_error( $section, 'colons_in_names' );
			}
		}

		if ( $this->supports( 'upload_filesize_overlimit' ) ) {
			if ( $this->detect_upload_filesize_overlimit( $section, $form ) ) {
				$this->add_error( $section, 'upload_filesize_overlimit',
					array(
						'message' => __( 'Uploadable file size exceeds PHP&#8217;s maximum acceptable size.', 'contact-form-7' ),
					)
				);
			} else {
				$this->remove_error( $section, 'upload_filesize_overlimit' );
			}
		}
	}


	/**
	 * Detects errors of multiple form controls in a single label.
	 *
	 * @link https://contactform7.com/configuration-errors/multiple-controls-in-label/
	 */
	public function detect_multiple_controls_in_label( $section, $content ) {
		$pattern = '%<label(?:[ \t\n]+.*?)?>(.+?)</label>%s';

		if ( preg_match_all( $pattern, $content, $matches ) ) {
			$form_tags_manager = WPCF7_FormTagsManager::get_instance();

			foreach ( $matches[1] as $insidelabel ) {
				$tags = $form_tags_manager->scan( $insidelabel );
				$fields_count = 0;

				foreach ( $tags as $tag ) {
					$is_multiple_controls_container = wpcf7_form_tag_supports(
						$tag->type, 'multiple-controls-container'
					);

					$is_zero_controls_container = wpcf7_form_tag_supports(
						$tag->type, 'zero-controls-container'
					);

					if ( $is_multiple_controls_container ) {
						$fields_count += count( $tag->values );

						if ( $tag->has_option( 'free_text' ) ) {
							$fields_count += 1;
						}
					} elseif ( $is_zero_controls_container ) {
						$fields_count += 0;
					} elseif ( ! empty( $tag->name ) ) {
						$fields_count += 1;
					}

					if ( 1 < $fields_count ) {
						return true;
					}
				}
			}
		}

		return false;
	}


	/**
	 * Detects errors of unavailable form-tag names.
	 *
	 * @link https://contactform7.com/configuration-errors/unavailable-names/
	 */
	public function detect_unavailable_names( $section, $content ) {
		$public_query_vars = array( 'm', 'p', 'posts', 'w', 'cat',
			'withcomments', 'withoutcomments', 's', 'search', 'exact', 'sentence',
			'calendar', 'page', 'paged', 'more', 'tb', 'pb', 'author', 'order',
			'orderby', 'year', 'monthnum', 'day', 'hour', 'minute', 'second',
			'name', 'category_name', 'tag', 'feed', 'author_name', 'static',
			'pagename', 'page_id', 'error', 'attachment', 'attachment_id',
			'subpost', 'subpost_id', 'preview', 'robots', 'taxonomy', 'term',
			'cpage', 'post_type', 'embed',
		);

		$form_tags_manager = WPCF7_FormTagsManager::get_instance();

		$ng_named_tags = $form_tags_manager->filter( $content, array(
			'name' => $public_query_vars,
		) );

		$ng_names = array();

		foreach ( $ng_named_tags as $tag ) {
			$ng_names[] = sprintf( '"%s"', $tag->name );
		}

		if ( $ng_names ) {
			return array_unique( $ng_names );
		}

		return false;
	}


	/**
	 * Detects errors of unavailable HTML elements.
	 *
	 * @link https://contactform7.com/configuration-errors/unavailable-html-elements/
	 */
	public function detect_unavailable_html_elements( $section, $content ) {
		$pattern = '%(?:<form[\s\t>]|</form>)%i';

		if ( preg_match( $pattern, $content ) ) {
			return true;
		}

		return false;
	}


	/**
	 * Detects errors of dots in form-tag names.
	 *
	 * @link https://contactform7.com/configuration-errors/dots-in-names/
	 */
	public function detect_dots_in_names( $section, $content ) {
		$form_tags_manager = WPCF7_FormTagsManager::get_instance();

		$tags = $form_tags_manager->filter( $content, array(
			'feature' => 'name-attr',
		) );

		foreach ( $tags as $tag ) {
			if ( str_contains( $tag->raw_name, '.' ) ) {
				return true;
			}
		}

		return false;
	}


	/**
	 * Detects errors of colons in form-tag names.
	 *
	 * @link https://contactform7.com/configuration-errors/colons-in-names/
	 */
	public function detect_colons_in_names( $section, $content ) {
		$form_tags_manager = WPCF7_FormTagsManager::get_instance();

		$tags = $form_tags_manager->filter( $content, array(
			'feature' => 'name-attr',
		) );

		foreach ( $tags as $tag ) {
			if ( str_contains( $tag->raw_name, ':' ) ) {
				return true;
			}
		}

		return false;
	}


	/**
	 * Detects errors of uploadable file size overlimit.
	 *
	 * @link https://contactform7.com/configuration-errors/upload-filesize-overlimit
	 */
	public function detect_upload_filesize_overlimit( $section, $content ) {
		$upload_max_filesize = ini_get( 'upload_max_filesize' );

		if ( ! $upload_max_filesize ) {
			return false;
		}

		$upload_max_filesize = strtolower( $upload_max_filesize );
		$upload_max_filesize = trim( $upload_max_filesize );

		if ( ! preg_match( '/^(\d+)([kmg]?)$/', $upload_max_filesize, $matches ) ) {
			return false;
		}

		if ( 'k' === $matches[2] ) {
			$upload_max_filesize = (int) $matches[1] * KB_IN_BYTES;
		} elseif ( 'm' === $matches[2] ) {
			$upload_max_filesize = (int) $matches[1] * MB_IN_BYTES;
		} elseif ( 'g' === $matches[2] ) {
			$upload_max_filesize = (int) $matches[1] * GB_IN_BYTES;
		} else {
			$upload_max_filesize = (int) $matches[1];
		}

		$form_tags_manager = WPCF7_FormTagsManager::get_instance();

		$tags = $form_tags_manager->filter( $content, array(
			'basetype' => 'file',
		) );

		foreach ( $tags as $tag ) {
			if ( $upload_max_filesize < $tag->get_limit_option() ) {
				return true;
			}
		}

		return false;
	}

}
