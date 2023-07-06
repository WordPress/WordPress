<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

echo do_shortcode(stripcslashes(wp_kses_post(wp_unslash(woof()->settings['override_no_products']))));
