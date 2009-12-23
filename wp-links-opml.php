<?php
/**
 * Outputs the OPML XML format for getting the links defined in the link
 * administration. This can be used to export links from one blog over to
 * another. Links aren't exported by the WordPress export, so this file handles
 * that.
 *
 * This file is not added by default to WordPress theme pages when outputting
 * feed links. It will have to be added manually for browsers and users to pick
 * up that this file exists.
 *
 * @package WordPress
 */

if (empty($wp)) {
	require_once('./wp-load.php');
	wp();
}

header('Content-Type: text/xml; charset=' . get_option('blog_charset'), true);
$link_cat = $_GET['link_cat'];
if ((empty ($link_cat)) || ($link_cat == 'all') || ($link_cat == '0')) {
	$link_cat = '';
} else { // be safe
	$link_cat = '' . urldecode($link_cat) . '';
	$link_cat = intval($link_cat);
}
?><?php echo '<?xml version="1.0"?'.">\n"; ?>
<?php the_generator( 'comment' ); ?>
<opml version="1.0">
	<head>
		<title>Links for <?php echo esc_attr(get_bloginfo('name', 'display').$cat_name); ?></title>
		<dateCreated><?php echo gmdate("D, d M Y H:i:s"); ?> GMT</dateCreated>
	</head>
	<body>
<?php

if (empty ($link_cat))
	$cats = get_categories(array('type' => 'link', 'hierarchical' => 0));
else
	$cats = get_categories(array('type' => 'link', 'hierarchical' => 0, 'include' => $link_cat));

foreach ((array) $cats as $cat) {
	$catname = apply_filters('link_category', $cat->name);

?>
<outline type="category" title="<?php echo esc_attr($catname); ?>">
<?php

	$bookmarks = get_bookmarks(array("category" => $cat->term_id));
	foreach ((array) $bookmarks as $bookmark) {
		$title = esc_attr(apply_filters('link_title', $bookmark->link_name));
?>
	<outline text="<?php echo $title; ?>" type="link" xmlUrl="<?php echo esc_attr($bookmark->link_rss); ?>" htmlUrl="<?php echo esc_attr($bookmark->link_url); ?>" updated="<?php if ('0000-00-00 00:00:00' != $bookmark->link_updated) echo $bookmark->link_updated; ?>" />
<?php

	}
?>
</outline>
<?php

}
?>
</body>
</opml>
