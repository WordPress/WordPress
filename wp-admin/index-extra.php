<?php
require_once('admin.php');
require_once (ABSPATH . WPINC . '/rss.php');

switch ( $_GET['jax'] ) {

case 'incominglinks' :
$rss = @fetch_rss('http://feeds.technorati.com/cosmos/rss/?url='. trailingslashit(get_option('home')) .'&partner=wordpress');
if ( isset($rss->items) && 1 < count($rss->items) ) { // Technorati returns a 1-item feed when it has no results
?>
<h3><?php _e('Incoming Links'); ?> <cite><a href="http://www.technorati.com/search/<?php echo trailingslashit(get_option('home')); ?>?partner=wordpress"><?php _e('More &raquo;'); ?></a></cite></h3>
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
$rss = @fetch_rss('http://wordpress.org/development/feed/');
if ( isset($rss->items) && 0 != count($rss->items) ) {
?>
<h3><?php _e('WordPress Development Blog'); ?></h3>
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
$rss = @fetch_rss('http://planet.wordpress.org/feed/');
if ( isset($rss->items) && 0 != count($rss->items) ) {
?>
<h3><?php _e('Other WordPress News'); ?></h3>
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
<p class="readmore"><a href="http://planet.wordpress.org/"><?php _e('Read more'); ?> &raquo;</a></p>
<?php
}
break;
}

?>