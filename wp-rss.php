<?php /* These first lines are the first part of a CafeLog template.
         In every template you do, you got to copy them before the CafeLog 'loop' */
if (! $feed) {
    $blog = 1; // enter your blog's ID
    $doing_rss = 1;
    require('wp-blog-header.php');
}

header('Content-type: text/xml', true);

/* This doesn't take into account edits
// Get the time of the most recent article
$maxdate = $wpdb->get_var("SELECT max(post_date) FROM $tableposts");
$unixtime = strtotime($maxdate);

// format timestamp for Last-Modified header
$clast = gmdate("D, d M Y H:i:s \G\M\T", $unixtime);
$cetag = (isset($clast)) ? md5($clast) : '';

// send it in a Last-Modified header
header("Last-Modified: " . $clast, true);
header("Etag: " . $cetag, true);
*/

if (!isset($rss_language)) { $rss_language = 'en'; }
if (!isset($rss_encoded_html)) { $rss_encoded_html = 0; }
if (!isset($rss_excerpt_length) || ($rss_encoded_html == 1)) { $rss_excerpt_length = 0; }
?>
<?php echo "<?xml version=\"1.0\"?".">"; ?>
<!-- generator="wordpress/<?php echo $wp_version ?>" -->
<rss version="0.92">
    <channel>
        <title><?php bloginfo_rss("name") ?></title>
        <link><?php bloginfo_rss("url") ?></link>
        <description><?php bloginfo_rss("description") ?></description>
        <lastBuildDate><?php echo gmdate("D, d M Y H:i:s"); ?> GMT</lastBuildDate>
        <docs>http://backend.userland.com/rss092</docs>
        <managingEditor><?php echo antispambot($admin_email) ?></managingEditor>
        <webMaster><?php echo antispambot($admin_email) ?></webMaster>
        <language><?php echo $rss_language ?></language>

<?php $items_count = 0; if ($posts) { foreach ($posts as $post) { start_wp(); ?>
        <item>
            <title><?php the_title_rss() ?></title>
<?php
// we might use this in the future, but not now, that's why it's commented in PHP
// so that it doesn't appear at all in the RSS
//          echo "<category>"; the_category_unicode(); echo "</category>";
$more = 1; 
if ($rss_use_excerpt) {
?>
            <description><?php the_excerpt_rss($rss_excerpt_length, $rss_encoded_html) ?></description>
<?php
} else { // use content
?>
            <description><?php the_content_rss('', 0, '', $rss_excerpt_length, $rss_encoded_html) ?></description>
<?php
} // end else use content
?>
            <link><?php permalink_single_rss() ?></link>
        </item>
<?php $items_count++; if (($items_count == $posts_per_rss) && empty($m)) { break; } } } ?>
    </channel>
</rss>