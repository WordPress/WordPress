<?php
/**
 * Edit Posts Administration Panel.
 *
 * @package WordPress
 * @subpackage Administration
 */

/** WordPress Administration Bootstrap */
require_once('admin.php');

// Handle bulk actions
if ( isset($_GET['action']) && ( -1 != $_GET['action'] || -1 != $_GET['action2'] ) ) {
	$doaction = ( -1 != $_GET['action'] ) ? $_GET['action'] : $_GET['action2'];

	switch ( $doaction ) {
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

				if ( -1 == $_GET['_status'] ) {
					$_GET['post_status'] = null;
					unset($_GET['_status'], $_GET['post_status']);
				} else {
					$_GET['post_status'] = $_GET['_status'];
				}

				$done = bulk_edit_posts($_GET);
			}
			break;
	}

	$sendback = wp_get_referer();
	if ( strpos($sendback, 'post.php') !== false ) $sendback = admin_url('post-new.php');
	elseif ( strpos($sendback, 'attachments.php') !== false ) $sendback = admin_url('attachments.php');
	$sendback = preg_replace('|[^a-z0-9-~+_.?#=&;,/:]|i', '', $sendback);
	if ( isset($done) ) {
		$done['updated'] = count( $done['updated'] );
		$done['skipped'] = count( $done['skipped'] );
		$done['locked'] = count( $done['locked'] );
		$sendback = add_query_arg( $done, $sendback );
	}
	wp_redirect($sendback);
	exit();
} elseif ( isset($_GET['_wp_http_referer']) && ! empty($_GET['_wp_http_referer']) ) {
	 wp_redirect( remove_query_arg( array('_wp_http_referer', '_wpnonce'), stripslashes($_SERVER['REQUEST_URI']) ) );
	 exit;
}

if ( empty($title) )
	$title = __('Edit Posts');
$parent_file = 'edit.php';
wp_enqueue_script('admin-forms');
wp_enqueue_script('inline-edit-post');
wp_enqueue_script('posts');

list($post_stati, $avail_post_stati) = wp_edit_posts_query();

if ( 1 == count($posts) && is_singular() ) {
	wp_enqueue_script( 'admin-comments' );
	enqueue_comment_hotkeys_js();
}

require_once('admin-header.php');

if ( !isset( $_GET['paged'] ) )
	$_GET['paged'] = 1;

if ( empty($_GET['mode']) )
	$mode = 'list';
else
	$mode = attribute_escape($_GET['mode']); ?>

<?php screen_meta('post') ?>

<div class="wrap">
<h2><?php echo wp_specialchars( $title ); ?></h2>

<?php
if ( isset($_GET['posted']) && $_GET['posted'] ) : $_GET['posted'] = (int) $_GET['posted']; ?>
<div id="message" class="updated fade"><p><strong><?php _e('Your post has been saved.'); ?></strong> <a href="<?php echo get_permalink( $_GET['posted'] ); ?>"><?php _e('View post'); ?></a> | <a href="<?php echo get_edit_post_link( $_GET['posted'] ); ?>"><?php _e('Edit post'); ?></a></p></div>
<?php $_SERVER['REQUEST_URI'] = remove_query_arg(array('posted'), $_SERVER['REQUEST_URI']);
endif; ?>

<?php if ( isset($_GET['locked']) || isset($_GET['skipped']) || isset($_GET['updated']) ) { ?>
<div id="message" class="updated fade"><p>
<?php if ( (int) $_GET['updated'] ) {
	printf( __ngettext( '%d post updated.', '%d posts updated.', $_GET['updated'] ), number_format_i18n( $_GET['updated'] ) );
	unset($_GET['updated']);
}

if ( (int) $_GET['skipped'] )
	unset($_GET['skipped']);

if ( (int) $_GET['locked'] ) {
	printf( __ngettext( ' %d post not updated, somebody is editing it.', ' %d posts not updated, somebody is editing them.', $_GET['locked'] ), number_format_i18n( $_GET['locked'] ) );
	unset($_GET['locked']);
} ?>
</p></div>
<?php } ?>

<form id="posts-filter" action="" method="get">
<ul class="subsubsub">
<?php
if ( empty($locked_post_status) ) :
$status_links = array();
$num_posts = wp_count_posts( 'post', 'readable' );
$total_posts = array_sum( (array) $num_posts );
$class = empty( $_GET['post_status'] ) ? ' class="current"' : '';
$status_links[] = "<li><a href='edit.php' $class>" . sprintf( __ngettext( 'All <span class="count">(%s)</span>', 'All <span class="count">(%s)</span>', $total_posts ), number_format_i18n( $total_posts ) ) . '</a>';


foreach ( $post_stati as $status => $label ) {
	$class = '';

	if ( !in_array( $status, $avail_post_stati ) )
		continue;

	if ( empty( $num_posts->$status ) )
		continue;
	if ( isset($_GET['post_status']) && $status == $_GET['post_status'] )
		$class = ' class="current"';

	$status_links[] = "<li><a href='edit.php?post_status=$status' $class>" . sprintf( __ngettext( $label[2][0], $label[2][1], $num_posts->$status ), number_format_i18n( $num_posts->$status ) ) . '</a>';
}
echo implode( " |</li>\n", $status_links ) . '</li>';
unset( $status_links );
endif;
?>
</ul>

<p class="search-box">
	<label class="hidden" for="post-search-input"><?php _e( 'Search Posts' ); ?>:</label>
	<input type="text" class="search-input" id="post-search-input" name="s" value="<?php the_search_query(); ?>" />
	<input type="submit" value="<?php _e( 'Search Posts' ); ?>" class="button-primary" />
</p>

<?php if ( isset($_GET['post_status'] ) ) : ?>
<input type="hidden" name="post_status" value="<?php echo attribute_escape($_GET['post_status']) ?>" />
<?php endif; ?>
<input type="hidden" name="mode" value="<?php echo $mode; ?>" />

<div class="tablenav">
<?php
$page_links = paginate_links( array(
	'base' => add_query_arg( 'paged', '%#%' ),
	'format' => '',
	'prev_text' => __('&laquo;'),
	'next_text' => __('&raquo;'),
	'total' => $wp_query->max_num_pages,
	'current' => $_GET['paged']
));

?>

<div class="alignleft actions">
<select name="action">
<option value="-1" selected="selected"><?php _e('Actions'); ?></option>
<option value="edit"><?php _e('Edit'); ?></option>
<option value="delete"><?php _e('Delete'); ?></option>
</select>
<input type="submit" value="<?php _e('Apply'); ?>" name="doaction" id="doaction" class="button-secondary action" />
<?php wp_nonce_field('bulk-posts'); ?>

<?php // view filters
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

<?php if ( $page_links ) { ?>
<div class="tablenav-pages"><?php $page_links_text = sprintf( '<span class="displaying-num">' . __( 'Displaying %s-%s of %s' ) . '</span>' . __( '%s' ),
	number_format_i18n( ( $_GET['paged'] - 1 ) * $wp_query->query_vars['posts_per_page'] + 1 ),
	number_format_i18n( min( $_GET['paged'] * $wp_query->query_vars['posts_per_page'], $wp_query->found_posts ) ),
	number_format_i18n( $wp_query->found_posts ),
	$page_links
); echo $page_links_text; ?></div>
<?php } ?>

<div class="view-switch">
	<a href="<?php echo clean_url(add_query_arg('mode', 'list', $_SERVER['REQUEST_URI'])) ?>"><img <?php if ( 'list' == $mode ) echo 'class="current"'; ?> src="images/list.gif" title="<?php _e('List View') ?>" alt="<?php _e('List View') ?>" /></a>
	<a href="<?php echo clean_url(add_query_arg('mode', 'excerpt', $_SERVER['REQUEST_URI'])) ?>"><img <?php if ( 'excerpt' == $mode ) echo 'class="current"'; ?> src="images/exc.gif" title="<?php _e('Excerpt View') ?>" alt="<?php _e('Excerpt View') ?>" /></a>
</div>

<div class="clear"></div>
</div>

<div class="clear"></div>

<?php include( 'edit-post-rows.php' ); ?>

<div class="tablenav">

<?php
if ( $page_links )
	echo "<div class='tablenav-pages'>$page_links_text</div>";
?>

<div class="alignleft actions">
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

<tfoot>
  <tr>
    <th scope="col"><?php _e('Comment') ?></th>
    <th scope="col"><?php _e('Author') ?></th>
    <th scope="col"><?php _e('Submitted') ?></th>
  </tr>
</tfoot>

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
