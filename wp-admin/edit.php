<?php
/**
 * Edit Posts Administration Panel.
 *
 * @package WordPress
 * @subpackage Administration
 */

/** WordPress Administration Bootstrap */
require_once('admin.php');

$action = -1;
if ( isset($_GET['action2']) && $_GET['action2'] != -1 )
	$action = $_GET['action2'];
if ( isset($_GET['action']) && $_GET['action'] != -1 )
	$action = $_GET['action'];

// Handle bulk actions
if ( $action != -1 ) {
	switch ( $action ) {
		case 'delete':
			if ( isset($_GET['post']) &&  (isset($_GET['doaction']) || isset($_GET['doaction2'])) ) {
				check_admin_referer('bulk-posts');
				foreach( (array) $_GET['post'] as $post_id_del ) {
					$post_del = & get_post($post_id_del);

					if ( !current_user_can('delete_post', $post_id_del) )
						wp_die( __('You are not allowed to delete this post.') );

					if ( $post_del->post_type == 'attachment' ) {
						if ( ! wp_delete_attachment($post_id_del) )
							wp_die( __('Error in deleting...') );
					} else {
						if ( !wp_delete_post($post_id_del) )
							wp_die( __('Error in deleting...') );
					}
				}
			}
			break;
		case 'edit':
			if ( isset($_GET['post']) ) {
				check_admin_referer('bulk-posts');
				$_GET['post_status'] = $_GET['_status'];
	
				if ( -1 == $_GET['post_author'] )
					unset($_GET['post_author']);
	
				$done = bulk_edit_posts($_GET);
			}
			break;
	}

	$sendback = wp_get_referer();
	if (strpos($sendback, 'post.php') !== false) $sendback = admin_url('post-new.php');
	elseif (strpos($sendback, 'attachments.php') !== false) $sendback = admin_url('attachments.php');
	$sendback = preg_replace('|[^a-z0-9-~+_.?#=&;,/:]|i', '', $sendback);
	if ( isset($done) ) {
		$done['upd'] = count( $done['upd'] );
		$done['skip'] = count( $done['skip'] );
		$sendback = add_query_arg( $done, $sendback );
		unset($done);
	}
	wp_redirect($sendback);
	exit();
} elseif ( !empty($_GET['_wp_http_referer']) ) {
	 wp_redirect(remove_query_arg(array('_wp_http_referer', '_wpnonce'), stripslashes($_SERVER['REQUEST_URI'])));
	 exit;
}

if ( empty($title) )
	$title = __('View All Posts');
$parent_file = 'edit.php';
wp_enqueue_script('admin-forms');
wp_enqueue_script('inline-edit');
wp_enqueue_script('posts');

list($post_stati, $avail_post_stati) = wp_edit_posts_query();

if ( 1 == count($posts) && is_singular() ) {
	wp_enqueue_script( 'admin-comments' );
	wp_enqueue_script( 'jquery-table-hotkeys' );
}

require_once('admin-header.php');

if ( !isset( $_GET['paged'] ) )
	$_GET['paged'] = 1;

if ( empty($_GET['mode']) )
	$mode = 'list';
else
	$mode = attribute_escape($_GET['mode']); ?>

<div id="edit-settings">
<a href="#edit_settings" id="show-settings-link" class="hide-if-no-js show-settings"><?php _e('Page Options') ?></a>
<div id="edit-settings-wrap" class="hidden">
<a href="#edit_settings" id="hide-settings-link" class="show-settings"><?php _e('Hide Options') ?></a>
<h5><?php _e('Show on screen') ?></h5>
<form id="adv-settings" action="" method="get">
<div class="metabox-prefs">
<?php manage_columns_prefs('post') ?>
<?php wp_nonce_field( 'hiddencolumns', 'hiddencolumnsnonce', false ); ?>
<br class="clear" />
</div></form>
</div></div>

<?php
if ( isset($_GET['posted']) && $_GET['posted'] ) : $_GET['posted'] = (int) $_GET['posted']; ?>
<div id="message" class="updated fade"><p><strong><?php _e('Your post has been saved.'); ?></strong> <a href="<?php echo get_permalink( $_GET['posted'] ); ?>"><?php _e('View post'); ?></a> | <a href="<?php echo get_edit_post_link( $_GET['posted'] ); ?>"><?php _e('Edit post'); ?></a></p></div>
<?php $_SERVER['REQUEST_URI'] = remove_query_arg(array('posted'), $_SERVER['REQUEST_URI']);
endif; ?>

<?php if ( isset($_GET['upd']) && (int) $_GET['upd'] ) { ?>
<div id="message" class="updated fade"><p>
<?php printf( __ngettext( '%d post updated.', '%d posts updated.', $_GET['upd'] ), number_format_i18n( $_GET['upd'] ) );
unset($_GET['upd']);
	
	if ( isset($_GET['skip']) && (int) $_GET['skip'] ) {
		printf( __ngettext( ' %d post not updated. Somebody is editing it.', ' %d posts not updated. Somebody is editing them.', $_GET['skip'] ), number_format_i18n( $_GET['skip'] ) );
		unset($_GET['skip']);
	} ?>
</p></div>
<?php } ?>

<div class="wrap">

<h2><?php
if ( is_single() ) {
	printf(__('Comments on %s'), apply_filters( "the_title", $post->post_title));
} else {
	$post_status_label = _c('Posts|manage posts header');
	if ( isset($_GET['post_status']) && in_array( $_GET['post_status'], array_keys($post_stati) ) )
        $post_status_label = $post_stati[$_GET['post_status']][1];
   	//TODO: Unreachable code: $post_listing_pageable is undefined, Similar code in upload.php
	//if ( $post_listing_pageable && !is_archive() && !is_search() ) 
	//	$h2_noun = is_paged() ? sprintf(__( 'Previous %s' ), $post_status_label) : sprintf(__('Latest %s'), $post_status_label);
	//else
		$h2_noun = $post_status_label;
	// Use $_GET instead of is_ since they can override each other
	$h2_author = '';
	$_GET['author'] = isset($_GET['author']) ? (int) $_GET['author'] : 0;
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
	printf( _c( '%1$s%2$s%3$s%4$s%5$s%6$s (<a href="%7$s">Add New</a>)|You can reorder these: 1: Posts, 2: by {s}, 3: matching {s}, 4: in {s}, 5: tagged with {s}, 6: during {s}' ), $h2_noun, $h2_author, $h2_search, $h2_cat, $h2_tag, $h2_month, 'post-new.php' );
}
?></h2>

<form id="posts-filter" action="" method="get">
<ul class="subsubsub">
<?php
if ( empty($locked_post_status) ) :
$status_links = array();
$num_posts = wp_count_posts( 'post', 'readable' );
$class = empty( $_GET['post_status'] ) ? ' class="current"' : '';
$status_links[] = "<li><a href='edit.php' $class>" . __('All Posts') . '</a>';
foreach ( $post_stati as $status => $label ) {
	$class = '';

	if ( !in_array( $status, $avail_post_stati ) )
		continue;

	if ( empty( $num_posts->$status ) )
		continue;
	if ( isset($_GET['post_status']) && $status == $_GET['post_status'] )
		$class = ' class="current"';

	$status_links[] = "<li><a href='edit.php?post_status=$status' $class>" .
	sprintf( __ngettext( $label[2][0], $label[2][1], $num_posts->$status ), number_format_i18n( $num_posts->$status ) ) . '</a>';
}
echo implode( ' |</li>', $status_links ) . '</li>';
unset( $status_links );
endif;
?>
</ul>

<?php if ( isset($_GET['post_status'] ) ) : ?>
<input type="hidden" name="post_status" value="<?php echo attribute_escape($_GET['post_status']) ?>" />
<?php endif; ?>
<input type="hidden" name="mode" value="<?php echo $mode; ?>" />

<ul class="view-switch">
	<li <?php if ( 'list' == $mode ) echo "class='current'" ?>><a href="<?php echo clean_url(add_query_arg('mode', 'list', $_SERVER['REQUEST_URI'])) ?>"><?php _e('List View') ?></a></li>
	<li <?php if ( 'excerpt' == $mode ) echo "class='current'" ?>><a href="<?php echo clean_url(add_query_arg('mode', 'excerpt', $_SERVER['REQUEST_URI'])) ?>"><?php _e('Excerpt View') ?></a></li>
</ul>

<div class="tablenav">

<?php
$page_links = paginate_links( array(
	'base' => add_query_arg( 'paged', '%#%' ),
	'format' => '',
	'total' => $wp_query->max_num_pages,
	'current' => $_GET['paged']
));

if ( $page_links )
	echo "<div class='tablenav-pages'>$page_links</div>";
?>

<div class="alignleft">
<select name="action">
<option value="-1" selected="selected"><?php _e('Actions'); ?></option>
<option value="edit"><?php _e('Edit'); ?></option>
<option value="delete"><?php _e('Delete'); ?></option>
</select>
<input type="submit" value="<?php _e('Apply'); ?>" name="doaction" id="doaction" class="button-secondary action" />
<?php wp_nonce_field('bulk-posts'); ?>
<?php
if ( !is_singular() ) {
$arc_query = "SELECT DISTINCT YEAR(post_date) AS yyear, MONTH(post_date) AS mmonth FROM $wpdb->posts WHERE post_type = 'post' ORDER BY post_date DESC";

$arc_result = $wpdb->get_results( $arc_query );

$month_count = count($arc_result);

if ( $month_count && !( 1 == $month_count && 0 == $arc_result[0]->mmonth ) ) {
$m = isset($_GET['m']) ? (int)$_GET['m'] : 0;
?>
<select name='m'>
<option<?php selected( $m, 0 ); ?> value='0'><?php _e('Show all dates'); ?></option>
<?php
foreach ($arc_result as $arc_row) {
	if ( $arc_row->yyear == 0 )
		continue;
	$arc_row->mmonth = zeroise( $arc_row->mmonth, 2 );

	if ( $arc_row->yyear . $arc_row->mmonth == $m )
		$default = ' selected="selected"';
	else
		$default = '';

	echo "<option$default value='$arc_row->yyear$arc_row->mmonth'>";
	echo $wp_locale->get_month($arc_row->mmonth) . " $arc_row->yyear";
	echo "</option>\n";
}
?>
</select>
<?php } ?>

<?php
$dropdown_options = array('show_option_all' => __('View all categories'), 'hide_empty' => 0, 'hierarchical' => 1,
	'show_count' => 0, 'orderby' => 'name', 'selected' => $cat);
wp_dropdown_categories($dropdown_options);
do_action('restrict_manage_posts');
?>
<input type="submit" id="post-query-submit" value="<?php _e('Filter'); ?>" class="button-secondary" />

<?php } ?>
</div>

<br class="clear" />
</div>

<br class="clear" />

<?php include( 'edit-post-rows.php' ); ?>

<div class="tablenav">

<?php
if ( $page_links )
	echo "<div class='tablenav-pages'>$page_links</div>";
?>

<div class="alignleft">
<select name="action2">
<option value="-1" selected="selected"><?php _e('Actions'); ?></option>
<option value="edit"><?php _e('Edit'); ?></option>
<option value="delete"><?php _e('Delete'); ?></option>
</select>
<input type="submit" value="<?php _e('Apply'); ?>" name="doaction2" id="doaction2" class="button-secondary action" />
<br class="clear" />
</div>
<br class="clear" />
</div>

</form>

<?php inline_edit_row( 'post' ); ?>

<div id="ajax-response"></div>

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
    <th scope="col"><?php _e('Author') ?></th>
    <th scope="col"><?php _e('Submitted') ?></th>
  </tr>
</thead>
<tbody id="the-comment-list" class="list:comment">
<?php
	foreach ($comments as $comment)
		_wp_comment_row( $comment->comment_ID, 'single', false, false );
?>
</tbody>
</table>

<?php
wp_comment_reply();
endif; // comments
endif; // posts;

?>

</div>

<?php include('admin-footer.php'); ?>
