<?php
/**
 * Frontend Template Tag functions, only available when the Frontend Controller is loaded
 *
 * @package TablePress
 * @subpackage Frontend Template Tag functions
 * @author Tobias Bäthge
 * @since 1.0.0
 */

/**
 * Add template tag function for "table" Shortcode to be used anywhere in the template, returns the table HTML.
 *
 * This function provides a possibility to show a table anywhere in a WordPress template,
 * which is needed for any region of a theme that can not use Shortcodes.
 *
 * @since 1.0.0
 *
 * @param string|array $table_query Query string like list or array of parameters for Shortcode "table" rendering.
 * @return string HTML of the rendered table.
 */
function tablepress_get_table( $table_query ) {
	if ( is_array( $table_query ) ) {
		$atts = $table_query;
	} else {
		parse_str( (string) $table_query, $atts );
	}
	return TablePress::$controller->shortcode_table( $atts );
}

/**
 * Add template tag function for "table" Shortcode to be used anywhere in the template, echoes the table HTML.
 *
 * This function provides a possibility to show a table anywhere in a WordPress template,
 * which is needed for any region of a theme that can not use Shortcodes.
 *
 * @since 1.0.0
 *
 * @see tablepress_get_table()
 *
 * @param string|array $table_query Query string like list or array of parameters for Shortcode "table" rendering.
 */
function tablepress_print_table( $table_query ) {
	echo tablepress_get_table( $table_query );
}

/**
 * Add template tag function for "table-info" Shortcode to be used anywhere in the template, returns the info.
 *
 * @since 1.0.0
 *
 * @param string|array $table_query Query string like list or array of parameters for Shortcode "table-info" rendering.
 * @return string Desired table information.
 */
function tablepress_get_table_info( $table_query ) {
	if ( is_array( $table_query ) ) {
		$atts = $table_query;
	} else {
		parse_str( (string) $table_query, $atts );
	}
	return TablePress::$controller->shortcode_table_info( $atts );
}

/**
 * Add template tag function for "table-info" Shortcode to be used anywhere in the template, echoes the info.
 *
 * This function provides a possibility to show table info data anywhere in a WordPress template,
 * which is needed for any region of a theme that can not use Shortcodes.
 *
 * @since 1.0.0
 *
 * @see tablepress_get_table_info()
 *
 * @param string|array $table_query Query string like list or array of parameters for Shortcode "table-info" rendering.
 */
function tablepress_print_table_info( $table_query ) {
	echo tablepress_get_table_info( $table_query );
}
