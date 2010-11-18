<?php
/**
 * Themes List Table class.
 *
 * @package WordPress
 * @subpackage List_Table
 * @since 3.1.0
 */
class WP_Themes_List_Table extends WP_List_Table {

	var $search = array();
	var $features = array();

	function check_permissions() {
		if ( !current_user_can('switch_themes') && !current_user_can('edit_theme_options') )
			wp_die( __( 'Cheatin&#8217; uh?' ) );
	}

	function prepare_items() {
		global $ct;

		$ct = current_theme_info();

		$themes = get_allowed_themes();

		$search = !empty( $_REQUEST['s'] ) ? trim( stripslashes( $_REQUEST['s'] ) ) : '';

		if ( '' !== $search ) {
			$this->search = array_merge( $this->search, array_filter( array_map( 'trim', explode( ',', $search ) ) ) );
			$this->search = array_unique( $this->search );
		}

		if ( !empty( $_REQUEST['features'] ) ) {
			$this->features = $_REQUEST['features'];
			$this->features = array_map( 'trim', $this->features );
			$this->features = array_map( 'sanitize_title_with_dashes', $this->features );
			$this->features = array_unique( $this->features );
		}

		if ( $this->search || $this->features ) {
			foreach ( $themes as $key => $theme ) {
				if ( !$this->search_theme( $theme ) )
					unset( $themes[ $key ] );
			}
		}

		unset( $themes[$ct->name] );
		uksort( $themes, "strnatcasecmp" );

		$per_page = 15;
		$page = $this->get_pagenum();

		$start = ( $page - 1 ) * $per_page;

		$this->items = array_slice( $themes, $start, $per_page );

		$this->set_pagination_args( array(
			'total_items' => count( $themes ),
			'per_page' => $per_page,
		) );
	}

	function no_items() {
		if ( current_user_can( 'install_themes' ) )
			printf( __( 'You only have one theme installed right now. Live a little! You can choose from over 1,000 free themes in the WordPress.org Theme Directory at any time: just click on the <em><a href="%s">Install Themes</a></em> tab above.' ), 'theme-install.php' );
		else
			printf( __( 'Only the current theme is available to you. Contact the %s administrator for information about accessing additional themes.' ), get_site_option( 'site_name' ) );
	}

	function display_table() {
?>
		<div class="tablenav">
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

	function get_columns() {
		return array();
	}

	function display_rows() {
		$themes = $this->items;
		$theme_names = array_keys( $themes );
		natcasesort( $theme_names );

		$table = array();
		$rows = ceil( count( $theme_names ) / 3 );
		for ( $row = 1; $row <= $rows; $row++ )
			for ( $col = 1; $col <= 3; $col++ )
				$table[$row][$col] = array_shift( $theme_names );

		foreach ( $table as $row => $cols ) {
?>
<tr>
<?php
foreach ( $cols as $col => $theme_name ) {
	$class = array( 'available-theme' );
	if ( $row == 1 ) $class[] = 'top';
	if ( $col == 1 ) $class[] = 'left';
	if ( $row == $rows ) $class[] = 'bottom';
	if ( $col == 3 ) $class[] = 'right';
?>
	<td class="<?php echo join( ' ', $class ); ?>">
<?php if ( !empty( $theme_name ) ) :
	$template = $themes[$theme_name]['Template'];
	$stylesheet = $themes[$theme_name]['Stylesheet'];
	$title = $themes[$theme_name]['Title'];
	$version = $themes[$theme_name]['Version'];
	$description = $themes[$theme_name]['Description'];
	$author = $themes[$theme_name]['Author'];
	$screenshot = $themes[$theme_name]['Screenshot'];
	$stylesheet_dir = $themes[$theme_name]['Stylesheet Dir'];
	$template_dir = $themes[$theme_name]['Template Dir'];
	$parent_theme = $themes[$theme_name]['Parent Theme'];
	$theme_root = $themes[$theme_name]['Theme Root'];
	$theme_root_uri = $themes[$theme_name]['Theme Root URI'];
	$preview_link = esc_url( get_option( 'home' ) . '/' );
	if ( is_ssl() )
		$preview_link = str_replace( 'http://', 'https://', $preview_link );
	$preview_link = htmlspecialchars( add_query_arg( array( 'preview' => 1, 'template' => $template, 'stylesheet' => $stylesheet, 'TB_iframe' => 'true' ), $preview_link ) );
	$preview_text = esc_attr( sprintf( __( 'Preview of &#8220;%s&#8221;' ), $title ) );
	$tags = $themes[$theme_name]['Tags'];
	$thickbox_class = 'thickbox thickbox-preview';
	$activate_link = wp_nonce_url( "themes.php?action=activate&amp;template=".urlencode( $template )."&amp;stylesheet=".urlencode( $stylesheet ), 'switch-theme_' . $template );
	$activate_text = esc_attr( sprintf( __( 'Activate &#8220;%s&#8221;' ), $title ) );
	$actions = array();
	$actions[] = '<a href="' . $activate_link .  '" class="activatelink" title="' . $activate_text . '">' . __( 'Activate' ) . '</a>';
	$actions[] = '<a href="' . $preview_link . '" class="thickbox thickbox-preview" title="' . esc_attr( sprintf( __( 'Preview &#8220;%s&#8221;' ), $theme_name ) ) . '">' . __( 'Preview' ) . '</a>';
	if ( current_user_can( 'delete_themes' ) )
		$actions[] = '<a class="submitdelete deletion" href="' . wp_nonce_url( "themes.php?action=delete&amp;template=$stylesheet", 'delete-theme_' . $stylesheet ) . '" onclick="' . "return confirm( '" . esc_js( sprintf( __( "You are about to delete this theme '%s'\n  'Cancel' to stop, 'OK' to delete." ), $theme_name ) ) . "' );" . '">' . __( 'Delete' ) . '</a>';
	$actions = apply_filters( 'theme_action_links', $actions, $themes[$theme_name] );

	$actions = implode ( ' | ', $actions );
?>
		<a href="<?php echo $preview_link; ?>" class="<?php echo $thickbox_class; ?> screenshot">
<?php if ( $screenshot ) : ?>
			<img src="<?php echo $theme_root_uri . '/' . $stylesheet . '/' . $screenshot; ?>" alt="" />
<?php endif; ?>
		</a>
<h3><?php
	/* translators: 1: theme title, 2: theme version, 3: theme author */
	printf( __( '%1$s %2$s by %3$s' ), $title, $version, $author ) ; ?></h3>
<p class="description"><?php echo $description; ?></p>
<span class='action-links'><?php echo $actions ?></span>
	<?php if ( current_user_can( 'edit_themes' ) && $parent_theme ) {
	/* translators: 1: theme title, 2:  template dir, 3: stylesheet_dir, 4: theme title, 5: parent_theme */ ?>
	<p><?php printf( __( 'The template files are located in <code>%2$s</code>. The stylesheet files are located in <code>%3$s</code>. <strong>%4$s</strong> uses templates from <strong>%5$s</strong>. Changes made to the templates will affect both themes.' ), $title, str_replace( WP_CONTENT_DIR, '', $template_dir ), str_replace( WP_CONTENT_DIR, '', $stylesheet_dir ), $title, $parent_theme ); ?></p>
<?php } else { ?>
	<p><?php printf( __( 'All of this theme&#8217;s files are located in <code>%2$s</code>.' ), $title, str_replace( WP_CONTENT_DIR, '', $template_dir ), str_replace( WP_CONTENT_DIR, '', $stylesheet_dir ) ); ?></p>
<?php } ?>
<?php if ( $tags ) : ?>
<p><?php _e( 'Tags:' ); ?> <?php echo join( ', ', $tags ); ?></p>
<?php endif; ?>
		<?php theme_update_available( $themes[$theme_name] ); ?>
<?php endif; // end if not empty theme_name ?>
	</td>
<?php } // end foreach $cols ?>
</tr>
<?php } // end foreach $table
	}

	function search_theme( $theme ) {
		$matched = 0;

		// Match all phrases
		if ( count( $this->search ) > 0 ) {
			foreach ( $this->search as $word ) {
				$matched = 0;

				// In a tag?
				if ( in_array( $word, array_map( 'sanitize_title_with_dashes', $theme['Tags'] ) ) )
					$matched = 1;

				// In one of the fields?
				foreach ( array( 'Name', 'Title', 'Description', 'Author', 'Template', 'Stylesheet' ) AS $field ) {
					if ( stripos( $theme[$field], $word ) !== false )
						$matched++;
				}

				if ( $matched == 0 )
					return false;
			}
		}

		// Now search the features
		if ( count( $this->features ) > 0 ) {
			foreach ( $this->features as $word ) {
				// In a tag?
				if ( !in_array( $word, array_map( 'sanitize_title_with_dashes', $theme['Tags'] ) ) )
					return false;
			}
		}

		// Only get here if each word exists in the tags or one of the fields
		return true;
	}
}

?>
