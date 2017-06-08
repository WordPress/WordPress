<?php

class us_migration_2_0 extends US_Migration_Translator {

	/**
	 * @var bool Possibly dangerous translation that needs to be migrated manually (don't use this too often)
	 */
	public $should_be_manual = TRUE;

	public function migration_completed_message() {
		$output = '<div class="updated us-migration">';
		$output .= '<p><strong>Congratulations</strong>: Migration to Impreza ' . US_THEMEVERSION . ' is completed! Now please regenerate thumbnails and check your website once again. If you notice some issues, <a href="https://help.us-themes.com/impreza/installation/update20/" target="_blank">follow the manual</a>.</p>';
		$output .= '</div>';

		return $output;
	}

	public function translate_menus( &$locations ) {
		$rules = array(
			'impeza_main_menu' => 'us_main_menu',
			'impeza_footer_menu' => 'us_footer_menu',
		);

		return $this->_translate_menus( $locations, $rules );
	}

	// Content
	public function translate_content( &$content, $post_id = NULL ) {

		$content_changed = FALSE;

		if ( $post_id !== NULL ) {
			$rows_count = 0;
			$rows_indexes = array();
			$confirmed_rows_indexes = array();
			$shortcode_pattern = $this->get_shortcode_regex();
			if ( preg_match_all( '/' . $shortcode_pattern . '/s', $content, $matches ) ) {
				if ( count( $matches[2] ) ) {
					foreach ( $matches[2] as $i => $shortcode_name ) {
						if ( $shortcode_name == 'vc_row' ) {
							$shortcode_params_string = $matches[3][ $i ];
							if ( ! empty( $shortcode_params_string ) ) {
								$shortcode_params = shortcode_parse_atts( $shortcode_params_string );
							} else {
								$shortcode_params = array();
							}
							if ( empty($shortcode_params['section']) ) {
								$rows_count++;
								$rows_indexes[] = $i;
							} else {
								if ( $rows_count > 1 ) {
									$confirmed_rows_indexes[] = $rows_indexes;
								}
								$rows_count = 0;
								$rows_indexes = array();
							}

						} else {
							if ( $rows_count > 1 ) {
								$confirmed_rows_indexes[] = $rows_indexes;
							}
							$rows_count = 0;
							$rows_indexes = array();
						}
					}
					if ( $rows_count > 1 ) {
						$confirmed_rows_indexes[] = $rows_indexes;
					}

					if ( count( $confirmed_rows_indexes ) ) {
						foreach ( $confirmed_rows_indexes as $rows_indexes ) {
							$from_string = '';
							$to_string = '[vc_row][vc_column width="1/1"]';
							foreach ( $rows_indexes as $i ) {
								$shortcode_string = $matches[0][ $i ];
								$from_string .= $shortcode_string;
								$shortcode_string = str_replace(
									array( '[vc_row]', '[vc_column]', '[vc_row ', '[vc_column ', '[/vc_row]', '[/vc_column]' ),
									array( '[vc_row_inner]', '[vc_column_inner]', '[vc_row_inner ', '[vc_column_inner ', '[/vc_row_inner]', '[/vc_column_inner]' ),
									$shortcode_string );
								$to_string .= $shortcode_string;
							}
							$to_string .= '[/vc_column][/vc_row]';

							// preventing replace if there is an inner row in regular row
							if ( strpos ( $from_string, '[vc_row_inner'  ) ) {
								continue;
							}

							// Doing str_replace only once to avoid collisions
							$pos = strpos( $content, $from_string );
							if ( $pos !== FALSE ) {
								$content = substr_replace( $content, $to_string, $pos, strlen( $from_string ) );
								$content_changed = TRUE;
							}
						}
					}
				}
			}
		}

		$content_changed = ( $this->_translate_content( $content ) OR $content_changed ) ;

		if ( $post_id !== NULL ) {
			$meta = get_post_meta( $post_id );

			if ( isset( $meta['_wp_page_template'] ) AND isset( $meta['_wp_page_template'][0] ) AND ( $meta['_wp_page_template'][0] != 'default' ) ) {
				switch ( $meta['_wp_page_template'][0] ) {
					case 'page-grid_blog.php':
						$content .= '[vc_row][vc_column width="1/1"][us_blog layout="masonry" content_type="excerpt" columns="4" pagination="ajax"][/vc_column][/vc_row]';
						$content_changed = TRUE;
						break;
					case 'page-grid_blog_paginated.php':
						$content .= '[vc_row][vc_column width="1/1"][us_blog layout="masonry" content_type="excerpt" columns="4" pagination="regular"][/vc_column][/vc_row]';
						$content_changed = TRUE;
						break;
					case 'page-big_blog.php':
						$content .= '[vc_row][vc_column width="1/1"][us_blog layout="large" content_type="excerpt" columns="1" pagination="regular"][/vc_column][/vc_row]';
						$content_changed = TRUE;
						break;
					case 'page-small_blog.php':
						$content .= '[vc_row][vc_column width="1/1"][us_blog layout="smallcircle" content_type="excerpt" columns="1" pagination="regular"][/vc_column][/vc_row]';
						$content_changed = TRUE;
						break;
					case 'page-blank.php':
						if ( substr( $content, 0, 7 ) != '[vc_row' ) {
							$content = '[vc_row height="full" valign="center"][vc_column width="1/1"]' . $content . '[/vc_column][/vc_row]';
							$content_changed = TRUE;
						} elseif ( preg_match( '@^\[vc_row([^\]]+?\])@u', $content, $matches ) ) {
							$row_html = '[vc_row height="full" valign="center"' . preg_replace( '@ (height|valign)=\"[^\"]+?\"@u', '', $matches[1] );
							$pos = strpos( $content, $matches[0] );
							if ( $pos !== FALSE ) {
								$content = substr_replace( $content, $row_html, $pos, strlen( $matches[0] ) );
								$content_changed = TRUE;
							}
						}

						break;
				}
			}
		}



		return $content_changed;
	}

	// Options
	public function translate_theme_options( &$options ) {

		foreach ( range( 2, 10 ) as $number ) {
			if ( isset( $options[ 'header_language_' . $number . '_url' ] ) ) {
				$options[ 'header_language_' . $number . '_url' ] = str_replace( '[site_url]', site_url(), $options[ 'header_language_' . $number . '_url' ] );
			}
		}

		if ( isset( $options['blog_layout'] ) AND $options['blog_layout'] == 'Masonry Grid with ajax load' ) {
			$options['blog_pagination'] = 'ajax';
		}

		$rules = array(
			'custom_logo' => array(
				'new_name' => 'logo_image',
			),
			'custom_logo_transparent' => array(
				'new_name' => 'logo_image_transparent',
			),
			'logo_as_text' => array(
				'new_name' => 'logo_type',
				'values' => array(
					TRUE => 'text',
					FALSE => 'img',
				),
			),
			'header_layout_type' => array(
				'new_name' => 'titlebar_size',
				'values' => array(
					'Ultra Compact' => 'small',
					'Compact' => 'medium',
					'Large' => 'large',
					'Huge' => 'huge',
				),
			),
			'titlebar_color_style' => array(
				'new_name' => 'titlebar_color',
				'values' => array(
					'Content bg | Content text' => 'default',
					'Alternate bg | Content text' => 'alternate',
					'Primary bg | White text' => 'primary',
					'Secondary bg | White text' => 'secondary',
				),
			),
			'custom_favicon' => array(
				'new_name' => 'favicon',
			),
			'tracking_code' => array(
				'new_name' => 'custom_html',
			),
			'blog_excerpt_length' => array(
				'new_name' => 'excerpt_length',
			),
			'boxed_layout' => array(
				'new_name' => 'canvas_layout',
				'values' => array(
					TRUE => 'boxed',
					FALSE => 'wide',
				),
			),
			'body_bg' => array(
				'new_name' => 'color_body_bg',
			),
			'body_background_image' => array(
				'new_name' => 'body_bg_image',
			),
			'body_background_image_repeat' => array(
				'new_name' => 'body_bg_image_repeat',
				'values' => array(
					'Repeat' => 'repeat',
					'Repeat Horizontally' => 'repeat-x',
					'Repeat Vertically' => 'repeat-y',
					'Do Not Repeat' => 'no-repeat',
				),
			),
			'body_background_image_position' => array(
				'new_name' => 'body_bg_image_position',
				'values' => array(
					'Top Center' => 'top center',
					'Top Left' => 'top left',
					'Bottom Center' => 'bottom center',
					'Bottom Left' => 'bottom left',
					'Bottom Right' => 'bottom right',
					'Center Center' => 'center center',
					'Center Left' => 'center left',
					'Center Right' => 'center right',
				),
			),
			'body_background_image_attachment_fixed' => array(
				'new_name' => 'body_bg_image_attachment',
				'values' => array(
					TRUE => 'fixed',
					FALSE => 'scroll',
				),
			),
			'body_background_image_stretch' => array(
				'new_name' => 'body_bg_image_size',
				'values' => array(
					TRUE => 'cover',
					FALSE => 'initial',
				),
			),
			'disable_animation_width' => array(
				'new_name' => 'disable_effects_width',
			),
			'color_scheme' => array(
				'new_name' => 'color_style',
				'values' => array(
					'White Pink' => '1',
					'White Blue' => '2',
					'Ectoplasm' => '11',
					'Midnight Red' => '12',
					'Stylish Cyan' => '13',
					'Light Ocean' => '14',
					'Coffee Shop' => '15',
					'Bright Sunrise' => '16',
					'Grey Turquoise' => '18',
					'Twilight' => '17',
					'White Alizarin' => '3',
					'White Royal' => '4',
					'White Green' => '6',
					'White Yellow' => '7',
					'Black & White' => '5',
					'Retro Package' => '8',
					'Nautical Knot' => '9',
					'Mild Ocean' => '10',
					'City Hunter' => '19',
					'Dark Cyan' => '20',
				),
			),
			'header_bg' => array(
				'new_name' => 'color_header_bg',
			),
			'header_text' => array(
				'new_name' => 'color_header_text',
			),
			'header_text_hover' => array(
				'new_name' => 'color_header_text_hover',
			),
			'header_ext_bg' => array(
				'new_name' => 'color_header_ext_bg',
			),
			'header_ext_text' => array(
				'new_name' => 'color_header_ext_text',
			),
			'header_ext_text_hover' => array(
				'new_name' => 'color_header_ext_text_hover',
			),
			'transparent_header_text' => array(
				'new_name' => 'color_header_transparent_text',
			),
			'transparent_header_text_hover' => array(
				'new_name' => 'color_header_transparent_text_hover',
			),
			'search_bg' => array(
				'new_name' => 'color_header_search_bg',
			),
			'search_text' => array(
				'new_name' => 'color_header_search_text',
			),
			'change_main_menu_colors' => array(
				'new_name' => 'change_menu_colors',
			),
			'transparent_menu_active_text' => array(
				'new_name' => 'color_menu_transparent_active_text',
			),
			'menu_active_bg' => array(
				'new_name' => 'color_menu_active_bg',
			),
			'menu_active_text' => array(
				'new_name' => 'color_menu_active_text',
			),
			'menu_hover_bg' => array(
				'new_name' => 'color_menu_hover_bg',
			),
			'menu_hover_text' => array(
				'new_name' => 'color_menu_hover_text',
			),
			'drop_bg' => array(
				'new_name' => 'color_drop_bg',
			),
			'drop_text' => array(
				'new_name' => 'color_drop_text',
			),
			'drop_hover_bg' => array(
				'new_name' => 'color_drop_hover_bg',
			),
			'drop_hover_text' => array(
				'new_name' => 'color_drop_hover_text',
			),
			'drop_active_bg' => array(
				'new_name' => 'color_drop_active_bg',
			),
			'drop_active_text' => array(
				'new_name' => 'color_drop_active_text',
			),
			'menu_button_bg' => array(
				'new_name' => 'color_menu_button_bg',
			),
			'menu_button_text' => array(
				'new_name' => 'color_menu_button_text',
			),
			'menu_button_hover_bg' => array(
				'new_name' => 'color_menu_button_hover_bg',
			),
			'menu_button_hover_text' => array(
				'new_name' => 'color_menu_button_hover_text',
			),
			'change_main_content_colors' => array(
				'new_name' => 'change_content_colors',
			),
			'main_bg' => array(
				'new_name' => 'color_content_bg',
			),
			'main_bg_alternative' => array(
				'new_name' => 'color_content_bg_alt',
			),
			'main_border' => array(
				'new_name' => 'color_content_border',
			),
			'main_heading' => array(
				'new_name' => 'color_content_heading',
			),
			'main_text' => array(
				'new_name' => 'color_content_text',
			),
			'main_primary' => array(
				'new_name' => 'color_content_primary',
			),
			'main_secondary' => array(
				'new_name' => 'color_content_secondary',
			),
			'main_fade' => array(
				'new_name' => 'color_content_faded',
			),
			'change_alternate_content_colors' => array(
				'new_name' => 'change_alt_content_colors',
			),
			'alt_bg' => array(
				'new_name' => 'color_alt_content_bg',
			),
			'alt_bg_alternative' => array(
				'new_name' => 'color_alt_content_bg_alt',
			),
			'alt_border' => array(
				'new_name' => 'color_alt_content_border',
			),
			'alt_heading' => array(
				'new_name' => 'color_alt_content_heading',
			),
			'alt_text' => array(
				'new_name' => 'color_alt_content_text',
			),
			'alt_primary' => array(
				'new_name' => 'color_alt_content_primary',
			),
			'alt_secondary' => array(
				'new_name' => 'color_alt_content_secondary',
			),
			'alt_fade' => array(
				'new_name' => 'color_alt_content_faded',
			),
			'subfooter_bg' => array(
				'new_name' => 'color_subfooter_bg',
			),
			'subfooter_bg_alt' => array(
				'new_name' => 'color_subfooter_bg_alt',
			),
			'subfooter_border' => array(
				'new_name' => 'color_subfooter_border',
			),
			'subfooter_text' => array(
				'new_name' => 'color_subfooter_text',
			),
			'subfooter_heading' => array(
				'new_name' => 'color_subfooter_heading',
			),
			'subfooter_link' => array(
				'new_name' => 'color_subfooter_link',
			),
			'subfooter_link_hover' => array(
				'new_name' => 'color_subfooter_link_hover',
			),
			'footer_bg' => array(
				'new_name' => 'color_footer_bg',
			),
			'footer_text' => array(
				'new_name' => 'color_footer_text',
			),
			'footer_link' => array(
				'new_name' => 'color_footer_link',
			),
			'footer_link_hover' => array(
				'new_name' => 'color_footer_link_hover',
			),
			'header_is_sticky' => array(
				'new_name' => 'header_sticky',
			),
			'header_bg_transparent' => array(
				'new_name' => 'header_transparent',
			),
			'disable_sticky_header_width' => array(
				'new_name' => 'header_sticky_disable_width',
			),
			'main_header_layout' => array(
				'new_name' => 'header_layout',
			),
			'header_main_shrinked_height' => array(
				'new_name' => 'header_main_sticky_height_1',
			),
			'header_show_search' => array(
				'new_name' => 'header_search_show',
			),
			'header_show_contacts' => array(
				'new_name' => 'header_contacts_show',
			),
			'header_phone' => array(
				'new_name' => 'header_contacts_phone',
			),
			'header_email' => array(
				'new_name' => 'header_contacts_email',
			),
			'header_custom_icon' => array(
				'new_name' => 'header_contacts_custom_icon',
			),
			'header_custom_text' => array(
				'new_name' => 'header_contacts_custom_text',
			),
			'header_show_socials' => array(
				'new_name' => 'header_socials_show',
			),
			'header_social_facebook' => array(
				'new_name' => 'header_socials_facebook',
			),
			'header_social_twitter' => array(
				'new_name' => 'header_socials_twitter',
			),
			'header_social_google' => array(
				'new_name' => 'header_socials_google',
			),
			'header_social_linkedin' => array(
				'new_name' => 'header_socials_linkedin',
			),
			'header_social_youtube' => array(
				'new_name' => 'header_socials_youtube',
			),
			'header_social_vimeo' => array(
				'new_name' => 'header_socials_vimeo',
			),
			'header_social_flickr' => array(
				'new_name' => 'header_socials_flickr',
			),
			'header_social_instagram' => array(
				'new_name' => 'header_socials_instagram',
			),
			'header_social_behance' => array(
				'new_name' => 'header_socials_behance',
			),
			'header_social_xing' => array(
				'new_name' => 'header_socials_xing',
			),
			'header_social_pinterest' => array(
				'new_name' => 'header_socials_pinterest',
			),
			'header_social_skype' => array(
				'new_name' => 'header_socials_skype',
			),
			'header_social_tumblr' => array(
				'new_name' => 'header_socials_tumblr',
			),
			'header_social_dribbble' => array(
				'new_name' => 'header_socials_dribbble',
			),
			'header_social_vk' => array(
				'new_name' => 'header_socials_vk',
			),
			'header_social_soundcloud' => array(
				'new_name' => 'header_socials_soundcloud',
			),
			'header_social_yelp' => array(
				'new_name' => 'header_socials_yelp',
			),
			'header_social_twitch' => array(
				'new_name' => 'header_socials_twitch',
			),
			'header_social_deviantart' => array(
				'new_name' => 'header_socials_deviantart',
			),
			'header_social_foursquare' => array(
				'new_name' => 'header_socials_foursquare',
			),
			'header_social_github' => array(
				'new_name' => 'header_socials_github',
			),
			'header_social_rss' => array(
				'new_name' => 'header_socials_rss',
			),
			'header_show_language' => array(
				'new_name' => 'header_language_show',
			),
			'header_language_type' => array(
				'new_name' => 'header_language_source',
				'values' => array(
					'Your own links' => 'own',
					'WPML language switcher' => 'wpml',
				),
			),
			'header_language_amount' => array(
				'new_name' => 'header_link_qty',
				'values' => array(
					'2' => '1',
					'3' => '2',
					'4' => '3',
					'5' => '4',
					'6' => '5',
					'7' => '6',
					'8' => '7',
					'9' => '8',
					'10' => '9',
				),
			),
			'header_language_1_name' => array(
				'new_name' => 'header_link_title',
			),
			'header_language_2_name' => array(
				'new_name' => 'header_link_1_label',
			),
			'header_language_2_url' => array(
				'new_name' => 'header_link_1_url',
			),
			'header_language_3_name' => array(
				'new_name' => 'header_link_2_label',
			),
			'header_language_3_url' => array(
				'new_name' => 'header_link_2_url',
			),
			'header_language_4_name' => array(
				'new_name' => 'header_link_3_label',
			),
			'header_language_4_url' => array(
				'new_name' => 'header_link_3_url',
			),
			'header_language_5_name' => array(
				'new_name' => 'header_link_4_label',
			),
			'header_language_5_url' => array(
				'new_name' => 'header_link_4_url',
			),
			'header_language_6_name' => array(
				'new_name' => 'header_link_5_label',
			),
			'header_language_6_url' => array(
				'new_name' => 'header_link_5_url',
			),
			'header_language_7_name' => array(
				'new_name' => 'header_link_6_label',
			),
			'header_language_7_url' => array(
				'new_name' => 'header_link_6_url',
			),
			'header_language_8_name' => array(
				'new_name' => 'header_link_7_label',
			),
			'header_language_8_url' => array(
				'new_name' => 'header_link_7_url',
			),
			'header_language_9_name' => array(
				'new_name' => 'header_link_8_label',
			),
			'header_language_9_url' => array(
				'new_name' => 'header_link_8_url',
			),
			'header_language_10_name' => array(
				'new_name' => 'header_link_9_label',
			),
			'header_language_10_url' => array(
				'new_name' => 'header_link_9_url',
			),
			'mobile_nav_width' => array(
				'new_name' => 'menu_mobile_width',
			),
			'menu_hover_effect' => array(
				'values' => array(
					TRUE => 'underline',
					FALSE => 'simple',
				),
			),
			'menu_hover_animation' => array(
				'new_name' => 'menu_dropdown_effect',
				'values' => array(
					'FadeIn' => 'opacity',
					'FadeIn + SlideDown' => 'height',
					'Material Design Effect' => 'mdesign',
				),
			),
			'header_menu_togglable' => array(
				'new_name' => 'menu_togglable_type',
			),
			'footer_show_widgets' => array(
				'new_name' => 'footer_show_top',
			),
			'footer_widgets_columns' => array(
				'new_name' => 'footer_columns',
			),
			'footer_show_footer' => array(
				'new_name' => 'footer_show_bottom',
			),
			'heading_font' => array(
				'new_name' => 'heading_font_family',
			),
			'body_text_font' => array(
				'new_name' => 'body_font_family',
			),
			'regular_fontsize' => array(
				'new_name' => 'body_fontsize',
			),
			'regular_fontsize_mobile' => array(
				'new_name' => 'body_fontsize_mobile',
			),
			'regular_lineheight' => array(
				'new_name' => 'body_lineheight',
			),
			'regular_lineheight_mobile' => array(
				'new_name' => 'body_lineheight_mobile',
			),
			'navigation_font' => array(
				'new_name' => 'menu_font_family',
			),
			'nav_font_weight_200' => array(
				'new_name' => 'menu_font_weight_200',
			),
			'nav_font_weight_300' => array(
				'new_name' => 'menu_font_weight_300',
			),
			'nav_font_weight_400' => array(
				'new_name' => 'menu_font_weight_400',
			),
			'nav_font_weight_600' => array(
				'new_name' => 'menu_font_weight_600',
			),
			'nav_font_weight_700' => array(
				'new_name' => 'menu_font_weight_700',
			),
			'nav_font_style_italic' => array(
				'new_name' => 'menu_font_style_italic',
			),
			'nav_fontsize' => array(
				'new_name' => 'menu_fontsize',
			),
			'nav_fontsize_mobile' => array(
				'new_name' => 'menu_fontsize_mobile',
			),
			'subnav_fontsize' => array(
				'new_name' => 'menu_sub_fontsize',
			),
			'subnav_fontsize_mobile' => array(
				'new_name' => 'menu_sub_fontsize_mobile',
			),
			'portfolio_sidebar_pos' => array(
				'new_name' => 'portfolio_sidebar',
				'values' => array(
					'No Sidebar' => 'none',
					'Right' => 'right',
					'Left' => 'left',
				),
			),
			'portfolio_slug_info' => array(
				'new_name' => 'portfolio_info',
			),
			'blog_sidebar_pos' => array(
				'new_name' => array(
					'blog_sidebar',
					'archive_sidebar',
					'search_sidebar',
				),
				'values' => array(
					'No Sidebar' => 'none',
					'Right' => 'right',
					'Left' => 'left',
				),
			),
			'post_sidebar_pos' => array(
				'new_name' => 'post_sidebar',
				'values' => array(
					'No Sidebar' => 'none',
					'Right' => 'right',
					'Left' => 'left',
				),
			),
			'post_related_posts' => array(
				'new_name' => 'post_related',
			),
			'blog_layout' => array(
				'values' => array(
					'Large Image' => 'large',
					'Small Image' => 'smallcircle',
					'Masonry Grid with ajax load' => 'masonry',
					'Masonry Grid with pagination' => 'masonry',
				),
			),
			'archive_layout' => array(
				'values' => array(
					'Large Image' => 'large',
					'Small Image' => 'smallcircle',
					'Masonry Grid' => 'masonry',
				),
			),
			'search_layout' => array(
				'values' => array(
					'Large Image' => 'large',
					'Small Image' => 'smallcircle',
					'Masonry Grid' => 'masonry',
				),
			),
			'shop_sidebar_pos' => array(
				'new_name' => 'shop_sidebar',
				'values' => array(
					'No Sidebar' => 'none',
					'Right' => 'right',
					'Left' => 'left',
				),
			),
			'good_sidebar_pos' => array(
				'new_name' => 'product_sidebar',
				'values' => array(
					'No Sidebar' => 'none',
					'Right' => 'right',
					'Left' => 'left',
				),
			),
			'shop_columns_qty' => array(
				'new_name' => 'shop_columns',
				'values' => array(
					'2 columns' => '2',
					'3 columns' => '3',
					'4 columns' => '4',
					'5 columns' => '5',
				),
			),
			'related_products_qty' => array(
				'new_name' => 'product_related_qty',
				'values' => array(
					'2 items' => '2',
					'3 items' => '3',
					'4 items' => '4',
					'5 items' => '5',
				),
			),
			'forum_sidebar_pos' => array(
				'new_name' => 'forum_sidebar',
				'values' => array(
					'No Sidebar' => 'none',
					'Right' => 'right',
					'Left' => 'left',
				),
			),
		);

		foreach ( $rules as $option => $rule ) {
			if ( isset( $options[ $option ] ) ) {
				if ( isset( $rule['values'] ) ) {
					foreach ( $rule['values'] as $old_value => $new_value ) {
						if ( $options[ $option ] == $old_value ) {
							$options[ $option ] = $new_value;
							break;
						}
					}
				}

				if ( isset( $rule['new_name'] ) ) {
					if ( ! is_array( $rule['new_name'] ) ) {
						$rule['new_name'] = array( $rule['new_name'] );
					}
					$option_value = $options[ $option ];
					unset( $options[ $option ] );
					foreach ( $rule['new_name'] as $new_name ) {
						if ( ! isset( $options[ $new_name ] ) ) {
							$options[ $new_name ] = $option_value;
						}
					}
				}
			}
		}

		if ( isset( $options['use_excerpt'] ) ) {
			if ( $options['use_excerpt'] == 'Full Content of Post' ) {
				$options['blog_content_type'] = $options['archive_content_type'] = $options['search_content_type'] = 'content';
			} elseif ( $options['use_excerpt'] == 'No Content' ) {
				$options['blog_content_type'] = $options['archive_content_type'] = $options['search_content_type'] = 'none';
			} else {
				$options['blog_content_type'] = $options['archive_content_type'] = $options['search_content_type'] = 'excerpt';
			}
			unset( $options['use_excerpt'] );
		}

		foreach ( array( 'blog_meta', 'archive_meta', 'search_meta', 'post_meta' ) as $key ) {
			if ( ! isset( $options[ $key ] ) OR ! is_array( $options[ $key ] ) ) {
				$options[ $key ] = array();
			}
		}
		if ( isset( $options['post_read_more'] ) AND $options['post_read_more'] == 1 ) {
			$options['blog_meta'][] = 'read_more';
			$options['archive_meta'][] = 'read_more';
			$options['search_meta'][] = 'read_more';

			unset( $options['post_read_more'] );
		}

		if ( isset( $options['post_meta_date'] ) AND $options['post_meta_date'] == 1 ) {
			$options['post_meta'][] = 'date';
			$options['blog_meta'][] = 'date';
			$options['archive_meta'][] = 'date';
			$options['search_meta'][] = 'date';

			unset( $options['post_meta_date'] );
		}

		if ( isset( $options['post_meta_author'] ) AND $options['post_meta_author'] == 1 ) {
			$options['post_meta'][] = 'author';
			$options['blog_meta'][] = 'author';
			$options['archive_meta'][] = 'author';
			$options['search_meta'][] = 'author';

			unset( $options['post_meta_author'] );
		}

		if ( isset( $options['post_meta_categories'] ) AND $options['post_meta_categories'] == 1 ) {
			$options['post_meta'][] = 'categories';
			$options['blog_meta'][] = 'categories';
			$options['archive_meta'][] = 'categories';
			$options['search_meta'][] = 'categories';

			unset( $options['post_meta_categories'] );
		}

		if ( isset( $options['post_meta_comments'] ) AND $options['post_meta_comments'] == 1 ) {
			$options['post_meta'][] = 'comments';
			$options['blog_meta'][] = 'comments';
			$options['archive_meta'][] = 'comments';
			$options['search_meta'][] = 'comments';

			unset( $options['post_meta_comments'] );
		}

		if ( isset( $options['post_meta_tags'] ) AND $options['post_meta_tags'] == 1 ) {
			$options['post_meta'][] = 'tags';
			$options['blog_meta'][] = 'tags';
			$options['archive_meta'][] = 'tags';
			$options['search_meta'][] = 'tags';

			unset( $options['post_meta_tags'] );
		}

		// Applying font weights
		$weights = array( 200, 300, 400, 600, 700 );
		foreach ( array( 'heading', 'body', 'menu' ) as $prefix ) {
			$has_italic = isset( $options[ $prefix . '_font_style_italic' ] ) ? ( ! ! $options[ $prefix . '_font_style_italic' ] ) : FALSE;
			$variants = array();
			foreach ( $weights as $weight ) {
				if ( isset( $options[ $prefix . '_font_weight_' . $weight ] ) ) {
					if ( $options[ $prefix . '_font_weight_' . $weight ] ) {
						$variants[] = $weight;
						if ( $has_italic ) {
							$variants[] = $weight . 'italic';
						}
					}
					unset( $options[ $prefix . '_font_weight_' . $weight ] );
				}
			}
			// Empty font or web safe combination selected
			if ( ! isset( $options[ $prefix . '_font_family' ] ) ) {
				$options[ $prefix . '_font_family' ] = 'none';
				$changed = TRUE;
			}
			if ( $options[ $prefix . '_font_family' ] == 'none' OR strpos( $options[ $prefix . '_font_family' ], ',' ) !== FALSE ) {
				continue;
			}
			if ( empty( $variants ) ) {
				$variants = array( 400, 700 );
			}
			$options[ $prefix . '_font_family' ] .= '|' . implode( ',', $variants );
		}

		return TRUE;
	}

	// Meta
	public function translate_meta( &$meta, $post_type ) {

		$meta_changed = FALSE;

		$translate_meta_for = array(
			'post',
			'page',
			'us_portfolio',
			'forum',
			'topic',
		);

		if ( ! in_array( $post_type, $translate_meta_for ) ) {
			return $meta_changed;
		}

		$rules = array(
			'us_subtitle' => array(
				'new_name' => 'us_titlebar_subtitle',
				'post_types' => array(
					'page',
					'us_portfolio',
					'forum',
					'topic',
				),
			),
			'us_header_layout_type' => array(
				'new_name' => 'us_titlebar_size',
				'values' => array(
					'Ultra Compact' => 'small',
					'Compact' => 'medium',
					'Large' => 'large',
					'Huge' => 'huge',
				),
				'post_types' => array(
					'page',
					'us_portfolio',
					'forum',
					'topic',
				),
			),
			'us_titlebar_parallax' => array(
				'new_name' => 'us_titlebar_image_parallax',
				'post_types' => array(
					'page',
					'us_portfolio',
					'forum',
					'topic',
				),
			),
			'us_show_subfooter_widgets' => array(
				'new_name' => 'us_footer_show_top',
				'values' => array(
					'yes' => 'show',
					'no' => 'hide',
				),
				'post_types' => array(
					'post',
					'page',
					'us_portfolio',
				),
			),
			'us_show_footer' => array(
				'new_name' => 'us_footer_show_bottom',
				'values' => array(
					'yes' => 'show',
					'no' => 'hide',
				),
				'post_types' => array(
					'post',
					'page',
					'us_portfolio',
				),
			),
			'us_additional_image' => array(
				'new_name' => 'us_tile_additional_image',
				'post_types' => array(
					'us_portfolio',
				),
			),
			'us_title_bg_color' => array(
				'new_name' => 'us_tile_bg_color',
				'post_types' => array(
					'us_portfolio',
				),
			),
			'us_title_text_color' => array(
				'new_name' => 'us_tile_text_color',
				'post_types' => array(
					'us_portfolio',
				),
			),
			'us_titlebar' => array(
				'new_name' => 'us_titlebar_content',
				'values' => array(
					'' => 'all',
					'caption_only' => 'caption',
				),
				'post_types' => array(
					'page',
					'us_portfolio',
					'forum',
					'topic',
				),
			),
		);

		// Changing values and giving new names where needed
		foreach ( $rules as $meta_name => $rule ) {
			if ( ! in_array( $post_type, $rule['post_types'] ) ) {
				continue;
			}
			if ( ! isset( $meta[ $meta_name ] ) AND isset( $rule['values'] ) AND isset( $rule['values'][''] ) ) {
				$meta[ $meta_name ] = array( '' );
			}
			if ( isset( $meta[ $meta_name ] ) ) {
				if ( isset( $rule['values'] ) ) {
					foreach ( $rule['values'] as $old_value => $new_value ) {
						if ( $meta[ $meta_name ][0] == $old_value ) {
							$meta_changed = TRUE;
							$meta[ $meta_name ][0] = $new_value;
							break;
						}
					}
				}

				if ( isset( $rule['new_name'] ) ) {
					if ( ! is_array( $rule['new_name'] ) ) {
						$rule['new_name'] = array( $rule['new_name'] );
					}
					$meta_value = $meta[ $meta_name ];
					//unset( $meta[$meta_name] );
					foreach ( $rule['new_name'] as $new_name ) {
						if ( ! isset( $meta[ $new_name ] ) ) {
							$meta_changed = TRUE;
							$meta[ $new_name ] = $meta_value;
						}
					}
				}
			}
		}

		// Cases that is hard to describe by rues

		// Translating us_header_type to us_header_pos & us_heder_bg
		if ( isset( $meta['us_header_type'] ) AND ( empty( $meta['us_header_pos'] ) OR empty( $meta['us_header_bg'] ) ) ) {
			$meta_changed = TRUE;
			if ( ! isset( $meta['us_header_pos'] ) ) {
				$meta['us_header_pos'] = array();
			}
			if ( ! isset( $meta['us_header_bg'] ) ) {
				$meta['us_header_bg'] = array();
			}
			switch ( $meta['us_header_type'][0] ) {
				case 'Sticky Transparent':
					$meta['us_header_pos'][0] = isset( $meta['us_header_pos'][0] ) ? $meta['us_header_pos'][0] : 'fixed';
					$meta['us_header_bg'][0] = isset( $meta['us_header_bg'][0] ) ? $meta['us_header_bg'][0] : 'transparent';
					break;

				case 'Sticky Solid':
					$meta['us_header_pos'][0] = isset( $meta['us_header_pos'][0] ) ? $meta['us_header_pos'][0] : 'fixed';
					$meta['us_header_bg'][0] = isset( $meta['us_header_bg'][0] ) ? $meta['us_header_bg'][0] : 'solid';
					break;

				case 'Non-sticky':
					$meta['us_header_pos'][0] = isset( $meta['us_header_pos'][0] ) ? $meta['us_header_pos'][0] : 'static';
					$meta['us_header_bg'][0] = isset( $meta['us_header_bg'][0] ) ? $meta['us_header_bg'][0] : 'solid';
					break;
			}
			unset( $meta['us_header_type'] );
		}

		// Adding us_titlebar_image_size if needed
		if ( in_array( $post_type, array(
				'page',
				'portfolio',
				'forum',
				'topic',
			) ) AND empty( $meta['us_titlebar_image_size'] )
		) {
			$meta_changed = TRUE;
			$meta['us_titlebar_image_size'][0] = 'cover';
		}

		// Translating Template into meta fields
		if ( isset( $meta['_wp_page_template'][0] ) AND ( $meta['_wp_page_template'][0] != 'default' ) ) {
			$meta_changed = TRUE;
			switch ( $meta['_wp_page_template'][0] ) {
				case 'page-blank.php':
					$meta['us_header_remove'][0] = TRUE;
					$meta['us_titlebar_content'][0] = 'hide';
					if ( ! isset( $meta['us_footer_show_top'] ) ) {
						$meta['us_footer_show_top'] = array();
					}
					$meta['us_footer_show_top'][0] = 'hide';
					if ( ! isset( $meta['us_footer_show_bottom'] ) ) {
						$meta['us_footer_show_bottom'] = array();
					}
					$meta['us_footer_show_bottom'][0] = 'hide';
					break;

				case 'page-sidebar_left.php':
					if ( ! isset( $meta['us_sidebar'] ) ) {
						$meta['us_sidebar'] = array();
					}
					$meta['us_sidebar'][0] = 'left';
					$meta['_wp_page_template'][0] = 'default';
					break;

				case 'page-sidebar_right.php':
					if ( ! isset( $meta['us_sidebar'] ) ) {
						$meta['us_sidebar'] = array();
					}
					$meta['us_sidebar'][0] = 'right';
					$meta['_wp_page_template'][0] = 'default';
					break;

				case 'page-grid_blog.php':
				case 'page-grid_blog_paginated.php':
					if ( ! isset( $meta['us_sidebar'] ) ) {
						$meta['us_sidebar'] = array();
					}
					$meta['us_sidebar'][0] = 'none';
					break;

				case 'page-big_blog.php':
				case 'page-small_blog.php':
					if ( ! isset( $meta['us_sidebar'] ) ) {
						$meta['us_sidebar'] = array();
					}
					$meta['us_sidebar'][0] = us_get_option( 'blog_sidebar', '' );
					break;
			}
		}

		return $meta_changed;
	}

	// Widgets
	public function translate_widgets( &$name, &$instance ) {
		if ( $name == 'socials' ) {
			$name = 'us_socials';
			if ( isset( $instance['size'] ) ) {
				if ( $instance['size'] == 'normal' ) {
					$instance['size'] = 'medium';
				} elseif ( $instance['size'] == 'big' ) {
					$instance['size'] = 'large';
				}
			}
			if ( isset( $instance['style'] ) ) {
				$old_new_values = array(
					'1' => 'colored',
					'2' => 'colored_inv',
					'3' => 'desaturated',
					'4' => 'desaturated_inv',
				);
				$instance['color'] = isset( $old_new_values[ $instance['style'] ] ) ? $old_new_values[ $instance['style'] ] : 'colored';
				unset( $instance['style'] );
			} else {
				$instance['color'] = us_config( 'widgets.us_socials.params.color.std', 'colored' );
			}
			return TRUE;
		} elseif ( $name == 'login' ) {
			$name = 'us_login';

			return TRUE;
		} elseif ( $name == 'contact' ) {
			$name = 'us_contacts';

			return TRUE;
		}

		return FALSE;
	}

	// Shortcodes
	public function translate_vc_row( &$name, &$params, &$content ) {

		$shortcode_changed = FALSE;

		$params_rules = array(
			'section_id' => array(
				'new_name' => 'el_id',
			),
		);
		$shortcode_changed = ( $this->translate_params( $params, $params_rules ) OR $shortcode_changed );

		if ( isset( $params['parallax_image'] ) ) {
			unset( $params['parallax_image'] );
			$shortcode_changed = TRUE;
		}

		if ( isset( $params['bg_color_info'] ) ) {
			unset( $params['bg_color_info'] );
			$shortcode_changed = TRUE;
		}

		if ( isset( $params['parallax_speed'] ) ) {
			unset( $params['parallax_speed'] );
			$shortcode_changed = TRUE;
		}

		if ( isset( $params['columns_type'] ) ) {
			if ( $params['columns_type'] == 'default' ) {
				unset( $params['columns_type'] );
				$shortcode_changed = TRUE;
			} elseif ( $params['columns_type'] == 'wide' ) {
				$params['columns_type'] = 'medium';
				$shortcode_changed = TRUE;
			}
		}

		if ( isset( $params['section'] ) AND $params['section'] == 'yes' ) {
			unset( $params['section'] );
			unset( $params['bg_fade'] );
			unset( $params['boxed_columns'] );

			$params_rules = array(
				'background' => array(
					'new_name' => 'color_scheme',
				),
				'section_bg_color' => array(
					'new_name' => 'us_bg_color',
				),
				'section_text_color' => array(
					'new_name' => 'us_text_color',
				),
				'img' => array(
					'new_name' => 'us_bg_image',
				),
				'parallax' => array(
					'new_name' => 'us_bg_parallax',
				),
			);
			if ( isset( $params['section_overlay'] ) AND ! empty( $params['section_overlay'] ) ) {
				$params_rules['section_overlay'] = array(
					'new_name' => 'us_bg_overlay_color',
				);
			} elseif ( isset( $params['overlay'] ) AND ! empty( $params['overlay'] ) ) {
				$params_rules['overlay'] = array(
					'new_name' => 'us_bg_overlay_color',
					'values' => array(
						'black_10' => 'rgba(0,0,0,0.1)',
						'black_20' => 'rgba(0,0,0,0.2)',
						'black_30' => 'rgba(0,0,0,0.3)',
						'black_40' => 'rgba(0,0,0,0.4)',
						'black_50' => 'rgba(0,0,0,0.5)',
						'black_60' => 'rgba(0,0,0,0.6)',
						'black_70' => 'rgba(0,0,0,0.7)',
						'black_80' => 'rgba(0,0,0,0.8)',
						'black_90' => 'rgba(0,0,0,0.9)',
						'white_10' => 'rgba(255,255,255,0.1)',
						'white_20' => 'rgba(255,255,255,0.2)',
						'white_30' => 'rgba(255,255,255,0.3)',
						'white_40' => 'rgba(255,255,255,0.4)',
						'white_50' => 'rgba(255,255,255,0.5)',
						'white_60' => 'rgba(255,255,255,0.6)',
						'white_70' => 'rgba(255,255,255,0.7)',
						'white_80' => 'rgba(255,255,255,0.8)',
						'white_90' => 'rgba(255,255,255,0.9)',
					),
				);
			}

			$this->translate_params( $params, $params_rules );

			if ( isset( $params['full_height'] ) AND empty( $params['full_height'] ) ) {
				unset( $params['full_height'] );
			} elseif ( isset( $params['full_height'] ) AND $params['full_height'] == 'yes' ) {
				$params['height'] = 'auto';
				unset( $params['full_height'] );
			}

			if ( isset( $params['full_screen'] ) AND empty( $params['full_screen'] ) ) {
				unset( $params['full_screen'] );
			} elseif ( isset( $params['full_screen'] ) AND $params['full_screen'] == 'yes' ) {
				$params['height'] = 'full';
				unset( $params['full_screen'] );
			}

			if ( isset( $params['vertical_centering'] ) AND empty( $params['vertical_centering'] ) ) {
				unset( $params['vertical_centering'] );
			} elseif ( isset( $params['vertical_centering'] ) AND $params['vertical_centering'] == 'yes' ) {
				$params['valign'] = 'center';
				unset( $params['vertical_centering'] );
			}

			if ( isset( $params['full_width'] ) AND empty( $params['full_width'] ) ) {
				unset( $params['full_width'] );
			} elseif ( isset( $params['full_width'] ) AND $params['full_width'] == 'yes' ) {
				$params['width'] = 'full';
				unset( $params['full_width'] );
			}

			if ( isset( $params['parallax_bg_width'] ) AND ! empty( $params['parallax_bg_width'] ) ) {
				if ( $params['parallax_bg_width'] != '110' ) {
					$params['us_bg_parallax_width'] = $params['parallax_bg_width'];
				}
				unset( $params['parallax_bg_width'] );
			}

			if ( isset( $params['parallax_reverse'] ) AND empty( $params['parallax_reverse'] ) ) {
				unset( $params['parallax_reverse'] );
			} elseif ( isset( $params['parallax_reverse'] ) AND $params['parallax_reverse'] == 'yes' ) {
				$params['us_bg_parallax_reverse'] = '1';
				unset( $params['parallax_reverse'] );
			}

			if ( isset( $params['video'] ) AND empty( $params['video'] ) ) {
				unset( $params['video'] );
			} elseif ( isset( $params['video'] ) AND $params['video'] == 'yes' ) {
				$params['us_bg_video'] = '1';
				unset( $params['video'] );
			}

			$shortcode_changed = TRUE;
		}

		// Parsing CSS
		if ( isset( $params['css'] ) AND ! empty( $params['css'] ) ) {
			$css_params_changed = FALSE;
			$css_params_str = substr( $params['css'], strpos( $params['css'], '{' ) + 1, strpos( $params['css'], '}' ) - strpos( $params['css'], '{' ) - 1 );
			$css_params = explode( ';', $css_params_str );
			foreach ( $css_params as $k => $css_param ) {
				if ( empty( $css_param ) ) {
					continue;
				}
				$css_param_info = explode( ': ', $css_param );
				if ( trim( $css_param_info[0] ) == 'background-color' ) {
					$params['us_bg_color'] = trim( $css_param_info[1] );
					unset( $css_params[ $k ] );
					$shortcode_changed = $css_params_changed = TRUE;
				}
				if ( in_array( trim( $css_param_info[0] ), array( 'background-image', 'background' ) ) ) {
					if ( preg_match( '/\?id=(\d+)/i', trim( $css_param_info[1] ), $bg_image_matches ) ) {
						$params['us_bg_image'] = $bg_image_matches[1];
						unset( $css_params[ $k ] );
						$shortcode_changed = $css_params_changed = TRUE;
					}
				}
			}

			if ( $css_params_changed ) {
				$params['css'] = str_replace( $css_params_str, implode( ';', $css_params ), $params['css'] );
			}
		}

		return $shortcode_changed;
	}

	public function translate_vc_row_inner( &$name, &$params, &$content ) {

		$shortcode_changed = FALSE;

		if ( isset( $params['columns_type'] ) ) {
			if ( $params['columns_type'] == 'default' ) {
				unset( $params['columns_type'] );
				$shortcode_changed = TRUE;
			} elseif ( $params['columns_type'] == 'wide' ) {
				$params['columns_type'] = 'medium';
				$shortcode_changed = TRUE;
			}
		}

		$params_rules = array(
			'section_id' => array(
				'new_name' => 'el_id',
			),
		);

		$shortcode_changed = ( $this->translate_params( $params, $params_rules ) OR $shortcode_changed );

		return $shortcode_changed;
	}

	public function translate_vc_column( &$name, &$params, &$content ) {
		$shortcode_changed = FALSE;

		if ( isset( $params['text_color'] ) AND empty( $params['text_color'] ) ) {
			unset( $params['text_color'] );
			$shortcode_changed = TRUE;
		}

		if ( isset( $params['animate'] ) AND empty( $params['animate'] ) ) {
			unset( $params['animate'] );
			$shortcode_changed = TRUE;
		}

		if ( isset( $params['animate_delay'] ) AND empty( $params['animate_delay'] ) ) {
			unset( $params['animate_delay'] );
			$shortcode_changed = TRUE;
		}

		return $shortcode_changed;
	}

	public function translate_vc_column_inner( &$name, &$params, &$content ) {
		return $this->translate_vc_column( $name, $params, $content );
	}

	public function translate_vc_column_text( &$name, &$params, &$content ) {
		$shortcode_changed = FALSE;

		if ( isset( $params['css_animation'] ) AND empty( $params['css_animation'] ) ) {
			unset( $params['css_animation'] );
			$shortcode_changed = TRUE;
		}

		return $shortcode_changed;
	}

	public function translate_vc_tabs( &$name, &$params, &$content ) {
		$name = 'vc_tta_tabs';

		if ( isset( $params['timeline'] ) AND $params['timeline'] == 'yes' ) {
			$params['layout'] = 'timeline';
			unset( $params['timeline'] );
		} elseif ( isset( $params['timeline'] ) AND empty( $params['timeline'] ) ) {
			unset( $params['timeline'] );
		}

		unset( $params['no_indents'] );

		return TRUE;
	}

	public function translate_vc_tab( &$name, &$params, &$content ) {
		$name = 'vc_tta_section';

		if ( isset( $params['no_indents'] ) AND $params['no_indents'] == 'yes' ) {
			$params['indents'] = 'none';
			unset( $params['no_indents'] );
		} elseif ( isset( $params['no_indents'] ) AND empty( $params['no_indents'] ) ) {
			unset( $params['no_indents'] );
		}

		if ( isset( $params['active'] ) AND $params['active'] == 'yes' ) {
			$params['active'] = '1';
		} elseif ( isset( $params['active'] ) AND empty( $params['active'] ) ) {
			unset( $params['active'] );
		}

		unset( $params['tab_id'] );

		return TRUE;
	}

	public function translate_vc_accordion( &$name, &$params, &$content ) {
		$name = 'vc_tta_accordion';

		if ( isset( $params['title_center'] ) AND $params['title_center'] == 'yes' ) {
			$params['c_align'] = 'center';
			unset( $params['title_center'] );
		} elseif ( isset( $params['title_center'] ) AND empty( $params['title_center'] ) ) {
			unset( $params['title_center'] );
		}

		if ( isset( $params['toggle'] ) AND $params['toggle'] == 'yes' ) {
			$params['toggle'] = '1';
		} elseif ( isset( $params['toggle'] ) AND empty( $params['toggle'] ) ) {
			unset( $params['toggle'] );
		}

		return TRUE;
	}

	public function translate_vc_accordion_tab( &$name, &$params, &$content ) {
		$name = 'vc_tta_section';

		if ( isset( $params['no_indents'] ) AND $params['no_indents'] == 'yes' ) {
			$params['indents'] = 'none';
			unset( $params['no_indents'] );
		} elseif ( isset( $params['no_indents'] ) AND empty( $params['no_indents'] ) ) {
			unset( $params['no_indents'] );
		}

		if ( isset( $params['active'] ) AND $params['active'] == 'yes' ) {
			$params['active'] = '1';
		} elseif ( isset( $params['active'] ) AND empty( $params['active'] ) ) {
			unset( $params['active'] );
		}

		if ( isset( $params['bg_color'] ) AND empty( $params['bg_color'] ) ) {
			unset( $params['bg_color'] );
		}

		if ( isset( $params['text_color'] ) AND empty( $params['text_color'] ) ) {
			unset( $params['text_color'] );
		}

		return TRUE;
	}

	public function translate_vc_actionbox( &$name, &$params ) {
		$name = 'us_cta';

		if ( isset( $params['button2'] ) AND ( ! empty( $params['button2'] ) ) ) {
			$params['second_button'] = 1;
		}

		$params_rules = array(
			'type' => array(
				'new_name' => 'color',
				'values' => array(
					'alternate' => 'light',
				),
			),
			'button1' => array(
				'new_name' => 'btn_label',
			),
			'style1' => array(
				'new_name' => 'btn_color',
				'values' => array(
					'default' => 'light',
				),
			),
			'size1' => array(
				'new_name' => 'btn_size',
				'values' => array(
					'big' => 'large',
				),
			),
			'icon1' => array(
				'new_name' => 'btn_icon',
			),
			'button2' => array(
				'new_name' => 'btn2_label',
			),
			'style2' => array(
				'new_name' => 'btn2_color',
				'values' => array(
					'default' => 'light',
				),
			),
			'size2' => array(
				'new_name' => 'btn2_size',
				'values' => array(
					'big' => 'large',
				),
			),
			'icon2' => array(
				'new_name' => 'btn2_icon',
			),
		);

		$this->translate_params( $params, $params_rules );

		$btn_link = '';
		if ( isset( $params['link1'] ) ) {
			if ( $params['link1'] != '' ) {
				$btn_link .= 'url:' . urlencode( $params['link1'] );
			}
			unset( $params['link1'] );
		}
		if ( isset( $params['target1'] ) AND ( $params['target1'] == 1 OR $params['target1'] == 'yes' ) ) {
			$btn_link .= '|target:%20_blank';
			unset( $params['target1'] );
		}
		$params['btn_link'] = trim( $btn_link, '|' );

		if ( isset( $params['outlined1'] ) AND empty( $params['outlined1'] ) ) {
			unset( $params['outlined1'] );
		} elseif ( isset( $params['outlined1'] ) AND $params['outlined1'] == 'yes' ) {
			$params['btn_style'] = 'outlined';
			unset( $params['outlined1'] );
		}

		$btn_link = '';
		if ( isset( $params['link2'] ) ) {
			if ( $params['link2'] != '' ) {
				$btn_link .= 'url:' . urlencode( $params['link2'] );
			}
			unset( $params['link2'] );
		}
		if ( isset( $params['target2'] ) AND ( $params['target2'] == 1 OR $params['target2'] == 'yes' ) ) {
			$btn_link .= '|target:%20_blank';
			unset( $params['target2'] );
		}
		$params['btn2_link'] = trim( $btn_link, '|' );

		if ( isset( $params['outlined2'] ) AND empty( $params['outlined2'] ) ) {
			unset( $params['outlined2'] );
		} elseif ( isset( $params['outlined2'] ) AND $params['outlined2'] == 'yes' ) {
			$params['btn2_style'] = 'outlined';
			unset( $params['outlined2'] );
		}

		return TRUE;
	}

	public function translate_vc_blog( &$name, &$params, &$content ) {
		$name = 'us_blog';

		$params_rules = array(
			'type' => array(
				'new_name' => 'layout',
				'values' => array(
					'large_image' => 'large',
					'small_circle_image' => 'smallcircle',
					'small_square_image' => 'smallsquare',
					'masonry_paginated' => 'masonry',
				),
			),
			'show_date' => array(
				'values' => array(
					'yes' => NULL,
					NULL => '0',
				),
			),
			'show_author' => array(
				'values' => array(
					'yes' => NULL,
					NULL => '0',
				),
			),
			'show_categories' => array(
				'values' => array(
					'yes' => NULL,
					NULL => '0',
				),
			),
			'show_tags' => array(
				'values' => array(
					'yes' => NULL,
					NULL => '0',
				),
			),
			'show_comments' => array(
				'values' => array(
					'yes' => NULL,
					NULL => '0',
				),
			),
			'show_read_more' => array(
				'values' => array(
					'yes' => NULL,
					NULL => '0',
				),
			),
			'post_content' => array(
				'new_name' => 'content_type',
				'values' => array(
					'full' => 'content',
				),
			),
			'category' => array(
				'new_name' => 'categories',
			),
		);

		$this->translate_params( $params, $params_rules );

		return TRUE;
	}

	public function translate_vc_latest_posts( &$name, &$params, &$content ) {
		$name = 'us_blog';

		$params_rules = array(
			'category' => array(
				'new_name' => 'categories',
			),
		);

		$this->translate_params( $params, $params_rules );

		if ( isset( $params['posts'] ) AND in_array( $params['posts'], array( 1, 2, 3 ) ) ) {
			$params['columns'] = $params['posts'];
			$params['items'] = $params['posts'];
			unset( $params['posts'] );
		}

		$params['layout'] = 'latest';
		$params['show_date'] = '1';
		$params['show_author'] = '';
		$params['show_categories'] = '';
		$params['show_tags'] = '';
		$params['show_comments'] = '';
		$params['show_read_more'] = '';

		return TRUE;
	}

	public function translate_vc_button( &$name, &$params, &$content ) {
		$name = 'us_btn';

		$params_rules = array(
			'type' => array(
				'new_name' => 'color',
				'values' => array(
					'default' => 'light',
				),
			),
			'size' => array(
				'values' => array(
					'big' => 'large',
				),
			),
		);
		$this->translate_params( $params, $params_rules );

		if ( isset( $params['outlined'] ) AND empty( $params['outlined'] ) ) {
			unset( $params['outlined'] );
		} elseif ( isset( $params['outlined'] ) AND $params['outlined'] == 'yes' ) {
			$params['style'] = 'outlined';
			unset( $params['outlined'] );
		}

		$link = '';

		if ( isset( $params['url'] ) AND $params['url'] != '' ) {
			$link .= 'url:' . urlencode( $params['url'] );
			unset( $params['url'] );
		}
		if ( isset( $params['target'] ) AND ( $params['target'] == 1 OR $params['target'] == 'yes' ) ) {
			$link .= '|target:%20_blank';
			unset( $params['target'] );
		}

		$params['link'] = $link;

		return TRUE;
	}

	public function translate_vc_clients( &$name, &$params, &$content ) {
		$name = 'us_logos';

		$params_rules = array(
			'indents' => array(
				'new_name' => 'with_indents',
				'values' => array(
					'yes' => '1',
				),
			),
			'arrows' => array(
				'values' => array(
					'yes' => '1',
				),
			),
			'auto_scroll' => array(
				'values' => array(
					'yes' => '1',
				),
			),
		);

		$this->translate_params( $params, $params_rules );

		return TRUE;
	}

	public function translate_vc_counter( &$name, &$params, &$content ) {
		$name = 'us_counter';

		$params_rules = array(
			'number' => array(
				'new_name' => 'initial',
			),
			'count' => array(
				'new_name' => 'target',
			),
			'size' => array(
				'values' => array(
					'big' => 'large',
				),
			),
		);

		$this->translate_params( $params, $params_rules );

		return TRUE;
	}

	public function translate_vc_video( &$name, &$params, &$content ) {

		$params_rules = array(
			'ratio' => array(
				'values' => array(
					'16-9' => NULL,
					'4-3' => '4x3',
					'3-2' => '3x2',
					'1-1' => '1x1',
				),
			),
		);

		$shortcode_changed = $this->translate_params( $params, $params_rules );

		return $shortcode_changed;
	}

	public function translate_vc_contact_form( &$name, &$params ) {
		$name = 'us_cform';

		$params_rules = array(
			'form_email' => array(
				'new_name' => 'receiver_email',
			),
			'button_type' => array(
				'new_name' => 'button_style',
			),
		);

		$this->translate_params( $params, $params_rules );

		$fields = array(
			'form_name_field' => 'name_field',
			'form_email_field' => 'email_field',
			'form_phone_field' => 'phone_field',
			'form_message_field' => 'message_field',
		);
		foreach ( $fields as $field => $new_field ) {
			if ( isset( $params[ $field ] ) AND $params[ $field ] == 'show' ) {
				$params[ $new_field ] = 'shown';
				unset( $params[ $field ] );
			} elseif ( isset( $params[ $field ] ) AND $params[ $field ] == 'not_show' ) {
				$params[ $new_field ] = 'hidden';
				unset( $params[ $field ] );
			} elseif ( isset( $params[ $field ] ) AND $params[ $field ] == 'required' ) {
				unset( $params[ $field ] );
			}
		}

		if ( isset( $params['form_captcha'] ) AND $params['form_captcha'] == 'show' ) {
			$params['captcha_field'] = 'required';
		} elseif ( isset( $params['form_captcha'] ) AND $params['form_captcha'] == '' ) {
			unset( $params['form_captcha'] );
		}

		if ( isset( $params['button_size'] ) AND $params['button_size'] == 'big' ) {
			$params['button_size'] = 'large';
		}

		if ( isset( $params['button_color'] ) AND $params['button_color'] == 'default' ) {
			$params['button_color'] = 'light';
		}

		if ( isset( $params['button_outlined'] ) AND $params['button_outlined'] == 'yes' ) {
			$params['button_style'] = 'outlined';
		}

		return TRUE;
	}

	public function translate_vc_gallery( &$name, &$params, &$content ) {
		$name = 'us_gallery';

		if ( isset( $params['masonry'] ) AND $params['masonry'] == 'yes' ) {
			$params['layout'] = 'masonry';
			unset( $params['masonry'] );
		} elseif ( isset( $params['masonry'] ) AND empty( $params['masonry'] ) ) {
			unset( $params['masonry'] );
		}

		if ( isset( $params['indents'] ) AND $params['indents'] == 'yes' ) {
			$params['indents'] = '1';
		} elseif ( isset( $params['indents'] ) AND empty( $params['indents'] ) ) {
			unset( $params['indents'] );
		}

		return TRUE;
	}

	public function translate_vc_gmaps( &$name, &$params, &$content ) {
		$name = 'us_gmaps';

		$params_rules = array(
			'address' => array(
				'new_name' => 'marker_address',
			),
			'marker_2_address' => array(
				'new_name' => 'marker2_address',
			),
			'marker_3_address' => array(
				'new_name' => 'marker3_address',
			),
			'marker_4_address' => array(
				'new_name' => 'marker4_address',
			),
			'marker_5_address' => array(
				'new_name' => 'marker5_address',
			),
			'type' => array(
				'values' => array(
					'ROADMAP' => 'roadmap',
					'SATELLITE' => 'satellite',
					'HYBRID' => 'hybrid',
					'TERRAIN' => 'terrain',
				),
			),
			'add_markers' => array(
				'values' => array(
					'yes' => '1',
				),
			),
		);

		$this->translate_params( $params, $params_rules );

		if ( isset( $params['marker'] ) AND $params['marker'] != '' ) {
			$params['marker_text'] = base64_encode( $params['marker'] );
			unset( $params['marker'] );
		}

		if ( isset( $params['marker_2'] ) AND $params['marker_2'] != '' ) {
			$params['marker2_text'] = base64_encode( $params['marker_2'] );
			unset( $params['marker_2'] );
		}

		if ( isset( $params['marker_3'] ) AND $params['marker_3'] != '' ) {
			$params['marker3_text'] = base64_encode( $params['marker_3'] );
			unset( $params['marker_3'] );
		}

		if ( isset( $params['marker_4'] ) AND $params['marker_4'] != '' ) {
			$params['marker4_text'] = base64_encode( $params['marker_4'] );
			unset( $params['marker_4'] );
		}

		if ( isset( $params['marker_5'] ) AND $params['marker_5'] != '' ) {
			$params['marker5_text'] = base64_encode( $params['marker_5'] );
			unset( $params['marker_5'] );
		}

		return TRUE;
	}

	public function translate_vc_icon( &$name, &$params, &$content ) {

		$params_rules = array(
			'size' => array(
				'values' => array(
					'tiny' => 'xs',
					'small' => 'sm',
					'medium' => 'md',
					'big' => 'lg',
					'huge' => 'xl',
				),
			),
		);

		$this->translate_params( $params, $params_rules );

		if ( isset( $params['icon'] ) AND $params['icon'] != '' ) {
			$params['icon_fontawesome'] = "fa fa-" . $params['icon'];
			unset( $params['icon'] );
		}

		if ( isset( $params['color'] ) AND $params['color'] == '' ) {
			$params['color'] = 'custom';
			$params['custom_color'] = us_get_option( 'color_content_text' );
			if ( isset( $params['with_circle'] ) AND $params['with_circle'] == 'yes' ) {
				$params['background_color'] = 'custom';
				$params['custom_background_color'] = us_get_option( 'color_content_bg_alt' );
			}
		} elseif ( isset( $params['color'] ) AND $params['color'] == 'primary' ) {
			$params['color'] = 'custom';
			$params['custom_color'] = us_get_option( 'color_content_primary' );
			if ( isset( $params['with_circle'] ) AND $params['with_circle'] == 'yes' ) {
				$params['custom_color'] = '#fff';
				$params['background_color'] = 'custom';
				$params['custom_background_color'] = us_get_option( 'color_content_primary' );
			}
		} elseif ( isset( $params['color'] ) AND $params['color'] == 'secondary' ) {
			$params['color'] = 'custom';
			$params['custom_color'] = us_get_option( 'color_content_secondary' );
			if ( isset( $params['with_circle'] ) AND $params['with_circle'] == 'yes' ) {
				$params['custom_color'] = '#fff';
				$params['background_color'] = 'custom';
				$params['custom_background_color'] = us_get_option( 'color_content_secondary' );
			}
		} elseif ( isset( $params['color'] ) AND $params['color'] == 'fade' ) {
			$params['color'] = 'custom';
			$params['custom_color'] = us_get_option( 'color_content_faded' );
			if ( isset( $params['with_circle'] ) AND $params['with_circle'] == 'yes' ) {
				$params['background_color'] = 'custom';
				$params['custom_background_color'] = us_get_option( 'color_content_bg_alt' );
			}
		} elseif ( isset( $params['color'] ) AND $params['color'] == 'border' ) {
			$params['color'] = 'custom';
			$params['custom_color'] = us_get_option( 'color_content_border' );
			if ( isset( $params['with_circle'] ) AND $params['with_circle'] == 'yes' ) {
				$params['custom_color'] = '#fff';
				$params['background_color'] = 'custom';
				$params['custom_background_color'] = us_get_option( 'color_content_border' );
			}
		}

		if ( isset( $params['with_circle'] ) ) {
			if ( $params['with_circle'] == 'yes' ) {
				$params['background_style'] = 'rounded';
			}
			unset( $params['with_circle'] );
		}

		$link = '';

		if ( isset( $params['link'] ) AND $params['link'] != '' ) {
			$link .= 'url:' . urlencode( $params['link'] );
			unset( $params['link'] );
		}
		if ( isset( $params['external'] ) AND $params['external'] == 1 ) {
			$link .= '|target:%20_blank';
			unset( $params['external'] );
		}

		$params['link'] = $link;

		return TRUE;
	}

	public function translate_vc_iconbox( &$name, &$params, &$content ) {
		$name = 'us_iconbox';

		if ( ! isset( $params['icon_style'] ) AND isset( $params['with_circle'] ) AND $params['with_circle'] == 'yes' ) {
			$params['icon_style'] = 'circle_outlined';
		}

		$params_rules = array(
			'icon_style' => array(
				'new_name' => 'style',
				'values' => array(
					'default' => NULL,
					'circle_solid' => 'circle',
					'circle_outlined' => 'outlined',
				),
			),
			'size' => array(
				'values' => array(
					'big' => 'large',
				),
			),
		);

		$this->translate_params( $params, $params_rules );

		$link = '';

		if ( isset( $params['link'] ) AND $params['link'] != '' ) {
			$link .= 'url:' . urlencode( $params['link'] );
			unset( $params['link'] );
		}
		if ( isset( $params['external'] ) AND $params['external'] == 1 ) {
			$link .= '|target:%20_blank';
			unset( $params['external'] );
		}

		$params['link'] = $link;

		return TRUE;
	}

	public function translate_vc_simple_slider( &$name, &$params, &$content ) {
		$name = 'us_image_slider';

		$params_rules = array(
			'auto_rotation' => array(
				'new_name' => 'autoplay',
				'values' => array(
					'yes' => '1',
				),
			),
			'fullscreen' => array(
				'values' => array(
					'yes' => '1',
				),
			),
			'stretch' => array(
				'new_name' => 'img_fit',
				'values' => array(
					'yes' => '1',
				),
			),
		);

		$this->translate_params( $params, $params_rules );

		if ( isset( $params['transition'] ) AND $params['transition'] == 'fade' ) {
			$params['transition'] = 'crossfade';
		} elseif ( isset( $params['transition'] ) AND $params['transition'] == 'dissolve' ) {
			$params['transition'] = 'crossfade';
		}

		if ( isset( $params['stretch'] ) AND $params['stretch'] == 'yes' ) {
			$params['img_fit'] = 'cover';
			unset( $params['stretch'] );
		}

		return TRUE;
	}

	public function translate_vc_single_image( &$name, &$params, &$content ) {
		$name = 'us_single_image';

		$params_rules = array(
			'img_size' => array(
				'new_name' => 'size',
			),
			'img_link_large' => array(
				'new_name' => 'lightbox',
				'values' => array(
					'' => NULL,
					'yes' => '1',
				),
			),
		);

		$this->translate_params( $params, $params_rules );

		$link = '';

		if ( isset( $params['img_link'] ) AND $params['img_link'] != '' ) {
			$link .= 'url:' . urlencode( $params['img_link'] );
			unset( $params['img_link'] );
		}
		if ( isset( $params['img_link_new_tab'] ) AND ( $params['img_link_new_tab'] == 1 OR $params['img_link_new_tab'] == 'yes' ) ) {
			$link .= '|target:%20_blank';
			unset( $params['img_link_new_tab'] );
		}

		$params['link'] = $link;

		return TRUE;
	}

	public function translate_vc_separator( &$name, &$params, &$content ) {
		$name = 'us_separator';

		if ( isset( $params['size'] ) AND $params['size'] == 'big' ) {
			$params['size'] = 'large';
		} elseif ( isset( $params['size'] ) AND $params['size'] == '' ) {
			unset( $params['size'] );
		}

		return TRUE;
	}

	public function translate_vc_testimonial( &$name, &$params, &$content ) {
		$name = 'us_testimonial';

		return TRUE;
	}

	public function translate_vc_contacts( &$name, &$params, &$content ) {
		$name = 'us_contacts';

		return TRUE;
	}

	public function translate_vc_member( &$name, &$params, &$content ) {
		$name = 'us_person';

		$params_rules = array(
			'img' => array(
				'new_name' => 'image',
			),
		);

		$this->translate_params( $params, $params_rules );

		$link = '';

		if ( isset( $params['link'] ) AND $params['link'] != '' ) {
			$link .= 'url:' . urlencode( $params['link'] );
			unset( $params['link'] );
		}
		if ( isset( $params['external'] ) AND ( $params['external'] == 1 OR $params['external'] == 'yes' ) ) {
			$link .= '|target:%20_blank';
			unset( $params['external'] );
		}

		$params['link'] = $link;

		return TRUE;
	}

	public function translate_vc_message( &$name, &$params, &$content ) {
		$name = 'us_message';

		$params_rules = array(
			'closing' => array(
				'values' => array(
					'' => NULL,
					'yes' => '1',
				),
			),
		);

		$this->translate_params( $params, $params_rules );

		return TRUE;
	}

	public function translate_vc_portfolio( &$name, &$params, &$content ) {
		$name = 'us_portfolio';

		$params_rules = array(
			'style' => array(
				'values' => array(
					'type_1' => 'style_1',
					'type_2' => 'style_2',
					'type_3' => 'style_3',
					'type_4' => 'style_4',
					'type_5' => 'style_5',
					'type_6' => 'style_6',
					'type_7' => 'style_7',
					'type_8' => 'style_8',
					'type_9' => 'style_9',
					'type_10' => 'style_10',
					'type_11' => 'style_11',
					'type_12' => 'style_12',
					'type_13' => 'style_13',
					'type_14' => 'style_14',
					'type_15' => 'style_15',
				),
			),
			'ratio' => array(
				'values' => array(
					'3:2' => '3x2',
					'4:3' => '4x3',
					'1:1' => '1x1',
					'2:3' => '2x3',
					'3:4' => '3x4',
				),
			),
			'meta' => array(
				'values' => array(
					'category' => 'categories',
				),
			),
			'filters' => array(
				'new_name' => 'filter',
				'values' => array(
					'' => NULL,
					'yes' => 'category',
				),
			),
			'with_indents' => array(
				'values' => array(
					'' => NULL,
					'yes' => '1',
				),
			),
			'random_order' => array(
				'new_name' => 'orderby',
				'values' => array(
					'' => NULL,
					'yes' => 'rand',
				),
			),
			'category' => array(
				'new_name' => 'categories',
			),
		);

		$this->translate_params( $params, $params_rules );

		if ( ( ! isset( $params['items'] ) OR $params['items'] == '' ) AND ( ! empty( $params['columns'] ) ) ) {
			$params['items'] = $params['columns'];
		}

		return TRUE;
	}

	public function translate_pricing_table( &$name, &$params, &$content ) {
		$name = 'us_pricing';

		$items = array();

		$shortcode_pattern = $this->get_shortcode_regex( array( 'pricing_column', 'pricing_row', 'pricing_footer' ) );
		if ( preg_match_all( '/' . $shortcode_pattern . '/s', $content, $matches ) ) {
			if ( count( $matches[2] ) ) {
				foreach ( $matches[2] as $i => $shortcode_name ) {
					if ( $shortcode_name == 'pricing_column' ) {
						$item = array();
						$shortcode_params = shortcode_parse_atts( $matches[3][ $i ] );
						$shortcode_content = $matches[5][ $i ];

						if ( ! empty( $shortcode_params['title'] ) ) {
							$item['title'] = $shortcode_params['title'];
						}
						if ( ! empty( $shortcode_params['price'] ) ) {
							$item['price'] = $shortcode_params['price'];
						}
						if ( ! empty( $shortcode_params['time'] ) ) {
							$item['substring'] = $shortcode_params['time'];
						}
						if ( ! empty( $shortcode_params['type'] ) AND $shortcode_params['type'] == 'featured' ) {
							$item['type'] = 'featured';
						}

						$item['features'] = '';

						if ( preg_match_all( '/' . $shortcode_pattern . '/s', $shortcode_content, $item_matches ) ) {
							if ( count( $item_matches[2] ) ) {
								foreach ( $item_matches[2] as $j => $item_shortcode_name ) {
									if ( $item_shortcode_name == 'pricing_row' ) {
										$item['features'] .= $item_matches[5][ $j ] . "\n";
									}

									if ( $item_shortcode_name == 'pricing_footer' ) {
										$footer_shortcode_params = shortcode_parse_atts( $item_matches[3][ $j ] );

										$item['btn_text'] = $item_matches[5][ $j ];

										$item['btn_link'] = '';

										if ( isset( $footer_shortcode_params['url'] ) AND $footer_shortcode_params['url'] != '' ) {
											$item['btn_link'] .= 'url:' . urlencode( $footer_shortcode_params['url'] );
										}
										if ( isset( $footer_shortcode_params['external'] ) AND ( $footer_shortcode_params['external'] == 1 OR $footer_shortcode_params['external'] == 'yes' ) ) {
											$item['btn_link'] .= '|target:%20_blank';
										}

										if ( isset( $footer_shortcode_params['type'] ) AND $footer_shortcode_params['type'] != '' ) {
											$item['btn_color'] = $footer_shortcode_params['type'];
											if ( $item['btn_color'] == 'default' ) {
												$item['btn_color'] = 'light';
											}
										}

										if ( isset( $footer_shortcode_params['outlined'] ) AND $footer_shortcode_params['outlined'] == 1 ) {
											$item['btn_style'] = 'outlined';
										} else {
											$item['btn_style'] = 'solid';
										}

										if ( isset( $footer_shortcode_params['icon'] ) AND $footer_shortcode_params['icon'] != '' ) {
											$item['btn_icon'] = $footer_shortcode_params['icon'];
											$item['btn_iconpos'] = 'left';
										}

										if ( isset( $footer_shortcode_params['size'] ) AND $footer_shortcode_params['size'] != '' ) {
											$item['btn_size'] = $footer_shortcode_params['size'];
											if ( $item['btn_size'] == 'big' ) {
												$item['btn_size'] = 'large';
											}
										}
									}
								}
							}
						}

						$items[] = $item;
					}
				}
			}
		}

		$params['items'] = rawurlencode( json_encode( $items ) );

		$content = '';

		return TRUE;
	}

	public function translate_vc_social_links( &$name, &$params, &$content ) {
		$name = 'us_social_links';

		$params_rules = array(
			'size' => array(
				'values' => array(
					'normal' => 'medium',
					'big' => 'large',
				),
			),
		);

		$this->translate_params( $params, $params_rules );

		return TRUE;
	}

	public function translate_vc_custom_heading( &$name, &$params ) {
		if ( ! isset( $params['google_fonts'] ) OR empty( $params['google_fonts'] ) ) {
			$heading_font = us_get_option( 'heading_font_family' );
			if ( empty( $heading_font ) ) {
				return FALSE;
			}
			$font_config = us_config( 'google-fonts.' . $heading_font );
			if ( empty( $font_config ) OR ! is_array( $font_config ) ) {
				return FALSE;
			}
			$vc_font_value = array(
				'font_family' => array(),
				'font_style' => rawurlencode( '400 regular:400:normal' ),
			);
			foreach ( $font_config['variants'] as $font_family ) {
				if ( $font_family == '400' ) {
					$font_family = 'regular';
				} elseif ( $font_family == '400italic' ) {
					$font_family = 'italic';
				}
				$vc_font_value['font_family'][] = $font_family;
			}
			$vc_font_value['font_family'] = rawurlencode( $heading_font . ':' . implode( ',', $vc_font_value['font_family'] ) );
			foreach ( array( 200, 300, 400, 600, 700 ) as $weight ) {
				if ( us_get_option( 'heading_font_weight_' . $weight ) ) {
					$vc_font_value['font_style'] = rawurlencode( $weight . ' regular:' . $weight . ':normal' );
					break;
				}
			}
			$params['google_fonts'] = 'font_family:' . $vc_font_value['font_family'] . '|font_style:' . $vc_font_value['font_style'];

			return TRUE;
		}

		return FALSE;
	}

	public function get_shortcode_regex( $tagnames = NULL ) {
		if ( empty( $tagnames ) OR ! is_array( $tagnames ) ) {
			// Retrieving list of possible shortcode translations from the class methods
			$tagnames = array();
			foreach ( get_class_methods( $this ) as $method_name ) {
				if ( substr( $method_name, 0, 10 ) != 'translate_' ) {
					continue;
				}
				$tagname = substr( $method_name, 10 );
				if ( ! in_array( $tagname, array( 'menus', 'params', 'content', 'theme_options', 'meta' ) ) ) {
					$tagnames[] = $tagname;
				}
			}

			$tagnames[] = 'vc_column_text';
		}

		$tagregexp = join( '|', array_map( 'preg_quote', $tagnames ) );

		// WARNING! Do not change this regex without changing do_shortcode_tag() and strip_shortcode_tag()
		// Also, see shortcode_unautop() and shortcode.js.
		return '\\[' // Opening bracket
		       . '(\\[?)' // 1: Optional second opening bracket for escaping shortcodes: [[tag]]
		       . "($tagregexp)" // 2: Shortcode name
		       . '(?![\\w-])' // Not followed by word character or hyphen
		       . '(' // 3: Unroll the loop: Inside the opening shortcode tag
		       . '[^\\]\\/]*' // Not a closing bracket or forward slash
		       . '(?:' . '\\/(?!\\])' // A forward slash not followed by a closing bracket
		       . '[^\\]\\/]*' // Not a closing bracket or forward slash
		       . ')*?' . ')' . '(?:' . '(\\/)' // 4: Self closing tag ...
		       . '\\]' // ... and closing bracket
		       . '|' . '\\]' // Closing bracket
		       . '(?:' . '(' // 5: Unroll the loop: Optionally, anything between the opening and closing shortcode tags
		       . '[^\\[]*+' // Not an opening bracket
		       . '(?:' . '\\[(?!\\/\\2\\])' // An opening bracket not followed by the closing shortcode tag
		       . '[^\\[]*+' // Not an opening bracket
		       . ')*+' . ')' . '\\[\\/\\2\\]' // Closing shortcode tag
		       . ')?' . ')' . '(\\]?)'; // 6: Optional second closing brocket for escaping shortcodes: [[tag]]
	}
}

