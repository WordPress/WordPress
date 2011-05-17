<?php
/**
 * Displays Administration Menu.
 *
 * @package WordPress
 * @subpackage Administration
 */

/**
 * The current page.
 *
 * @global string $self
 * @name $self
 * @var string
 */
$self = preg_replace('|^.*/wp-admin/network/|i', '', $_SERVER['PHP_SELF']);
$self = preg_replace('|^.*/wp-admin/|i', '', $self);
$self = preg_replace('|^.*/plugins/|i', '', $self);
$self = preg_replace('|^.*/mu-plugins/|i', '', $self);

global $menu, $submenu, $parent_file; //For when admin-header is included from within a function.
$parent_file = apply_filters("parent_file", $parent_file); // For plugins to move submenu tabs around.

get_admin_page_parent();

/**
 * Display menu.
 *
 * @access private
 * @since 2.7.0
 *
 * @param array $menu
 * @param array $submenu
 * @param bool $submenu_as_parent
 */
function _wp_menu_output( $menu, $submenu, $submenu_as_parent = true ) {
	global $self, $parent_file, $submenu_file, $plugin_page, $pagenow, $typenow;

	$menu_setting_increment = -1;
	$user_settings = get_all_user_settings();

	$first = true;
	// 0 = name, 1 = capability, 2 = file, 3 = class, 4 = id, 5 = icon src
	foreach ( $menu as $key => $item ) {
		$admin_is_parent = false;
		$class = array();
		if ( $first ) {
			$class[] = 'wp-first-item';
			$first = false;
		}
		if ( !empty($submenu[$item[2]]) ) {
			$class[] = 'wp-has-submenu';
			$menu_setting_increment++;
		}

		if ( ( $parent_file && $item[2] == $parent_file ) || ( empty($typenow) && $self == $item[2] ) ) {
			if ( !empty($submenu[$item[2]]) )
				$class[] = 'wp-has-current-submenu wp-menu-open';
			else
				$class[] = 'current';
		} elseif ( ! empty( $submenu[ $item[2] ] ) && isset( $user_settings[ 'm' . $menu_setting_increment ] ) && 'o' == $user_settings[ 'm' . $menu_setting_increment ] ) {
				$class[] = 'wp-menu-open';
		}

		if ( ! empty($item[4]) )
			$class[] = $item[4];

		$class = $class ? ' class="' . join( ' ', $class ) . '"' : '';
		$tabindex = ' tabindex="1"';
		$id = ! empty($item[5]) ? ' id="' . preg_replace( '|[^a-zA-Z0-9_:.]|', '-', $item[5] ) . '"' : '';
		$img = '';
		if ( ! empty($item[6]) ) {
			if ( 'div' === $item[6] )
				$img = '<br />';
			else
				$img = '<img src="' . $item[6] . '" alt="" />';
		}
		$toggle = '<div class="wp-menu-toggle"><br /></div>';
		$arrow = '<div class="wp-menu-arrow"><div></div></div>';

		$title = wptexturize($item[0]);

		echo "\n\t<li$class$id>";

		if ( $submenu_as_parent && !empty($submenu[$item[2]]) ) {
			$submenu[$item[2]] = array_values($submenu[$item[2]]);  // Re-index.
			$menu_hook = get_plugin_page_hook($submenu[$item[2]][0][2], $item[2]);
			$menu_file = $submenu[$item[2]][0][2];
			if ( false !== $pos = strpos($menu_file, '?') )
				$menu_file = substr($menu_file, 0, $pos);
			if ( ( ('index.php' != $submenu[$item[2]][0][2]) && file_exists(WP_PLUGIN_DIR . "/$menu_file") ) || !empty($menu_hook)) {
				$admin_is_parent = true;
				echo "<div class='wp-menu-image'><a href='admin.php?page={$submenu[$item[2]][0][2]}'>$img</a></div>$arrow$toggle<a href='admin.php?page={$submenu[$item[2]][0][2]}'$class$tabindex>$title</a>";
			} else {
				echo "\n\t<div class='wp-menu-image'><a href='{$submenu[$item[2]][0][2]}'>$img</a></div>$arrow$toggle<a href='{$submenu[$item[2]][0][2]}'$class$tabindex>$title</a>";
			}
		} else if ( !empty($item[2]) && current_user_can($item[1]) ) {
			$menu_hook = get_plugin_page_hook($item[2], 'admin.php');
			$menu_file = $item[2];
			if ( false !== $pos = strpos($menu_file, '?') )
				$menu_file = substr($menu_file, 0, $pos);
			if ( ('index.php' != $item[2]) && file_exists(WP_PLUGIN_DIR . "/$menu_file") || !empty($menu_hook) ) {
				$admin_is_parent = true;
				echo "\n\t<div class='wp-menu-image'><a href='admin.php?page={$item[2]}'>$img</a></div>$arrow$toggle<a href='admin.php?page={$item[2]}'$class$tabindex>{$item[0]}</a>";
			} else {
				echo "\n\t<div class='wp-menu-image'><a href='{$item[2]}'>$img</a></div>$arrow$toggle<a href='{$item[2]}'$class$tabindex>{$item[0]}</a>";
			}
		}

		if ( !empty($submenu[$item[2]]) ) {
			echo "\n\t<div class='wp-submenu'><div class='wp-submenu-wrap'>";
			echo "<div class='wp-submenu-head'>{$item[0]}</div><ul>";
			$first = true;
			foreach ( $submenu[$item[2]] as $sub_key => $sub_item ) {
				if ( !current_user_can($sub_item[1]) )
					continue;

				$class = array();
				if ( $first ) {
					$class[] = 'wp-first-item';
					$first = false;
				}

				$menu_file = $item[2];

				if ( false !== $pos = strpos($menu_file, '?') )
					$menu_file = substr($menu_file, 0, $pos);

				// Handle current for post_type=post|page|foo pages, which won't match $self.
				if ( !empty($typenow) )
					$self_type = $self . '?post_type=' . $typenow;
				else
					$self_type = 'nothing';

				if ( isset($submenu_file) ) {
					if ( $submenu_file == $sub_item[2] )
						$class[] = 'current';
				// If plugin_page is set the parent must either match the current page or not physically exist.
				// This allows plugin pages with the same hook to exist under different parents.
				} else if ( (isset($plugin_page) && $plugin_page == $sub_item[2] && (!file_exists($menu_file) || ($item[2] == $self) || ($item[2] == $self_type))) || (!isset($plugin_page) && $self == $sub_item[2]) ) {
					$class[] = 'current';
				}

				$class = $class ? ' class="' . join( ' ', $class ) . '"' : '';

				$menu_hook = get_plugin_page_hook($sub_item[2], $item[2]);
				$sub_file = $sub_item[2];
				if ( false !== $pos = strpos($sub_file, '?') )
					$sub_file = substr($sub_file, 0, $pos);

				$title = wptexturize($sub_item[0]);

				if ( ( ('index.php' != $sub_item[2]) && file_exists(WP_PLUGIN_DIR . "/$sub_file") ) || ! empty($menu_hook) ) {
					// If admin.php is the current page or if the parent exists as a file in the plugins or admin dir
					if ( (!$admin_is_parent && file_exists(WP_PLUGIN_DIR . "/$menu_file") && !is_dir(WP_PLUGIN_DIR . "/{$item[2]}")) || file_exists($menu_file) )
						$sub_item_url = add_query_arg( array('page' => $sub_item[2]), $item[2] );
					else
						$sub_item_url = add_query_arg( array('page' => $sub_item[2]), 'admin.php' );
					$sub_item_url = esc_url($sub_item_url);
					echo "<li$class><a href='$sub_item_url'$class$tabindex>$title</a></li>";
				} else {
					echo "<li$class><a href='{$sub_item[2]}'$class$tabindex>$title</a></li>";
				}
			}
			echo "</ul></div></div>";
		}
		echo "</li>";
	}

	echo '<li id="collapse-menu"><div id="collapse-button"><div></div></div>';
	echo '<span>' . esc_html__( 'Collapse menu' ) . '</span>';
	echo '</li>';
}

?>

<div id="adminmenuback"></div>
<div id="adminmenuwrap">
<div id="adminmenushadow"></div>
<ul id="adminmenu">

<?php

_wp_menu_output( $menu, $submenu );
do_action( 'adminmenu' );

?>
</ul>
</div>
