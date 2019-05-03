<?php
/**
 * RSS 1 RDF Feed Template for displaying RSS 1 Posts feed.
 *
 * @package WordPress
 */

header( 'Content-Type: ' . feed_content_type( 'rdf' ) . '; charset=' . get_option( 'blog_charset' ), true );
$more = 1;

echo '<?xml version="1.0" encoding="' . get_option( 'blog_charset' ) . '"?' . '>';

/** This action is documented in wp-includes/feed-rss2.php */
do_action( 'rss_tag_pre', 'rdf' );
?>
<rdf:RDF
	xmlns="http://purl.org/rss/1.0/"
	xmlns:rdf="http://www.w3.org/1999/02/22-rdf-syntax-ns#"
	xmlns:dc="http://purl.org/dc/elements/1.1/"
	xmlns:sy="http://purl.org/rss/1.0/modules/syndication/"
	xmlns:admin="http://webns.net/mvcb/"
	xmlns:content="http://purl.org/rss/1.0/modules/content/"
	<?php
	/**
	 * Fires at the end of the feed root to add namespaces.
	 *
	 * @since 2.0.0
	 */
	do_action( 'rdf_ns' );
	?>
>
<channel rdf:about="<?php bloginfo_rss( 'url' ); ?>">
	<title><?php wp_title_rss(); ?></title>
	<link><?php bloginfo_rss( 'url' ); ?></link>
	<description><?php bloginfo_rss( 'description' ); ?></description>
	<dc:date><?php echo get_feed_build_date( 'Y-m-d\TH:i:s\Z' ); ?>	</dc:date>
	<sy:updatePeriod>
	<?php
		/** This filter is documented in wp-includes/feed-rss2.php */
		echo apply_filters( 'rss_update_period', 'hourly' );
	?>
	</sy:updatePeriod>
	<sy:updateFrequency>
	<?php
		/** This filter is documented in wp-includes/feed-rss2.php */
		echo apply_filters( 'rss_update_frequency', '1' );
	?>
	</sy:updateFrequency>
	<sy:updateBase>2000-01-01T12:00+00:00</sy:updateBase>
	<?php
	/**
	 * Fires at the end of the RDF feed header.
	 *
	 * @since 2.0.0
	 */
	do_action( 'rdf_header' );
	?>
	<items>
		<rdf:Seq>
		<?php
		while ( have_posts() ) :
			the_post();
			?>
			<rdf:li rdf:resource="<?php the_permalink_rss(); ?>"/>
		<?php endwhile; ?>
		</rdf:Seq>
	</items>
</channel>
<?php
rewind_posts();
while ( have_posts() ) :
	the_post();
	?>
<item rdf:about="<?php the_permalink_rss(); ?>">
	<title><?php the_title_rss(); ?></title>
	<link><?php the_permalink_rss(); ?></link>
	<dc:date><?php echo mysql2date( 'Y-m-d\TH:i:s\Z', $post->post_date_gmt, false ); ?></dc:date>
	<dc:creator><![CDATA[<?php the_author(); ?>]]></dc:creator>
	<?php the_category_rss( 'rdf' ); ?>
	<?php if ( get_option( 'rss_use_excerpt' ) ) : ?>
	<description><![CDATA[<?php the_excerpt_rss(); ?>]]></description>
<?php else : ?>
	<description><![CDATA[<?php the_excerpt_rss(); ?>]]></description>
	<content:encoded><![CDATA[<?php the_content_feed( 'rdf' ); ?>]]></content:encoded>
<?php endif; ?>
	<?php
	/**
	 * Fires at the end of each RDF feed item.
	 *
	 * @since 2.0.0
	 */
	do_action( 'rdf_item' );
	?>
</item>
<?php endwhile; ?>
</rdf:RDF>
