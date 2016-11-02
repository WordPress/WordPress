<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

/**
 * Outputs a minified version of theme-options.css
 *
 * Thanks to http://manas.tungare.name/software/css-compression-in-php/
 *
 * @action Before the template: 'us_before_template:config/theme-options.min.css'
 * @action After the template: 'us_after_template:config/theme-options.min.css'
 */

$buffer = us_get_template( 'config/theme-options.css' );

// Remove comments
$buffer = preg_replace( '!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $buffer );

// Remove space after colons
$buffer = str_replace( ': ', ':', $buffer );

// Remove whitespace
$buffer = str_replace( array( "\r\n", "\r", "\n", "\t", '  ', '    ', '    ' ), '', $buffer );

echo $buffer;
