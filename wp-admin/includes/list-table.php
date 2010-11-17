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
		'WP_Posts_List_Table' => 'posts',
		'WP_Media_List_Table' => 'media',
		'WP_Terms_List_Table' => 'terms',
		'WP_Users_List_Table' => 'users',
		'WP_Comments_List_Table' => 'comments',
		'WP_Post_Comments_List_Table' => 'comments',
		'WP_Links_List_Table' => 'links',
		'WP_Plugin_Install_List_Table' => 'plugin-install',
		'WP_Themes_List_Table' => 'themes',
		'WP_Theme_Install_List_Table' => 'theme-install',
		'WP_Plugins_List_Table' => 'plugins',
		// Network Admin
		'WP_MS_Sites_List_Table' => 'ms-sites',
		'WP_MS_Users_List_Table' => 'ms-users',
		'WP_MS_Themes_List_Table' => 'ms-themes',
	);

	if ( isset( $core_classes[ $class ] ) ) {
		require_once( ABSPATH . '/wp-admin/includes/class-wp-' . $core_classes[ $class ] . '-list-table.php' );
		return true;
	}

	return false;
}

?>
