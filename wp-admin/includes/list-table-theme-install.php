<?php
/**
 * Theme Installer List Table class.
 *
 * @package WordPress
 * @subpackage List_Table
 * @since 3.1.0
 */
class WP_Theme_Install_Table extends WP_List_Table {

	function WP_Theme_Install_Table() {
		parent::WP_List_Table( array(
			'screen' => 'theme-install',
		) );
	}

	function check_permissions() {
		if ( ! current_user_can('install_themes') )
			wp_die( __( 'You do not have sufficient permissions to install themes on this site.' ) );
	}

	function prepare_items() {
		include( ABSPATH . 'wp-admin/includes/theme-install.php' );

		global $tabs, $tab, $paged, $type, $term, $theme_field_defaults;

		wp_reset_vars( array( 'tab' ) );

		$paged = $this->get_pagenum();

		$per_page = 30;

		// These are the tabs which are shown on the page,
		$tabs = array();
		$tabs['dashboard'] = __( 'Search' );
		if ( 'search' == $tab )
			$tabs['search']	= __( 'Search Results' );
		$tabs['upload'] = __( 'Upload' );
		$tabs['featured'] = _x( 'Featured','Theme Installer' );
		//$tabs['popular']  = _x( 'Popular','Theme Installer' );
		$tabs['new']      = _x( 'Newest','Theme Installer' );
		$tabs['updated']  = _x( 'Recently Updated','Theme Installer' );

		$nonmenu_tabs = array( 'theme-information' ); // Valid actions to perform which do not have a Menu item.

		$tabs = apply_filters( 'install_themes_tabs', $tabs );
		$nonmenu_tabs = apply_filters( 'install_themes_nonmenu_tabs', $nonmenu_tabs );

		// If a non-valid menu tab has been selected, And its not a non-menu action.
		if ( empty( $tab ) || ( ! isset( $tabs[ $tab ] ) && ! in_array( $tab, (array) $nonmenu_tabs ) ) )
			$tab = key( $tabs );

		$args = array( 'page' => $paged, 'per_page' => $per_page, 'fields' => $theme_field_defaults );

		switch ( $tab ) {
			case 'search':
				$type = isset( $_REQUEST['type'] ) ? stripslashes( $_REQUEST['type'] ) : '';
				$term = isset( $_REQUEST['s'] ) ? stripslashes( $_REQUEST['s'] ) : '';

				switch ( $type ) {
					case 'tag':
						$terms = explode( ',', $term );
						$terms = array_map( 'trim', $terms );
						$terms = array_map( 'sanitize_title_with_dashes', $terms );
						$args['tag'] = $terms;
						break;
					case 'term':
						$args['search'] = $term;
						break;
					case 'author':
						$args['author'] = $term;
						break;
				}

				if ( !empty( $_POST['features'] ) ) {
					$terms = $_POST['features'];
					$terms = array_map( 'trim', $terms );
					$terms = array_map( 'sanitize_title_with_dashes', $terms );
					$args['tag'] = $terms;
					$_REQUEST['s'] = implode( ',', $terms );
					$_REQUEST['type'] = 'tag';
				}

				add_action( 'install_themes_table_header', 'install_theme_search_form' );
				break;

			case 'featured':
			//case 'popular':
			case 'new':
			case 'updated':
				$args['browse'] = $tab;
				break;

			default:
				$args = false;
		}

		if ( !$args )
			return;

		$api = themes_api( 'query_themes', $args );

		if ( is_wp_error( $api ) )
			wp_die( $api->get_error_message() . '</p> <p class="hide-if-no-js"><a href="#" onclick="document.location.reload(); return false;">' . __( 'Try again' ) . '</a>' );

		$this->items = $api->themes;

		$this->set_pagination_args( array(
			'total_items' => $api->info['results'],
			'per_page' => $per_page,
		) );
	}

	function no_items() {
		_e( 'No themes match your request.' );
	}

	function get_views() {
		global $tabs, $tab;

		$display_tabs = array();
		foreach ( (array) $tabs as $action => $text ) {
			$class = ( $action == $tab ) ? ' class="current"' : '';
			$href = self_admin_url('theme-install.php?tab=' . $action);
			$display_tabs[$action] = "<a href='$href'$class>$text</a>";
		}

		return $display_tabs;
	}

	function get_columns() {
		return array();
	}

	function display_table() {
?>
		<div class="tablenav">
			<div class="alignleft actions">
				<?php do_action( 'install_themes_table_header' ); ?>
			</div>
			<?php $this->pagination( 'top' ); ?>
			<br class="clear" />
		</div>

		<table id="availablethemes" cellspacing="0" cellpadding="0">
			<tbody id="the-list" class="list:themes">
				<?php $this->display_rows(); ?>
			</tbody>
		</table>

		<div class="tablenav">
			<?php $this->pagination( 'bottom' ); ?>
			<br class="clear" />
		</div>
<?php
	}

	function display_rows() {
		$themes = $this->items;

		$rows = ceil( count( $themes ) / 3 );
		$table = array();
		$theme_keys = array_keys( $themes );
		for ( $row = 1; $row <= $rows; $row++ )
			for ( $col = 1; $col <= 3; $col++ )
				$table[$row][$col] = array_shift( $theme_keys );

		foreach ( $table as $row => $cols ) {
			echo "\t<tr>\n";
			foreach ( $cols as $col => $theme_index ) {
				$class = array( 'available-theme' );
				if ( $row == 1 ) $class[] = 'top';
				if ( $col == 1 ) $class[] = 'left';
				if ( $row == $rows ) $class[] = 'bottom';
				if ( $col == 3 ) $class[] = 'right';
				?>
				<td class="<?php echo join( ' ', $class ); ?>"><?php
					if ( isset( $themes[$theme_index] ) )
						display_theme( $themes[$theme_index] );
				?></td>
			<?php } // end foreach $cols
			echo "\t</tr>\n";
		} // end foreach $table
	}
}

?>