<?php
/* <Edit> */

require_once('../wp-includes/wp-l10n.php');

function add_magic_quotes($array) {
foreach ($array as $k => $v) {
	if (is_array($v)) {
		$array[$k] = add_magic_quotes($v);
	} else {
		$array[$k] = addslashes($v);
	}
}
return $array;
}

if (!get_magic_quotes_gpc()) {
$_GET    = add_magic_quotes($_GET);
$_POST   = add_magic_quotes($_POST);
$_COOKIE = add_magic_quotes($_COOKIE);
}

$wpvarstoreset = array('action', 'safe_mode', 'withcomments', 'posts', 'poststart', 'postend', 'content', 'edited_post_title', 'comment_error', 'profile', 'trackback_url', 'excerpt', 'showcomments', 'commentstart', 'commentend', 'commentorder');

for ($i=0; $i<count($wpvarstoreset); $i += 1) {
$wpvar = $wpvarstoreset[$i];
if (!isset($$wpvar)) {
	if (empty($_POST["$wpvar"])) {
		if (empty($_GET["$wpvar"])) {
			$$wpvar = '';
		} else {
			$$wpvar = $_GET["$wpvar"];
		}
	} else {
		$$wpvar = $_POST["$wpvar"];
	}
}
}

switch($action) {


case 'post':

		$standalone = 1;
		require_once('admin-header.php');

        $post_ID = $wpdb->get_var("SELECT ID FROM $wpdb->posts ORDER BY ID DESC LIMIT 1") + 1;

		$post_pingback = intval($_POST['post_pingback']);
		$content = balanceTags($_POST['content']);
		$content = format_to_post($content);
		$excerpt = balanceTags($_POST['excerpt']);
		$excerpt = format_to_post($excerpt);
		$post_title = $_POST['post_title'];
		$post_categories = $_POST['post_category'];
		if(get_settings('use_geo_positions')) {
			$latstr = $_POST['post_latf'];
			$lonstr = $_POST['post_lonf'];
			if((strlen($latstr) > 2) && (strlen($lonstr) > 2 ) ) {
				$post_latf = floatval($_POST['post_latf']);
				$post_lonf = floatval($_POST['post_lonf']);
			}
		}
		$post_status = $_POST['post_status'];
		$post_name = $_POST['post_name'];

		if (empty($post_status)) $post_status = 'draft';
		$comment_status = $_POST['comment_status'];
		if (empty($comment_status)) $comment_status = get_settings('default_comment_status');
		$ping_status = $_POST['ping_status'];
		if (empty($ping_status)) $ping_status = get_settings('default_ping_status');
		$post_password = $_POST['post_password'];
		
		if (empty($post_name))
			$post_name = sanitize_title($post_title, $post_ID);
		else
			$post_name = sanitize_title($post_name, $post_ID);

		$trackback = $_POST['trackback_url'];
	// Format trackbacks
	$trackback = preg_replace('|\s+|', '\n', $trackback);

	if ($user_level == 0)
		die (__('Cheatin&#8217; uh?'));

	if (($user_level > 4) && (!empty($_POST['edit_date']))) {
		$aa = $_POST['aa'];
		$mm = $_POST['mm'];
		$jj = $_POST['jj'];
		$hh = $_POST['hh'];
		$mn = $_POST['mn'];
		$ss = $_POST['ss'];
		$jj = ($jj > 31) ? 31 : $jj;
		$hh = ($hh > 23) ? $hh - 24 : $hh;
		$mn = ($mn > 59) ? $mn - 60 : $mn;
		$ss = ($ss > 59) ? $ss - 60 : $ss;
	$now = "$aa-$mm-$jj $hh:$mn:$ss";
	$now_gmt = get_gmt_from_date("$aa-$mm-$jj $hh:$mn:$ss");
	} else {
	$now = current_time('mysql');
	$now_gmt = current_time('mysql', 1);
	}

	// What to do based on which button they pressed
	if ('' != $_POST['saveasdraft']) $post_status = 'draft';
	if ('' != $_POST['saveasprivate']) $post_status = 'private';
	if ('' != $_POST['publish']) $post_status = 'publish';
	if ('' != $_POST['advanced']) $post_status = 'draft';
	if ('' != $_POST['savepage']) $post_status = 'static';

	if((get_settings('use_geo_positions')) && (strlen($latstr) > 2) && (strlen($lonstr) > 2) ) {
	$postquery ="INSERT INTO $wpdb->posts
			(ID, post_author, post_date, post_date_gmt, post_content, post_title, post_lat, post_lon, post_excerpt,  post_status, comment_status, ping_status, post_password, post_name, to_ping, post_modified, post_modified_gmt)
			VALUES
			('0', '$user_ID', '$now', '$now_gmt', '$content', '$post_title', $post_latf, $post_lonf,'$excerpt', '$post_status', '$comment_status', '$ping_status', '$post_password', '$post_name', '$trackback', '$now', '$now_gmt')
			";
	} else {
	$postquery ="INSERT INTO $wpdb->posts
			(ID, post_author, post_date, post_date_gmt, post_content, post_title, post_excerpt,  post_status, comment_status, ping_status, post_password, post_name, to_ping, post_modified, post_modified_gmt)
			VALUES
			('0', '$user_ID', '$now', '$now_gmt', '$content', '$post_title', '$excerpt', '$post_status', '$comment_status', '$ping_status', '$post_password', '$post_name', '$trackback', '$now', '$now_gmt')
			";
	}

	$result = $wpdb->query($postquery);

	if (!empty($_POST['mode'])) {
	switch($_POST['mode']) {
		case 'bookmarklet':
			$location = 'bookmarklet.php?a=b';
			break;
		case 'sidebar':
			$location = 'sidebar.php?a=b';
			break;
		default:
			$location = 'post.php';
			break;
		}
	} else {
		$location = 'post.php';
	}
	if ( '' != $_POST['advanced'] || isset($_POST['save']) )
		$location = "post.php?action=edit&post=$post_ID";

	if ( '' != $_POST['savepage'] )
		$location = "post.php?action=createpage";

	header("Location: $location"); // Send user on their way while we keep working


	// Insert categories
	// Check to make sure there is a category, if not just set it to some default
	if (!$post_categories) $post_categories[] = 1;
	foreach ($post_categories as $post_category) {
		// Double check it's not there already
		$exists = $wpdb->get_row("SELECT * FROM $wpdb->post2cat WHERE post_id = $post_ID AND category_id = $post_category");

		 if (!$exists && $result) { 
			$wpdb->query("
			INSERT INTO $wpdb->post2cat
			(post_id, category_id)
			VALUES
			($post_ID, $post_category)
			");
		}
	}

	add_meta($post_ID);

	$wpdb->query("UPDATE $wpdb->posts SET guid = '" . get_permalink($post_ID) . "' WHERE ID = '$post_ID'");
	
	if (isset($sleep_after_edit) && $sleep_after_edit > 0) {
			sleep($sleep_after_edit);
	}

	if ($post_status == 'publish') {
		if((get_settings('use_geo_positions')) && ($post_latf != null) && ($post_lonf != null)) {
			pingGeoUrl($post_ID);
		}

		if ($post_pingback) {
			pingback($content, $post_ID);
		}
		
		do_action('publish_post', $post_ID);

		// Time for trackbacks
		$to_ping = $wpdb->get_var("SELECT to_ping FROM $wpdb->posts WHERE ID = $post_ID");
		$pinged = $wpdb->get_var("SELECT pinged FROM $wpdb->posts WHERE ID = $post_ID");
		$pinged = explode("\n", $pinged);
		if ('' != $to_ping) {
			if (strlen($excerpt) > 0) {
				$the_excerpt = (strlen(strip_tags($excerpt)) > 255) ? substr(strip_tags($excerpt), 0, 252) . '...' : strip_tags($excerpt)	;
			} else {
				$the_excerpt = (strlen(strip_tags($content)) > 255) ? substr(strip_tags($content), 0, 252) . '...' : strip_tags($content);
			}
			$excerpt = stripslashes($the_excerpt);
			$to_pings = explode("\n", $to_ping);
			foreach ($to_pings as $tb_ping) {
				$tb_ping = trim($tb_ping);
				if (!in_array($tb_ping, $pinged)) {
				 trackback($tb_ping, stripslashes($post_title), $excerpt, $post_ID);
				}
			}
		}

	} // end if publish

	exit();
	break;

case 'edit':
	$title = __('Edit');

	$standalone = 0;
	require_once('admin-header.php');

	$post = $post_ID = $p = (int) $_GET['post'];
	if ($user_level > 0) {
		$postdata = $wpdb->get_row("SELECT * FROM $wpdb->posts WHERE ID = '$post_ID'");
		$authordata = get_userdata($postdata->post_author);
		if ($user_level < $authordata->user_level)
			die ('You don&#8217;t have the right to edit <strong>'.$authordata[1].'</strong>&#8217;s posts.');

		$content = $postdata->post_content;
		$content = format_to_edit($content);
		$edited_lat = $postdata->post_lat;
		$edited_lon = $postdata->post_lon;
		$excerpt = $postdata->post_excerpt;
		$excerpt = format_to_edit($excerpt);
		$edited_post_title = format_to_edit($postdata->post_title);
		$post_status = $postdata->post_status;
		$comment_status = $postdata->comment_status;
		$ping_status = $postdata->ping_status;
		$post_password = $postdata->post_password;
		$to_ping = $postdata->to_ping;
		$pinged = $postdata->pinged;
		$post_name = $postdata->post_name;

        if ($post_status == 'static') {
            include('edit-page-form.php');
        } else {
            include('edit-form-advanced.php');
        }

		$post = $wpdb->get_row("SELECT * FROM $wpdb->posts WHERE ID = '$post_ID'");
		?>
<div id='preview' class='wrap'>
	 <h2><?php _e('Post Preview (updated when post is saved)'); ?></h2>
																		<h3 class="storytitle" id="post-<?php the_ID(); ?>"><a href="<?php the_permalink() ?>" rel="bookmark" title="<?php printf(__("Permanent Link: %s"), the_title()); ?>"><?php the_title(); ?></a></h3>
																																																																					<div class="meta"><?php _e("Filed under:"); ?> <?php the_category(','); ?> &#8212; <?php the_author() ?> @ <?php the_time() ?></div>

<div class="storycontent">
<?php 
$content = apply_filters('the_content', $post->post_content);
echo $content;
?>
</div>
		</div>
<?php
	} else {
?>
		<p><?php printf(__('Since you&#8217;re a newcomer, you&#8217;ll have to wait for an admin to raise your level to 1, in order to be authorized to post.<br />
You can also <a href="mailto:%s?subject=Promotion?">e-mail the admin</a> to ask for a promotion.<br />
When you&#8217;re promoted, just reload this page and you&#8217;ll be able to blog. :)'), get_settings('admin_email')); ?>
		</p>
<?php
	}
	break;

case 'editpost':
// die(var_dump('<pre>', $_POST));
	$standalone = 1;
	require_once('./admin-header.php');

	if ($user_level == 0)
		die (__('Cheatin&#8217; uh?'));

	if (!isset($blog_ID)) {
		$blog_ID = 1;
	}
		$post_ID = $_POST['post_ID'];
		$post_categories = $_POST['post_category'];
		if (!$post_categories) $post_categories[] = 1;
		$content = balanceTags($_POST['content']);
		$content = format_to_post($content);
		$excerpt = balanceTags($_POST['excerpt']);
		$excerpt = format_to_post($excerpt);
		$post_title = $_POST['post_title'];
		if(get_settings('use_geo_positions')) {
			$latf = floatval($_POST["post_latf"]);
				$lonf = floatval($_POST["post_lonf"]);
				$latlonaddition = "";
				if( ($latf != null) && ($latf <= 90 ) && ($latf >= -90) && ($lonf != null) && ($lonf <= 360) && ($lonf >= -360) ) {
						pingGeoUrl($post_ID);
				$latlonaddition = " post_lat=".$latf.", post_lon =".$lonf.", ";
				} else {
				$latlonaddition = " post_lat=null, post_lon=null, ";
			}
		} else {
			$latlonaddition = '';
		}
		$prev_status = $_POST['prev_status'];
		$post_status = $_POST['post_status'];
		$comment_status = $_POST['comment_status'];
		if (empty($comment_status)) $comment_status = 'closed';
		//if (!$_POST['comment_status']) $comment_status = get_settings('default_comment_status');

		$ping_status = $_POST['ping_status'];
		if (empty($ping_status)) $ping_status = 'closed';
		//if (!$_POST['ping_status']) $ping_status = get_settings('default_ping_status');
		$post_password = $_POST['post_password'];
		$post_name = $_POST['post_name'];
		if (empty($post_name)) {
		  $post_name = $post_title;
		}
		$post_name = sanitize_title($post_name, $post_ID);
		if (empty($post_name)) $post_name = sanitize_title($post_title);
		$trackback = $_POST['trackback_url'];
	// Format trackbacks
	$trackback = preg_replace('|\s+|', '\n', $trackback);
	
	if (isset($_POST['publish'])) $post_status = 'publish';

	if (($user_level > 4) && (!empty($_POST['edit_date']))) {
		$aa = $_POST['aa'];
		$mm = $_POST['mm'];
		$jj = $_POST['jj'];
		$hh = $_POST['hh'];
		$mn = $_POST['mn'];
		$ss = $_POST['ss'];
		$jj = ($jj > 31) ? 31 : $jj;
		$hh = ($hh > 23) ? $hh - 24 : $hh;
		$mn = ($mn > 59) ? $mn - 60 : $mn;
		$ss = ($ss > 59) ? $ss - 60 : $ss;
		$datemodif = ", post_date = '$aa-$mm-$jj $hh:$mn:$ss'";
	$datemodif_gmt = ", post_date_gmt = '".get_gmt_from_date("$aa-$mm-$jj $hh:$mn:$ss")."'";
	} else {
		$datemodif = '';
		$datemodif_gmt = '';
	}

	if ($_POST['save']) {
		$location = $_SERVER['HTTP_REFERER'];
	} elseif ($_POST['updatemeta']) {
		$location = $_SERVER['HTTP_REFERER'] . '&message=2#postcustom';
	} elseif ($_POST['deletemeta']) {
		$location = $_SERVER['HTTP_REFERER'] . '&message=3#postcustom';
	} elseif (isset($_POST['referredby']) && $_POST['referredby'] != $_SERVER['HTTP_REFERER']) {
		$location = $_POST['referredby'];
	} else {
		$location = 'post.php';
	}
	header ('Location: ' . $location); // Send user on their way while we keep working

$now = current_time('mysql');
$now_gmt = current_time('mysql', 1);

	$result = $wpdb->query("
		UPDATE $wpdb->posts SET
			post_content = '$content',
			post_excerpt = '$excerpt',
			post_title = '$post_title'"
			.$datemodif_gmt
			.$datemodif.","
			.$latlonaddition."
			
			post_status = '$post_status',
			comment_status = '$comment_status',
			ping_status = '$ping_status',
			post_password = '$post_password',
			post_name = '$post_name',
			to_ping = '$trackback',
			post_modified = '$now',
			post_modified_gmt = '$now_gmt'
		WHERE ID = $post_ID ");


	// Now it's category time!
	// First the old categories
	$old_categories = $wpdb->get_col("SELECT category_id FROM $wpdb->post2cat WHERE post_id = $post_ID");
	
	// Delete any?
	foreach ($old_categories as $old_cat) {
		if (!in_array($old_cat, $post_categories)) // If a category was there before but isn't now
			$wpdb->query("DELETE FROM $wpdb->post2cat WHERE category_id = $old_cat AND post_id = $post_ID LIMIT 1");
	}
	
	// Add any?
	foreach ($post_categories as $new_cat) {
		if (!in_array($new_cat, $old_categories))
			$wpdb->query("INSERT INTO $wpdb->post2cat (post_id, category_id) VALUES ($post_ID, $new_cat)");
	}
	
	if (isset($sleep_after_edit) && $sleep_after_edit > 0) {
		sleep($sleep_after_edit);
	}

	// are we going from draft/private to published?
	if ($prev_status != 'publish' && $post_status == 'publish') {
		if ($post_pingback) {
			pingback($content, $post_ID);
		}
	} // end if moving from draft/private to published
	if ($post_status == 'publish') {
		do_action('publish_post', $post_ID);
		// Trackback time.
		$to_ping = trim($wpdb->get_var("SELECT to_ping FROM $wpdb->posts WHERE ID = $post_ID"));
		$pinged = trim($wpdb->get_var("SELECT pinged FROM $wpdb->posts WHERE ID = $post_ID"));
		$pinged = explode("\n", $pinged);
		if ('' != $to_ping) {
			if (strlen($excerpt) > 0) {
				$the_excerpt = (strlen(strip_tags($excerpt)) > 255) ? substr(strip_tags($excerpt), 0, 252) . '...' : strip_tags($excerpt)	;
			} else {
				$the_excerpt = (strlen(strip_tags($content)) > 255) ? substr(strip_tags($content), 0, 252) . '...' : strip_tags($content);
			}
			$excerpt = stripslashes($the_excerpt);
			$to_pings = explode("\n", $to_ping);
			foreach ($to_pings as $tb_ping) {
				$tb_ping = trim($tb_ping);
				if (!in_array($tb_ping, $pinged)) {
				 trackback($tb_ping, stripslashes($post_title), $excerpt, $post_ID);
				}
			}
		}
	} // end if publish

	// Meta Stuff
	if ($_POST['meta']) :
		foreach ($_POST['meta'] as $key => $value) :
			update_meta($key, $value['key'], $value['value']);
		endforeach;
	endif;

	if ($_POST['deletemeta']) :
		foreach ($_POST['deletemeta'] as $key => $value) :
			delete_meta($key);
		endforeach;
	endif;

	add_meta($post_ID);

	do_action('edit_post', $post_ID);
	exit();
	break;

case 'delete':

	$standalone = 1;
	require_once('./admin-header.php');

	check_admin_referer();

	if ($user_level == 0)
		die ('Cheatin&#8217; uh?');

	$post_id = intval($_GET['post']);
	$postdata = get_postdata($post_id) or die(sprintf(__('Oops, no post with this ID. <a href="%s">Go back</a>!'), 'post.php'));
	$authordata = get_userdata($postdata['Author_ID']);

	if ($user_level < $authordata->user_level)
		die (sprintf(__('You don&#8217;t have the right to delete <strong>%s</strong>&#8217;s posts.'), $authordata[1]));

	// send geoURL ping to "erase" from their DB
	$query = "SELECT post_lat from $wpdb->posts WHERE ID=$post_id";
	$rows = $wpdb->query($query); 
	$myrow = $rows[0];
	$latf = $myrow->post_lat;
	if($latf != null ) {
		pingGeoUrl($post);
	}

	$result = $wpdb->query("DELETE FROM $wpdb->posts WHERE ID=$post_id");
	if (!$result)
		die(__('Error in deleting...'));

	$result = $wpdb->query("DELETE FROM $wpdb->comments WHERE comment_post_ID=$post_id");

	$categories = $wpdb->query("DELETE FROM $wpdb->post2cat WHERE post_id = $post_id");

    $meta = $wpdb->query("DELETE FROM $wpdb->postmeta WHERE post_id = $post_id");

	if (isset($sleep_after_edit) && $sleep_after_edit > 0) {
		sleep($sleep_after_edit);
	}

	$sendback = $_SERVER['HTTP_REFERER'];
	if (strstr($sendback, 'post.php')) $sendback = get_settings('siteurl') .'/wp-admin/post.php';
	header ('Location: ' . $sendback);
	do_action('delete_post', $post_id);
	break;

case 'editcomment':
	$title = __('Edit Comment');
	$standalone = 0;
	$parent_file = 'edit.php';
	require_once ('admin-header.php');

	get_currentuserinfo();

	if ($user_level == 0) {
		die (__('Cheatin&#8217; uh?'));
	}

	$comment = $_GET['comment'];
	$commentdata = get_commentdata($comment, 1, true) or die(sprintf(__('Oops, no comment with this ID. <a href="%s">Go back</a>!'), 'javascript:history.go(-1)'));
	$content = $commentdata['comment_content'];
	$content = format_to_edit($content);

	include('edit-form-comment.php');

	break;

case 'confirmdeletecomment':

$standalone = 0;
require_once('./admin-header.php');

if ($user_level == 0)
	die (__('Cheatin&#8217; uh?'));

$comment = $_GET['comment'];
$p = $_GET['p'];
$commentdata = get_commentdata($comment, 1, true) or die(sprintf(__('Oops, no comment with this ID. <a href="%s">Go back</a>!'), 'edit.php'));

echo "<div class=\"wrap\">\n";
echo "<p>" . __('<strong>Caution:</strong> You are about to delete the following comment:') . "</p>\n";
echo "<table border=\"0\">\n";
echo "<tr><td>" . __('Author:') . "</td><td>" . $commentdata["comment_author"] . "</td></tr>\n";
echo "<tr><td>" . __('E-mail:') . "</td><td>" . $commentdata["comment_author_email"] . "</td></tr>\n";
echo "<tr><td>". __('URL:') . "</td><td>" . $commentdata["comment_author_url"] . "</td></tr>\n";
echo "<tr><td>". __('Comment:') . "</td><td>" . stripslashes($commentdata["comment_content"]) . "</td></tr>\n";
echo "</table>\n";
echo "<p>" . __('Are you sure you want to do that?') . "</p>\n";

echo "<form action='".get_settings('siteurl')."/wp-admin/post.php' method='get'>\n";
echo "<input type=\"hidden\" name=\"action\" value=\"deletecomment\" />\n";
echo "<input type=\"hidden\" name=\"p\" value=\"$p\" />\n";
echo "<input type=\"hidden\" name=\"comment\" value=\"$comment\" />\n";
echo "<input type=\"hidden\" name=\"noredir\" value=\"1\" />\n";
echo "<input type=\"submit\" value=\"" . __('Yes') . "\" />";
echo "&nbsp;&nbsp;";
echo "<input type=\"button\" value=\"" . __('No') . "\" onClick=\"self.location='". get_settings('siteurl') ."/wp-admin/edit.php?p=$p&c=1#comments';\" />\n";
echo "</form>\n";
echo "</div>\n";

break;

case 'deletecomment':

$standalone = 1;
require_once('./admin-header.php');

check_admin_referer();

if ($user_level == 0)
	die (__('Cheatin&#8217; uh?'));


$comment = $_GET['comment'];
$p = $_GET['p'];
if (isset($_GET['noredir'])) {
	$noredir = true;
} else {
	$noredir = false;
}

$postdata = get_postdata($p) or die(sprintf(__('Oops, no post with this ID. <a href="%s">Go back</a>!'), 'edit.php'));
$commentdata = get_commentdata($comment, 1, true) or die(sprintf(__('Oops, no comment with this ID. <a href="%s">Go back</a>!'), 'post.php'));

$authordata = get_userdata($postdata['Author_ID']);
if ($user_level < $authordata->user_level)
	die (sprintf(__('You don&#8217;t have the right to delete <strong>%1$s</strong>&#8217;s post comments. <a href="%2$s">Go back</a>!'), $authordata->user_nickname, 'post.php'));

wp_set_comment_status($comment, "delete");
do_action('delete_comment', $comment);

if (($_SERVER['HTTP_REFERER'] != "") && (false == $noredir)) {
	header('Location: ' . $_SERVER['HTTP_REFERER']);
} else {
	header('Location: '. get_settings('siteurl') .'/wp-admin/edit.php?p='.$p.'&c=1#comments');
}

break;

case 'unapprovecomment':

$standalone = 1;
require_once('./admin-header.php');

check_admin_referer();

if ($user_level == 0)
	die (__('Cheatin&#8217; uh?'));
	
$comment = $_GET['comment'];
$p = $_GET['p'];
if (isset($_GET['noredir'])) {
	$noredir = true;
} else {
	$noredir = false;
}

$commentdata = get_commentdata($comment) or die(sprintf(__('Oops, no comment with this ID. <a href="%s">Go back</a>!'), 'edit.php'));

wp_set_comment_status($comment, "hold");

if (($_SERVER['HTTP_REFERER'] != "") && (false == $noredir)) {
	header('Location: ' . $_SERVER['HTTP_REFERER']);
} else {
	header('Location: '. get_settings('siteurl') .'/wp-admin/edit.php?p='.$p.'&c=1#comments');
}

break;

case 'mailapprovecomment':

$standalone = 0;
require_once('./admin-header.php');

if ($user_level == 0)
	die (__('Cheatin&#8217; uh?'));

$comment = $_GET['comment'];
$p = $_GET['p'];
$commentdata = get_commentdata($comment, 1, true) or die(sprintf(__('Oops, no comment with this ID. <a href="%s">Go back</a>!'), 'edit.php'));

wp_set_comment_status($comment, "approve");
if (get_settings("comments_notify") == true) {
	wp_notify_postauthor($comment);
}

echo "<div class=\"wrap\">\n";
echo "<p>" . __('Comment has been approved.') . "</p>\n";

echo "<form action=\"". get_settings('siteurl') ."/wp-admin/edit.php?p=$p&c=1#comments\" method=\"get\">\n";
echo "<input type=\"hidden\" name=\"p\" value=\"$p\" />\n";
echo "<input type=\"hidden\" name=\"c\" value=\"1\" />\n";
echo "<input type=\"submit\" value=\"" . __('Ok') . "\" />";
echo "</form>\n";
echo "</div>\n";

break;

case 'approvecomment':

$standalone = 1;
require_once('./admin-header.php');

if ($user_level == 0)
	die (__('Cheatin&#8217; uh?'));
	
$comment = $_GET['comment'];
$p = $_GET['p'];
if (isset($_GET['noredir'])) {
	$noredir = true;
} else {
	$noredir = false;
}
$commentdata = get_commentdata($comment) or die(sprintf(__('Oops, no comment with this ID. <a href="%s">Go back</a>!'), 'edit.php'));

wp_set_comment_status($comment, "approve");
if (get_settings("comments_notify") == true) {
	wp_notify_postauthor($comment);
}

 
if (($_SERVER['HTTP_REFERER'] != "") && (false == $noredir)) {
	header('Location: ' . $_SERVER['HTTP_REFERER']);
} else {
	header('Location: '. get_settings('siteurl') .'/wp-admin/edit.php?p='.$p.'&c=1#comments');
}

break;

case 'editedcomment':

	$standalone = 1;
	require_once('./admin-header.php');

	if ($user_level == 0)
		die (__('Cheatin&#8217; uh?'));

	$comment_ID = $_POST['comment_ID'];
	$comment_post_ID = $_POST['comment_post_ID'];
	$newcomment_author = $_POST['newcomment_author'];
	$newcomment_author_email = $_POST['newcomment_author_email'];
	$newcomment_author_url = $_POST['newcomment_author_url'];

	if (($user_level > 4) && (!empty($_POST['edit_date']))) {
		$aa = $_POST['aa'];
		$mm = $_POST['mm'];
		$jj = $_POST['jj'];
		$hh = $_POST['hh'];
		$mn = $_POST['mn'];
		$ss = $_POST['ss'];
		$jj = ($jj > 31) ? 31 : $jj;
		$hh = ($hh > 23) ? $hh - 24 : $hh;
		$mn = ($mn > 59) ? $mn - 60 : $mn;
		$ss = ($ss > 59) ? $ss - 60 : $ss;
		$datemodif = ", comment_date = '$aa-$mm-$jj $hh:$mn:$ss'";
	} else {
		$datemodif = '';
	}
	$content = balanceTags($_POST['content']);
	$content = format_to_post($content);

	$result = $wpdb->query("
		UPDATE $wpdb->comments SET
			comment_content = '$content',
			comment_author = '$newcomment_author',
			comment_author_email = '$newcomment_author_email',
			comment_author_url = '$newcomment_author_url'".$datemodif."
		WHERE comment_ID = $comment_ID"
		);

	$referredby = $_POST['referredby'];
	if (!empty($referredby)) header('Location: ' . $referredby);
	else header ("Location: edit.php?p=$comment_post_ID&c=1#comments");
	do_action('edit_comment', $comment_ID);
	break;

 case 'createpage':
	$standalone = 0;
	$title = __('Create New Page');
	require_once ('./admin-header.php');

	if ($user_level > 0) {
		$action = 'post';
		get_currentuserinfo();
		//set defaults
		$post_status = 'static';
		$comment_status = get_settings('default_comment_status');
		$ping_status = get_settings('default_ping_status');
		$post_pingback = get_settings('default_pingback_flag');

        include('edit-page-form.php');
	} else {
?>
<div class="wrap">
		<p><?php printf(__('Since you&#8217;re a newcomer, you&#8217;ll have to wait for an admin to raise your level to 1, in order to be authorized to post.<br />
You can also <a href="mailto:%s?subject=Promotion?">e-mail the admin</a> to ask for a promotion.<br />
When you&#8217;re promoted, just reload this page and you&#8217;ll be able to blog. :)'), get_settings('admin_email')); ?>
		</p>
</div>
<?php

	}

     break;

default:
	$standalone = 0;
	$title = __('Create New Post');
	require_once ('./admin-header.php');

	if ($user_level > 0) {
		$action = 'post';
		get_currentuserinfo();
		$drafts = $wpdb->get_results("SELECT ID, post_title FROM $wpdb->posts WHERE post_status = 'draft' AND post_author = $user_ID");
		if ($drafts) {
			?>
			<div class="wrap">
			<p><strong><?php _e('Your Drafts:') ?></strong>
			<?php
			$i = 0;
			foreach ($drafts as $draft) {
				if (0 != $i)
					echo ', ';
				$draft->post_title = stripslashes($draft->post_title);
				if ($draft->post_title == '')
					$draft->post_title = sprintf(__('Post # %s'), $draft->ID);
				echo "<a href='post.php?action=edit&amp;post=$draft->ID' title='" . __('Edit this draft') . "'>$draft->post_title</a>";
				++$i;
				}
			?>.</p>
			</div>
			<?php
		}
		//set defaults
		$post_status = 'draft';
		$comment_status = get_settings('default_comment_status');
		$ping_status = get_settings('default_ping_status');
		$post_pingback = get_settings('default_pingback_flag');

		if (get_settings('advanced_edit')) {
			include('edit-form-advanced.php');
		} else {
			include('edit-form.php');
		}
?>
<div class="wrap">
<?php _e('<h3>WordPress bookmarklet</h3>
<p>You can drag the following link to your links bar or add it to your bookmarks and when you "Press it" it will open up a popup window with information and a link to the site you&#8217;re currently browsing so you can make a quick post about it. Try it out:</p>') ?>
<p>

<?php
$bookmarklet_height= 420;

if ($is_NS4 || $is_gecko) {
?>
<a href="javascript:if(navigator.userAgent.indexOf('Safari') >= 0){Q=getSelection();}else{Q=document.selection?document.selection.createRange().text:document.getSelection();}void(window.open('<?php echo get_settings('siteurl') ?>/wp-admin/bookmarklet.php?text='+encodeURIComponent(Q)+'&amp;popupurl='+encodeURIComponent(location.href)+'&amp;popuptitle='+encodeURIComponent(document.title),'<?php _e('WordPress bookmarklet') ?>','scrollbars=yes,width=600,height=460,left=100,top=150,status=yes'));"><?php printf(__('Press It - %s'), htmlspecialchars(get_settings('blogname'))); ?></a> 
<?php
} else if ($is_winIE) {
?>
<a href="javascript:Q='';if(top.frames.length==0)Q=document.selection.createRange().text;void(btw=window.open('<?php echo get_settings('siteurl') ?>/wp-admin/bookmarklet.php?text='+encodeURIComponent(Q)+'<?php echo $bookmarklet_tbpb ?>&amp;popupurl='+encodeURIComponent(location.href)+'&amp;popuptitle='+encodeURIComponent(document.title),'bookmarklet','scrollbars=yes,width=600,height=<?php echo $bookmarklet_height ?>,left=100,top=150,status=yes'));btw.focus();"><?php printf(__('Press it - %s'), get_settings('blogname')); ?></a>
<script type="text/javascript">
<!--
function oneclickbookmarklet(blah) {
window.open ("profile.php?action=IErightclick", "oneclickbookmarklet", "width=500, height=450, location=0, menubar=0, resizable=0, scrollbars=1, status=1, titlebar=0, toolbar=0, screenX=120, left=120, screenY=120, top=120");
}
// -->
</script>
<br />
<br />
<?php _e('One-click bookmarklet:') ?><br />
<a href="javascript:oneclickbookmarklet(0);"><?php _e('click here') ?></a> 
<?php
} else if ($is_opera) {
?>
<a href="javascript:void(window.open('<?php echo get_settings('siteurl'); ?>/wp-admin/bookmarklet.php?popupurl='+escape(location.href)+'&popuptitle='+escape(document.title)+'<?php echo $bookmarklet_tbpb ?>','bookmarklet','scrollbars=yes,width=600,height=<?php echo $bookmarklet_height ?>,left=100,top=150,status=yes'));"><?php printf(__('Press it - %s'), get_settings('blogname')); ?></a> 
<?php
} else if ($is_macIE) {
?>
<a href="javascript:Q='';if(top.frames.length==0);void(btw=window.open('<?php echo get_settings('siteurl'); ?>/wp-admin/bookmarklet.php?text='+escape(document.getSelection())+'&popupurl='+escape(location.href)+'&popuptitle='+escape(document.title)+'<?php echo $bookmarklet_tbpb ?>','bookmarklet','scrollbars=yes,width=600,height=<?php echo $bookmarklet_height ?>,left=100,top=150,status=yes'));btw.focus();"><?php printf(__('Press it - %s'), get_settings('blogname')); ?></a> 
<?php
}
?>
</p>
</div>
<?php
	} else {


?>
<div class="wrap">
		<p><?php printf(__('Since you&#8217;re a newcomer, you&#8217;ll have to wait for an admin to raise your level to 1, in order to be authorized to post.<br />
You can also <a href="mailto:%s?subject=Promotion?">e-mail the admin</a> to ask for a promotion.<br />
When you&#8217;re promoted, just reload this page and you&#8217;ll be able to blog. :)'), get_settings('admin_email')); ?>
		</p>
</div>
<?php

	}

	break;
} // end switch
/* </Edit> */
include('admin-footer.php');
?>
