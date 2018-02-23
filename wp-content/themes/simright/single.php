<?php
    $rootCategory = get_category_root_slug(the_category_ID(false));
    if ( $rootCategory === 'video')
    {
        include(TEMPLATEPATH .'/single-video.php');
    }
    elseif ( $rootCategory === 'news')
    {
        include(TEMPLATEPATH . '/single-news.php');
    }
    elseif ( $rootCategory === 'joinus')
    {
        include(TEMPLATEPATH . '/single-joinus.php');
    }
    elseif ( $rootCategory === 'blogs')
    {
        include(TEMPLATEPATH . '/single-blogs.php');
    }
    else{
        echo $rootCategory;
        return;
    }
?>