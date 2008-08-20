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
$self = preg_replace('|^.*/wp-admin/|i', '', $_SERVER['PHP_SELF']);
$self = preg_replace('|^.*/plugins/|i', '', $self);

global $menu, $submenu, $parent_file; //For when admin-header is included from within a function.

get_admin_page_parent();

function _wp_menu_output( &$menu, &$submenu, $submenu_as_parent = true ) {
	global $self, $parent_file, $submenu_file, $plugin_page;

	$first = true;
	// 0 = name, 1 = capability, 2 = file, 3 = class
	foreach ( $menu as $key => $item ) {
		$class = array();
		if ( $first ) {
			$class[] = 'wp-first-item';
			$first = false;
		}
		if ( !empty($submenu[$item[2]]) )
			$class[] = 'wp-has-submenu';

		if ( ( $parent_file && $item[2] == $parent_file ) || strcmp($self, $item[2]) == 0 ) {
			if ( !empty($submenu[$item[2]]) )
				$class[] = 'wp-has-current-submenu wp-menu-open';
			else
				$class[] = 'current';
		}

		if ( isset($item[3]) )
			$class[] = $item[3];

		$class = $class ? ' class="' . join( ' ', $class ) . '"' : '';

		echo "\n\t<li$class>";

		if ( $submenu_as_parent && !empty($submenu[$item[2]]) ) {
			$submenu[$item[2]] = array_values($submenu[$item[2]]);  // Re-index.
			$menu_hook = get_plugin_page_hook($submenu[$item[2]][0][2], $item[2]);
			if ( file_exists(WP_PLUGIN_DIR . "/{$submenu[$item[2]][0][2]}") || !empty($menu_hook))
				echo "<a href='admin.php?page={$submenu[$item[2]][0][2]}'$class>{$item[0]}</a>";
			else
				echo "\n\t<a href='{$submenu[$item[2]][0][2]}'$class>{$item[0]}</a>";
		} else if ( current_user_can($item[1]) ) {
			$menu_hook = get_plugin_page_hook($item[2], 'admin.php');
			if ( file_exists(WP_PLUGIN_DIR . "/{$item[2]}") || !empty($menu_hook) )
				echo "\n\t<a href='admin.php?page={$item[2]}'$class>{$item[0]}</a>";
			else
				echo "\n\t<a href='{$item[2]}'$class>{$item[0]}</a>";
		}

		if ( !empty($submenu[$item[2]]) ) {
			echo "\n\t<ul class='wp-submenu'>";
			$first = true;
			foreach ( $submenu[$item[2]] as $sub_key => $sub_item ) {
				if ( !current_user_can($sub_item[1]) )
					continue;

				$class = array();
				if ( $first ) {
					$class[] = 'wp-first-item';
					$first = false;
				}
				if ( isset($submenu_file) ) {
					if ( $submenu_file == $sub_item[2] )
						$class[] = 'current';
				} else if ( (isset($plugin_page) && $plugin_page == $sub_item[2]) || (!isset($plugin_page) && $self == $sub_item[2]) ) {
					$class[] = 'current';
				}

				$class = $class ? ' class="' . join( ' ', $class ) . '"' : '';

				$menu_hook = get_plugin_page_hook($sub_item[2], $item[2]);

				if ( file_exists(WP_PLUGIN_DIR . "/{$sub_item[2]}") || ! empty($menu_hook) ) {
					if ( 'admin.php' == $pagenow )
						echo "\n\t\t<li$class><a href='admin.php?page={$sub_item[2]}'$class>{$sub_item[0]}</a></li>";
					else
						echo "\n\t\t<li$class><a href='{$item[2]}?page={$sub_item[2]}'$class>{$sub_item[0]}</a></li>";
				} else {
					echo "\n\t\t<li$class><a href='{$sub_item[2]}'$class>{$sub_item[0]}</a></li>";
				}
			}
			echo "\n\t</ul>";
		}
		echo "</li>";
	}
}

?>

<ul id="dashmenu" class="wp-menu">

<?php

_wp_menu_output( $top_menu, $top_submenu, false );
do_action( 'dashmenu' );

?>


</ul>


<ul id="adminmenu" class="wp-menu">

<li class="write-new-button"><a class="button-secondary" href="post-new.php">Write New Post</a></li>

<?php

_wp_menu_output( $menu, $submenu );
do_action( 'adminmenu' );

?>


</ul>

<?php

do_action('admin_notices');

?>
