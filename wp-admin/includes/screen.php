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
 * @param string|object $screen The screen you want the headers for
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
 * @param string|object $screen The screen you want the hidden columns for
 * @return array
 */
function get_hidden_columns( $screen ) {
	if ( is_string( $screen ) )
		$screen = convert_to_screen( $screen );

	return (array) get_user_option( 'manage' . $screen->id . 'columnshidden' );
}

/**
 * {@internal Missing Short Description}}
 *
 * @since 2.7.0
 *
 * @param unknown_type $screen
 */
function meta_box_prefs( $screen ) {
	global $wp_meta_boxes;

	if ( is_string($screen) )
		$screen = convert_to_screen($screen);

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
 * @param string|object $screen Screen identifier
 * @return array Hidden Meta Boxes
 */
function get_hidden_meta_boxes( $screen ) {
	if ( is_string( $screen ) )
		$screen = convert_to_screen( $screen );

	$hidden = get_user_option( "metaboxhidden_{$screen->id}" );

	// Hide slug boxes by default
	if ( !is_array( $hidden ) ) {
		if ( 'post' == $screen->base || 'page' == $screen->base )
			$hidden = array('slugdiv', 'trackbacksdiv', 'postcustom', 'postexcerpt', 'commentstatusdiv', 'commentsdiv', 'authordiv', 'revisionsdiv');
		else
			$hidden = array( 'slugdiv' );
		$hidden = apply_filters('default_hidden_meta_boxes', $hidden, $screen);
	}

	return $hidden;
}

/**
 * {@internal Missing Short Description}}
 *
 * @since 2.7.0
 */
function favorite_actions( $screen = null ) {
	$default_action = false;

	if ( is_string($screen) )
		$screen = convert_to_screen($screen);

	if ( $screen->is_user )
		return;

	if ( isset($screen->post_type) ) {
		$post_type_object = get_post_type_object($screen->post_type);
		if ( 'add' != $screen->action )
			$default_action = array('post-new.php?post_type=' . $post_type_object->name => array($post_type_object->labels->new_item, $post_type_object->cap->edit_posts));
		else
			$default_action = array('edit.php?post_type=' . $post_type_object->name => array($post_type_object->labels->name, $post_type_object->cap->edit_posts));
	}

	if ( !$default_action ) {
		if ( $screen->is_network ) {
			$default_action = array('sites.php' => array( __('Sites'), 'manage_sites'));
		} else {
			switch ( $screen->id ) {
				case 'upload':
					$default_action = array('media-new.php' => array(__('New Media'), 'upload_files'));
					break;
				case 'media':
					$default_action = array('upload.php' => array(__('Edit Media'), 'upload_files'));
					break;
				case 'link-manager':
				case 'link':
					if ( 'add' != $screen->action )
						$default_action = array('link-add.php' => array(__('New Link'), 'manage_links'));
					else
						$default_action = array('link-manager.php' => array(__('Edit Links'), 'manage_links'));
					break;
				case 'users':
					$default_action = array('user-new.php' => array(__('New User'), 'create_users'));
					break;
				case 'user':
					$default_action = array('users.php' => array(__('Edit Users'), 'edit_users'));
					break;
				case 'plugins':
					$default_action = array('plugin-install.php' => array(__('Install Plugins'), 'install_plugins'));
					break;
				case 'plugin-install':
					$default_action = array('plugins.php' => array(__('Manage Plugins'), 'activate_plugins'));
					break;
				case 'themes':
					$default_action = array('theme-install.php' => array(__('Install Themes'), 'install_themes'));
					break;
				case 'theme-install':
					$default_action = array('themes.php' => array(__('Manage Themes'), 'switch_themes'));
					break;
				default:
					$default_action = array('post-new.php' => array(__('New Post'), 'edit_posts'));
					break;
			}
		}
	}

	if ( !$screen->is_network ) {
		$actions = array(
			'post-new.php' => array(__('New Post'), 'edit_posts'),
			'edit.php?post_status=draft' => array(__('Drafts'), 'edit_posts'),
			'post-new.php?post_type=page' => array(__('New Page'), 'edit_pages'),
			'media-new.php' => array(__('Upload'), 'upload_files'),
			'edit-comments.php' => array(__('Comments'), 'moderate_comments')
			);
	} else {
		$actions = array(
			'sites.php' => array( __('Sites'), 'manage_sites'),
			'users.php' => array( __('Users'), 'manage_network_users')
		);
	}

	$default_key = array_keys($default_action);
	$default_key = $default_key[0];
	if ( isset($actions[$default_key]) )
		unset($actions[$default_key]);
	$actions = array_merge($default_action, $actions);
	$actions = apply_filters( 'favorite_actions', $actions, $screen );

	$allowed_actions = array();
	foreach ( $actions as $action => $data ) {
		if ( current_user_can($data[1]) )
			$allowed_actions[$action] = $data[0];
	}

	if ( empty($allowed_actions) )
		return;

	$first = array_keys($allowed_actions);
	$first = $first[0];
	echo '<div id="favorite-actions">';
	echo '<div id="favorite-first"><a href="' . $first . '">' . $allowed_actions[$first] . '</a></div><div id="favorite-toggle"><br /></div>';
	echo '<div id="favorite-inside">';

	array_shift($allowed_actions);

	foreach ( $allowed_actions as $action => $label) {
		echo "<div class='favorite-action'><a href='$action'>";
		echo $label;
		echo "</a></div>\n";
	}
	echo "</div></div>\n";
}

/**
 * Convert a screen string to a screen object
 *
 * @since 3.0.0
 *
 * @param string $screen The name of the screen
 * @return object An object containing the safe screen name and id
 */
function convert_to_screen( $screen ) {
	$screen = str_replace( array('.php', '-new', '-add', '-network', '-user' ), '', $screen);

	if ( is_network_admin() )
		$screen .= '-network';
	elseif ( is_user_admin() )
		$screen .= '-user';

	$screen = (string) apply_filters( 'screen_meta_screen', $screen );
	$screen = new WP_Screen( $screen );
	return $screen;
}

/**
 * Add contextual help text for a page.
 *
 * Creates a 'Screen Info' help tab.
 *
 * @since 2.7.0
 *
 * @param string    $screen The handle for the screen to add help to.  This is usually the hook name returned by the add_*_page() functions.
 * @param string    $help   The content of a 'Screen Info' help tab.
 *
 * @todo: deprecate?
 */
function add_contextual_help( $screen, $help ) {
	if ( is_string( $screen ) )
		$screen = convert_to_screen( $screen );

	$screen->add_old_compat_help( $help );
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

	return $current_screen->add_option( $option, $args );
}

function screen_icon( $screen = '' ) {
	echo get_screen_icon( $screen );
}

function get_screen_icon( $screen = '' ) {
	global $current_screen, $typenow;

	if ( empty($screen) )
		$screen = $current_screen;
	elseif ( is_string($screen) )
		$name = $screen;

	$class = 'icon32';

	if ( empty($name) ) {
		if ( !empty($screen->parent_base) )
			$name = $screen->parent_base;
		else
			$name = $screen->base;

		if ( 'edit' == $name && isset($screen->post_type) && 'page' == $screen->post_type )
			$name = 'edit-pages';

		$post_type = '';
		if ( isset( $screen->post_type ) )
			$post_type = $screen->post_type;
		elseif ( $current_screen == $screen )
			$post_type = $typenow;
		if ( $post_type )
			$class .= ' ' . sanitize_html_class( 'icon32-posts-' . $post_type );
	}

	return '<div id="icon-' . esc_attr( $name ) . '" class="' . $class . '"><br /></div>';
}

/**
 *  Get the current screen object
 *
 *  @since 3.1.0
 *
 * @return object Current screen object
 */
function get_current_screen() {
	global $current_screen;

	if ( !isset($current_screen) )
		return null;

	return $current_screen;
}

/**
 * Set the current screen object
 *
 * @since 3.0.0
 *
 * @uses $current_screen
 *
 * @param string $id Screen id, optional.
 */
function set_current_screen( $id =  '' ) {
	global $current_screen;

	$current_screen = new WP_Screen( $id );

	$current_screen = apply_filters('current_screen', $current_screen);
}

/**
 * A class representing the current admin screen.
 *
 * @since 3.3.0
 * @access public
 */
final class WP_Screen {
	/**
	 * Any action associated with the screen.  'add' for *-add.php and *-new.php screens.  Empty otherwise.
	 *
	 * @since 3.3.0
	 * @var string
	 * @access public
	 */
	public $action = '';

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
	private static $_help_tabs = array();
 
 	/**
	 * The help sidebar data associated with screens, if any.
	 *
	 * @since 3.3.0
	 * @var string
	 * @access private
 	 */
	private static $_help_sidebar = array();

	/**
	 * Stores old string-based help.
	 */
	private static $_old_compat_help = array();

	/**
	 * The screen options associated with screens, if any.
	 *
	 * @since 3.3.0
	 * @var array
	 * @access private
	 */
	private static $_options = array();

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
	 * Constructor
	 *
	 * @since 3.3.0
	 *
	 * @param string $id A screen id.  If empty, the $hook_suffix global is used to derive the ID.
	 */
	public function __construct( $id = '' ) {
		global $hook_suffix, $typenow, $taxnow;

		$action = '';

		if ( empty( $id ) ) {
			$screen = $hook_suffix;
			$screen = str_replace('.php', '', $screen);
			if ( preg_match('/-add|-new$/', $screen) )
				$action = 'add';
			$screen = str_replace('-new', '', $screen);
			$screen = str_replace('-add', '', $screen);
			$this->id = $this->base = $screen;
		} else {
			$id = sanitize_key( $id );
			if ( false !== strpos($id, '-') ) {
				list( $id, $typenow ) = explode('-', $id, 2);
				if ( taxonomy_exists( $typenow ) ) {
					$id = 'edit-tags';
					$taxnow = $typenow;
					$typenow = '';
				}
			}
			$this->id = $this->base = $id;
		}

		$this->action = $action;

		// Map index to dashboard
		if ( 'index' == $this->base )
			$this->base = 'dashboard';
		if ( 'index' == $this->id )
			$this->id = 'dashboard';

		if ( 'edit' == $this->id ) {
			if ( empty($typenow) )
				$typenow = 'post';
			$this->id .= '-' . $typenow;
			$this->post_type = $typenow;
		} elseif ( 'post' == $this->id ) {
			if ( empty($typenow) )
				$typenow = 'post';
			$this->id = $typenow;
			$this->post_type = $typenow;
		} elseif ( 'edit-tags' == $this->id ) {
			if ( empty($taxnow) )
				$taxnow = 'post_tag';
			$this->id = 'edit-' . $taxnow;
			$this->taxonomy = $taxnow;
		}

		$this->is_network = is_network_admin();
		$this->is_user = is_user_admin();

		if ( $this->is_network ) {
			$this->base .= '-network';
			$this->id .= '-network';
		} elseif ( $this->is_user ) {
			$this->base .= '-user';
			$this->id .= '-user';
		}

		if ( ! isset( self::$_help_tabs[ $this->id ] ) )
			self::$_help_tabs[ $this->id ] = array();
		if ( ! isset( self::$_help_sidebar[ $this->id ] ) )
			self::$_help_sidebar[ $this->id ] = '';
		if ( ! isset( self::$_options[ $this->id ] ) )
			self::$_options[ $this->id ] = array();
	}

	function add_old_compat_help( $help ) {
		self::$_old_compat_help[ $this->id ] = $help;	
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
		$this->parent_base = preg_replace('/\?.*$/', '', $parent_file);
		$this->parent_base = str_replace('.php', '', $this->parent_base);
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
		self::$_options[ $this->id ][ $option ] = $args;
	}

	/**
	 * Gets the arguments for an option for the screen.
	 *
	 * @since 3.3.0
	 *
	 * @param string 
	 */
	public function get_option( $option, $key = false ) {
		if ( ! isset( self::$_options[ $this->id ][ $option ] ) )
			return null;
		if ( $key ) {
			if ( isset( self::$_options[ $this->id ][ $option ][ $key ] ) )
				return self::$_options[ $this->id ][ $option ][ $key ];
			return null;
		}
		return self::$_options[ $this->id ][ $option ];
	}

	/**
	 * Add a help tab to the contextual help for the screen.
	 * Call this in template files after admin.php is loaded and before admin-header.php is loaded to add contextual help tabs.
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

		self::$_help_tabs[ $this->id ][] = $args;
	}

	/**
	 * Add a sidebar to the contextual help for the screen.
	 * Call this in template files after admin.php is loaded and before admin-header.php is loaded to add a sidebar to the contextual help.
	 *
	 * @since 3.3.0
	 *
	 * @param string $content Sidebar content in plain text or HTML.
	 */
	public function add_help_sidebar( $content ) {
		self::$_help_sidebar[ $this->id ] = $content;
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

		if ( isset( self::$_old_compat_help[ $this->id ] ) || empty(self::$_help_tabs[ $this->id ] ) ) {
			// Call old contextual_help filter.
			if ( isset( self::$_old_compat_help[ $this->id ] ) )
				$contextual_help = apply_filters( 'contextual_help', self::$_old_compat_help[ $this->id ], $this->id, $this );

			if ( empty( $contextual_help ) ) {
				$default_help = __( '<a href="http://codex.wordpress.org/" target="_blank">Documentation</a>' );
				$default_help .= '<br />';
				$default_help .= __( '<a href="http://wordpress.org/support/" target="_blank">Support Forums</a>' );
				$contextual_help = '<p>' . apply_filters( 'default_contextual_help', $default_help ) . '</p>';
			}

			$this->add_help_tab( array(
				'id'      => 'contextual-help',
				'title'   => __('Screen Info'),
				'content' => $contextual_help,
			) );
		}

		// Add screen options tab
		if ( $this->show_screen_options() ) {
			$this->add_help_tab( array(
				'id'       => 'screen-options',
				'title'    => __('Screen Options'),
				'callback' => array( $this, 'render_screen_options' ),
			) );
			$_options_tab = array_pop( self::$_help_tabs[ $this->id ] );
			array_unshift( self::$_help_tabs[ $this->id ], $_options_tab );
		}

		// Time to render!
		?>
		<div id="screen-meta" class='metabox-prefs'>
			<div id="contextual-help-back"></div>
			<div id="contextual-help-wrap" class="hidden">
				<div class="contextual-help-tabs">
					<ul>
					<?php foreach ( self::$_help_tabs[ $this->id ] as $i => $tab ):
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

				<?php if ( ! empty( self::$_help_sidebar[ $this->id ] ) ) : ?>
				<div class="contextual-help-sidebar">
					<?php echo self::$_help_sidebar[ $this->id ]; ?>
				</div>
				<?php endif; ?>

				<div class="contextual-help-tabs-wrap">
					<?php foreach ( self::$_help_tabs[ $this->id ] as $i => $tab ):
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
								call_user_func( $tab['callback'], $this );
							?>
						</div>
					<?php endforeach; ?>
				</div>
			</div>
		</div>
		<?php
	}

	public function show_screen_options() {
		global $wp_meta_boxes, $wp_list_table;

		if ( is_bool( $this->_show_screen_options ) )
			return $this->_show_screen_options;

		$columns = get_column_headers( $this );

		$show_screen = false;
		if ( ! empty( $wp_meta_boxes[ $this->id ] ) || ! empty( $columns ) )
			$show_screen = true;

		// Check if there are per-page options.
		$show_screen = $show_screen || $this->get_option('per_page');

		$this->_screen_settings = apply_filters( 'screen_settings', '', $this );

		switch ( $this->id ) {
			case 'widgets':
				$this->_screen_settings = '<p><a id="access-on" href="widgets.php?widgets-access=on">' . __('Enable accessibility mode') . '</a><a id="access-off" href="widgets.php?widgets-access=off">' . __('Disable accessibility mode') . "</a></p>\n";
				$show_screen = true;
				break;
		}

		if ( ! empty( $this->_screen_settings ) )
			$show_screen = true;

		if ( ! empty( self::$_options[ $this->id ] ) )
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
		<form id="adv-settings" action="" method="post">
		<?php
		if ( $this->get_option('overview') )
			echo $this->get_option('overview');
		if ( isset( $wp_meta_boxes[ $this->id ] ) ) : ?>
			<h5><?php _ex('Show on screen', 'Metaboxes') ?></h5>
			<div class="metabox-prefs">
				<?php meta_box_prefs( $this ); ?>
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