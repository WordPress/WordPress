<?php
/**
 * WordPress Administration Screen API.
 *
 * @package WordPress
 * @subpackage Administration
 */

/**
 * Get the column headers for a screen
 *
 * @since 2.7.0
 *
 * @param string|WP_Screen $screen The screen you want the headers for
 * @return array Containing the headers in the format id => UI String
 */
function get_column_headers( $screen ) {
	if ( is_string( $screen ) )
		$screen = convert_to_screen( $screen );

	static $column_headers = array();

	if ( ! isset( $column_headers[ $screen->id ] ) )
		$column_headers[ $screen->id ] = apply_filters( 'manage_' . $screen->id . '_columns', array() );

	return $column_headers[ $screen->id ];
}

/**
 * Get a list of hidden columns.
 *
 * @since 2.7.0
 *
 * @param string|WP_Screen $screen The screen you want the hidden columns for
 * @return array
 */
function get_hidden_columns( $screen ) {
	if ( is_string( $screen ) )
		$screen = convert_to_screen( $screen );

	return (array) get_user_option( 'manage' . $screen->id . 'columnshidden' );
}

/**
 * Prints the meta box preferences for screen meta.
 *
 * @since 2.7.0
 *
 * @param string|WP_Screen $screen
 */
function meta_box_prefs( $screen ) {
	global $wp_meta_boxes;

	if ( is_string( $screen ) )
		$screen = convert_to_screen( $screen );

	if ( empty($wp_meta_boxes[$screen->id]) )
		return;

	$hidden = get_hidden_meta_boxes($screen);

	foreach ( array_keys($wp_meta_boxes[$screen->id]) as $context ) {
		foreach ( array_keys($wp_meta_boxes[$screen->id][$context]) as $priority ) {
			foreach ( $wp_meta_boxes[$screen->id][$context][$priority] as $box ) {
				if ( false == $box || ! $box['title'] )
					continue;
				// Submit box cannot be hidden
				if ( 'submitdiv' == $box['id'] || 'linksubmitdiv' == $box['id'] )
					continue;
				$box_id = $box['id'];
				echo '<label for="' . $box_id . '-hide">';
				echo '<input class="hide-postbox-tog" name="' . $box_id . '-hide" type="checkbox" id="' . $box_id . '-hide" value="' . $box_id . '"' . (! in_array($box_id, $hidden) ? ' checked="checked"' : '') . ' />';
				echo "{$box['title']}</label>\n";
			}
		}
	}
}

/**
 * Get Hidden Meta Boxes
 *
 * @since 2.7.0
 *
 * @param string|WP_Screen $screen Screen identifier
 * @return array Hidden Meta Boxes
 */
function get_hidden_meta_boxes( $screen ) {
	if ( is_string( $screen ) )
		$screen = convert_to_screen( $screen );

	$hidden = get_user_option( "metaboxhidden_{$screen->id}" );

	$use_defaults = ! is_array( $hidden );

	// Hide slug boxes by default
	if ( $use_defaults ) {
		$hidden = array();
		if ( 'post' == $screen->base ) {
			if ( 'post' == $screen->post_type || 'page' == $screen->post_type )
				$hidden = array('slugdiv', 'trackbacksdiv', 'postcustom', 'postexcerpt', 'commentstatusdiv', 'commentsdiv', 'authordiv', 'revisionsdiv');
			else
				$hidden = array( 'slugdiv' );
		}
		$hidden = apply_filters( 'default_hidden_meta_boxes', $hidden, $screen );
	}

	return apply_filters( 'hidden_meta_boxes', $hidden, $screen, $use_defaults );
}

/**
 * Register and configure an admin screen option
 *
 * @since 3.1.0
 *
 * @param string $option An option name.
 * @param mixed $args Option-dependent arguments.
 * @return void
 */
function add_screen_option( $option, $args = array() ) {
	$current_screen = get_current_screen();

	if ( ! $current_screen )
		return;

	$current_screen->add_option( $option, $args );
}

/**
 * Displays a screen icon.
 *
 * @uses get_screen_icon()
 * @since 2.7.0
 *
 * @param string|WP_Screen $screen Optional. Accepts a screen object (and defaults to the current screen object)
 * 	which it uses to determine an icon HTML ID. Or, if a string is provided, it is used to form the icon HTML ID.
 */
function screen_icon( $screen = '' ) {
	echo get_screen_icon( $screen );
}

/**
 * Gets a screen icon.
 *
 * @since 3.2.0
 *
 * @param string|WP_Screen $screen Optional. Accepts a screen object (and defaults to the current screen object)
 * 	which it uses to determine an icon HTML ID. Or, if a string is provided, it is used to form the icon HTML ID.
 * @return string HTML for the screen icon.
 */
function get_screen_icon( $screen = '' ) {
	if ( empty( $screen ) )
		$screen = get_current_screen();
	elseif ( is_string( $screen ) )
		$icon_id = $screen;

	$class = 'icon32';

	if ( empty( $icon_id ) ) {
		if ( ! empty( $screen->parent_base ) )
			$icon_id = $screen->parent_base;
		else
			$icon_id = $screen->base;

		if ( 'page' == $screen->post_type )
			$icon_id = 'edit-pages';

		if ( $screen->post_type )
			$class .= ' ' . sanitize_html_class( 'icon32-posts-' . $screen->post_type );
	}

	return '<div id="icon-' . esc_attr( $icon_id ) . '" class="' . $class . '"><br /></div>';
}

/**
 * Get the current screen object
 *
 * @since 3.1.0
 *
 * @return object Current screen object
 */
function get_current_screen() {
	global $current_screen;

	if ( ! isset( $current_screen ) )
		return null;

	return $current_screen;
}

/**
 * Set the current screen object
 *
 * @since 3.0.0
 * @uses $current_screen
 *
 * @param mixed $hook_name Optional. The hook name (also known as the hook suffix) used to determine the screen,
 *	or an existing screen object.
 */
function set_current_screen( $hook_name =  '' ) {
	WP_Screen::get( $hook_name )->set_current_screen();
}

/**
 * A class representing the admin screen.
 *
 * @since 3.3.0
 * @access public
 */
final class WP_Screen {
	/**
	 * Any action associated with the screen. 'add' for *-add.php and *-new.php screens.  Empty otherwise.
	 *
	 * @since 3.3.0
	 * @var string
	 * @access public
	 */
	public $action;

	/**
	 * The base type of the screen.  This is typically the same as $id but with any post types and taxonomies stripped.
	 * For example, for an $id of 'edit-post' the base is 'edit'.
	 *
	 * @since 3.3.0
	 * @var string
	 * @access public
	 */
	public $base;

	/**
	 * The unique ID of the screen.
	 *
	 * @since 3.3.0
	 * @var string
	 * @access public
	 */
	public $id;

	/**
	 * Whether the screen is in the network admin.
	 *
	 * @since 3.3.0
	 * @var bool
	 * @access public
	 */
	public $is_network;

	/**
	 * Whether the screen is in the user admin.
	 *
	 * @since 3.3.0
	 * @var bool
	 * @access public
	 */
	public $is_user;

	/**
	 * The base menu parent.
	 * This is derived from $parent_file by removing the query string and any .php extension.
	 * $parent_file values of 'edit.php?post_type=page' and 'edit.php?post_type=post' have a $parent_base of 'edit'.
	 *
	 * @since 3.3.0
	 * @var string
	 * @access public
	 */
	public $parent_base;

	/**
	 * The parent_file for the screen per the admin menu system.
	 * Some $parent_file values are 'edit.php?post_type=page', 'edit.php', and 'options-general.php'.
	 *
	 * @since 3.3.0
	 * @var string
	 * @access public
	 */
	public $parent_file;

	/**
	 * The post type associated with the screen, if any.
	 * The 'edit.php?post_type=page' screen has a post type of 'page'.
	 * The 'edit-tags.php?taxonomy=$taxonomy&post_type=page' screen has a post type of 'page'.
	 *
	 * @since 3.3.0
	 * @var string
	 * @access public
	 */
	public $post_type;

	/**
	 * The taxonomy associated with the screen, if any.
	 * The 'edit-tags.php?taxonomy=category' screen has a taxonomy of 'category'.
	 * @since 3.3.0
	 * @var string
	 * @access public
	 */
	public $taxonomy;

	/**
	 * The help tab data associated with the screen, if any.
 	 *
 	 * @since 3.3.0
 	 * @var array
 	 * @access private
 	 */
	private $_help_tabs = array();

 	/**
	 * The help sidebar data associated with screen, if any.
	 *
	 * @since 3.3.0
	 * @var string
	 * @access private
 	 */
	private $_help_sidebar = '';

	/**
	 * Stores old string-based help.
	 */
	private static $_old_compat_help = array();

	/**
	 * The screen options associated with screen, if any.
	 *
	 * @since 3.3.0
	 * @var array
	 * @access private
	 */
	private $_options = array();

	/**
	 * The screen object registry.
	 *
	 * @since 3.3.0
	 * @var array
	 * @access private
	 */
	private static $_registry = array();

	/**
	 * Stores the result of the public show_screen_options function.
	 *
	 * @since 3.3.0
	 * @var bool
	 * @access private
	 */
	private $_show_screen_options;

	/**
	 * Stores the 'screen_settings' section of screen options.
	 *
	 * @since 3.3.0
	 * @var string
	 * @access private
	 */
	private $_screen_settings;

 	/**
	 * Fetches a screen object.
 	 *
 	 * @since 3.3.0
	 * @access public
 	 *
	 * @param string $hook_name Optional. The hook name (also known as the hook suffix) used to determine the screen.
	 * 	Defaults to the current $hook_suffix global.
	 * @return WP_Screen Screen object.
 	 */
	public static function get( $hook_name = '' ) {

		if ( is_a( $hook_name, 'WP_Screen' ) )
			return $hook_name;

		$post_type = $taxonomy = null;
		$is_network = $is_user = false;
		$action = '';

		if ( $hook_name )
			$id = $hook_name;
		else
			$id = $GLOBALS['hook_suffix'];

		// For those pesky meta boxes.
		if ( $hook_name && post_type_exists( $hook_name ) ) {
			$post_type = $id;
			$id = 'post'; // changes later. ends up being $base.
		} else {
			if ( '.php' == substr( $id, -4 ) )
				$id = substr( $id, 0, -4 );

			if ( 'post-new' == $id || 'link-add' == $id || 'media-new' == $id || 'user-new' == $id ) {
				$id = substr( $id, 0, -4 );
				$action = 'add';
			}
		}

		if ( ! $post_type && $hook_name ) {
			if ( '-network' == substr( $id, -8 ) ) {
				$id = substr( $id, 0, -8 );
				$is_network = true;
			} elseif ( '-user' == substr( $id, -5 ) ) {
				$id = substr( $id, 0, -5 );
				$is_user = true;
			}

			$id = sanitize_key( $id );
			if ( 'edit-comments' != $id && 'edit-tags' != $id && 'edit-' == substr( $id, 0, 5 ) ) {
				$maybe = substr( $id, 5 );
				if ( taxonomy_exists( $maybe ) ) {
 					$id = 'edit-tags';
					$taxonomy = $maybe;
				} elseif ( post_type_exists( $maybe ) ) {
					$id = 'edit';
					$post_type = $maybe;
				}
 			}
		} else {
			$is_network = is_network_admin();
			$is_user = is_user_admin();
		}

		if ( 'index' == $id )
			$id = 'dashboard';

		$base = $id;

		// If this is the current screen, see if we can be more accurate for post types and taxonomies.
		if ( ! $hook_name ) {
			if ( isset( $_REQUEST['post_type'] ) )
				$post_type = post_type_exists( $_REQUEST['post_type'] ) ? $_REQUEST['post_type'] : false;
			if ( isset( $_REQUEST['taxonomy'] ) )
				$taxonomy = taxonomy_exists( $_REQUEST['taxonomy'] ) ? $_REQUEST['taxonomy'] : false;

			switch ( $base ) {
				case 'post' :
					if ( isset( $_GET['post'] ) )
						$post_id = (int) $_GET['post'];
					elseif ( isset( $_POST['post_ID'] ) )
						$post_id = (int) $_POST['post_ID'];
					else
						$post_id = 0;

					if ( $post_id ) {
						$post = get_post( $post_id );
						if ( $post )
							$post_type = $post->post_type;
					}
					break;
				case 'edit-tags' :
					if ( null === $post_type && is_object_in_taxonomy( 'post', $taxonomy ? $taxonomy : 'post_tag' ) )
						$post_type = 'post';
					break;
			}
 		}

		switch ( $base ) {
			case 'post' :
				if ( null === $post_type )
					$post_type = 'post';
				$id = $post_type;
				break;
			case 'edit' :
				if ( null === $post_type )
					$post_type = 'post';
				$id .= '-' . $post_type;
				break;
			case 'edit-tags' :
				if ( null === $taxonomy )
					$taxonomy = 'post_tag';
				$id = 'edit-' . $taxonomy;
				break;
		}

		if ( $is_network ) {
			$id   .= '-network';
			$base .= '-network';
		} elseif ( $is_user ) {
			$id   .= '-user';
			$base .= '-user';
 		}

		if ( isset( self::$_registry[ $id ] ) ) {
			$screen = self::$_registry[ $id ];
			if ( $screen === get_current_screen() )
				return $screen;
		} else {
			$screen = new WP_Screen();
			$screen->id     = $id;
		}

		$screen->base       = $base;
		$screen->action     = $action;
		$screen->post_type  = (string) $post_type;
		$screen->taxonomy   = (string) $taxonomy;
		$screen->is_user    = $is_user;
		$screen->is_network = $is_network;

		self::$_registry[ $id ] = $screen;

		return $screen;
 	}

	/**
	 * Makes the screen object the current screen.
	 *
	 * @see set_current_screen()
	 * @since 3.3.0
	 */
	function set_current_screen() {
		global $current_screen, $taxnow, $typenow;
		$current_screen = $this;
		$taxnow = $this->taxonomy;
		$typenow = $this->post_type;
		do_action( 'current_screen', $current_screen );
	}

	/**
	 * Constructor
	 *
	 * @since 3.3.0
	 * @access private
	 */
	private function __construct() {}

	/**
	 * Sets the old string-based contextual help for the screen.
	 *
	 * For backwards compatibility.
	 *
	 * @since 3.3.0
	 *
	 * @param WP_Screen $screen A screen object.
	 * @param string $help Help text.
	 */
	static function add_old_compat_help( $screen, $help ) {
		self::$_old_compat_help[ $screen->id ] = $help;
	}

	/**
	 * Set the parent information for the screen.
	 * This is called in admin-header.php after the menu parent for the screen has been determined.
	 *
	 * @since 3.3.0
	 *
	 * @param string $parent_file The parent file of the screen.  Typically the $parent_file global.
	 */
	function set_parentage( $parent_file ) {
		$this->parent_file = $parent_file;
		list( $this->parent_base ) = explode( '?', $parent_file );
		$this->parent_base = str_replace( '.php', '', $this->parent_base );
	}

	/**
	 * Adds an option for the screen.
	 * Call this in template files after admin.php is loaded and before admin-header.php is loaded to add screen options.
	 *
	 * @since 3.3.0
	 *
	 * @param string $option Option ID
	 * @param mixed $args Option-dependent arguments.
	 */
	public function add_option( $option, $args = array() ) {
		$this->_options[ $option ] = $args;
	}

	/**
	 * Gets the arguments for an option for the screen.
	 *
	 * @since 3.3.0
	 *
	 * @param string
	 */
	public function get_option( $option, $key = false ) {
		if ( ! isset( $this->_options[ $option ] ) )
			return null;
		if ( $key ) {
			if ( isset( $this->_options[ $option ][ $key ] ) )
				return $this->_options[ $option ][ $key ];
			return null;
		}
		return $this->_options[ $option ];
	}

	/**
	 * Add a help tab to the contextual help for the screen.
	 * Call this on the load-$pagenow hook for the relevant screen.
	 *
	 * @since 3.3.0
	 *
	 * @param array $args
	 * - string   - title    - Title for the tab.
	 * - string   - id       - Tab ID. Must be HTML-safe.
	 * - string   - content  - Help tab content in plain text or HTML. Optional.
	 * - callback - callback - A callback to generate the tab content. Optional.
	 *
	 */
	public function add_help_tab( $args ) {
		$defaults = array(
			'title'    => false,
			'id'       => false,
			'content'  => '',
			'callback' => false,
		);
		$args = wp_parse_args( $args, $defaults );

		$args['id'] = sanitize_html_class( $args['id'] );

		// Ensure we have an ID and title.
		if ( ! $args['id'] || ! $args['title'] )
			return;

		$this->_help_tabs[] = $args;
	}

	/**
	 * Removes a help tab from the contextual help for the screen.
	 *
	 * @since 3.3.0
	 *
	 * @param string $id The help tab ID.
	 */
	public function remove_help_tab( $id ) {
		unset( $this->_help_tabs[ $id ] );
	}

	/**
	 * Removes all help tabs from the contextual help for the screen.
	 *
	 * @since 3.3.0
	 */
	public function remove_help_tabs() {
		$this->_help_tabs = array();
	}

	/**
	 * Add a sidebar to the contextual help for the screen.
	 * Call this in template files after admin.php is loaded and before admin-header.php is loaded to add a sidebar to the contextual help.
	 *
	 * @since 3.3.0
	 *
	 * @param string $content Sidebar content in plain text or HTML.
	 */
	public function set_help_sidebar( $content ) {
		$this->_help_sidebar = $content;
	}

	/**
	 * Render the screen's help section.
	 *
	 * This will trigger the deprecated filters for backwards compatibility.
	 *
	 * @since 3.3.0
	 */
	public function render_screen_meta() {

		// Call old contextual_help_list filter.
		self::$_old_compat_help = apply_filters( 'contextual_help_list', self::$_old_compat_help, $this );

		$old_help = isset( self::$_old_compat_help[ $this->id ] ) ? self::$_old_compat_help[ $this->id ] : '';
		$old_help = apply_filters( 'contextual_help', $old_help, $this->id, $this );

		// Default help only if there is no old-style block of text and no new-style help tabs.
		if ( empty( $old_help ) && empty( $this->_help_tabs ) ) {
			$default_help = apply_filters( 'default_contextual_help', '' );
			if ( $default_help )
				$old_help = '<p>' . $default_help . '</p>';
		}

		if ( $old_help ) {
			$this->add_help_tab( array(
				'id'      => 'contextual-help',
				'title'   => __('Overview'),
				'content' => $old_help,
			) );
		}

		$has_sidebar = ! empty( $this->_help_sidebar );

		$help_class = 'hidden';
		if ( ! $has_sidebar )
			$help_class .= ' no-sidebar';

		// Time to render!
		?>
		<div id="screen-meta" class="metabox-prefs">

			<div id="contextual-help-wrap" class="<?php echo esc_attr( $help_class ); ?>">
				<div id="contextual-help-back"></div>
				<div id="contextual-help-columns">
					<div class="contextual-help-tabs">
						<ul>
						<?php foreach ( $this->_help_tabs as $i => $tab ):
							$link_id  = "tab-link-{$tab['id']}";
							$panel_id = "tab-panel-{$tab['id']}";
							$classes  = ( $i == 0 ) ? 'active' : '';
							?>

							<li id="<?php echo esc_attr( $link_id ); ?>" class="<?php echo esc_attr( $classes ); ?>">
								<a href="<?php echo esc_url( "#$panel_id" ); ?>">
									<?php echo esc_html( $tab['title'] ); ?>
								</a>
							</li>
						<?php endforeach; ?>
						</ul>
					</div>

					<?php if ( $has_sidebar ) : ?>
					<div class="contextual-help-sidebar">
						<?php echo self::$this->_help_sidebar; ?>
					</div>
					<?php endif; ?>

					<div class="contextual-help-tabs-wrap">
						<?php foreach ( $this->_help_tabs as $i => $tab ):
							$panel_id = "tab-panel-{$tab['id']}";
							$classes  = ( $i == 0 ) ? 'active' : '';
							$classes .= ' help-tab-content';
							?>

							<div id="<?php echo esc_attr( $panel_id ); ?>" class="<?php echo esc_attr( $classes ); ?>">
								<?php
								// Print tab content.
								echo $tab['content'];

								// If it exists, fire tab callback.
								if ( ! empty( $tab['callback'] ) )
									call_user_func_array( $tab['callback'], array( $this, $tab ) );
								?>
							</div>
						<?php endforeach; ?>
					</div>
				</div>
			</div>
		<?php
		// Add screen options
		if ( $this->show_screen_options() )
			$this->render_screen_options();
		?>
		</div>
		<?php
		if ( ! $this->_help_tabs && ! $this->show_screen_options() )
			return;
		?>
		<div id="screen-meta-links">
		<?php if ( $this->_help_tabs ) : ?>
			<div id="contextual-help-link-wrap" class="hide-if-no-js screen-meta-toggle">
			<a href="#contextual-help-wrap" id="contextual-help-link" class="show-settings"><?php _e( 'Help' ); ?></a>
			</div>
		<?php endif;
		if ( $this->show_screen_options() ) : ?>
			<div id="screen-options-link-wrap" class="hide-if-no-js screen-meta-toggle">
			<a href="#screen-options-wrap" id="show-settings-link" class="show-settings"><?php _e( 'Screen Options' ); ?></a>
			</div>
		<?php endif; ?>
		</div>
		<?php
	}

	public function show_screen_options() {
		global $wp_meta_boxes;

		if ( is_bool( $this->_show_screen_options ) )
			return $this->_show_screen_options;

		$columns = get_column_headers( $this );

		$show_screen = ! empty( $wp_meta_boxes[ $this->id ] ) || $columns || $this->get_option( 'per_page' );

		$this->_screen_settings = apply_filters( 'screen_settings', '', $this );

		switch ( $this->id ) {
			case 'widgets':
				$this->_screen_settings = '<p><a id="access-on" href="widgets.php?widgets-access=on">' . __('Enable accessibility mode') . '</a><a id="access-off" href="widgets.php?widgets-access=off">' . __('Disable accessibility mode') . "</a></p>\n";
				break;
		}

		if ( $this->_screen_settings || $this->_options )
			$show_screen = true;

		$this->_show_screen_options = apply_filters( 'screen_options_show_screen', $show_screen, $this );
		return $this->_show_screen_options;
	}

	/**
	 * Render the screen options tab.
	 *
	 * @since 3.3.0
	 */
	public function render_screen_options() {
		global $wp_meta_boxes, $wp_list_table;

		$columns = get_column_headers( $this );
		$hidden  = get_hidden_columns( $this );

		?>
		<div id="screen-options-wrap" class="hidden">
		<form id="adv-settings" action="" method="post">
		<?php
		if ( $this->get_option('overview') )
			echo $this->get_option('overview');
		if ( isset( $wp_meta_boxes[ $this->id ] ) ) : ?>
			<h5><?php _ex('Show on screen', 'Metaboxes') ?></h5>
			<div class="metabox-prefs">
				<?php
					meta_box_prefs( $this );

					if ( 'dashboard' === $this->id && current_user_can( 'edit_theme_options' ) ) {
						if ( isset( $_GET['welcome'] ) ) {
							$welcome_checked = empty( $_GET['welcome'] ) ? 0 : 1;
							update_user_meta( get_current_user_id(), 'show_welcome_panel', $welcome_checked );
						} else {
							$welcome_checked = get_user_meta( get_current_user_id(), 'show_welcome_panel', true );
							if ( 2 == $welcome_checked && wp_get_current_user()->user_email != get_option( 'admin_email' ) )
								$welcome_checked = false;
						}
						echo '<label for="wp_welcome_panel-hide">';
						echo '<input type="checkbox" id="wp_welcome_panel-hide"' . checked( (bool) $welcome_checked, true, false )  . ' />';
						echo __( 'Welcome' ) . "</label>\n";
					}
				?>
				<br class="clear" />
			</div>
			<?php endif;
			if ( ! empty( $columns ) ) : ?>
			<h5><?php echo ( isset( $columns['_title'] ) ?  $columns['_title'] :  _x('Show on screen', 'Columns') ) ?></h5>
			<div class="metabox-prefs">
				<?php
				$special = array('_title', 'cb', 'comment', 'media', 'name', 'title', 'username', 'blogname');

				foreach ( $columns as $column => $title ) {
					// Can't hide these for they are special
					if ( in_array( $column, $special ) )
						continue;
					if ( empty( $title ) )
						continue;

					if ( 'comments' == $column )
						$title = __( 'Comments' );
					$id = "$column-hide";
					echo '<label for="' . $id . '">';
					echo '<input class="hide-column-tog" name="' . $id . '" type="checkbox" id="' . $id . '" value="' . $column . '"' . checked( !in_array($column, $hidden), true, false ) . ' />';
					echo "$title</label>\n";
				}
				?>
				<br class="clear" />
			</div>
		<?php endif;

		$this->render_screen_layout();
		$this->render_per_page_options();
		echo $this->_screen_settings;

		?>
		<div><?php wp_nonce_field( 'screen-options-nonce', 'screenoptionnonce', false ); ?></div>
		</form>
		</div>
		<?php
	}

	/**
	 * Render the option for number of columns on the page
	 *
	 * @since 3.3.0
	 */
	function render_screen_layout() {
		global $screen_layout_columns;

		// Back compat for plugins using the filter instead of add_screen_option()
		$columns = apply_filters( 'screen_layout_columns', array(), $this->id, $this );

		if ( ! empty( $columns ) && isset( $columns[ $this->id ] ) )
			$this->add_option( 'layout_columns', array('max' => $columns[ $this->id ] ) );

		if ( ! $this->get_option('layout_columns') ) {
			$screen_layout_columns = 0;
			return;
		}

		$screen_layout_columns = get_user_option("screen_layout_$this->id");
		$num = $this->get_option( 'layout_columns', 'max' );

		if ( ! $screen_layout_columns || 'auto' == $screen_layout_columns ) {
			if ( $this->get_option( 'layout_columns', 'default' ) )
				$screen_layout_columns = $this->get_option( 'layout_columns', 'default' );
		}

		?>
		<h5><?php _e('Screen Layout'); ?></h5>
		<div class='columns-prefs'><?php
			_e('Number of Columns:');
			for ( $i = 1; $i <= $num; ++$i ):
				?>
				<label>
					<input type='radio' name='screen_columns' value='<?php echo esc_attr( $i ); ?>'
						<?php checked( $screen_layout_columns, $i ); ?> />
					<?php echo esc_html( $i ); ?>
				</label>
				<?php
			endfor; ?>
		</div>
		<?php
	}

	/**
	 * Render the items per page option
	 *
	 * @since 3.3.0
	 */
	function render_per_page_options() {
		if ( ! $this->get_option( 'per_page' ) )
			return;

		$per_page_label = $this->get_option( 'per_page', 'label' );

		$option = $this->get_option( 'per_page', 'option' );
		if ( ! $option )
			$option = str_replace( '-', '_', "{$this->id}_per_page" );

		$per_page = (int) get_user_option( $option );
		if ( empty( $per_page ) || $per_page < 1 ) {
			$per_page = $this->get_option( 'per_page', 'default' );
			if ( ! $per_page )
				$per_page = 20;
		}

		if ( 'edit_comments_per_page' == $option ) {
			$comment_status = isset( $_REQUEST['comment_status'] ) ? $_REQUEST['comment_status'] : 'all';
			$per_page = apply_filters( 'comments_per_page', $per_page, $comment_status );
		} elseif ( 'categories_per_page' == $option ) {
			$per_page = apply_filters( 'edit_categories_per_page', $per_page );
		} else {
			$per_page = apply_filters( $option, $per_page );
		}

		// Back compat
		if ( isset( $this->post_type ) )
			$per_page = apply_filters( 'edit_posts_per_page', $per_page, $this->post_type );

		?>
		<h5><?php _ex('Show on screen', 'Screen Options') ?></h5>
		<div class='screen-options'>
			<?php if ( !empty($per_page_label) ): ?>
				<input type='text' class='screen-per-page' name='wp_screen_options[value]'
					id='<?php echo esc_attr( $option ); ?>' maxlength='3'
					value='<?php echo esc_attr( $per_page ); ?>' />
				<label for='<?php echo esc_attr( $option ); ?>'>
					<?php echo esc_html( $per_page_label ); ?>
				</label>
			<?php endif;

			echo get_submit_button( __( 'Apply' ), 'button', 'screen-options-apply', false ); ?>
			<input type='hidden' name='wp_screen_options[option]' value='<?php echo esc_attr($option); ?>' />
		</div>
		<?php
	}
}
