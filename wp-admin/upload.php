<?php
/**
 * Media Library administration panel.
 *
 * @package WordPress
 * @subpackage Administration
 */

/** WordPress Administration Bootstrap */
require_once('admin.php');
add_thickbox();
wp_enqueue_script( 'media-upload' );
wp_enqueue_script( 'wp-ajax-response' );
wp_enqueue_script( 'jquery-ui-draggable' );
wp_enqueue_script( 'jquery-ui-resizable' );

if (!current_user_can('upload_files'))
	wp_die(__('You do not have permission to upload files.'));

if ( isset($_GET['find_detached'] ) ) {
	check_admin_referer('bulk-media');

	if ( ! current_user_can('edit_posts') )
		wp_die( __('You are not allowed to scan for lost attachments.') );

	$all_posts = $wpdb->get_col("SELECT ID FROM $wpdb->posts WHERE post_type = 'post' OR post_type = 'page'");
	$all_att = $wpdb->get_results("SELECT ID, post_parent FROM $wpdb->posts WHERE post_type = 'attachment'");

	$lost = array();
	foreach ( (array) $all_att as $att ) {
		if ( $att->post_parent > 0 && ! in_array($att->post_parent, $all_posts) )
			$lost[] = $att->ID;
	}
	$_GET['detached'] = 1;

} elseif ( isset($_GET['found_post_id']) && isset($_GET['media']) ) {
	check_admin_referer('bulk-media');

	if ( ! ( $parent_id = (int) $_GET['found_post_id'] ) )
		return;

	$parent = &get_post($parent_id);
	if ( !current_user_can('edit_post', $parent_id) )
		wp_die( __('You are not allowed to edit this post.') );

	$attach = array();
	foreach( (array) $_GET['media'] as $att_id ) {
		$att_id = (int) $att_id;

		if ( !current_user_can('edit_post', $att_id) )
			continue;

		$attach[] = $att_id;
	}

	if ( ! empty($attach) ) {
		$attach = implode(',', $attach);
		$attached = $wpdb->query( $wpdb->prepare("UPDATE $wpdb->posts SET post_parent = %d WHERE post_type = 'attachment' AND ID IN ($attach)", $parent_id) );

		$message = sprintf( __ngettext('Added %1$s attachment to <strong>%2$s</strong>', 'Added %1$s attachments to <strong>%2$s</strong>', $attached, apply_filters( "the_title", $parent->post_title ) ) , $attached, apply_filters( "the_title", $parent->post_title ) );
	}

	$_GET['detached'] = 1;

} elseif ( isset($_GET['action']) && isset($_GET['media']) && isset($_GET['doaction']) ) {
	check_admin_referer('bulk-media');
	if ( $_GET['action'] == 'delete' ) {
		foreach( (array) $_GET['media'] as $post_id_del ) {
			$post_del = & get_post($post_id_del);

			if ( !current_user_can('delete_post', $post_id_del) )
				wp_die( __('You are not allowed to delete this post.') );

			if ( $post_del->post_type == 'attachment' )
				if ( ! wp_delete_attachment($post_id_del) )
					wp_die( __('Error in deleting...') );
		}

		$location = 'upload.php';
		if ( $referer = wp_get_referer() ) {
			if ( false !== strpos($referer, 'upload.php') )
				$location = $referer;
		}

		$location = add_query_arg('message', 2, $location);
		$location = remove_query_arg('posted', $location);
		wp_redirect($location);
		exit;
	}
} elseif ( !empty($_GET['_wp_http_referer']) ) {
	wp_redirect(remove_query_arg(array('_wp_http_referer', '_wpnonce'), stripslashes($_SERVER['REQUEST_URI'])));
	exit;
}

$title = __('Media Library');
$parent_file = 'edit.php';
wp_enqueue_script( 'admin-forms' );
wp_enqueue_script('media');

if ( ! isset( $_GET['paged'] ) || $_GET['paged'] < 1 )
	$_GET['paged'] = 1;

if ( isset($_GET['detached']) ) {

	if ( isset($lost) ) {
		$start = ( $_GET['paged'] - 1 ) * 50;
		$page_links_total = ceil(count($lost) / 50);
		$lost = implode(',', $lost);

		$orphans = $wpdb->get_results( "SELECT * FROM $wpdb->posts WHERE post_type = 'attachment' AND ID IN ($lost) LIMIT $start, 50" );
	} else {
		$start = ( $_GET['paged'] - 1 ) * 25;
		$orphans = $wpdb->get_results( "SELECT SQL_CALC_FOUND_ROWS * FROM $wpdb->posts WHERE post_type = 'attachment' AND post_parent < 1 LIMIT $start, 25" );
		$page_links_total = ceil($wpdb->get_var( "SELECT FOUND_ROWS()" ) / 25);
	}

	$post_mime_types = array(
				'image' => array(__('Images'), __('Manage Images'), __ngettext_noop('Image (%s)', 'Images (%s)')),
				'audio' => array(__('Audio'), __('Manage Audio'), __ngettext_noop('Audio (%s)', 'Audio (%s)')),
				'video' => array(__('Video'), __('Manage Video'), __ngettext_noop('Video (%s)', 'Video (%s)')),
			);
	$post_mime_types = apply_filters('post_mime_types', $post_mime_types);

	$avail_post_mime_types = get_available_post_mime_types('attachment');

	if ( isset($_GET['post_mime_type']) && !array_intersect( (array) $_GET['post_mime_type'], array_keys($post_mime_types) ) )
		unset($_GET['post_mime_type']);

} else {
	list($post_mime_types, $avail_post_mime_types) = wp_edit_attachments_query();
}

if ( is_singular() ) {
	wp_enqueue_script( 'admin-comments' );
	wp_enqueue_script( 'jquery-table-hotkeys' );
}

require_once('admin-header.php'); ?>

<form class="search-form" action="" method="get">
	<p id="media-search" class="search-box" >
		<label class="hidden" for="media-search-input"><?php _e( 'Search Media' ); ?></label>
		<input type="text" id="media-search-input" class="search-input" name="s" value="<?php the_search_query(); ?>" />
		<input type="submit" value="<?php _e( 'Search Media' ); ?>" class="button" />
	</p>
</form>

<div class="wrap">

<form id="adv-settings" action="" method="get">
<div id="show-settings"><a href="#edit_settings" id="show-settings-link" class="hide-if-no-js"><?php _e('Advanced Options') ?></a>
<a href="#edit_settings" id="hide-settings-link" class="hide-if-js hide-if-no-js"><?php _e('Hide Options') ?></a></div>

<div id="edit-settings" class="hide-if-js hide-if-no-js">
<div id="edit-settings-wrap">
<h5><?php _e('Show on screen') ?></h5>
<div class="metabox-prefs">
<?php manage_columns_prefs('media') ?>
<br class="clear" />
</div></div>
<?php wp_nonce_field( 'hiddencolumns', 'hiddencolumnsnonce', false ); ?>
</div></form>

<h2><?php
if ( isset($_GET['detached']) ) {
	_e('Unattached Media');
} elseif ( is_singular() ) {
	printf( __('Comments on %s'), apply_filters("the_title", $post->post_title) );
} else {
	$post_mime_type_label = _c('Media|manage media header');
	if ( isset($_GET['post_mime_type']) && in_array( $_GET['post_mime_type'], array_keys($post_mime_types) ) )
        $post_mime_type_label = $post_mime_types[$_GET['post_mime_type']][1];
   	//TODO: Unreachable code: $post_listing_pageable is undefined, Similar code in edit.php
	//if ( $post_listing_pageable && !is_archive() && !is_search() )
	//	$h2_noun = is_paged() ? sprintf(__( 'Previous %s' ), $post_mime_type_label) : sprintf(__('Latest %s'), $post_mime_type_label);
	//else
		$h2_noun = $post_mime_type_label;
	// Use $_GET instead of is_ since they can override each other
	$h2_author = '';
	$_GET['author'] = isset( $_GET['author'] ) ? (int) $_GET['author'] : 0;
	if ( $_GET['author'] != 0 ) {
		if ( $_GET['author'] == '-' . $user_ID ) { // author exclusion
			$h2_author = ' ' . __('by other authors');
		} else {
			$author_user = get_userdata( get_query_var( 'author' ) );
			$h2_author = ' ' . sprintf(__('by %s'), wp_specialchars( $author_user->display_name ));
		}
	}
	$h2_search = isset($_GET['s'])   && $_GET['s']   ? ' ' . sprintf(__('matching &#8220;%s&#8221;'), wp_specialchars( get_search_query() ) ) : '';
	$h2_cat    = isset($_GET['cat']) && $_GET['cat'] ? ' ' . sprintf( __('in &#8220;%s&#8221;'), single_cat_title('', false) ) : '';
	$h2_tag    = isset($_GET['tag']) && $_GET['tag'] ? ' ' . sprintf( __('tagged with &#8220;%s&#8221;'), single_tag_title('', false) ) : '';
	$h2_month  = isset($_GET['m'])   && $_GET['m']   ? ' ' . sprintf( __('during %s'), single_month_title(' ', false) ) : '';
	printf( _c( '%1$s%2$s%3$s%4$s%5$s%6$s (<a href="%7$s" class="thickbox">Add New</a>)|You can reorder these: 1: Posts, 2: by {s}, 3: matching {s}, 4: in {s}, 5: tagged with {s}, 6: during {s}' ), $h2_noun, $h2_author, $h2_search, $h2_cat, $h2_tag, $h2_month, 'media-upload.php?library=false&TB_iframe=true' );
}
?></h2>

<form id="posts-filter" action="" method="get">
<ul class="subsubsub">
<?php
$type_links = array();
$_num_posts = (array) wp_count_attachments();
$matches = wp_match_mime_types(array_keys($post_mime_types), array_keys($_num_posts));
foreach ( $matches as $type => $reals )
	foreach ( $reals as $real )
		$num_posts[$type] += $_num_posts[$real];

$class = empty($_GET['post_mime_type']) && ! isset($_GET['detached']) ? ' class="current"' : '';
$type_links[] = "<li><a href=\"upload.php\"$class>".__('All Types')."</a>";
foreach ( $post_mime_types as $mime_type => $label ) {
	$class = '';

	if ( !wp_match_mime_types($mime_type, $avail_post_mime_types) )
		continue;

	if ( wp_match_mime_types($mime_type, $_GET['post_mime_type']) )
		$class = ' class="current"';

	$type_links[] = "<li><a href=\"upload.php?post_mime_type=$mime_type\"$class>" .
	sprintf(__ngettext($label[2][0], $label[2][1], $num_posts[$mime_type]), number_format_i18n( $num_posts[$mime_type] )) . '</a>';
}
$class = isset($_GET['detached']) ? ' class="current"' : '';
$type_links[] = '<li><a href="upload.php?detached=1"' . $class . '>' . __('Unattached') . '</a>';

echo implode(' | </li>', $type_links) . '</li>';
unset($type_links);
?>
</ul>

<?php
if ( isset($_GET['posted']) && $_GET['posted'] ) : $_GET['posted'] = (int) $_GET['posted']; ?>
<div id="message" class="updated fade"><p><strong><?php _e('Your media has been saved.'); ?></strong> <a href="<?php echo get_permalink( $_GET['posted'] ); ?>"><?php _e('View media'); ?></a> | <a href="<?php echo get_edit_post_link( $_GET['posted'] ); ?>"><?php _e('Edit media'); ?></a></p></div>
<?php $_SERVER['REQUEST_URI'] = remove_query_arg(array('posted'), $_SERVER['REQUEST_URI']);
endif;

$messages[1] = __('Media updated.');
$messages[2] = __('Media deleted.');

if ( isset($_GET['message']) && (int) $_GET['message'] )
	$message = $messages[$_GET['message']];

if ( $message ) { ?>
<div id="message" class="updated fade"><p><?php echo $message; ?></p></div>
<?php $_SERVER['REQUEST_URI'] = remove_query_arg(array('message'), $_SERVER['REQUEST_URI']);
}
?>

<?php do_action('restrict_manage_posts'); ?>

<div class="tablenav">

<?php
if ( ! isset($page_links_total) )
	$page_links_total =  $wp_query->max_num_pages;

$page_links = paginate_links( array(
	'base' => add_query_arg( 'paged', '%#%' ),
	'format' => '',
	'total' => $page_links_total,
	'current' => $_GET['paged']
));

if ( $page_links )
	echo "<div class='tablenav-pages'>$page_links</div>";
?>

<div class="alignleft">
<select name="action" id="select-action">
<option value="" selected><?php _e('Actions'); ?></option>
<option value="delete"><?php _e('Delete'); ?></option>
<?php if ( isset($orphans) ) { ?>
<option value="attach"><?php _e('Attach to a post'); ?></option>
<?php } ?>
</select>
<input type="submit" id="submit" value="<?php _e('Apply'); ?>" name="doaction" class="button-secondary action" />
<?php wp_nonce_field('bulk-media'); ?>
<?php

if ( ! is_singular() && ! isset($_GET['detached']) ) {
	$arc_query = "SELECT DISTINCT YEAR(post_date) AS yyear, MONTH(post_date) AS mmonth FROM $wpdb->posts WHERE post_type = 'attachment' ORDER BY post_date DESC";

	$arc_result = $wpdb->get_results( $arc_query );

	$month_count = count($arc_result);

	if ( $month_count && !( 1 == $month_count && 0 == $arc_result[0]->mmonth ) ) : ?>
<select name='m'>
<option<?php selected( @$_GET['m'], 0 ); ?> value='0'><?php _e('Show all dates'); ?></option>
<?php
foreach ($arc_result as $arc_row) {
	if ( $arc_row->yyear == 0 )
		continue;
	$arc_row->mmonth = zeroise( $arc_row->mmonth, 2 );

	if ( $arc_row->yyear . $arc_row->mmonth == $_GET['m'] )
		$default = ' selected="selected"';
	else
		$default = '';

	echo "<option$default value='$arc_row->yyear$arc_row->mmonth'>";
	echo $wp_locale->get_month($arc_row->mmonth) . " $arc_row->yyear";
	echo "</option>\n";
}
?>
</select>
<?php endif; // month_count ?>

<input type="submit" id="post-query-submit" value="<?php _e('Filter'); ?>" class="button-secondary" />

<?php } // ! is_singular ?>

<?php if ( isset($_GET['detached']) ) { ?>
	<input type="submit" id="find_detached" name="find_detached" value="<?php _e('Scan for lost attachments'); ?>" class="button-secondary" />
<?php } ?>

</div>

<br class="clear" />
</div>

<br class="clear" />

<?php if ( isset($orphans) ) { ?>
<table class="widefat">
<thead>
<tr>
	<th scope="col" class="check-column"><input type="checkbox" /></th>
	<th scope="col"></th>
	<th scope="col"><?php echo _c('Media|media column header'); ?></th>
	<th scope="col"><?php echo _c('Date Added|media column header'); ?></th>
</tr>
</thead>

<tbody id="the-list" class="list:post">
<?php
	if ( $orphans ) {
		foreach ( $orphans as $post ) {
			$class = 'alternate' == $class ? '' : 'alternate';
			$att_title = empty($post->post_title) ? __('(no title)') : wp_specialchars( apply_filters('the_title', $post->post_title) );
?>
	<tr id='post-<?php echo $post->ID; ?>' class='<?php echo $class; ?>' valign="top">
		<th scope="row" class="check-column"><input type="checkbox" name="media[]" value="<?php echo $post->ID; ?>" /></th>

		<td class="media-icon"><?php
		if ( $thumb = wp_get_attachment_image( $post->ID, array(80, 60), true ) ) { ?>
			<a href="media.php?action=edit&amp;attachment_id=<?php echo $post->ID; ?>" title="<?php echo attribute_escape(sprintf(__('Edit "%s"'), $att_title)); ?>"><?php echo $thumb; ?></a>
<?php	} ?></td>

		<td><strong><a href="<?php echo get_edit_post_link( $post->ID ); ?>" title="<?php echo attribute_escape(sprintf(__('Edit "%s"'), $att_title)); ?>"><?php echo $att_title; ?></a></strong><br />
		<?php echo strtoupper(preg_replace('/^.*?\.(\w+)$/', '$1', get_attached_file($post->ID))); ?>

		<p>
		<?php
		$actions = array();
		$actions['edit'] = '<a href="' . get_edit_post_link($post->ID, true) . '">' . __('Edit') . '</a>';
		$actions['delete'] = "<a class='submitdelete' href='" . wp_nonce_url("post.php?action=delete&amp;post=$post->ID", 'delete-post_' . $post->ID) . "' onclick=\"if ( confirm('" . js_escape(sprintf( ('draft' == $post->post_status) ? __("You are about to delete this attachment '%s'\n  'Cancel' to stop, 'OK' to delete.") : __("You are about to delete this attachment '%s'\n  'Cancel' to stop, 'OK' to delete."), $post->post_title )) . "') ) { return true;}return false;\">" . __('Delete') . "</a>";
		$actions['view'] = '<a href="' . get_permalink($post->ID) . '" title="' . attribute_escape(sprintf(__('View "%s"'), $title)) . '" rel="permalink">' . __('View') . '</a>';
		$actions['attach'] = '<a href="#the-list" onclick="findPosts.open(\'media[]\',\''.$post->ID.'\');return false;">'.__('Attach').'</a>';
		$action_count = count($actions);
		$i = 0;
		foreach ( $actions as $action => $link ) {
			++$i;
			( $i == $action_count ) ? $sep = '' : $sep = ' | ';
			echo "<span class='$action'>$link$sep</span>";
		} ?>
		</p></td>

<?php	if ( '0000-00-00 00:00:00' == $post->post_date && 'date' == $column_name ) {
			$t_time = $h_time = __('Unpublished');
		} else {
			$t_time = get_the_time(__('Y/m/d g:i:s A'));
			$m_time = $post->post_date;
			$time = get_post_time( 'G', true );
			if ( ( abs($t_diff = time() - $time) ) < 86400 ) {
				if ( $t_diff < 0 )
					$h_time = sprintf( __('%s from now'), human_time_diff( $time ) );
				else
					$h_time = sprintf( __('%s ago'), human_time_diff( $time ) );
			} else {
				$h_time = mysql2date(__('Y/m/d'), $m_time);
			}
		} ?>
		<td><?php echo $h_time ?></td>
	</tr>
<?php	}

	} else { ?>
	<tr><td colspan="5"><?php _e('No posts found.') ?></td></tr>
<?php } ?>
</tbody>
</table>
<script type="text/javascript">
	(function($){
		$('#submit').click(function(e) {
			if ( 'attach' == $('#select-action').val() ) {
				e.preventDefault();
				findPosts.open();
			}
		});
	})(jQuery);
</script>
<?php find_posts_div();

} else {
	include( 'edit-attachment-rows.php' );
} ?>

</form>

<div id="ajax-response"></div>

<div class="tablenav">

<?php
if ( $page_links )
	echo "<div class='tablenav-pages'>$page_links</div>";
?>

</div>

<br class="clear" />

<?php

if ( 1 == count($posts) && is_singular() ) :

	$comments = $wpdb->get_results( $wpdb->prepare("SELECT * FROM $wpdb->comments WHERE comment_post_ID = %d AND comment_approved != 'spam' ORDER BY comment_date", $id) );
	if ( $comments ) :
		// Make sure comments, post, and post_author are cached
		update_comment_cache($comments);
		$post = get_post($id);
		$authordata = get_userdata($post->post_author);
	?>

<br class="clear" />

<table class="widefat" style="margin-top: .5em">
<thead>
  <tr>
    <th scope="col"><?php _e('Comment') ?></th>
    <th scope="col"><?php _e('Date') ?></th>
    <th scope="col"><?php _e('Actions') ?></th>
  </tr>
</thead>
<tbody id="the-comment-list" class="list:comment">
<?php
        foreach ($comments as $comment)
                _wp_comment_row( $comment->comment_ID, 'detail', false, false );
?>
</tbody>
</table>

<?php
wp_comment_reply();
endif; // comments
endif; // posts;

?>

</div>

<?php

include('admin-footer.php');
?>
