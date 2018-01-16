<?php
    if ( in_category('video') || post_is_in_descendant_category('video') )
    {
    include(TEMPLATEPATH .'/single-video.php');
    }
    elseif ( in_category('news') || post_is_in_descendant_category('news') )
    {
    include(TEMPLATEPATH . '/single-news.php');
    }
    elseif ( in_category('joinus') || post_is_in_descendant_category('joinus') )
    {
    include(TEMPLATEPATH . '/single-joinus.php');
    }
    else{
    return;
    }
?>