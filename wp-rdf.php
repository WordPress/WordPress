<?php /* RDF 1.0 generator, original version by garym@teledyn.com */
$blog = 1; // enter your blog's ID
$doing_rss=1;
header('Content-type: text/xml');

include('blog.header.php');

// Handle Conditional GET

// Get the time of the most recent article
$sql = "SELECT max(post_date) FROM $tableposts";

$maxdate = $wpdb->get_var($sql);
++$querycount;
$unixtime = strtotime($maxdate);

// format timestamp for Last-Modified header
$clast = gmdate("D, d M Y H:i:s \G\M\T",$unixtime);
$cetag = md5($last);

$slast = $_SERVER['HTTP_IF_MODIFIED_SINCE'];
$setag = $_SERVER['HTTP_IF_NONE_MATCH'];

// send it in a Last-Modified header
header("Last-Modified: " . $clast, true);
header("Etag: " . $cetag, true);

// compare it to aggregator's If-Modified-Since and If-None-Match headers
// if they match, send a 304 and die

// This logic says that if only one header is provided, just use that one,
// but if both headers exist, they *both* must match up with the locally
// generated values.
//if (($slast?($slast == $clast):true) && ($setag?($setag == $cetag):true)){
if (($slast && $setag)?(($slast == $clast) && ($setag == $cetag)):(($slast == $clast) || ($setag == $cetag))) { 
	header("HTTP/1.1 304 Not Modified");
	echo "\r\n\r\n";
	exit;
}

add_filter('the_content', 'trim');
if (!isset($rss_language)) { $rss_language = 'en'; }
?>
<?php echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?".">"; ?>
<!-- generator="wordpress/<?php echo $b2_version ?>" -->
<rdf:RDF
	xmlns="http://purl.org/rss/1.0/"
	xmlns:rdf="http://www.w3.org/1999/02/22-rdf-syntax-ns#"
	xmlns:dc="http://purl.org/dc/elements/1.1/"
	xmlns:sy="http://purl.org/rss/1.0/modules/syndication/"
	xmlns:admin="http://webns.net/mvcb/"
	xmlns:content="http://purl.org/rss/1.0/modules/content/"
>
<channel rdf:about="<?php bloginfo_rss("url") ?>">
	<title><?php bloginfo_rss('name') ?></title>
	<link><?php bloginfo_rss('url') ?></link>
	<description><?php bloginfo_rss('description') ?></description>
	<dc:language><?php echo $rss_language ?></dc:language>
	<dc:date><?php echo gmdate('Y-m-d\TH:i:s'); ?></dc:date>
	<dc:creator><?php echo antispambot($admin_email) ?></dc:creator>
	<admin:generatorAgent rdf:resource="http://wordpress.org/?v=<?php echo $b2_version ?>"/>
	<admin:errorReportsTo rdf:resource="mailto:<?php echo antispambot($admin_email) ?>"/>
	<sy:updatePeriod>hourly</sy:updatePeriod>
	<sy:updateFrequency>1</sy:updateFrequency>
	<sy:updateBase>2000-01-01T12:00+00:00</sy:updateBase>
	<items>
		<rdf:Seq>
		<?php $items_count = 0; if ($posts) { foreach ($posts as $post) { start_b2(); ?>
			<rdf:li rdf:resource="<?php permalink_single_rss() ?>"/>
		<?php $b2_items[] = $row; $items_count++; if (($items_count == $posts_per_rss) && empty($m)) { break; } } } ?>
		</rdf:Seq>
	</items>
</channel>
<?php if ($posts) { foreach ($posts as $post) { start_b2(); ?>
<item rdf:about="<?php permalink_single_rss() ?>">
	<title><?php the_title_rss() ?></title>
	<link><?php permalink_single_rss() ?></link>
	<dc:date><?php the_time('Y-m-d\TH:i:s'); ?></dc:date>
	<dc:creator><?php the_author() ?> (mailto:<?php the_author_email() ?>)</dc:creator>
	<dc:subject><?php the_category_rss() ?></dc:subject>
<?php $more = 1; if ($rss_use_excerpt) {
?>
	<description><?php the_excerpt_rss($rss_excerpt_length, 2) ?></description>
<?php
} else { // use content
?>
	<description><?php the_content_rss('', 0, '', $rss_excerpt_length, 2) ?></description>
<?php
} // end else use content
?>
	<content:encoded><![CDATA[<?php the_content('', 0, '') ?>]]></content:encoded>
</item>
<?php } }  ?>
</rdf:RDF>