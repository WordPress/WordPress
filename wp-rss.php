<?php /* These first lines are the first part of a CafeLog template.
         In every template you do, you got to copy them before the CafeLog 'loop' */
if (! $feed) {
    $blog = 1; // enter your blog's ID
    $doing_rss = 1;
    require('wp-blog-header.php');
}

header('Content-type: text/xml', true);

?>
<?php echo '<?xml version="1.0" encoding="'.get_settings('blog_charset').'"?'.'>'; ?>
<!-- generator="wordpress/<?php echo $wp_version ?>" -->
<rss version="0.92">
    <channel>
        <title><?php bloginfo_rss("name") ?></title>
        <link><?php bloginfo_rss("url") ?></link>
        <description><?php bloginfo_rss("description") ?></description>
        <lastBuildDate><?php echo mysql2date('D, d M Y H:i:s +0000', get_lastpostmodified('GMT')); ?></lastBuildDate>
        <docs>http://backend.userland.com/rss092</docs>
        <language><?php echo get_settings('rss_language'); ?></language>

<?php $items_count = 0; if ($posts) { foreach ($posts as $post) { start_wp(); ?>
        <item>
            <title><?php the_title_rss() ?></title>
<?php
// we might use this in the future, but not now, that's why it's commented in PHP
// so that it doesn't appear at all in the RSS
//          echo "<category>"; the_category_unicode(); echo "</category>";
$more = 1; 
if (get_settings('rss_use_excerpt')) {
?>
            <description><?php the_excerpt_rss(get_settings('rss_excerpt_length'), get_settings('rss_encoded_html')) ?></description>
<?php
} else { // use content
?>
            <description><?php the_content_rss('', 0, '', get_settings('rss_excerpt_length'), get_settings('rss_encoded_html')) ?></description>
<?php
} // end else use content
?>
            <link><?php permalink_single_rss() ?></link>
        </item>
<?php $items_count++; if (($items_count == get_settings('posts_per_rss')) && empty($m)) { break; } } } ?>
    </channel>
</rss>