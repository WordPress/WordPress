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
		wp_add_dashboard_widget( 'dashboard_right_now', __( 'At a Glance' ), 'wp_dashboard_right_now' );

	if ( is_network_admin() )
		wp_add_dashboard_widget( 'network_dashboard_right_now', __( 'Right Now' ), 'wp_network_dashboard_right_now' );

	// Activity Widget
	if ( is_blog_admin() ) {
		wp_add_dashboard_widget( 'dashboard_activity', __( 'Activity' ), 'wp_dashboard_site_activity' );
	}

	// QuickPress Widget
	if ( is_blog_admin() && current_user_can( 'edit_posts' ) ) {
		$quick_draft_title = sprintf( '<span class="hide-if-no-js">%1$s</span> <span class="hide-if-js">%2$s</span>', __( 'Quick Draft' ), __( 'Drafts' ) );
		wp_add_dashboard_widget( 'dashboard_quick_press', $quick_draft_title, 'wp_dashboard_quick_press' );
	}

	// WordPress News
	wp_add_dashboard_widget( 'dashboard_primary', __( 'WordPress News' ), 'wp_dashboard_primary' );

	if ( is_network_admin() ) {

		/**
		 * Fires after core widgets for the Network Admin dashboard have been registered.
		 *
		 * @since 3.1.0
		 */
		do_action( 'wp_network_dashboard_setup' );

		/**
		 * Filter the list of widgets to load for the Network Admin dashboard.
		 *
		 * @since 3.1.0
		 *
		 * @param array $dashboard_widgets An array of dashboard widgets.
		 */
		$dashboard_widgets = apply_filters( 'wp_network_dashboard_widgets', array() );
	} elseif ( is_user_admin() ) {

		/**
		 * Fires after core widgets for the User Admin dashboard have been registered.
		 *
		 * @since 3.1.0
		 */
		do_action( 'wp_user_dashboard_setup' );

		/**
		 * Filter the list of widgets to load for the User Admin dashboard.
		 *
		 * @since 3.1.0
		 *
		 * @param array $dashboard_widgets An array of dashboard widgets.
		 */
		$dashboard_widgets = apply_filters( 'wp_user_dashboard_widgets', array() );
	} else {

		/**
		 * Fires after core widgets for the admin dashboard have been registered.
		 *
		 * @since 2.5.0
		 */
		do_action( 'wp_dashboard_setup' );

		/**
		 * Filter the list of widgets to load for the admin dashboard.
		 *
		 * @since 2.5.0
		 *
		 * @param array $dashboard_widgets An array of dashboard widgets.
		 */
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

	/** This action is documented in wp-admin/edit-form-advanced.php */
	do_action( 'do_meta_boxes', $screen->id, 'normal', '' );

	/** This action is documented in wp-admin/edit-form-advanced.php */
	do_action( 'do_meta_boxes', $screen->id, 'side', '' );
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

	$side_widgets = array( 'dashboard_quick_press', 'dashboard_primary' );

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
	$columns = absint( $screen->get_columns() );
	$columns_css = '';
	if ( $columns ) {
		$columns_css = " columns-$columns";
	}

?>
<div id="dashboard-widgets" class="metabox-holder<?php echo $columns_css; ?>">
	<div id="postbox-container-1" class="postbox-container">
	<?php do_meta_boxes( $screen->id, 'normal', '' ); ?>
	</div>
	<div id="postbox-container-2" class="postbox-container">
	<?php do_meta_boxes( $screen->id, 'side', '' ); ?>
	</div>
	<div id="postbox-container-3" class="postbox-container">
	<?php do_meta_boxes( $screen->id, 'column3', '' ); ?>
	</div>
	<div id="postbox-container-4" class="postbox-container">
	<?php do_meta_boxes( $screen->id, 'column4', '' ); ?>
	</div>
</div>

<?php
	wp_nonce_field( 'closedpostboxes', 'closedpostboxesnonce', false );
	wp_nonce_field( 'meta-box-order', 'meta-box-order-nonce', false );

}

//
// Dashboard Widgets
//

/**
 * Dashboard widget that displays some basic stats about the site.
 *
 * Formerly 'Right Now'. A streamlined 'At a Glance' as of 3.8.
 *
 * @since 2.7.0
 */
function wp_dashboard_right_now() {
?>
	<div class="main">
	<ul>
	<?php
	// Posts and Pages
	foreach ( array( 'post', 'page' ) as $post_type ) {
		$num_posts = wp_count_posts( $post_type );
		if ( $num_posts && $num_posts->publish ) {
			if ( 'post' == $post_type ) {
				$text = _n( '%s Post', '%s Posts', $num_posts->publish );
			} else {
				$text = _n( '%s Page', '%s Pages', $num_posts->publish );
			}
			$text = sprintf( $text, number_format_i18n( $num_posts->publish ) );
			$post_type_object = get_post_type_object( $post_type );
			if ( $post_type_object && current_user_can( $post_type_object->cap->edit_posts ) ) {
				printf( '<li class="%1$s-count"><a href="edit.php?post_type=%1$s">%2$s</a></li>', $post_type, $text );
			} else {
				printf( '<li class="%1$s-count"><span>%2$s</span></li>', $post_type, $text );
			}

		}
	}
	// Comments
	$num_comm = wp_count_comments();
	if ( $num_comm && $num_comm->total_comments ) {
		$text = sprintf( _n( '%s Comment', '%s Comments', $num_comm->total_comments ), number_format_i18n( $num_comm->total_comments ) );
		?>
		<li class="comment-count"><a href="edit-comments.php"><?php echo $text; ?></a></li>
		<?php
		if ( $num_comm->moderated ) {
			/* translators: Number of comments in moderation */
			$text = sprintf( _nx( '%s in moderation', '%s in moderation', $num_comm->moderated, 'comments' ), number_format_i18n( $num_comm->moderated ) );
			?>
			<li class="comment-mod-count"><a href="edit-comments.php?comment_status=moderated"><?php echo $text; ?></a></li>
			<?php
		}
	}

	/**
	 * Filter the array of extra elements to list in the 'At a Glance'
	 * dashboard widget.
	 *
	 * Prior to 3.8.0, the widget was named 'Right Now'. Each element
	 * is wrapped in list-item tags on output.
	 *
	 * @since 3.8.0
	 *
	 * @param array $items Array of extra 'At a Glance' widget items.
	 */
	$elements = apply_filters( 'dashboard_glance_items', array() );

	if ( $elements ) {
		echo '<li>' . implode( "</li>\n<li>", $elements ) . "</li>\n";
	}

	?>
	</ul>
	<?php
	update_right_now_message();

	// Check if search engines are asked not to index this site.
	if ( ! is_network_admin() && ! is_user_admin() && current_user_can( 'manage_options' ) && '1' != get_option( 'blog_public' ) ) {

		/**
		 * Filter the link title attribute for the 'Search Engines Discouraged'
		 * message displayed in the 'At a Glance' dashboard widget.
		 *
		 * Prior to 3.8.0, the widget was named 'Right Now'.
		 *
		 * @since 3.0.0
		 *
		 * @param string $title Default attribute text.
		 */
		$title = apply_filters( 'privacy_on_link_title', __( 'Your site is asking search engines not to index its content' ) );

		/**
		 * Filter the link label for the 'Search Engines Discouraged' message
		 * displayed in the 'At a Glance' dashboard widget.
		 *
		 * Prior to 3.8.0, the widget was named 'Right Now'.
		 *
		 * @since 3.0.0
		 *
		 * @param string $content Default text.
		 */
		$content = apply_filters( 'privacy_on_link_text' , __( 'Search Engines Discouraged' ) );

		echo "<p><a href='options-reading.php' title='$title'>$content</a></p>";
	}
	?>
	</div>
	<?php
	/*
	 * activity_box_end has a core action, but only prints content when multisite.
	 * Using an output buffer is the only way to really check if anything's displayed here.
	 */
	ob_start();

	/**
	 * Fires at the end of the 'At a Glance' dashboard widget.
	 *
	 * Prior to 3.8.0, the widget was named 'Right Now'.
	 *
	 * @since 2.5.0
	 */
	do_action( 'rightnow_end' );

	/**
	 * Fires at the end of the 'At a Glance' dashboard widget.
	 *
	 * Prior to 3.8.0, the widget was named 'Right Now'.
	 *
	 * @since 2.0.0
	 */
	do_action( 'activity_box_end' );

	$actions = ob_get_clean();

	if ( !empty( $actions ) ) : ?>
	<div class="sub">
		<?php echo $actions; ?>
	</div>
	<?php endif;
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


	<?php
		/**
		 * Fires in the Network Admin 'Right Now' dashboard widget
		 * just before the user and site search form fields.
		 *
		 * @since MU
		 *
		 * @param null $unused
		 */
		do_action( 'wpmuadminresult', '' );
	?>

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
	/**
	 * Fires at the end of the 'Right Now' widget in the Network Admin dashboard.
	 *
	 * @since MU
	 */
	do_action( 'mu_rightnow_end' );

	/**
	 * Fires at the end of the 'Right Now' widget in the Network Admin dashboard.
	 *
	 * @since MU
	 */
	do_action( 'mu_activity_box_end' );
}

/**
 * The Quick Draft widget display and creation of drafts.
 *
 * @since 3.8.0
 *
 * @param string $error_msg Optional. Error message. Default false.
 */
function wp_dashboard_quick_press( $error_msg = false ) {
	global $post_ID;

	/* Check if a new auto-draft (= no new post_ID) is needed or if the old can be used */
	$last_post_id = (int) get_user_option( 'dashboard_quick_press_last_post_id' ); // Get the last post_ID
	if ( $last_post_id ) {
		$post = get_post( $last_post_id );
		if ( empty( $post ) || $post->post_status != 'auto-draft' ) { // auto-draft doesn't exists anymore
			$post = get_default_post_to_edit( 'post', true );
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
?>

	<form name="post" action="<?php echo esc_url( admin_url( 'post.php' ) ); ?>" method="post" id="quick-press" class="initial-form hide-if-no-js">

		<?php if ( $error_msg ) : ?>
		<div class="error"><?php echo $error_msg; ?></div>
		<?php endif; ?>

		<div class="input-text-wrap" id="title-wrap">
			<label class="screen-reader-text prompt" for="title" id="title-prompt-text">

				<?php
				/** This filter is documented in wp-admin/edit-form-advanced.php */
				echo apply_filters( 'enter_title_here', __( 'Title' ), $post );
				?>
			</label>
			<input type="text" name="post_title" id="title" autocomplete="off" />
		</div>

		<div class="textarea-wrap" id="description-wrap">
			<label class="screen-reader-text prompt" for="content" id="content-prompt-text"><?php _e( 'What&#8217;s on your mind?' ); ?></label>
			<textarea name="content" id="content" class="mceEditor" rows="3" cols="15" autocomplete="off"></textarea>
		</div>

		<p class="submit">
			<input type="hidden" name="action" id="quickpost-action" value="post-quickdraft-save" />
			<input type="hidden" name="post_ID" value="<?php echo $post_ID; ?>" />
			<input type="hidden" name="post_type" value="post" />
			<?php wp_nonce_field( 'add-post' ); ?>
			<?php submit_button( __( 'Save Draft' ), 'primary', 'save', false, array( 'id' => 'save-post' ) ); ?>
			<br class="clear" />
		</p>

	</form>
	<?php
	wp_dashboard_recent_drafts();
}

/**
 * Show recent drafts of the user on the dashboard.
 *
 * @since 2.7.0
 */
function wp_dashboard_recent_drafts( $drafts = false ) {
	if ( ! $drafts ) {
		$query_args = array(
			'post_type'      => 'post',
			'post_status'    => 'draft',
			'author'         => get_current_user_id(),
			'posts_per_page' => 4,
			'orderby'        => 'modified',
			'order'          => 'DESC'
		);
		$drafts = get_posts( $query_args );
		if ( ! $drafts ) {
			return;
 		}
 	}

	echo '<div class="drafts">';
	if ( count( $drafts ) > 3 ) {
		echo '<p class="view-all"><a href="' . esc_url( admin_url( 'edit.php?post_status=draft' ) ) . '">' . _x( 'View all', 'drafts' ) . "</a></p>\n";
 	}
	echo '<h4 class="hide-if-no-js">' . __( 'Drafts' ) . "</h4>\n<ul>";

	$drafts = array_slice( $drafts, 0, 3 );
	foreach ( $drafts as $draft ) {
		$url = get_edit_post_link( $draft->ID );
		$title = _draft_or_post_title( $draft->ID );
		echo "<li>\n";
		echo '<div class="draft-title"><a href="' . esc_url( $url ) . '" title="' . esc_attr( sprintf( __( 'Edit &#8220;%s&#8221;' ), $title ) ) . '">' . esc_html( $title ) . '</a>';
		echo '<time datetime="' . get_the_time( 'c', $draft ) . '">' . get_the_time( get_option( 'date_format' ), $draft ) . '</time></div>';
		if ( $the_content = wp_trim_words( $draft->post_content, 10 ) ) {
			echo '<p>' . $the_content . '</p>';
 		}
		echo "</li>\n";
 	}
	echo "</ul>\n</div>";
}

function _wp_dashboard_recent_comments_row( &$comment, $show_date = true ) {
	$GLOBALS['comment'] =& $comment;

	$comment_post_title = strip_tags(get_the_title( $comment->comment_post_ID ));

	if ( current_user_can( 'edit_post', $comment->comment_post_ID ) ) {
		$comment_post_url = get_edit_post_link( $comment->comment_post_ID );
		$comment_post_link = "<a href='$comment_post_url'>$comment_post_title</a>";
	} else {
		$comment_post_link = $comment_post_title;
	}

	$comment_link = '<a class="comment-link" href="' . esc_url(get_comment_link()) . '">#</a>';

	$actions_string = '';
	if ( current_user_can( 'edit_comment', $comment->comment_ID ) ) {
		// Pre-order it: Approve | Reply | Edit | Spam | Trash.
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
		$actions['reply'] = '<a onclick="window.commentReply && commentReply.open(\''.$comment->comment_ID.'\',\''.$comment->comment_post_ID.'\');return false;" class="vim-r hide-if-no-js" title="'.esc_attr__('Reply to this comment').'" href="#">' . __('Reply') . '</a>';
		$actions['spam'] = "<a href='$spam_url' data-wp-lists='delete:the-comment-list:comment-$comment->comment_ID::spam=1' class='vim-s vim-destructive' title='" . esc_attr__( 'Mark this comment as spam' ) . "'>" . /* translators: mark as spam link */ _x( 'Spam', 'verb' ) . '</a>';
		if ( !EMPTY_TRASH_DAYS )
			$actions['delete'] = "<a href='$delete_url' data-wp-lists='delete:the-comment-list:comment-$comment->comment_ID::trash=1' class='delete vim-d vim-destructive'>" . __('Delete Permanently') . '</a>';
		else
			$actions['trash'] = "<a href='$trash_url' data-wp-lists='delete:the-comment-list:comment-$comment->comment_ID::trash=1' class='delete vim-d vim-destructive' title='" . esc_attr__( 'Move this comment to the trash' ) . "'>" . _x('Trash', 'verb') . '</a>';

		/**
		 * Filter the action links displayed for each comment in the 'Recent Comments'
		 * dashboard widget.
		 *
		 * @since 2.6.0
		 *
		 * @param array  $actions An array of comment actions. Default actions include:
		 *                        'Approve', 'Unapprove', 'Edit', 'Reply', 'Spam',
		 *                        'Delete', and 'Trash'.
		 * @param object $comment The comment object.
		 */
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

			<?php echo get_avatar( $comment, 50, 'mystery' ); ?>

			<?php if ( !$comment->comment_type || 'comment' == $comment->comment_type ) : ?>

			<div class="dashboard-comment-wrap">
			<h4 class="comment-meta">
				<?php printf( /* translators: 1: comment author, 2: post link, 3: notification if the comment is pending */__( 'From %1$s on %2$s%3$s' ),
					'<cite class="comment-author">' . get_comment_author_link() . '</cite>', $comment_post_link.' '.$comment_link, ' <span class="approve">' . __( '[Pending]' ) . '</span>' ); ?>
			</h4>

			<?php
			else :
				switch ( $comment->comment_type ) {
					case 'pingback' :
						$type = __( 'Pingback' );
						break;
					case 'trackback' :
						$type = __( 'Trackback' );
						break;
					default :
						$type = ucwords( $comment->comment_type );
				}
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
 * Callback function for Activity widget.
 *
 * @since 3.8.0
 */
function wp_dashboard_site_activity() {

	echo '<div id="activity-widget">';

	$future_posts = wp_dashboard_recent_posts( array(
		'max'     => 5,
		'status'  => 'future',
		'order'   => 'ASC',
		'title'   => __( 'Publishing Soon' ),
		'id'      => 'future-posts',
	) );
	$recent_posts = wp_dashboard_recent_posts( array(
		'max'     => 5,
		'status'  => 'publish',
		'order'   => 'DESC',
		'title'   => __( 'Recently Published' ),
		'id'      => 'published-posts',
	) );

	$recent_comments = wp_dashboard_recent_comments();

	if ( !$future_posts && !$recent_posts && !$recent_comments ) {
		echo '<div class="no-activity">';
		echo '<p class="smiley"></p>';
		echo '<p>' . __( 'No activity yet!' ) . '</p>';
		echo '</div>';
	}

	echo '</div>';
}

/**
 * Generates Publishing Soon and Recently Published sections.
 *
 * @since 3.8.0
 *
 * @param array $args {
 *     An array of query and display arguments.
 *
 *     @type int    $max     Number of posts to display.
 *     @type string $status  Post status.
 *     @type string $order   Designates ascending ('ASC') or descending ('DESC') order.
 *     @type string $title   Section title.
 *     @type string $id      The container id.
 * }
 * @return bool False if no posts were found. True otherwise.
 */
function wp_dashboard_recent_posts( $args ) {
	$query_args = array(
		'post_type'      => 'post',
		'post_status'    => $args['status'],
		'orderby'        => 'date',
		'order'          => $args['order'],
		'posts_per_page' => intval( $args['max'] ),
		'no_found_rows'  => true,
		'cache_results'  => false,
		'perm'           => ( 'future' === $args['status'] ) ? 'editable' : 'readable',
	);
	$posts = new WP_Query( $query_args );

	if ( $posts->have_posts() ) {

		echo '<div id="' . $args['id'] . '" class="activity-block">';

		echo '<h4>' . $args['title'] . '</h4>';

		echo '<ul>';

		$today    = date( 'Y-m-d', current_time( 'timestamp' ) );
		$tomorrow = date( 'Y-m-d', strtotime( '+1 day', current_time( 'timestamp' ) ) );

		while ( $posts->have_posts() ) {
			$posts->the_post();

			$time = get_the_time( 'U' );
			if ( date( 'Y-m-d', $time ) == $today ) {
				$relative = __( 'Today' );
			} elseif ( date( 'Y-m-d', $time ) == $tomorrow ) {
				$relative = __( 'Tomorrow' );
			} else {
				/* translators: date and time format for recent posts on the dashboard, see http://php.net/date */
				$relative = date_i18n( __( 'M jS' ), $time );
			}

			if ( current_user_can( 'edit_post', get_the_ID() ) ) {
				/* translators: 1: relative date, 2: time, 3: post edit link, 4: post title */
				$format = __( '<span>%1$s, %2$s</span> <a href="%3$s">%4$s</a>' );
				printf( "<li>$format</li>", $relative, get_the_time(), get_edit_post_link(), _draft_or_post_title() );
			} else {
				/* translators: 1: relative date, 2: time, 3: post title */
				$format = __( '<span>%1$s, %2$s</span> %3$s' );
				printf( "<li>$format</li>", $relative, get_the_time(), _draft_or_post_title() );
			}
		}

		echo '</ul>';
		echo '</div>';

	} else {
		return false;
	}

	wp_reset_postdata();

	return true;
}

/**
 * Show Comments section.
 *
 * @since 3.8.0
 *
 * @param int $total_items Optional. Number of comments to query. Default 5.
 * @return bool False if no comments were found. True otherwise.
 */
function wp_dashboard_recent_comments( $total_items = 5 ) {
	// Select all comment types and filter out spam later for better query performance.
	$comments = array();

	$comments_query = array(
		'number' => $total_items * 5,
		'offset' => 0
	);
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
		echo '<div id="latest-comments" class="activity-block">';
		echo '<h4>' . __( 'Comments' ) . '</h4>';

		echo '<div id="the-comment-list" data-wp-lists="list:comment">';
		foreach ( $comments as $comment )
			_wp_dashboard_recent_comments_row( $comment );
		echo '</div>';

		if ( current_user_can('edit_posts') )
			_get_list_table('WP_Comments_List_Table')->views();

		wp_comment_reply( -1, false, 'dashboard', false );
		wp_comment_trashnotice();

		echo '</div>';
	} else {
		return false;
	}
	return true;
}

/**
 * Display generic dashboard RSS widget feed.
 *
 * @since 2.5.0
 *
 * @param string $widget_id
 */
function wp_dashboard_rss_output( $widget_id ) {
	$widgets = get_option( 'dashboard_widget_options' );
	echo '<div class="rss-widget">';
	wp_widget_rss_output( $widgets[ $widget_id ] );
	echo "</div>";
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
		$args = array_slice( func_get_args(), 3 );
		array_unshift( $args, $widget_id, $check_urls );
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

		// Title is optional. If black, fill it if possible.
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
 * WordPress News dashboard widget.
 *
 * @since 2.7.0
 */
function wp_dashboard_primary() {
	$feeds = array(
		'news' => array(

			/**
			 * Filter the primary link URL for the 'WordPress News' dashboard widget.
			 *
			 * @since 2.5.0
			 *
			 * @param string $link The widget's primary link URL.
			 */
			'link' => apply_filters( 'dashboard_primary_link', __( 'http://wordpress.org/news/' ) ),

			/**
			 * Filter the primary feed URL for the 'WordPress News' dashboard widget.
			 *
			 * @since 2.3.0
			 *
			 * @param string $url The widget's primary feed URL.
			 */
			'url' => apply_filters( 'dashboard_primary_feed', __( 'http://wordpress.org/news/feed/' ) ),

			/**
			 * Filter the primary link title for the 'WordPress News' dashboard widget.
			 *
			 * @since 2.3.0
			 *
			 * @param string $title Title attribute for the widget's primary link.
			 */
			'title'        => apply_filters( 'dashboard_primary_title', __( 'WordPress Blog' ) ),
			'items'        => 1,
			'show_summary' => 1,
			'show_author'  => 0,
			'show_date'    => 1,
		),
		'planet' => array(

			/**
			 * Filter the secondary link URL for the 'WordPress News' dashboard widget.
			 *
			 * @since 2.3.0
			 *
			 * @param string $link The widget's secondary link URL.
			 */
			'link' => apply_filters( 'dashboard_secondary_link', __( 'http://planet.wordpress.org/' ) ),

			/**
			 * Filter the secondary feed URL for the 'WordPress News' dashboard widget.
			 *
			 * @since 2.3.0
			 *
			 * @param string $url The widget's secondary feed URL.
			 */
			'url' => apply_filters( 'dashboard_secondary_feed', __( 'http://planet.wordpress.org/feed/' ) ),

			/**
			 * Filter the secondary link title for the 'WordPress News' dashboard widget.
			 *
			 * @since 2.3.0
			 *
			 * @param string $title Title attribute for the widget's secondary link.
			 */
			'title'        => apply_filters( 'dashboard_secondary_title', __( 'Other WordPress News' ) ),
			'items'        => 3,
			'show_summary' => 0,
			'show_author'  => 0,
			'show_date'    => 0,
		)
	);

	if ( ( ! is_multisite() && is_blog_admin() && current_user_can( 'install_plugins' ) ) || ( is_network_admin() && current_user_can( 'manage_network_plugins' ) && current_user_can( 'install_plugins' ) ) ) {
		$feeds['plugins'] = array(
			'link'         => '',
			'url'          => array(
				'popular' => 'http://wordpress.org/plugins/rss/browse/popular/',
			),
			'title'        => '',
			'items'        => 1,
			'show_summary' => 0,
			'show_author'  => 0,
			'show_date'    => 0,
		);
	}

	wp_dashboard_cached_rss_widget( 'dashboard_primary', 'wp_dashboard_primary_output', $feeds );
}

/**
 * Display the WordPress news feeds.
 *
 * @since 3.8.0
 *
 * @param string $widget_id Widget ID.
 * @param array  $feeds     Array of RSS feeds.
 */
function wp_dashboard_primary_output( $widget_id, $feeds ) {
	foreach( $feeds as $type => $args ) {
		$args['type'] = $type;
		echo '<div class="rss-widget">';
		if ( $type === 'plugins' ) {
			wp_dashboard_plugins_output( $args['url'], $args );
		} else {
			wp_widget_rss_output( $args['url'], $args );
		}
		echo "</div>";
	}
}

/**
 * Display plugins text for the WordPress news widget.
 *
 * @since 2.5.0
 */
function wp_dashboard_plugins_output( $rss, $args = array() ) {
	// Plugin feeds plus link to install them
	$popular = fetch_feed( $args['url']['popular'] );

	if ( false === $plugin_slugs = get_transient( 'plugin_slugs' ) ) {
		$plugin_slugs = array_keys( get_plugins() );
		set_transient( 'plugin_slugs', $plugin_slugs, DAY_IN_SECONDS );
	}

	echo '<ul>';

	foreach ( array( $popular ) as $feed ) {
		if ( is_wp_error( $feed ) || ! $feed->get_item_quantity() )
			continue;

		$items = $feed->get_items(0, 5);

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

		$ilink = wp_nonce_url('plugin-install.php?tab=plugin-information&plugin=' . $slug, 'install-plugin_' . $slug) . '&amp;TB_iframe=true&amp;width=600&amp;height=800';
		echo "<li class='dashboard-news-plugin'><span>" . __( 'Popular Plugin' ) . ":</span> <a href='$link' class='dashboard-news-plugin-link'>$title</a>&nbsp;<span>(<a href='$ilink' class='thickbox' title='$title'>" . __( 'Install' ) . "</a>)</span></li>";

		$feed->__destruct();
		unset( $feed );
	}

	echo '</ul>';
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
	if ( !is_multisite() || !current_user_can( 'upload_files' ) || get_site_option( 'upload_space_check_disabled' ) )
		return true;

	$quota = get_space_allowed();
	$used = get_space_used();

	if ( $used > $quota )
		$percentused = '100';
	else
		$percentused = ( $used / $quota ) * 100;
	$used_class = ( $percentused >= 70 ) ? ' warning' : '';
	$used = round( $used, 2 );
	$percentused = number_format( $percentused );

	?>
	<h4 class="mu-storage"><?php _e( 'Storage Space' ); ?></h4>
	<div class="mu-storage">
	<ul>
		<li class="storage-count">
			<?php $text = sprintf(
				/* translators: number of megabytes */
				__( '%s MB Space Allowed' ),
				number_format_i18n( $quota )
			);
			printf(
				'<a href="%1$s" title="%2$s">%3$s</a>',
				esc_url( admin_url( 'upload.php' ) ),
				__( 'Manage Uploads' ),
				$text
			); ?>
		</li><li class="storage-count <?php echo $used_class; ?>">
			<?php $text = sprintf(
				/* translators: 1: number of megabytes, 2: percentage */
				__( '%1$s MB (%2$s%%) Space Used' ),
				number_format_i18n( $used, 2 ),
				$percentused
			);
			printf(
				'<a href="%1$s" title="%2$s" class="musublink">%3$s</a>',
				esc_url( admin_url( 'upload.php' ) ),
				__( 'Manage Uploads' ),
				$text
			); ?>
		</li>
	</ul>
	</div>
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

	/**
	* Filter the notice output for the 'Browse Happy' nag meta box.
	*
	* @since 3.2.0
	*
	* @param string $notice   The notice content.
	* @param array  $response An array containing web browser information.
	*/
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
		<?php if ( current_user_can( 'customize' ) ): ?>
			<h4><?php _e( 'Get Started' ); ?></h4>
			<a class="button button-primary button-hero load-customize hide-if-no-customize" href="<?php echo wp_customize_url(); ?>"><?php _e( 'Customize Your Site' ); ?></a>
		<?php endif; ?>
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
		<?php if ( current_theme_supports( 'widgets' ) || current_theme_supports( 'menus' ) ) : ?>
			<li><div class="welcome-icon welcome-widgets-menus"><?php
				if ( current_theme_supports( 'widgets' ) && current_theme_supports( 'menus' ) ) {
					printf( __( 'Manage <a href="%1$s">widgets</a> or <a href="%2$s">menus</a>' ),
						admin_url( 'widgets.php' ), admin_url( 'nav-menus.php' ) );
				} elseif ( current_theme_supports( 'widgets' ) ) {
					echo '<a href="' . admin_url( 'widgets.php' ) . '">' . __( 'Manage widgets' ) . '</a>';
				} else {
					echo '<a href="' . admin_url( 'nav-menus.php' ) . '">' . __( 'Manage menus' ) . '</a>';
				}
			?></div></li>
		<?php endif; ?>
		<?php if ( current_user_can( 'manage_options' ) ) : ?>
			<li><?php printf( '<a href="%s" class="welcome-icon welcome-comments">' . __( 'Turn comments on or off' ) . '</a>', admin_url( 'options-discussion.php' ) ); ?></li>
		<?php endif; ?>
			<li><?php printf( '<a href="%s" class="welcome-icon welcome-learn-more">' . __( 'Learn more about getting started' ) . '</a>', __( 'http://codex.wordpress.org/First_Steps_With_WordPress' ) ); ?></li>
		</ul>
	</div>
	</div>
	</div>
	<?php
}
