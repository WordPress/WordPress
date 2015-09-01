<?php

// Header fix if needed
//ob_start();


// Cleaner theme calls...and easier to remember :)
define('BASE_URL',get_bloginfo('url'));
define('THEME_URL',get_bloginfo('template_url'));
define('CURRENT_PATH','http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']);


// Remove Items from wp_head()
remove_action('wp_head', 'feed_links_extra', 3); // Display the links to the extra feeds such as category feeds
remove_action('wp_head', 'feed_links', 2); // Display the links to the general feeds: Post and Comment Feed
remove_action('wp_head', 'rsd_link'); // Display the link to the Really Simple Discovery service endpoint, EditURI link
remove_action('wp_head', 'wlwmanifest_link'); // Display the link to the Windows Live Writer manifest file.
remove_action('wp_head', 'index_rel_link'); // index link
remove_action('wp_head', 'parent_post_rel_link'); // prev link
remove_action('wp_head', 'wp_generator'); // Display the XHTML generator that is generated on the wp_head hook, WP version
remove_action('wp_head', 'rel_canonical');
remove_action('wp_head', 'previous_post_link');
remove_action('wp_head', 'next_post_link');
remove_action('wp_head', 'post_permalink');
remove_action('wp_head', 'the_permalink');
remove_action('wp_head', 'start_post_rel_link', 10, 0 );
remove_action('wp_head', 'adjacent_posts_rel_link_wp_head', 10, 0);


// Disable file editing
define('DISALLOW_FILE_EDIT', true);


/* Disable WordPress Admin Bar for all users but admins. */
show_admin_bar(false);


################################################
#
#  Add CSS styles as a dropdown option in the Editor
#
################################################


add_filter('tiny_mce_before_init', 'add_custom_classes');


function add_custom_classes($initArray) {


 $initArray['theme_advanced_styles'] = "Banner Item=page-title banner__item,Section title=section-title,Bold=section-title__highlight";


 $initArray['theme_advanced_buttons2_add_before'] = 'styleselect';


 return $initArray;

}


################################################
#
#  Remove WP Upgrade message
#
################################################

add_action('admin_menu','wphidenag');

function wphidenag() {

    remove_action( 'admin_notices', 'update_nag', 3 );

}

################################################
#
#  Add menus
#
################################################

function add_custom_nav() {

    register_nav_menus(

        array(

            'main_navigation_menu' => 'Main Menu',

            'footer_links' => 'Footer links'

        )

    );

}



add_action( 'init', 'add_custom_nav' );


################################################
#
#  Allow SVG uploads
#
################################################

function cc_mime_types( $mimes ){

    $mimes['svg'] = 'image/svg+xml';

    return $mimes;

}

add_filter( 'upload_mimes', 'cc_mime_types' );

################################################
#
#  Create Custom Post Types
#
################################################

function theme_layout() {

        register_post_type( 'layout', array(

                'labels' => array(

                        'name' => 'Layout',

                        'singular_name' => 'layout',

                ),

                'public' => false,

                'exclude_from_search' => true,

                'show_ui' => true,

                'show_in_menu' => 'themes.php',

                'supports' => array( 'title' ,'thumbnail', 'editor' ),

        ) );

}



add_action( 'init', 'theme_layout' );


// function careers() {

//         register_post_type( 'jobs', array(

//                 'labels' => array(

//                         'name' => 'Careers',

//                         'singular_name' => 'career',

//                 ),

//                 'public' => true,

//                 'exclude_from_search' => false,

//                 'show_ui' => true,

//                 'rewrite' => array('slug' => 'career','with_front' => false),

//                 'supports' => array( 'title' ,'thumbnail', 'editor' ),

//         ) );

// }



// add_action( 'init', 'careers' );



################################################
#
#  Hide sensitive menus from client
#
################################################


add_action( 'admin_menu', 'remove_menu_pages' );

function remove_menu_pages(){


    global $current_user;

    if ($current_user->ID != 1){

        remove_menu_page('edit-comments.php');

        remove_submenu_page('themes.php','themes.php');

        remove_submenu_page('themes.php','customize.php');

        remove_submenu_page('themes.php','theme-editor.php');

        remove_menu_page('tools.php');

        remove_menu_page('plugins.php');

        remove_submenu_page('index.php','update-core.php');

        remove_menu_page('options-general.php');

        remove_menu_page('edit.php?post_type=acf');

    }

    //remove_menu_page('edit.php');

}

add_action( 'admin_head', 'admin_css' );


function admin_css(){

    global $current_user;

    if ($current_user->ID != 1){

        echo '<style>.update-nag, #wp-version-message a.button { display:none; } #dashboard_recent_comments, #dashboard_quick_press, #dashboard_incoming_links, #dashboard_recent_drafts, #dashboard_primary, #dashboard_secondary, #dashboard_plugins { display:none!important; }</style>';

    }

}



################################################
#
#  Change login logo
#
################################################

add_action("login_head", "client_logo");

function client_logo() {

    echo "

    <style>

    body.login { background:#f7f7f5; }

    body.login #login h1 a {

        background: url('".get_bloginfo('template_url')."/img/jb-logo.gif') no-repeat scroll center top transparent;

        height: 80px;

        width: 336px;

    }

    .login #nav a, .login #backtoblog a { color:#ffffff!important; text-shadow:none!important; }

    .wp-core-ui .button-primary { background:#dd5136!important; border:none; text-shadow:none; }

    </style>

    ";

}

################################################
#
#  Breadcrumbs
#
################################################

function get_breadcrumb() {


    global $post;
 

    $trail = '<ol class="breadcrumb"><li class="breadcrumb__item"><a class="breadcrumb__anchor" href="'.BASE_URL.'">Home</a></li>';

    $page_title = get_the_title($post->ID);

 

    if($post->post_parent) {



        $parent_id = $post->post_parent;

 

        while ($parent_id) {

            $page = get_page($parent_id);

            $breadcrumbs[] = '<li class="breadcrumb__item"><a class="breadcrumb__anchor" href="' . get_permalink($page->ID) . '">' . get_the_title($page->ID) . '</a></li>';

            $parent_id = $page->post_parent;

        }

 

        $breadcrumbs = array_reverse($breadcrumbs);

        foreach($breadcrumbs as $crumb) $trail .= $crumb;

    }

 

    $trail .= '<li class="breadcrumb__item">'.$page_title.'</li>';

    $trail .= '</ol>';

 

    return $trail;  

 

}




################################################
#
#  Hide editor for specific page templates.
#
################################################

add_action( 'admin_init', 'hide_editor' );
 

function hide_editor() {

    // Get the Post ID.

    $post_id = isset($_GET['post']) ? $_GET['post'] : '';

    if( !isset( $post_id ) ) return;

 
    // Get the name of the Page Template file.
    $template_file = get_post_meta($post_id, '_wp_page_template', true);

    if($template_file == 'templates/tpl-news.php'){ // edit the template name

        remove_post_type_support('page', 'editor');

    }

}



################################################
#
#  Use $_CLEAN instead of $_POST and $_GET
#
################################################



function clean($elem) 

{ 

    if(!is_array($elem)) 

        $elem = htmlentities($elem,ENT_QUOTES,"UTF-8"); 

    else 

        foreach ($elem as $key => $value) 

            $elem[$key] = $this->clean($value); 

    return $elem; 

}


################################################
#
#  Remove Comments from CMS
#
################################################

// Removes from admin menu

add_action( 'admin_menu', 'my_remove_admin_menus' );

function my_remove_admin_menus() {

    remove_menu_page( 'edit-comments.php' );

}

// Removes from post and pages

add_action('init', 'remove_comment_support', 100);



function remove_comment_support() {

    remove_post_type_support( 'post', 'comments' );

    remove_post_type_support( 'page', 'comments' );

}

// Removes from admin bar

function mytheme_admin_bar_render() {

    global $wp_admin_bar;

    $wp_admin_bar->remove_menu('comments');

}

add_action( 'wp_before_admin_bar_render', 'mytheme_admin_bar_render' );



################################################
#
#  Limit words
#
################################################

function limit_words($str, $len) {

  $tail = max(0, $len-10);

  $trunk = substr($str, 0, $tail);

  $trunk .= strrev(preg_replace('~^..+?[\s,:]\b|^...~', '...', strrev(substr($str, $tail, $len-$tail))));

  return $trunk;

}

################################################
#
#  Create contact form table
#
################################################


// function create_contact_form_table(){

//     global $wpdb;



//     $table_name = $wpdb->prefix . "contact_form";



//     $create_table_sql = "CREATE TABLE $table_name (

//         id INT(11) NOT NULL AUTO_INCREMENT,

//         user_name VARCHAR(255) NOT NULL,

//         user_email VARCHAR(255) NOT NULL,

//         user_time INT(11) NOT NULL,

//         user_ip VARCHAR(255) NOT NULL,

//         UNIQUE KEY id (id)

//     );";



//     require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );



//     dbDelta($create_table_sql);





// }



// create_contact_form_table();




################################################
#
#  Pagination
#
################################################

function jb_pagination($post_type) {

    if( is_singular($post_type) )

        return;

    global $wp_query;



    //print_r($wp_query);


    /** Stop execution if there's only 1 page */

    if( $wp_query->max_num_pages <= 1 )

        return;



    $paged = get_query_var( 'paged' ) ? absint( get_query_var( 'paged' ) ) : 1;

    $max   = intval( $wp_query->max_num_pages );



    /** Add current page to the array */

    if ( $paged >= 1 )

        $links[] = $paged;



    /** Add the pages around the current page to the array */

    if ( $paged >= 3 ) {

        $links[] = $paged - 1;

        $links[] = $paged - 2;

    }



    if ( ( $paged + 2 ) <= $max ) {

        $links[] = $paged + 2;

        $links[] = $paged + 1;

    }



    echo '<ol class="pagination">' . "\n";



    /** Previous Post Link */

    if ( get_previous_posts_link() )

        printf( '<li class="pagination__item pagination__item--bookend">%s</li>' . "\n", str_replace('<a ','<a class="pagination__link" ',get_previous_posts_link()) );



    /** Link to first page, plus ellipses if necessary */

    if ( ! in_array( 1, $links ) ) {

        $class = 1 == $paged ? ' class="pagination__link pagination__link--active"' : ' class="pagination__link"';



        printf( '<li class="pagination__item"><a%s href="%s">%s</a></li>' . "\n", $class, esc_url( get_pagenum_link( 1 ) ), '1' );



        if ( ! in_array( 2, $links ) )

            echo '<li>…</li>';

    }



    /** Link to current page, plus 2 pages in either direction if necessary */

    sort( $links );

    foreach ( (array) $links as $link ) {

        $class = $paged == $link ? ' class="pagination__link pagination__link--active"' : ' class="pagination__link"';

        printf( '<li class="pagination__item"><a%s href="%s">%s</a></li>' . "\n", $class, esc_url( get_pagenum_link( $link ) ), $link );

    }



    /** Link to last page, plus ellipses if necessary */

    if ( ! in_array( $max, $links ) ) {

        if ( ! in_array( $max - 1, $links ) )

            echo '<li>…</li>' . "\n";



        $class = $paged == $max ? ' class="pagination__link pagination__link--active"' : ' class="pagination__link"';

        printf( '<li class="pagination__item"><a%s href="%s">%s</a></li>' . "\n", $class, esc_url( get_pagenum_link( $max ) ), $max );

    }



    /** Next Post Link */

    if ( get_next_posts_link() )

        printf( '<li class="pagination__item pagination__item--bookend">%s</li>' . "\n", str_replace('<a ','<a class="pagination__link" ',get_next_posts_link()) );



    echo '</ol>' . "\n";



}



################################################
#
#  Get First sentence from string
#
################################################



function first_sentence($content) {



    $content = html_entity_decode(strip_tags($content));

    $pos = strpos($content, '.');

       

    if($pos === false) {

        return $content;

    }

    else {

        return substr($content, 0, $pos+1);

    }

   

}

