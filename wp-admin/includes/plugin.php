<?php

function get_plugin_data( $plugin_file ) {
	$plugin_data = implode( '', file( $plugin_file ));
	preg_match( '|Plugin Name:(.*)$|mi', $plugin_data, $plugin_name );
	preg_match( '|Plugin URI:(.*)$|mi', $plugin_data, $plugin_uri );
	preg_match( '|Description:(.*)$|mi', $plugin_data, $description );
	preg_match( '|Author:(.*)$|mi', $plugin_data, $author_name );
	preg_match( '|Author URI:(.*)$|mi', $plugin_data, $author_uri );

	if ( preg_match( "|Version:(.*)|i", $plugin_data, $version ))
		$version = trim( $version[1] );
	else
		$version = '';

	$description = wptexturize( trim( $description[1] ));

	$name = $plugin_name[1];
	$name = trim( $name );
	$plugin = $name;
	if ('' != trim($plugin_uri[1]) && '' != $name ) {
		$plugin = '<a href="' . trim( $plugin_uri[1] ) . '" title="'.__( 'Visit plugin homepage' ).'">'.$plugin.'</a>';
	}

	if ('' == $author_uri[1] ) {
		$author = trim( $author_name[1] );
	} else {
		$author = '<a href="' . trim( $author_uri[1] ) . '" title="'.__( 'Visit author homepage' ).'">' . trim( $author_name[1] ) . '</a>';
	}

	return array('Name' => $name, 'Title' => $plugin, 'Description' => $description, 'Author' => $author, 'Version' => $version);
}

function get_plugins($plugin_folder = '') {
	global $wp_plugins;

	if ( isset( $wp_plugins ) ) {
		return $wp_plugins;
	}

	$wp_plugins = array ();
	$plugin_root = ABSPATH . PLUGINDIR;
	if( !empty($plugin_folder) )
		$plugin_root .= $plugin_folder;

	// Files in wp-content/plugins directory
	$plugins_dir = @ opendir( $plugin_root);
	if ( $plugins_dir ) {
		while (($file = readdir( $plugins_dir ) ) !== false ) {
			if ( substr($file, 0, 1) == '.' )
				continue;
			if ( is_dir( $plugin_root.'/'.$file ) ) {
				$plugins_subdir = @ opendir( $plugin_root.'/'.$file );
				if ( $plugins_subdir ) {
					while (($subfile = readdir( $plugins_subdir ) ) !== false ) {
						if ( substr($subfile, 0, 1) == '.' )
							continue;
						if ( substr($subfile, -4) == '.php' )
							$plugin_files[] = "$file/$subfile";
					}
				}
			} else {
				if ( substr($file, -4) == '.php' )
					$plugin_files[] = $file;
			}
		}
	}
	@closedir( $plugins_dir );
	@closedir( $plugins_subdir );

	if ( !$plugins_dir || !$plugin_files )
		return $wp_plugins;

	foreach ( $plugin_files as $plugin_file ) {
		if ( !is_readable( "$plugin_root/$plugin_file" ) )
			continue;

		$plugin_data = get_plugin_data( "$plugin_root/$plugin_file" );

		if ( empty ( $plugin_data['Name'] ) )
			continue;

		$wp_plugins[plugin_basename( $plugin_file )] = $plugin_data;
	}

	uasort( $wp_plugins, create_function( '$a, $b', 'return strnatcasecmp( $a["Name"], $b["Name"] );' ));

	return $wp_plugins;
}

function is_plugin_active($plugin){
	return in_array($plugin, get_option('active_plugins'));
}

function activate_plugin($plugin, $redirect = '') {
		$current = get_option('active_plugins');
		$plugin = trim($plugin);

		$valid = validate_plugin($plugin);
		if ( is_wp_error($valid) )
			return $valid;

		if ( !in_array($plugin, $current) ) {
			if ( !empty($redirect) )
				wp_redirect(add_query_arg('_error_nonce', wp_create_nonce('plugin-activation-error_' . $plugin), $redirect)); // we'll override this later if the plugin can be included without fatal error
			ob_start();
			@include(ABSPATH . PLUGINDIR . '/' . $plugin);
			$current[] = $plugin;
			sort($current);
			update_option('active_plugins', $current);
			do_action('activate_' . $plugin);
			ob_end_clean();
		}

		return null;
}

function deactivate_plugins($plugins, $silent= false) {
	$current = get_option('active_plugins');

	if ( !is_array($plugins) )
		$plugins = array($plugins);

	foreach ( $plugins as $plugin ) {
		if( ! is_plugin_active($plugin) )
			continue;
		array_splice($current, array_search( $plugin, $current), 1 ); // Fixed Array-fu!
		if ( ! $silent ) //Used by Plugin updater to internally deactivate plugin, however, not to notify plugins of the fact to prevent plugin output.
			do_action('deactivate_' . trim( $plugin ));
	}

	update_option('active_plugins', $current);
}

function deactivate_all_plugins() {
	$current = get_option('active_plugins');
	if ( empty($current) )
		return;

	deactivate_plugins($current);

	update_option('deactivated_plugins', $current);
}

function reactivate_all_plugins($redirect = '') {
	$plugins = get_option('deactivated_plugins');

	if ( empty($plugins) )
		return;

	if ( !empty($redirect) )
		wp_redirect(add_query_arg('_error_nonce', wp_create_nonce('plugin-activation-error_' . $plugin), $redirect));

	$errors = array();
	foreach ( (array) $plugins as $plugin ) {
		$result = activate_plugin($plugin);
		if ( is_wp_error($result) )
			$errors[$plugin] = $result;
	}

	delete_option('deactivated_plugins');

	if ( !empty($errors) )
		return new WP_Error('plugins_invalid', __('One of the plugins is invalid.'), $errors);

	return true;
}

function validate_active_plugins() {
	$check_plugins = get_option('active_plugins');

	// Sanity check.  If the active plugin list is not an array, make it an
	// empty array.
	if ( !is_array($check_plugins) ) {
		update_option('active_plugins', array());
		return;
	}

	// If a plugin file does not exist, remove it from the list of active
	// plugins.
	foreach ( $check_plugins as $check_plugin ) {
		if ( !file_exists(ABSPATH . PLUGINDIR . '/' . $check_plugin) ) {
			$current = get_option('active_plugins');
			$key = array_search($check_plugin, $current);
			if ( false !== $key && NULL !== $key ) {
				unset($current[$key]);
				update_option('active_plugins', $current);
			}
		}
	}
}

function validate_plugin($plugin) {
	if ( validate_file($plugin) )
		return new WP_Error('plugin_invalid', __('Invalid plugin.'));
	if ( ! file_exists(ABSPATH . PLUGINDIR . '/' . $plugin) )
		return new WP_Error('plugin_not_found', __('Plugin file does not exist.'));

	return 0;
}

//
// Menu
//

function add_menu_page( $page_title, $menu_title, $access_level, $file, $function = '' ) {
	global $menu, $admin_page_hooks;

	$file = plugin_basename( $file );

	$menu[] = array ( $menu_title, $access_level, $file, $page_title );

	$admin_page_hooks[$file] = sanitize_title( $menu_title );

	$hookname = get_plugin_page_hookname( $file, '' );
	if (!empty ( $function ) && !empty ( $hookname ))
		add_action( $hookname, $function );

	return $hookname;
}

function add_submenu_page( $parent, $page_title, $menu_title, $access_level, $file, $function = '' ) {
	global $submenu;
	global $menu;
	global $_wp_real_parent_file;
	global $_wp_submenu_nopriv;

	$file = plugin_basename( $file );

	$parent = plugin_basename( $parent);
	if ( isset( $_wp_real_parent_file[$parent] ) )
		$parent = $_wp_real_parent_file[$parent];

	if ( !current_user_can( $access_level ) ) {
		$_wp_submenu_nopriv[$parent][$file] = true;
		return false;
	}

	// If the parent doesn't already have a submenu, add a link to the parent
	// as the first item in the submenu.  If the submenu file is the same as the
	// parent file someone is trying to link back to the parent manually.  In
	// this case, don't automatically add a link back to avoid duplication.
	if (!isset( $submenu[$parent] ) && $file != $parent  ) {
		foreach ( $menu as $parent_menu ) {
			if ( $parent_menu[2] == $parent && current_user_can( $parent_menu[1] ) )
				$submenu[$parent][] = $parent_menu;
		}
	}

	$submenu[$parent][] = array ( $menu_title, $access_level, $file, $page_title );

	$hookname = get_plugin_page_hookname( $file, $parent);
	if (!empty ( $function ) && !empty ( $hookname ))
		add_action( $hookname, $function );

	return $hookname;
}

function add_management_page( $page_title, $menu_title, $access_level, $file, $function = '' ) {
	return add_submenu_page( 'edit.php', $page_title, $menu_title, $access_level, $file, $function );
}

function add_options_page( $page_title, $menu_title, $access_level, $file, $function = '' ) {
	return add_submenu_page( 'options-general.php', $page_title, $menu_title, $access_level, $file, $function );
}

function add_theme_page( $page_title, $menu_title, $access_level, $file, $function = '' ) {
	return add_submenu_page( 'themes.php', $page_title, $menu_title, $access_level, $file, $function );
}

function add_users_page( $page_title, $menu_title, $access_level, $file, $function = '' ) {
	if ( current_user_can('edit_users') )
		$parent = 'users.php';
	else
		$parent = 'profile.php';
	return add_submenu_page( $parent, $page_title, $menu_title, $access_level, $file, $function );
}

//
// Pluggable Menu Support -- Private
//

function get_admin_page_parent() {
	global $parent_file;
	global $menu;
	global $submenu;
	global $pagenow;
	global $plugin_page;
	global $_wp_real_parent_file;
	global $_wp_menu_nopriv;
	global $_wp_submenu_nopriv;

	if ( !empty ( $parent_file ) ) {
		if ( isset( $_wp_real_parent_file[$parent_file] ) )
			$parent_file = $_wp_real_parent_file[$parent_file];

		return $parent_file;
	}

	if ( $pagenow == 'admin.php' && isset( $plugin_page ) ) {
		foreach ( $menu as $parent_menu ) {
			if ( $parent_menu[2] == $plugin_page ) {
				$parent_file = $plugin_page;
				if ( isset( $_wp_real_parent_file[$parent_file] ) )
					$parent_file = $_wp_real_parent_file[$parent_file];
				return $parent_file;
			}
		}
		if ( isset( $_wp_menu_nopriv[$plugin_page] ) ) {
			$parent_file = $plugin_page;
			if ( isset( $_wp_real_parent_file[$parent_file] ) )
					$parent_file = $_wp_real_parent_file[$parent_file];
			return $parent_file;
		}
	}

	if ( isset( $plugin_page ) && isset( $_wp_submenu_nopriv[$pagenow][$plugin_page] ) ) {
		$parent_file = $pagenow;
		if ( isset( $_wp_real_parent_file[$parent_file] ) )
			$parent_file = $_wp_real_parent_file[$parent_file];
		return $parent_file;
	}

	foreach (array_keys( $submenu ) as $parent) {
		foreach ( $submenu[$parent] as $submenu_array ) {
			if ( isset( $_wp_real_parent_file[$parent] ) )
				$parent = $_wp_real_parent_file[$parent];
			if ( $submenu_array[2] == $pagenow ) {
				$parent_file = $parent;
				return $parent;
			} else
				if ( isset( $plugin_page ) && ($plugin_page == $submenu_array[2] ) ) {
					$parent_file = $parent;
					return $parent;
				}
		}
	}

	$parent_file = '';
	return '';
}

function get_admin_page_title() {
	global $title;
	global $menu;
	global $submenu;
	global $pagenow;
	global $plugin_page;

	if ( isset( $title ) && !empty ( $title ) ) {
		return $title;
	}

	$hook = get_plugin_page_hook( $plugin_page, $pagenow );

	$parent = $parent1 = get_admin_page_parent();
	if ( empty ( $parent) ) {
		foreach ( $menu as $menu_array ) {
			if ( isset( $menu_array[3] ) ) {
				if ( $menu_array[2] == $pagenow ) {
					$title = $menu_array[3];
					return $menu_array[3];
				} else
					if ( isset( $plugin_page ) && ($plugin_page == $menu_array[2] ) && ($hook == $menu_array[3] ) ) {
						$title = $menu_array[3];
						return $menu_array[3];
					}
			} else {
				$title = $menu_array[0];
				return $title;
			}
		}
	} else {
		foreach (array_keys( $submenu ) as $parent) {
			foreach ( $submenu[$parent] as $submenu_array ) {
				if ( isset( $plugin_page ) &&
					($plugin_page == $submenu_array[2] ) &&
					(($parent == $pagenow ) || ($parent == $plugin_page ) || ($plugin_page == $hook ) || (($pagenow == 'admin.php' ) && ($parent1 != $submenu_array[2] ) ) )
					) {
						$title = $submenu_array[3];
						return $submenu_array[3];
					}

				if ( $submenu_array[2] != $pagenow || isset( $_GET['page'] ) ) // not the current page
					continue;

				if ( isset( $submenu_array[3] ) ) {
					$title = $submenu_array[3];
					return $submenu_array[3];
				} else {
					$title = $submenu_array[0];
					return $title;
				}
			}
		}
	}

	return $title;
}

function get_plugin_page_hook( $plugin_page, $parent_page ) {
	$hook = get_plugin_page_hookname( $plugin_page, $parent_page );
	if ( has_action($hook) )
		return $hook;
	else
		return null;
}

function get_plugin_page_hookname( $plugin_page, $parent_page ) {
	global $admin_page_hooks;

	$parent = get_admin_page_parent();

	if ( empty ( $parent_page ) || 'admin.php' == $parent_page ) {
		if ( isset( $admin_page_hooks[$plugin_page] ))
			$page_type = 'toplevel';
		else
			if ( isset( $admin_page_hooks[$parent] ))
				$page_type = $admin_page_hooks[$parent];
	} else
		if ( isset( $admin_page_hooks[$parent_page] ) ) {
			$page_type = $admin_page_hooks[$parent_page];
		} else {
			$page_type = 'admin';
		}

	$plugin_name = preg_replace( '!\.php!', '', $plugin_page );

	return $page_type.'_page_'.$plugin_name;
}

function user_can_access_admin_page() {
	global $pagenow;
	global $menu;
	global $submenu;
	global $_wp_menu_nopriv;
	global $_wp_submenu_nopriv;
	global $plugin_page;

	$parent = get_admin_page_parent();

	if ( isset( $_wp_submenu_nopriv[$parent][$pagenow] ) )
		return false;

	if ( isset( $plugin_page ) && isset( $_wp_submenu_nopriv[$parent][$plugin_page] ) )
		return false;

	if ( empty( $parent) ) {
		if ( isset( $_wp_menu_nopriv[$pagenow] ) )
			return false;
		if ( isset( $_wp_submenu_nopriv[$pagenow][$pagenow] ) )
			return false;
		if ( isset( $plugin_page ) && isset( $_wp_submenu_nopriv[$pagenow][$plugin_page] ) )
			return false;
		foreach (array_keys( $_wp_submenu_nopriv ) as $key ) {
			if ( isset( $_wp_submenu_nopriv[$key][$pagenow] ) )
				return false;
			if ( isset( $plugin_page ) && isset( $_wp_submenu_nopriv[$key][$plugin_page] ) )
			return false;
		}
		return true;
	}

	if ( isset( $submenu[$parent] ) ) {
		foreach ( $submenu[$parent] as $submenu_array ) {
			if ( isset( $plugin_page ) && ( $submenu_array[2] == $plugin_page ) ) {
				if ( current_user_can( $submenu_array[1] ))
					return true;
				else
					return false;
			} else if ( $submenu_array[2] == $pagenow ) {
				if ( current_user_can( $submenu_array[1] ))
					return true;
				else
					return false;
			}
		}
	}

	foreach ( $menu as $menu_array ) {
		if ( $menu_array[2] == $parent) {
			if ( current_user_can( $menu_array[1] ))
				return true;
			else
				return false;
		}
	}

	return true;
}

?>
