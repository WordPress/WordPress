<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

/**
 * Get header option for the specified state
 *
 * @param string $name Option name
 * @param string $state Header state: 'default' / 'tablets' / 'mobiles'
 * @param string $default
 *
 * @return string
 */
function us_get_header_option( $name, $state = 'default', $default = NULL ) {
	global $us_header_settings;
	us_load_header_settings_once();
	$shared_options = array( 'top_fullwidth', 'middle_fullwidth', 'bottom_fullwidth' );
	if ( $state != 'default' AND ( ! isset( $us_header_settings[ $state ]['options'][ $name ] ) OR in_array( $name, $shared_options ) ) ) {
		$state = 'default';
	}

	return isset( $us_header_settings[ $state ]['options'][ $name ] ) ? $us_header_settings[ $state ]['options'][ $name ] : $default;
}

/**
 * Get header layout for the specified state
 *
 * @param $state
 *
 * @return array
 */
function us_get_header_layout( $state = 'default' ) {
	global $us_header_settings;
	us_load_header_settings_once();
	$layout = array(
		'top_left' => array(),
		'top_center' => array(),
		'top_right' => array(),
		'middle_left' => array(),
		'middle_center' => array(),
		'middle_right' => array(),
		'bottom_left' => array(),
		'bottom_center' => array(),
		'bottom_right' => array(),
		'hidden' => array(),
	);
	if ( $state != 'default' AND isset( $us_header_settings['default']['layout'] ) AND is_array( $us_header_settings['default']['layout'] ) ) {
		$layout = array_merge( $layout, $us_header_settings['default']['layout'] );
	}
	if ( isset( $us_header_settings[ $state ]['layout'] ) AND is_array( $us_header_settings[ $state ]['layout'] ) ) {
		$layout = array_merge( $layout, $us_header_settings[ $state ]['layout'] );
	}

	return $layout;
}

/**
 * Load the current header settings for all possible responsive states
 */
function us_load_header_settings_once() {

	global $us_header_settings;

	if ( isset( $us_header_settings ) ) {
		return;
	}
	// Basic structure
	$us_header_settings = array(
		'default' => array( 'options' => array(), 'layout' => array() ),
		'tablets' => array( 'options' => array(), 'layout' => array() ),
		'mobiles' => array( 'options' => array(), 'layout' => array() ),
		'data' => array(),
	);
	$us_header_settings = apply_filters( 'us_load_header_settings', $us_header_settings );
}

/**
 * Translating the v.2 USOF header settings to v.3 header-builder compatible ones
 */
add_filter( 'us_load_header_settings', 'us_load_usof_header_settings' );
function us_load_usof_header_settings( $header_settings ) {

	global $usof_options;
	usof_load_options_once();

	// Side options defaults from theme-options.php config
	$side_options_config = us_config( 'header-settings.options', array() );
	foreach ( $side_options_config as $section_name => $section_options ) {
		foreach ( $section_options as $field_name => $field ) {
			$field_value = isset( $field['std'] ) ? $field['std'] : '';
			$header_settings['default']['options'][ $field_name ] = $field_value;
			$header_settings['tablets']['options'][ $field_name ] = $field_value;
			$header_settings['mobiles']['options'][ $field_name ] = $field_value;
		}
	}

	// Layout-defined values
	$header_templates = us_config( 'header-templates', array() );
	if ( isset( $usof_options['header_layout'] ) AND isset( $header_templates[ $usof_options['header_layout'] ] ) ) {
		$header_template = us_fix_header_template_settings( $header_templates[ $usof_options['header_layout'] ] );
		$header_settings = us_array_merge( $header_settings, $header_template );
	}

	// Filling elements' data with default values
	$header_settings = us_fix_header_settings( $header_settings );

	// Side options
	$rules = array(
		'header_transparent' => array(
			'new_name' => 'transparent',
		),
		'header_fullwidth' => array(
			'new_names' => array( 'top_fullwidth', 'middle_fullwidth', 'bottom_fullwidth' ),
		),
		'header_top_height' => array(
			'new_name' => 'top_height',
		),
		'header_top_sticky_height' => array(
			'new_name' => 'top_sticky_height',
		),
		'header_middle_height' => array(
			'new_name' => 'middle_height',
		),
		'header_middle_sticky_height' => array(
			'new_name' => 'middle_sticky_height',
		),
		'header_bottom_height' => array(
			'new_name' => 'bottom_height',
		),
		'header_bottom_sticky_height' => array(
			'new_name' => 'bottom_sticky_height',
		),
		'header_main_width' => array(
			'new_name' => 'width',
		),
		'header_scroll_breakpoint' => array(
			'new_name' => 'scroll_breakpoint',
		),
	);

	foreach ( $rules as $old_name => $rule ) {
		if ( ! isset( $usof_options[ $old_name ] ) AND ( isset( $rule['new_name'] ) OR isset( $rule['new_names'] ) ) ) {
			continue;
		}
		if ( isset( $rule['transfer_if'] ) AND ! usof_execute_show_if( $rule['transfer_if'], $usof_options ) ) {
			continue;
		}
		$new_names = isset( $rule['new_names'] ) ? $rule['new_names'] : array( $rule['new_name'] );
		foreach ( $new_names as $new_name ) {
			$header_settings['default']['options'][ $new_name ] = $usof_options[ $old_name ];
		}
	}

	// header_sticky => sticky
	if ( isset( $usof_options['header_sticky'] ) ) {
		if ( is_array( $usof_options['header_sticky'] ) ) {
			foreach ( array( 'default', 'tablets', 'mobiles' ) as $layout ) {
				$header_settings[ $layout ]['options']['sticky'] = in_array( $layout, $usof_options['header_sticky'] );
			}
		} else {
			$header_settings['default']['options']['sticky'] = $usof_options['header_sticky'];
			$header_settings['tablets']['options']['sticky'] = $usof_options['header_sticky'];
			$header_settings['mobiles']['options']['sticky'] = $usof_options['header_sticky'];
		}
	}

	// Transferring elements' values
	$rules = array(
		'image:1' => array(
			'show_if' => array( 'logo_type', '=', 'img' ),
			'values' => array(
				'img' => '=logo_image',
				'link' => function_exists( 'icl_get_home_url' ) ? icl_get_home_url() : esc_url( home_url( '/' ) ),
				'img_transparent' => '=logo_image_transparent',
				'height' => '=logo_height',
				'height_tablets' => '=logo_height_tablets',
				'height_mobiles' => '=logo_height_mobiles',
				'height_sticky' => '=logo_height_sticky',
				'height_sticky_tablets' => '=logo_height_tablets',
				'height_sticky_mobiles' => '=logo_height_mobiles',
			),
		),
		'text:1' => array(
			'show_if' => array( 'logo_type', '=', 'text' ),
			'values' => array(
				'text' => '=logo_text',
				'link' => function_exists( 'icl_get_home_url' ) ? icl_get_home_url() : esc_url( home_url( '/' ) ),
				'size' => '=logo_font_size',
				'size_tablets' => '=logo_font_size_tablets',
				'size_mobiles' => '=logo_font_size_mobiles',
			),
		),
		'text:2' => array(
			'show_if' => array(
				array( 'header_contacts_show', '=', 1 ),
				'and',
				array( 'header_contacts_phone', '!=', '' ),
			),
			'values' => array(
				'text' => '=header_contacts_phone',
				'icon' => 'phone',
			),
		),
		'text:3' => array(
			'show_if' => array(
				array( 'header_contacts_show', '=', 1 ),
				'and',
				array( 'header_contacts_email', '!=', '' ),
			),
			'values' => array(
				'text' => '=header_contacts_email',
				'icon' => 'envelope',
			),
		),
		'text:4' => array(
			'show_if' => array(
				array( 'header_contacts_show', '=', 1 ),
				'and',
				array( 'header_contacts_custom_icon', '!=', '' ),
				'and',
				array( 'header_contacts_custom_text', '!=', '' ),
			),
			'values' => array(
				'text' => '=header_contacts_custom_text',
				'icon' => '=header_contacts_custom_icon',
				'link' => '',
			),
		),
		'menu:1' => array(
			'values' => array(
				'font_size' => '=menu_fontsize',
				'indents' => '=menu_indents',
				'vstretch' => '=menu_height',
				'hover_effect' => '=menu_hover_effect',
				'dropdown_effect' => '=menu_dropdown_effect',
				'dropdown_font_size' => '=menu_sub_fontsize',
				'mobile_width' => '=menu_mobile_width',
				'mobile_behavior' => '=menu_togglable_type',
				'mobile_font_size' => '=menu_fontsize_mobile',
				'mobile_dropdown_font_size' => '=menu_sub_fontsize_mobile',
			),
		),
		'search:1' => array(
			'show_if' => array( 'header_search_show', '=', 1 ),
			'values' => array(
				'layout' => '=header_search_layout',
			),
		),
		'socials:1' => array(
			'show_if' => array( 'header_socials_show', '=', 1 ),
			'values' => array(
				'facebook' => '=header_socials_facebook',
				'twitter' => '=header_socials_twitter',
				'google' => '=header_socials_google',
				'linkedin' => '=header_socials_linkedin',
				'youtube' => '=header_socials_youtube',
				'vimeo' => '=header_socials_vimeo',
				'flickr' => '=header_socials_flickr',
				'instagram' => '=header_socials_instagram',
				'behance' => '=header_socials_behance',
				'xing' => '=header_socials_xing',
				'pinterest' => '=header_socials_pinterest',
				'skype' => '=header_socials_skype',
				'tumblr' => '=header_socials_tumblr',
				'dribbble' => '=header_socials_dribbble',
				'vk' => '=header_socials_vk',
				'soundcloud' => '=header_socials_soundcloud',
				'yelp' => '=header_socials_yelp',
				'twitch' => '=header_socials_twitch',
				'deviantart' => '=header_socials_deviantart',
				'foursquare' => '=header_socials_foursquare',
				'github' => '=header_socials_github',
				'odnoklassniki' => '=header_socials_odnoklassniki',
				's500px' => '=header_socials_s500px',
				'houzz' => '=header_socials_houzz',
				'medium' => '=header_socials_medium',
				'tripadvisor' => '=header_socials_tripadvisor',
				'rss' => '=header_socials_rss',
				'custom_icon' => '=header_socials_custom_icon',
				'custom_url' => '=header_socials_custom_url',
				'custom_color' => '=header_socials_custom_color',
			),
		),
		'dropdown:1' => array(
			'show_if' => array( 'header_language_show', '=', 1 ),
			'values' => array(
				'source' => '=header_language_source',
				'link_title' => '=header_link_title',
				'link_qty' => '=header_link_qty',
				'link_1_label' => '=header_link_1_label',
				'link_1_url' => '=header_link_1_url',
				'link_2_label' => '=header_link_2_label',
				'link_2_url' => '=header_link_2_url',
				'link_3_label' => '=header_link_3_label',
				'link_3_url' => '=header_link_3_url',
				'link_4_label' => '=header_link_4_label',
				'link_4_url' => '=header_link_4_url',
				'link_5_label' => '=header_link_5_label',
				'link_5_url' => '=header_link_5_url',
				'link_6_label' => '=header_link_6_label',
				'link_6_url' => '=header_link_6_url',
				'link_7_label' => '=header_link_7_label',
				'link_7_url' => '=header_link_7_url',
				'link_8_label' => '=header_link_8_label',
				'link_8_url' => '=header_link_8_url',
				'link_9_label' => '=header_link_9_label',
				'link_9_url' => '=header_link_9_url',
			),
		),
	);

	// Elements defaults from theme-options.php config
	$elms_config = us_config( 'header-settings.elements', array() );

	foreach ( $rules as $elm => $rule ) {
		if ( ! isset( $header_settings['data'][ $elm ] ) ) {
			$header_settings['data'][ $elm ] = array();
			$type = strtok( $elm, ':' );
			// Setting default values for fallback
			if ( isset( $elms_config[ $type ] ) AND isset( $elms_config[ $type ]['params'] ) ) {
				foreach ( $elms_config[ $type ]['params'] as $field_name => &$field ) {
					$header_settings['data'][ $elm ][ $field_name ] = isset( $field['std'] ) ? $field['std'] : '';
				}
			}
		}
		// Setting values
		if ( isset( $rule['values'] ) AND is_array( $rule['values'] ) ) {
			foreach ( $rule['values'] as $key => $value ) {
				if ( is_string( $value ) AND substr( $value, 0, 1 ) == '=' ) {
					$old_key = substr( $value, 1 );
					if ( ! isset( $usof_options[ $old_key ] ) ) {
						continue;
					}
					$value = $usof_options[ $old_key ];
				}
				$header_settings['data'][ $elm ][ $key ] = $value;
			}
		}
		// Hiding the element when needed
		if ( isset( $rule['show_if'] ) AND ! usof_execute_show_if( $rule['show_if'], $usof_options ) ) {
			foreach ( array( 'default', 'tablets', 'mobiles' ) as $layout ) {
				foreach ( $header_settings[ $layout ]['layout'] as $cell => $cell_elms ) {
					if ( $cell == 'hidden' ) {
						continue;
					}
					if ( ( $elm_pos = array_search( $elm, $cell_elms ) ) !== FALSE ) {
						array_splice( $header_settings[ $layout ]['layout'][ $cell ], $elm_pos, 1 );
						$header_settings[ $layout ]['layout']['hidden'][] = $elm;
						break;
					}
				}
			}
		}
	}

	// Logos for tablets and mobiles states
	if ( isset( $header_settings['data']['image:1'] ) ) {
		foreach ( array( 'tablets' => 'image:2', 'mobiles' => 'image:3' ) as $layout => $key ) {
			if ( isset( $header_settings['data'][ $key ] ) OR ! isset( $usof_options[ 'logo_image_' . $layout ] ) OR empty( $usof_options[ 'logo_image_' . $layout ] ) ) {
				continue;
			}
			$header_settings['data'][ $key ] = array_merge( $header_settings['data']['image:1'], array(
				'img' => $usof_options[ 'logo_image_' . $layout ],
				'img_transparent' => '',
			) );
			foreach ( $header_settings[ $layout ]['layout'] as $cell => $cell_elms ) {
				if ( $cell == 'hidden' ) {
					continue;
				}
				if ( ( $elm_pos = array_search( 'image:1', $cell_elms ) ) !== FALSE ) {
					$header_settings[ $layout ]['layout'][ $cell ][ $elm_pos ] = $key;
					$header_settings[ $layout ]['layout']['hidden'][] = 'image:1';
					break;
				}
			}
			$header_settings['default']['layout']['hidden'][] = $key;
			$header_settings[ ( $layout == 'tablets' ) ? 'mobiles' : 'tablets' ]['layout']['hidden'][] = $key;
		}
	}

	// Fixing text links
	if ( isset( $header_settings['data']['text:3'] ) AND isset( $header_settings['data']['text:3']['text'] ) ) {
		$header_settings['data']['text:3']['link'] = 'mailto:' . $header_settings['data']['text:3']['text'];
	}

	// Inverting logo position
	if ( isset( $usof_options['header_invert_logo_pos'] ) AND $usof_options['header_invert_logo_pos'] ) {
		foreach ( array( 'default', 'tablets', 'mobiles' ) as $layout ) {
			if ( isset( $header_settings[ $layout ]['layout']['middle_left'] ) AND isset( $header_settings[ $layout ]['layout']['middle_left'] ) ) {
				$tmp = $header_settings[ $layout ]['layout']['middle_left'];
				$header_settings[ $layout ]['layout']['middle_left'] = $header_settings[ $layout ]['layout']['middle_right'];
				$header_settings[ $layout ]['layout']['middle_right'] = $tmp;
			}
		}
	}

	return $header_settings;
}

add_filter( 'us_load_header_settings', 'us_load_metabox_header_settings', 20 );
function us_load_metabox_header_settings( $header_settings ) {
	if ( is_singular( array( 'post', 'page', 'us_portfolio', 'product' ) ) ) {
		foreach ( array( 'default', 'tablets', 'mobiles' ) as $state ) {
			if ( us_arr_path( $header_settings, $state . '.options.orientation' ) == 'hor' ) {
				if ( usof_meta( 'us_header_bg' ) != '' ) {
					$header_settings[ $state ]['options']['transparent'] = intval( usof_meta( 'us_header_bg' ) == 'transparent' );
				}
				if ( usof_meta( 'us_header_pos' ) != '' ) {
					$header_settings[ $state ]['options']['sticky'] = intval( usof_meta( 'us_header_pos' ) == 'fixed' );
				}
			}
		}
	}

	return $header_settings;
}

/**
 * Recursively output elements of a certain cell
 *
 * @param array $layout Current layout
 * @param array $data Elements' data
 * @param string $place Outputted place
 */
function us_output_header_elms( &$layout, &$data, $place ) {
	if ( ! isset( $layout[ $place ] ) OR ! is_array( $layout[ $place ] ) ) {
		return;
	}
	foreach ( $layout[ $place ] as $elm ) {
		if ( substr( $elm, 1, 7 ) == 'wrapper' ) {
			// Wrapper
			$type = strtok( $elm, ':' );
			$classes = '';
			if ( isset( $data[ $elm ] ) ) {
				if ( isset( $data[ $elm ]['alignment'] ) ) {
					$classes .= ' align_' . $data[ $elm ]['alignment'];
				}
				if ( us_arr_path( $data[ $elm ], 'design_options.hide_for_sticky', FALSE ) ) {
					$classes .= ' hide-for-sticky';
				}
			}
			echo '<div class="w-' . $type . $classes . ' ush_' . str_replace( ':', '_', $elm ) . '">';
			us_output_header_elms( $layout, $data, $elm );
			echo '</div>';
		} else {
			// Element
			$type = strtok( $elm, ':' );
			$defaults = us_get_header_elm_defaults( $type );
			if ( ! isset( $data[ $elm ] ) ) {
				$data[ $elm ] = us_get_header_elm_defaults( $type );
			}
			$values = array_merge( $defaults, array_intersect_key( $data[ $elm ], $defaults ) );
			$values['id'] = $elm;
			us_load_template( 'templates/elements/' . $type, $values );
		}
	}
}

function us_get_header_elm_defaults( $type ) {
	global $us_header_elm_defaults, $usof_options;
	usof_load_options_once();
	if ( ! isset( $us_header_elm_defaults ) ) {
		$us_header_elm_defaults = array();
	}
	if ( ! isset( $us_header_elm_defaults[ $type ] ) ) {
		foreach ( us_config( 'header-settings.elements.' . $type . '.params', array() ) as $field_name => $field ) {
			$value = isset( $field['std'] ) ? $field['std'] : '';
			// Some default values may be based on main theme options' values
			if ( is_string( $value ) AND substr( $value, 0, 1 ) == '=' AND isset( $usof_options[ substr( $value, 1 ) ] ) ) {
				$value = $usof_options[ substr( $value, 1 ) ];
			}
			$us_header_elm_defaults[ $type ][ $field_name ] = $value;
		}
	}

	return $us_header_elm_defaults[ $type ];
}

/**
 * Get elements
 *
 * @param string $type
 * @param bool $key_as_class Should the keys of the resulting array be css classes instead of elms ids?
 *
 * @return array
 */
function us_get_header_elms_of_a_type( $type, $key_as_class = TRUE ) {
	global $us_header_settings;
	us_load_header_settings_once();
	$defaults = us_get_header_elm_defaults( $type );
	$result = array();
	foreach ( $us_header_settings['data'] as $elm_id => $elm ) {
		if ( strtok( $elm_id, ':' ) != $type ) {
			continue;
		}
		$key = $key_as_class ? ( 'ush_' . str_replace( ':', '_', $elm_id ) ) : $elm_id;
		$result[ $key ] = array_merge( $defaults, array_intersect_key( $elm, $defaults ) );
	}

	return $result;
}

/**
 * Make the provided header settings value consistent and proper
 *
 * @param $value array
 *
 * @return array
 */
function us_fix_header_settings( $value ) {
	if ( empty( $value ) OR ! is_array( $value ) ) {
		$value = array();
	}
	if ( ! isset( $value['data'] ) OR ! is_array( $value['data'] ) ) {
		$value['data'] = array();
	}
	$options_defaults = array();
	foreach ( us_config( 'header-settings.options', array() ) as $group => $opts ) {
		foreach ( $opts as $opt_name => $opt ) {
			$options_defaults[ $opt_name ] = isset( $opt['std'] ) ? $opt['std'] : '';
		}
	}
	foreach ( array( 'default', 'tablets', 'mobiles' ) as $state ) {
		if ( ! isset( $value[ $state ] ) OR ! is_array( $value[ $state ] ) ) {
			$value[ $state ] = array();
		}
		if ( ! isset( $value[ $state ]['layout'] ) OR ! is_array( $value[ $state ]['layout'] ) ) {
			if ( $state != 'default' AND isset( $value['default']['layout'] ) ) {
				$value[ $state ]['layout'] = $value['default']['layout'];
			} else {
				$value[ $state ]['layout'] = array();
			}
		}
		$state_elms = array();
		foreach ( $value[ $state ]['layout'] as $place => $elms ) {
			if ( ! is_array( $elms ) ) {
				$elms = array();
			}
			foreach ( $elms as $index => $elm_id ) {
				if ( ! is_string( $elm_id ) OR strpos( $elm_id, ':' ) == -1 ) {
					unset( $elms[ $index ] );
				} else {
					$state_elms[] = $elm_id;
					if ( ! isset( $value['data'][ $elm_id ] ) ) {
						$value['data'][ $elm_id ] = array();
					}
				}
			}
			$value[ $state ]['layout'][ $place ] = array_values( $elms );
		}
		if ( ! isset( $value[ $state ]['layout']['hidden'] ) OR ! is_array( $value[ $state ]['layout']['hidden'] ) ) {
			$value[ $state ]['layout']['hidden'] = array();
		}
		$value[ $state ]['layout']['hidden'] = array_merge( $value[ $state ]['layout']['hidden'], array_diff( array_keys( $value['data'] ), $state_elms ) );
		// Fixing options
		if ( ! isset( $value[ $state ]['options'] ) OR ! is_array( $value[ $state ]['options'] ) ) {
			$value[ $state ]['options'] = array();
		}
		$value[ $state ]['options'] = array_merge( $options_defaults, ( $state != 'default' ) ? $value['default']['options'] : array(), $value[ $state ]['options'] );
	}

	foreach ( $value['data'] as $elm_id => $values ) {
		$type = strtok( $elm_id, ':' );
		$defaults = us_get_header_elm_defaults( $type );
		$value['data'][ $elm_id ] = array_merge( $defaults, array_intersect_key( $value['data'][ $elm_id ], $defaults ) );
	}

	return $value;
}

function us_fix_header_template_settings( $value ) {

	if ( isset( $value['title'] ) ) {
		// Don't need this in data processing
		unset( $value['title'] );
	}
	$template_structure = array(
		'default' => array( 'options' => array(), 'layout' => array() ),
		'tablets' => array( 'options' => array(), 'layout' => array() ),
		'mobiles' => array( 'options' => array(), 'layout' => array() ),
		'data' => array(),
	);
	$value = us_array_merge( $template_structure, $value );
	$layout_structure = array(
		'top_left' => array(),
		'top_center' => array(),
		'top_right' => array(),
		'middle_left' => array(),
		'middle_center' => array(),
		'middle_right' => array(),
		'bottom_left' => array(),
		'bottom_center' => array(),
		'bottom_right' => array(),
		'hidden' => array(),
	);
	foreach ( array( 'default', 'tablets', 'mobiles' ) as $state ) {
		// Options
		$value[ $state ]['options'] = array_merge( ( $state == 'default' ) ? array() : $value['default']['options'], $value[ $state ]['options'] );
		// Layout
		$value[ $state ]['layout'] = array_merge( $layout_structure, ( $state == 'default' ) ? array() : $value['default']['layout'], $value[ $state ]['layout'] );
	}
	$value = us_fix_header_settings( $value );

	return $value;
}

/**
 * Get list of user registered nav menus with theirs proper names, in a format sutable for usof select field
 *
 * @return array
 */
function us_get_nav_menus() {
	$menus = array();
	foreach ( get_terms( 'nav_menu', array( 'hide_empty' => TRUE ) ) as $menu ) {
		$menus[ $menu->slug ] = $menu->name;
	}

	return $menus;
}

/**
 * Get the list of header elements that are shown in the certain layout listing
 *
 * @param array $list Euther layout or separate list
 *
 * @return array
 */
function us_get_header_shown_elements_list( $list ) {
	$shown = array();
	foreach ( $list as $key => $sublist ) {
		if ( $key != 'hidden' ) {
			$shown = array_merge( $shown, $sublist );
		}
	}

	return $shown;
}

add_action( 'wp_footer', 'us_pass_header_settings_to_js' );
function us_pass_header_settings_to_js() {
	global $us_header_settings;
	us_load_header_settings_once();
	$header_settings = $us_header_settings;
	if ( isset( $header_settings['data'] ) ) {
		unset( $header_settings['data'] );
	}
	echo '<script type="text/javascript">';
	echo '$us.headerSettings = ' . json_encode( $header_settings ) . ';';
	echo '</script>';
	echo "\n";
}

/**
 * Get the header design options css for all the fields
 *
 * @return string
 */
function us_get_header_design_options_css() {
	global $us_header_settings;
	us_load_header_settings_once();
	$sizes = array(
		'default' => '@media (min-width: 901px)',
		'tablets' => '@media (min-width: 601px) and (max-width: 900px)',
		'mobiles' => '@media (max-width: 600px)',
	);
	$data = array();
	foreach ( $us_header_settings['data'] as $elm_id => $elm ) {
		if ( ! isset( $elm['design_options'] ) OR empty( $elm['design_options'] ) OR ! is_array( $elm['design_options'] ) ) {
			continue;
		}
		$elm_class = 'ush_' . str_replace( ':', '_', $elm_id );
		foreach ( $elm['design_options'] as $key => $value ) {
			if ( $key == 'hide_for_sticky' ) {
				continue;
			}
			$key = explode( '_', $key );
			if ( ! isset( $data[ $key[2] ] ) ) {
				$data[ $key[2] ] = array();
			}
			if ( ! isset( $data[ $key[2] ][ $elm_class ] ) ) {
				$data[ $key[2] ][ $elm_class ] = array();
			}
			if ( ! isset( $data[ $key[2] ][ $elm_class ][ $key[0] ] ) ) {
				$data[ $key[2] ][ $elm_class ][ $key[0] ] = array();
			}
			$data[ $key[2] ][ $elm_class ][ $key[0] ][ $key[1] ] = $value;
		}
	}
	$css = '';
	foreach ( $sizes as $state => $mquery ) {
		if ( ! isset( $data[ $state ] ) ) {
			continue;
		}
		$css .= $mquery . " {\n";
		foreach ( $data[ $state ] as $elm_class => $props ) {
			$css .= '.' . $elm_class . '{';
			foreach ( $props as $prop => $values ) {
				if ( count( $values ) == 4 AND count( array_unique( $values ) ) == 1 ) {
					// All the directions have the same value, so grouping them together
					$values = array_values( $values );
					$css .= $prop . ':' . $values[0] . '!important;';
				} else {
					foreach ( $values as $dir => $val ) {
						$css .= $prop . '-' . $dir . ':' . $val . '!important;';
					}
				}
			}
			$css .= "}\n";
		}
		$css .= "}\n";
	}

	return $css;
}
