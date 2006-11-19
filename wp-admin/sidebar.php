<?php
$mode = 'sidebar';

require_once('admin.php');

if ( ! current_user_can('edit_posts') )
	wp_die(__('Cheatin&#8217; uh?'));

if ('b' == $_GET['a']) {

?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>WordPress &#8250; Posted</title>
<meta http-equiv="Content-Type" content="<?php bloginfo('html_type'); ?>; charset=UTF-8" />
<link rel="stylesheet" href="wp-admin.css" type="text/css" />
</head>
<body>
	<p>Posted !</p>
	<p><a href="sidebar.php">Click here</a> to post again.</p>
</body>
</html><?php

} else {

?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>WordPress &#8250; Sidebar</title>
<meta http-equiv="Content-Type" content="<?php bloginfo('html_type'); ?>; charset=<?php bloginfo('blog_charset'); ?>" />
<link rel="stylesheet" href="wp-admin.css" type="text/css" />
<style type="text/css" media="screen">
form {
	padding: 3px;
}
.sidebar-categories {
	display: block;
	height: 6.6em;
	overflow: auto;
	background-color: #f4f4f4;
}
.sidebar-categories label {
	font-size: 10px;
	display: block;
	width: 90%;
}
</style>
</head>
<body id="sidebar">
<h1 id="wphead"><a href="http://wordpress.org/" rel="external">WordPress</a></h1>
<form name="post" action="post.php" method="post">
<div><input type="hidden" name="action" value="post" />
<input type="hidden" name="user_ID" value="<?php echo $user_ID ?>" />
<input type="hidden" name="mode" value="sidebar" />
<p>Title:
<input type="text" name="post_title" size="20" tabindex="1" style="width: 100%;" />
</p>
<p>Categories:
<span class="sidebar-categories">
<?php dropdown_categories(); ?>
</span>
</p>
<p>
Post:
<textarea rows="8" cols="12" style="width: 100%" name="content" tabindex="2"></textarea>
</p>
<p>
	<input name="saveasdraft" type="submit" id="saveasdraft" tabindex="9" value="Save as Draft" />
<?php if ( current_user_can('publish_posts') ) : ?>
	<input name="publish" type="submit" id="publish" tabindex="6" style="font-weight: bold;" value="Publish" />
<?php endif; ?>
</p>
</div>
</form>

</body>
</html>
<?php
}
?>