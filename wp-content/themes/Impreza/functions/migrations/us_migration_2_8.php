<?php

class us_migration_2_8 extends US_Migration_Translator {

	// Content
	public function translate_content( &$content ) {
		return $this->_translate_content( $content );
	}

	public function translate_vc_row( &$name, &$params, &$content ) {
		global $us_migration_current_row_is_width;

		if ( ! empty( $params['width'] ) AND $params['width'] == 'full' ) {
			$us_migration_current_row_is_width = TRUE;
		} else {
			$us_migration_current_row_is_width = FALSE;
		}
	}

	public function translate_us_blog( &$name, &$params, &$content ) {
		global $us_migration_current_post_id, $us_migration_current_row_is_width;
		$changed = FALSE;

		if ( empty( $params['layout'] ) OR $params['layout'] == 'large' ) {
			$params['layout'] = 'classic';
			$params['columns'] = 1;
			$changed = TRUE;
		}

		if ( in_array( $params['layout'], array( 'smallcircle', 'smallsquare', 'compact' ) ) ) {
			$params['columns'] = 1;
			$changed = TRUE;
		}

		if ( in_array( $params['layout'], array( 'grid', 'masonry' ) ) ) {
			// Get sidebar info
			$post_type = get_post_type( $us_migration_current_post_id );

			if ( $post_type == 'post' ) {
				// Posts and attachments
				$sidebar_pos = us_get_option( 'post_sidebar', 'right' );
			} elseif ( $post_type == 'us_portfolio' ) {
				// Portfolio item
				$sidebar_pos = us_get_option( 'portfolio_sidebar', 'none' );
			} elseif ( $post_type == 'page' ) {
				// Page
				$sidebar_pos = us_get_option( 'page_sidebar', 'none' );
			} elseif ( $post_type == 'product' ) {
				// WooCommerce product
				$sidebar_pos = us_get_option( 'product_sidebar', 'right' );
			} else {
				$sidebar_pos = 'none';
			}

			if ( usof_meta( 'us_sidebar', array(), $us_migration_current_post_id ) != '' ) {
				$sidebar_pos = usof_meta( 'us_sidebar', array(), $us_migration_current_post_id );
			}

			// Some wrong value may came from various theme options, so filtering it
			if ( ! in_array( $sidebar_pos, array( 'right', 'left', 'none' ) ) ) {
				$sidebar_pos = 'right';
			}

			if ( $sidebar_pos == 'none' ) {
				$params['columns'] = 3;
				if ( $us_migration_current_row_is_width ) {
					$params['columns'] = 5;
				}
			}

			if ( $params['layout'] == 'grid' ) {
				$params['layout'] = 'classic';
			}

			$changed = TRUE;
		}

		return $changed;
	}

	// Options
	public function translate_theme_options( &$options ) {
		$changed = FALSE;

		// Blog Home Page
		if ( in_array( $options['blog_layout'], array( 'smallcircle', 'smallsquare', 'compact' ) ) ) {
			$options['blog_cols'] = 1;
			$changed = TRUE;
		}

		if ( $options['blog_layout'] == 'large' ) {
			$options['blog_layout'] = 'classic';
			$options['blog_cols'] = 1;
			$changed = TRUE;
		}

		if ( in_array( $options['blog_layout'], array( 'grid', 'masonry' ) ) ) {
			if ( $options['blog_sidebar'] == 'none' ) {
				$options['blog_cols'] = 3;
			} else {
				$options['blog_cols'] = 2;
			}
			if ( $options['blog_layout'] == 'grid' ) {
				$options['blog_layout'] = 'classic';
			}
			$changed = TRUE;
		}

		// Archive Page
		if ( in_array( $options['archive_layout'], array( 'smallcircle', 'smallsquare', 'compact' ) ) ) {
			$options['archive_cols'] = 1;
			$changed = TRUE;
		}

		if ( $options['archive_layout'] == 'large' ) {
			$options['archive_layout'] = 'classic';
			$options['archive_cols'] = 1;
			$changed = TRUE;
		}

		if ( in_array( $options['archive_layout'], array( 'grid', 'masonry' ) ) ) {
			if ( $options['archive_sidebar'] == 'none' ) {
				$options['archive_cols'] = 3;
			} else {
				$options['archive_cols'] = 2;
			}
			if ( $options['archive_layout'] == 'grid' ) {
				$options['archive_layout'] = 'classic';
			}
			$changed = TRUE;
		}

		// Search Page
		if ( in_array( $options['search_layout'], array( 'smallcircle', 'smallsquare', 'compact' ) ) ) {
			$options['search_cols'] = 1;
			$changed = TRUE;
		}

		if ( $options['search_layout'] == 'large' ) {
			$options['search_layout'] = 'classic';
			$options['search_cols'] = 1;
			$changed = TRUE;
		}

		if ( in_array( $options['search_layout'], array( 'grid', 'masonry' ) ) ) {
			if ( $options['search_sidebar'] == 'none' ) {
				$options['search_cols'] = 3;
			} else {
				$options['search_cols'] = 2;
			}
			if ( $options['search_layout'] == 'grid' ) {
				$options['search_layout'] = 'classic';
			}
			$changed = TRUE;
		}

		return $changed;
	}
}
