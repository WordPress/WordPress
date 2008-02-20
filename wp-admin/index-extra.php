<?php
require_once('admin.php');
require_once (ABSPATH . WPINC . '/rss.php');

@header('Content-Type: ' . get_option('html_type') . '; charset=' . get_option('blog_charset'));

$widgets = get_option( 'dashboard_widget_options' );


switch ( $_GET['jax'] ) {

case 'incominglinks' :
@extract( @$widgets['dashboard_incoming_links'], EXTR_SKIP );
$rss = @fetch_rss( $url );
if ( isset($rss->items) && 1 < count($rss->items) ) { // Technorati returns a 1-item feed when it has no results
?>

<ul>
<?php
$rss->items = array_slice($rss->items, 0, $items);
foreach ($rss->items as $item ) {
	$publisher = '';
	$site_link = '';
	$link = '';
	$content = '';
	$date = '';
	$link = clean_url( strip_tags( $item['link'] ) );

	if ( isset( $item['author_uri'] ) )
		$site_link = clean_url( strip_tags( $item['author_uri'] ) );

	if ( !$publisher = wp_specialchars( strip_tags( isset($item['dc']['publisher']) ? $item['dc']['publisher'] : $item['author_name'] ) ) )
		$publisher = __( 'Somebody' );
	if ( $site_link )
		$publisher = "<a href='$site_link'>$publisher</a>";
	else
		$publisher = "<strong>$publisher</strong>";

	if ( isset($item['description']) )
		$content = $item['description'];
	elseif ( isset($item['summary']) )
		$content = $item['summary'];
	elseif ( isset($item['atom_content']) )
		$content = $item['atom_content'];
	else
		$content = __( 'something' );
	$content = strip_tags( $content );
	if ( 50 < strlen($content) )
		$content = substr($content, 0, 50) . ' ...';
	$content = wp_specialchars( $content );
	if ( $link )
		$text = _c( '%1$s linked here <a href="%2$s">saying</a>, "%3$s"|feed_display' );
	else
		$text = _c( '%1$s linked here saying, "%3$s"|feed_display' );

	if ( $show_date ) {
		if ( $show_author || $show_summary )
			$text .= _c( ' on %4$s|feed_display' );
		$date = wp_specialchars( strip_tags( isset($item['pubdate']) ? $item['pubdate'] : $item['published'] ) );
		$date = strtotime( $date );
		$date = gmdate( get_option( 'date_format' ), $date );
	}

?>
	<li><?php printf( _c( "$text|feed_display" ), $publisher, $link, $content, $date ); ?></li>
<?php } ?>
</ul>
<?php
} else {
?>
<p><?php _e('No incoming links found... yet.'); ?></p>
<?php
}
break;

case 'devnews' :
wp_widget_rss_output( $widgets['dashboard_primary'] );
break;

case 'planetnews' :
extract( $widgets['dashboard_secondary'], EXTR_SKIP );
$rss = @fetch_rss( $url );
if ( isset($rss->items) && 0 != count($rss->items) ) {
?>
<ul>
<?php
$rss->items = array_slice($rss->items, 0, $items);
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
<br class="clear" />
<?php
}
break;

case 'plugins' :
$popular = @fetch_rss( 'http://wordpress.org/extend/plugins/rss/browse/popular/' );
$new     = @fetch_rss( 'http://wordpress.org/extend/plugins/rss/browse/new/' );
$updated = @fetch_rss( 'http://wordpress.org/extend/plugins/rss/browse/updated/' );

foreach ( array( 'popular' => __('Most Popular'), 'new' => __('Newest Plugins'), 'updated' => __('Recently Updated') ) as $feed => $label ) :
	if ( !isset($$feed->items) || 0 == count($$feed->items) )
		continue;

	$$feed->items = array_slice($$feed->items, 0, 5);
	$item_key = array_rand($$feed->items);

	// Eliminate some common badly formed plugin descriptions
	while ( ( null !== $item_key = array_rand($$feed->items) ) && false !== strpos( $$feed->items[$item_key]['description'], 'Plugin Name:' ) )
		unset($$feed->items[$item_key]);

	if ( !isset($$feed->items[$item_key]) )
		continue;

	$item = $$feed->items[$item_key];

	// current bbPress feed item titles are: user on "topic title"
	if ( preg_match( '/"(.*)"/s', $item['title'], $matches ) )
		$title = $matches[1];
	else // but let's make it forward compatible if things change
		$title = $item['title'];
	$title = wp_specialchars( $title );

	$description = wp_specialchars( strip_tags(html_entity_decode($item['description'], ENT_QUOTES)) );

	list($link, $frag) = explode( '#', $item['link'] );

	$link = clean_url($link);
	$dlink = rtrim($link, '/') . '/download/';

?>

<h4><?php echo $label; ?></h4>
<h5><a href="<?php echo $link; ?>"><?php echo $title; ?></a></h5> <span>(<a href="<?php echo $dlink; ?>"><?php _e( 'Download' ); ?></a>)</span>

<p><?php echo $description; ?></p>

<?php

endforeach;
break;

}

?>
