<div class="wrap">
<?php

switch($action) {
	case 'post':
		$submitbutton_text = 'Blog this!';
		$toprow_title = 'New Post';
		$form_action = 'post';
		$form_extra = '';
		if ($use_pingback) {
			$form_pingback = '<input type="checkbox" class="checkbox" name="post_pingback" value="1" checked="checked" tabindex="7" id="pingback" /> <label for="pingback">PingBack the URLs in this post</label><br />';
		} else {
			$form_pingback = '';
		}
		if ($use_trackback) {
			$form_trackback = '<p><label for="trackback"><strong>TrackBack</strong> an <acronym title="Uniform Resource Locator">URL</acronym>:</label> (Seperate multiple URLs with commas.)<br /><input type="text" name="trackback_url" style="width: 415px" id="trackback" /></p>';
		} else {
			$form_trackback = '';
		}
		$colspan = 3;
		break;
	case "edit":
		$submitbutton_text = 'Edit this!';
		$toprow_title = 'Editing Post #' . $postdata["ID"];
		$form_action = 'editpost';
		$form_extra = "' />\n<input type='hidden' name='post_ID' value='$post";
		$colspan = 2;
		$form_pingback = '<input type="hidden" name="post_pingback" value="0" />';
		$form_trackback = '';
		break;
	case "editcomment":
		$submitbutton_text = 'Edit this!';
		$toprow_title = 'Editing Comment # '.$commentdata["comment_ID"];
		$form_action = 'editedcomment';
		$form_extra = "' />\n<input type='hidden' name='comment_ID' value='$comment' />\n<input type='hidden' name='comment_post_ID' value='".$commentdata["comment_post_ID"];
		$colspan = 3;
		$form_pingback = '<input type="hidden" name="post_pingback" value="0" />';
		$form_trackback = '';
		break;
}

?>

<form name="post" action="b2edit.php" method="POST">
<input type="hidden" name="user_ID" value="<?php echo $user_ID ?>" />
<input type="hidden" name="action" value='<?php echo $form_action . $form_extra ?>' />

<?php if ($action != "editcomment") {
  // this is for everything but comment editing
?> 
      <table>
	  	<tr>
        <td width="210">
          <label for="title">Title:</label><br />
          <input type="text" name="post_title" size="25" tabindex="1" style="width: 190px;" value="<?php echo $edited_post_title; ?>" id="title" />
        </td>
        <td>
          <label for="category">Category :</label>
              <br /><?php dropdown_categories(); ?>
        </td>
		</tr>
      </table>
  <?php

} else {
  
// this is for comment editing
?>
<table>
	<tr>
	<td>
	<label for="name">Name:</label>
        <br />
	<input type="text" name="newcomment_author" size="22" value="<?php echo format_to_edit($commentdata["comment_author"]) ?>" tabindex="1" id="name" /></td>
	<td>
	<label for="email">E-mail:</label>
        <br />
	<input type="text" name="newcomment_author_email" size="30" value="<?php echo format_to_edit($commentdata["comment_author_email"]) ?>" tabindex="2" id="email" /></td>
	<td>
	<label for="URL">URL:</label>
        <br />
	<input type="text" name="newcomment_author_url" size="35" value="<?php echo format_to_edit($commentdata["comment_author_url"]) ?>" tabindex="3" id="URL" /></td>
	</tr>
</table>
	<?php
  
} // end else comment editing

	?>

<?php
if ($action != 'editcomment') {
  echo '<label for="excerpt">Excerpt:</label>';
?>
<p><textarea rows="3" cols="40" style="width:100%" name="excerpt" tabindex="4" wrap="virtual" id="excerpt"><?php echo $excerpt ?></textarea></p>

<?php
} // if not a comment
?>
<table width="100%">
	<tr>
		<td>
<?php
if ($action != 'editcomment') {
	echo '<label for="content">Post:</label>';
} else {
	echo '<br /><label for="content">Comment:</label>';
}
?>
		</td>
		<td align="right">
<?php if ($use_quicktags) {
	include('b2quicktags.php');
	}
?>
		</td>
	</tr>
</table>
<textarea rows="9" cols="40" style="width:100%" name="content" tabindex="4" wrap="virtual" id="content"><?php echo $content ?></textarea><br />

<?php echo $form_pingback ?>

<p><input type="submit" name="submit" value="<?php echo $submitbutton_text ?>" class="search" style="font-weight: bold;" tabindex="5" /></p>


<?php if ( ($use_fileupload) && ($user_level >= $fileupload_minlevel) && ((ereg(" ".$user_login." ", $fileupload_allowedusers)) || (trim($fileupload_allowedusers)=="")) ) { ?>
<input type="button" value="upload a file/image" onclick="launchupload();" class="search"  tabindex="10" />
<?php }

echo $form_trackback;

// if the level is 5+, allow user to edit the timestamp - not on 'new post' screen though
// if (($user_level > 4) && ($action != "post"))
if ($user_level > 4) {
	touch_time(($action == 'edit'));
}
?>

</form>
</div>