<?php
require_once('../wp-config.php');
require_once('admin-functions.php');
require_once('admin-db.php');

define('DOING_AJAX', true);

check_ajax_referer();
if ( !is_user_logged_in() )
	die('-1');

function get_out_now() { exit; }
add_action( 'shutdown', 'get_out_now', -1 );

function wp_ajax_meta_row( $pid, $mid, $key, $value ) {
	$value = attribute_escape($value);
	$key_js = addslashes(wp_specialchars($key, 'double'));
	$key = attribute_escape($key);
	$r .= "<tr id='meta-$mid'><td valign='top'>";
	$r .= "<input name='meta[$mid][key]' tabindex='6' onkeypress='return killSubmit(\"theList.ajaxUpdater(&#039;meta&#039;,&#039;meta-$mid&#039;);\",event);' type='text' size='20' value='$key' />";
	$r .= "</td><td><textarea name='meta[$mid][value]' tabindex='6' rows='2' cols='30'>$value</textarea></td><td align='center'>";
	$r .= "<input name='updatemeta' type='button' class='updatemeta' tabindex='6' value='Update' onclick='return theList.ajaxUpdater(&#039;meta&#039;,&#039;meta-$mid&#039;);' /><br />";
	$r .= "<input name='deletemeta[$mid]' type='submit' onclick=\"return deleteSomething( 'meta', $mid, '";
	$r .= sprintf(__("You are about to delete the &quot;%s&quot; custom field on this post.\\n&quot;OK&quot; to delete, &quot;Cancel&quot; to stop."), $key_js);
	$r .= "' );\" class='deletemeta' tabindex='6' value='Delete' /></td></tr>";
	return $r;
}

$id = (int) $_POST['id'];
switch ( $_POST['action'] ) :
case 'delete-comment' :
	if ( !$comment = get_comment( $id ) )
		die('0');
	if ( !current_user_can( 'edit_post', $comment->comment_post_ID ) )
		die('-1');

	if ( wp_delete_comment( $comment->comment_ID ) )
		die('1');
	else	die('0');
	break;
case 'delete-comment-as-spam' :
	if ( !$comment = get_comment( $id ) )
		die('0');
	if ( !current_user_can( 'edit_post', $comment->comment_post_ID ) )
		die('-1');

	if ( wp_set_comment_status( $comment->comment_ID, 'spam' ) )
		die('1');
	else	die('0');
	break;
case 'delete-cat' :
	if ( !current_user_can( 'manage_categories' ) )
		die('-1');

	if ( wp_delete_category( $id ) )
		die('1');
	else	die('0');
	break;
case 'delete-link' :
	if ( !current_user_can( 'manage_links' ) )
		die('-1');

	if ( wp_delete_link( $id ) )
		die('1');
	else	die('0');
	break;
case 'delete-meta' :
	if ( !$meta = get_post_meta_by_id( $id ) )
		die('0');
	if ( !current_user_can( 'edit_post', $meta->post_id ) )
		die('-1');
	if ( delete_meta( $meta->meta_id ) )
		die('1');
	die('0');
	break;
case 'delete-post' :
	if ( !current_user_can( 'delete_post', $id ) )
		die('-1');

	if ( wp_delete_post( $id ) )
		die('1');
	else	die('0');
	break;
case 'delete-page' :
	if ( !current_user_can( 'delete_page', $id ) )
		die('-1');

	if ( wp_delete_post( $id ) )
		die('1');
	else	die('0');
	break;
case 'dim-comment' :
	if ( !$comment = get_comment( $id ) )
		die('0');
	if ( !current_user_can( 'edit_post', $comment->comment_post_ID ) )
		die('-1');
	if ( !current_user_can( 'moderate_comments' ) )
		die('-1');

	if ( 'unapproved' == wp_get_comment_status($comment->comment_ID) ) {
		if ( wp_set_comment_status( $comment->comment_ID, 'approve' ) )
			die('1');
	} else {
		if ( wp_set_comment_status( $comment->comment_ID, 'hold' ) )
			die('1');
	}
	die('0');
	break;
case 'add-category' : // On the Fly
	if ( !current_user_can( 'manage_categories' ) )
		die('-1');
	$names = explode(',', $_POST['newcat']);
	$x = new WP_Ajax_Response();
	foreach ( $names as $cat_name ) {
		$cat_name = trim($cat_name);
		if ( !$category_nicename = sanitize_title($cat_name) )
			die('0');
		if ( !$cat_id = category_exists( $cat_name ) )
			$cat_id = wp_create_category( $cat_name );
		$cat_name = wp_specialchars(stripslashes($cat_name));
		$x->add( array(
			'what' => 'category',
			'id' => $cat_id,
			'data' => "<li id='category-$cat_id'><label for='in-category-$cat_id' class='selectit'><input value='$cat_id' type='checkbox' checked='checked' name='post_category[]' id='in-category-$cat_id'/> $cat_name</label></li>"
		) );
	}
	$x->send();
	break;
case 'add-cat' : // From Manage->Categories
	if ( !current_user_can( 'manage_categories' ) )
		die('-1');
	if ( !$cat = wp_insert_category( $_POST ) )
		die('0');
	if ( !$cat = get_category( $cat ) )
		die('0');
	$level = 0;
	$cat_full_name = $cat->cat_name;
	$_cat = $cat;
	while ( $_cat->category_parent ) {
		$_cat = get_category( $_cat->category_parent );
		$cat_full_name = $_cat->cat_name . ' &#8212; ' . $cat_full_name;
		$level++;
	}
	$cat_full_name = attribute_escape($cat_full_name);

	$x = new WP_Ajax_Response( array(
		'what' => 'cat',
		'id' => $cat->cat_ID,
		'data' => _cat_row( $cat, $level, $cat_full_name ),
		'supplemental' => array('name' => $cat_full_name, 'show-link' => sprintf(__( 'Category <a href="#%s">%s</a> added' ), "cat-$cat->cat_ID", $cat_full_name))
	) );
	$x->send();
	break;
case 'add-meta' :
	if ( !current_user_can( 'edit_post', $id ) )
		die('-1');
	if ( $id < 0 ) {
		$now = current_time('timestamp');
		if ( $pid = wp_insert_post( array(
			'post_title' => sprintf('Draft created on %s at %s', date(get_option('date_format'), $now), date(get_option('time_format'), $now))
		) ) )
			$mid = add_meta( $pid );
		else
			die('0');
	} else if ( !$mid = add_meta( $id ) ) {
		die('0');
	}

	$meta = get_post_meta_by_id( $mid );
	$key = $meta->meta_key;
	$value = $meta->meta_value;
	$pid = (int) $meta->post_id;

	$x = new WP_Ajax_Response( array(
		'what' => 'meta',
		'id' => $mid,
		'data' => wp_ajax_meta_row( $pid, $mid, $key, $value ),
		'supplemental' => array('postid' => $pid)
	) );
	$x->send();
	break;
case 'update-meta' :
	$mid = (int) array_pop(array_keys($_POST['meta']));
	$key = $_POST['meta'][$mid]['key'];
	$value = $_POST['meta'][$mid]['value'];
	if ( !$meta = get_post_meta_by_id( $mid ) )
		die('0'); // if meta doesn't exist
	if ( !current_user_can( 'edit_post', $meta->post_id ) )
		die('-1');
	if ( $u = update_meta( $mid, $key, $value ) ) {
		$key = stripslashes($key);
		$value = stripslashes($value);
		$x = new WP_Ajax_Response( array(
			'what' => 'meta',
			'id' => $mid,
			'data' => wp_ajax_meta_row( $meta->post_id, $mid, $key, $value ),
			'supplemental' => array('postid' => $meta->post_id)
		) );
		$x->send();
	}
	die('1'); // We know meta exists; we also know it's unchanged (or DB error, in which case there are bigger problems).
	break;
case 'add-user' :
	if ( !current_user_can('edit_users') )
		die('-1');
	require_once(ABSPATH . WPINC . '/registration.php');
	if ( !$user_id = add_user() )
		die('0');
	elseif ( is_wp_error( $user_id ) ) {
		foreach( $user_id->get_error_messages() as $message )
			echo "<p>$message<p>";
		exit;
	}
	$user_object = new WP_User( $user_id );
	$x = new WP_Ajax_Response( array(
		'what' => 'user',
		'id' => $user_id,
		'data' => user_row( $user_object ),
		'supplemental' => array('show-link' => sprintf(__( 'User <a href="#%s">%s</a> added' ), "user-$user_id", $user_object->user_login))
	) );
	$x->send();
	break;
case 'autosave' :
	$_POST['post_content'] = $_POST['content'];
	$_POST['post_excerpt'] = $_POST['excerpt'];
	$_POST['post_status'] = 'draft';
	$_POST['post_category'] = explode(",", $_POST['catslist']);
	if($_POST['post_type'] == 'page' || empty($_POST['post_category']))
		unset($_POST['post_category']);	
	
	if($_POST['post_ID'] < 0) {
		$_POST['temp_ID'] = $_POST['post_ID'];
		$id = wp_write_post();
		if(is_wp_error($id))
			die($id->get_error_message());
		else
			die("$id");
	} else {
		$post_ID = (int) $_POST['post_ID'];
		$_POST['ID'] = $post_ID;
		$post = get_post($post_ID);
		if ( 'page' == $post->post_type ) {
			if ( !current_user_can('edit_page', $post_ID) )
				die(__('You are not allowed to edit this page.'));
		} else {
			if ( !current_user_can('edit_post', $post_ID) )
				die(__('You are not allowed to edit this post.'));
		}
		wp_update_post($_POST);
	}
	die('0');
break;
case 'autosave-generate-nonces' :
	$ID = (int) $_POST['post_ID'];
	if($_POST['post_type'] == 'post') {
		if(current_user_can('edit_post', $ID))
			die(wp_create_nonce('update-post_' . $ID));
	}
	if($_POST['post_type'] == 'page') {
		if(current_user_can('edit_page', $ID)) {
			die(wp_create_nonce('update-page_' . $ID));
		}
	}
	die($_POST['post_type']);
break;
default :
	do_action( 'wp_ajax_' . $_POST['action'] );
	die('0');
	break;
endswitch;
?>
