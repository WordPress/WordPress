<?php
header('Content-Type: application/atom+xml; charset=' . get_option('blog_charset'), true);
echo '<?xml version="1.0" encoding="' . get_option('blog_charset') . '" ?' . '>';
?>
<feed
	xmlns="http://www.w3.org/2005/Atom"
	xml:lang="<?php echo get_option('rss_language'); ?>"
	<?php do_action('atom_ns'); ?>
>
	<title type="text"><?php
		if ( is_singular() )
			printf(__('Comments on: %s'), get_the_title_rss());
		elseif ( is_search() )
			printf(__('Comments for %s searching on %s'), get_bloginfo_rss( 'name' ), attribute_escape($wp_query->query_vars['s']));
		else
			printf(__('Comments for %s'), get_bloginfo_rss( 'name' ) . get_wp_title_rss());
	?></title>
	<subtitle type="text"><?php bloginfo_rss('description'); ?></subtitle>

	<updated><?php echo mysql2date('Y-m-d\TH:i:s\Z', get_lastcommentmodified('GMT')); ?></updated>
	<generator uri="http://wordpress.org/" version="<?php bloginfo('version'); ?>">WordPress</generator>

	<link rel="alternate" type="<?php bloginfo_rss('html_type'); ?>" href="<?php bloginfo_rss('home'); ?>" />
	<link rel="self" type="application/atom+xml" href="<?php bloginfo_rss('comments_atom_url'); ?>" />
	<id><?php bloginfo_rss('comments_atom_url'); ?></id>

<?php
if ( have_comments() ) : while ( have_comments() ) : the_comment();
	$comment_post = get_post($comment->comment_post_ID);
	get_post_custom($comment_post->ID);
?>
	<entry>
		<title><?php
			if ( !is_singular() ) {
				$title = get_the_title($comment_post->ID);
				$title = apply_filters('the_title_rss', $title);
				printf(__('Comment on %1$s by %2$s'), $title, get_comment_author_rss());
			} else {
				printf(__('By: %s'), get_comment_author_rss());
			}
		?></title>
		<link rel="alternate" href="<?php comment_link(); ?>" type="<?php bloginfo_rss('html_type'); ?>" />

		<author>
			<name><?php comment_author_rss(); ?></name>
			<?php if (get_comment_author_url()) echo '<uri>' . get_comment_author_url() . '</uri>'; ?>

		</author>

		<id><?php comment_link(); ?></id>
		<updated><?php echo mysql2date('Y-m-d\TH:i:s\Z', get_comment_time('Y-m-d H:i:s', true), false); ?></updated>
		<published><?php echo mysql2date('Y-m-d\TH:i:s\Z', get_comment_time('Y-m-d H:i:s', true), false); ?></published>
<?php if (!empty($comment_post->post_password) && $_COOKIE['wp-postpass'] != $comment_post->post_password) : ?>
		<content type="html" xml:base="<?php comment_link(); ?>"><![CDATA[<?php echo get_the_password_form(); ?>]]></content>
<?php else : // post pass ?>
		<content type="html" xml:base="<?php comment_link(); ?>"><![CDATA[<?php comment_text(); ?>]]></content>
<?php endif; // post pass
	do_action('comment_atom_entry', $comment->comment_ID, $comment_post->ID);
?>
	</entry>
<?php endwhile; endif; ?>
</feed>
