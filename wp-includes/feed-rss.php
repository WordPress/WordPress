<?php
header('Content-Type: text/xml; charset=' . get_option('blog_charset'), true);
$more = 1;

?>
<?php echo '<?xml version="1.0" encoding="'.get_option('blog_charset').'"?'.'>'; ?>
<!-- generator="wordpress/<?php echo $wp_version ?>" -->
<rss version="0.92">
<channel>
	<title><?php bloginfo_rss('name'); wp_title_rss(); ?></title>
	<link><?php bloginfo_rss('url') ?></link>
	<description><?php bloginfo_rss('description') ?></description>
	<lastBuildDate><?php echo mysql2date('D, d M Y H:i:s +0000', get_lastpostmodified('GMT'), false); ?></lastBuildDate>
	<docs>http://backend.userland.com/rss092</docs>
	<language><?php echo get_option('rss_language'); ?></language>
	<?php do_action('rss_head'); ?>

<?php while (have_posts()) : the_post(); ?>
	<item>
		<title><?php the_title_rss() ?></title>
<?php if (get_option('rss_use_excerpt')) { ?>
		<description><![CDATA[<?php the_excerpt_rss() ?>]]></description>
<?php } else { // use content ?>
		<description><?php the_content_rss('', 0, '', get_option('rss_excerpt_length')) ?></description>
<?php } ?>
		<link><?php the_permalink_rss() ?></link>
		<?php do_action('rss_item'); ?>
	</item>
<?php endwhile; ?>
</channel>
</rss>
