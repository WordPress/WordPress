<?php
require_once('admin.php');

if (!current_user_can('upload_files'))
	wp_die(__('You do not have permission to upload files.'));

// Handle bulk deletes
if ( isset($_GET['deleteit']) && isset($_GET['delete']) ) {
	check_admin_referer('bulk-media');
	foreach( (array) $_GET['delete'] as $post_id_del ) {
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
} elseif ( !empty($_GET['_wp_http_referer']) ) {
	wp_redirect(remove_query_arg(array('_wp_http_referer', '_wpnonce'), stripslashes($_SERVER['REQUEST_URI'])));
	exit;
}

$title = __('Media Library');
$parent_file = 'edit.php';
wp_enqueue_script( 'admin-forms' );

list($post_mime_types, $avail_post_mime_types) = wp_edit_attachments_query();

if ( is_singular() )
	wp_enqueue_script( 'admin-comments' );

require_once('admin-header.php');

if ( !isset( $_GET['paged'] ) )
	$_GET['paged'] = 1;

?>

<div class="wrap">

<form id="posts-filter" action="" method="get">
<h2><?php
if ( is_singular() ) {
	printf(__('Comments on %s'), apply_filters( "the_title", $post->post_title));
} else {
	$post_mime_type_label = _c('Manage Media|manage media header');
	if ( isset($_GET['post_mime_type']) && in_array( $_GET['post_mime_type'], array_keys($post_mime_types) ) )
        $post_mime_type_label = $post_mime_types[$_GET['post_mime_type']][1];
	if ( $post_listing_pageable && !is_archive() && !is_search() )
		$h2_noun = is_paged() ? sprintf(__( 'Previous %s' ), $post_mime_type_label) : sprintf(__('Latest %s'), $post_mime_type_label);
	else
		$h2_noun = $post_mime_type_label;
	// Use $_GET instead of is_ since they can override each other
	$h2_author = '';
	$_GET['author'] = (int) $_GET['author'];
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
	printf( _c( '%1$s%2$s%3$s%4$s%5$s%6$s|You can reorder these: 1: Posts, 2: by {s}, 3: matching {s}, 4: in {s}, 5: tagged with {s}, 6: during {s}' ), $h2_noun, $h2_author, $h2_search, $h2_cat, $h2_tag, $h2_month );
}
?></h2>

<ul class="subsubsub">
<?php
$type_links = array();
$_num_posts = (array) wp_count_attachments();
$matches = wp_match_mime_types(array_keys($post_mime_types), array_keys($_num_posts));
foreach ( $matches as $type => $reals )
	foreach ( $reals as $real )
		$num_posts[$type] += $_num_posts[$real];
$class = empty($_GET['post_mime_type']) ? ' class="current"' : '';
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
echo implode(' | </li>', $type_links) . '</li>';
unset($type_links);
?>
</ul>

<?php
if ( isset($_GET['posted']) && $_GET['posted'] ) : $_GET['posted'] = (int) $_GET['posted']; ?>
<div id="message" class="updated fade"><p><strong><?php _e('Your media has been saved.'); ?></strong> <a href="<?php echo get_permalink( $_GET['posted'] ); ?>"><?php _e('View media'); ?></a> | <a href="media.php?action=edit&amp;attachment_id=<?php echo $_GET['posted']; ?>"><?php _e('Edit media'); ?></a></p></div>
<?php $_SERVER['REQUEST_URI'] = remove_query_arg(array('posted'), $_SERVER['REQUEST_URI']);
endif;

$messages[1] = __('Media updated.');
$messages[2] = __('Media deleted.');

if (isset($_GET['message'])) : ?>
<div id="message" class="updated fade"><p><?php echo $messages[$_GET['message']]; ?></p></div>
<?php $_SERVER['REQUEST_URI'] = remove_query_arg(array('message'), $_SERVER['REQUEST_URI']);
endif;
?>

<p id="post-search">
	<input type="text" id="post-search-input" name="s" value="<?php the_search_query(); ?>" />
	<input type="submit" value="<?php _e( 'Search Media' ); ?>" class="button" />
</p>

<?php do_action('restrict_manage_posts'); ?>

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
<input type="submit" value="<?php _e('Delete'); ?>" name="deleteit" class="button-secondary delete" />
<?php wp_nonce_field('bulk-media'); ?>
<?php

if ( !is_singular() ) :
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

<?php endif; // is_singular ?>

</div>

<br class="clear" />
</div>

<br class="clear" />

<?php include( 'edit-attachment-rows.php' ); ?>

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
	
	$comments = $wpdb->get_results("SELECT * FROM $wpdb->comments WHERE comment_post_ID = $id AND comment_approved != 'spam' ORDER BY comment_date");
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

endif; // comments
endif; // posts;

?>

</div>

<?php include('admin-footer.php'); ?>
