<?php // CONTENT WIDTH & feedlinks 

	if ( ! isset( $content_width ) ) $content_width = 900;
	add_theme_support( 'automatic-feed-links' );

?>
<?php // REPLY comment script 

	function fullby_enqueue_comments_reply() {
		if( get_option( 'thread_comments' ) )  {
			wp_enqueue_script( 'comment-reply' );
		}
	}
	add_action( 'comment_form_before', 'fullby_enqueue_comments_reply' );
	
?>
<?php // MENU 

	add_action( 'after_setup_theme', 'wpt_setup' );
    if ( ! function_exists( 'wpt_setup' ) ):
        function wpt_setup() { 
            register_nav_menu( 'primary', __( 'Primary navigation', 'wptuts' ) );
            register_nav_menu( 'secondary', __( 'Secondary navigation', 'wptuts' ) );
    } endif;
?>
<?php // BOOTSTRAP MENU - Custom navigation walker (Required)

    require_once('wp_bootstrap_navwalker.php');
    
?>
<?php // CUSTOM THUMBNAIL 

	add_theme_support('post-thumbnails');
	
	if ( function_exists('add_theme_support') ) {
		add_theme_support('post-thumbnails');
	}
	
	if ( function_exists( 'add_image_size' ) ) { 
		add_image_size( 'quad', 400, 400, true ); //(cropped)
		add_image_size( 'single', 800, 494, true ); //(cropped)
	}

?>
<?php // WIDGET SIDEBAR 

	if ( function_exists('register_sidebar') )
		register_sidebar(array('name'=>'Primary Sidebar',
		'before_widget' => '<div id="%1$s" class="widget %2$s">',	
		'after_widget' => '</div>',
		'before_title' => '<h3>',
		'after_title' => '</h3>',
	));
	register_sidebar(array('name'=>'Secondary Sidebar',
		'before_widget' => '<div id="%1$s" class="widget %2$s">',	
		'after_widget' => '</div>',
		'before_title' => '<h3>',
		'after_title' => '</h3>',
	));

?>
<?php // METABOX POST (Video,[...])

add_action( 'add_meta_boxes', 'meta_box_post' );

	function meta_box_post( $post ) {
	
	    add_meta_box(
	            'meta-box-post', // ID, should be a string
	            'YouTube Video', // Meta Box Title
	            'meta_box_post_content', // Your call back function, this is where your form field will go
	            'post', // The post type you want this to show up on, can be post, page, or custom post type
	            'normal', // The placement of your meta box, can be normal or side
	            'high' // The priority in which this will be displayed
	        );
	        
	}
	
	// Content for the custom meta box
	function meta_box_post_content() {
	
		// info current post
	    global $post;
	    
	    //metabox value if is saved
	    $fullby_video = get_post_meta($post->ID, 'fullby_video', true);
	    // ADD here more custom field 	    
	    
	    // security check
	    wp_nonce_field(__FILE__, 'fullby_nonce');
	    ?>
	    <p>To show a video in the article paste the id of a YouTube video in the box below. <br/><input name="fullby_video" id="fullby_video" value="<?php echo $fullby_video; ?>" style="border: 1px solid #ccc; margin: 10px 10px 0 0"/> <small>If the url is http://www.youtube.com/watch?v=<strong>UWHeEI7aOvc</strong>, the ID is <strong>UWHeEI7aOvc</strong>.</small></p>
	    <!-- *** ADD here more custom field  *** -->	    
	    
	    <?php
		
	}

// save function only when save
add_action('save_post', 'save_resource_meta');

	function save_resource_meta(){
    // post info
	    global $post;
	    // don't autosave metabox
	    if ( defined('DOING_AUTOSAVE') && DOING_AUTOSAVE ) {
	        return;
	    }
	    
	    // security check:
	    // chek if hidden field wp_nonce_field()
	    // is correct, if isn't don't save the field
	    if ($_POST && wp_verify_nonce($_POST['fullby_nonce'], __FILE__) ) {
	        // check if the value is in the form
	        if ( isset($_POST['fullby_video']) ) {
	            // save info metabox
	            update_post_meta($post->ID, 'fullby_video', $_POST['fullby_video']);
	            //ADD here more custom field 
	        }
	    }  
	}
?>
<?php // POPULAR POST 

if ( !function_exists('wpb_set_post_views') ) {

	function wpb_set_post_views($postID) {
	    $count_key = 'wpb_post_views_count';
	    $count = get_post_meta($postID, $count_key, true);
	    if($count==''){
	        $count = 0;
	        delete_post_meta($postID, $count_key);
	        add_post_meta($postID, $count_key, '0');
	    }else{
	        $count++;
	        update_post_meta($postID, $count_key, $count);
	    }
	}
}
//To keep the count accurate, lets get rid of prefetching
remove_action( 'wp_head', 'adjacent_posts_rel_link_wp_head', 10, 0);

/* add post views to single page */
if ( !function_exists('wpb_track_post_views') ) {

	function wpb_track_post_views ($post_id) {
	    if ( !is_single() ) return;
	    if ( empty ( $post_id) ) {
	        global $post;
	        $post_id = $post->ID;    
	    }
	    wpb_set_post_views($post_id);
	}
}
add_action( 'wp_head', 'wpb_track_post_views');

?>
<?php // THEME OPTIONS

add_action('admin_menu', 'fullby_theme_page');
function fullby_theme_page ()
{
	if ( count($_POST) > 0 && isset($_POST['fullby_settings']) )
	{
		$options = array ('description','analytics');
		
		foreach ( $options as $opt )
		{
			delete_option ( 'fullby_'.$opt, $_POST[$opt] );
			add_option ( 'fullby_'.$opt, $_POST[$opt] );	
		}			
		 
	}
	add_theme_page('Theme Options', 'Theme Options', 'edit_themes', basename(__FILE__), 'fullby_settings');
	
}
function fullby_settings()
{?>
<div class="wrap">
<h2>SEO Options</h2>
	
<form method="post" action="">
 
    <fieldset style="border:1px solid #ddd; padding:20px; margin-top:20px;">
	<legend style="margin-left:5px; color:#2481C6;text-transform:uppercase;"><strong>SEO</strong></legend>
		<table class="form-table">
        
        <tr>
			<th><label for="description">Meta Description</label></th>
			<td>
				<textarea name="description" id="description" rows="7" cols="70" style="font-size:11px;"><?php echo get_option('fullby_description'); ?></textarea>
			</td>
		</tr>
		<tr>
			<th><label for="ads">Google Analytics code:</label></th>
			<td>
				<textarea name="analytics" id="analytics" rows="7" cols="70" style="font-size:11px;"><?php echo stripslashes(get_option('fullby_analytics')); ?></textarea>
			</td>
		</tr>
        
	</table>
	</fieldset>
    
    <p class="submit">
		<input type="submit" name="Submit" class="button-primary" value="Save Changes" />
		<input type="hidden" name="fullby_settings" value="save" style="display:none;" />
		</p>

</form>
</div>
<?php }?>
<?php // CUSTOM PAGE - Premium Version
add_action( 'admin_menu', 'register_my_custom_menu_page' );

function register_my_custom_menu_page(){
    add_menu_page( 'Fullby Premium', 'FULLBY Premium', 'manage_options', 'custompage', 'my_custom_menu_page', get_template_directory_uri() . '/img/icon-backend.png', 99); 
}

function my_custom_menu_page(){ ?>
<div style="float:left; padding:3% 5% 5% 5%; width:90%; ">

   <h1>Why update to FULLBY Premium ?</h1>
   <h2>A lot of new features for awesome site with easy customization..</h2>
   
   <img src="<?php echo get_template_directory_uri(); ?>/img/features.png" style="width:80%; height:auto; float:left;margin-right:20%"/>
   
   <a href="http://www.marchettidesign.net/fullby/demo.php" target="_blank" style="float:left; display:block; padding: 15px 40px; margin-right:20px; border-radius: 4px;color:#fff; background:#333; font-weight:700; text-decoration:none">LIVE DEMO</a>

   <a href="http://www.marchettidesign.net/shop/cart/?add-to-cart=12" target="_blank" style="float:left; display:block; padding: 15px 40px; margin-right:20px; border-radius: 4px;color:#000; background:#00ecbd; font-weight:700; text-decoration:none">BUY PREMIUM 29$</a>
   
</div>
   
   
<? }

?>
