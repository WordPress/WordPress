<?php

if (empty($doing_rss)) {
    $doing_rss = 1;
    require(dirname(__FILE__) . '/wp-blog-header.php');
}

do_feed();

?>
