<?php
    $rootCategory = get_category_root_slug(the_category_ID(false));
    if ( $rootCategory === 'video')
    {
    include(TEMPLATEPATH .'/category-video.php');
    }
    elseif ( $rootCategory === 'news')
    {
    include(TEMPLATEPATH . '/category-news.php');
    }
    elseif ( $rootCategory === 'joinus')
    {
    include(TEMPLATEPATH . '/category-joinus.php');
    }
    else{
    return;
    }
?>