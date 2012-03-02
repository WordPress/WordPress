<?php
/**
 * Themes List Table class.
 *
 * @package WordPress
 * @subpackage List_Table
 * @since 3.1.0
 * @access private
 */
class WP_Themes_List_Table extends WP_List_Table {

	protected $search_terms = array();
	var $features = array();

	function __construct() {
		parent::__construct( array(
			'ajax' => true,
		) );
	}

	function ajax_user_can() {
		// Do not check edit_theme_options here. AJAX calls for available themes require switch_themes.
		return current_user_can('switch_themes');
	}

	function prepare_items() {
		$themes = wp_get_themes( array( 'allowed' => true ) );

		if ( ! empty( $_REQUEST['s'] ) )
			$this->search_terms = array_unique( array_filter( array_map( 'trim', explode( ',', strtolower( stripslashes( $_REQUEST['s'] ) ) ) ) ) );

		if ( ! empty( $_REQUEST['features'] ) )
			$this->features = $_REQUEST['features'];

		if ( $this->search_terms || $this->features ) {
			foreach ( $themes as $key => $theme ) {
				if ( ! $this->search_theme( $theme ) )
					unset( $themes[ $key ] );
			}
		}

		unset( $themes[ get_option( 'stylesheet' ) ] );
		WP_Theme::sort_by_name( $themes );

		$per_page = 999;
		$page = $this->get_pagenum();

		$start = ( $page - 1 ) * $per_page;

		$this->items = array_slice( $themes, $start, $per_page, true );

		$this->set_pagination_args( array(
			'total_items' => count( $themes ),
			'per_page' => $per_page,
		) );
	}

	function no_items() {
		if ( $this->search_terms || $this->features ) {
			_e( 'No items found.' );
			return;
		}

		if ( is_multisite() ) {
			if ( current_user_can( 'install_themes' ) && current_user_can( 'manage_network_themes' ) ) {
				printf( __( 'You only have one theme enabled for this site right now. Visit the Network Admin to <a href="%1$s">enable</a> or <a href="%2$s">install</a> more themes.' ), network_admin_url( 'site-themes.php?id=' . $GLOBALS['blog_id'] ), network_admin_url( 'theme-install.php' ) );

				return;
			} elseif ( current_user_can( 'manage_network_themes' ) ) {
				printf( __( 'You only have one theme enabled for this site right now. Visit the Network Admin to <a href="%1$s">enable</a> more themes.' ), network_admin_url( 'site-themes.php?id=' . $GLOBALS['blog_id'] ) );

				return;
			}
			// else, fallthrough. install_themes doesn't help if you can't enable it.
		} else {
			if ( current_user_can( 'install_themes' ) ) {
				printf( __( 'You only have one theme installed right now. Live a little! You can choose from over 1,000 free themes in the WordPress.org Theme Directory at any time: just click on the <a href="%s">Install Themes</a> tab above.' ), admin_url( 'theme-install.php' ) );

				return;
			}
		}
		// Fallthrough.
		printf( __( 'Only the current theme is available to you. Contact the %s administrator for information about accessing additional themes.' ), get_site_option( 'site_name' ) );
	}

	function tablenav( $which = 'top' ) {
		if ( $this->get_pagination_arg( 'total_pages' ) <= 1 )
			return;
		?>
		<div class="tablenav themes <?php echo $which; ?>">
			<?php $this->pagination( $which ); ?>
		   <img src="<?php echo esc_url( admin_url( 'images/wpspin_light.gif' ) ); ?>" class="ajax-loading list-ajax-loading" alt="" />
		  <br class="clear" />
		</div>
		<?php
	}

	function display() {
		wp_nonce_field( "fetch-list-" . get_class( $this ), '_ajax_fetch_list_nonce' );
?>
		<?php $this->tablenav( 'top' ); ?>

		<div id="availablethemes">
			<?php $this->display_rows_or_placeholder(); ?>
		</div>

		<?php $this->tablenav( 'bottom' ); ?>
<?php
	}

	function get_columns() {
		return array();
	}

	function display_rows() {
		$themes = $this->items;

		foreach ( $themes as $theme ) {
			echo '<div class="available-theme">';

			$template = $theme->get_template();
			$stylesheet = $theme->get_stylesheet();

			$title = $theme->display('Name');
			$version = $theme->display('Version');
			$author = $theme->display('Author');
 
			$activate_link = wp_nonce_url( "themes.php?action=activate&amp;template=" . urlencode( $template ) . "&amp;stylesheet=" . urlencode( $stylesheet ), 'switch-theme_' . $template );
			$preview_link = esc_url( add_query_arg(
				array( 'preview' => 1, 'template' => $template, 'stylesheet' => $stylesheet, 'preview_iframe' => true, 'TB_iframe' => 'true' ),
				home_url( '/' ) ) );
 
			$actions = array();
			$actions[] = '<a href="' . $activate_link . '" class="activatelink" title="'
				. esc_attr( sprintf( __( 'Activate &#8220;%s&#8221;' ), $title ) ) . '">' . __( 'Activate' ) . '</a>';
			$actions[] = '<a href="' . $preview_link . '" class="thickbox thickbox-preview" title="'
				. esc_attr( sprintf( __( 'Preview &#8220;%s&#8221;' ), $title ) ) . '">' . __( 'Preview' ) . '</a>';
			if ( ! is_multisite() && current_user_can( 'delete_themes' ) )
				$actions[] = '<a class="submitdelete deletion" href="' . wp_nonce_url( "themes.php?action=delete&amp;template=$stylesheet", 'delete-theme_' . $stylesheet )
					. '" onclick="' . "return confirm( '" . esc_js( sprintf( __( "You are about to delete this theme '%s'\n  'Cancel' to stop, 'OK' to delete." ), $title ) )
					. "' );" . '">' . __( 'Delete' ) . '</a>';
 
			$actions = apply_filters( 'theme_action_links', $actions, $theme );
 
			$actions = implode ( ' | ', $actions );
			?>
			<a href="<?php echo $preview_link; ?>" class="thickbox thickbox-preview screenshot">
			<?php if ( $screenshot = $theme->get_screenshot() ) : ?>
				<img src="<?php echo esc_url( $screenshot ); ?>" alt="" />
			<?php endif; ?>
			</a>
			<h3><?php
			/* translators: 1: theme title, 2: theme version, 3: theme author */
			printf( __( '%1$s %2$s by %3$s' ), $title, $version, $author ) ; ?></h3>
 
			<span class='action-links'><?php echo $actions ?></span>
			<span class="separator hide-if-no-js">| </span><a href="#" class="theme-detail hide-if-no-js" tabindex='4'><?php _e('Details') ?></a>
			<div class="themedetaildiv hide-if-js">
			<p><?php echo $theme->display('Description'); ?></p>
			<?php if ( current_user_can( 'edit_themes' ) && $theme->parent() ) :
				/* translators: 1: theme title, 2:  template dir, 3: stylesheet_dir, 4: theme title, 5: parent_theme */ ?>
				<p><?php printf( __( 'The template files are located in <code>%2$s</code>. The stylesheet files are located in <code>%3$s</code>. <strong>%4$s</strong> uses templates from <strong>%5$s</strong>. Changes made to the templates will affect both themes.' ),
					$title, str_replace( WP_CONTENT_DIR, '', $theme->get_template_directory() ), str_replace( WP_CONTENT_DIR, '', $theme->get_stylesheet_directory() ), $title, $theme->parent()->display('Name') ); ?></p>
			<?php else :
					/* translators: 1: theme title, 2:  template dir, 3: stylesheet_dir */ ?>
				<p><?php printf( __( 'All of this theme&#8217;s files are located in <code>%2$s</code>.' ),
					$title, str_replace( WP_CONTENT_DIR, '', $theme->get_template_directory() ), str_replace( WP_CONTENT_DIR, '', $theme->get_stylesheet_directory() ) ); ?></p>
			<?php endif; ?>
			</div>
			<?php theme_update_available( $theme ); ?>
			</div>
		<?php
		}
	}

	function search_theme( $theme ) {
		// Search the features
		foreach ( $this->features as $word ) {
			if ( ! in_array( $word, $theme->get('Tags') ) )
				return false;
		}

		// Match all phrases
		foreach ( $this->search_terms as $word ) {
			if ( in_array( $word, $theme->get('Tags') ) )
				continue;

			foreach ( array( 'Name', 'Description', 'Author', 'AuthorURI' ) as $header ) {
				// Don't mark up; Do translate.
				if ( false !== stripos( $theme->display( $header, false, true ), $word ) )
					continue 2;
			}

			if ( false !== stripos( $theme->get_stylesheet(), $word ) )
				continue;

			if ( false !== stripos( $theme->get_template(), $word ) )
				continue;

			return false;
		}

		return true;
	}
}
