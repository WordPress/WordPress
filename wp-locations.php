<?php
include('wp-blog-header.php');
header('Content-type: text/xml; charset=' . get_settings('blog_charset'));
?><?php echo "<?xml version=\"1.0\"?".">\n"; ?>
<travels>
<?php
$start = count($posts)-1;
for ($i = $start; $i >= 0; $i--) {
    $post = $posts[$i];
    start_wp();
    if ((get_Lon() != null) && (get_Lon > -360) && (get_Lon() < 360 )) {
?>
    <location arrival="<?php the_date_xml() ?>">
        <name><?php the_title_rss() ?></name>
        <latitude><?php print_Lat() ?></latitude>
        <longitude><?php print_Lon() ?></longitude>
<?php
        if (get_settings('rss_use_excerpt')) {
?>
        <note><?php the_content_rss('', 0, '', get_settings('rss_excerpt_length'), get_settings('rss_encoded_html')) ?>
        </note>
<?php
        } else { // use content
?>
        <note><?php the_excerpt_rss('', 0, '', get_settings('rss_excerpt_length'), get_settings('rss_encoded_html')) ?></note>
<?php
        } // end else use content
?>
        <url><?php permalink_single_rss() ?></url>
    </location>
<?php
    } // end if lon valid
?>
<?php
} // end loop
?>
</travels>
