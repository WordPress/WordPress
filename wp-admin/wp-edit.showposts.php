<?php

require_once('../wp-config.php');

if (!$showposts) {
	if ($posts_per_page) {
		$showposts=$posts_per_page;
	} else {
		$showposts=10;
		$posts_per_page=$showposts;
	}
} else {
	$posts_per_page = $showposts;
}

if ((!empty($poststart)) && (!empty($postend)) && ($poststart == $postend)) {
	$p=$poststart;
	$poststart=0;
	$postend=0;
}

if (!$poststart) {
	$poststart=0;
	$postend=$showposts;
}

$nextXstart=$postend;
$nextXend=$postend+$showposts;

$previousXstart=($poststart-$showposts);
$previousXend=$poststart;
if ($previousXstart < 0) {
	$previousXstart=0;
	$previousXend=$showposts;
}

ob_start();
?>

<h2 id="posts">Posts</h2>

<p class="anchors">Go to: <a href="wp-post.php#top">Post/Edit</a> | <a href="wp-post.php#posts">Posts</a> | <a href="wp-post.php#comments">Comments</a></p>

<div class="wrap">
<table width="100%">
  <tr>
    <td valign="top" width="200">
      Show posts:
    </td>
    <td>
      <table cellpadding="0" cellspacing="0" border="0">
        <tr>
          <td colspan="2" align="center"><!-- show next/previous X posts -->
            <form name="previousXposts" method="get" action="">
<?php
if ($previousXstart > 0) {
?>
              <input type="hidden" name="showposts" value="<?php echo $showposts; ?>" />
              <input type="hidden" name="poststart" value="<?php echo $previousXstart; ?>" />
              <input type="hidden" name="postend" value="<?php echo $previousXend; ?>" />
              <input type="submit" name="submitprevious" class="search" value="< <?php echo $showposts ?>" />
<?php
}
?>
            </form>
          </td>
          <td>
            <form name="nextXposts" method="get" action="">
              <input type="hidden" name="showposts" value="<?php echo $showposts; ?>" />
              <input type="hidden" name="poststart" value="<?php echo $nextXstart; ?>" />
              <input type="hidden" name="postend" value="<?php echo $nextXend; ?>" />
              <input type="submit" name="submitnext" class="search" value="<?php echo $showposts ?> >" />
            </form>
          </td>
        </tr>
      </table>
    </td>
  </tr>
  <tr>
    <td valign="top" width="200"><!-- show X first/last posts -->
      <form name="showXfirstlastposts" method="get" action="">
        <input type="text" name="showposts" value="<?php echo $showposts ?>" style="width:40px;" /?>
<?php
if (!isset($order))
  $order="DESC";
$i = $order;
if ($i == "DESC")
 $besp_selected = "selected='selected'";
?>
        <select name="order">
          <option value="DESC" <?php echo $besp_selected ?>>last posts</option>
<?php
$besp_selected = "";
if ($i == "ASC")
$besp_selected = "selected='selected'";
?>
          <option value="ASC" <?php echo $besp_selected?>>first posts</option>
        </select>&nbsp;
        <input type="submit" name="submitfirstlast" class="search" value="OK" />
      </form>
    </td>
    <td valign="top"><!-- show post X to post X -->
      <form name="showXfirstlastposts" method="get" action="">
        <input type="text" name="poststart" value="<?php echo $poststart ?>" style="width:40px;" /?>&nbsp;to&nbsp;<input type="text" name="postend" value="<?php echo $postend ?>" style="width:40px;" /?>&nbsp;
        <select name="order">
<?php
$besp_selected = "";
$i = $order;
if ($i == "DESC")
  $besp_selected = "selected='selected'";
?>
          <option value="DESC" "<?php echo $besp_selected ?>">from the end</option>
<?php
$besp_selected = "";
if ($i == "ASC")
  $besp_selected = "selected='selected'";
?>        <option value="ASC" "<?php echo $besp_selected ?>">from the start</option>
        </select>&nbsp;
        <input type="submit" name="submitXtoX" class="search" value="OK" />
      </form>
    </td>
  </tr>
</table>
</div>

<?php
$posts_nav_bar = ob_get_contents();
ob_end_clean();
echo $posts_nav_bar;
?>

<div class="wrap">
<table width="100%">
  <tr>
	<td valign="top" width="33%">
		<form name="searchform" action="wp-post.php" method="get">
			<input type="hidden" name="a" value="s" />
			<input onfocus="this.value='';" onblur="if (this.value=='') {this.value='search...';}" type="text" name="s" value="search..." size="7" style="width: 100px;" />
			<input type="submit" name="submit" value="search" class="search" />
		</form>
	</td>
    <td valign="top" width="33%" align="center">
	  <form name="viewcat" action="wp-post.php" method="get">
		<select name="cat" style="width:140px;">
		<option value="all">All Categories</option>
		<?php
	$categories = $wpdb->get_results("SELECT * FROM $tablecategories");
	$querycount++;
	$width = ($mode=="sidebar") ? "100%" : "170px";
	foreach ($categories as $category) {
		echo "<option value=\"".$category->cat_ID."\"";
		if ($category->cat_ID == $postdata["Category"])
			echo " selected='selected'";
		echo ">".$category->cat_name."</option>";
	}
		?>
		</select>
		<input type="submit" name="submit" value="View" class="search" />
	  </form>
    </td>
    <td valign="top" width="33%" align="right">
    <form name="viewarc" action="wp-post.php" method="get">
	<?php

	if ($archive_mode == "monthly") {
		echo "<select name=\"m\" style=\"width:120px;\">";
		$querycount++;
		$arc_result=$wpdb->get_results("SELECT DISTINCT YEAR(post_date), MONTH(post_date) FROM $tableposts ORDER BY post_date DESC",ARRAY_A);
		foreach ($arc_result as $arc_row) {
			$arc_year  = $arc_row["YEAR(post_date)"];
			$arc_month = $arc_row["MONTH(post_date)"];
			echo "<option value=\"$arc_year".zeroise($arc_month,2)."\">";
			echo $month[zeroise($arc_month,2)]." $arc_year";
			echo "</option>\n";
		}
	} elseif ($archive_mode == "daily") {
		echo "<select name=\"d\" style=\"width:120px;\">";
		$archive_day_date_format = "Y/m/d";
		$querycount++;
		$arc_result=$wpdb->get_results("SELECT DISTINCT YEAR(post_date), MONTH(post_date), DAYOFMONTH(post_date) FROM $tableposts ORDER BY post_date DESC", ARRAY_A);
		foreach ($arc_result as $arc_row) {
			$arc_year  = $arc_row["YEAR(post_date)"];
			$arc_month = $arc_row["MONTH(post_date)"];
			$arc_dayofmonth = $arc_row["DAYOFMONTH(post_date)"];
			echo "<option value=\"$arc_year".zeroise($arc_month,2).zeroise($arc_dayofmonth,2)."\">";
			echo mysql2date($archive_day_date_format, $arc_year.zeroise($arc_month,2).zeroise($arc_dayofmonth,2)." 00:00:00");
			echo "</option>\n";
		}
	} elseif ($archive_mode == "weekly") {
		echo "<select name=\"w\" style=\"width:120px;\">";
		if (!isset($start_of_week)) {
			$start_of_week = 1;
		}
		$archive_week_start_date_format = "Y/m/d";
		$archive_week_end_date_format   = "Y/m/d";
		$archive_week_separator = " - ";
		$querycount++;
		$arc_result=$wpdb->geT_results("SELECT DISTINCT YEAR(post_date), MONTH(post_date), DAYOFMONTH(post_date), WEEK(post_date) FROM $tableposts ORDER BY post_date DESC", ARRAY_A);
		$arc_w_last = '';
        foreach ($arc_result as $arc_row) {
			$arc_year = $arc_row["YEAR(post_date)"];
			$arc_w = $arc_row["WEEK(post_date)"];
			if ($arc_w != $arc_w_last) {
				$arc_w_last = $arc_w;
				$arc_ymd = $arc_year."-".zeroise($arc_row["MONTH(post_date)"],2)."-" .zeroise($arc_row["DAYOFMONTH(post_date)"],2);
				$arc_week = get_weekstartend($arc_ymd, $start_of_week);
				$arc_week_start = date($archive_week_start_date_format, $arc_week['start']);
				$arc_week_end = date($archive_week_end_date_format, $arc_week['end']);
				echo "<option value=\"$arc_w\">";
				echo $arc_week_start.$archive_week_separator.$arc_week_end;
				echo "</option>\n";
			}
		}
	} elseif ($archive_mode == "postbypost") {
		echo '<input type="hidden" name="more" value="1" />';
		echo '<select name="p" style="width:120px;">';
        $querycount++;
		$resultarc = $wpdb->get_results("SELECT ID,post_date,post_title FROM $tableposts ORDER BY post_date DESC");
		foreach ($resultarc as $row) {
			if ($row->post_date != "0000-00-00 00:00:00") {
				echo "<option value=\"".$row->ID."\">";
				if (strip_tags($row->post_title)) {
					echo strip_tags(stripslashes($row->post_title));
				} else {
					echo $row->ID;
				}
				echo "</option>\n";
			}
		}
	}

	echo "</select>";
	?>
	    <input type="submit" name="submit" value="View" class="search" />
      </form>
    </td>
  </tr>
</table>

	<?php
	// these lines are b2's "motor", do not alter nor remove them
	include($abspath.'blog.header.php');

	if ($posts) {
	foreach ($posts as $post) {
        //$posts_per_page = 10;
        start_b2(); ?>
			<p>
				<strong><?php the_time('Y/m/d @ H:i:s'); ?></strong> [ <a href="wp-post.php?p=<?php echo $id ?>&c=1"><?php comments_number('no comments', '1 comment', "% comments", true) ?></a>
				<?php
				if (($user_level > $authordata->user_level) or ($user_login == $authordata->user_login)) {
				echo " - <a href='wp-post.php?action=edit&amp;post=$id";
				if ($m)
				echo "&m=$m";
				echo "'>Edit</a>";
				echo " - <a href='wp-post.php?action=delete&amp;post=$id' onclick=\"return confirm('You are about to delete this post \'".the_title('','',0)."\'\\n  \'Cancel\' to stop, \'OK\' to delete.')\">Delete</a> ";
				}
				if ('private' == $post->post_status) echo ' - <strong>Private</strong>';
				?>
				]
				<br />
				<font color="#999999"><b><a href="<?php permalink_single($siteurl.'/'.$blogfilename); ?>" title="permalink"><?php the_title() ?></a></b> by <b><?php the_author() ?> (<a href="javascript:profile(<?php the_author_ID() ?>)"><?php the_author_nickname() ?></a>)</b>, in <b><?php the_category() ?></b></font><br />
				<?php permalink_anchor(); ?>
				<?php
				the_content();
				?>
			</p>
				<?php

				// comments
				if (($withcomments) or ($c)) {

					$comments = $wpdb->get_results("SELECT * FROM $tablecomments WHERE comment_post_ID = $id ORDER BY comment_date");
                    ++$querycount;
					if ($comments) {
					?>

					<h3>Comments</h3>
					<ol id="comments">
						<?php
						foreach ($comments as $comment) {
						?>
				
					<!-- comment -->
					<li>
							<?php comment_date('Y/m/d') ?> @ <?php comment_time() ?> 
							<?php 
							if (($user_level > $authordata->user_level) or ($user_login == $authordata->user_login)) {
								echo "[ <a href=\"wp-post.php?action=editcomment&amp;comment=".$comment->comment_ID."\">Edit</a>";
								echo " - <a href=\"wp-post.php?action=deletecomment&amp;p=".$post->ID."&amp;comment=".$comment->comment_ID."\" onclick=\"return confirm('You are about to delete this comment by \'".$comment->comment_author."\'\\n  \'Cancel\' to stop, \'OK\' to delete.')\">Delete</a> ";
								if ( ('none' != get_settings("comment_moderation")) && ($user_level >= 3) ) {
									if ('approved' == wp_get_comment_status($comment->comment_ID)) {
										echo " - <a href=\"b2edit.php?action=unapprovecomment&amp;p=".$post->ID."&amp;comment=".$comment->comment_ID."\">Unapprove</a> ";
									} else {
										echo " - <a href=\"b2edit.php?action=approvecomment&amp;p=".$post->ID."&amp;comment=".$comment->comment_ID."\">Approve</a> ";
									}
								}
								echo " ]";
							} // end if any comments to show
							?>
						<br />
						<strong><?php comment_author() ?> ( <?php comment_author_email_link() ?> / <?php comment_author_url_link() ?> )</strong> (IP: <?php comment_author_IP() ?>)
							<?php comment_text() ?>
					</li>
					<!-- /comment -->

						<?php //end of the loop, don't delete
						} // end foreach
					echo '</ol>';
					}//end if comments
					if ($comment_error)
						echo "<p>Error: please fill the required fields (name & comment)</p>";
					?>

					<h3>Leave Comment</h3>


					<!-- form to add a comment -->

					<form action="<?php echo $siteurl.'/b2comments.post.php'?>" method="post">
						<input type="hidden" name="comment_post_ID" value="<?php echo $id; ?>" />
						<input type="hidden" name="redirect_to" value="<?php echo $HTTP_SERVER_VARS["REQUEST_URI"]; ?>" />
						<input type="text" name="author" class="textarea" value="<?php echo $user_nickname ?>" size="20" tabindex="1" /><br />
						<input type="text" name="email" class="textarea" value="<?php echo $user_email ?>" size="20" tabindex="2" /><br />
						<input type="text" name="url" class="textarea" value="<?php echo $user_url ?>" size="20" tabindex="3" /><br />
						<textarea cols="40" rows="4" name="comment" tabindex="4" class="textarea">comment</textarea><br />
						<input type="submit" name="submit" class="buttonarea" value="ok" tabindex="5" />
					</form>
					<!-- /form -->

					<?php // if you delete this the sky will fall on your head
				}
				?>
			<br />

	<?php

	} // end b2 loop
	
	} else {

		?>
		<p>
		<strong>No results found.</strong>
		</p>
		
		<?php
	} // end if ($posts)

	?>

</div>

<?php 
// uncomment this to show the nav bar at the bottom as well
// echo $posts_nav_bar; 
?>