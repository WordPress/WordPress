<?php

if( is_object( $wpsmarty ) == false )
{
	return;
}

function smarty_bloginfo( $params ) 
{
	$show = '';
	$display = true;
	extract( $params );
	return bloginfo($show, $display);
}
$wpsmarty->register_function( 'bloginfo', 'smarty_bloginfo' );

function smarty_bloginfo_rss( $params )
{
	$show = '';
	extract( $params );
	return bloginfo_rss($show);
}
$wpsmarty->register_function( 'bloginfo_rss', 'smarty_bloginfo_rss' );

function smarty_bloginfo_unicode( $params )
{
	$show = '';
	extract( $params );
	return bloginfo_unicode( $show );
}
$wpsmarty->register_function( 'bloginfo_unicode', 'smarty_bloginfo_unicode' );

function smarty_get_bloginfo( $params )
{
	$show = '';
	extract( $params );
	return get_bloginfo( $show );
}
$wpsmarty->register_function( 'get_bloginfo', 'smarty_get_bloginfo' );

function smarty_single_post_title( $params )
{
	$display = '';
	$prefix = '';
	extract( $params );
	return single_post_title( $prefix, $display );
}
$wpsmarty->register_function( 'single_post_title', 'smarty_single_post_title' );

function smarty_single_cat_title( $params )
{
	$display = '';
	$prefix = '';
	extract( $params );
	return single_cat_title( $prefix, $display );
}
$wpsmarty->register_function( 'single_cat_title', 'smarty_single_cat_title' );

function smarty_single_month_title( $params )
{
	$display = '';
	$prefix = '';
	extract( $params );
	return single_month_title( $prefix, $display );
}
$wpsmarty->register_function( 'single_month_title', 'smarty_single_month_title' );

function smarty_get_archives_link( $params )
{
	$format = 'html';
	$before = '';
	$after = '';
	extract( $params );
	return get_archives_link( $url, $text, $format, $before, $after );
}
$wpsmarty->register_function( 'single_month_title', 'smarty_single_month_title' );

function smarty_get_archives( $params )
{
	$type = '';
	$limit = '';
	$format = 'html';
	$before = '';
	$after = '';
	extract( $params );
	return get_archives( $type, $limit, $format, $before, $after );
}
$wpsmarty->register_function( 'get_archives', 'smarty_get_archives' );

function smarty_the_date_xml()
{
	return the_date_xml();
}
$wpsmarty->register_function( 'the_date_xml', 'smarty_the_date_xml' );

function smarty_the_date( $params )
{
	$d = '';
	$before = '';
	$after = '';
	$echo = true;
	extract( $params );
	return the_date( $d, $before, $after, $echo );
}
$wpsmarty->register_function( 'the_date', 'smarty_the_date' );

function smarty_the_time( $params )
{
	$d = '';
	$echo = true;
	extract( $params );
	return the_time( $d, $echo );
}
$wpsmarty->register_function( 'the_time', 'smarty_the_time' );

function smarty_the_weekday()
{
	return the_weekday();
}
$wpsmarty->register_function( 'the_weekday', 'smarty_the_weekday' );

function smarty_the_weekday_date( $params )
{
	$before='';
	$after='';
	extract( $params );
	return the_weekday_date( $before, $after );

}
$wpsmarty->register_function( 'the_weekday_date', 'smarty_the_weekday_date' );

function smarty_get_Lat() {
	return get_Lat();
}
$wpsmarty->register_function( 'get_Lat', 'smarty_get_Lat' );

function smarty_get_Lon() {
	return get_Lon();
}
$wpsmarty->register_function( 'get_Lon', 'smarty_get_Lon' );

function smarty_the_ID()
{
	return the_ID();
}
$wpsmarty->register_function( 'the_ID', 'smarty_the_ID' );

function smarty_permalink_link( $params )
{
	$file='';
	$mode = 'id';
	extract( $params );
	return permalink_link( $file, $mode );
}
$wpsmarty->register_function( 'permalink_link', 'smarty_permalink_link' );

function smarty_the_title( $params )
{
	$before='';
	$after='';
	$echo=true;
	extract( $params );
	return the_title( $before, $after, $echo);
}
$wpsmarty->register_function( 'the_title', 'smarty_the_title' );

function smarty_the_category_ID( $params )
{
	$echo=true;
	extract( $params );
	return the_category_ID( $echo );
}
$wpsmarty->register_function( 'the_category_ID', 'smarty_the_category_ID' );
$wpsmarty->register_function( 'the_category', 'the_category' );
$wpsmarty->register_function( 'the_author', 'the_author' );

function smarty_the_content( $params )
{
	$more_link_text='(more...)';
	$stripteaser=0;
	$more_file='';
	extract( $params );
	return the_content( $more_link_text, $stripteaser, $more_file );
}
$wpsmarty->register_function( 'the_content', 'smarty_the_content' );

function smarty_link_pages( $params )
{
	$before='<br />';
	$after='<br />';
	$next_or_number='number';
	$nextpagelink='next page';
	$previouspagelink='previous page';
	$pagelink='%';
	$more_file='';
	extract( $params );
	return link_pages( $before, $after, $next_or_number, $nextpagelink, $previouspagelink, $pagelink, $more_file);
}
$wpsmarty->register_function( 'link_pages', 'smarty_link_pages' );

function smarty_comments_popup_link( $params )
{
	$zero='No Comments';
	$one='1 Comment';
	$more='% Comments';
	$CSSclass='';
	$none='Comments Off';
	extract( $params );
	return comments_popup_link( $zero, $one, $more, $CSSclass, $none );
}
$wpsmarty->register_function( 'comments_popup_link', 'smarty_comments_popup_link' );

function smarty_trackback_rdf( $params )
{
	$timezone = 0;
	extract( $params );
	return trackback_rdf( $timezone );
}
$wpsmarty->register_function( 'trackback_rdf', 'smarty_trackback_rdf' );

function smarty_comments_popup_script( $params )
{
	$width=400;
	$height=400;
	$file='wp-comments-popup.php';
	extract( $params );
	return comments_popup_script( $width, $height, $file );
}
$wpsmarty->register_function( 'comments_popup_script', 'smarty_comments_popup_script' );

function smarty_get_links( $params )
{
	extract($params);
        get_links($category, $before, $after, $between, $show_images, $orderby, $show_description, $show_rating, $limit, $show_updated, true );
}
$wpsmarty->register_function( 'get_links', 'smarty_get_links' );

function smarty_list_cats( $params )
{
	extract($params);
	list_cats($optionall, $all, $sort_column, $sort_order, $file, $list, $optiondates, $optioncount, $hide_empty);
}
$wpsmarty->register_function( 'list_cats', 'smarty_list_cats' );

function smarty_timer_stop( $params )
{
	$display = 0;
	$precision = 3;
	extract($params);
	timer_stop( $display, $precision );
}
$wpsmarty->register_function( 'timer_stop', 'smarty_timer_stop' );

function smarty_get_calendar( $params )
{
	$daylength = 1;
	extract($params);
	get_calendar( $daylength );
}
$wpsmarty->register_function( 'get_calendar', 'smarty_get_calendar' );

?>
