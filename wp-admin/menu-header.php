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

	$first = true;
	// 0 = name, 1 = capability, 2 = file, 3 = class, 4 = id, 5 = icon src
	foreach ( $menu as $key => $item ) {
		$admin_is_parent = false;
		$class = array();
		$aria_attributes = '';

		if ( $first ) {
			$class[] = 'wp-first-item';
			$first = false;
		}

		$submenu_items = false;
		if ( ! empty( $submenu[$item[2]] ) ) {
			$class[] = 'wp-has-submenu';
			$submenu_items = $submenu[$item[2]];
		}

		if ( ( $parent_file && $item[2] == $parent_file ) || ( empty($typenow) && $self == $item[2] ) ) {
			$class[] = ! empty( $submenu_items ) ? 'wp-has-current-submenu wp-menu-open' : 'current';
		} else {
			$class[] = 'wp-not-current-submenu';
			if ( ! empty( $submenu_items ) )
				$aria_attributes .= 'aria-haspopup="true"';
		}

		if ( ! empty( $item[4] ) )
			$class[] = $item[4];

		$class = $class ? ' class="' . join( ' ', $class ) . '"' : '';
		$id = ! empty( $item[5] ) ? ' id="' . preg_replace( '|[^a-zA-Z0-9_:.]|', '-', $item[5] ) . '"' : '';
		$img = '';
		// if the string 'none' (previously 'div') is passed instead of an URL, don't output the default menu image
		// so an icon can be added to div.wp-menu-image as background with CSS.
		if ( ! empty( $item[6] ) )
			$img = ( 'none' === $item[6] || 'div' === $item[6] ) ? '<br />' : '<img src="' . $item[6] . '" alt="" />';
		$arrow = '<div class="wp-menu-arrow"><div></div></div>';

		$title = wptexturize( $item[0] );
		$aria_label = esc_attr( strip_tags( $item[0] ) ); // strip the comment/plugins/updates bubbles spans but keep the pending number if any

		echo "\n\t<li$class$id>";

		if ( false !== strpos( $class, 'wp-menu-separator' ) ) {
			echo '<div class="separator"></div>';
		} elseif ( $submenu_as_parent && ! empty( $submenu_items ) ) {
			$submenu_items = array_values( $submenu_items );  // Re-index.
			$menu_hook = get_plugin_page_hook( $submenu_items[0][2], $item[2] );
			$menu_file = $submenu_items[0][2];
			if ( false !== ( $pos = strpos( $menu_file, '?' ) ) )
				$menu_file = substr( $menu_file, 0, $pos );
			if ( ! empty( $menu_hook ) || ( ('index.php' != $submenu_items[0][2]) && file_exists( WP_PLUGIN_DIR . "/$menu_file" ) ) ) {
				$admin_is_parent = true;
				echo "<a href='admin.php?page={$submenu_items[0][2]}'$class $aria_attributes>$arrow<div class='wp-menu-image'>$img</div><div class='wp-menu-name'>$title</div></a>";
			} else {
				echo "\n\t<a href='{$submenu_items[0][2]}'$class $aria_attributes>$arrow<div class='wp-menu-image'>$img</div><div class='wp-menu-name'>$title</div></a>";
			}
		} elseif ( ! empty( $item[2] ) && current_user_can( $item[1] ) ) {
			$menu_hook = get_plugin_page_hook( $item[2], 'admin.php' );
			$menu_file = $item[2];
			if ( false !== ( $pos = strpos( $menu_file, '?' ) ) )
				$menu_file = substr( $menu_file, 0, $pos );
			if ( ! empty( $menu_hook ) || ( ('index.php' != $item[2]) && file_exists( WP_PLUGIN_DIR . "/$menu_file" ) ) ) {
				$admin_is_parent = true;
				echo "\n\t<a href='admin.php?page={$item[2]}'$class $aria_attributes>$arrow<div class='wp-menu-image'>$img</div><div class='wp-menu-name'>{$item[0]}</div></a>";
			} else {
				echo "\n\t<a href='{$item[2]}'$class $aria_attributes>$arrow<div class='wp-menu-image'>$img</div><div class='wp-menu-name'>{$item[0]}</div></a>";
			}
		}

		if ( ! empty( $submenu_items ) ) {
			echo "\n\t<ul class='wp-submenu wp-submenu-wrap'>";
			echo "<li class='wp-submenu-head'>{$item[0]}</li>";

			$first = true;
			foreach ( $submenu_items as $sub_key => $sub_item ) {
				if ( ! current_user_can( $sub_item[1] ) )
					continue;

				$class = array();
				if ( $first ) {
					$class[] = 'wp-first-item';
					$first = false;
				}

				$menu_file = $item[2];

				if ( false !== ( $pos = strpos( $menu_file, '?' ) ) )
					$menu_file = substr( $menu_file, 0, $pos );

				// Handle current for post_type=post|page|foo pages, which won't match $self.
				$self_type = ! empty( $typenow ) ? $self . '?post_type=' . $typenow : 'nothing';

				if ( isset( $submenu_file ) ) {
					if ( $submenu_file == $sub_item[2] )
						$class[] = 'current';
				// If plugin_page is set the parent must either match the current page or not physically exist.
				// This allows plugin pages with the same hook to exist under different parents.
				} else if (
					( ! isset( $plugin_page ) && $self == $sub_item[2] ) ||
					( isset( $plugin_page ) && $plugin_page == $sub_item[2] && ( $item[2] == $self_type || $item[2] == $self || file_exists($menu_file) === false ) )
				) {
					$class[] = 'current';
				}

				$class = $class ? ' class="' . join( ' ', $class ) . '"' : '';

				$menu_hook = get_plugin_page_hook($sub_item[2], $item[2]);
				$sub_file = $sub_item[2];
				if ( false !== ( $pos = strpos( $sub_file, '?' ) ) )
					$sub_file = substr($sub_file, 0, $pos);

				$title = wptexturize($sub_item[0]);

				if ( ! empty( $menu_hook ) || ( ('index.php' != $sub_item[2]) && file_exists( WP_PLUGIN_DIR . "/$sub_file" ) ) ) {
					// If admin.php is the current page or if the parent exists as a file in the plugins or admin dir
					if ( (!$admin_is_parent && file_exists(WP_PLUGIN_DIR . "/$menu_file") && !is_dir(WP_PLUGIN_DIR . "/{$item[2]}")) || file_exists($menu_file) )
						$sub_item_url = add_query_arg( array('page' => $sub_item[2]), $item[2] );
					else
						$sub_item_url = add_query_arg( array('page' => $sub_item[2]), 'admin.php' );

					$sub_item_url = esc_url( $sub_item_url );
					echo "<li$class><a href='$sub_item_url'$class>$title</a></li>";
				} else {
					echo "<li$class><a href='{$sub_item[2]}'$class>$title</a></li>";
				}
			}
			echo "</ul>";
		}
		echo "</li>";
	}

	echo '<li id="collapse-menu" class="hide-if-no-js"><div id="collapse-button"><div></div></div>';
	echo '<span>' . esc_html__( 'Collapse menu' ) . '</span>';
	echo '</li>';
}

?>

<div id="adminmenuback"></div>
<div id="adminmenuwrap">
<div id="adminmenushadow"></div>
<ul id="adminmenu" role="navigation">

<?php

_wp_menu_output( $menu, $submenu );
do_action( 'adminmenu' );

?>
</ul>
</div>
