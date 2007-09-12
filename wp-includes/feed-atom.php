<?php
header('Content-Type: application/atom+xml; charset=' . get_option('blog_charset'), true);
$more = 1;

?>
<?php echo '<?xml version="1.0" encoding="'.get_option('blog_charset').'"?'.'>'; ?>
<feed
  xmlns="http://www.w3.org/2005/Atom"
  xmlns:thr="http://purl.org/syndication/thread/1.0"
  xml:lang="<?php echo get_option('rss_language'); ?>"
  xml:base="<?php bloginfo_rss('home') ?>/wp-atom.php"
  <?php do_action('atom_ns'); ?>
 >
	<title type="text"><?php bloginfo_rss('name'); wp_title_rss(); ?></title>
	<subtitle type="text"><?php bloginfo_rss("description") ?></subtitle>

	<updated><?php echo mysql2date('Y-m-d\TH:i:s\Z', get_lastpostmodified('GMT')); ?></updated>
	<generator uri="http://wordpress.org/" version="<?php bloginfo_rss('version'); ?>">WordPress</generator>

	<link rel="alternate" type="text/html" href="<?php bloginfo_rss('home') ?>" />
	<id><?php bloginfo('atom_url'); ?></id>
	<link rel="self" type="application/atom+xml" href="<?php bloginfo('atom_url'); ?>" />

	<?php do_action('atom_head'); ?>
	<?php while (have_posts()) : the_post(); ?>
	<entry>
		<author>
			<name><?php the_author() ?></name>
			<?php $author_url = get_the_author_url(); if ( !empty($author_url) ) : ?>
			<uri><?php the_author_url()?></uri>
			<?php endif; ?>
		</author>
		<title type="<?php html_type_rss(); ?>"><![CDATA[<?php the_title_rss() ?>]]></title>
		<link rel="alternate" type="text/html" href="<?php the_permalink_rss() ?>" />
		<id><?php the_guid(); ?></id>
		<updated><?php echo get_post_modified_time('Y-m-d\TH:i:s\Z', true); ?></updated>
		<published><?php echo get_post_time('Y-m-d\TH:i:s\Z', true); ?></published>
		<?php the_category_rss('atom') ?>
		<summary type="<?php html_type_rss(); ?>"><![CDATA[<?php the_excerpt_rss(); ?>]]></summary>
<?php if ( !get_option('rss_use_excerpt') ) : ?>
		<content type="<?php html_type_rss(); ?>" xml:base="<?php the_permalink_rss() ?>"><![CDATA[<?php the_content('', 0, '') ?>]]></content>
<?php endif; ?>
<?php atom_enclosure(); ?>
<?php do_action('atom_entry'); ?>
	</entry>
	<?php endwhile ; ?>
</feed>
