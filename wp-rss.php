<?php

if (empty($feed)) {
    $blog = 1;
		$feed = 'rss';
    $doing_rss = 1;
    require('wp-blog-header.php');
}

header('Content-type: text/xml; charset=' . get_settings('blog_charset'), true);
$more = 1;

?>
<?php echo '<?xml version="1.0" encoding="'.get_settings('blog_charset').'"?'.'>'; ?>
<!-- generator="wordpress/<?php echo $wp_version ?>" -->
<rss version="0.92">
<channel>
	<title><?php bloginfo_rss('name') ?></title>
	<link><?php bloginfo_rss('url') ?></link>
	<description><?php bloginfo_rss('description') ?></description>
	<lastBuildDate><?php echo mysql2date('D, d M Y H:i:s +0000', get_lastpostmodified('GMT'), false); ?></lastBuildDate>
	<docs>http://backend.userland.com/rss092</docs>
	<language><?php echo get_option('rss_language'); ?></language>
	<?php do_action('rss_head'); ?>

<?php $items_count = 0; if ($posts) { foreach ($posts as $post) { start_wp(); ?>
	<item>
		<title><?php the_title_rss() ?></title>
<?php if (get_settings('rss_use_excerpt')) { ?>
		<description><![CDATA[<?php the_excerpt_rss() ?>]]></description>
<?php } else { // use content ?>
		<description><?php the_content_rss('', 0, '', get_settings('rss_excerpt_length')) ?></description>
<?php } ?>
		<link><?php permalink_single_rss() ?></link>
		<?php do_action('rss_item'); ?>
	</item>
<?php $items_count++; if (($items_count == get_settings('posts_per_rss')) && empty($m)) { break; } } } ?>
</channel>
</rss>
