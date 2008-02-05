<?php
require_once('admin.php');

$title = __('Tags');
$parent_file = 'edit.php';

wp_reset_vars(array('action', 'tag'));

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
	if( $ret && !is_wp_error( $ret ) ) {
		wp_redirect('edit-tags.php?message=3');
	} else {
		wp_redirect('edit-tags.php?message=5');
	}
	exit;
break;

default:

wp_enqueue_script( 'admin-tags' );
wp_enqueue_script('admin-forms');

require_once ('admin-header.php');

$messages[1] = __('Tag added.');
$messages[2] = __('Tag deleted.');
$messages[3] = __('Tag updated.');
$messages[4] = __('Tag not added.');
$messages[5] = __('Tag not updated.');
?>

<?php if (isset($_GET['message'])) : ?>
<div id="message" class="updated fade"><p><?php echo $messages[$_GET['message']]; ?></p></div>
<?php endif; ?>

<div class="wrap">

<form id="tags-filter" action="" method="get">
<?php if ( current_user_can('manage_categories') ) : ?>
	<h2><?php printf(__('Tags (<a href="%s">add new</a>)'), '#addtag') ?> </h2>
<?php else : ?>
	<h2><?php _e('Tags') ?> </h2>
<?php endif; ?>
	<p id="tag-search">
		<input type="text" id="tag-search-input" name="s" value="<?php echo attribute_escape( stripslashes( $_GET[ 's' ]) ); ?>" />
		<input type="submit" value="<?php _e( 'Search Tags' ); ?>" />
	</p>
</form>

<br style="clear:both;" />

<form name="deletetags" id="deletetags" action="" method="post">
<?php wp_nonce_field('bulk-tags'); ?>
<table class="widefat">
	<thead>
	<tr>
		<th scope="col" style="text-align: center"><input type="checkbox" onclick="checkAll(document.getElementById('deletetags'));" /></th>
		<th scope="col" style="text-align: center"><?php _e('ID') ?></th>
        <th scope="col"><?php _e('Name') ?></th>
        <th scope="col" width="90" style="text-align: center"><?php _e('Posts') ?></th>
        <th colspan="2" style="text-align: center"><?php _e('Action') ?></th>
	</tr>
	</thead>
	<tbody id="the-list" class="list:tag">
<?php
$pagenum = absint( $_GET['pagenum'] );
if( !$tagsperpage || $tagsperpage < 0 ) {
	$tagsperpage = 20;
}
$searchterms = trim( $_GET['s'] );

$count = tag_rows( $pagenum, $tagsperpage, $searchterms );
?>
	</tbody>
</table>
</form>
<?php

$baseurl = get_bloginfo( 'wpurl' ) . '/wp-admin/edit-tags.php?pagenum=';
if( $pagenum >= 1 ) {
	echo '<a href="' . $baseurl . ($pagenum - 1 ) . '">&lt;&lt;' . __('Previous Tags') . '</a>';
	if( $count == $tagsperpage ) {
		echo ' | ';
	}
}


if( $count == $tagsperpage ) {
	echo '<a href="' . $baseurl . ($pagenum + 1 ) . '">' . __('Next Tags') . '&gt;&gt;</a>';
}

?>

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
