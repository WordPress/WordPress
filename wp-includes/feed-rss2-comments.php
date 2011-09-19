<?php
/**
 * RSS2 Feed Template for displaying RSS2 Comments feed.
 *
 * @package WordPress
 */

header('Content-Type: ' . feed_content_type('rss-http') . '; charset=' . get_option('blog_charset'), true);

echo '<?xml version="1.0" encoding="'.get_option('blog_charset').'"?'.'>';
?>
<rss version="2.0"
	xmlns:content="http://purl.org/rss/1.0/modules/content/"
	xmlns:dc="http://purl.org/dc/elements/1.1/"
	xmlns:atom="http://www.w3.org/2005/Atom"
	xmlns:sy="http://purl.org/rss/1.0/modules/syndication/"
	<?php do_action('rss2_ns'); do_action('rss2_comments_ns'); ?>
	>
<channel>
	<title><?php
		if ( is_singular() )
			printf(ent2ncr(__('Comments on: %s')), get_the_title_rss());
		elseif ( is_search() )
			printf(ent2ncr(__('Comments for %s searching on %s')), get_bloginfo_rss( 'name' ), esc_attr($wp_query->query_vars['s']));
		else
			printf(ent2ncr(__('Comments for %s')), get_bloginfo_rss( 'name' ) . get_wp_title_rss());
	?></title>
	<atom:link href="<?php self_link(); ?>" rel="self" type="application/rss+xml" />
	<link><?php (is_single()) ? the_permalink_rss() : bloginfo_rss("url") ?></link>
	<description><?php bloginfo_rss("description") ?></description>
	<lastBuildDate><?php echo mysql2date('r', get_lastcommentmodified('GMT')); ?></lastBuildDate>
	<sy:updatePeriod><?php echo apply_filters( 'rss_update_period', 'hourly' ); ?></sy:updatePeriod>
	<sy:updateFrequency><?php echo apply_filters( 'rss_update_frequency', '1' ); ?></sy:updateFrequency>
	<?php do_action('commentsrss2_head'); ?>
<?php
if ( have_comments() ) : while ( have_comments() ) : the_comment();
	$comment_post = $GLOBALS['post'] = get_post( $comment->comment_post_ID );
?>
	<item>
		<title><?php
			if ( !is_singular() ) {
				$title = get_the_title($comment_post->ID);
				$title = apply_filters('the_title_rss', $title);
				printf(ent2ncr(__('Comment on %1$s by %2$s')), $title, get_comment_author_rss());
			} else {
				printf(ent2ncr(__('By: %s')), get_comment_author_rss());
			}
		?></title>
		<link><?php comment_link() ?></link>
		<dc:creator><?php echo get_comment_author_rss() ?></dc:creator>
		<pubDate><?php echo mysql2date('D, d M Y H:i:s +0000', get_comment_time('Y-m-d H:i:s', true, false), false); ?></pubDate>
		<guid isPermaLink="false"><?php comment_guid() ?></guid>
<?php if ( post_password_required($comment_post) ) : ?>
		<description><?php echo ent2ncr(__('Protected Comments: Please enter your password to view comments.')); ?></description>
		<content:encoded><![CDATA[<?php echo get_the_password_form() ?>]]></content:encoded>
<?php else : // post pass ?>
		<description><?php comment_text_rss() ?></description>
		<content:encoded><![CDATA[<?php comment_text() ?>]]></content:encoded>
<?php endif; // post pass
	do_action('commentrss2_item', $comment->comment_ID, $comment_post->ID);
?>
	</item>
<?php endwhile; endif; ?>
</channel>
</rss>
