<?php 
if (! $feed) {
    $blog = 1;
    $doing_rss = 1;
    require('wp-blog-header.php');
}

header('Content-type: application/rss+xml', true);

/* This doesn't take into account edits
// Get the time of the most recent article
$maxdate = $wpdb->get_var("SELECT max(post_date) FROM $tableposts");
$unixtime = strtotime($maxdate);

// format timestamp for Last-Modified header
$clast = gmdate("D, d M Y H:i:s \G\M\T", $unixtime);
$cetag = (isset($clast)) ? md5($clast) : '';

// send it in a Last-Modified header
header("Last-Modified: " . $clast, true);
header("Etag: " . $cetag, true);
*/

?>
<?php echo '<?xml version="1.0" encoding="'.get_settings('blog_charset').'"?'.'>'; ?>
<!-- generator="wordpress/<?php echo $wp_version ?>" -->
<rss version="2.0" 
	xmlns:content="http://purl.org/rss/1.0/modules/content/">

<channel>
	<title><?php bloginfo_rss('name') ?></title>
	<link><?php bloginfo_rss('url') ?></link>
	<description><?php bloginfo_rss("description") ?></description>
	<language><?php echo $rss_language ?></language>
	<copyright>Copyright <?php echo mysql2date('Y', get_lastpostdate()); ?></copyright>
	<pubDate><?php echo gmdate('r'); ?></pubDate>
	<generator>http://wordpress.org/?v=<?php echo $wp_version ?></generator>

	<?php $items_count = 0; if ($posts) { foreach ($posts as $post) { start_wp(); ?>
	<item>
		<title><?php the_title_rss() ?></title>
		<link><?php permalink_single_rss() ?></link>
		<comments><?php comments_link(); ?></comments>
		<pubDate><?php the_time('r'); ?></pubDate>
		<author><?php the_author() ?> (mailto:<?php the_author_email() ?>)</author>
		<?php the_category_rss() ?>
		<guid isPermaLink="false"><?php echo $id; ?>@<?php bloginfo_rss("url") ?></guid>
<?php $more = 1; if (get_settings('rss_use_excerpt')) {
?>
		<description><?php the_excerpt_rss(get_settings('rss_excerpt_length'), 2) ?></description>
<?php
} else { // use content
?>
		<description><?php the_content_rss('', 0, '', get_settings('rss_excerpt_length'), 2) ?></description>
<?php
} // end else use content
?>
		<content:encoded><![CDATA[<?php the_content('', 0, '') ?>]]></content:encoded>
	</item>
	<?php $items_count++; if (($items_count == get_settings('posts_per_rss')) && empty($m)) { break; } } } ?>
</channel>
</rss>