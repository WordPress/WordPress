<?php
require_once('admin.php');
require_once (ABSPATH . WPINC . '/rss.php');

@header('Content-Type: ' . get_option('html_type') . '; charset=' . get_option('blog_charset'));

switch ( $_GET['jax'] ) {

case 'incominglinks' :

$rss_feed = apply_filters( 'dashboard_incoming_links_feed', 'http://blogsearch.google.com/blogsearch_feeds?hl=en&scoring=d&ie=utf-8&num=10&output=rss&partner=wordpress&q=link:' . trailingslashit( get_option('home') ) );
$more_link = apply_filters( 'dashboard_incoming_links_link', 'http://blogsearch.google.com/blogsearch?hl=en&scoring=d&partner=wordpress&q=link:' . trailingslashit( get_option('home') ) );

$rss = @fetch_rss( $rss_feed );
if ( isset($rss->items) && 1 < count($rss->items) ) { // Technorati returns a 1-item feed when it has no results
?>
<h3><?php _e('Incoming Links'); ?> <cite><a href="<?php echo htmlspecialchars( $more_link ); ?>"><?php _e('More &raquo;'); ?></a></cite></h3>
<ul>
<?php
$rss->items = array_slice($rss->items, 0, 10);
foreach ($rss->items as $item ) {
?>
	<li><a href="<?php echo wp_filter_kses($item['link']); ?>"><?php echo wptexturize(wp_specialchars($item['title'])); ?></a></li>
<?php } ?>
</ul>
<?php
}
break;

case 'devnews' :
$rss = @fetch_rss(apply_filters( 'dashboard_primary_feed', 'http://wordpress.org/development/feed/' ));
if ( isset($rss->items) && 0 != count($rss->items) ) {
?>
<h3><?php echo apply_filters( 'dashboard_primary_title', __('WordPress Development Blog') ); ?></h3>
<?php
$rss->items = array_slice($rss->items, 0, 3);
foreach ($rss->items as $item ) {
?>
<h4><a href='<?php echo wp_filter_kses($item['link']); ?>'><?php echo wp_specialchars($item['title']); ?></a> &#8212; <?php printf(__('%s ago'), human_time_diff(strtotime($item['pubdate'], time() ) ) ); ?></h4>
<p><?php echo $item['description']; ?></p>
<?php
	}
}
?>

<?php
break;

case 'planetnews' :
$rss = @fetch_rss(apply_filters( 'dashboard_secondary_feed', 'http://planet.wordpress.org/feed/' ));
if ( isset($rss->items) && 0 != count($rss->items) ) {
?>
<h3><?php echo apply_filters( 'dashboard_secondary_title', __('Other WordPress News') ); ?></h3>
<ul>
<?php
$rss->items = array_slice($rss->items, 0, 20);
foreach ($rss->items as $item ) {
$title = wp_specialchars($item['title']);
$author = preg_replace( '|(.+?):.+|s', '$1', $item['title'] );
$post = preg_replace( '|.+?:(.+)|s', '$1', $item['title'] );
?>
<li><a href='<?php echo wp_filter_kses($item['link']); ?>'><span class="post"><?php echo $post; ?></span><span class="hidden"> - </span><cite><?php echo $author; ?></cite></a></li>
<?php
	}
?>
</ul>
<p class="readmore"><a href="<?php echo apply_filters( 'dashboard_secondary_link', 'http://planet.wordpress.org/' ); ?>"><?php _e('Read more &raquo;'); ?></a></p>
<?php
}
break;
}

?>