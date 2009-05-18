<?php
/**
 * Edit Posts Administration Panel.
 *
 * @package WordPress
 * @subpackage Administration
 */

/** WordPress Administration Bootstrap */
require_once('admin.php');

// Back-compat for viewing comments of an entry
if ( $_redirect = intval( max( @$_GET['p'], @$_GET['attachment_id'], @$_GET['page_id'] ) ) ) {
	wp_redirect( admin_url('edit-comments.php?p=' . $_redirect ) );
	exit;
} else {
	unset( $_redirect );
}

// Handle bulk actions
if ( isset($_GET['action']) && ( -1 != $_GET['action'] || -1 != $_GET['action2'] ) ) {
	$doaction = ( -1 != $_GET['action'] ) ? $_GET['action'] : $_GET['action2'];

	switch ( $doaction ) {
		case 'delete':
			if ( isset($_GET['post']) && ! isset($_GET['bulk_edit']) && (isset($_GET['doaction']) || isset($_GET['doaction2'])) ) {
				check_admin_referer('bulk-posts');
				$deleted = 0;
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
					$deleted++;
				}
			}
			break;
		case 'edit':
			if ( isset($_GET['post']) && isset($_GET['bulk_edit']) ) {
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
	if ( isset($done) ) {
		$done['updated'] = count( $done['updated'] );
		$done['skipped'] = count( $done['skipped'] );
		$done['locked'] = count( $done['locked'] );
		$sendback = add_query_arg( $done, $sendback );
	}
	if ( isset($deleted) )
		$sendback = add_query_arg('deleted', $deleted, $sendback);
	wp_redirect($sendback);
	exit();
} elseif ( isset($_GET['_wp_http_referer']) && ! empty($_GET['_wp_http_referer']) ) {
	 wp_redirect( remove_query_arg( array('_wp_http_referer', '_wpnonce'), stripslashes($_SERVER['REQUEST_URI']) ) );
	 exit;
}

if ( empty($title) )
	$title = __('Edit Posts');
$parent_file = 'edit.php';
wp_enqueue_script('inline-edit-post');

list($post_stati, $avail_post_stati) = wp_edit_posts_query();

require_once('admin-header.php');

if ( !isset( $_GET['paged'] ) )
	$_GET['paged'] = 1;

if ( empty($_GET['mode']) )
	$mode = 'list';
else
	$mode = esc_attr($_GET['mode']); ?>

<div class="wrap">
<?php screen_icon(); ?>
<h2><?php echo esc_html( $title );
if ( isset($_GET['s']) && $_GET['s'] )
	printf( '<span class="subtitle">' . __('Search results for &#8220;%s&#8221;') . '</span>', esc_html( get_search_query() ) ); ?>
</h2>

<?php
if ( isset($_GET['posted']) && $_GET['posted'] ) : $_GET['posted'] = (int) $_GET['posted']; ?>
<div id="message" class="updated fade"><p><strong><?php _e('Your post has been saved.'); ?></strong> <a href="<?php echo get_permalink( $_GET['posted'] ); ?>"><?php _e('View post'); ?></a> | <a href="<?php echo get_edit_post_link( $_GET['posted'] ); ?>"><?php _e('Edit post'); ?></a></p></div>
<?php $_SERVER['REQUEST_URI'] = remove_query_arg(array('posted'), $_SERVER['REQUEST_URI']);
endif; ?>

<?php if ( isset($_GET['locked']) || isset($_GET['skipped']) || isset($_GET['updated']) || isset($_GET['deleted']) ) { ?>
<div id="message" class="updated fade"><p>
<?php if ( isset($_GET['updated']) && (int) $_GET['updated'] ) {
	printf( _n( '%s post updated.', '%s posts updated.', $_GET['updated'] ), number_format_i18n( $_GET['updated'] ) );
	unset($_GET['updated']);
}

if ( isset($_GET['skipped']) && (int) $_GET['skipped'] )
	unset($_GET['skipped']);

if ( isset($_GET['locked']) && (int) $_GET['locked'] ) {
	printf( _n( '%s post not updated, somebody is editing it.', '%s posts not updated, somebody is editing them.', $_GET['locked'] ), number_format_i18n( $_GET['locked'] ) );
	unset($_GET['locked']);
}

if ( isset($_GET['deleted']) && (int) $_GET['deleted'] ) {
	printf( _n( 'Post deleted.', '%s posts deleted.', $_GET['deleted'] ), number_format_i18n( $_GET['deleted'] ) );
	unset($_GET['deleted']);
}

$_SERVER['REQUEST_URI'] = remove_query_arg( array('locked', 'skipped', 'updated', 'deleted'), $_SERVER['REQUEST_URI'] );
?>
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
$status_links[] = "<li><a href='edit.php' $class>" . sprintf( _nx( 'All <span class="count">(%s)</span>', 'All <span class="count">(%s)</span>', $total_posts, 'posts' ), number_format_i18n( $total_posts ) ) . '</a>';


foreach ( $post_stati as $status => $label ) {
	$class = '';

	if ( !in_array( $status, $avail_post_stati ) )
		continue;

	if ( empty( $num_posts->$status ) )
		continue;
	if ( isset($_GET['post_status']) && $status == $_GET['post_status'] )
		$class = ' class="current"';

	$status_links[] = "<li><a href='edit.php?post_status=$status' $class>" . sprintf( _n( $label[2][0], $label[2][1], $num_posts->$status ), number_format_i18n( $num_posts->$status ) ) . '</a>';
}
echo implode( " |</li>\n", $status_links ) . '</li>';
unset( $status_links );
endif;
?>
</ul>

<p class="search-box">
	<label class="screen-reader-text" for="post-search-input"><?php _e( 'Search Posts' ); ?>:</label>
	<input type="text" id="post-search-input" name="s" value="<?php the_search_query(); ?>" />
	<input type="submit" value="<?php esc_attr_e( 'Search Posts' ); ?>" class="button" />
</p>

<?php if ( isset($_GET['post_status'] ) ) : ?>
<input type="hidden" name="post_status" value="<?php echo esc_attr($_GET['post_status']) ?>" />
<?php endif; ?>
<input type="hidden" name="mode" value="<?php echo esc_attr($mode); ?>" />

<?php if ( have_posts() ) { ?>

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
<option value="-1" selected="selected"><?php _e('Bulk Actions'); ?></option>
<option value="edit"><?php _e('Edit'); ?></option>
<option value="delete"><?php _e('Delete'); ?></option>
</select>
<input type="submit" value="<?php esc_attr_e('Apply'); ?>" name="doaction" id="doaction" class="button-secondary action" />
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

	echo "<option$default value='" . esc_attr("$arc_row->yyear$arc_row->mmonth") . "'>";
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
<input type="submit" id="post-query-submit" value="<?php esc_attr_e('Filter'); ?>" class="button-secondary" />

<?php } ?>
</div>

<?php if ( $page_links ) { ?>
<div class="tablenav-pages"><?php $page_links_text = sprintf( '<span class="displaying-num">' . __( 'Displaying %s&#8211;%s of %s' ) . '</span>%s',
	number_format_i18n( ( $_GET['paged'] - 1 ) * $wp_query->query_vars['posts_per_page'] + 1 ),
	number_format_i18n( min( $_GET['paged'] * $wp_query->query_vars['posts_per_page'], $wp_query->found_posts ) ),
	number_format_i18n( $wp_query->found_posts ),
	$page_links
); echo $page_links_text; ?></div>
<?php } ?>

<div class="view-switch">
	<a href="<?php echo esc_url(add_query_arg('mode', 'list', $_SERVER['REQUEST_URI'])) ?>"><img <?php if ( 'list' == $mode ) echo 'class="current"'; ?> id="view-switch-list" src="../wp-includes/images/blank.gif" width="20" height="20" title="<?php _e('List View') ?>" alt="<?php _e('List View') ?>" /></a>
	<a href="<?php echo esc_url(add_query_arg('mode', 'excerpt', $_SERVER['REQUEST_URI'])) ?>"><img <?php if ( 'excerpt' == $mode ) echo 'class="current"'; ?> id="view-switch-excerpt" src="../wp-includes/images/blank.gif" width="20" height="20" title="<?php _e('Excerpt View') ?>" alt="<?php _e('Excerpt View') ?>" /></a>
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
<option value="-1" selected="selected"><?php _e('Bulk Actions'); ?></option>
<option value="edit"><?php _e('Edit'); ?></option>
<option value="delete"><?php _e('Delete'); ?></option>
</select>
<input type="submit" value="<?php esc_attr_e('Apply'); ?>" name="doaction2" id="doaction2" class="button-secondary action" />
<br class="clear" />
</div>
<br class="clear" />
</div>

<?php } else { // have_posts() ?>
<div class="clear"></div>
<p><?php _e('No posts found') ?></p>
<?php } ?>

</form>

<?php inline_edit_row( 'post' ); ?>

<div id="ajax-response"></div>

<br class="clear" />

</div>

<?php
include('admin-footer.php');
