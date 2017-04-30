<?php
/**
 * EventON Admin Functions
 *
 * Hooked-in functions for EventON related events in admin.
 *
 * @author 		AJDE
 * @category 	Admin
 * @package 	EventON/Admin
 * @version     1.1
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly




/**
 * Prevent non-admin access to backend
 */
add_filter( 'tiny_mce_version', 'eventon_refresh_mce' ); 
 
function eventon_prevent_admin_access() {
	if ( get_option('eventon_lock_down_admin') == 'yes' && ! is_ajax() && ! ( current_user_can('edit_posts') || current_user_can('manage_eventon') ) ) {
		//wp_safe_redirect(get_permalink(woocommerce_get_page_id('myaccount')));
		exit;
	}
}


// ADD custom "add eventon shortcode" button next to add media 
add_action('media_buttons_context',  'eventon_shortcode_button');
function eventon_shortcode_button($context) {
	
	global $pagenow, $typenow, $post;	
	
	if ( $typenow == 'post' && ! empty( $_GET['post'] ) ) {
		$typenow = $post->post_type;
	} elseif ( empty( $typenow ) && ! empty( $_GET['post'] ) ) {
        $post = get_post( $_GET['post'] );
        $typenow = $post->post_type;
    }
	
	if ( $typenow == '' || $typenow == "ajde_events" ) return;

	//our popup's title
  	$text = '[ ] ADD EVENTON';
  	$title = 'eventON Shortcode generator';

  	//append the icon
  	$context .= "<a id='evo_shortcode_btn' class='eventon_popup_trig evo_admin_btn btn_prime' title='{$title}' href='#'>{$text}</a>";
	
	eventon_shortcode_pop_content();
	
  	return $context;
}

/* eventON shortcode generator button for WYSIWYG editor */
 add_action('init', 'eventon_shortcode_button_init');
 function eventon_shortcode_button_init() {

 	global $pagenow, $typenow, $post;	
	
	if ( $typenow == 'post' && ! empty( $_GET['post'] ) ) {
		$typenow = $post->post_type;
	} elseif ( empty( $typenow ) && ! empty( $_GET['post'] ) ) {
        $post = get_post( $_GET['post'] );
        $typenow = $post->post_type;
    }
	
	if ( $typenow == '' || $typenow == "ajde_events" ) return;
	

      //Abort early if the user will never see TinyMCE
      if ( ! current_user_can('edit_posts') && ! current_user_can('edit_pages') && get_user_option('rich_editing') == 'true')
           return;

      //Add a callback to regiser our tinymce plugin   
      add_filter("mce_external_plugins", "eventon_register_tinymce_plugin"); 

      // Add a callback to add our button to the TinyMCE toolbar
      add_filter('mce_buttons', 'eventon_add_tinymce_button');
}


//This callback registers our plug-in
function eventon_register_tinymce_plugin($plugin_array) {
    $plugin_array['eventon_shortcode_button'] = AJDE_EVCAL_URL.'/assets/js/admin/shortcode.js';
    return $plugin_array;
}

//This callback adds our button to the toolbar
function eventon_add_tinymce_button($buttons) {
            //Add the button ID to the $button array
    $buttons[] = "eventon_shortcode_button";
    return $buttons;
}




/**
 * Short code popup content
 */
function eventon_shortcode_pop_content(){
	global $evo_shortcode_box, $eventon;
	$content='';
	
	require_once(AJDE_EVCAL_PATH.'/classes/shortcodes/class-shortcode_box_generator.php');
	
	$content = $evo_shortcode_box->get_content();
	
	// eventon popup box
	echo $eventon->output_eventon_pop_window(array(
		'content'=>$content, 
		'class'=>'eventon_shortcode', 
		'attr'=>'clear="false"', 
		'title'=>'Shortcode Generator',
		//'subtitle'=>'Select option to customize shortcode variable values'
	));
}





/**
 * Force TinyMCE to refresh.
 */
function eventon_refresh_mce( $ver ) {
	$ver += 3;
	return $ver;
}


// SAVE: closed meta field boxes
function eventon_save_collapse_metaboxes( $page, $post_value ) {
	
	if(empty($post_value)) return;
	
	$user_id = get_current_user_id();
	$option_name = 'closedmetaboxes_' . $page; // use the "pagehook" ID
	
	$meta_box_ids = array_unique(array_filter(explode(',',$post_value)));
	
	$meta_box_id_ar =serialize($meta_box_ids);
	
	update_user_option( $user_id, $option_name,  $meta_box_id_ar , true );
	
}

function eventon_get_collapse_metaboxes($page){
	
	$user_id = get_current_user_id();
    $option_name = 'closedmetaboxes_' . $page; // use the "pagehook" ID
	$option_arr = get_user_option( $option_name, $user_id );
	
	if(empty($option_arr)) return;
	
	return unserialize($option_arr);
	//return ($option_arr);
	
}





?>