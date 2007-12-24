<?php
require_once('admin.php');

$title = __('Posts');
$parent_file = 'edit.php';
wp_enqueue_script( 'admin-posts' );
if ( 1 == $_GET['c'] )
	wp_enqueue_script( 'admin-comments' );
require_once('admin-header.php');

add_filter( 'post_limits', $limit_filter = create_function( '$a', '$b = split(" ",$a); if ( !isset($b[2]) ) return $a; $start = intval(trim($b[1])) / 20 * 15; if ( !is_int($start) ) return $a; return "LIMIT $start, 20";' ) );
list($post_stati, $avail_post_stati) = wp_edit_posts_query();
$wp_query->max_num_pages = ceil( $wp_query->found_posts / 15 ); // We grab 20 but only show 15 ( 5 more for ajax extra )
?>

<div class="wrap">

<?php

$posts_columns = wp_manage_posts_columns();

?>

<h2><?php
if ( is_single() ) {
	printf(__('Comments on %s'), apply_filters( "the_title", $post->post_title));
} else {
	if ( $post_listing_pageable && !is_archive() && !is_search() )
		$h2_noun = is_paged() ? sprintf(__( 'Previous %s' ), $post_status_label) : sprintf(__('Latest %s'), $post_status_label);
	else
		$h2_noun = $post_status_label;
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
	$h2_month  = isset($_GET['m'])   && $_GET['m']   ? ' ' . sprintf( __('during %s'), single_month_title(' ', false) ) : '';
	printf( _c( '%1$s%2$s%3$s%4$s%5$s|You can reorder these: 1: Posts, 2: by {s}, 3: matching {s}, 4: in {s}, 5: during {s}' ), $h2_noun, $h2_author, $h2_search, $h2_cat, $h2_month );
}
?></h2>

<form name="searchform" id="searchform" action="" method="get">
	<fieldset><legend><?php _e('Search terms&hellip;'); ?></legend>
		<input type="text" name="s" id="s" value="<?php the_search_query(); ?>" size="17" />
	</fieldset>

	<fieldset><legend><?php _e('Status&hellip;'); ?></legend>
		<select name='post_status'>
			<option<?php selected( @$_GET['post_status'], 0 ); ?> value='0'><?php _e('Any'); ?></option>
<?php	foreach ( $post_stati as $status => $label ) : if ( !in_array($status, $avail_post_stati) ) continue; ?>
			<option<?php selected( @$_GET['post_status'], $status ); ?> value='<?php echo $status; ?>'><?php echo $label[0]; ?></option>
<?php	endforeach; ?>
		</select>
	</fieldset>

<?php
$editable_ids = get_editable_user_ids( $user_ID );
if ( $editable_ids && count( $editable_ids ) > 1 ) :
?>
	<fieldset><legend><?php _e('Author&hellip;'); ?></legend>
		<?php wp_dropdown_users( array('include' => $editable_ids, 'show_option_all' => __('Any'), 'name' => 'author', 'selected' => isset($_GET['author']) ? $_GET['author'] : 0) ); ?>
	</fieldset>

<?php
endif;

$arc_query = "SELECT DISTINCT YEAR(post_date) AS yyear, MONTH(post_date) AS mmonth FROM $wpdb->posts WHERE post_type = 'post' ORDER BY post_date DESC";

$arc_result = $wpdb->get_results( $arc_query );

$month_count = count($arc_result);

if ( $month_count && !( 1 == $month_count && 0 == $arc_result[0]->mmonth ) ) { ?>

	<fieldset><legend><?php _e('Month&hellip;') ?></legend>
		<select name='m'>
			<option<?php selected( @$_GET['m'], 0 ); ?> value='0'><?php _e('Any'); ?></option>
		<?php
		foreach ($arc_result as $arc_row) {
			if ( $arc_row->yyear == 0 )
				continue;
			$arc_row->mmonth = zeroise($arc_row->mmonth, 2);

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
	</fieldset>

<?php } ?>

	<fieldset><legend><?php _e('Category&hellip;') ?></legend>
		<?php wp_dropdown_categories('show_option_all='.__('All').'&hide_empty=1&hierarchical=1&show_count=1&selected='.$cat);?>
	</fieldset>
	<input type="submit" id="post-query-submit" value="<?php _e('Filter &#187;'); ?>" class="button" />
</form>

<?php do_action('restrict_manage_posts'); ?>

<br style="clear:both;" />

<?php include( 'edit-post-rows.php' ); ?>

<form action="" method="post" id="get-extra-posts" class="add:the-extra-list:" style="display:none">
	<?php wp_nonce_field( 'add-post', '_ajax_nonce', false ); ?>
</form>

<div id="ajax-response"></div>

<div class="navigation">
<div class="alignleft"><?php next_posts_link(__('&laquo; Older Entries')) ?></div>
<div class="alignright"><?php previous_posts_link(__('Newer Entries &raquo;')) ?></div>
</div>

<?php

if ( 1 == count($posts) && isset( $_GET['p'] ) ) {

	$comments = $wpdb->get_results("SELECT * FROM $wpdb->comments WHERE comment_post_ID = $id AND comment_approved != 'spam' ORDER BY comment_date");
	if ($comments) {
		// Make sure comments, post, and post_author are cached
		update_comment_cache($comments);
		$post = get_post($id);
		$authordata = get_userdata($post->post_author);
	?>
<h3 id="comments"><?php _e('Comments') ?></h3>
<ol id="the-comment-list" class="list:comment commentlist">
<?php
		$i = 0;
		foreach ( $comments as $comment ) {
			_wp_comment_list_item( $comment->comment_ID, ++$i );
		}
	echo '</ol>';
	} // end if comments
?>
<?php } ?>
</div>

<?php include('admin-footer.php'); ?>
