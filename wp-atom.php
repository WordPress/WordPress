<?php

if (empty($feed)) {
    $blog = 1;
		$feed = 'atom';
    $doing_rss = 1;
    require('wp-blog-header.php');
}

header('Content-type: application/atom+xml; charset=' . get_settings('blog_charset'), true);
$more = 1;

?>
<?php echo '<?xml version="1.0" encoding="'.get_settings('blog_charset').'"?'.'>'; ?>
<feed version="0.3"
  xmlns="http://purl.org/atom/ns#"
  xmlns:dc="http://purl.org/dc/elements/1.1/">
	<title><?php bloginfo_rss('name') ?></title>
	<link rel="alternate" type="text/html" href="<?php bloginfo_rss('home') ?>" />
	<tagline><?php bloginfo_rss("description") ?></tagline>
	<modified><?php echo mysql2date('Y-m-d\TH:i:s\Z', get_lastpostmodified('GMT')); ?></modified>
	<copyright>Copyright <?php echo mysql2date('Y', get_lastpostdate('blog')); ?></copyright>
	<generator url="http://wordpress.org/" version="<?php bloginfo_rss('version'); ?>">WordPress</generator>
	
	<?php $items_count = 0; if ($posts) { foreach ($posts as $post) { start_wp(); ?>
	<entry>
	  	<author>
			<name><?php the_author() ?></name>
		</author>
		<title><?php the_title_rss() ?></title>
		<link rel="alternate" type="text/html" href="<?php permalink_single_rss() ?>" />
		<id><?php the_guid(); ?></id>
		<modified><?php the_time('Y-m-d\TH:i:s\Z'); ?></modified>
		<issued>  <?php the_time('Y-m-d\TH:i:s\Z'); ?></issued>
		<?php the_category_rss('rdf') ?>
		<summary type="text/html" mode="escaped"><![CDATA[<?php the_excerpt_rss(); ?>]]></summary>
<?php if (!get_settings('rss_use_excerpt')) : ?>
	<?php if ( strlen( $post->post_content ) ) : ?>
		<content type="text/html" mode="escaped" xml:base="<?php permalink_single_rss() ?>"><![CDATA[<?php the_content('', 0, '') ?>]]></content>
	<?php else : ?>
		<content type="text/html" mode="escaped" xml:base="<?php permalink_single_rss() ?>"><![CDATA[<?php the_excerpt_rss(); ?>]]></content>
	<?php endif; ?>
<?php else : ?>
		<content type="text/html" mode="escaped" xml:base="<?php permalink_single_rss() ?>"><![CDATA[<?php the_excerpt_rss() ?>]]></content>
<?php endif; ?>
<?php rss_enclosure(); ?>
	</entry>
	<?php $items_count++; if (($items_count == get_settings('posts_per_rss')) && empty($m)) { break; } } } ?>
</feed>
