<?php
/**
 * rss.js Feed Template for displaying rss.js Posts feed.
 *
 * @package WordPress
 * @subpackage Feed
 * @since 3.8.0
 */

$json = new stdClass();
$json->rss = new stdClass();

$json->rss->version = "2.0";
$json->rss->channel = new stdClass();

$json->rss->channel->title         = get_bloginfo( 'name' );
$json->rss->channel->link          = get_bloginfo( 'url' );
$json->rss->channel->description   = get_bloginfo( 'description' );
$json->rss->channel->language      = get_bloginfo( 'language' );
$json->rss->channel->lastBuildDate = mysql2date( 'D, d M Y H:i:s +0000', get_lastpostmodified( 'GMT' ), false );
$json->rss->channel->docs          = "http://cyber.law.harvard.edu/rss/rss.html";
$json->rss->channel->generator     = 'WordPress ' . get_bloginfo( 'version' );
$json->rss->channel->ttl           = 15;

$json->rss->channel->item = array();

header( 'Content-Type: ' . feed_content_type( 'rssjs' ) . '; charset=' . get_option( 'blog_charset' ), true );

/*
 * The JSONP callback function to add to the JSON feed
 *
 * @since 3.8.0
 *
 * @param string $callback The JSONP callback function name
 */
$callback = apply_filters( 'json_feed_callback', get_query_var( 'callback' ) );

if ( ! empty( $callback ) && ! apply_filters( 'json_jsonp_enabled', true ) ) {
	status_header( 400 );
	echo json_encode( array(
		'code'    => 'json_callback_disabled',
		'message' => 'JSONP support is disabled on this site.'
	) );
	exit;
}

if ( preg_match( '/\W/', $callback ) ) {
	status_header( 400 );
	echo json_encode( array(
		'code'    => 'json_callback_invalid',
		'message' => 'The JSONP callback function is invalid.'
	) );
	exit;
}

/*
 * Action triggerd prior to the JSON feed being created and sent to the client
 *
 * @since 3.8.0
 */
do_action( 'json_feed_pre' );

while( have_posts() ) {
	the_post();

	$item = new stdClass();

	$item->title       = get_the_title();
	$item->link        = get_permalink();
	$item->guid        = get_the_guid();
	$item->description = get_the_content();
	$item->pubDate     = mysql2date( 'D, d M Y H:i:s +0000', get_post_time( 'Y-m-d H:i:s', true ), false );

	/*
	 * The item to be added to the rss.js Post feed
	 *
	 * @since 3.8.0
	 *
	 * @param object $item The rss.js Post item
	 */
	$item = apply_filters( 'rssjs_feed_item', $item );

	$json->rss->channel->item[] = $item;
}

/*
 * The data to be sent to the user as JSON
 *
 * @since 3.8.0
 *
 * @param object $json The JSON data object
 */
$json = apply_filters( 'rssjs_feed', $json );


$json_str = json_encode( $json );

if ( ! empty( $callback ) ) {
	echo "$callback( $json_str );";
} else {
	echo $json_str;
}

/*
 * Action triggerd after the JSON feed has been created and sent to the client
 *
 * @since 3.8.0
 */
do_action( 'json_feed_post' );
