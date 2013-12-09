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

	function __construct( $args = array() ) {
		parent::__construct( array(
			'ajax' => true,
			'screen' => isset( $args['screen'] ) ? $args['screen'] : null,
		) );
	}

	function ajax_user_can() {
		// Do not check edit_theme_options here. AJAX calls for available themes require switch_themes.
		return current_user_can( 'switch_themes' );
	}

	function prepare_items() {
		$themes = wp_get_themes( array( 'allowed' => true ) );

		if ( ! empty( $_REQUEST['s'] ) )
			$this->search_terms = array_unique( array_filter( array_map( 'trim', explode( ',', strtolower( wp_unslash( $_REQUEST['s'] ) ) ) ) ) );

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

		$per_page = 36;
		$page = $this->get_pagenum();

		$start = ( $page - 1 ) * $per_page;

		$this->items = array_slice( $themes, $start, $per_page, true );

		$this->set_pagination_args( array(
			'total_items' => count( $themes ),
			'per_page' => $per_page,
			'infinite_scroll' => true,
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
			<span class="spinner"></span>
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

	function display_rows_or_placeholder() {
		if ( $this->has_items() ) {
			$this->display_rows();
		} else {
			echo '<div class="no-items">';
			$this->no_items();
			echo '</div>';
		}
	}

	function display_rows() {
		$themes = $this->items;

		foreach ( $themes as $theme ):
			?><div class="available-theme"><?php

			$template   = $theme->get_template();
			$stylesheet = $theme->get_stylesheet();
			$title      = $theme->display('Name');
			$version    = $theme->display('Version');
			$author     = $theme->display('Author');

			$activate_link = wp_nonce_url( "themes.php?action=activate&amp;template=" . urlencode( $template ) . "&amp;stylesheet=" . urlencode( $stylesheet ), 'switch-theme_' . $stylesheet );

			$preview_link = esc_url( add_query_arg(
				array( 'preview' => 1, 'template' => urlencode( $template ), 'stylesheet' => urlencode( $stylesheet ), 'preview_iframe' => true, 'TB_iframe' => 'true' ),
				home_url( '/' ) ) );

			$actions = array();
			$actions['activate'] = '<a href="' . $activate_link . '" class="activatelink" title="'
				. esc_attr( sprintf( __( 'Activate &#8220;%s&#8221;' ), $title ) ) . '">' . __( 'Activate' ) . '</a>';

			$actions['preview'] = '<a href="' . $preview_link . '" class="hide-if-customize" title="'
				. esc_attr( sprintf( __( 'Preview &#8220;%s&#8221;' ), $title ) ) . '">' . __( 'Preview' ) . '</a>';

			if ( current_user_can( 'edit_theme_options' ) )
				$actions['preview'] .= '<a href="' . wp_customize_url( $stylesheet ) . '" class="load-customize hide-if-no-customize">'
					. __( 'Live Preview' ) . '</a>';

			if ( ! is_multisite() && current_user_can( 'delete_themes' ) )
				$actions['delete'] = '<a class="submitdelete deletion" href="' . wp_nonce_url( 'themes.php?action=delete&amp;stylesheet=' . urlencode( $stylesheet ), 'delete-theme_' . $stylesheet )
					. '" onclick="' . "return confirm( '" . esc_js( sprintf( __( "You are about to delete this theme '%s'\n  'Cancel' to stop, 'OK' to delete." ), $title ) )
					. "' );" . '">' . __( 'Delete' ) . '</a>';

			$actions       = apply_filters( 'theme_action_links', $actions, $theme );
			$actions       = apply_filters( "theme_action_links_$stylesheet", $actions, $theme );
			$delete_action = isset( $actions['delete'] ) ? '<div class="delete-theme">' . $actions['delete'] . '</div>' : '';
			unset( $actions['delete'] );

			?>

			<a href="<?php echo $preview_link; ?>" class="screenshot hide-if-customize">
				<?php if ( $screenshot = $theme->get_screenshot() ) : ?>
					<img src="<?php echo esc_url( $screenshot ); ?>" alt="" />
				<?php endif; ?>
			</a>
			<a href="<?php echo wp_customize_url( $stylesheet ); ?>" class="screenshot load-customize hide-if-no-customize">
				<?php if ( $screenshot = $theme->get_screenshot() ) : ?>
					<img src="<?php echo esc_url( $screenshot ); ?>" alt="" />
				<?php endif; ?>
			</a>

			<h3><?php echo $title; ?></h3>
			<div class="theme-author"><?php printf( __( 'By %s' ), $author ); ?></div>
			<div class="action-links">
				<ul>
					<?php foreach ( $actions as $action ): ?>
						<li><?php echo $action; ?></li>
					<?php endforeach; ?>
					<li class="hide-if-no-js"><a href="#" class="theme-detail"><?php _e('Details') ?></a></li>
				</ul>
				<?php echo $delete_action; ?>

				<?php theme_update_available( $theme ); ?>
			</div>

			<div class="themedetaildiv hide-if-js">
				<p><strong><?php _e('Version: '); ?></strong><?php echo $version; ?></p>
				<p><?php echo $theme->display('Description'); ?></p>
				<?php if ( $theme->parent() ) {
					printf( ' <p class="howto">' . __( 'This <a href="%1$s">child theme</a> requires its parent theme, %2$s.' ) . '</p>',
						__( 'http://codex.wordpress.org/Child_Themes' ),
						$theme->parent()->display( 'Name' ) );
				} ?>
			</div>

			</div>
		<?php
		endforeach;
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

	/**
	 * Send required variables to JavaScript land
	 *
	 * @since 3.4
	 * @access private
	 *
	 * @uses $this->features Array of all feature search terms.
	 * @uses get_pagenum()
	 * @uses _pagination_args['total_pages']
	 */
	 function _js_vars( $extra_args = array() ) {
		$search_string = isset( $_REQUEST['s'] ) ? esc_attr( wp_unslash( $_REQUEST['s'] ) ) : '';

		$args = array(
			'search' => $search_string,
			'features' => $this->features,
			'paged' => $this->get_pagenum(),
			'total_pages' => ! empty( $this->_pagination_args['total_pages'] ) ? $this->_pagination_args['total_pages'] : 1,
		);

		if ( is_array( $extra_args ) )
			$args = array_merge( $args, $extra_args );

		printf( "<script type='text/javascript'>var theme_list_args = %s;</script>\n", json_encode( $args ) );
		parent::_js_vars();
	}
}
