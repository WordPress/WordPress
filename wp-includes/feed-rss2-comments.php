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
	<?php
	/** This action is documented in wp-includes/feed-rss2.php */
	do_action( 'rss2_ns' );
	?>

	<?php
	/**
	 * Fires at the end of the RSS root to add namespaces.
	 *
	 * @since 2.8.0
	 */
	do_action( 'rss2_comments_ns' );
	?>
>
<channel>
	<title><?php
		if ( is_singular() )
			printf( ent2ncr( __( 'Comments on: %s' ) ), get_the_title_rss() );
		elseif ( is_search() )
			printf( ent2ncr( __( 'Comments for %1$s searching on %2$s' ) ), get_bloginfo_rss( 'name' ), get_search_query() );
		else
			printf( ent2ncr( __( 'Comments for %s' ) ), get_bloginfo_rss( 'name' ) . get_wp_title_rss() );
	?></title>
	<atom:link href="<?php self_link(); ?>" rel="self" type="application/rss+xml" />
	<link><?php (is_single()) ? the_permalink_rss() : bloginfo_rss("url") ?></link>
	<description><?php bloginfo_rss("description") ?></description>
	<lastBuildDate><?php echo mysql2date('r', get_lastcommentmodified('GMT')); ?></lastBuildDate>
	<?php /** This filter is documented in wp-includes/feed-rss2.php */ ?>
	<sy:updatePeriod><?php echo apply_filters( 'rss_update_period', 'hourly' ); ?></sy:updatePeriod>
	<?php /** This filter is documented in wp-includes/feed-rss2.php */ ?>
	<sy:updateFrequency><?php echo apply_filters( 'rss_update_frequency', '1' ); ?></sy:updateFrequency>
	<?php
	/**
	 * Fires at the end of the RSS2 comment feed header.
	 *
	 * @since 2.3.0
	 */
	do_action( 'commentsrss2_head' );

	if ( have_comments() ) : while ( have_comments() ) : the_comment();
		$comment_post = $GLOBALS['post'] = get_post( $comment->comment_post_ID );
	?>
	<item>
		<title><?php
			if ( !is_singular() ) {
				$title = get_the_title($comment_post->ID);
				/** This filter is documented in wp-includes/feed.php */
				$title = apply_filters( 'the_title_rss', $title );
				printf(ent2ncr(__('Comment on %1$s by %2$s')), $title, get_comment_author_rss());
			} else {
				printf(ent2ncr(__('By: %s')), get_comment_author_rss());
			}
		?></title>
		<link><?php comment_link() ?></link>
		<dc:creator><![CDATA[<?php echo get_comment_author_rss() ?>]]></dc:creator>
		<pubDate><?php echo mysql2date('D, d M Y H:i:s +0000', get_comment_time('Y-m-d H:i:s', true, false), false); ?></pubDate>
		<guid isPermaLink="false"><?php comment_guid() ?></guid>
<?php if ( post_password_required($comment_post) ) : ?>
		<description><?php echo ent2ncr(__('Protected Comments: Please enter your password to view comments.')); ?></description>
		<content:encoded><![CDATA[<?php echo get_the_password_form() ?>]]></content:encoded>
<?php else : // post pass ?>
		<description><![CDATA[<?php comment_text_rss() ?>]]></description>
		<content:encoded><![CDATA[<?php comment_text() ?>]]></content:encoded>
<?php endif; // post pass
	/**
	 * Fires at the end of each RSS2 comment feed item.
	 *
	 * @since 2.1.0
	 *
	 * @param int $comment->comment_ID The ID of the comment being displayed.
	 * @param int $comment_post->ID    The ID of the post the comment is connected to.
	 */
	do_action( 'commentrss2_item', $comment->comment_ID, $comment_post->ID );
?>
	</item>
<?php endwhile; endif; ?>
</channel>
</rss>
