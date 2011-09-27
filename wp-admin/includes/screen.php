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

	global $_wp_column_headers;

	if ( !isset( $_wp_column_headers[ $screen->id ] ) ) {
		$_wp_column_headers[ $screen->id ] = apply_filters( 'manage_' . $screen->id . '_columns', array() );
	}

	return $_wp_column_headers[ $screen->id ];
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
function meta_box_prefs($screen) {
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
	$screen = (object) array('id' => $screen, 'base' => $screen);
	return $screen;
}

function screen_meta($screen) {
	global $wp_meta_boxes, $_wp_contextual_help, $wp_list_table, $wp_current_screen_options;

	if ( is_string($screen) )
		$screen = convert_to_screen($screen);

	$columns = get_column_headers( $screen );
	$hidden = get_hidden_columns( $screen );

	$meta_screens = array('index' => 'dashboard');

	if ( isset($meta_screens[$screen->id]) ) {
		$screen->id = $meta_screens[$screen->id];
		$screen->base = $screen->id;
	}

	$show_screen = false;
	if ( !empty($wp_meta_boxes[$screen->id]) || !empty($columns) )
		$show_screen = true;

	$screen_options = screen_options($screen);
	if ( $screen_options )
		$show_screen = true;

	if ( !isset($_wp_contextual_help) )
		$_wp_contextual_help = array();

	$settings = apply_filters('screen_settings', '', $screen);

	switch ( $screen->id ) {
		case 'widgets':
			$settings = '<p><a id="access-on" href="widgets.php?widgets-access=on">' . __('Enable accessibility mode') . '</a><a id="access-off" href="widgets.php?widgets-access=off">' . __('Disable accessibility mode') . "</a></p>\n";
			$show_screen = true;
			break;
	}
	if ( ! empty( $settings ) )
		$show_screen = true;

	if ( !empty($wp_current_screen_options) )
		$show_screen = true;

	$show_screen = apply_filters('screen_options_show_screen', $show_screen, $screen);

	// If we have screen options, add the menu to the admin bar.
	if ( $show_screen )
		add_action( 'admin_bar_menu', 'wp_admin_bar_screen_options_menu', 80 );


?>
<div id="screen-meta">
<?php if ( $show_screen ) : ?>
<div id="screen-options-wrap" class="hidden">
	<form id="adv-settings" action="" method="post">
	<?php if ( isset($wp_meta_boxes[$screen->id]) ) : ?>
		<h5><?php _ex('Show on screen', 'Metaboxes') ?></h5>
		<div class="metabox-prefs">
			<?php meta_box_prefs($screen); ?>
			<br class="clear" />
		</div>
		<?php endif;
		if ( ! empty($columns) ) : ?>
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
	echo screen_layout($screen);

	if ( !empty( $screen_options ) ) {
		?>
		<h5><?php _ex('Show on screen', 'Screen Options') ?></h5>
		<?php
	}

	echo $screen_options;
	echo $settings; ?>
	<div><?php wp_nonce_field( 'screen-options-nonce', 'screenoptionnonce', false ); ?></div>
	</form>
</div>

<?php endif; // $show_screen

	$_wp_contextual_help = apply_filters('contextual_help_list', $_wp_contextual_help, $screen);
	?>
	<div id="contextual-help-wrap" class="hidden">
	<?php
	$contextual_help = '';
	if ( isset($_wp_contextual_help[$screen->id]) && is_array($_wp_contextual_help[$screen->id]) ) {
		$contextual_help .= '<div class="metabox-prefs">' . "\n";

		/*
		 * Loop through ['contextual-help-tabs']
		 *   - It's a nested array where $key=>$value >> $title=>$content
		 * Has no output so can only loop the array once
		 */
		$contextual_help_tabs = ''; // store looped content for later
		$contextual_help_panels = ''; // store looped content for later

		$tab_active = true;

		foreach ( $_wp_contextual_help[$screen->id]['tabs'] as $tab ) {
			$tab_slug = sanitize_html_class( $tab[ 0 ] );
			$contextual_help_tabs .= '<li class="tab-' . $tab_slug . ( ($tab_active) ? ' active' : '' ) . '">';
			$contextual_help_tabs .= '<a href="#' . $tab_slug . '">' . $tab[1] . '</a>';
			$contextual_help_tabs .= '</li>' ."\n";
			
			$contextual_help_panels .= '<div id="' . $tab_slug . '" class="help-tab-content' . ( ($tab_active) ? ' active' : '' ) . '">';
			$contextual_help_panels .= $tab[2];
			$contextual_help_panels .= "</div>\n";

			$tab_active = false;
		}

		// Start output from loop: Tabbed help content
		$contextual_help .= '<ul class="contextual-help-tabs">' . "\n";
		$contextual_help .= $contextual_help_tabs;
		$contextual_help .= '</ul>' ."\n";
		$contextual_help .= '<div class="contextual-help-tabs-wrap">' . "\n";
		$contextual_help .= $contextual_help_panels;
		$contextual_help .= "</div>\n";
		// END: Tabbed help content

		// Sidebar to right of tabs
		$contextual_help .= '<div class="contextual-help-links">' . "\n";
		$contextual_help .= $_wp_contextual_help[$screen->id]['sidebar'];
		$contextual_help .= "</div>\n";
		
		$contextual_help .= "</div>\n"; // end metabox
		
	} elseif ( isset($_wp_contextual_help[$screen->id]) ) {
		$contextual_help .= '<div class="metabox-prefs">' . $_wp_contextual_help[$screen->id] . "</div>\n";
	} else {
		$contextual_help .= '<div class="metabox-prefs">';
		$default_help  = __('<a href="http://codex.wordpress.org/" target="_blank">Documentation</a>');
		$default_help .= '<br />';
		$default_help .= __('<a href="http://wordpress.org/support/" target="_blank">Support Forums</a>');
		$contextual_help .= apply_filters('default_contextual_help', $default_help);
		$contextual_help .= '</div>' . "\n";
	}

	echo apply_filters('contextual_help', $contextual_help, $screen->id, $screen);
	?>
	</div>

</div> <?php // #screen-meta
}

/**
 * Add contextual help text for a page
 *
 * The array $help takes the following format:
 * 	array( 'contextual-help-tabs' 	=> array( $tab1_title => $tab1_value [, $tab2_title => $tab2_value, ...] ),
 *		'contextual-help-links' => $help_links_as_string )
 *
 * For backwards compatability, a string is also accepted.
 *
 * @since 2.7.0
 *
 * @param string 	$screen The handle for the screen to add help to.  This is usually the hook name returned by the add_*_page() functions.
 * @param array|string 	$help 	Creates tabs & links columns within help text in array.
 *
 */
function add_contextual_help($screen, $help) {
	global $_wp_contextual_help;

	if ( is_string($screen) )
		$screen = convert_to_screen($screen);

	if ( !isset($_wp_contextual_help) )
		$_wp_contextual_help = array();

	$_wp_contextual_help[$screen->id] = $help;
}

function screen_layout($screen) {
	global $screen_layout_columns, $wp_current_screen_options;

	if ( is_string($screen) )
		$screen = convert_to_screen($screen);

	// Back compat for plugins using the filter instead of add_screen_option()
	$columns = apply_filters('screen_layout_columns', array(), $screen->id, $screen);
	if ( !empty($columns) && isset($columns[$screen->id]) )
		add_screen_option('layout_columns', array('max' => $columns[$screen->id]) );

	if ( !isset($wp_current_screen_options['layout_columns']) ) {
		$screen_layout_columns = 0;
		return '';
	}

	$screen_layout_columns = get_user_option("screen_layout_$screen->id");
	$num = $wp_current_screen_options['layout_columns']['max'];

	if ( ! $screen_layout_columns ) {
		if ( isset($wp_current_screen_options['layout_columns']['default']) )
			$screen_layout_columns = $wp_current_screen_options['layout_columns']['default'];
		else
			$screen_layout_columns = 'auto';
	}

	$i = 1;
	$return = '<h5>' . __('Screen Layout') . "</h5>\n<div class='columns-prefs'>" . __('Number of Columns:') . "\n";
	while ( $i <= $num ) {
		$return .= "<label><input type='radio' name='screen_columns' value='$i'" . ( ($screen_layout_columns == $i) ? " checked='checked'" : "" ) . " /> $i</label>\n";
		++$i;
	}
	$return .= "<label><input type='radio' id='wp_auto_columns' name='screen_columns' value='auto'" . ( ($screen_layout_columns == 'auto') ? " checked='checked'" : "" ) . " />" . __('auto') . "</label>\n";
	$return .= "</div>\n";
	return $return;
}

/**
 * Register and configure an admin screen option
 *
 * @since 3.1.0
 *
 * @param string $option An option name.
 * @param mixed $args Option dependent arguments
 * @return void
 */
function add_screen_option( $option, $args = array() ) {
	global $wp_current_screen_options;

	if ( !isset($wp_current_screen_options) )
		$wp_current_screen_options = array();

	$wp_current_screen_options[$option] = $args;
}

function screen_options($screen) {
	global $wp_current_screen_options;

	if ( is_string($screen) )
		$screen = convert_to_screen($screen);

	if ( !isset($wp_current_screen_options['per_page']) )
		return '';

	$per_page_label = $wp_current_screen_options['per_page']['label'];

	if ( empty($wp_current_screen_options['per_page']['option']) ) {
		$option = str_replace( '-', '_', "{$screen->id}_per_page" );
	} else {
		$option = $wp_current_screen_options['per_page']['option'];
	}

	$per_page = (int) get_user_option( $option );
	if ( empty( $per_page ) || $per_page < 1 ) {
		if ( isset($wp_current_screen_options['per_page']['default']) )
			$per_page = $wp_current_screen_options['per_page']['default'];
		else
			$per_page = 20;
	}

	if ( 'edit_comments_per_page' == $option )
		$per_page = apply_filters( 'comments_per_page', $per_page, isset($_REQUEST['comment_status']) ? $_REQUEST['comment_status'] : 'all' );
	elseif ( 'categories_per_page' == $option )
		$per_page = apply_filters( 'edit_categories_per_page', $per_page );
	else
		$per_page = apply_filters( $option, $per_page );

	// Back compat
	if ( isset( $screen->post_type ) )
		$per_page = apply_filters( 'edit_posts_per_page', $per_page, $screen->post_type );

	$return = "<div class='screen-options'>\n";
	if ( !empty($per_page_label) )
		$return .= "<input type='text' class='screen-per-page' name='wp_screen_options[value]' id='$option' maxlength='3' value='$per_page' /> <label for='$option'>$per_page_label</label>\n";
	$return .= get_submit_button( __( 'Apply' ), 'button', 'screen-options-apply', false );
	$return .= "<input type='hidden' name='wp_screen_options[option]' value='" . esc_attr($option) . "' />";
	$return .= "</div>\n";
	return $return;
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
	 * @access private
	 */
	public $action = '';

	/**
	 * The base type of the screen.  This is typically the same as $id but with any post types and taxonomies stripped.
	 * For example, for an $id of 'edit-post' the base is 'edit'.
	 *
	 * @since 3.3.0
	 * @var string
	 * @access private
	 */
	public $base;

	/**
	 * The unique ID of the screen.
	 *
	 * @since 3.3.0
	 * @var string
	 * @access private
	 */
	public $id;

	/**
	 * Whether the screen is in the network admin.
	 *
	 * @since 3.3.0
	 * @var bool
	 * @access private
	 */
	public $is_network;

	/**
	 * Whether the screen is in the user admin.
	 *
	 * @since 3.3.0
	 * @var bool
	 * @access private
	 */
	public $is_user;

	/**
	 * The base menu parent.
	 * This is derived from $parent_file by removing the query string and any .php extension.
	 * $parent_file values of 'edit.php?post_type=page' and 'edit.php?post_type=post' have a $parent_base of 'edit'.
	 *
	 * @since 3.3.0
	 * @var string
	 * @access private
	 */
	public $parent_base;

	/**
	 * The parent_file for the screen per the admin menu system.
	 * Some $parent_file values are 'edit.php?post_type=page', 'edit.php', and 'options-general.php'.
	 *
	 * @since 3.3.0
	 * @var string
	 * @access private
	 */
	public $parent_file;

	/**
	 * The post type associated with the screen, if any.
	 * The 'edit.php?post_type=page' screen has a post type of 'page'.
	 *
	 * @since 3.3.0
	 * @var string
	 * @access private
	 */
	public $post_type;

	/**
	 * The taxonomy associated with the screen, if any.
	 * The 'edit-tags.php?taxonomy=category' screen has a taxonomy of 'category'.
	 * @since 3.3.0
	 * @var string
	 * @access private
	 */
	public $taxonomy;

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
	 * @param array $args Associative array of arguments particular to the given $option.
	 */
	public function add_option( $option, $args = array() ) {
		return add_screen_option( $option, $args );
	}

	/**
	 * Add a help tab to the contextual help for the screen.
	 * Call this in template files after admin.php is loaded and before admin-header.php is loaded to add contextual help tabs.
	 * 
	 * @since 3.3.0
	 *
	 * @param string $id Tab ID
	 * @param string $title Title for the tab
	 * @param string $content Help tab content in plain text or HTML.
	 */
	public function add_help_tab( $id, $title, $content) {
		global $_wp_contextual_help;

		$_wp_contextual_help[$this->id]['tabs'][] = array( $id, $title, $content );
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
		global $_wp_contextual_help;

		$_wp_contextual_help[$this->id]['sidebar'] = $content;
	}
}