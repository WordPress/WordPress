<?php

if (! $doing_rss) {
    $doing_rss = 1;
    require('wp-blog-header.php');
}

// Remove the pad, if present.
$feed = preg_replace('/^_+/', '', $feed);

if ($feed == '' || $feed == 'feed') {
    // TODO:  Get default feed from options DB.
    $feed = 'rss2';
}

if ( is_single() || ($withcomments == 1) ) {
    require('wp-commentsrss2.php');
} else {
    switch ($feed) {
    case 'atom':
        require('wp-atom.php');
        break;
    case 'rdf':
        require('wp-rdf.php');
        break;
    case 'rss':
        require('wp-rss.php');
        break;
    case 'rss2':
        require('wp-rss2.php');
        break;
    }
}

?>
