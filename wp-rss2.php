<?php 
if (! $feed) {
    $blog = 1;
    $doing_rss = 1;
    require('wp-blog-header.php');
}
$more = 1;
$charset = get_settings('blog_charset');
if (!$charset) $charset = 'UTF-8';
header('Content-type: text/xml', true);

?>
<?php echo '<?xml version="1.0" encoding="' . $charset . '"?'.'>'; ?>

<!-- generator="wordpress/<?php echo $wp_version ?>" -->
<rss version="2.0" 
	xmlns:content="http://purl.org/rss/1.0/modules/content/">

<channel>
	<title><?php bloginfo_rss('name') ?></title>
	<link><?php bloginfo_rss('url') ?></link>
	<description><?php bloginfo_rss("description") ?></description>
	<copyright>Copyright <?php echo mysql2date('Y', get_lastpostdate()); ?></copyright>
	<pubDate><?php echo mysql2date('D, d M Y H:i:s +0000', get_lastpostmodified('GMT')); ?></pubDate>
	<generator>http://wordpress.org/?v=<?php echo $wp_version ?></generator>

	<?php $items_count = 0; if ($posts) { foreach ($posts as $post) { start_wp(); ?>
	<item>
		<title><?php the_title_rss() ?></title>
		<link><?php permalink_single_rss() ?></link>
		<comments><?php comments_link(); ?></comments>
		<pubDate><?php echo mysql2date('D, d M Y H:i:s +0000', $post->post_date_gmt); ?></pubDate>
		<?php the_category_rss() ?>
		<guid><?php the_permalink($id); ?></guid>
<?php if (get_settings('rss_use_excerpt')) : ?>
		<description><?php the_excerpt_rss(get_settings('rss_excerpt_length'), 2) ?></description>
<?php else : ?>
		<description><?php the_content_rss('', 0, '', get_settings('rss_excerpt_length'), 2) ?></description>
		<content:encoded><![CDATA[<?php the_content('', 0, '') ?>]]></content:encoded>
<?php endif; ?>
		
	</item>
	<?php $items_count++; if (($items_count == get_settings('posts_per_rss')) && empty($m)) { break; } } } ?>
</channel>
</rss>