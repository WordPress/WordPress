<?php

if ( ! function_exists( 'et_get_safe_localization' ) ) :
function et_get_safe_localization( $string ) {
	return wp_kses( $string, et_get_allowed_localization_html_elements() );
}
endif;

if ( ! function_exists( 'et_get_allowed_localization_html_elements' ) ) :
function et_get_allowed_localization_html_elements() {
	$whitelisted_attributes = array(
		'id'    => array(),
		'class' => array(),
		'style' => array(),
	);

	$whitelisted_attributes = apply_filters( 'et_allowed_localization_html_attributes', $whitelisted_attributes );

	$elements = array(
		'a'      => array(
			'href'   => array(),
			'title'  => array(),
			'target' => array(),
		),
		'b'      => array(),
		'em'     => array(),
		'p'      => array(),
		'span'   => array(),
		'div'    => array(),
		'strong' => array(),
	);

	$elements = apply_filters( 'et_allowed_localization_html_elements', $elements );

	foreach ( $elements as $tag => $attributes ) {
		$elements[ $tag ] = array_merge( $attributes, $whitelisted_attributes );
	}

	return $elements;
}
endif;
