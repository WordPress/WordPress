<?php
/**
 * Helper functions for displaying a list of items in an ajaxified HTML table.
 *
 * @package WordPress
 * @subpackage List_Table
 * @since 3.1.0
 */

/**
 * Fetch an instance of a WP_List_Table class.
 *
 * @since 3.1.0
 *
 * @param string $class The type of the list table, which is the class name except for core list tables.
 * @return object|bool Object on success, false if the class does not exist.
 */
function get_list_table( $class ) {
	$class = apply_filters( "get_list_table_$class", $class );
	
	require_list_table( $class );

	if ( class_exists( $class ) )
		return new $class;
	return false;
}

/**
 * Include the proper file for a core list table.
 *
 * Useful for extending a core class that would not otherwise be required.
 *
 * @since 3.1.0
 *
 * @param string $table The core table to include.
 * @return bool True on success, false on failure.
 */
function require_list_table( $class ) {
	$core_classes = array(
		//Site Admin
		'WP_List_Table_Posts' => 'posts',
		'WP_List_Table_Media' => 'media',
		'WP_List_Table_Terms' => 'terms',
		'WP_List_Table_Users' => 'users',
		'WP_List_Table_Comments' => 'comments',
		'WP_List_Table_Post_Comments' => 'comments',
		'WP_List_Table_Links' => 'links',
		'WP_List_Table_Plugin_Install' => 'plugin-install',
		'WP_List_Table_Themes' => 'themes',
		'WP_List_Table_Theme_Install' => 'theme-install',
		'WP_List_Table_Plugins' => 'plugins',
		// Network Admin
		'WP_List_Table_MS_Sites' => 'ms-sites',
		'WP_List_Table_MS_Users' => 'ms-users',
		'WP_List_Table_MS_Themes' => 'ms-themes',
	);

	if ( isset( $core_classes[ $class ] ) ) {
		require_once( ABSPATH . '/wp-admin/includes/class-wp-list-table-' . $core_classes[ $class ] . '.php' );
		return true;
	}

	return false;
}

?>
