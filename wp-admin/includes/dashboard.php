<?php
/**
 * WordPress Dashboard Widget Administration Screen API
 *
 * @package WordPress
 * @subpackage Administration
 */

/**
 * Registers dashboard widgets.
 *
 * Handles POST data, sets up filters.
 *
 * @since 2.5.0
 */
function wp_dashboard_setup() {
	global $wp_registered_widgets, $wp_registered_widget_controls, $wp_dashboard_control_callbacks;
	$wp_dashboard_control_callbacks = array();
	$screen = get_current_screen();

	$update = false;
	$widget_options = get_option( 'dashboard_widget_options' );
	if ( !$widget_options || !is_array($widget_options) )
		$widget_options = array();

	/* Register Widgets and Controls */

	$response = wp_check_browser_version();

	if ( $response && $response['upgrade'] ) {
		add_filter( 'postbox_classes_dashboard_dashboard_browser_nag', 'dashboard_browser_nag_class' );
		if ( $response['insecure'] )
			wp_add_dashboard_widget( 'dashboard_browser_nag', __( 'You are using an insecure browser!' ), 'wp_dashboard_browser_nag' );
		else
			wp_add_dashboard_widget( 'dashboard_browser_nag', __( 'Your browser is out of date!' ), 'wp_dashboard_browser_nag' );
	}

	// Right Now
	if ( is_blog_admin() && current_user_can('edit_posts') )
		wp_add_dashboard_widget( 'dashboard_right_now', __( 'Right Now' ), 'wp_dashboard_right_now' );

	if ( is_network_admin() )
		wp_add_dashboard_widget( 'network_dashboard_right_now', __( 'Right Now' ), 'wp_network_dashboard_right_now' );

	// Recent Comments Widget
	if ( is_blog_admin() && current_user_can('moderate_comments') ) {
		if ( !isset( $widget_options['dashboard_recent_comments'] ) || !isset( $widget_options['dashboard_recent_comments']['items'] ) ) {
			$update = true;
			$widget_options['dashboard_recent_comments'] = array(
				'items' => 5,
			);
		}
		$recent_comments_title = __( 'Recent Comments' );
		wp_add_dashboard_widget( 'dashboard_recent_comments', $recent_comments_title, 'wp_dashboard_recent_comments', 'wp_dashboard_recent_comments_control' );
	}

	// Incoming Links Widget
	if ( is_blog_admin() && current_user_can('publish_posts') ) {
		if ( !isset( $widget_options['dashboard_incoming_links'] ) || !isset( $widget_options['dashboard_incoming_links']['home'] ) || $widget_options['dashboard_incoming_links']['home'] != get_option('home') ) {
			$update = true;
			$num_items = isset($widget_options['dashboard_incoming_links']['items']) ? $widget_options['dashboard_incoming_links']['items'] : 10;
			$widget_options['dashboard_incoming_links'] = array(
				'home' => get_option('home'),
				'link' => apply_filters( 'dashboard_incoming_links_link', 'http://blogsearch.google.com/blogsearch?scoring=d&partner=wordpress&q=link:' . trailingslashit( get_option('home') ) ),
				'url' => isset($widget_options['dashboard_incoming_links']['url']) ? apply_filters( 'dashboard_incoming_links_feed', $widget_options['dashboard_incoming_links']['url'] ) : apply_filters( 'dashboard_incoming_links_feed', 'http://blogsearch.google.com/blogsearch_feeds?scoring=d&ie=utf-8&num=' . $num_items . '&output=rss&partner=wordpress&q=link:' . trailingslashit( get_option('home') ) ),
				'items' => $num_items,
				'show_date' => isset($widget_options['dashboard_incoming_links']['show_date']) ? $widget_options['dashboard_incoming_links']['show_date'] : false
			);
		}
		wp_add_dashboard_widget( 'dashboard_incoming_links', __( 'Incoming Links' ), 'wp_dashboard_incoming_links', 'wp_dashboard_incoming_links_control' );
	}

	// WP Plugins Widget
	if ( ( ! is_multisite() && is_blog_admin() && current_user_can( 'install_plugins' ) ) || ( is_network_admin() && current_user_can( 'manage_network_plugins' ) && current_user_can( 'install_plugins' ) ) )
		wp_add_dashboard_widget( 'dashboard_plugins', __( 'Plugins' ), 'wp_dashboard_plugins' );

	// QuickPress Widget
	if ( is_blog_admin() && current_user_can('edit_posts') )
		wp_add_dashboard_widget( 'dashboard_quick_press', __( 'QuickPress' ), 'wp_dashboard_quick_press' );

	// Recent Drafts
	if ( is_blog_admin() && current_user_can('edit_posts') )
		wp_add_dashboard_widget( 'dashboard_recent_drafts', __('Recent Drafts'), 'wp_dashboard_recent_drafts' );

	// Primary feed (Dev Blog) Widget
	if ( !isset( $widget_options['dashboard_primary'] ) ) {
		$update = true;
		$widget_options['dashboard_primary'] = array(
			'link' => apply_filters( 'dashboard_primary_link', __( 'http://wordpress.org/news/' ) ),
			'url' => apply_filters( 'dashboard_primary_feed', __( 'http://wordpress.org/news/feed/' ) ),
			'title' => apply_filters( 'dashboard_primary_title', __( 'WordPress Blog' ) ),
			'items' => 2,
			'show_summary' => 1,
			'show_author' => 0,
			'show_date' => 1,
		);
	}
	wp_add_dashboard_widget( 'dashboard_primary', $widget_options['dashboard_primary']['title'], 'wp_dashboard_primary', 'wp_dashboard_primary_control' );

	// Secondary Feed (Planet) Widget
	if ( !isset( $widget_options['dashboard_secondary'] ) ) {
		$update = true;
		$widget_options['dashboard_secondary'] = array(
			'link' => apply_filters( 'dashboard_secondary_link', __( 'http://planet.wordpress.org/' ) ),
			'url' => apply_filters( 'dashboard_secondary_feed', __( 'http://planet.wordpress.org/feed/' ) ),
			'title' => apply_filters( 'dashboard_secondary_title', __( 'Other WordPress News' ) ),
			'items' => 5,
			'show_summary' => 0,
			'show_author' => 0,
			'show_date' => 0,
		);
	}
	wp_add_dashboard_widget( 'dashboard_secondary', $widget_options['dashboard_secondary']['title'], 'wp_dashboard_secondary', 'wp_dashboard_secondary_control' );

	// Hook to register new widgets
	// Filter widget order
	if ( is_network_admin() ) {
		do_action( 'wp_network_dashboard_setup' );
		$dashboard_widgets = apply_filters( 'wp_network_dashboard_widgets', array() );
	} elseif ( is_user_admin() ) {
		do_action( 'wp_user_dashboard_setup' );
		$dashboard_widgets = apply_filters( 'wp_user_dashboard_widgets', array() );
	} else {
		do_action( 'wp_dashboard_setup' );
		$dashboard_widgets = apply_filters( 'wp_dashboard_widgets', array() );
	}

	foreach ( $dashboard_widgets as $widget_id ) {
		$name = empty( $wp_registered_widgets[$widget_id]['all_link'] ) ? $wp_registered_widgets[$widget_id]['name'] : $wp_registered_widgets[$widget_id]['name'] . " <a href='{$wp_registered_widgets[$widget_id]['all_link']}' class='edit-box open-box'>" . __('View all') . '</a>';
		wp_add_dashboard_widget( $widget_id, $name, $wp_registered_widgets[$widget_id]['callback'], $wp_registered_widget_controls[$widget_id]['callback'] );
	}

	if ( 'POST' == $_SERVER['REQUEST_METHOD'] && isset($_POST['widget_id']) ) {
		check_admin_referer( 'edit-dashboard-widget_' . $_POST['widget_id'], 'dashboard-widget-nonce' );
		ob_start(); // hack - but the same hack wp-admin/widgets.php uses
		wp_dashboard_trigger_widget_control( $_POST['widget_id'] );
		ob_end_clean();
		wp_redirect( remove_query_arg( 'edit' ) );
		exit;
	}

	if ( $update )
		update_option( 'dashboard_widget_options', $widget_options );

	/** This action is documented in wp-admin/edit-form-advanced.php */
	do_action('do_meta_boxes', $screen->id, 'normal', '');
	/** This action is documented in wp-admin/edit-form-advanced.php */
	do_action('do_meta_boxes', $screen->id, 'side', '');
}

function wp_add_dashboard_widget( $widget_id, $widget_name, $callback, $control_callback = null, $callback_args = null ) {
	$screen = get_current_screen();
	global $wp_dashboard_control_callbacks;

	if ( $control_callback && current_user_can( 'edit_dashboard' ) && is_callable( $control_callback ) ) {
		$wp_dashboard_control_callbacks[$widget_id] = $control_callback;
		if ( isset( $_GET['edit'] ) && $widget_id == $_GET['edit'] ) {
			list($url) = explode( '#', add_query_arg( 'edit', false ), 2 );
			$widget_name .= ' <span class="postbox-title-action"><a href="' . esc_url( $url ) . '">' . __( 'Cancel' ) . '</a></span>';
			$callback = '_wp_dashboard_control_callback';
		} else {
			list($url) = explode( '#', add_query_arg( 'edit', $widget_id ), 2 );
			$widget_name .= ' <span class="postbox-title-action"><a href="' . esc_url( "$url#$widget_id" ) . '" class="edit-box open-box">' . __( 'Configure' ) . '</a></span>';
		}
	}

	if ( is_blog_admin () )
		$side_widgets = array('dashboard_quick_press', 'dashboard_recent_drafts', 'dashboard_primary', 'dashboard_secondary');
	else if (is_network_admin() )
		$side_widgets = array('dashboard_primary', 'dashboard_secondary');
	else
		$side_widgets = array();

	$location = 'normal';
	if ( in_array($widget_id, $side_widgets) )
		$location = 'side';

	$priority = 'core';
	if ( 'dashboard_browser_nag' === $widget_id )
		$priority = 'high';

	add_meta_box( $widget_id, $widget_name, $callback, $screen, $location, $priority, $callback_args );
}

function _wp_dashboard_control_callback( $dashboard, $meta_box ) {
	echo '<form action="" method="post" class="dashboard-widget-control-form">';
	wp_dashboard_trigger_widget_control( $meta_box['id'] );
	wp_nonce_field( 'edit-dashboard-widget_' . $meta_box['id'], 'dashboard-widget-nonce' );
	echo '<input type="hidden" name="widget_id" value="' . esc_attr($meta_box['id']) . '" />';
	submit_button( __('Submit') );
	echo '</form>';
}

/**
 * Displays the dashboard.
 *
 * @since 2.5.0
 */
function wp_dashboard() {
	$screen = get_current_screen();
	$class = 'columns-' . get_current_screen()->get_columns();

?>
<div id="dashboard-widgets" class="metabox-holder <?php echo $class; ?>">
	<div id='postbox-container-1' class='postbox-container'>
	<?php do_meta_boxes( $screen->id, 'normal', '' ); ?>
	</div>
	<div id='postbox-container-2' class='postbox-container'>
	<?php do_meta_boxes( $screen->id, 'side', '' ); ?>
	</div>
	<div id='postbox-container-3' class='postbox-container'>
	<?php do_meta_boxes( $screen->id, 'column3', '' ); ?>
	</div>
	<div id='postbox-container-4' class='postbox-container'>
	<?php do_meta_boxes( $screen->id, 'column4', '' ); ?>
	</div>
</div>

<?php
	wp_nonce_field( 'closedpostboxes', 'closedpostboxesnonce', false );
	wp_nonce_field( 'meta-box-order', 'meta-box-order-nonce', false );

}

/* Dashboard Widgets */

function wp_dashboard_right_now() {
	global $wp_registered_sidebars;

	$num_posts = wp_count_posts( 'post' );
	$num_pages = wp_count_posts( 'page' );

	$num_cats  = wp_count_terms('category');

	$num_tags = wp_count_terms('post_tag');

	$num_comm = wp_count_comments();

	echo "\n\t".'<div class="table table_content">';
	echo "\n\t".'<p class="sub">' . __('Content') . '</p>'."\n\t".'<table>';
	echo "\n\t".'<tr class="first">';

	// Posts
	$num = number_format_i18n( $num_posts->publish );
	$text = _n( 'Post', 'Posts', intval($num_posts->publish) );
	if ( current_user_can( 'edit_posts' ) ) {
		$num = "<a href='edit.php'>$num</a>";
		$text = "<a href='edit.php'>$text</a>";
	}
	echo '<td class="first b b-posts">' . $num . '</td>';
	echo '<td class="t posts">' . $text . '</td>';

	echo '</tr><tr>';
	/* TODO: Show status breakdown on hover
	if ( $can_edit_pages && !empty($num_pages->publish) ) { // how many pages is not exposed in feeds. Don't show if !current_user_can
		$post_type_texts[] = '<a href="edit-pages.php">'.sprintf( _n( '%s page', '%s pages', $num_pages->publish ), number_format_i18n( $num_pages->publish ) ).'</a>';
	}
	if ( $can_edit_posts && !empty($num_posts->draft) ) {
		$post_type_texts[] = '<a href="edit.php?post_status=draft">'.sprintf( _n( '%s draft', '%s drafts', $num_posts->draft ), number_format_i18n( $num_posts->draft ) ).'</a>';
	}
	if ( $can_edit_posts && !empty($num_posts->future) ) {
		$post_type_texts[] = '<a href="edit.php?post_status=future">'.sprintf( _n( '%s scheduled post', '%s scheduled posts', $num_posts->future ), number_format_i18n( $num_posts->future ) ).'</a>';
	}
	if ( current_user_can('publish_posts') && !empty($num_posts->pending) ) {
		$pending_text = sprintf( _n( 'There is <a href="%1$s">%2$s post</a> pending your review.', 'There are <a href="%1$s">%2$s posts</a> pending your review.', $num_posts->pending ), 'edit.php?post_status=pending', number_format_i18n( $num_posts->pending ) );
	} else {
		$pending_text = '';
	}
	*/

	// Pages
	$num = number_format_i18n( $num_pages->publish );
	$text = _n( 'Page', 'Pages', $num_pages->publish );
	if ( current_user_can( 'edit_pages' ) ) {
		$num = "<a href='edit.php?post_type=page'>$num</a>";
		$text = "<a href='edit.php?post_type=page'>$text</a>";
	}
	echo '<td class="first b b_pages">' . $num . '</td>';
	echo '<td class="t pages">' . $text . '</td>';

	echo '</tr><tr>';

	// Categories
	$num = number_format_i18n( $num_cats );
	$text = _n( 'Category', 'Categories', $num_cats );
	if ( current_user_can( 'manage_categories' ) ) {
		$num = "<a href='edit-tags.php?taxonomy=category'>$num</a>";
		$text = "<a href='edit-tags.php?taxonomy=category'>$text</a>";
	}
	echo '<td class="first b b-cats">' . $num . '</td>';
	echo '<td class="t cats">' . $text . '</td>';

	echo '</tr><tr>';

	// Tags
	$num = number_format_i18n( $num_tags );
	$text = _n( 'Tag', 'Tags', $num_tags );
	if ( current_user_can( 'manage_categories' ) ) {
		$num = "<a href='edit-tags.php'>$num</a>";
		$text = "<a href='edit-tags.php'>$text</a>";
	}
	echo '<td class="first b b-tags">' . $num . '</td>';
	echo '<td class="t tags">' . $text . '</td>';

	echo "</tr>";
	do_action('right_now_content_table_end');
	echo "\n\t</table>\n\t</div>";

	echo "\n\t".'<div class="table table_discussion">';
	echo "\n\t".'<p class="sub">' . __('Discussion') . '</p>'."\n\t".'<table>';
	echo "\n\t".'<tr class="first">';

	// Total Comments
	$num = '<span class="total-count">' . number_format_i18n($num_comm->total_comments) . '</span>';
	$text = _n( 'Comment', 'Comments', $num_comm->total_comments );
	if ( current_user_can( 'moderate_comments' ) ) {
		$num = '<a href="edit-comments.php">' . $num . '</a>';
		$text = '<a href="edit-comments.php">' . $text . '</a>';
	}
	echo '<td class="b b-comments">' . $num . '</td>';
	echo '<td class="last t comments">' . $text . '</td>';

	echo '</tr><tr>';

	// Approved Comments
	$num = '<span class="approved-count">' . number_format_i18n($num_comm->approved) . '</span>';
	$text = _nx( 'Approved', 'Approved', $num_comm->approved, 'Right Now' );
	if ( current_user_can( 'moderate_comments' ) ) {
		$num = "<a href='edit-comments.php?comment_status=approved'>$num</a>";
		$text = "<a class='approved' href='edit-comments.php?comment_status=approved'>$text</a>";
	}
	echo '<td class="b b_approved">' . $num . '</td>';
	echo '<td class="last t">' . $text . '</td>';

	echo "</tr>\n\t<tr>";

	// Pending Comments
	$num = '<span class="pending-count">' . number_format_i18n($num_comm->moderated) . '</span>';
	$text = _n( 'Pending', 'Pending', $num_comm->moderated );
	if ( current_user_can( 'moderate_comments' ) ) {
		$num = "<a href='edit-comments.php?comment_status=moderated'>$num</a>";
		$text = "<a class='waiting' href='edit-comments.php?comment_status=moderated'>$text</a>";
	}
	echo '<td class="b b-waiting">' . $num . '</td>';
	echo '<td class="last t">' . $text . '</td>';

	echo "</tr>\n\t<tr>";

	// Spam Comments
	$num = number_format_i18n($num_comm->spam);
	$text = _nx( 'Spam', 'Spam', $num_comm->spam, 'comment' );
	if ( current_user_can( 'moderate_comments' ) ) {
		$num = "<a href='edit-comments.php?comment_status=spam'><span class='spam-count'>$num</span></a>";
		$text = "<a class='spam' href='edit-comments.php?comment_status=spam'>$text</a>";
	}
	echo '<td class="b b-spam">' . $num . '</td>';
	echo '<td class="last t">' . $text . '</td>';

	echo "</tr>";
	do_action('right_now_table_end');
	do_action('right_now_discussion_table_end');
	echo "\n\t</table>\n\t</div>";

	echo "\n\t".'<div class="versions">';
	$theme = wp_get_theme();

	echo "\n\t<p>";

	if ( $theme->errors() ) {
		if ( ! is_multisite() || is_super_admin() )
			echo '<span class="error-message">' . sprintf( __( 'ERROR: %s' ), $theme->errors()->get_error_message() ) . '</span>';
	} elseif ( ! empty($wp_registered_sidebars) ) {
		$sidebars_widgets = wp_get_sidebars_widgets();
		$num_widgets = 0;
		foreach ( (array) $sidebars_widgets as $k => $v ) {
			if ( 'wp_inactive_widgets' == $k || 'orphaned_widgets' == substr( $k, 0, 16 ) )
				continue;
			if ( is_array($v) )
				$num_widgets = $num_widgets + count($v);
		}
		$num = number_format_i18n( $num_widgets );

		$switch_themes = $theme->display('Name');
		if ( current_user_can( 'switch_themes') )
			$switch_themes = '<a href="themes.php">' . $switch_themes . '</a>';
		if ( current_user_can( 'edit_theme_options' ) ) {
			printf(_n('Theme <span class="b">%1$s</span> with <span class="b"><a href="widgets.php">%2$s Widget</a></span>', 'Theme <span class="b">%1$s</span> with <span class="b"><a href="widgets.php">%2$s Widgets</a></span>', $num_widgets), $switch_themes, $num);
		} else {
			printf(_n('Theme <span class="b">%1$s</span> with <span class="b">%2$s Widget</span>', 'Theme <span class="b">%1$s</span> with <span class="b">%2$s Widgets</span>', $num_widgets), $switch_themes, $num);
		}
	} else {
		if ( current_user_can( 'switch_themes' ) )
			printf( __('Theme <span class="b"><a href="themes.php">%1$s</a></span>'), $theme->display('Name') );
		else
			printf( __('Theme <span class="b">%1$s</span>'), $theme->display('Name') );
	}
	echo '</p>';

	// Check if search engines are asked not to index this site.
	if ( !is_network_admin() && !is_user_admin() && current_user_can('manage_options') && '1' != get_option('blog_public') ) {
		$title = apply_filters('privacy_on_link_title', __('Your site is asking search engines not to index its content') );
		$content = apply_filters('privacy_on_link_text', __('Search Engines Discouraged') );

		echo "<p><a href='options-reading.php' title='$title'>$content</a></p>";
	}

	update_right_now_message();

	echo "\n\t".'<br class="clear" /></div>';
	do_action( 'rightnow_end' );
	do_action( 'activity_box_end' );
}

function wp_network_dashboard_right_now() {
	$actions = array();
	if ( current_user_can('create_sites') )
		$actions['create-site'] = '<a href="' . network_admin_url('site-new.php') . '">' . __( 'Create a New Site' ) . '</a>';
	if ( current_user_can('create_users') )
		$actions['create-user'] = '<a href="' . network_admin_url('user-new.php') . '">' . __( 'Create a New User' ) . '</a>';

	$c_users = get_user_count();
	$c_blogs = get_blog_count();

	$user_text = sprintf( _n( '%s user', '%s users', $c_users ), number_format_i18n( $c_users ) );
	$blog_text = sprintf( _n( '%s site', '%s sites', $c_blogs ), number_format_i18n( $c_blogs ) );

	$sentence = sprintf( __( 'You have %1$s and %2$s.' ), $blog_text, $user_text );

	if ( $actions ) {
		echo '<ul class="subsubsub">';
		foreach ( $actions as $class => $action ) {
			 $actions[ $class ] = "\t<li class='$class'>$action";
		}
		echo implode( " |</li>\n", $actions ) . "</li>\n";
		echo '</ul>';
	}
?>
	<br class="clear" />

	<p class="youhave"><?php echo $sentence; ?></p>
	<?php do_action( 'wpmuadminresult', '' ); ?>

	<form action="<?php echo network_admin_url('users.php'); ?>" method="get">
		<p>
			<input type="search" name="s" value="" size="30" autocomplete="off" />
			<?php submit_button( __( 'Search Users' ), 'button', 'submit', false, array( 'id' => 'submit_users' ) ); ?>
		</p>
	</form>

	<form action="<?php echo network_admin_url('sites.php'); ?>" method="get">
		<p>
			<input type="search" name="s" value="" size="30" autocomplete="off" />
			<?php submit_button( __( 'Search Sites' ), 'button', 'submit', false, array( 'id' => 'submit_sites' ) ); ?>
		</p>
	</form>
<?php
	do_action( 'mu_rightnow_end' );
	do_action( 'mu_activity_box_end' );
}

function wp_dashboard_quick_press() {
	global $post_ID;

	$drafts = false;
	if ( 'post' === strtolower( $_SERVER['REQUEST_METHOD'] ) && isset( $_POST['action'] ) && 0 === strpos( $_POST['action'], 'post-quickpress' ) && (int) $_POST['post_ID'] ) {
		$view = get_permalink( $_POST['post_ID'] );
		$edit = esc_url( get_edit_post_link( $_POST['post_ID'] ) );
		if ( 'post-quickpress-publish' == $_POST['action'] ) {
			if ( current_user_can('publish_posts') )
				printf( '<div class="updated"><p>' . __( 'Post published. <a href="%s">View post</a> | <a href="%s">Edit post</a>' ) . '</p></div>', esc_url( $view ), $edit );
			else
				printf( '<div class="updated"><p>' . __( 'Post submitted. <a href="%s">Preview post</a> | <a href="%s">Edit post</a>' ) . '</p></div>', esc_url( add_query_arg( 'preview', 1, $view ) ), $edit );
		} else {
			printf( '<div class="updated"><p>' . __( 'Draft saved. <a href="%s">Preview post</a> | <a href="%s">Edit post</a>' ) . '</p></div>', esc_url( add_query_arg( 'preview', 1, $view ) ), $edit );
			$drafts_query = new WP_Query( array(
				'post_type' => 'post',
				'post_status' => 'draft',
				'author' => $GLOBALS['current_user']->ID,
				'posts_per_page' => 1,
				'orderby' => 'modified',
				'order' => 'DESC'
			) );

			if ( $drafts_query->posts )
				$drafts =& $drafts_query->posts;
		}
		printf('<p class="easy-blogging">' . __('You can also try %s, easy blogging from anywhere on the Web.') . '</p>', '<a href="' . esc_url( admin_url( 'tools.php' ) ) . '">' . __('Press This') . '</a>' );
		$_REQUEST = array(); // hack for get_default_post_to_edit()
	}

	if ( ! current_user_can( 'edit_posts' ) ) {
		return;
	}

	/* Check if a new auto-draft (= no new post_ID) is needed or if the old can be used */
	$last_post_id = (int) get_user_option( 'dashboard_quick_press_last_post_id' ); // Get the last post_ID
	if ( $last_post_id ) {
		$post = get_post( $last_post_id );
		if ( empty( $post ) || $post->post_status != 'auto-draft' ) { // auto-draft doesn't exists anymore
			$post = get_default_post_to_edit('post', true);
			update_user_option( get_current_user_id(), 'dashboard_quick_press_last_post_id', (int) $post->ID ); // Save post_ID
		} else {
			$post->post_title = ''; // Remove the auto draft title
		}
	} else {
		$post = get_default_post_to_edit( 'post' , true);
		$user_id = get_current_user_id();
		// Don't create an option if this is a super admin who does not belong to this site.
		if ( ! ( is_super_admin( $user_id ) && ! in_array( get_current_blog_id(), array_keys( get_blogs_of_user( $user_id ) ) ) ) )
			update_user_option( $user_id, 'dashboard_quick_press_last_post_id', (int) $post->ID ); // Save post_ID
	}

	$post_ID = (int) $post->ID;

	$media_settings = array(
		'id' => $post->ID,
		'nonce' => wp_create_nonce( 'update-post_' . $post->ID ),
	);

	if ( current_theme_supports( 'post-thumbnails', $post->post_type ) && post_type_supports( $post->post_type, 'thumbnail' ) ) {
		$featured_image_id = get_post_meta( $post->ID, '_thumbnail_id', true );
		$media_settings['featuredImageId'] = $featured_image_id ? $featured_image_id : -1;
	}
?>

	<form name="post" action="<?php echo esc_url( admin_url( 'post.php' ) ); ?>" method="post" id="quick-press">
		<div class="input-text-wrap" id="title-wrap">
			<label class="screen-reader-text prompt" for="title" id="title-prompt-text"><?php _e( 'Enter title here' ); ?></label>
			<input type="text" name="post_title" id="title" autocomplete="off" value="<?php echo esc_attr( $post->post_title ); ?>" />
		</div>

		<?php if ( current_user_can( 'upload_files' ) ) : ?>
		<div id="wp-content-wrap" class="wp-editor-wrap hide-if-no-js wp-media-buttons">
			<?php do_action( 'media_buttons', 'content' ); ?>
		</div>
		<?php endif; ?>

		<div class="textarea-wrap">
			<label class="screen-reader-text" for="content"><?php _e( 'Content' ); ?></label>
			<textarea name="content" id="content" class="mceEditor" rows="3" cols="15"><?php echo esc_textarea( $post->post_content ); ?></textarea>
		</div>

		<script type="text/javascript">
		edCanvas = document.getElementById('content');
		edInsertContent = null;
		<?php if ( $_POST ) : ?>
		wp.media.editor.remove('content');
		wp.media.view.settings.post = <?php echo json_encode( $media_settings ); // big juicy hack. ?>;
		wp.media.editor.add('content');
		<?php endif; ?>
		</script>

		<div class="input-text-wrap" id="tags-input-wrap">
			<label class="screen-reader-text prompt" for="tags-input" id="tags-input-prompt-text"><?php _e( 'Tags (separate with commas)' ); ?></label>
			<input type="text" name="tags_input" id="tags-input" value="<?php echo get_tags_to_edit( $post->ID ); ?>" />
		</div>

		<p class="submit">
			<span id="publishing-action">
				<input type="submit" name="publish" id="publish" accesskey="p" class="button-primary" value="<?php current_user_can('publish_posts') ? esc_attr_e('Publish') : esc_attr_e('Submit for Review'); ?>" />
				<span class="spinner"></span>
			</span>
			<input type="hidden" name="action" id="quickpost-action" value="post-quickpress-save" />
			<input type="hidden" name="post_ID" value="<?php echo $post_ID; ?>" />
			<input type="hidden" name="post_type" value="post" />
			<?php wp_nonce_field('add-post'); ?>
			<?php submit_button( __( 'Save Draft' ), 'button', 'save', false, array( 'id' => 'save-post' ) ); ?>
			<input type="reset" value="<?php esc_attr_e( 'Reset' ); ?>" class="button" />
			<br class="clear" />
		</p>

	</form>

<?php
	if ( $drafts )
		wp_dashboard_recent_drafts( $drafts );
}

function wp_dashboard_recent_drafts( $drafts = false ) {
	if ( !$drafts ) {
		$drafts_query = new WP_Query( array(
			'post_type' => 'post',
			'post_status' => 'draft',
			'author' => $GLOBALS['current_user']->ID,
			'posts_per_page' => 5,
			'orderby' => 'modified',
			'order' => 'DESC'
		) );
		$drafts =& $drafts_query->posts;
	}

	if ( $drafts && is_array( $drafts ) ) {
		$list = array();
		foreach ( $drafts as $draft ) {
			$url = get_edit_post_link( $draft->ID );
			$title = _draft_or_post_title( $draft->ID );
			$item = "<h4><a href='$url' title='" . sprintf( __( 'Edit &#8220;%s&#8221;' ), esc_attr( $title ) ) . "'>" . esc_html($title) . "</a> <abbr title='" . get_the_time(__('Y/m/d g:i:s A'), $draft) . "'>" . get_the_time( get_option( 'date_format' ), $draft ) . '</abbr></h4>';
			if ( $the_content = wp_trim_words( $draft->post_content, 10 ) )
				$item .= '<p>' . $the_content . '</p>';
			$list[] = $item;
		}
?>
	<ul>
		<li><?php echo join( "</li>\n<li>", $list ); ?></li>
	</ul>
	<p class="textright"><a href="edit.php?post_status=draft" ><?php _e('View all'); ?></a></p>
<?php
	} else {
		_e('There are no drafts at the moment');
	}
}

/**
 * Display recent comments dashboard widget content.
 *
 * @since 2.5.0
 */
function wp_dashboard_recent_comments() {
	global $wpdb;

	// Select all comment types and filter out spam later for better query performance.
	$comments = array();
	$start = 0;

	$widgets = get_option( 'dashboard_widget_options' );
	$total_items = isset( $widgets['dashboard_recent_comments'] ) && isset( $widgets['dashboard_recent_comments']['items'] )
		? absint( $widgets['dashboard_recent_comments']['items'] ) : 5;

	$comments_query = array( 'number' => $total_items * 5, 'offset' => 0 );
	if ( ! current_user_can( 'edit_posts' ) )
		$comments_query['status'] = 'approve';

	while ( count( $comments ) < $total_items && $possible = get_comments( $comments_query ) ) {
		foreach ( $possible as $comment ) {
			if ( ! current_user_can( 'read_post', $comment->comment_post_ID ) )
				continue;
			$comments[] = $comment;
			if ( count( $comments ) == $total_items )
				break 2;
		}
		$comments_query['offset'] += $comments_query['number'];
		$comments_query['number'] = $total_items * 10;
	}

	if ( $comments ) {
		echo '<div id="the-comment-list" data-wp-lists="list:comment">';
		foreach ( $comments as $comment )
			_wp_dashboard_recent_comments_row( $comment );
		echo '</div>';

		if ( current_user_can('edit_posts') )
			_get_list_table('WP_Comments_List_Table')->views();

		wp_comment_reply( -1, false, 'dashboard', false );
		wp_comment_trashnotice();
	} else {
		echo '<p>' . __( 'No comments yet.' ) . '</p>';
	}
}

function _wp_dashboard_recent_comments_row( &$comment, $show_date = true ) {
	$GLOBALS['comment'] =& $comment;

	$comment_post_url = get_edit_post_link( $comment->comment_post_ID );
	$comment_post_title = _draft_or_post_title( $comment->comment_post_ID );
	$comment_post_link = "<a href='$comment_post_url'>$comment_post_title</a>";
	$comment_link = '<a class="comment-link" href="' . esc_url(get_comment_link()) . '">#</a>';

	$actions_string = '';
	if ( current_user_can( 'edit_comment', $comment->comment_ID ) ) {
		// preorder it: Approve | Reply | Edit | Spam | Trash
		$actions = array(
			'approve' => '', 'unapprove' => '',
			'reply' => '',
			'edit' => '',
			'spam' => '',
			'trash' => '', 'delete' => ''
		);

		$del_nonce = esc_html( '_wpnonce=' . wp_create_nonce( "delete-comment_$comment->comment_ID" ) );
		$approve_nonce = esc_html( '_wpnonce=' . wp_create_nonce( "approve-comment_$comment->comment_ID" ) );

		$approve_url = esc_url( "comment.php?action=approvecomment&p=$comment->comment_post_ID&c=$comment->comment_ID&$approve_nonce" );
		$unapprove_url = esc_url( "comment.php?action=unapprovecomment&p=$comment->comment_post_ID&c=$comment->comment_ID&$approve_nonce" );
		$spam_url = esc_url( "comment.php?action=spamcomment&p=$comment->comment_post_ID&c=$comment->comment_ID&$del_nonce" );
		$trash_url = esc_url( "comment.php?action=trashcomment&p=$comment->comment_post_ID&c=$comment->comment_ID&$del_nonce" );
		$delete_url = esc_url( "comment.php?action=deletecomment&p=$comment->comment_post_ID&c=$comment->comment_ID&$del_nonce" );

		$actions['approve'] = "<a href='$approve_url' data-wp-lists='dim:the-comment-list:comment-$comment->comment_ID:unapproved:e7e7d3:e7e7d3:new=approved' class='vim-a' title='" . esc_attr__( 'Approve this comment' ) . "'>" . __( 'Approve' ) . '</a>';
		$actions['unapprove'] = "<a href='$unapprove_url' data-wp-lists='dim:the-comment-list:comment-$comment->comment_ID:unapproved:e7e7d3:e7e7d3:new=unapproved' class='vim-u' title='" . esc_attr__( 'Unapprove this comment' ) . "'>" . __( 'Unapprove' ) . '</a>';
		$actions['edit'] = "<a href='comment.php?action=editcomment&amp;c={$comment->comment_ID}' title='" . esc_attr__('Edit comment') . "'>". __('Edit') . '</a>';
		$actions['reply'] = '<a onclick="commentReply.open(\''.$comment->comment_ID.'\',\''.$comment->comment_post_ID.'\');return false;" class="vim-r hide-if-no-js" title="'.esc_attr__('Reply to this comment').'" href="#">' . __('Reply') . '</a>';
		$actions['spam'] = "<a href='$spam_url' data-wp-lists='delete:the-comment-list:comment-$comment->comment_ID::spam=1' class='vim-s vim-destructive' title='" . esc_attr__( 'Mark this comment as spam' ) . "'>" . /* translators: mark as spam link */ _x( 'Spam', 'verb' ) . '</a>';
		if ( !EMPTY_TRASH_DAYS )
			$actions['delete'] = "<a href='$delete_url' data-wp-lists='delete:the-comment-list:comment-$comment->comment_ID::trash=1' class='delete vim-d vim-destructive'>" . __('Delete Permanently') . '</a>';
		else
			$actions['trash'] = "<a href='$trash_url' data-wp-lists='delete:the-comment-list:comment-$comment->comment_ID::trash=1' class='delete vim-d vim-destructive' title='" . esc_attr__( 'Move this comment to the trash' ) . "'>" . _x('Trash', 'verb') . '</a>';

		$actions = apply_filters( 'comment_row_actions', array_filter($actions), $comment );

		$i = 0;
		foreach ( $actions as $action => $link ) {
			++$i;
			( ( ('approve' == $action || 'unapprove' == $action) && 2 === $i ) || 1 === $i ) ? $sep = '' : $sep = ' | ';

			// Reply and quickedit need a hide-if-no-js span
			if ( 'reply' == $action || 'quickedit' == $action )
				$action .= ' hide-if-no-js';

			$actions_string .= "<span class='$action'>$sep$link</span>";
		}
	}

?>

		<div id="comment-<?php echo $comment->comment_ID; ?>" <?php comment_class( array( 'comment-item', wp_get_comment_status($comment->comment_ID) ) ); ?>>
			<?php if ( !$comment->comment_type || 'comment' == $comment->comment_type ) : ?>

			<?php echo get_avatar( $comment, 50, 'mystery' ); ?>

			<div class="dashboard-comment-wrap">
			<h4 class="comment-meta">
				<?php printf( /* translators: 1: comment author, 2: post link, 3: notification if the comment is pending */__( 'From %1$s on %2$s%3$s' ),
					'<cite class="comment-author">' . get_comment_author_link() . '</cite>', $comment_post_link.' '.$comment_link, ' <span class="approve">' . __( '[Pending]' ) . '</span>' ); ?>
			</h4>

			<?php
			else :
				switch ( $comment->comment_type ) :
				case 'pingback' :
					$type = __( 'Pingback' );
					break;
				case 'trackback' :
					$type = __( 'Trackback' );
					break;
				default :
					$type = ucwords( $comment->comment_type );
				endswitch;
				$type = esc_html( $type );
			?>
			<div class="dashboard-comment-wrap">
			<?php /* translators: %1$s is type of comment, %2$s is link to the post */ ?>
			<h4 class="comment-meta"><?php printf( _x( '%1$s on %2$s', 'dashboard' ), "<strong>$type</strong>", $comment_post_link." ".$comment_link ); ?></h4>
			<p class="comment-author"><?php comment_author_link(); ?></p>

			<?php endif; // comment_type ?>
			<blockquote><p><?php comment_excerpt(); ?></p></blockquote>
			<p class="row-actions"><?php echo $actions_string; ?></p>
			</div>
		</div>
<?php
}

/**
 * The recent comments dashboard widget control.
 *
 * @since 3.0.0
 */
function wp_dashboard_recent_comments_control() {
	if ( !$widget_options = get_option( 'dashboard_widget_options' ) )
		$widget_options = array();

	if ( !isset($widget_options['dashboard_recent_comments']) )
		$widget_options['dashboard_recent_comments'] = array();

	if ( 'POST' == $_SERVER['REQUEST_METHOD'] && isset($_POST['widget-recent-comments']) ) {
		$number = absint( $_POST['widget-recent-comments']['items'] );
		$widget_options['dashboard_recent_comments']['items'] = $number;
		update_option( 'dashboard_widget_options', $widget_options );
	}

	$number = isset( $widget_options['dashboard_recent_comments']['items'] ) ? (int) $widget_options['dashboard_recent_comments']['items'] : '';

	echo '<p><label for="comments-number">' . __('Number of comments to show:') . '</label>';
	echo '<input id="comments-number" name="widget-recent-comments[items]" type="text" value="' . $number . '" size="3" /></p>';
}

function wp_dashboard_incoming_links() {
	wp_dashboard_cached_rss_widget( 'dashboard_incoming_links', 'wp_dashboard_incoming_links_output' );
}

/**
 * Display incoming links dashboard widget content.
 *
 * @since 2.5.0
 */
function wp_dashboard_incoming_links_output() {
	$widgets = get_option( 'dashboard_widget_options' );
	@extract( @$widgets['dashboard_incoming_links'], EXTR_SKIP );
	$rss = fetch_feed( $url );

	if ( is_wp_error($rss) ) {
		if ( is_admin() || current_user_can('manage_options') ) {
			echo '<p>';
			printf(__('<strong>RSS Error</strong>: %s'), $rss->get_error_message());
			echo '</p>';
		}
		return;
	}

	if ( !$rss->get_item_quantity() ) {
		echo '<p>' . __('This dashboard widget queries <a href="http://blogsearch.google.com/">Google Blog Search</a> so that when another blog links to your site it will show up here. It has found no incoming links&hellip; yet. It&#8217;s okay &#8212; there is no rush.') . "</p>\n";
		$rss->__destruct();
		unset($rss);
		return;
	}

	echo "<ul>\n";

	if ( !isset($items) )
		$items = 10;

	foreach ( $rss->get_items(0, $items) as $item ) {
		$publisher = '';
		$site_link = '';
		$link = '';
		$content = '';
		$date = '';
		$link = esc_url( strip_tags( $item->get_link() ) );

		$author = $item->get_author();
		if ( $author ) {
			$site_link = esc_url( strip_tags( $author->get_link() ) );

			if ( !$publisher = esc_html( strip_tags( $author->get_name() ) ) )
				$publisher = __( 'Somebody' );
		} else {
		  $publisher = __( 'Somebody' );
		}
		if ( $site_link )
			$publisher = "<a href='$site_link'>$publisher</a>";
		else
			$publisher = "<strong>$publisher</strong>";

		$content = $item->get_content();
		$content = wp_html_excerpt( $content, 50, ' &hellip;' );

		if ( $link )
			/* translators: incoming links feed, %1$s is other person, %3$s is content */
			$text = __( '%1$s linked here <a href="%2$s">saying</a>, "%3$s"' );
		else
			/* translators: incoming links feed, %1$s is other person, %3$s is content */
			$text = __( '%1$s linked here saying, "%3$s"' );

		if ( !empty( $show_date ) ) {
			if ( $link )
				/* translators: incoming links feed, %1$s is other person, %3$s is content, %4$s is the date */
				$text = __( '%1$s linked here <a href="%2$s">saying</a>, "%3$s" on %4$s' );
			else
				/* translators: incoming links feed, %1$s is other person, %3$s is content, %4$s is the date */
				$text = __( '%1$s linked here saying, "%3$s" on %4$s' );
			$date = esc_html( strip_tags( $item->get_date() ) );
			$date = strtotime( $date );
			$date = gmdate( get_option( 'date_format' ), $date );
		}

		echo "\t<li>" . sprintf( $text, $publisher, $link, $content, $date ) . "</li>\n";
	}

	echo "</ul>\n";
	$rss->__destruct();
	unset($rss);
}

function wp_dashboard_incoming_links_control() {
	wp_dashboard_rss_control( 'dashboard_incoming_links', array( 'title' => false, 'show_summary' => false, 'show_author' => false ) );
}

function wp_dashboard_primary() {
	wp_dashboard_cached_rss_widget( 'dashboard_primary', 'wp_dashboard_rss_output' );
}

function wp_dashboard_primary_control() {
	wp_dashboard_rss_control( 'dashboard_primary' );
}

/**
 * Display primary dashboard RSS widget feed.
 *
 * @since 2.5.0
 *
 * @param string $widget_id
 */
function wp_dashboard_rss_output( $widget_id ) {
	$widgets = get_option( 'dashboard_widget_options' );
	echo '<div class="rss-widget">';
	wp_widget_rss_output( $widgets[$widget_id] );
	echo "</div>";
}

function wp_dashboard_secondary() {
	wp_dashboard_cached_rss_widget( 'dashboard_secondary', 'wp_dashboard_secondary_output' );
}

function wp_dashboard_secondary_control() {
	wp_dashboard_rss_control( 'dashboard_secondary' );
}

/**
 * Display secondary dashboard RSS widget feed.
 *
 * @since 2.5.0
 *
 * @return unknown
 */
function wp_dashboard_secondary_output() {
	$widgets = get_option( 'dashboard_widget_options' );
	@extract( @$widgets['dashboard_secondary'], EXTR_SKIP );
	$rss = @fetch_feed( $url );

	if ( is_wp_error($rss) ) {
		if ( is_admin() || current_user_can('manage_options') ) {
			echo '<div class="rss-widget"><p>';
			printf(__('<strong>RSS Error</strong>: %s'), $rss->get_error_message());
			echo '</p></div>';
		}
	} elseif ( !$rss->get_item_quantity() ) {
		$rss->__destruct();
		unset($rss);
		return false;
	} else {
		echo '<div class="rss-widget">';
		wp_widget_rss_output( $rss, $widgets['dashboard_secondary'] );
		echo '</div>';
		$rss->__destruct();
		unset($rss);
	}
}

function wp_dashboard_plugins() {
	wp_dashboard_cached_rss_widget( 'dashboard_plugins', 'wp_dashboard_plugins_output', array(
		'http://wordpress.org/plugins/rss/browse/popular/',
		'http://wordpress.org/plugins/rss/browse/new/'
	) );
}

/**
 * Display plugins most popular, newest plugins, and recently updated widget text.
 *
 * @since 2.5.0
 */
function wp_dashboard_plugins_output() {
	$popular = fetch_feed( 'http://wordpress.org/plugins/rss/browse/popular/' );
	$new     = fetch_feed( 'http://wordpress.org/plugins/rss/browse/new/' );

	if ( false === $plugin_slugs = get_transient( 'plugin_slugs' ) ) {
		$plugin_slugs = array_keys( get_plugins() );
		set_transient( 'plugin_slugs', $plugin_slugs, DAY_IN_SECONDS );
	}

	foreach ( array( 'popular' => __('Most Popular'), 'new' => __('Newest Plugins') ) as $feed => $label ) {
		if ( is_wp_error($$feed) || !$$feed->get_item_quantity() )
			continue;

		$items = $$feed->get_items(0, 5);

		// Pick a random, non-installed plugin
		while ( true ) {
			// Abort this foreach loop iteration if there's no plugins left of this type
			if ( 0 == count($items) )
				continue 2;

			$item_key = array_rand($items);
			$item = $items[$item_key];

			list($link, $frag) = explode( '#', $item->get_link() );

			$link = esc_url($link);
			if ( preg_match( '|/([^/]+?)/?$|', $link, $matches ) )
				$slug = $matches[1];
			else {
				unset( $items[$item_key] );
				continue;
			}

			// Is this random plugin's slug already installed? If so, try again.
			reset( $plugin_slugs );
			foreach ( $plugin_slugs as $plugin_slug ) {
				if ( $slug == substr( $plugin_slug, 0, strlen( $slug ) ) ) {
					unset( $items[$item_key] );
					continue 2;
				}
			}

			// If we get to this point, then the random plugin isn't installed and we can stop the while().
			break;
		}

		// Eliminate some common badly formed plugin descriptions
		while ( ( null !== $item_key = array_rand($items) ) && false !== strpos( $items[$item_key]->get_description(), 'Plugin Name:' ) )
			unset($items[$item_key]);

		if ( !isset($items[$item_key]) )
			continue;

		$title = esc_html( $item->get_title() );

		$description = esc_html( strip_tags(@html_entity_decode($item->get_description(), ENT_QUOTES, get_option('blog_charset'))) );

		$ilink = wp_nonce_url('plugin-install.php?tab=plugin-information&plugin=' . $slug, 'install-plugin_' . $slug) .
							'&amp;TB_iframe=true&amp;width=600&amp;height=800';

		echo "<h4>$label</h4>\n";
		echo "<h5><a href='$link'>$title</a></h5>&nbsp;<span>(<a href='$ilink' class='thickbox' title='$title'>" . __( 'Install' ) . "</a>)</span>\n";
		echo "<p>$description</p>\n";

		$$feed->__destruct();
		unset($$feed);
	}
}

/**
 * Checks to see if all of the feed url in $check_urls are cached.
 *
 * If $check_urls is empty, look for the rss feed url found in the dashboard
 * widget options of $widget_id. If cached, call $callback, a function that
 * echoes out output for this widget. If not cache, echo a "Loading..." stub
 * which is later replaced by AJAX call (see top of /wp-admin/index.php)
 *
 * @since 2.5.0
 *
 * @param string $widget_id
 * @param callback $callback
 * @param array $check_urls RSS feeds
 * @return bool False on failure. True on success.
 */
function wp_dashboard_cached_rss_widget( $widget_id, $callback, $check_urls = array() ) {
	$loading = '<p class="widget-loading hide-if-no-js">' . __( 'Loading&#8230;' ) . '</p><p class="hide-if-js">' . __( 'This widget requires JavaScript.' ) . '</p>';
	$doing_ajax = ( defined('DOING_AJAX') && DOING_AJAX );

	if ( empty($check_urls) ) {
		$widgets = get_option( 'dashboard_widget_options' );
		if ( empty($widgets[$widget_id]['url']) && ! $doing_ajax ) {
			echo $loading;
			return false;
		}
		$check_urls = array( $widgets[$widget_id]['url'] );
	}

	$cache_key = 'dash_' . md5( $widget_id );
	if ( false !== ( $output = get_transient( $cache_key ) ) ) {
		echo $output;
		return true;
	}

	if ( ! $doing_ajax ) {
		echo $loading;
		return false;
	}

	if ( $callback && is_callable( $callback ) ) {
		$args = array_slice( func_get_args(), 2 );
		array_unshift( $args, $widget_id );
		ob_start();
		call_user_func_array( $callback, $args );
		set_transient( $cache_key, ob_get_flush(), 12 * HOUR_IN_SECONDS ); // Default lifetime in cache of 12 hours (same as the feeds)
	}

	return true;
}

/* Dashboard Widgets Controls */

// Calls widget_control callback
/**
 * Calls widget control callback.
 *
 * @since 2.5.0
 *
 * @param int $widget_control_id Registered Widget ID.
 */
function wp_dashboard_trigger_widget_control( $widget_control_id = false ) {
	global $wp_dashboard_control_callbacks;

	if ( is_scalar($widget_control_id) && $widget_control_id && isset($wp_dashboard_control_callbacks[$widget_control_id]) && is_callable($wp_dashboard_control_callbacks[$widget_control_id]) ) {
		call_user_func( $wp_dashboard_control_callbacks[$widget_control_id], '', array( 'id' => $widget_control_id, 'callback' => $wp_dashboard_control_callbacks[$widget_control_id] ) );
	}
}

/**
 * The RSS dashboard widget control.
 *
 * Sets up $args to be used as input to wp_widget_rss_form(). Handles POST data
 * from RSS-type widgets.
 *
 * @since 2.5.0
 *
 * @param string $widget_id
 * @param array $form_inputs
 */
function wp_dashboard_rss_control( $widget_id, $form_inputs = array() ) {
	if ( !$widget_options = get_option( 'dashboard_widget_options' ) )
		$widget_options = array();

	if ( !isset($widget_options[$widget_id]) )
		$widget_options[$widget_id] = array();

	$number = 1; // Hack to use wp_widget_rss_form()
	$widget_options[$widget_id]['number'] = $number;

	if ( 'POST' == $_SERVER['REQUEST_METHOD'] && isset($_POST['widget-rss'][$number]) ) {
		$_POST['widget-rss'][$number] = wp_unslash( $_POST['widget-rss'][$number] );
		$widget_options[$widget_id] = wp_widget_rss_process( $_POST['widget-rss'][$number] );
		$widget_options[$widget_id]['number'] = $number;
		// title is optional. If black, fill it if possible
		if ( !$widget_options[$widget_id]['title'] && isset($_POST['widget-rss'][$number]['title']) ) {
			$rss = fetch_feed($widget_options[$widget_id]['url']);
			if ( is_wp_error($rss) ) {
				$widget_options[$widget_id]['title'] = htmlentities(__('Unknown Feed'));
			} else {
				$widget_options[$widget_id]['title'] = htmlentities(strip_tags($rss->get_title()));
				$rss->__destruct();
				unset($rss);
			}
		}
		update_option( 'dashboard_widget_options', $widget_options );
		$cache_key = 'dash_' . md5( $widget_id );
		delete_transient( $cache_key );
	}

	wp_widget_rss_form( $widget_options[$widget_id], $form_inputs );
}

/**
 * Display file upload quota on dashboard.
 *
 * Runs on the activity_box_end hook in wp_dashboard_right_now().
 *
 * @since 3.0.0
 *
 * @return bool True if not multisite, user can't upload files, or the space check option is disabled.
*/
function wp_dashboard_quota() {
	if ( !is_multisite() || !current_user_can('upload_files') || get_site_option( 'upload_space_check_disabled' ) )
		return true;

	$quota = get_space_allowed();
	$used = get_space_used();

	if ( $used > $quota )
		$percentused = '100';
	else
		$percentused = ( $used / $quota ) * 100;
	$used_color = ( $percentused >= 70 ) ? ' spam' : '';
	$used = round( $used, 2 );
	$percentused = number_format( $percentused );

	?>
	<p class="sub musub"><?php _e( 'Storage Space' ); ?></p>
	<div class="table table_content musubtable">
	<table>
		<tr class="first">
			<td class="first b b-posts"><?php printf( __( '<a href="%1$s" title="Manage Uploads" class="musublink">%2$sMB</a>' ), esc_url( admin_url( 'upload.php' ) ), number_format_i18n( $quota ) ); ?></td>
			<td class="t posts"><?php _e( 'Space Allowed' ); ?></td>
		</tr>
	</table>
	</div>
	<div class="table table_discussion musubtable">
	<table>
		<tr class="first">
			<td class="b b-comments"><?php printf( __( '<a href="%1$s" title="Manage Uploads" class="musublink">%2$sMB (%3$s%%)</a>' ), esc_url( admin_url( 'upload.php' ) ), number_format_i18n( $used, 2 ), $percentused ); ?></td>
			<td class="last t comments<?php echo $used_color;?>"><?php _e( 'Space Used' );?></td>
		</tr>
	</table>
	</div>
	<br class="clear" />
	<?php
}
add_action( 'activity_box_end', 'wp_dashboard_quota' );

// Display Browser Nag Meta Box
function wp_dashboard_browser_nag() {
	$notice = '';
	$response = wp_check_browser_version();

	if ( $response ) {
		if ( $response['insecure'] ) {
			$msg = sprintf( __( "It looks like you're using an insecure version of <a href='%s'>%s</a>. Using an outdated browser makes your computer unsafe. For the best WordPress experience, please update your browser." ), esc_attr( $response['update_url'] ), esc_html( $response['name'] ) );
		} else {
			$msg = sprintf( __( "It looks like you're using an old version of <a href='%s'>%s</a>. For the best WordPress experience, please update your browser." ), esc_attr( $response['update_url'] ), esc_html( $response['name'] ) );
		}

		$browser_nag_class = '';
		if ( !empty( $response['img_src'] ) ) {
			$img_src = ( is_ssl() && ! empty( $response['img_src_ssl'] ) )? $response['img_src_ssl'] : $response['img_src'];

			$notice .= '<div class="alignright browser-icon"><a href="' . esc_attr($response['update_url']) . '"><img src="' . esc_attr( $img_src ) . '" alt="" /></a></div>';
			$browser_nag_class = ' has-browser-icon';
		}
		$notice .= "<p class='browser-update-nag{$browser_nag_class}'>{$msg}</p>";

		$browsehappy = 'http://browsehappy.com/';
		$locale = get_locale();
		if ( 'en_US' !== $locale )
			$browsehappy = add_query_arg( 'locale', $locale, $browsehappy );

		$notice .= '<p>' . sprintf( __( '<a href="%1$s" class="update-browser-link">Update %2$s</a> or learn how to <a href="%3$s" class="browse-happy-link">browse happy</a>' ), esc_attr( $response['update_url'] ), esc_html( $response['name'] ), esc_url( $browsehappy ) ) . '</p>';
		$notice .= '<p class="hide-if-no-js"><a href="" class="dismiss">' . __( 'Dismiss' ) . '</a></p>';
		$notice .= '<div class="clear"></div>';
	}

	echo apply_filters( 'browse-happy-notice', $notice, $response );
}

function dashboard_browser_nag_class( $classes ) {
	$response = wp_check_browser_version();

	if ( $response && $response['insecure'] )
		$classes[] = 'browser-insecure';

	return $classes;
}

/**
 * Check if the user needs a browser update
 *
 * @since 3.2.0
 *
 * @return array|bool False on failure, array of browser data on success.
 */
function wp_check_browser_version() {
	if ( empty( $_SERVER['HTTP_USER_AGENT'] ) )
		return false;

	$key = md5( $_SERVER['HTTP_USER_AGENT'] );

	if ( false === ($response = get_site_transient('browser_' . $key) ) ) {
		global $wp_version;

		$options = array(
			'body'			=> array( 'useragent' => $_SERVER['HTTP_USER_AGENT'] ),
			'user-agent'	=> 'WordPress/' . $wp_version . '; ' . home_url()
		);

		$response = wp_remote_post( 'http://api.wordpress.org/core/browse-happy/1.1/', $options );

		if ( is_wp_error( $response ) || 200 != wp_remote_retrieve_response_code( $response ) )
			return false;

		/**
		 * Response should be an array with:
		 *  'name' - string - A user friendly browser name
		 *  'version' - string - The most recent version of the browser
		 *  'current_version' - string - The version of the browser the user is using
		 *  'upgrade' - boolean - Whether the browser needs an upgrade
		 *  'insecure' - boolean - Whether the browser is deemed insecure
		 *  'upgrade_url' - string - The url to visit to upgrade
		 *  'img_src' - string - An image representing the browser
		 *  'img_src_ssl' - string - An image (over SSL) representing the browser
		 */
		$response = json_decode( wp_remote_retrieve_body( $response ), true );

		if ( ! is_array( $response ) )
			return false;

		set_site_transient( 'browser_' . $key, $response, WEEK_IN_SECONDS );
	}

	return $response;
}

/**
 * Empty function usable by plugins to output empty dashboard widget (to be populated later by JS).
 */
function wp_dashboard_empty() {}

/**
 * Displays a welcome panel to introduce users to WordPress.
 *
 * @since 3.3.0
 */
function wp_welcome_panel() {
	?>
	<div class="welcome-panel-content">
	<h3><?php _e( 'Welcome to WordPress!' ); ?></h3>
	<p class="about-description"><?php _e( 'We&#8217;ve assembled some links to get you started:' ); ?></p>
	<div class="welcome-panel-column-container">
	<div class="welcome-panel-column">
		<h4><?php _e( 'Get Started' ); ?></h4>
		<a class="button button-primary button-hero load-customize hide-if-no-customize" href="<?php echo wp_customize_url(); ?>"><?php _e( 'Customize Your Site' ); ?></a>
		<a class="button button-primary button-hero hide-if-customize" href="<?php echo admin_url( 'themes.php' ); ?>"><?php _e( 'Customize Your Site' ); ?></a>
		<?php if ( current_user_can( 'install_themes' ) || ( current_user_can( 'switch_themes' ) && count( wp_get_themes( array( 'allowed' => true ) ) ) > 1 ) ) : ?>
			<p class="hide-if-no-customize"><?php printf( __( 'or, <a href="%s">change your theme completely</a>' ), admin_url( 'themes.php' ) ); ?></p>
		<?php endif; ?>
	</div>
	<div class="welcome-panel-column">
		<h4><?php _e( 'Next Steps' ); ?></h4>
		<ul>
		<?php if ( 'page' == get_option( 'show_on_front' ) && ! get_option( 'page_for_posts' ) ) : ?>
			<li><?php printf( '<a href="%s" class="welcome-icon welcome-edit-page">' . __( 'Edit your front page' ) . '</a>', get_edit_post_link( get_option( 'page_on_front' ) ) ); ?></li>
			<li><?php printf( '<a href="%s" class="welcome-icon welcome-add-page">' . __( 'Add additional pages' ) . '</a>', admin_url( 'post-new.php?post_type=page' ) ); ?></li>
		<?php elseif ( 'page' == get_option( 'show_on_front' ) ) : ?>
			<li><?php printf( '<a href="%s" class="welcome-icon welcome-edit-page">' . __( 'Edit your front page' ) . '</a>', get_edit_post_link( get_option( 'page_on_front' ) ) ); ?></li>
			<li><?php printf( '<a href="%s" class="welcome-icon welcome-add-page">' . __( 'Add additional pages' ) . '</a>', admin_url( 'post-new.php?post_type=page' ) ); ?></li>
			<li><?php printf( '<a href="%s" class="welcome-icon welcome-write-blog">' . __( 'Add a blog post' ) . '</a>', admin_url( 'post-new.php' ) ); ?></li>
		<?php else : ?>
			<li><?php printf( '<a href="%s" class="welcome-icon welcome-write-blog">' . __( 'Write your first blog post' ) . '</a>', admin_url( 'post-new.php' ) ); ?></li>
			<li><?php printf( '<a href="%s" class="welcome-icon welcome-add-page">' . __( 'Add an About page' ) . '</a>', admin_url( 'post-new.php?post_type=page' ) ); ?></li>
		<?php endif; ?>
			<li><?php printf( '<a href="%s" class="welcome-icon welcome-view-site">' . __( 'View your site' ) . '</a>', home_url( '/' ) ); ?></li>
		</ul>
	</div>
	<div class="welcome-panel-column welcome-panel-last">
		<h4><?php _e( 'More Actions' ); ?></h4>
		<ul>
			<li><?php printf( '<div class="welcome-icon welcome-widgets-menus">' . __( 'Manage <a href="%1$s">widgets</a> or <a href="%2$s">menus</a>' ) . '</div>', admin_url( 'widgets.php' ), admin_url( 'nav-menus.php' ) ); ?></li>
			<li><?php printf( '<a href="%s" class="welcome-icon welcome-comments">' . __( 'Turn comments on or off' ) . '</a>', admin_url( 'options-discussion.php' ) ); ?></li>
			<li><?php printf( '<a href="%s" class="welcome-icon welcome-learn-more">' . __( 'Learn more about getting started' ) . '</a>', __( 'http://codex.wordpress.org/First_Steps_With_WordPress' ) ); ?></li>
		</ul>
	</div>
	</div>
	</div>
	<?php
}
