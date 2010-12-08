<?php
/**
 * Media Library administration panel.
 *
 * @package WordPress
 * @subpackage Administration
 */

/** WordPress Administration Bootstrap */
require_once('./admin.php');
wp_enqueue_script( 'wp-ajax-response' );
wp_enqueue_script( 'jquery-ui-draggable' );

if ( !current_user_can('upload_files') )
	wp_die(__('You do not have permission to upload files.'));

if ( isset($_GET['find_detached']) ) {
	check_admin_referer('bulk-media');

	if ( !current_user_can('edit_posts') )
		wp_die( __('You are not allowed to scan for lost attachments.') );

	$lost = $wpdb->get_col("SELECT ID FROM $wpdb->posts WHERE post_type = 'attachment' AND post_parent > '0' and post_parent NOT IN ( SELECT ID FROM $wpdb->posts WHERE post_type NOT IN ('attachment', '" . join("', '", get_post_types( array( 'public' => false ) ) ) . "') )");

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
		clean_attachment_cache($att_id);
	}

	if ( ! empty($attach) ) {
		$attach = implode(',', $attach);
		$attached = $wpdb->query( $wpdb->prepare("UPDATE $wpdb->posts SET post_parent = %d WHERE post_type = 'attachment' AND ID IN ($attach)", $parent_id) );
	}

	if ( isset($attached) ) {
		$location = 'upload.php';
		if ( $referer = wp_get_referer() ) {
			if ( false !== strpos($referer, 'upload.php') )
				$location = $referer;
		}

		$location = add_query_arg( array( 'attached' => $attached ) , $location );
		wp_redirect($location);
		exit;
	}

} elseif ( isset($_GET['doaction']) || isset($_GET['doaction2']) || isset($_GET['delete_all']) || isset($_GET['delete_all2']) ) {
	check_admin_referer('bulk-media');

	if ( isset($_GET['delete_all']) || isset($_GET['delete_all2']) ) {
		$post_ids = $wpdb->get_col( "SELECT ID FROM $wpdb->posts WHERE post_type='attachment' AND post_status = 'trash'" );
		$doaction = 'delete';
	} elseif ( ( $_GET['action'] != -1 || $_GET['action2'] != -1 ) && ( isset($_GET['media']) || isset($_GET['ids']) ) ) {
		$post_ids = isset($_GET['media']) ? $_GET['media'] : explode(',', $_GET['ids']);
		$doaction = ($_GET['action'] != -1) ? $_GET['action'] : $_GET['action2'];
	} else {
		wp_redirect($_SERVER['HTTP_REFERER']);
	}

	$location = 'upload.php';
	if ( $referer = wp_get_referer() ) {
		if ( false !== strpos($referer, 'upload.php') )
			$location = remove_query_arg( array('trashed', 'untrashed', 'deleted', 'message', 'ids', 'posted'), $referer );
	}

	switch ( $doaction ) {
		case 'trash':
			foreach( (array) $post_ids as $post_id ) {
				if ( !current_user_can('delete_post', $post_id) )
					wp_die( __('You are not allowed to move this post to the trash.') );

				if ( !wp_trash_post($post_id) )
					wp_die( __('Error in moving to trash...') );
			}
			$location = add_query_arg( array( 'trashed' => count($post_ids), 'ids' => join(',', $post_ids) ), $location );
			break;
		case 'untrash':
			foreach( (array) $post_ids as $post_id ) {
				if ( !current_user_can('delete_post', $post_id) )
					wp_die( __('You are not allowed to move this post out of the trash.') );

				if ( !wp_untrash_post($post_id) )
					wp_die( __('Error in restoring from trash...') );
			}
			$location = add_query_arg('untrashed', count($post_ids), $location);
			break;
		case 'delete':
			foreach( (array) $post_ids as $post_id_del ) {
				if ( !current_user_can('delete_post', $post_id_del) )
					wp_die( __('You are not allowed to delete this post.') );

				if ( !wp_delete_attachment($post_id_del) )
					wp_die( __('Error in deleting...') );
			}
			$location = add_query_arg('deleted', count($post_ids), $location);
			break;
	}

	wp_redirect($location);
	exit;
} elseif ( ! empty($_GET['_wp_http_referer']) ) {
	 wp_redirect( remove_query_arg( array('_wp_http_referer', '_wpnonce'), stripslashes($_SERVER['REQUEST_URI']) ) );
	 exit;
}

$title = __('Media Library');
$parent_file = 'upload.php';

if ( ! isset( $_GET['paged'] ) || $_GET['paged'] < 1 )
	$_GET['paged'] = 1;

if ( isset($_GET['detached']) ) {

	$media_per_page = (int) get_user_option( 'upload_per_page' );
	if ( empty($media_per_page) || $media_per_page < 1 )
		$media_per_page = 20;
	$media_per_page = apply_filters( 'upload_per_page', $media_per_page );

	if ( !empty($lost) ) {
		$start = ( (int) $_GET['paged'] - 1 ) * $media_per_page;
		$page_links_total = ceil(count($lost) / $media_per_page);
		$lost = implode(',', $lost);

		$orphans = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM $wpdb->posts WHERE post_type = 'attachment' AND ID IN (%s) LIMIT %d, %d", $lost, $start, $media_per_page ) );
	} else {
		$start = ( (int) $_GET['paged'] - 1 ) * $media_per_page;
		$orphans = $wpdb->get_results( $wpdb->prepare( "SELECT SQL_CALC_FOUND_ROWS * FROM $wpdb->posts WHERE post_type = 'attachment' AND post_status != 'trash' AND post_parent < 1 LIMIT %d, %d", $start, $media_per_page ) );
		$total_orphans = $wpdb->get_var( "SELECT FOUND_ROWS()" );
		$page_links_total = ceil( $total_orphans / $media_per_page );
		$wp_query->found_posts = $total_orphans;
		$wp_query->query_vars['posts_per_page'] = $media_per_page;
	}

	$post_mime_types = get_post_mime_types();
	$avail_post_mime_types = get_available_post_mime_types('attachment');

	if ( isset($_GET['post_mime_type']) && !array_intersect( (array) $_GET['post_mime_type'], array_keys($post_mime_types) ) )
		unset($_GET['post_mime_type']);

} else {
	list($post_mime_types, $avail_post_mime_types) = wp_edit_attachments_query();
}

$is_trash = ( isset($_GET['status']) && $_GET['status'] == 'trash' );

wp_enqueue_script('media');

add_contextual_help( $current_screen,
	'<p>' . __('All the files you&#8217;ve uploaded are listed in the Media Library, with the most recent uploads listed first. You can use the <em>Screen Options</em> tab to customize the display of this screen.') . '</p>' .
	'<p>' . __('You can narrow the list by file type/status using the text link filters at the top of the screen. You also can refine the list by date using the dropdown menu above the media table.') . '</p>' .
	'<p>' . __('Hovering over a row reveals action links: <em>Edit</em>, <em>Delete Permanently</em>, and <em>View</em>. Clicking <em>Edit</em> or on the media file&#8217;s name displays a simple screen to edit that individual file&#8217;s metadata. Clicking <em>Delete Permanently</em> will delete the file from the media library (as well as from any posts to which it is currently attached). <em>View</em> will take you to the display page for that file.') . '</p>' .
	'<p>' . __('If a media file has not been attached to any post, you will see that in the <em>Attached To</em> column, and can click on <em>Attach File</em> to launch a small popup that will allow you to search for a post and attach the file.') . '</p>' .
	'<p><strong>' . __('For more information:') . '</strong></p>' .
	'<p>' . __('<a href="http://codex.wordpress.org/Media_Library_SubPanel" target="_blank">Media Library Documentation</a>') . '</p>' .
	'<p>' . __('<a href="http://wordpress.org/support/" target="_blank">Support Forums</a>') . '</p>'
);

require_once('./admin-header.php');
?>

<div class="wrap">
<?php screen_icon(); ?>
<h2><?php echo esc_html( $title ); ?> <a href="media-new.php" class="button add-new-h2"><?php echo esc_html_x('Add New', 'file'); ?></a> <?php
if ( isset($_GET['s']) && $_GET['s'] )
	printf( '<span class="subtitle">' . __('Search results for &#8220;%s&#8221;') . '</span>', get_search_query() ); ?>
</h2>

<?php
$message = '';
if ( isset($_GET['posted']) && (int) $_GET['posted'] ) {
	$message = __('Media attachment updated.');
	$_SERVER['REQUEST_URI'] = remove_query_arg(array('posted'), $_SERVER['REQUEST_URI']);
}

if ( isset($_GET['attached']) && (int) $_GET['attached'] ) {
	$attached = (int) $_GET['attached'];
	$message = sprintf( _n('Reattached %d attachment.', 'Reattached %d attachments.', $attached), $attached );
	$_SERVER['REQUEST_URI'] = remove_query_arg(array('attached'), $_SERVER['REQUEST_URI']);
}

if ( isset($_GET['deleted']) && (int) $_GET['deleted'] ) {
	$message = sprintf( _n( 'Media attachment permanently deleted.', '%d media attachments permanently deleted.', $_GET['deleted'] ), number_format_i18n( $_GET['deleted'] ) );
	$_SERVER['REQUEST_URI'] = remove_query_arg(array('deleted'), $_SERVER['REQUEST_URI']);
}

if ( isset($_GET['trashed']) && (int) $_GET['trashed'] ) {
	$message = sprintf( _n( 'Media attachment moved to the trash.', '%d media attachments moved to the trash.', $_GET['trashed'] ), number_format_i18n( $_GET['trashed'] ) );
	$message .= ' <a href="' . esc_url( wp_nonce_url( 'upload.php?doaction=undo&action=untrash&ids='.(isset($_GET['ids']) ? $_GET['ids'] : ''), "bulk-media" ) ) . '">' . __('Undo') . '</a>';
	$_SERVER['REQUEST_URI'] = remove_query_arg(array('trashed'), $_SERVER['REQUEST_URI']);
}

if ( isset($_GET['untrashed']) && (int) $_GET['untrashed'] ) {
	$message = sprintf( _n( 'Media attachment restored from the trash.', '%d media attachments restored from the trash.', $_GET['untrashed'] ), number_format_i18n( $_GET['untrashed'] ) );
	$_SERVER['REQUEST_URI'] = remove_query_arg(array('untrashed'), $_SERVER['REQUEST_URI']);
}

$messages[1] = __('Media attachment updated.');
$messages[2] = __('Media permanently deleted.');
$messages[3] = __('Error saving media attachment.');
$messages[4] = __('Media moved to the trash.') . ' <a href="' . esc_url( wp_nonce_url( 'upload.php?doaction=undo&action=untrash&ids='.(isset($_GET['ids']) ? $_GET['ids'] : ''), "bulk-media" ) ) . '">' . __('Undo') . '</a>';
$messages[5] = __('Media restored from the trash.');

if ( isset($_GET['message']) && (int) $_GET['message'] ) {
	$message = $messages[$_GET['message']];
	$_SERVER['REQUEST_URI'] = remove_query_arg(array('message'), $_SERVER['REQUEST_URI']);
}

if ( !empty($message) ) { ?>
<div id="message" class="updated"><p><?php echo $message; ?></p></div>
<?php } ?>

<ul class="subsubsub">
<?php
$type_links = array();
$_num_posts = (array) wp_count_attachments();
$_total_posts = array_sum($_num_posts) - $_num_posts['trash'];
if ( !isset( $total_orphans ) )
		$total_orphans = $wpdb->get_var( "SELECT COUNT( * ) FROM $wpdb->posts WHERE post_type = 'attachment' AND post_status != 'trash' AND post_parent < 1" );
$matches = wp_match_mime_types(array_keys($post_mime_types), array_keys($_num_posts));
foreach ( $matches as $type => $reals )
	foreach ( $reals as $real )
		$num_posts[$type] = ( isset( $num_posts[$type] ) ) ? $num_posts[$type] + $_num_posts[$real] : $_num_posts[$real];

$class = ( empty($_GET['post_mime_type']) && !isset($_GET['detached']) && !isset($_GET['status']) ) ? ' class="current"' : '';
$type_links[] = "<li><a href='upload.php'$class>" . sprintf( _nx( 'All <span class="count">(%s)</span>', 'All <span class="count">(%s)</span>', $_total_posts, 'uploaded files' ), number_format_i18n( $_total_posts ) ) . '</a>';
foreach ( $post_mime_types as $mime_type => $label ) {
	$class = '';

	if ( !wp_match_mime_types($mime_type, $avail_post_mime_types) )
		continue;

	if ( !empty($_GET['post_mime_type']) && wp_match_mime_types($mime_type, $_GET['post_mime_type']) )
		$class = ' class="current"';
	if ( !empty( $num_posts[$mime_type] ) )
		$type_links[] = "<li><a href='upload.php?post_mime_type=$mime_type'$class>" . sprintf( _n( $label[2][0], $label[2][1], $num_posts[$mime_type] ), number_format_i18n( $num_posts[$mime_type] )) . '</a>';
}
$type_links[] = '<li><a href="upload.php?detached=1"' . ( isset($_GET['detached']) ? ' class="current"' : '' ) . '>' . sprintf( _nx( 'Unattached <span class="count">(%s)</span>', 'Unattached <span class="count">(%s)</span>', $total_orphans, 'detached files' ), number_format_i18n( $total_orphans ) ) . '</a>';

if ( !empty($_num_posts['trash']) )
	$type_links[] = '<li><a href="upload.php?status=trash"' . ( (isset($_GET['status']) && $_GET['status'] == 'trash' ) ? ' class="current"' : '') . '>' . sprintf( _nx( 'Trash <span class="count">(%s)</span>', 'Trash <span class="count">(%s)</span>', $_num_posts['trash'], 'uploaded files' ), number_format_i18n( $_num_posts['trash'] ) ) . '</a>';

echo implode( " |</li>\n", $type_links) . '</li>';
unset($type_links);
?>
</ul>

<form class="search-form" action="" method="get">
<p class="search-box">
	<label class="screen-reader-text" for="media-search-input"><?php _e( 'Search Media' ); ?>:</label>
	<input type="text" id="media-search-input" name="s" value="<?php the_search_query(); ?>" />
	<input type="submit" value="<?php esc_attr_e( 'Search Media' ); ?>" class="button" />
</p>
</form>

<form id="posts-filter" action="" method="get">
<?php wp_nonce_field('bulk-media'); ?>
<?php if ( have_posts() || isset( $orphans ) ) { ?>
<div class="tablenav">
<?php
if ( ! isset($page_links_total) )
	$page_links_total =  $wp_query->max_num_pages;

$page_links = paginate_links( array(
	'base' => add_query_arg( 'paged', '%#%' ),
	'format' => '',
	'prev_text' => __('&laquo;'),
	'next_text' => __('&raquo;'),
	'total' => $page_links_total,
	'current' => $_GET['paged']
));

if ( $page_links ) : ?>
<div class="tablenav-pages"><?php $page_links_text = sprintf( '<span class="displaying-num">' . __( 'Displaying %s&#8211;%s of %s' ) . '</span>%s',
	number_format_i18n( ( $_GET['paged'] - 1 ) * $wp_query->query_vars['posts_per_page'] + 1 ),
	number_format_i18n( min( $_GET['paged'] * $wp_query->query_vars['posts_per_page'], $wp_query->found_posts ) ),
	number_format_i18n( $wp_query->found_posts ),
	$page_links
); echo $page_links_text; ?></div>
<?php endif; ?>

<div class="alignleft actions">
<?php if ( ! isset( $orphans ) || ! empty( $orphans ) ) { ?>
<select name="action" class="select-action">
<option value="-1" selected="selected"><?php _e('Bulk Actions'); ?></option>
<?php if ( $is_trash ) { ?>
<option value="untrash"><?php _e('Restore'); ?></option>
<?php } if ( $is_trash || !EMPTY_TRASH_DAYS || !MEDIA_TRASH ) { ?>
<option value="delete"><?php _e('Delete Permanently'); ?></option>
<?php } else { ?>
<option value="trash"><?php _e('Move to Trash'); ?></option>
<?php } if ( isset($orphans) ) { ?>
<option value="attach"><?php _e('Attach to a post'); ?></option>
<?php } ?>
</select>
<input type="submit" value="<?php esc_attr_e('Apply'); ?>" name="doaction" id="doaction" class="button-secondary action" />

<?php
if ( !is_singular() && !isset($_GET['detached']) && !$is_trash ) {
	$arc_query = "SELECT DISTINCT YEAR(post_date) AS yyear, MONTH(post_date) AS mmonth FROM $wpdb->posts WHERE post_type = 'attachment' ORDER BY post_date DESC";

	$arc_result = $wpdb->get_results( $arc_query );

	$month_count = count($arc_result);

	if ( $month_count && !( 1 == $month_count && 0 == $arc_result[0]->mmonth ) ) : ?>
<select name='m'>
<option value='0'><?php _e('Show all dates'); ?></option>
<?php
foreach ($arc_result as $arc_row) {
	if ( $arc_row->yyear == 0 )
		continue;
	$arc_row->mmonth = zeroise( $arc_row->mmonth, 2 );

	if ( isset($_GET['m']) && ( $arc_row->yyear . $arc_row->mmonth == $_GET['m'] ) )
		$default = ' selected="selected"';
	else
		$default = '';

	echo "<option$default value='" . esc_attr("$arc_row->yyear$arc_row->mmonth") . "'>";
	echo $wp_locale->get_month($arc_row->mmonth) . " $arc_row->yyear";
	echo "</option>\n";
}
?>
</select>
<?php endif; // month_count ?>

<?php do_action('restrict_manage_posts'); ?>

<input type="submit" id="post-query-submit" value="<?php esc_attr_e('Filter'); ?>" class="button-secondary" />

<?php } // ! is_singular ?>

<?php

} // ! empty( $orphans )

if ( isset($_GET['detached']) ) { ?>
	<input type="submit" id="find_detached" name="find_detached" value="<?php esc_attr_e('Scan for lost attachments'); ?>" class="button-secondary" />
<?php } elseif ( isset($_GET['status']) && $_GET['status'] == 'trash' && current_user_can('edit_others_posts') ) { ?>
	<input type="submit" id="delete_all" name="delete_all" value="<?php esc_attr_e('Empty Trash'); ?>" class="button-secondary apply" />
<?php } ?>

</div>

<br class="clear" />
</div>

<?php } // have_posts() || !empty( $orphans ) ?>

<div class="clear"></div>

<?php if ( ! empty( $orphans ) ) { ?>
<table class="widefat" cellspacing="0">
<thead>
<tr>
	<th scope="col" class="check-column"><input type="checkbox" /></th>
	<th scope="col"></th>
	<th scope="col"><?php /* translators: column name in media */ _ex('Media', 'media column name'); ?></th>
	<th scope="col"><?php /* translators: column name in media */ _ex('Author', 'media column name'); ?></th>
	<th scope="col"><?php /* translators: column name in media */ _ex('Date Added', 'media column name'); ?></th>
</tr>
</thead>

<tfoot>
<tr>
	<th scope="col" class="check-column"><input type="checkbox" /></th>
	<th scope="col"></th>
	<th scope="col"><?php /* translators: column name in media */ _ex('Media', 'media column name'); ?></th>
	<th scope="col"><?php /* translators: column name in media */ _ex('Author', 'media column name'); ?></th>
	<th scope="col"><?php /* translators: column name in media */ _ex('Date Added', 'media column name'); ?></th>
</tr>
</tfoot>

<tbody id="the-list" class="list:post">
<?php
		foreach ( $orphans as $post ) {
			$class = 'alternate' == $class ? '' : 'alternate';
			$att_title = esc_html( _draft_or_post_title($post->ID) );
?>
	<tr id='post-<?php echo $post->ID; ?>' class='<?php echo $class; ?>' valign="top">
		<th scope="row" class="check-column"><?php if ( current_user_can('edit_post', $post->ID) ) { ?><input type="checkbox" name="media[]" value="<?php echo esc_attr($post->ID); ?>" /><?php } ?></th>

		<td class="media-icon"><?php
		if ( $thumb = wp_get_attachment_image( $post->ID, array(80, 60), true ) ) { ?>
			<a href="media.php?action=edit&amp;attachment_id=<?php echo $post->ID; ?>" title="<?php echo esc_attr(sprintf(__('Edit &#8220;%s&#8221;'), $att_title)); ?>"><?php echo $thumb; ?></a>
<?php	} ?></td>

		<td class="media column-media"><strong><a href="<?php echo get_edit_post_link( $post->ID ); ?>" title="<?php echo esc_attr(sprintf(__('Edit &#8220;%s&#8221;'), $att_title)); ?>"><?php echo $att_title; ?></a></strong><br />
		<?php
		if ( preg_match( '/^.*?\.(\w+)$/', get_attached_file( $post->ID ), $matches ) )
			echo esc_html( strtoupper( $matches[1] ) );
		else
			echo strtoupper( str_replace( 'image/', '', get_post_mime_type() ) );
		?>

		<div class="row-actions">
		<?php
		$actions = array();
		if ( current_user_can('edit_post', $post->ID) )
			$actions['edit'] = '<a href="' . get_edit_post_link($post->ID, true) . '">' . __('Edit') . '</a>';
		if ( current_user_can('delete_post', $post->ID) )
			if ( EMPTY_TRASH_DAYS && MEDIA_TRASH ) {
				$actions['trash'] = "<a class='submitdelete' href='" . wp_nonce_url("post.php?action=trash&amp;post=$post->ID", 'trash-attachment_' . $post->ID) . "'>" . __('Trash') . "</a>";
			} else {
				$delete_ays = !MEDIA_TRASH ? " onclick='return showNotice.warn();'" : '';
				$actions['delete'] = "<a class='submitdelete'$delete_ays href='" . wp_nonce_url("post.php?action=delete&amp;post=$post->ID", 'delete-attachment_' . $post->ID) . "'>" . __('Delete Permanently') . "</a>";
			}
		$actions['view'] = '<a href="' . get_permalink($post->ID) . '" title="' . esc_attr(sprintf(__('View &#8220;%s&#8221;'), $title)) . '" rel="permalink">' . __('View') . '</a>';
		if ( current_user_can('edit_post', $post->ID) )
			$actions['attach'] = '<a href="#the-list" onclick="findPosts.open(\'media[]\',\''.$post->ID.'\');return false;" class="hide-if-no-js">'.__('Attach').'</a>';
		$actions = apply_filters( 'media_row_actions', $actions, $post );
		$action_count = count($actions);
		$i = 0;
		foreach ( $actions as $action => $link ) {
			++$i;
			( $i == $action_count ) ? $sep = '' : $sep = ' | ';
			echo "<span class='$action'>$link$sep</span>";
		} ?>
		</div></td>
		<td class="author column-author"><?php $author = get_userdata($post->post_author); echo $author->display_name; ?></td>
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
		<td class="date column-date"><?php echo $h_time ?></td>
	</tr>
<?php	} ?>
</tbody>
</table>

<?php

} else {
	include( './edit-attachment-rows.php' );
} ?>

<div id="ajax-response"></div>

<div class="tablenav">

<?php
if ( have_posts() || ! empty( $orphans ) ) {

if ( $page_links )
	echo "<div class='tablenav-pages'>$page_links_text</div>";
?>

<div class="alignleft actions">
<select name="action2" class="select-action">
<option value="-1" selected="selected"><?php _e('Bulk Actions'); ?></option>
<?php if ($is_trash) { ?>
<option value="untrash"><?php _e('Restore'); ?></option>
<?php } if ( $is_trash || !EMPTY_TRASH_DAYS || !MEDIA_TRASH ) { ?>
<option value="delete"><?php _e('Delete Permanently'); ?></option>
<?php } else { ?>
<option value="trash"><?php _e('Move to Trash'); ?></option>
<?php } if (isset($orphans)) { ?>
<option value="attach"><?php _e('Attach to a post'); ?></option>
<?php } ?>
</select>
<input type="submit" value="<?php esc_attr_e('Apply'); ?>" name="doaction2" id="doaction2" class="button-secondary action" />

<?php if ( isset($_GET['status']) && $_GET['status'] == 'trash' && current_user_can('edit_others_posts') ) { ?>
	<input type="submit" id="delete_all2" name="delete_all2" value="<?php esc_attr_e('Empty Trash'); ?>" class="button-secondary apply" />
<?php } ?>
</div>

<?php } ?>
<br class="clear" />
</div>
<?php find_posts_div(); ?>
</form>
<br class="clear" />

</div>

<?php
include('./admin-footer.php');
