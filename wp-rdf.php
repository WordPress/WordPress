<?php /* RDF 1.0 generator, original version by garym@teledyn.com */

if (empty($feed)) {
    $blog = 1; // enter your blog's ID
    $doing_rss = 1;
    require('wp-blog-header.php');
}

header('Content-type: application/rdf+xml; charset=' . get_settings('blog_charset'), true);
$more = 1;

?>
<?php echo '<?xml version="1.0" encoding="'.get_settings('blog_charset').'"?'.'>'; ?>
<!-- generator="wordpress/<?php echo $wp_version ?>" -->
<rdf:RDF
	xmlns="http://purl.org/rss/1.0/"
	xmlns:rdf="http://www.w3.org/1999/02/22-rdf-syntax-ns#"
	xmlns:dc="http://purl.org/dc/elements/1.1/"
	xmlns:sy="http://purl.org/rss/1.0/modules/syndication/"
	xmlns:admin="http://webns.net/mvcb/"
	xmlns:content="http://purl.org/rss/1.0/modules/content/"
>
<channel rdf:about="<?php bloginfo_rss("url") ?>">
	<title><?php bloginfo_rss('name') ?></title>
	<link><?php bloginfo_rss('url') ?></link>
	<description><?php bloginfo_rss('description') ?></description>
	<dc:date><?php echo mysql2date('Y-m-d\TH:i:s\Z', get_lastpostmodified('GMT')); ?></dc:date>
	<admin:generatorAgent rdf:resource="http://wordpress.org/?v=<?php echo $wp_version ?>"/>
	<sy:updatePeriod>hourly</sy:updatePeriod>
	<sy:updateFrequency>1</sy:updateFrequency>
	<sy:updateBase>2000-01-01T12:00+00:00</sy:updateBase>
	<items>
		<rdf:Seq>
		<?php $items_count = 0; if ($posts) { foreach ($posts as $post) { start_wp(); ?>
			<rdf:li rdf:resource="<?php permalink_single_rss() ?>"/>
		<?php $wp_items[] = $row; $items_count++; if (($items_count == get_settings('posts_per_rss')) && empty($m)) { break; } } } ?>
		</rdf:Seq>
	</items>
</channel>
<?php if ($posts) { foreach ($posts as $post) { start_wp(); ?>
<item rdf:about="<?php permalink_single_rss() ?>">
	<title><?php the_title_rss() ?></title>
	<link><?php permalink_single_rss() ?></link>
	<dc:date><?php echo mysql2date('Y-m-d\TH:i:s\Z', $post->post_date_gmt); ?></dc:date>
	<dc:creator><?php the_author() ?></dc:creator>
	<?php the_category_rss('rdf') ?>
<?php if (get_settings('rss_use_excerpt')) : ?>
	<description><?php the_excerpt_rss() ?></description>
<?php else : ?>
	<description><?php the_content_rss('', 0, '', get_settings('rss_excerpt_length'), 2) ?></description>
	<content:encoded><![CDATA[<?php the_content('', 0, '') ?>]]></content:encoded>
<?php endif; ?>
	
</item>
<?php } }  ?>
</rdf:RDF>