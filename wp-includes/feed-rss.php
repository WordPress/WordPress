<?php
/**
 * RSS 0.92 Feed Template for displaying RSS 0.92 Posts feed.
 *
 * @package WordPress
 */

header('Content-Type: ' . feed_content_type('rss') . '; charset=' . get_option('blog_charset'), true);
$more = 1;

echo '<?xml version="1.0" encoding="'.get_option('blog_charset').'"?'.'>'; ?>
<rss version="0.92">
<channel>
	<title><?php wp_title_rss(); ?></title>
	<link><?php bloginfo_rss('url') ?></link>
	<description><?php bloginfo_rss('description') ?></description>
	<lastBuildDate><?php
		$date = get_lastpostmodified( 'GMT' );
		echo $date ? mysql2date( 'D, d M Y H:i:s +0000', $date ) : date( 'D, d M Y H:i:s +0000' );
	?></lastBuildDate>
	<docs>http://backend.userland.com/rss092</docs>
	<language><?php bloginfo_rss( 'language' ); ?></language>

	<?php
	/**
	 * Fires at the end of the RSS Feed Header.
	 *
	 * @since 2.0.0
	 */
	do_action( 'rss_head' );
	?>

<?php while (have_posts()) : the_post(); ?>
	<item>
		<title><?php the_title_rss() ?></title>
		<description><![CDATA[<?php the_excerpt_rss() ?>]]></description>
		<link><?php the_permalink_rss() ?></link>
		<?php
		/**
		 * Fires at the end of each RSS feed item.
		 *
		 * @since 2.0.0
		 */
		do_action( 'rss_item' );
		?>
	</item>
<?php endwhile; ?>
</channel>
</rss>
