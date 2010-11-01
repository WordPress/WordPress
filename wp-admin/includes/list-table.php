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
		'WP_Posts_Table' => 'posts',
		'WP_Media_Table' => 'media',
		'WP_Terms_Table' => 'terms',
		'WP_Users_Table' => 'users',
		'WP_Comments_Table' => 'comments',
		'WP_Post_Comments_Table' => 'comments',
		'WP_Links_Table' => 'links',
		'WP_Sites_Table' => 'sites',
		'WP_MS_Users_Table' => 'ms-users',
		'WP_Plugins_Table' => 'plugins',
		'WP_Plugin_Install_Table' => 'plugin-install',
		'WP_Themes_Table' => 'themes',
		'WP_Theme_Install_Table' => 'theme-install',
		'WP_MS_Themes_Table' => 'ms-themes',
	);

	if ( isset( $core_classes[ $class ] ) ) {
		require_once( ABSPATH . '/wp-admin/includes/list-table-' . $core_classes[ $class ] . '.php' );
		return true;
	}
	return false;
}

?>
