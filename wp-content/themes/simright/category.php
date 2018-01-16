<?php
    if ( in_category('video') || post_is_in_descendant_category('video') )
    {
    include(TEMPLATEPATH .'/category-video.php');
    }
    elseif ( in_category('news') || post_is_in_descendant_category('news') )
    {
    include(TEMPLATEPATH . '/category-news.php');
    }
    elseif ( in_category('joinus') || post_is_in_descendant_category('joinus') )
    {
    include(TEMPLATEPATH . '/category-joinus.php');
    }
    else{
    return;
    }
?>