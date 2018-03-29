<?php

function et_fb_remove_rtl_stylesheet( $uri ) {
	$uri = str_replace( get_template_directory_uri() . '/rtl.css', '', $uri );

	return $uri;
}

function et_fb_remove_html_rtl_dir( $attributes ) {
	$attributes = str_replace( 'dir="rtl"', '', $attributes );

	return $attributes;
}