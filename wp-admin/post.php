<?php
/* <Edit> */

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
    $HTTP_GET_VARS    = add_magic_quotes($HTTP_GET_VARS);
    $HTTP_POST_VARS   = add_magic_quotes($HTTP_POST_VARS);
    $HTTP_COOKIE_VARS = add_magic_quotes($HTTP_COOKIE_VARS);
}

$wpvarstoreset = array('action', 'safe_mode', 'withcomments', 'c', 'posts', 'poststart', 'postend', 'content', 'edited_post_title', 'comment_error', 'profile', 'trackback_url', 'excerpt', 'showcomments', 'commentstart', 'commentend', 'commentorder');

for ($i=0; $i<count($wpvarstoreset); $i += 1) {
    $wpvar = $wpvarstoreset[$i];
    if (!isset($$wpvar)) {
        if (empty($HTTP_POST_VARS["$wpvar"])) {
            if (empty($HTTP_GET_VARS["$wpvar"])) {
                $$wpvar = '';
            } else {
                $$wpvar = $HTTP_GET_VARS["$wpvar"];
            }
        } else {
            $$wpvar = $HTTP_POST_VARS["$wpvar"];
        }
    }
}

switch($action) {

    case 'post':

			$standalone = 1;
			require_once('admin-header.php');

			$post_pingback = intval($HTTP_POST_VARS['post_pingback']);
			$content = balanceTags($HTTP_POST_VARS['content']);
			$content = format_to_post($content);
			$excerpt = balanceTags($HTTP_POST_VARS['excerpt']);
			$excerpt = format_to_post($excerpt);
			$post_title = addslashes($HTTP_POST_VARS['post_title']);
			$post_categories = $HTTP_POST_VARS['post_category'];
			if(get_settings('use_geo_positions')) {
				$latstr = $HTTP_POST_VARS['post_latf'];
				$lonstr = $HTTP_POST_VARS['post_lonf'];
				if((strlen($latstr) > 2) && (strlen($lonstr) > 2 ) ) {
					$post_latf = floatval($HTTP_POST_VARS['post_latf']);
					$post_lonf = floatval($HTTP_POST_VARS['post_lonf']);
				}
			}
			$post_status = $HTTP_POST_VARS['post_status'];
			$comment_status = $HTTP_POST_VARS['comment_status'];
			$ping_status = $HTTP_POST_VARS['ping_status'];
			$post_password = addslashes($HTTP_POST_VARS['post_password']);
			$post_name = sanitize_title($post_title);

        if ($user_level == 0)
            die ('Cheatin&#8217; uh?');

        if (($user_level > 4) && (!empty($HTTP_POST_VARS['edit_date']))) {
            $aa = $HTTP_POST_VARS['aa'];
            $mm = $HTTP_POST_VARS['mm'];
            $jj = $HTTP_POST_VARS['jj'];
            $hh = $HTTP_POST_VARS['hh'];
            $mn = $HTTP_POST_VARS['mn'];
            $ss = $HTTP_POST_VARS['ss'];
            $jj = ($jj > 31) ? 31 : $jj;
            $hh = ($hh > 23) ? $hh - 24 : $hh;
            $mn = ($mn > 59) ? $mn - 60 : $mn;
            $ss = ($ss > 59) ? $ss - 60 : $ss;
            $now = "$aa-$mm-$jj $hh:$mn:$ss";
        } else {
            $now = date('Y-m-d H:i:s', (time() + ($time_difference * 3600)));
        }
		
		if ('' != $HTTP_POST_VARS['save']) $post_status = 'draft';

        if((get_settings('use_geo_positions')) && (strlen($latstr) > 2) && (strlen($lonstr) > 2) ) {
		$postquery ="INSERT INTO $tableposts
                (ID, post_author, post_date, post_content, post_title, post_lat, post_lon, post_excerpt,  post_status, comment_status, ping_status, post_password, post_name)
                VALUES
                ('0', '$user_ID', '$now', '$content', '$post_title', $post_latf, $post_lonf,'$excerpt', '$post_status', '$comment_status', '$ping_status', '$post_password', '$post_name')
                ";
        } else {
		$postquery ="INSERT INTO $tableposts
                (ID, post_author, post_date, post_content, post_title, post_excerpt,  post_status, comment_status, ping_status, post_password, post_name)
                VALUES
                ('0', '$user_ID', '$now', '$content', '$post_title', '$excerpt', '$post_status', '$comment_status', '$ping_status', '$post_password', '$post_name')
                ";
        }
        $postquery =
        $result = $wpdb->query($postquery);

        $post_ID = $wpdb->get_var("SELECT ID FROM $tableposts ORDER BY ID DESC LIMIT 1");

		// Insert categories
		// Check to make sure there is a category, if not just set it to some default
		if (!$post_categories) $post_categories = $wpdb->get_var("SELECT cat_ID FROM $tablecategories LIMIT 1");
		foreach ($post_categories as $post_category) {
			// Double check it's not there already
			$exists = $wpdb->get_row("SELECT * FROM $tablepost2cat WHERE post_id = $post_ID AND category_id = $post_category");

			 if (!$exists && $result) { 
			 	$wpdb->query("
				INSERT INTO $tablepost2cat
				(post_id, category_id)
				VALUES
				($post_ID, $post_category)
				");
			}
		}
		
        if (isset($sleep_after_edit) && $sleep_after_edit > 0) {
                sleep($sleep_after_edit);
        }

        if (!empty($HTTP_POST_VARS['mode'])) {
            switch($HTTP_POST_VARS['mode']) {
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
		
		if ('' != $HTTP_POST_VARS['save']) $location = "post.php?action=edit&post=$post_ID";
        header("Location: $location");

        if ($post_status == 'publish') {
            if((get_settings('use_geo_positions')) && ($post_latf != null) && ($post_lonf != null)) {
                pingGeoUrl($post_ID);
            }
            pingWeblogs($blog_ID);
            pingBlogs($blog_ID);

            if ($post_pingback) {
                pingback($content, $post_ID);
            }

            if (!empty($HTTP_POST_VARS['trackback_url'])) {
				if (strlen($excerpt) > 0) {
					$the_excerpt = (strlen(strip_tags($excerpt)) > 255) ? substr(strip_tags($excerpt), 0, 252) . '...' : strip_tags($excerpt)	;
				} else {
					$the_excerpt = (strlen(strip_tags($content)) > 255) ? substr(strip_tags($content), 0, 252) . '...' : strip_tags($content);
				}
                $excerpt = stripslashes($the_excerpt);
                $trackback_urls = explode(',', $HTTP_POST_VARS['trackback_url']);
                foreach($trackback_urls as $tb_url) {
                    $tb_url = trim($tb_url);
                    trackback($tb_url, stripslashes($post_title), $excerpt, $post_ID);
                }
            }
        } // end if publish

        exit();
        break;

    case 'edit':
        $title = 'Edit';

        $standalone = 0;
        require_once('admin-header.php');

        $post = $HTTP_GET_VARS['post'];
        if ($user_level > 0) {
			$postdata = get_postdata($post);
			$authordata = get_userdata($postdata['Author_ID']);
			if ($user_level < $authordata->user_level)
				die ('You don&#8217;t have the right to edit <strong>'.$authordata[1].'</strong>&#8217;s posts.');

			$content = $postdata['Content'];
			$content = format_to_edit($content);
			$edited_lat = $postdata["Lat"];
			$edited_lon = $postdata["Lon"];
			$excerpt = $postdata['Excerpt'];
			$excerpt = format_to_edit($excerpt);
			$edited_post_title = format_to_edit($postdata['Title']);
			$post_status = $postdata['post_status'];
			$comment_status = $postdata['comment_status'];
			$ping_status = $postdata['ping_status'];
			$post_password = $postdata['post_password'];

            include('edit-form.php');
        } else {
?>
            <p>Since you&#8217;re a newcomer, you&#8217;ll have to wait for an admin to raise your level to 1,
            in order to be authorized to post.<br />
            You can also <a href="mailto:<?php echo $admin_email ?>?subject=Promotion?">e-mail the admin</a>
            to ask for a promotion.<br />
            When you&#8217;re promoted, just reload this page and you&#8217;ll be able to blog. :)
            </p>
<?php
        }
        break;

    case 'editpost':

        $standalone = 1;
        require_once('./admin-header.php');

        if ($user_level == 0)
            die ('Cheatin&#8217; uh?');

        if (!isset($blog_ID)) {
            $blog_ID = 1;
        }
			$post_ID = $HTTP_POST_VARS['post_ID'];
			$post_categories = $HTTP_POST_VARS['post_category'];
			$post_autobr = intval($HTTP_POST_VARS['post_autobr']);
			$content = balanceTags($HTTP_POST_VARS['content']);
			$content = format_to_post($content);
			$excerpt = balanceTags($HTTP_POST_VARS['excerpt']);
			$excerpt = format_to_post($excerpt);
			$post_title = addslashes($HTTP_POST_VARS['post_title']);
			if(get_settings('use_geo_positions')) {
				$latf = floatval($HTTP_POST_VARS["post_latf"]);
        			$lonf = floatval($HTTP_POST_VARS["post_lonf"]);
        			$latlonaddition = "";
        			if( ($latf != null) && ($latf <= 90 ) && ($latf >= -90) && ($lonf != null) && ($lonf <= 360) && ($lonf >= -360) ) {
                			pingGeoUrl($post_ID);
					$latlonaddition = " post_lat=".$latf.", post_lon =".$lonf.", ";
        			} else {
					$latlonaddition = " post_lat=null, post_lon=null, ";
				}
			}
			$post_status = $HTTP_POST_VARS['post_status'];
			$prev_status = $HTTP_POST_VARS['prev_status'];
			$comment_status = $HTTP_POST_VARS['comment_status'];
			$ping_status = $HTTP_POST_VARS['ping_status'];
			$post_password = addslashes($HTTP_POST_VARS['post_password']);
			$post_name = sanitize_title($post_title);

        if (($user_level > 4) && (!empty($HTTP_POST_VARS['edit_date']))) {
            $aa = $HTTP_POST_VARS['aa'];
            $mm = $HTTP_POST_VARS['mm'];
            $jj = $HTTP_POST_VARS['jj'];
            $hh = $HTTP_POST_VARS['hh'];
            $mn = $HTTP_POST_VARS['mn'];
            $ss = $HTTP_POST_VARS['ss'];
            $jj = ($jj > 31) ? 31 : $jj;
            $hh = ($hh > 23) ? $hh - 24 : $hh;
            $mn = ($mn > 59) ? $mn - 60 : $mn;
            $ss = ($ss > 59) ? $ss - 60 : $ss;
            $datemodif = ", post_date=\"$aa-$mm-$jj $hh:$mn:$ss\"";
        } else {
            $datemodif = '';
        }

        $result = $wpdb->query("
			UPDATE $tableposts SET
				post_content = '$content',
				post_excerpt = '$excerpt',
				post_title = '$post_title'"
				.$datemodif.","
				.$latlonaddition."
				post_status = '$post_status',
				comment_status = '$comment_status',
				ping_status = '$ping_status',
				post_password = '$post_password',
				post_name = '$post_name'
			WHERE ID = $post_ID ");


		// Now it's category time!
		// First the old categories
		$old_categories = $wpdb->get_col("SELECT category_id FROM $tablepost2cat WHERE post_id = $post_ID");
		
		// Delete any?
		foreach ($old_categories as $old_cat) {
			if (!in_array($old_cat, $post_categories)) // If a category was there before but isn't now
				$wpdb->query("DELETE FROM $tablepost2cat WHERE category_id = $old_cat AND post_id = $post_ID LIMIT 1");
		}
		
		// Add any?
		foreach ($post_categories as $new_cat) {
			if (!in_array($new_cat, $old_categories))
				$wpdb->query("INSERT INTO $tablepost2cat (post_id, category_id) VALUES ($post_ID, $new_cat)");
		}
		
        if (isset($sleep_after_edit) && $sleep_after_edit > 0) {
            sleep($sleep_after_edit);
        }

        // are we going from draft/private to published?
        if ((($prev_status == 'draft') || ($prev_status == 'private')) && ($post_status == 'publish')) {
            pingWeblogs($blog_ID);
            pingBlogs($blog_ID);

            if ($post_pingback) {
                pingback($content, $post_ID);
            }

            if (!empty($HTTP_POST_VARS['trackback_url'])) {
                $excerpt = (strlen(strip_tags($content)) > 255) ? substr(strip_tags($content), 0, 252) . '...' : strip_tags($content);
                $excerpt = stripslashes($excerpt);
                $trackback_urls = explode(',', $HTTP_POST_VARS['trackback_url']);
                foreach($trackback_urls as $tb_url) {
                    $tb_url = trim($tb_url);
                    trackback($tb_url, stripslashes($post_title), $excerpt, $post_ID);
                }
            }
        } // end if publish

        $location = "Location: post.php";
        header ($location);
        break;

    case 'delete':

        $standalone = 1;
        require_once('./admin-header.php');

        if ($user_level == 0)
            die ('Cheatin&#8217; uh?');

        $post_id = $HTTP_GET_VARS['post'];
        $postdata = get_postdata($post) or die('Oops, no post with this ID. <a href="post.php">Go back</a>!');
        $authordata = get_userdata($postdata['Author_ID']);

        if ($user_level < $authordata->user_level)
            die ('You don&#8217;t have the right to delete <strong>'.$authordata[1].'</strong>&#8217;s posts.');

        // send geoURL ping to "erase" from their DB
        $query = "SELECT post_lat from $tableposts WHERE ID=$post_id";
        $rows = $wpdb->query($query); 
        $myrow = $rows[0];
        $latf = $myrow->post_lat;
        if($latf != null ) {
            pingGeoUrl($post);
        }

        $result = $wpdb->query("DELETE FROM $tableposts WHERE ID=$post_id");
        if (!$result)
            die('Error in deleting... contact the <a href="mailto:$admin_email">webmaster</a>.');

        $result = $wpdb->query("DELETE FROM $tablecomments WHERE comment_post_ID=$post_id");

		$categories = $wpdb->query("DELETE FROM $tablepost2cat WHERE post_id = $post_id");

        if (isset($sleep_after_edit) && $sleep_after_edit > 0) {
            sleep($sleep_after_edit);
        }

        // pingWeblogs($blog_ID);
		$sendback = $HTTP_SERVER_VARS['HTTP_REFERER'];
		if (strstr($sendback, 'post.php')) $sendback = $siteurl .'/wp-admin/post.php';
        header ('Location: ' . $sendback);

        break;

    case 'editcomment':
        $title = 'Edit Comment';
        $standalone = 0;
        require_once ('admin-header.php');

        get_currentuserinfo();

        if ($user_level == 0) {
            die ('Cheatin&#8217; uh?');
        }

        $comment = $HTTP_GET_VARS['comment'];
        $commentdata = get_commentdata($comment, 1, true) or die('Oops, no comment with this ID. <a href="javascript:history.go(-1)">Go back</a>!');
        $content = $commentdata['comment_content'];
        $content = format_to_edit($content);

        include('edit-form.php');

        break;

    case 'confirmdeletecomment':
    
	$standalone = 0;
	require_once('./admin-header.php');
	
	if ($user_level == 0)
		die ('Cheatin&#8217; uh?');
	
	$comment = $HTTP_GET_VARS['comment'];
	$p = $HTTP_GET_VARS['p'];
	$commentdata = get_commentdata($comment, 1, true) or die('Oops, no comment with this ID. <a href="edit.php">Go back</a>!');
	
	echo "<div class=\"wrap\">\n";
	echo "<p><strong>Caution:</strong> You are about to delete the following comment:</p>\n";
	echo "<table border=\"0\">\n";
	echo "<tr><td>Author:</td><td>" . $commentdata["comment_author"] . "</td></tr>\n";
	echo "<tr><td>E-Mail:</td><td>" . $commentdata["comment_author_email"] . "</td></tr>\n";
	echo "<tr><td>URL:</td><td>" . $commentdata["comment_author_url"] . "</td></tr>\n";
	echo "<tr><td>Comment:</td><td>" . stripslashes($commentdata["comment_content"]) . "</td></tr>\n";
	echo "</table>\n";
	echo "<p>Are you sure you want to do that?</p>\n";
	
	echo "<form action=\"$siteurl/wp-admin/post.php\" method=\"get\">\n";
	echo "<input type=\"hidden\" name=\"action\" value=\"deletecomment\" />\n";
	echo "<input type=\"hidden\" name=\"p\" value=\"$p\" />\n";
	echo "<input type=\"hidden\" name=\"comment\" value=\"$comment\" />\n";
	echo "<input type=\"hidden\" name=\"noredir\" value=\"1\" />\n";
	echo "<input type=\"submit\" value=\"Yes\" />";
	echo "&nbsp;&nbsp;";
	echo "<input type=\"button\" value=\"No\" onClick=\"self.location='$siteurl/wp-admin/edit.php?p=$p&c=1#comments';\" />\n";
	echo "</form>\n";
	echo "</div>\n";
	
	break;

    case 'deletecomment':

	$standalone = 1;
	require_once('./admin-header.php');

	if ($user_level == 0)
		die ('Cheatin&#8217; uh?');


	$comment = $HTTP_GET_VARS['comment'];
	$p = $HTTP_GET_VARS['p'];
	if (isset($HTTP_GET_VARS['noredir'])) {
	    $noredir = true;
	} else {
	    $noredir = false;
	}
	
	$postdata = get_postdata($p) or die('Oops, no post with this ID. <a href="edit.php">Go back</a>!');
	$commentdata = get_commentdata($comment, 1, true) or die('Oops, no comment with this ID. <a href="post.php">Go back</a>!');

	$authordata = get_userdata($postdata['Author_ID']);
	if ($user_level < $authordata->user_level)
		die ('You don&#8217;t have the right to delete <strong>'.$authordata->user_nickname.'</strong>&#8217;s post comments. <a href="post.php">Go back</a>!');

	wp_set_comment_status($comment, "delete");

	if (($HTTP_SERVER_VARS['HTTP_REFERER'] != "") && (false == $noredir)) {
		header('Location: ' . $HTTP_SERVER_VARS['HTTP_REFERER']);
	} else {
		header('Location: '.$siteurl.'/wp-admin/edit.php?p='.$p.'&c=1#comments');
	}

	break;
	
    case 'unapprovecomment':
	
	$standalone = 1;
	require_once('./admin-header.php');
	
	if ($user_level == 0)
		die ('Cheatin&#8217; uh?');
		
	$comment = $HTTP_GET_VARS['comment'];
	$p = $HTTP_GET_VARS['p'];
	if (isset($HTTP_GET_VARS['noredir'])) {
	    $noredir = true;
	} else {
	    $noredir = false;
	}

	$commentdata = get_commentdata($comment) or die('Oops, no comment with this ID. <a href="edit.php">Go back</a>!');
	
	wp_set_comment_status($comment, "hold");
	
	if (($HTTP_SERVER_VARS['HTTP_REFERER'] != "") && (false == $noredir)) {
		header('Location: ' . $HTTP_SERVER_VARS['HTTP_REFERER']);
	} else {
		header('Location: '.$siteurl.'/wp-admin/edit.php?p='.$p.'&c=1#comments');
	}
	
	break;
	
    case 'mailapprovecomment':
    
	$standalone = 0;
	require_once('./admin-header.php');
	
	if ($user_level == 0)
		die ('Cheatin&#8217; uh?');
	
	$comment = $HTTP_GET_VARS['comment'];
	$p = $HTTP_GET_VARS['p'];
	$commentdata = get_commentdata($comment, 1, true) or die('Oops, no comment with this ID. <a href="edit.php">Go back</a>!');

	wp_set_comment_status($comment, "approve");
	if (get_settings("comments_notify") == true) {
		wp_notify_postauthor($comment);
	}
	
	echo "<div class=\"wrap\">\n";
	echo "<p>Comment has been approved.</p>\n";
	
	echo "<form action=\"$siteurl/wp-admin/edit.php?p=$p&c=1#comments\" method=\"get\">\n";
	echo "<input type=\"hidden\" name=\"p\" value=\"$p\" />\n";
	echo "<input type=\"hidden\" name=\"c\" value=\"1\" />\n";
	echo "<input type=\"submit\" value=\"Ok\" />";
	echo "</form>\n";
	echo "</div>\n";
	
	break;

    case 'approvecomment':
    
	$standalone = 1;
	require_once('./admin-header.php');
	
	if ($user_level == 0)
		die ('Cheatin&#8217; uh?');
		
	$comment = $HTTP_GET_VARS['comment'];
	$p = $HTTP_GET_VARS['p'];
	if (isset($HTTP_GET_VARS['noredir'])) {
	    $noredir = true;
	} else {
	    $noredir = false;
	}
	$commentdata = get_commentdata($comment) or die('Oops, no comment with this ID. <a href="edit.php">Go back</a>!');
	
	wp_set_comment_status($comment, "approve");
	if (get_settings("comments_notify") == true) {
		wp_notify_postauthor($comment);
	}
	
	 
	if (($HTTP_SERVER_VARS['HTTP_REFERER'] != "") && (false == $noredir)) {
		header('Location: ' . $HTTP_SERVER_VARS['HTTP_REFERER']);
	} else {
		header('Location: '.$siteurl.'/wp-admin/edit.php?p='.$p.'&c=1#comments');
	}
	
	break;
	
    case 'editedcomment':

        $standalone = 1;
        require_once('./admin-header.php');

        if ($user_level == 0)
            die ('Cheatin&#8217; uh?');

        $comment_ID = $HTTP_POST_VARS['comment_ID'];
        $comment_post_ID = $HTTP_POST_VARS['comment_post_ID'];
        $newcomment_author = $HTTP_POST_VARS['newcomment_author'];
        $newcomment_author_email = $HTTP_POST_VARS['newcomment_author_email'];
        $newcomment_author_url = $HTTP_POST_VARS['newcomment_author_url'];
        $newcomment_author = addslashes($newcomment_author);
        $newcomment_author_email = addslashes($newcomment_author_email);
        $newcomment_author_url = addslashes($newcomment_author_url);

        if (($user_level > 4) && (!empty($HTTP_POST_VARS['edit_date']))) {
            $aa = $HTTP_POST_VARS['aa'];
            $mm = $HTTP_POST_VARS['mm'];
            $jj = $HTTP_POST_VARS['jj'];
            $hh = $HTTP_POST_VARS['hh'];
            $mn = $HTTP_POST_VARS['mn'];
            $ss = $HTTP_POST_VARS['ss'];
            $jj = ($jj > 31) ? 31 : $jj;
            $hh = ($hh > 23) ? $hh - 24 : $hh;
            $mn = ($mn > 59) ? $mn - 60 : $mn;
            $ss = ($ss > 59) ? $ss - 60 : $ss;
            $datemodif = ", comment_date = '$aa-$mm-$jj $hh:$mn:$ss'";
        } else {
            $datemodif = '';
        }
        $content = balanceTags($content);
        $content = format_to_post($content);

        $result = $wpdb->query("
			UPDATE $tablecomments SET
				comment_content = '$content',
				comment_author = '$newcomment_author',
				comment_author_email = '$newcomment_author_email',
				comment_author_url = '$newcomment_author_url'".$datemodif."
			WHERE comment_ID = $comment_ID"
			);

		$referredby = $HTTP_POST_VARS['referredby'];
		if (!empty($referredby)) header('Location: ' . $referredby);
        else header ("Location: edit.php?p=$comment_post_ID&c=1#comments");

        break;

    default:
		$title = 'Create New Post';
        $standalone = 0;
        require_once ('./admin-header.php');

        if ($user_level > 0) {
            if ((!$withcomments) && (!$c)) {

				$action = 'post';
				get_currentuserinfo();
				$drafts = $wpdb->get_results("SELECT ID, post_title FROM $tableposts WHERE post_status = 'draft' AND post_author = $user_ID");
				if ($drafts) {
					?>
					<div class="wrap">
					<p><strong>Your Drafts:</strong>
					<?php
					$i = 0;
					foreach ($drafts as $draft) {
						if (0 != $i)
                            echo ', ';
						$draft->post_title = stripslashes($draft->post_title);
                        if ($draft->post_title == '')
                            $draft->post_title = 'Post #'.$draft->ID;
						echo "<a href='post.php?action=edit&amp;post=$draft->ID' title='Edit this draft'>$draft->post_title</a>";
						++$i;
						}
					?>.</p>
					</div>
					<?php
				}
                //set defaults
                $post_status = get_settings('default_post_status');
                $comment_status = get_settings('default_comment_status');
                $ping_status = get_settings('default_ping_status');
                $post_pingback = get_settings('default_pingback_flag');
                $default_post_cat = get_settings('default_post_category');
                include('edit-form.php');
            }

        } else {


?>
<div class="wrap">
            <p>Since you&#8217;re a newcomer, you&#8217;ll have to wait for an admin to raise your level to 1, in order to be authorized to post.<br />
				You can also <a href="mailto:<?php echo $admin_email ?>?subject=b2-promotion">e-mail the admin</a> to ask for a promotion.<br />
				When you&#8217;re promoted, just reload this page and you&#8217;ll be able to blog. :)</p>
</div>
<?php

        }

        break;
} // end switch
/* </Edit> */
include('admin-footer.php');
?>
