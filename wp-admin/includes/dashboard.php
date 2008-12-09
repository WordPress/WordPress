<?php
/**
 * WordPress Dashboard Widget Administration Panel API
 *
 * @package WordPress
 * @subpackage Administration
 */

/**
 * Registers dashboard widgets.
 *
 * handles POST data, sets up filters.
 *
 * @since unknown
 */
function wp_dashboard_setup() {
	global $wp_registered_widgets, $wp_registered_widget_controls, $wp_dashboard_control_callbacks;
	$wp_dashboard_control_callbacks = array();

	$update = false;
	$widget_options = get_option( 'dashboard_widget_options' );
	if ( !$widget_options || !is_array($widget_options) )
		$widget_options = array();

	/* Register Widgets and Controls */

	// Right Now
	wp_add_dashboard_widget( 'dashboard_right_now', __( 'Right Now' ), 'wp_dashboard_right_now' );

	// Recent Comments Widget
	$recent_comments_title = __( 'Recent Comments' );
	wp_add_dashboard_widget( 'dashboard_recent_comments', $recent_comments_title, 'wp_dashboard_recent_comments' );

	// Incoming Links Widget
	if ( !isset( $widget_options['dashboard_incoming_links'] ) || !isset( $widget_options['dashboard_incoming_links']['home'] ) || $widget_options['dashboard_incoming_links']['home'] != get_option('home') ) {
		$update = true;
		$widget_options['dashboard_incoming_links'] = array(
			'home' => get_option('home'),
			'link' => apply_filters( 'dashboard_incoming_links_link', 'http://blogsearch.google.com/blogsearch?hl=en&scoring=d&partner=wordpress&q=link:' . trailingslashit( get_option('home') ) ),
			'url' => apply_filters( 'dashboard_incoming_links_feed', 'http://blogsearch.google.com/blogsearch_feeds?hl=en&scoring=d&ie=utf-8&num=10&output=rss&partner=wordpress&q=link:' . trailingslashit( get_option('home') ) ),
			'items' => isset($widget_options['dashboard_incoming_links']['items']) ? $widget_options['dashboard_incoming_links']['items'] : 10,
			'show_date' => isset($widget_options['dashboard_incoming_links']['show_date']) ? $widget_options['dashboard_incoming_links']['show_date'] : false
		);
	}
	wp_add_dashboard_widget( 'dashboard_incoming_links', __( 'Incoming Links' ), 'wp_dashboard_incoming_links', 'wp_dashboard_incoming_links_control' );

	// WP Plugins Widget
	if ( current_user_can( 'activate_plugins' ) )
		wp_add_dashboard_widget( 'dashboard_plugins', __( 'Plugins' ), 'wp_dashboard_plugins' );

	// QuickPress Widget
	if ( current_user_can('edit_posts') )
		wp_add_dashboard_widget( 'dashboard_quick_press', __( 'QuickPress' ), 'wp_dashboard_quick_press' );

	// Recent Drafts
	if ( current_user_can('edit_posts') )
		wp_add_dashboard_widget( 'dashboard_recent_drafts', __('Recent Drafts'), 'wp_dashboard_recent_drafts' );

	// Primary feed (Dev Blog) Widget
	if ( !isset( $widget_options['dashboard_primary'] ) ) {
		$update = true;
		$widget_options['dashboard_primary'] = array(
			'link' => apply_filters( 'dashboard_primary_link',  __( 'http://wordpress.org/development/' ) ),
			'url' => apply_filters( 'dashboard_primary_feed',  __( 'http://wordpress.org/development/feed/' ) ),
			'title' => apply_filters( 'dashboard_primary_title', __( 'WordPress Development Blog' ) ),
			'items' => 2,
			'show_summary' => 1,
			'show_author' => 0,
			'show_date' => 1
		);
	}
	wp_add_dashboard_widget( 'dashboard_primary', $widget_options['dashboard_primary']['title'], 'wp_dashboard_primary', 'wp_dashboard_primary_control' );

	// Secondary Feed (Planet) Widget
	if ( !isset( $widget_options['dashboard_secondary'] ) ) {
		$update = true;
		$widget_options['dashboard_secondary'] = array(
			'link' => apply_filters( 'dashboard_secondary_link',  __( 'http://planet.wordpress.org/' ) ),
			'url' => apply_filters( 'dashboard_secondary_feed',  __( 'http://planet.wordpress.org/feed/' ) ),
			'title' => apply_filters( 'dashboard_secondary_title', __( 'Other WordPress News' ) ),
			'items' => 5
		);
	}
	wp_add_dashboard_widget( 'dashboard_secondary', $widget_options['dashboard_secondary']['title'], 'wp_dashboard_secondary', 'wp_dashboard_secondary_control' );

	// Hook to register new widgets
	do_action( 'wp_dashboard_setup' );

	// Filter widget order
	$dashboard_widgets = apply_filters( 'wp_dashboard_widgets', array() );

	foreach ( $dashboard_widgets as $widget_id ) {
		$name = empty( $wp_registered_widgets[$widget_id]['all_link'] ) ? $wp_registered_widgets[$widget_id]['name'] : $wp_registered_widgets[$widget_id]['name'] . " <a href='{$wp_registered_widgets[$widget_id]['all_link']}' class='edit-box open-box'>" . __('View all') . '</a>';
		wp_add_dashboard_widget( $widget_id, $name, $wp_registered_widgets[$widget_id]['callback'], $wp_registered_widget_controls[$widget_id]['callback'] );
	}

	if ( 'POST' == $_SERVER['REQUEST_METHOD'] && isset($_POST['widget_id']) ) {
		ob_start(); // hack - but the same hack wp-admin/widgets.php uses
		wp_dashboard_trigger_widget_control( $_POST['widget_id'] );
		ob_end_clean();
		wp_redirect( remove_query_arg( 'edit' ) );
		exit;
	}

	if ( $update )
		update_option( 'dashboard_widget_options', $widget_options );

	do_action('do_meta_boxes', 'dashboard', 'normal', '');
	do_action('do_meta_boxes', 'dashboard', 'side', '');
}

function wp_add_dashboard_widget( $widget_id, $widget_name, $callback, $control_callback = null ) {
	global $wp_dashboard_control_callbacks;
	if ( $control_callback && current_user_can( 'edit_dashboard' ) && is_callable( $control_callback ) ) {
		$wp_dashboard_control_callbacks[$widget_id] = $control_callback;
		if ( isset( $_GET['edit'] ) && $widget_id == $_GET['edit'] ) {
			list($url) = explode( '#', add_query_arg( 'edit', false ), 2 );
			$widget_name .= ' <span class="postbox-title-action"><a href="' . clean_url( $url ) . '">' . __( 'Cancel' ) . '</a></span>';
			add_meta_box( $widget_id, $widget_name, '_wp_dashboard_control_callback', 'dashboard', 'normal', 'core' );
			return;
		}
		list($url) = explode( '#', add_query_arg( 'edit', $widget_id ), 2 );
		$widget_name .= ' <span class="postbox-title-action"><a href="' . clean_url( "$url#$widget_id" ) . '" class="edit-box open-box">' . __( 'Configure' ) . '</a></span>';
	}
	$side_widgets = array('dashboard_quick_press', 'dashboard_recent_drafts', 'dashboard_primary', 'dashboard_secondary');
	$location = 'normal';
	if ( in_array($widget_id, $side_widgets) )
		$location = 'side';
	add_meta_box( $widget_id, $widget_name , $callback, 'dashboard', $location, 'core' );
}

function _wp_dashboard_control_callback( $dashboard, $meta_box ) {
	echo '<form action="" method="post" class="dashboard-widget-control-form">';
	wp_dashboard_trigger_widget_control( $meta_box['id'] );
	echo "<p class='submit'><input type='hidden' name='widget_id' value='$meta_box[id]' /><input type='submit' value='" . __( 'Submit' ) . "' /></p>";

	echo '</form>';
}

/**
 * Displays the dashboard.
 *
 * @since unknown
 */
function wp_dashboard() {
	echo "<div id='dashboard-widgets' class='metabox-holder'>\n\n";

	echo "<div id='side-info-column' class='inner-sidebar'>\n\n";
	$class = do_meta_boxes( 'dashboard', 'side', '' ) ? ' class="has-sidebar"' : '';
	echo "</div>\n\n";

	echo "<div id='post-body'$class>\n\n";
	echo "<div id='dashboard-widgets-main-content' class='has-sidebar-content'>\n\n";
	do_meta_boxes( 'dashboard', 'normal', '' );
	echo "</div>\n\n";
	echo "</div>\n\n";

	echo "<form style='display: none' method='get' action=''>\n<p>\n";
	wp_nonce_field( 'closedpostboxes', 'closedpostboxesnonce', false );
	wp_nonce_field( 'meta-box-order', 'meta-box-order-nonce', false );
	echo "</p>\n</form>\n";

	echo "</div>";
}

/* Dashboard Widgets */

function wp_dashboard_right_now() {
	$num_posts = wp_count_posts( 'post' );
	$num_pages = wp_count_posts( 'page' );

	$num_cats  = wp_count_terms('category');

	$num_tags = wp_count_terms('post_tag');

	$num_comm = wp_count_comments( );

	echo "\n\t".'<p class="sub">' . __('At a Glance') . '</p>';
	echo "\n\t".'<div class="table">'."\n\t".'<table>';
	echo "\n\t".'<tr class="first">';

	// Posts
	$num = number_format_i18n( $num_posts->publish );
	if ( current_user_can( 'edit_posts' ) )
		$text = "<a href='edit.php'>$num</a>";
	else
		$text = $num;
	echo '<td class="first b b-posts">' . $text . '</td>';
	echo '<td class="t posts">' . __ngettext( 'Post', 'Posts', intval($num_posts->publish) ) . '</td>';
	/* TODO: Show status breakdown on hover
	if ( $can_edit_pages && !empty($num_pages->publish) ) { // how many pages is not exposed in feeds.  Don't show if !current_user_can
		$post_type_texts[] = '<a href="edit-pages.php">'.sprintf( __ngettext( '%s page', '%s pages', $num_pages->publish ), number_format_i18n( $num_pages->publish ) ).'</a>';
	}
	if ( $can_edit_posts && !empty($num_posts->draft) ) {
		$post_type_texts[] = '<a href="edit.php?post_status=draft">'.sprintf( __ngettext( '%s draft', '%s drafts', $num_posts->draft ), number_format_i18n( $num_posts->draft ) ).'</a>';
	}
	if ( $can_edit_posts && !empty($num_posts->future) ) {
		$post_type_texts[] = '<a href="edit.php?post_status=future">'.sprintf( __ngettext( '%s scheduled post', '%s scheduled posts', $num_posts->future ), number_format_i18n( $num_posts->future ) ).'</a>';
	}
	if ( current_user_can('publish_posts') && !empty($num_posts->pending) ) {
		$pending_text = sprintf( __ngettext( 'There is <a href="%1$s">%2$s post</a> pending your review.', 'There are <a href="%1$s">%2$s posts</a> pending your review.', $num_posts->pending ), 'edit.php?post_status=pending', number_format_i18n( $num_posts->pending ) );
	} else {
		$pending_text = '';
	}
	*/

	// Total Comments
	$num = number_format_i18n($num_comm->total_comments);
	if ( current_user_can( 'moderate_comments' ) )
		$num = "<a href='edit-comments.php'>$num</a>";
	echo '<td class="b b-comments">'.$num.'</td>';
	echo '<td class="last t comments">' . __ngettext( 'Comment', 'Comments', $num_comm->total_comments ) . '</td>';

	echo '</tr><tr>';

	// Pages
	$num = number_format_i18n( $num_pages->publish );
	if ( current_user_can( 'edit_pages' ) )
		$num = "<a href='edit-pages.php'>$num</a>";
	echo '<td class="first b b_pages">'.$num.'</td>';
	echo '<td class="t pages">' . __ngettext( 'Page', 'Pages', $num_pages->publish ) . '</td>';

	// Approved Comments
	$num = number_format_i18n($num_comm->approved);
	if ( current_user_can( 'moderate_comments' ) )
		$num = "<a href='edit-comments.php?comment_status=approved'>$num</a>";
	echo '<td class="b b_approved">'.$num.'</td>';
	echo '<td class="last t approved">' . __ngettext( 'Approved', 'Approved', $num_comm->approved ) . '</td>';

	echo "</tr>\n\t<tr>";

	// Categories
	$num = number_format_i18n( $num_cats );
	if ( current_user_can( 'manage_categories' ) )
		$num = "<a href='categories.php'>$num</a>";
	echo '<td class="first b b-cats">'.$num.'</td>';
	echo '<td class="t cats">' . __ngettext( 'Category', 'Categories', $num_cats ) . '</td>';

	// Pending Comments
	$num = number_format_i18n($num_comm->moderated);
	if ( current_user_can( 'moderate_comments' ) )
		$num = "<a href='edit-comments.php?comment_status=moderated'><span class='pending-count'>$num</span></a>";
	echo '<td class="b b-waiting">'.$num.'</td>';
	echo '<td class="last t waiting">' . __ngettext( 'Pending', 'Pending', $num_comm->moderated ) . '</td>';

	echo "</tr>\n\t<tr>";

	// Tags
	$num = number_format_i18n( $num_tags );
	if ( current_user_can( 'manage_categories' ) )
		$num = "<a href='edit-tags.php'>$num</a>";
	echo '<td class="first b b-tags">'.$num.'</td>';
	echo '<td class="t tags">' . __ngettext( 'Tag', 'Tags', $num_tags ) . '</td>';

	// Spam Comments
	$num = number_format_i18n($num_comm->spam);
	if ( current_user_can( 'moderate_comments' ) )
		$num = "<a href='edit-comments.php?comment_status=spam'><span class='spam-count'>$num</span></a>";
	echo '<td class="b b-spam">'.$num.'</td>';
	echo '<td class="last t spam">' . __ngettext( 'Spam', 'Spam', $num_comm->spam ) . '</td>';

	echo "</tr>";
	do_action('right_now_table_end');
	echo "\n\t</table>\n\t</div>";

	echo "\n\t".'<div class="versions">';
	$ct = current_theme_info();
	$sidebars_widgets = wp_get_sidebars_widgets();
	$num_widgets = array_reduce( $sidebars_widgets, create_function( '$prev, $curr', 'return $prev+count($curr);' ), 0 );
	$num = number_format_i18n( $num_widgets );

	echo "\n\t<p>";
	if ( current_user_can( 'switch_themes' ) ) {
		echo '<a href="themes.php" class="button rbutton">' . __('Change Theme') . '</a>';
		printf(__ngettext('Theme <span class="b"><a href="themes.php">%1$s</a></span> with <span class="b"><a href="widgets.php">%2$s Widget</a></span>', 'Theme <span class="b"><a href="themes.php">%1$s</a></span> with <span class="b"><a href="widgets.php">%2$s Widgets</a></span>', $num_widgets), $ct->title, $num);
	} else {
		printf(__ngettext('Theme <span class="b">%1$s</span> with <span class="b">%2$s Widget</span>', 'Theme <span class="b">%1$s</span> with <span class="b">%2$s Widgets</span>', $num_widgets), $ct->title, $num);
	}

	echo '</p>';

	update_right_now_message();

	echo "\n\t".'</div>';
	do_action( 'rightnow_end' );
	do_action( 'activity_box_end' );
}

function wp_dashboard_quick_press() {
	$drafts = false;
	if ( 'post' === strtolower( $_SERVER['REQUEST_METHOD'] ) && isset( $_POST['action'] ) && 0 === strpos( $_POST['action'], 'post-quickpress' ) && (int) $_POST['post_ID'] ) {
		$view = get_permalink( $_POST['post_ID'] );
		$edit = clean_url( get_edit_post_link( $_POST['post_ID'] ) );
		if ( 'post-quickpress-publish' == $_POST['action'] ) {
			if ( current_user_can('publish_posts') )
				printf( '<div class="message"><p>' . __( 'Post Published. <a href="%s">View post</a> | <a href="%s">Edit post</a>' ) . '</p></div>', clean_url( $view ), $edit );
			else
				printf( '<div class="message"><p>' . __( 'Post submitted. <a href="%s">Preview post</a> | <a href="%s">Edit post</a>' ) . '</p></div>', clean_url( add_query_arg( 'preview', 1, $view ) ), $edit );
		} else {
			printf( '<div class="message"><p>' . __( 'Draft Saved. <a href="%s">Preview post</a> | <a href="%s">Edit post</a>' ) . '</p></div>', clean_url( add_query_arg( 'preview', 1, $view ) ), $edit );
			$drafts_query = new WP_Query( array(
				'post_type' => 'post',
				'what_to_show' => 'posts',
				'post_status' => 'draft',
				'author' => $GLOBALS['current_user']->ID,
				'posts_per_page' => 1,
				'orderby' => 'modified',
				'order' => 'DESC'
			) );

			if ( $drafts_query->posts )
				$drafts =& $drafts_query->posts;
		}
		printf('<p class="textright">' . __('You can also try %s, easy blogging from anywhere on the Web.') . '</p>', '<a href="tools.php">' . __('Press This') . '</a>' );
		$_REQUEST = array(); // hack for get_default_post_to_edit()
	}

	$post = get_default_post_to_edit();
?>

	<form name="post" action="<?php echo clean_url( admin_url( 'post.php' ) ); ?>" method="post" id="quick-press">
		<h4 id="quick-post-title"><label for="title"><?php _e('Title') ?></label></h4>
		<div class="input-text-wrap">
			<input type="text" name="post_title" id="title" tabindex="1" autocomplete="off" value="<?php echo attribute_escape( $post->post_title ); ?>" />
		</div>

		<?php if ( current_user_can( 'upload_files' ) ) : ?>
		<div id="media-buttons" class="hide-if-no-js">
			<?php do_action( 'media_buttons' ); ?>
		</div>
		<?php endif; ?>

		<h4 id="content-label"><label for="content"><?php _e('Content') ?></label></h4>
		<div class="textarea-wrap">
			<textarea name="content" id="content" class="mceEditor" rows="3" cols="15" tabindex="2"><?php echo $post->post_content; ?></textarea>
		</div>

		<script type="text/javascript">edCanvas = document.getElementById('content');edInsertContent = null;</script>

		<h4><label for="tags-input"><?php _e('Tags') ?></label></h4>
		<div class="input-text-wrap">
			<input type="text" name="tags_input" id="tags-input" tabindex="3" value="<?php echo get_tags_to_edit( $post->ID ); ?>" />
		</div>

		<p class="submit">
			<input type="hidden" name="action" id="quickpost-action" value="post-quickpress-save" />
			<input type="hidden" name="quickpress_post_ID" value="<?php echo (int) $post->ID; ?>" />
			<?php wp_nonce_field('add-post'); ?>
			<input type="submit" name="save" id="save-post" class="button" tabindex="4" value="<?php _e('Save Draft'); ?>" />
			<input type="reset" value="<?php _e( 'Cancel' ); ?>" class="cancel" />
			<?php if ( current_user_can('publish_posts') ) { ?>
			<input type="submit" name="publish" id="publish" accesskey="p" tabindex="5" class="button-primary" value="<?php _e('Publish'); ?>" />
			<?php } else { ?>
			<input type="submit" name="publish" id="publish" accesskey="p" tabindex="5" class="button-primary" value="<?php _e('Submit for Review'); ?>" />
			<?php } ?>
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
			'what_to_show' => 'posts',
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
			$item = "<h4><a href='$url' title='" . sprintf( __( 'Edit "%s"' ), attribute_escape( $title ) ) . "'>$title</a> <abbr title='" . get_the_time(__('Y/m/d g:i:s A'), $draft) . "'>" . get_the_time( get_option( 'date_format' ), $draft ) . '</abbr></h4>';
			if ( $the_content = preg_split( '#\s#', strip_tags( $draft->post_content ), 11, PREG_SPLIT_NO_EMPTY ) )
				$item .= '<p>' . join( ' ', array_slice( $the_content, 0, 10 ) ) . ( 10 < count( $the_content ) ? '&hellip;' : '' ) . '</p>';
			$list[] = $item;
		}
?>
	<ul>
		<li><?php echo join( "</li>\n<li>", $list ); ?></li>
	</ul>
	<p class="textright"><a href="edit.php?post_status=draft" class="button"><?php _e('View all'); ?></a></p>
<?php
	} else {
		_e('There are no drafts at the moment');
	}
}

/**
 * Display recent comments dashboard widget content.
 *
 * @since unknown
 */
function wp_dashboard_recent_comments() {
	global $wpdb;

	if ( current_user_can('edit_posts') )
		$allowed_states = array('0', '1');
	else
		$allowed_states = array('1');

	// Select all comment types and filter out spam later for better query performance.
	$comments = array();
	$start = 0;

	while ( count( $comments ) < 5 && $possible = $wpdb->get_results( "SELECT * FROM $wpdb->comments ORDER BY comment_date_gmt DESC LIMIT $start, 50" ) ) {

		foreach ( $possible as $comment ) {
			if ( count( $comments ) >= 5 )
				break;
			if ( in_array( $comment->comment_approved, $allowed_states ) )
				$comments[] = $comment;
		}

		$start = $start + 50;
	}

	if ( $comments ) :
?>

		<div id="the-comment-list" class="list:comment">
<?php
		foreach ( $comments as $comment )
			_wp_dashboard_recent_comments_row( $comment );
?>

		</div>

<?php
		if ( current_user_can('edit_posts') ) { ?>
			<p class="textright"><a href="edit-comments.php" class="button"><?php _e('View all'); ?></a></p>
<?php	}

		wp_comment_reply( -1, false, 'dashboard', false );

	else :
?>

	<p><?php _e( 'No comments yet.' ); ?></p>

<?php
	endif; // $comments;
}

function _wp_dashboard_recent_comments_row( &$comment, $show_date = true ) {
	$GLOBALS['comment'] =& $comment;

	$comment_post_url = get_edit_post_link( $comment->comment_post_ID );
	$comment_post_title = get_the_title( $comment->comment_post_ID );
	$comment_post_link = "<a href='$comment_post_url'>$comment_post_title</a>";
	$comment_link = '<a class="comment-link" href="' . get_comment_link() . '">#</a>';

	$delete_url = clean_url( wp_nonce_url( "comment.php?action=deletecomment&p=$comment->comment_post_ID&c=$comment->comment_ID", "delete-comment_$comment->comment_ID" ) );
	$approve_url = clean_url( wp_nonce_url( "comment.php?action=approvecomment&p=$comment->comment_post_ID&c=$comment->comment_ID", "approve-comment_$comment->comment_ID" ) );
	$unapprove_url = clean_url( wp_nonce_url( "comment.php?action=unapprovecomment&p=$comment->comment_post_ID&c=$comment->comment_ID", "unapprove-comment_$comment->comment_ID" ) );
	$spam_url = clean_url( wp_nonce_url( "comment.php?action=deletecomment&dt=spam&p=$comment->comment_post_ID&c=$comment->comment_ID", "delete-comment_$comment->comment_ID" ) );

	$actions = array();

	$actions_string = '';
	if ( current_user_can('edit_post', $comment->comment_post_ID) ) {
		$actions['approve'] = "<a href='$approve_url' class='dim:the-comment-list:comment-$comment->comment_ID:unapproved:e7e7d3:e7e7d3:new=approved vim-a' title='" . __( 'Approve this comment' ) . "'>" . __( 'Approve' ) . '</a>';
		$actions['unapprove'] = "<a href='$unapprove_url' class='dim:the-comment-list:comment-$comment->comment_ID:unapproved:e7e7d3:e7e7d3:new=unapproved vim-u' title='" . __( 'Unapprove this comment' ) . "'>" . __( 'Unapprove' ) . '</a>';
		$actions['edit'] = "<a href='comment.php?action=editcomment&amp;c={$comment->comment_ID}' title='" . __('Edit comment') . "'>". __('Edit') . '</a>';
		//$actions['quickedit'] = '<a onclick="commentReply.open(\''.$comment->comment_ID.'\',\''.$comment->comment_post_ID.'\',\'edit\');return false;" class="vim-q" title="'.__('Quick Edit').'" href="#">' . __('Quick&nbsp;Edit') . '</a>';
		$actions['reply'] = '<a onclick="commentReply.open(\''.$comment->comment_ID.'\',\''.$comment->comment_post_ID.'\');return false;" class="vim-r hide-if-no-js" title="'.__('Reply to this comment').'" href="#">' . __('Reply') . '</a>';
		$actions['spam'] = "<a href='$spam_url' class='delete:the-comment-list:comment-$comment->comment_ID::spam=1 vim-s vim-destructive' title='" . __( 'Mark this comment as spam' ) . "'>" . _c( 'Spam|verb' ) . '</a>';
		$actions['delete'] = "<a href='$delete_url' class='delete:the-comment-list:comment-$comment->comment_ID delete vim-d vim-destructive'>" . __('Delete') . '</a>';

		$actions = apply_filters( 'comment_row_actions', $actions, $comment );

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

			<?php echo get_avatar( $comment, 50 ); ?>
			<h4 class="comment-meta"><?php printf( __( 'From %1$s on %2$s%3$s' ), '<cite class="comment-author">' . get_comment_author_link() . '</cite>', $comment_post_link." ".$comment_link, ' <span class="approve">' . __( '[Pending]' ) . '</span>' ); ?></h4>

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
				$type = wp_specialchars( $type );
			?>

			<h4 class="comment-meta"><?php printf( __( '%1$s on %2$s' ), "<strong>$type</strong>", $comment_post_link ); ?></h4>
			<p class="comment-author"><?php comment_author_link(); ?></p>

			<?php endif; // comment_type ?>
			<blockquote><p><?php comment_excerpt(); ?></p></blockquote>
			<p class="row-actions"><?php echo $actions_string; ?></p>

			<div id="inline-<?php echo $comment->comment_ID; ?>" class="hidden">
				<textarea class="comment" rows="3" cols="10"><?php echo $comment->comment_content; ?></textarea>
				<div class="author-email"><?php echo attribute_escape( $comment->comment_author_email ); ?></div>
				<div class="author"><?php echo attribute_escape( $comment->comment_author ); ?></div>
				<div class="author-url"><?php echo attribute_escape( $comment->comment_author_url ); ?></div>
				<div class="comment_status"><?php echo $comment->comment_approved; ?></div>
			</div>

		</div>
<?php
}

function wp_dashboard_incoming_links() {
	wp_dashboard_cached_rss_widget( 'dashboard_incoming_links', 'wp_dashboard_incoming_links_output' );
}

/**
 * Display incoming links dashboard widget content.
 *
 * @since unknown
 */
function wp_dashboard_incoming_links_output() {
	$widgets = get_option( 'dashboard_widget_options' );
	@extract( @$widgets['dashboard_incoming_links'], EXTR_SKIP );
	$rss = @fetch_rss( $url );
	if ( isset($rss->items) && 0 < count($rss->items) )  {

		echo "<ul>\n";

		$rss->items = array_slice($rss->items, 0, $items);
		foreach ( $rss->items as $item ) {
			$publisher = '';
			$site_link = '';
			$link = '';
			$content = '';
			$date = '';
			$link = clean_url( strip_tags( $item['link'] ) );

			if ( isset( $item['author_uri'] ) )
				$site_link = clean_url( strip_tags( $item['author_uri'] ) );

			if ( !$publisher = wp_specialchars( strip_tags( isset($item['dc']['publisher']) ? $item['dc']['publisher'] : $item['author_name'] ) ) )
				$publisher = __( 'Somebody' );
			if ( $site_link )
				$publisher = "<a href='$site_link'>$publisher</a>";
			else
				$publisher = "<strong>$publisher</strong>";

			if ( isset($item['description']) )
				$content = $item['description'];
			elseif ( isset($item['summary']) )
				$content = $item['summary'];
			elseif ( isset($item['atom_content']) )
				$content = $item['atom_content'];
			else
				$content = __( 'something' );
			$content = wp_html_excerpt($content, 50) . ' ...';
			if ( $link )
				$text = _c( '%1$s linked here <a href="%2$s">saying</a>, "%3$s"|feed_display' );
			else
				$text = _c( '%1$s linked here saying, "%3$s"|feed_display' );

			if ( $show_date ) {
				if ( $show_author || $show_summary )
					$text .= _c( ' on %4$s|feed_display' );
				$date = wp_specialchars( strip_tags( isset($item['pubdate']) ? $item['pubdate'] : $item['published'] ) );
				$date = strtotime( $date );
				$date = gmdate( get_option( 'date_format' ), $date );
			}

			echo "\t<li>" . sprintf( _c( "$text|feed_display" ), $publisher, $link, $content, $date ) . "</li>\n";
		}

		echo "</ul>\n";

	} else {
		echo '<p>' . __('This dashboard widget queries <a href="http://blogsearch.google.com/">Google Blog Search</a> so that when another blog links to your site it will show up here. It has found no incoming links&hellip; yet. It&#8217;s okay &#8212; there is no rush.') . "</p>\n";
	}
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
 * {@internal Missing Short Description}}
 *
 * @since unknown
 *
 * @param int $widget_id
 */
function wp_dashboard_rss_output( $widget_id ) {
	$widgets = get_option( 'dashboard_widget_options' );
	echo "<div class='rss-widget'>";
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
 * @since unknown
 *
 * @return unknown
 */
function wp_dashboard_secondary_output() {
	$widgets = get_option( 'dashboard_widget_options' );
	@extract( @$widgets['dashboard_secondary'], EXTR_SKIP );
	$rss = @fetch_rss( $url );

	if ( !isset($rss->items) || 0 == count($rss->items) )
		return false;

	$rss->items = array_slice($rss->items, 0, $items);

	if ( 'http://planet.wordpress.org/' == $rss->channel['link'] ) {
		foreach ( array_keys($rss->items) as $i ) {
			list($site, $description) = explode( ':', wp_specialchars($rss->items[$i]['title']), 2 );
			$rss->items[$i]['dc']['creator'] = trim($site);
			$rss->items[$i]['title'] = trim($description);
		}
	}

	echo "<div class='rss-widget'>";
	wp_widget_rss_output( $rss, $widgets['dashboard_secondary'] );
	echo "</div>";
}

function wp_dashboard_plugins() {
	wp_dashboard_cached_rss_widget( 'dashboard_plugins', 'wp_dashboard_plugins_output', array(
		'http://wordpress.org/extend/plugins/rss/browse/popular/',
		'http://wordpress.org/extend/plugins/rss/browse/new/',
		'http://wordpress.org/extend/plugins/rss/browse/updated/'
	) );
}

/**
 * Display plugins most popular, newest plugins, and recently updated widget text.
 *
 * @since unknown
 */
function wp_dashboard_plugins_output() {
	$popular = @fetch_rss( 'http://wordpress.org/extend/plugins/rss/browse/popular/' );
	$new     = @fetch_rss( 'http://wordpress.org/extend/plugins/rss/browse/new/' );
	$updated = @fetch_rss( 'http://wordpress.org/extend/plugins/rss/browse/updated/' );

	foreach ( array( 'popular' => __('Most Popular'), 'new' => __('Newest Plugins'), 'updated' => __('Recently Updated') ) as $feed => $label ) {
		if ( !isset($$feed->items) || 0 == count($$feed->items) )
			continue;

		$$feed->items = array_slice($$feed->items, 0, 5);
		$item_key = array_rand($$feed->items);

		// Eliminate some common badly formed plugin descriptions
		while ( ( null !== $item_key = array_rand($$feed->items) ) && false !== strpos( $$feed->items[$item_key]['description'], 'Plugin Name:' ) )
			unset($$feed->items[$item_key]);

		if ( !isset($$feed->items[$item_key]) )
			continue;

		$item = $$feed->items[$item_key];

		// current bbPress feed item titles are: user on "topic title"
		if ( preg_match( '/"(.*)"/s', $item['title'], $matches ) )
			$title = $matches[1];
		else // but let's make it forward compatible if things change
			$title = $item['title'];
		$title = wp_specialchars( $title );

		$description = wp_specialchars( strip_tags(html_entity_decode($item['description'], ENT_QUOTES)) );

		list($link, $frag) = explode( '#', $item['link'] );

		$link = clean_url($link);
		if( preg_match('|/([^/]+?)/?$|', $link, $matches) )
			$slug = $matches[1];
		else
			$slug = '';

		$ilink = wp_nonce_url('plugin-install.php?tab=plugin-information&plugin=' . $slug, 'install-plugin_' . $slug) .
							'&amp;TB_iframe=true&amp;width=600&amp;height=800';

		echo "<h4>$label</h4>\n";
		echo "<h5><a href='$link'>$title</a></h5>&nbsp;<span>(<a href='$ilink' class='thickbox' title='$title'>" . __( 'Install' ) . "</a>)</span>\n";
		echo "<p>$description</p>\n";
	}
}

/**
 * Checks to see if all of the feed url in $check_urls are cached.
 *
 * If $check_urls is empty, look for the rss feed url found in the dashboard
 * widget optios of $widget_id. If cached, call $callback, a function that
 * echoes out output for this widget. If not cache, echo a "Loading..." stub
 * which is later replaced by AJAX call (see top of /wp-admin/index.php)
 *
 * @since unknown
 *
 * @param int $widget_id
 * @param callback $callback
 * @param array $check_urls RSS feeds
 * @return bool False on failure. True on success.
 */
function wp_dashboard_cached_rss_widget( $widget_id, $callback, $check_urls = array() ) {
	$loading = '<p class="widget-loading">' . __( 'Loading&#8230;' ) . '</p>';

	if ( empty($check_urls) ) {
		$widgets = get_option( 'dashboard_widget_options' );
		if ( empty($widgets[$widget_id]['url']) ) {
			echo $loading;
			return false;
		}
		$check_urls = array( $widgets[$widget_id]['url'] );
	}


	require_once( ABSPATH . WPINC . '/rss.php' );
	init(); // initialize rss constants

	$cache = new RSSCache( MAGPIE_CACHE_DIR, MAGPIE_CACHE_AGE );

	foreach ( $check_urls as $check_url ) {
		$status = $cache->check_cache( $check_url );
		if ( 'HIT' !== $status ) {
			echo $loading;
			return false;
		}
	}

	if ( $callback && is_callable( $callback ) ) {
		$args = array_slice( func_get_args(), 2 );
		array_unshift( $args, $widget_id );
		call_user_func_array( $callback, $args );
	}

	return true;
}

/* Dashboard Widgets Controls */

// Calls widget_control callback
/**
 * Calls widget control callback.
 *
 * @since unknown
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
 * @since unknown
 *
 * @param string widget_id
 * @param array form_inputs
 */
function wp_dashboard_rss_control( $widget_id, $form_inputs = array() ) {
	if ( !$widget_options = get_option( 'dashboard_widget_options' ) )
		$widget_options = array();

	if ( !isset($widget_options[$widget_id]) )
		$widget_options[$widget_id] = array();

	$number = 1; // Hack to use wp_widget_rss_form()
	$widget_options[$widget_id]['number'] = $number;

	if ( 'POST' == $_SERVER['REQUEST_METHOD'] && isset($_POST['widget-rss'][$number]) ) {
		$_POST['widget-rss'][$number] = stripslashes_deep( $_POST['widget-rss'][$number] );
		$widget_options[$widget_id] = wp_widget_rss_process( $_POST['widget-rss'][$number] );
		// title is optional.  If black, fill it if possible
		if ( !$widget_options[$widget_id]['title'] && isset($_POST['widget-rss'][$number]['title']) ) {
			require_once(ABSPATH . WPINC . '/rss.php');
			$rss = fetch_rss($widget_options[$widget_id]['url']);
			$widget_options[$widget_id]['title'] = htmlentities(strip_tags($rss->channel['title']));
		}
		update_option( 'dashboard_widget_options', $widget_options );
	}

	wp_widget_rss_form( $widget_options[$widget_id], $form_inputs );
}

/**
 * Empty function usable by plugins to output empty dashboard widget (to be populated later by JS).
 */
function wp_dashboard_empty() {}

?>
