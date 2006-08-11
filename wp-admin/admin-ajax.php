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

function wp_ajax_echo_meta( $pid, $mid, $key, $value ) {
	$value = wp_specialchars($value, true);
	$key_js = addslashes(wp_specialchars($key, 'double'));
	$key = wp_specialchars($key, true);
	$r  = "<meta><id>$mid</id><postid>$pid</postid><newitem><![CDATA[<table><tbody>";
	$r .= "<tr id='meta-$mid'><td valign='top'>";
	$r .= "<input name='meta[$mid][key]' tabindex='6' onkeypress='return killSubmit(\"theList.ajaxUpdater(&#039;meta&#039;,&#039;meta-$mid&#039;);\",event);' type='text' size='20' value='$key' />";
	$r .= "</td><td><textarea name='meta[$mid][value]' tabindex='6' rows='2' cols='30'>$value</textarea></td><td align='center'>";
	$r .= "<input name='updatemeta' type='button' class='updatemeta' tabindex='6' value='Update' onclick='return theList.ajaxUpdater(&#039;meta&#039;,&#039;meta-$mid&#039;);' /><br />";
	$r .= "<input name='deletemeta[$mid]' type='submit' onclick=\"return deleteSomething( 'meta', $mid, '";
	$r .= sprintf(__("You are about to delete the &quot;%s&quot; custom field on this post.\\n&quot;OK&quot; to delete, &quot;Cancel&quot; to stop."), $key_js);
	$r .= "' );\" class='deletemeta' tabindex='6' value='Delete' />";
	$r .= "</td></tr></tbody></table>]]></newitem></meta>";
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
	$r = "<?xml version='1.0' standalone='yes'?><ajaxresponse>";
	foreach ( $names as $cat_name ) {
		$cat_name = trim($cat_name);
		if ( !$category_nicename = sanitize_title($cat_name) )
			die('0');
		if ( !$cat_id = category_exists( $cat_name ) )
			$cat_id = wp_create_category( $cat_name );
		$cat_name = wp_specialchars(stripslashes($cat_name));
		$r .= "<category><id>$cat_id</id><newitem><![CDATA[";
		$r .= "<li id='category-$cat_id'><label for='in-category-$cat_id' class='selectit'>";
		$r .= "<input value='$cat_id' type='checkbox' checked='checked' name='post_category[]' id='in-category-$cat_id'/> $cat_name</label></li>";
		$r .= "]]></newitem></category>";
	}
	$r .= '</ajaxresponse>';
	header('Content-type: text/xml');
	die($r);
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
	$cat_full_name = wp_specialchars( $cat_full_name, 1 );

	$r  = "<?xml version='1.0' standalone='yes'?><ajaxresponse>";
	$r .= "<cat><id>$cat->cat_ID</id><name>$cat_full_name</name><newitem><![CDATA[<table><tbody>";
	$r .= _cat_row( $cat, $level, $cat_full_name );
	$r .= "</tbody></table>]]></newitem></cat></ajaxresponse>";
	header('Content-type: text/xml');
	die($r);
	break;
case 'add-meta' :
	if ( !current_user_can( 'edit_post', $id ) )
		die('-1');
	if ( $id < 0 ) {
		if ( $pid = write_post() )
			$meta = has_meta( $pid );
		else
			die('0');
		$key = $meta[0]['meta_key'];
		$value = $meta[0]['meta_value'];
		$mid = (int) $meta[0]['meta_id'];
	} else {
		if ( $mid = add_meta( $id ) )
			$meta = get_post_meta_by_id( $mid );
		else
			die('0');
		$key = $meta->meta_key;
		$value = $meta->meta_value;
		$pid = (int) $meta->post_id;
	}
	$r = "<?xml version='1.0' standalone='yes'?><ajaxresponse>";
	$r .= wp_ajax_echo_meta( $pid, $mid, $key, $value );
	$r .= '</ajaxresponse>';
	header('Content-type: text/xml');
	die($r);
	break;
case 'update-meta' :
	$mid = (int) array_pop(array_keys($_POST['meta']));
	$key = $_POST['meta'][$mid]['key'];
	$value = $_POST['meta'][$mid]['value'];
	if ( !$meta = get_post_meta_by_id( $mid ) )
		die('0');
	if ( !current_user_can( 'edit_post', $meta->post_id ) )
		die('-1');
	$r = "<?xml version='1.0' standalone='yes'?><ajaxresponse>";
	if ( $u = update_meta( $mid, $key, $value ) ) {
		$key = stripslashes($key);
		$value = stripslashes($value);
		$r .= wp_ajax_echo_meta( $meta->post_id, $mid, $key, $value );
	}
	$r .= '</ajaxresponse>';
	header('Content-type: text/xml');
	die($r);
	break;
case 'add-user' :
	if ( !current_user_can('edit_users') )
		die('-1');
	require_once(ABSPATH . WPINC . '/registration.php');
	$user_id = add_user();
	if ( is_wp_error( $user_id ) ) {
		foreach( $user_id->get_error_messages() as $message )
			echo "$message<br />";
	exit;
	} elseif ( !$user_id ) {
		die('0');
	}
	$r  = "<?xml version='1.0' standalone='yes'?><ajaxresponse><user><id>$user_id</id><newitem><![CDATA[<table><tbody>";
	$r .= user_row( $user_id );
	$r .= "</tbody></table>]]></newitem></user></ajaxresponse>";
	header('Content-type: text/xml');
	die($r);
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
default :
	do_action( 'wp_ajax_' . $_POST['action'] );
	die('0');
	break;
endswitch;
?>
