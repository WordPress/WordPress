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
		if (is_single() || is_page()) {
			printf(__('Comments on: %s'), get_the_title_rss());
		} else {
			printf(__('Comments for %s'), get_bloginfo_rss('name'));
		}
	?></title>
	<subtitle type="text"><?php bloginfo_rss('description'); ?></subtitle>
	
	<updated><?php echo mysql2date('Y-m-d\TH:i:s\Z', get_lastcommentmodified('GMT')); ?></updated>
	<generator uri="http://wordpress.org/" version="<?php bloginfo('version'); ?>">WordPress</generator>
	
	<link rel="alternate" type="<?php bloginfo_rss('html_type'); ?>" content="<?php bloginfo_rss('home'); ?>" />
	<link rel="self" type="application/atom+xml" href="<?php bloginfo_rss('comments_atom_url'); ?>" />
	<id><?php bloginfo_rss('comments_atom_url'); ?></id>

<?php
$i = 0;
if (have_posts()) :
	while (have_posts()) : the_post();
		if ($i < 1) {
			$i++;
		}
		
		if (is_single() || is_page()) {
			$comments = $wpdb->get_results("SELECT comment_ID, comment_author, comment_author_email, 
			comment_author_url, comment_date, comment_date_gmt, comment_content, comment_post_ID, 
			$wpdb->posts.ID, $wpdb->posts.post_password FROM $wpdb->comments 
			LEFT JOIN $wpdb->posts ON comment_post_id = id WHERE comment_post_ID = '" . get_the_ID() . "' 
			AND $wpdb->comments.comment_approved = '1' AND $wpdb->posts.post_status = 'publish' 
			AND post_date_gmt < '" . gmdate("Y-m-d H:i:59") . "' 
			ORDER BY comment_date_gmt ASC" );
		} else { // if no post id passed in, we'll just use the last posts_per_rss comments.
			$comments = $wpdb->get_results("SELECT comment_ID, comment_author, comment_author_email, 
			comment_author_url, comment_date, comment_date_gmt, comment_content, comment_post_ID, 
			$wpdb->posts.ID, $wpdb->posts.post_password FROM $wpdb->comments 
			LEFT JOIN $wpdb->posts ON comment_post_id = id WHERE $wpdb->posts.post_status = 'publish' 
			AND $wpdb->comments.comment_approved = '1' AND post_date_gmt < '" . gmdate("Y-m-d H:i:s") . "'  
			ORDER BY comment_date_gmt DESC LIMIT " . get_option('posts_per_rss') );
		}
		
		if ($comments) {
			foreach ($comments as $comment) {
				$GLOBALS['comment'] =& $comment;
				get_post_custom($comment->comment_post_ID);
?>
	<entry>
		<title><?php
			if (!(is_single() || is_page())) {
				$title = get_the_title($comment->comment_post_ID);
				$title = apply_filters('the_title', $title);
				$title = apply_filters('the_title_rss', $title);
				printf(__('Comment on %1$s by %2$s'), $title, get_comment_author_rss());
			} else {
				printf(__('By: %s'), get_comment_author_rss());
			}
		?></title>
		<link rel="alternate" href="<?php comment_link(); ?>" type="<?php bloginfo_rss('content_type'); ?>" />
		
		<author>
			<name><?php comment_author_rss(); ?></name>
			<?php if (get_comment_author_url()) echo '<uri>' . get_comment_author_url() . '</uri>'; ?>
		</author>
		
		<id><?php comment_link(); ?></id>
		<updated><?php echo mysql2date('D, d M Y H:i:s +0000', get_comment_time('Y-m-d H:i:s', true), false); ?></updated>
		<published><?php echo mysql2date('D, d M Y H:i:s +0000', get_comment_time('Y-m-d H:i:s', true), false); ?></published>
		
	<?php if (!empty($comment->post_password) && $_COOKIE['wp-postpass'] != $comment->post_password) { ?>
		<content type="html" xml:base="<?php comment_link(); ?>"><![CDATA[<?php echo get_the_password_form(); ?>]]></content>
	<?php } else { ?>
		<content type="html" xml:base="<?php comment_link(); ?>"><![CDATA[<?php comment_text(); ?>]]></content>
	<?php } ?>
	</entry>
<?php
			}
		}
		
	endwhile;
endif;
?>
</feed>