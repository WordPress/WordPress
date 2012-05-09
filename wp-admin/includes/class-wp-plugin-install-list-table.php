<?php
/**
 * Plugin Installer List Table class.
 *
 * @package WordPress
 * @subpackage List_Table
 * @since 3.1.0
 * @access private
 */
class WP_Plugin_Install_List_Table extends WP_List_Table {

	function ajax_user_can() {
		return current_user_can('install_plugins');
	}

	function prepare_items() {
		include( ABSPATH . 'wp-admin/includes/plugin-install.php' );

		global $tabs, $tab, $paged, $type, $term;

		wp_reset_vars( array( 'tab' ) );

		$paged = $this->get_pagenum();

		$per_page = 30;

		// These are the tabs which are shown on the page
		$tabs = array();
		$tabs['dashboard'] = __( 'Search' );
		if ( 'search' == $tab )
			$tabs['search']	= __( 'Search Results' );
		$tabs['upload'] = __( 'Upload' );
		$tabs['featured'] = _x( 'Featured','Plugin Installer' );
		$tabs['popular']  = _x( 'Popular','Plugin Installer' );
		$tabs['new']      = _x( 'Newest','Plugin Installer' );

		$nonmenu_tabs = array( 'plugin-information' ); //Valid actions to perform which do not have a Menu item.

		$tabs = apply_filters( 'install_plugins_tabs', $tabs );
		$nonmenu_tabs = apply_filters( 'install_plugins_nonmenu_tabs', $nonmenu_tabs );

		// If a non-valid menu tab has been selected, And its not a non-menu action.
		if ( empty( $tab ) || ( !isset( $tabs[ $tab ] ) && !in_array( $tab, (array) $nonmenu_tabs ) ) )
			$tab = key( $tabs );

		$args = array( 'page' => $paged, 'per_page' => $per_page );

		switch ( $tab ) {
			case 'search':
				$type = isset( $_REQUEST['type'] ) ? stripslashes( $_REQUEST['type'] ) : 'term';
				$term = isset( $_REQUEST['s'] ) ? stripslashes( $_REQUEST['s'] ) : '';

				switch ( $type ) {
					case 'tag':
						$args['tag'] = sanitize_title_with_dashes( $term );
						break;
					case 'term':
						$args['search'] = $term;
						break;
					case 'author':
						$args['author'] = $term;
						break;
				}

				add_action( 'install_plugins_table_header', 'install_search_form', 10, 0 );
				break;

			case 'featured':
			case 'popular':
			case 'new':
				$args['browse'] = $tab;
				break;

			default:
				$args = false;
		}

		if ( !$args )
			return;

		$api = plugins_api( 'query_plugins', $args );

		if ( is_wp_error( $api ) )
			wp_die( $api->get_error_message() . '</p> <p class="hide-if-no-js"><a href="#" onclick="document.location.reload(); return false;">' . __( 'Try again' ) . '</a>' );

		$this->items = $api->plugins;

		$this->set_pagination_args( array(
			'total_items' => $api->info['results'],
			'per_page' => $per_page,
		) );
	}

	function no_items() {
		_e( 'No plugins match your request.' );
	}

	function get_views() {
		global $tabs, $tab;

		$display_tabs = array();
		foreach ( (array) $tabs as $action => $text ) {
			$class = ( $action == $tab ) ? ' class="current"' : '';
			$href = self_admin_url('plugin-install.php?tab=' . $action);
			$display_tabs['plugin-install-'.$action] = "<a href='$href'$class>$text</a>";
		}

		return $display_tabs;
	}

	function display_tablenav( $which ) {
		if ( 'top' ==  $which ) { ?>
			<div class="tablenav top">
				<div class="alignleft actions">
					<?php do_action( 'install_plugins_table_header' ); ?>
				</div>
				<?php $this->pagination( $which ); ?>
				<img src="<?php echo esc_url( admin_url( 'images/wpspin_light.gif' ) ); ?>" class="ajax-loading list-ajax-loading" alt="" />
				<br class="clear" />
			</div>
		<?php } else { ?>
			<div class="tablenav bottom">
				<?php $this->pagination( $which ); ?>
				<img src="<?php echo esc_url( admin_url( 'images/wpspin_light.gif' ) ); ?>" class="ajax-loading list-ajax-loading" alt="" />
				<br class="clear" />
			</div>
		<?php
		}
	}

	function get_table_classes() {
		extract( $this->_args );

		return array( 'widefat', $plural );
	}

	function get_columns() {
		return array(
			'name'        => _x( 'Name', 'plugin name' ),
			'version'     => __( 'Version' ),
			'rating'      => __( 'Rating' ),
			'description' => __( 'Description' ),
		);
	}

	function display_rows() {
		$plugins_allowedtags = array(
			'a' => array( 'href' => array(),'title' => array(), 'target' => array() ),
			'abbr' => array( 'title' => array() ),'acronym' => array( 'title' => array() ),
			'code' => array(), 'pre' => array(), 'em' => array(),'strong' => array(),
			'ul' => array(), 'ol' => array(), 'li' => array(), 'p' => array(), 'br' => array()
		);

		list( $columns, $hidden ) = $this->get_column_info();

		$style = array();
		foreach ( $columns as $column_name => $column_display_name ) {
			$style[ $column_name ] = in_array( $column_name, $hidden ) ? 'style="display:none;"' : '';
		}

		foreach ( (array) $this->items as $plugin ) {
			if ( is_object( $plugin ) )
				$plugin = (array) $plugin;

			$title = wp_kses( $plugin['name'], $plugins_allowedtags );
			//Limit description to 400char, and remove any HTML.
			$description = strip_tags( $plugin['description'] );
			if ( strlen( $description ) > 400 )
				$description = mb_substr( $description, 0, 400 ) . '&#8230;';
			//remove any trailing entities
			$description = preg_replace( '/&[^;\s]{0,6}$/', '', $description );
			//strip leading/trailing & multiple consecutive lines
			$description = trim( $description );
			$description = preg_replace( "|(\r?\n)+|", "\n", $description );
			//\n => <br>
			$description = nl2br( $description );
			$version = wp_kses( $plugin['version'], $plugins_allowedtags );

			$name = strip_tags( $title . ' ' . $version );

			$author = $plugin['author'];
			if ( ! empty( $plugin['author'] ) )
				$author = ' <cite>' . sprintf( __( 'By %s' ), $author ) . '.</cite>';

			$author = wp_kses( $author, $plugins_allowedtags );

			$action_links = array();
			$action_links[] = '<a href="' . self_admin_url( 'plugin-install.php?tab=plugin-information&amp;plugin=' . $plugin['slug'] .
								'&amp;TB_iframe=true&amp;width=600&amp;height=550' ) . '" class="thickbox" title="' .
								esc_attr( sprintf( __( 'More information about %s' ), $name ) ) . '">' . __( 'Details' ) . '</a>';

			if ( current_user_can( 'install_plugins' ) || current_user_can( 'update_plugins' ) ) {
				$status = install_plugin_install_status( $plugin );

				switch ( $status['status'] ) {
					case 'install':
						if ( $status['url'] )
							$action_links[] = '<a class="install-now" href="' . $status['url'] . '" title="' . esc_attr( sprintf( __( 'Install %s' ), $name ) ) . '">' . __( 'Install Now' ) . '</a>';
						break;
					case 'update_available':
						if ( $status['url'] )
							$action_links[] = '<a href="' . $status['url'] . '" title="' . esc_attr( sprintf( __( 'Update to version %s' ), $status['version'] ) ) . '">' . sprintf( __( 'Update Now' ), $status['version'] ) . '</a>';
						break;
					case 'latest_installed':
					case 'newer_installed':
						$action_links[] = '<span title="' . esc_attr__( 'This plugin is already installed and is up to date' ) . ' ">' . _x( 'Installed', 'plugin' ) . '</span>';
						break;
				}
			}

			$action_links = apply_filters( 'plugin_install_action_links', $action_links, $plugin );
		?>
		<tr>
			<td class="name column-name"<?php echo $style['name']; ?>><strong><?php echo $title; ?></strong>
				<div class="action-links"><?php if ( !empty( $action_links ) ) echo implode( ' | ', $action_links ); ?></div>
			</td>
			<td class="vers column-version"<?php echo $style['version']; ?>><?php echo $version; ?></td>
			<td class="vers column-rating"<?php echo $style['rating']; ?>>
				<div class="star-holder" title="<?php printf( _n( '(based on %s rating)', '(based on %s ratings)', $plugin['num_ratings'] ), number_format_i18n( $plugin['num_ratings'] ) ) ?>">
					<div class="star star-rating" style="width: <?php echo esc_attr( str_replace( ',', '.', $plugin['rating'] ) ); ?>px"></div>
				</div>
			</td>
			<td class="desc column-description"<?php echo $style['description']; ?>><?php echo $description, $author; ?></td>
		</tr>
		<?php
		}
	}
}
