<?php
/**
 * Quick way to create a WordPress Post.
 *
 * @package WordPress
 * @subpackage Administration
 */

/**
 * @var string
 * @name $mode
 */
$mode = 'sidebar';

/** WordPress Administration Bootstrap */
require_once('admin.php');

if ( ! current_user_can('edit_posts') )
	wp_die(__('Cheatin&#8217; uh?'));

$post = get_default_post_to_edit();

?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="<?php bloginfo('html_type'); ?>; charset=<?php bloginfo('blog_charset'); ?>" />
<title><?php bloginfo('name') ?> &rsaquo; <?php _e('Sidebar'); ?></title>
<style type="text/css" media="screen">
body {
	font-size: 0.9em;
	margin: 0;
	padding: 0;
}
form {
	padding: 1%;
}
.tags-wrap p {
	font-size: 0.75em;
	margin-top: 0.4em;
}
.button-highlighted, #wphead, label {
	font-weight: bold;
}
#post-title, #tags-input, #content {
	width: 99%;
	padding: 2px;
}
#wphead {
	font-size: 1.4em;
	background-color: #E4F2FD;
	color: #555555;
	padding: 0.2em 1%;
}
#wphead p {
	margin: 3px;
}
.button {
	font-family: "Lucida Grande", "Lucida Sans Unicode", Tahoma, Verdana, sans-serif;
	padding: 3px 5px;
	margin-right: 5px;
	font-size: 0.75em;
	line-height: 1.5em;
	border: 1px solid #80b5d0;
	-moz-border-radius: 3px;
	-khtml-border-radius: 3px;
	-webkit-border-radius: 3px;
	border-radius: 3px;
	cursor: pointer;
	background-color: #e5e5e5;
	color: #246;
}
.button:hover {
	border-color: #535353;
}
.updated {
	background-color: #FFFBCC;
	border: 1px solid #E6DB55;
	margin-bottom: 1em;
	padding: 0 0.6em;
}
.updated p {
	margin: 0.6em;
}
</style>
</head>
<body id="sidebar">
<div id="wphead"><p><?php bloginfo('name') ?> &rsaquo; <?php _e('Sidebar'); ?></p></div>
<form name="post" action="post.php" method="post">
<div>
<input type="hidden" name="action" value="post" />
<input type="hidden" name="user_ID" value="<?php echo $user_ID ?>" />
<input type="hidden" name="mode" value="sidebar" />
<input type="hidden" name="ping_status" value="<?php echo $post->ping_status; ?>" />
<input type="hidden" name="comment_status" value="<?php echo $post->comment_status; ?>" />
<?php wp_nonce_field('add-post');

if ( 'b' == $_GET['a'] )
	echo '<div class="updated"><p>' . __('Post published.') . '</p></div>';
elseif ( 'c' == $_GET['a'] )
	echo '<div class="updated"><p>' . __('Post saved.') . '</p></div>';
?>
<p>
<label for="post-title"><?php _e('Title:'); ?></label>
<input type="text" name="post_title" id="post-title" size="20" tabindex="1" autocomplete="off" value="" />
</p>

<p>
<label for="content"><?php _e('Post:'); ?></label>
<textarea rows="8" cols="12" name="content" id="content" style="height:10em;line-height:1.4em;" tabindex="2"></textarea>
</p>

<div class="tags-wrap">
<label for="tags-input"><?php _e('Tags:') ?></label>
<input type="text" name="tags_input" id="tags-input" tabindex="3" value="" />
<p><?php _e('Separate tags with commas'); ?></p>
</div>

<p>
<input name="saveasdraft" type="submit" id="saveasdraft" tabindex="9" accesskey="s" class="button" value="<?php _e('Save as Draft'); ?>" />
<?php if ( current_user_can('publish_posts') ) : ?>
<input name="publish" type="submit" id="publish" tabindex="6" accesskey="p" value="<?php _e('Publish') ?>" class="button button-highlighted" />
<?php endif; ?>
</p>
</div>
</form>

</body>
</html>
