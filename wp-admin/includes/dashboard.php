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

	// Recent Comments Widget
	wp_add_dashboard_widget( 'dashboard_recent_comments', __( 'Recent Comments' ), 'wp_dashboard_recent_comments' );

	// QuickPress Widget
	wp_add_dashboard_widget( 'dashboard_quick_press', __( 'QuickPress' ), 'wp_dashboard_quick_press', 'wp_dashboard_empty_control' );

	// Recent Drafts
	wp_add_dashboard_widget( 'dashboard_recent_drafts', __( 'Recent Drafts' ), 'wp_dashboard_recent_drafts' );

	// Incoming Links Widget
	if ( !isset( $widget_options['dashboard_incoming_links'] ) || !isset( $widget_options['dashboard_incoming_links']['home'] ) || $widget_options['dashboard_incoming_links']['home'] != get_option('home') ) {
		$update = true;
		$widget_options['dashboard_incoming_links'] = array(
			'home' => get_option('home'),
			'link' => apply_filters( 'dashboard_incoming_links_link', 'http://blogsearch.google.com/blogsearch?hl=en&scoring=d&partner=wordpress&q=link:' . trailingslashit( get_option('home') ) ),
			'url' => apply_filters( 'dashboard_incoming_links_feed', 'http://blogsearch.google.com/blogsearch_feeds?hl=en&scoring=d&ie=utf-8&num=10&output=rss&partner=wordpress&q=link:' . trailingslashit( get_option('home') ) ),
			'items' => 5,
			'show_date' => 0
		);
	}
	wp_add_dashboard_widget( 'dashboard_incoming_links', __( 'Incoming Links' ), 'wp_dashboard_incoming_links', 'wp_dashboard_incoming_links_control' );

	// WP Plugins Widget
	if ( current_user_can( 'activate_plugins' ) )
		wp_add_dashboard_widget( 'dashboard_plugins', __( 'Plugins' ), 'wp_dashboard_plugins' );

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
			'items' => 15
		);
	}
	wp_add_dashboard_widget( 'dashboard_secondary', $widget_options['dashboard_secondary']['title'], 'wp_dashboard_secondary', 'wp_dashboard_secondary_control' );

	// Hook to register new widgets
	do_action( 'wp_dashboard_setup' );

	// Filter widget order
	$dashboard_widgets = apply_filters( 'wp_dashboard_widgets', array() );

	if ( 'POST' == $_SERVER['REQUEST_METHOD'] && isset($_POST['widget_id']) ) {
		ob_start(); // hack - but the same hack wp-admin/widgets.php uses
		wp_dashboard_trigger_widget_control( $_POST['widget_id'] );
		ob_end_clean();
		wp_redirect( remove_query_arg( 'edit' ) );
		exit;
	}

	if ( $update )
		update_option( 'dashboard_widget_options', $widget_options );

	foreach ( $dashboard_widgets as $widget_id )
		wp_add_dashboard_widget( $widget_id, $wp_registered_widgets[$widget_id]['name'], $wp_registered_widgets[$widget_id]['callback'], $wp_registered_widget_controls[$widget_id]['callback'] );
}

function wp_add_dashboard_widget( $widget_id, $widget_name, $callback, $control_callback = null ) {
	global $wp_dashboard_control_callbacks;
	if ( $control_callback && current_user_can( 'edit_dashboard' ) && is_callable( $control_callback ) ) {
		$wp_dashboard_control_callbacks[$widget_id] = $control_callback;
		if ( isset( $_GET['edit'] ) && $widget_id == $_GET['edit'] ) {
			list($url) = explode( '#', add_query_arg( 'edit', false ), 2 );
			$widget_name .= ' <a href="' . clean_url( $url ) . '">' . __( 'Cancel' ) . '</a>';
			add_meta_box( $widget_id, $widget_name, '_wp_dashboard_control_callback', 'dashboard', 'normal', 'core' );
			return;
		}
		list($url) = explode( '#', add_query_arg( 'edit', $widget_id ), 2 );
		$widget_name .= ' <a href="' . clean_url( "$url#$widget_id" ) . '" class="edit-box open-box">' . __( 'Edit' ) . '</a>';
	}
	add_meta_box( $widget_id, $widget_name , $callback, 'dashboard', 'normal', 'core' );
}

function _wp_dashboard_control_callback( $dashboard, $meta_box ) {
	echo '<form action="" method="post">';
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

function wp_dashboard_quick_press( $dashboard, $meta_box ) {
	$drafts = false;
	if ( 'post' === strtolower( $_SERVER['REQUEST_METHOD'] ) && isset( $_POST['action'] ) && 0 === strpos( $_POST['action'], 'post-quickpress' ) ) {
		$view = get_permalink( $_POST['post_ID'] );
		$edit = clean_url( get_edit_post_link( $_POST['post_ID'] ) );
		if ( 'post-quickpress-publish' == $_POST['action'] ) {
			printf( __( 'Post Published. <a href="%s">View post</a> | <a href="%s">Edit post</a>' ), clean_url( $view ), $edit );
		} else {
			printf( __( 'Draft Saved. <a href="%s">Preview post</a> | <a href="%s">Edit post</a>' ), clean_url( add_query_arg( 'preview', 1, $view ) ), $edit );
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
		$_REQUEST = array(); // hack for get_default_post_to_edit()
	}

	$post = get_default_post_to_edit();
?>

	<form name="post" action="<?php echo clean_url( admin_url( 'post.php' ) ); ?>" method="post" id="quick-press">
		<h4 id="quick-post-title"><label for="title"><?php _e('Title') ?></label></h4>
		<div class="input-text-wrap">
			<input type="text" name="post_title" id="title" autocomplete="off" value="<?php echo attribute_escape( $post->post_title ); ?>" />
		</div>

		<h4><label for="quickpress-content"><?php _e('Post') ?></label></h4>
		<div class="textarea-wrap">
			<textarea name="content" id="quickpress-content" class="mceEditor" rows="3" cols="15"><?php echo $post->post_content; ?></textarea>
		</div>

		<h4><label for="tags-input"><?php _e('Tags') ?></label></h4>
		<div class="input-text-wrap">
			<input type="text" name="tags_input" id="tags-input" value="<?php echo get_tags_to_edit( $post->ID ); ?>" />
		</div>
		<p class='field-tip'><?php _e('Separate tags with commas'); ?></p>

		<p class="submit">
			<input type="hidden" name="action" id="quickpost-action" value="post-quickpress-save" />
			<input type="hidden" name="quickpress_post_ID" value="<?php echo (int) $post->ID; ?>" />
			<?php wp_nonce_field('add-post'); ?>
			<input type="submit" name="save" id="save-post" class="button alignleft" value="<?php _e('Save Draft'); ?>" />
			<input type="submit" name="publish" id="publish" accesskey="p" class="button button-highlighted alignright" value="<?php _e('Publish'); ?>" />
			<br class="clear" />
		</p>

	</form>

<?php
	if ( $drafts )
		wp_dashboard_recent_drafts( $drafts );
}

function wp_dashboard_recent_drafts( $drafts = false ) {
	global $post;
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

	if ( $drafts && is_array( $drafts ) ) :
		$list = array();
		foreach ( $drafts as $post ) {
			$url = get_edit_post_link( $draft->ID );
			$title = _draft_or_post_title( $draft->ID );
			$list[] = '<abbr title="' . get_the_time(__('Y/m/d g:i:s A')) . '">' . get_the_time( get_option( 'date_format' ) ) . "</abbr> <a href='$url' title='" . sprintf( __( 'Edit "%s"' ), attribute_escape( $title ) ) . "'>$title</a>";
		}
?>
	<ul>
		<li><?php echo join( "</li>\n<li>", $list ); ?></li>
	</ul>

<?php

	endif; // drafts
}

/**
 * Display recent comments dashboard widget content.
 *
 * @since unknown
 */
function wp_dashboard_recent_comments() {
	list($comments, $total) = _wp_get_comment_list( '', false, 0, 5 );

	if ( $comments ) :
?>

		<p class="view-all"><a href="edit-comments.php"><?php _e( 'View All Comments' ); ?></a></p>
		<div id="the-comment-list" class="list:comment">

<?php
		foreach ( $comments as $comment )
			_wp_dashboard_recent_comments_row( $comment );
?>

		</div>

<?php
		wp_comment_reply( -1, false, 'dashboard', false );

	else :
?>

	<p><?php _e( 'No comments yet.' ); ?></p>

<?php
	endif; // $comments;
}

function _wp_dashboard_recent_comments_row( &$comment, $show_date = true ) {
	static $date = false;
	static $today = false;
	static $yesterday = false;

	$GLOBALS['comment'] =& $comment;

	if ( $show_date ) {
		if ( !$today )
			$today = gmdate( get_option( 'date_format' ), time() + get_option( 'gmt_offset' ) );
		if ( !$yesterday )
			$yesterday = gmdate( get_option( 'date_format' ), strtotime( 'yesterday' ) + get_option( 'gmt_offset' ) );
		$wordy_dates = array( $today => __( 'Today' ), $yesterday => __( 'Yesterday' ) );
	
		$comment_date = gmdate( get_option( 'date_format' ), strtotime( $comment->comment_date ) + get_option( 'gmt_offset' ) );
		if ( $comment_date != $date ) {
			$date = $comment_date;
			echo '<h4>' . ( isset( $wordy_dates[$date] ) ? $wordy_dates[$date] : $date ) . ":</h4>\n";
		}
	}

	$comment_post_url = get_edit_post_link( $comment->comment_post_ID );
	$comment_post_title = get_the_title( $comment->comment_post_ID );
	$comment_post_link = "<a href='$comment_post_url'>$comment_post_title</a>";
	$comment_link = '<a class="comment-link" href="' . get_comment_link() . '">#</a>';

	$delete_url = clean_url( wp_nonce_url( "comment.php?action=deletecomment&p=$comment->comment_post_ID&c=$comment->comment_ID", "delete-comment_$comment->comment_ID" ) );
	$approve_url = clean_url( wp_nonce_url( "comment.php?action=approvecomment&p=$comment->comment_post_ID&c=$comment->comment_ID", "approve-comment_$comment->comment_ID" ) );
	$unapprove_url = clean_url( wp_nonce_url( "comment.php?action=unapprovecomment&p=$comment->comment_post_ID&c=$comment->comment_ID", "unapprove-comment_$comment->comment_ID" ) );
	$spam_url = clean_url( wp_nonce_url( "comment.php?action=deletecomment&dt=spam&p=$comment->comment_post_ID&c=$comment->comment_ID", "delete-comment_$comment->comment_ID" ) );

	$actions = array();

	if ( current_user_can('edit_post', $comment->comment_post_ID) ) {
		$actions['approve'] = "<a href='$approve_url' class='dim:the-comment-list:comment-$comment->comment_ID:unapproved:e7e7d3:e7e7d3:new=approved vim-a' title='" . __( 'Approve this comment' ) . "'>" . __( 'Approve' ) . '</a>';
		$actions['unapprove'] = "<a href='$unapprove_url' class='dim:the-comment-list:comment-$comment->comment_ID:unapproved:e7e7d3:e7e7d3:new=unapproved vim-u' title='" . __( 'Unapprove this comment' ) . "'>" . __( 'Unapprove' ) . '</a>';
		$actions['edit'] = "<a href='comment.php?action=editcomment&amp;c={$comment->comment_ID}' title='" . __('Edit comment') . "'>". __('Edit') . '</a>';
		$actions['spam'] = "<a href='$spam_url' class='delete:the-comment-list:comment-$comment->comment_ID::spam=1 vim-s vim-destructive' title='" . __( 'Mark this comment as spam' ) . "'>" . __( 'Spam' ) . '</a>';
		$actions['delete'] = "<a href='$delete_url' class='delete:the-comment-list:comment-$comment->comment_ID delete vim-d vim-destructive'>" . __('Delete') . '</a>';
		$actions['reply'] = '<a onclick="commentReply.open(\''.$comment->comment_ID.'\',\''.$comment->comment_post_ID.'\');return false;" class="vim-r" title="'.__('Reply to this comment').'" href="#">' . __('Reply') . '</a>';

		$actions = apply_filters( 'comment_row_actions', $actions, $comment );

		$i = 0;
		$actions_string = '';
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

			<?php echo get_avatar( $comment, 32 ); ?>
			<span class="comment-meta"><?php printf( __( '%1$s in response to %2$s:' ), '<cite>' . get_comment_author() . '</cite>', $comment_post_link ); ?></span>

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

			<span class="comment-meta"><?php printf( __( '%3$s on %2$s: %1$s' ), '<cite>' . get_comment_author() . '</cite>', $comment_post_link, "<strong>$type</strong>" ); ?></span>

			<?php endif; // comment_type ?>
			<blockquote><p><?php comment_excerpt(); ?></p></blockquote>
			<p class="comment-actions"><?php echo $actions_string; ?></p>
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
	wp_widget_rss_output( $widgets[$widget_id] );
}

function wp_dashboard_secondary() {
	wp_dashboard_cached_rss_widget( 'dashboard_secondary', 'wp_dashboard_secondary_output' );
}

function wp_dashboard_secondary_control() {
	wp_dashboard_rss_control( 'dashboard_secondary', array( 'show_summary' => false, 'show_author' => false, 'show_date' => false ) );
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

	echo "<ul id='planetnews'>\n";

	$rss->items = array_slice($rss->items, 0, $items);
	foreach ($rss->items as $item ) {
		$title = wp_specialchars($item['title']);
		list($author,$post) = explode( ':', $title, 2 );
		$link = clean_url($item['link']);

		echo "\t<li><a href='$link'><span class='post'>$post</span><span class='hidden'> - </span><cite>$author</cite></a></li>\n";
	}

	echo "</ul>\n<br class='clear' />\n";
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

// Temp
function wp_dashboard_empty_control() {
	echo "This feature isn't enabled in this prototype.";
}

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

?>
