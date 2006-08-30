<?php /* RDF 1.0 generator, original version by garym@teledyn.com */

if (empty($wp)) {
	require_once('wp-config.php');
	wp('feed=rdf');
}

header('Content-type: application/rdf+xml; charset=' . get_option('blog_charset'), true);
$more = 1;

?>
<?php echo '<?xml version="1.0" encoding="'.get_option('blog_charset').'"?'.'>'; ?>
<!-- generator="wordpress/<?php echo $wp_version ?>" -->
<rdf:RDF
	xmlns="http://purl.org/rss/1.0/"
	xmlns:rdf="http://www.w3.org/1999/02/22-rdf-syntax-ns#"
	xmlns:dc="http://purl.org/dc/elements/1.1/"
	xmlns:sy="http://purl.org/rss/1.0/modules/syndication/"
	xmlns:admin="http://webns.net/mvcb/"
	xmlns:content="http://purl.org/rss/1.0/modules/content/"
	<?php do_action('rdf_ns'); ?>
>
<channel rdf:about="<?php bloginfo_rss("url") ?>">
	<title><?php bloginfo_rss('name') ?></title>
	<link><?php bloginfo_rss('url') ?></link>
	<description><?php bloginfo_rss('description') ?></description>
	<dc:date><?php echo mysql2date('Y-m-d\TH:i:s\Z', get_lastpostmodified('GMT'), false); ?></dc:date>
	<admin:generatorAgent rdf:resource="http://wordpress.org/?v=<?php echo $wp_version ?>"/>
	<sy:updatePeriod>hourly</sy:updatePeriod>
	<sy:updateFrequency>1</sy:updateFrequency>
	<sy:updateBase>2000-01-01T12:00+00:00</sy:updateBase>
	<?php do_action('rdf_header'); ?>
	<items>
		<rdf:Seq>
		<?php while (have_posts()): the_post(); ?>
			<rdf:li rdf:resource="<?php permalink_single_rss() ?>"/>
		<?php endwhile; ?>
		</rdf:Seq>
	</items>
</channel>
<?php rewind_posts(); while (have_posts()): the_post(); ?>
<item rdf:about="<?php permalink_single_rss() ?>">
	<title><?php the_title_rss() ?></title>
	<link><?php permalink_single_rss() ?></link>
	 <dc:date><?php echo mysql2date('Y-m-d\TH:i:s\Z', $post->post_date_gmt, false); ?></dc:date>
	<dc:creator><?php the_author() ?></dc:creator>
	<?php the_category_rss('rdf') ?>
<?php if (get_option('rss_use_excerpt')) : ?>
	<description><?php the_excerpt_rss() ?></description>
<?php else : ?>
	<description><?php the_content_rss('', 0, '', get_option('rss_excerpt_length'), 2) ?></description>
	<content:encoded><![CDATA[<?php the_content('', 0, '') ?>]]></content:encoded>
<?php endif; ?>
	<?php do_action('rdf_item'); ?>
</item>
<?php endwhile;  ?>
</rdf:RDF>
