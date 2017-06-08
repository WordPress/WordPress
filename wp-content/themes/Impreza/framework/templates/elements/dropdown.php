<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

/**
 * Output dropdown element
 *
 * @var $source string Dropdown source: 'own' / 'wpml' / 'polylang' / 'qtranslate'
 * @var $text_size int
 * @var $link_title string
 * @var $link_qty int
 * @var $link_1_label string
 * @var $link_1_url string
 * @var $link_2_label string
 * @var $link_2_url string
 * @var $link_3_label string
 * @var $link_3_url string
 * @var $link_4_label string
 * @var $link_4_url string
 * @var $link_5_label string
 * @var $link_5_url string
 * @var $link_6_label string
 * @var $link_6_url string
 * @var $link_7_label string
 * @var $link_7_url string
 * @var $link_8_label string
 * @var $link_8_url string
 * @var $link_9_label string
 * @var $link_9_url string
 * @var $design_options array
 * @var $id string
 */

$classes = ' source_' . $source;
if ( isset( $design_options ) AND isset( $design_options['hide_for_sticky'] ) AND $design_options['hide_for_sticky'] ) {
	$classes .= ' hide-for-sticky';
}
if ( isset( $id ) AND ! empty( $id ) ) {
	$classes .= ' ush_' . str_replace( ':', '_', $id );
}

// Common data format
$data = array(
	'current' => array(),
	'list' => array(),
);
if ( $source == 'own' ) {
	$link_qty = intval( $link_qty );
	$data['current']['title'] = $link_title;
	for ( $i = 1; $i <= $link_qty; $i++ ) {
		$label_var = 'link_' . $i . '_label';
		$url_var = 'link_' . $i . '_url';
		$data['list'][] = array(
			'title' => $$label_var,
			'url' => ( substr( $$url_var, 0, 4 ) == 'http' ) ? $$url_var : ( '//' . $$url_var ),
		);
	}
} elseif ( $source == 'wpml' AND function_exists( 'icl_get_languages' ) ) {
	$widget_options = array(
		'icl_lso_flags' => 1,
		'icl_lso_native_lang' => 1,
		'icl_lso_display_lang' => 1,
	);
	$wpml_options = (array) get_option( 'icl_sitepress_settings' );
	$widget_options = array_merge( $widget_options, array_intersect_key( $wpml_options, $widget_options ) );
	$languages = icl_get_languages( 'skip_missing=0' );
	foreach ( $languages as $language ) {
		$data_language = array();
		if ( $widget_options['icl_lso_native_lang'] == 1 ) {
			$data_language['title'] = $language['native_name'];
			if ( $widget_options['icl_lso_display_lang'] AND ( $language['native_name'] != $language['translated_name'] ) ) {
				$data_language['title'] .= ' (' . $language['translated_name'] . ')';
			}
		} elseif ( $widget_options['icl_lso_display_lang'] == 1 ) {
			$data_language['title'] = $language['translated_name'];
		}
		if ( $widget_options['icl_lso_flags'] ) {
			$data_language['flag'] = $language['country_flag_url'];
		}
		if ( $language['active'] ) {
			$data['current'] = $data_language;
		} else {
			$data_language['url'] = $language['url'];
			$data['list'][] = $data_language;
		}
	}
} elseif ( $source == 'polylang' AND function_exists( 'pll_the_languages' ) ) {
	$pll_langs = pll_the_languages( array( 'raw' => 1 ) );
	foreach ( $pll_langs as $pll_lang ) {
		$data_language = array(
			'title' => $pll_lang['name'],
			'flag' => $pll_lang['flag'],
		);
		if ( $pll_lang['current_lang'] ) {
			$data['current'] = $data_language;
		} else {
			$data_language['url'] = $pll_lang['url'];
			$data['list'][] = $data_language;
		}
	}
} elseif ( $source == 'qtranslate' AND function_exists( 'qtranxf_getSortedLanguages' ) ) {
	global $q_config;
	if ( ! isset( $q_config ) OR ! is_array( $q_config ) ) {
		return;
	}
	$q_url = is_404() ? get_option( 'home' ) : '';
	foreach ( qtranxf_getSortedLanguages() as $q_lang_code ) {
		$data_language = array(
			'title' => $q_config['language_name'][ $q_lang_code ],
			'title_class' => 'qtranxs_flag_' . $q_lang_code,
		);
		if ( $q_lang_code == $q_config['language'] ) {
			$data['current'] = $data_language;
		} else {
			$data_language['url'] = qtranxf_convertURL( $q_url, $q_lang_code, FALSE, TRUE );
			$data['list'][] = $data_language;
		}
	}
}

if ( count( $data['list'] ) == 0 ) {
	return;
}
$output = '<div class="w-dropdown' . $classes . '"><div class="w-dropdown-h">';
$output .= '<div class="w-dropdown-list">';
foreach ( $data['list'] as $lang ) {
	$output .= '<a class="w-dropdown-item" href="' . esc_attr( $lang['url'] ) . '">';
	if ( isset( $lang['flag'] ) AND ! empty( $lang['flag'] ) ) {
		$output .= '<span class="w-dropdown-item-icon"><img class="w-dropdown-item-flag" alt="flag" src="' . $lang['flag'] . '" /></span>';
	}
	$output .= '<span class="w-dropdown-item-title';
	if ( isset( $lang['title_class'] ) AND ! empty( $lang['title_class'] ) ) {
		$output .= ' ' . esc_attr( $lang['title_class'] );
	}
	$output .= '">' . $lang['title'] . '</span>';
	$output .= '</a>';
}
$output .= '</div>';
if ( isset( $data['current'] ) AND ! empty( $data['current'] ) ) {
	$output .= '<div class="w-dropdown-current"><a class="w-dropdown-item" href="javascript:void(0)">';
	if ( isset( $data['current']['flag'] ) AND ! empty( $data['current']['flag'] ) ) {
		$output .= '<span class="w-dropdown-item-icon"><img class="w-dropdown-item-flag" alt="flag" src="' . $data['current']['flag'] . '" /></span>';
	}
	$output .= '<span class="w-dropdown-item-title';
	if ( isset( $data['current']['title_class'] ) AND ! empty( $data['current']['title_class'] ) ) {
		$output .= ' ' . esc_attr( $data['current']['title_class'] );
	}
	$output .= '">' . $data['current']['title'] . '</span>';
	$output .= '</a></div>';
}
$output .= '</div></div>';
echo $output;
