<?php
require_once('admin.php');

$title = __('Tags');
$parent_file = 'edit.php';

wp_reset_vars(array('action', 'tag'));

if ( isset($_GET['deleteit']) && isset($_GET['delete_tags']) )
	$action = 'bulk-delete';

switch($action) {

case 'addtag':

	check_admin_referer('add-tag');

	if ( !current_user_can('manage_categories') )
		wp_die(__('Cheatin&#8217; uh?'));

	$ret = wp_insert_term($_POST['name'], 'post_tag', $_POST);
	if ( $ret && !is_wp_error( $ret ) ) {
		wp_redirect('edit-tags.php?message=1#addtag');
	} else {
		wp_redirect('edit-tags.php?message=4#addtag');
	}
	exit;
break;

case 'delete':
	$tag_ID = (int) $_GET['tag_ID'];
	check_admin_referer('delete-tag_' .  $tag_ID);

	if ( !current_user_can('manage_categories') )
		wp_die(__('Cheatin&#8217; uh?'));

	wp_delete_term( $tag_ID, 'post_tag');

	wp_redirect('edit-tags.php?message=2');
	exit;

break;

case 'bulk-delete':
	check_admin_referer('bulk-tags');

	if ( !current_user_can('manage_categories') )
		wp_die(__('Cheatin&#8217; uh?'));

	$tags = $_GET['delete_tags'];
	foreach( (array) $tags as $tag_ID ) {
		wp_delete_term( $tag_ID, 'post_tag');
	}

	$location = 'edit-tags.php';
	if ( $referer = wp_get_referer() ) {
		if ( false !== strpos($referer, 'edit-tags.php') )
			$location = $referer;
	}

	$location = add_query_arg('message', 6, $location);
	wp_redirect($location);
	exit;

break;

case 'edit':

	require_once ('admin-header.php');
	$tag_ID = (int) $_GET['tag_ID'];

	$tag = get_term($tag_ID, 'post_tag', OBJECT, 'edit');
	include('edit-tag-form.php');

break;

case 'editedtag':
	$tag_ID = (int) $_POST['tag_ID'];
	check_admin_referer('update-tag_' . $tag_ID);

	if ( !current_user_can('manage_categories') )
		wp_die(__('Cheatin&#8217; uh?'));

	$ret = wp_update_term($tag_ID, 'post_tag', $_POST);

	$location = 'edit-tags.php';
	if ( $referer = wp_get_original_referer() ) {
		if ( false !== strpos($referer, 'edit-tags.php') )
			$location = $referer;
	}

	if ( $ret && !is_wp_error( $ret ) )
		$location = add_query_arg('message', 3, $location);
	else
		$location = add_query_arg('message', 5, $location);

	wp_redirect($location);
	exit;
break;

default:

if ( !empty($_GET['_wp_http_referer']) ) {
	 wp_redirect(remove_query_arg(array('_wp_http_referer', '_wpnonce'), stripslashes($_SERVER['REQUEST_URI'])));
	 exit;
}

wp_enqueue_script( 'admin-tags' );
wp_enqueue_script('admin-forms');

require_once ('admin-header.php');

$messages[1] = __('Tag added.');
$messages[2] = __('Tag deleted.');
$messages[3] = __('Tag updated.');
$messages[4] = __('Tag not added.');
$messages[5] = __('Tag not updated.');
$messages[6] = __('Tags deleted.');
?>

<?php if (isset($_GET['message'])) : ?>
<div id="message" class="updated fade"><p><?php echo $messages[$_GET['message']]; ?></p></div>
<?php $_SERVER['REQUEST_URI'] = remove_query_arg(array('message'), $_SERVER['REQUEST_URI']);
endif; ?>

<div class="wrap">

<form id="posts-filter" action="" method="get">
<?php if ( current_user_can('manage_categories') ) : ?>
	<h2><?php printf(__('Manage Tags (<a href="%s">add new</a>)'), '#addtag') ?> </h2>
<?php else : ?>
	<h2><?php _e('Manage Tags') ?> </h2>
<?php endif; ?>

<p id="post-search">
	<label class="hidden" for="post-search-input"><?php _e( 'Search Tags' ); ?>:</label>
	<input type="text" id="post-search-input" name="s" value="<?php echo attribute_escape(stripslashes($_GET['s'])); ?>" />
	<input type="submit" value="<?php _e( 'Search Tags' ); ?>" class="button" />
</p>

<br class="clear" />

<div class="tablenav">

<?php
$pagenum = absint( $_GET['pagenum'] );
if ( empty($pagenum) )
	$pagenum = 1;
if( !$tagsperpage || $tagsperpage < 0 )
	$tagsperpage = 20;

$page_links = paginate_links( array(
	'base' => add_query_arg( 'pagenum', '%#%' ),
	'format' => '',
	'total' => ceil(wp_count_terms('post_tag') / $tagsperpage),
	'current' => $pagenum
));

if ( $page_links )
	echo "<div class='tablenav-pages'>$page_links</div>";
?>

<div class="alignleft">
<input type="submit" value="<?php _e('Delete'); ?>" name="deleteit" class="button-secondary delete" />
<?php wp_nonce_field('bulk-tags'); ?>
</div>

<br class="clear" />
</div>

<br class="clear" />

<table class="widefat">
	<thead>
	<tr>
	<th scope="col" class="check-column"><input type="checkbox" /></th>
        <th scope="col"><?php _e('Name') ?></th>
        <th scope="col" class="num" style="width: 90px"><?php _e('Posts') ?></th>
	</tr>
	</thead>
	<tbody id="the-list" class="list:tag">
<?php

$searchterms = trim( $_GET['s'] );

$count = tag_rows( $pagenum, $tagsperpage, $searchterms );
?>
	</tbody>
</table>
</form>

<div class="tablenav">

<?php
if ( $page_links )
	echo "<div class='tablenav-pages'>$page_links</div>";
?>
<br class="clear" />
</div>
<br class="clear" />

</div>

<?php if ( current_user_can('manage_categories') ) : ?>

<br />
<?php include('edit-tag-form.php'); ?>

<?php endif; ?>

<?php
break;
}

include('admin-footer.php');

?>
