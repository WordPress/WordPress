<?php /* These first lines are the first part of a CaféLog template.
         In every template you do, you got to copy them before the CaféLog 'loop' */
$blog=1; // enter your blog's ID
header("Content-type: text/xml");
include ("blog.header.php");
if (!isset($rss_language)) { $rss_language = 'en'; }
if (!isset($rss_encoded_html)) { $rss_encoded_html = 0; }
if (!isset($rss_excerpt_length) || ($rss_encoded_html == 1)) { $rss_excerpt_length = 0; }
?><?php echo "<?xml version=\"1.0\"?".">"; ?>
<!-- generator="wordpress/<?php echo $b2_version ?>" -->
<rss version="0.92">
    <channel>
        <title><?php bloginfo_rss("name") ?></title>
        <link><?php bloginfo_rss("url") ?></link>
        <description><?php bloginfo_rss("description") ?></description>
        <lastBuildDate><?php echo gmdate("D, d M Y H:i:s"); ?> GMT</lastBuildDate>
        <docs>http://backend.userland.com/rss092</docs>
        <managingEditor><?php echo $admin_email ?></managingEditor>
        <webMaster><?php echo $admin_email ?></webMaster>
        <language><?php echo $rss_language ?></language>

<?php $items_count = 0; while($row = mysql_fetch_object($result)) { start_b2(); ?>
        <item>
            <title><?php the_title_rss() ?></title>
<?php
// we might use this in the future, but not now, that's why it's commented in PHP
// so that it doesn't appear at all in the RSS
//          echo "<category>"; the_category_unicode(); echo "</category>";
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
<?php $items_count++; if (($items_count == $posts_per_rss) && empty($m)) { break; } } ?>
    </channel>
</rss>