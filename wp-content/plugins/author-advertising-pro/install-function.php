<?php 

function kd_install(){
   global  $wpdb, $user_level;
   
   $base_prefix = $wpdb->base_prefix;
   $table_name = $base_prefix . "author_advertising";
   $table_name_ad_google = $base_prefix . "author_advertising_ad_google";
   $table_name_ad_rotator = $base_prefix . "author_advertising_ad_rotator";
   
   //1
   //check main db table
   $check_db_table = $wpdb->get_var("show tables like '$table_name'");
   
   if($check_db_table != $table_name){

   $sql = "CREATE TABLE ".$table_name." (
   id mediumint(9) NOT NULL auto_increment,
   author_id int(11) NOT NULL default '0',
   his_referral_id int(11) default '0',
   author_advertising text NOT NULL,
   author_custom1 text,
   author_custom2 text,
   author_custom3 text,
   author_custom4 text,
   author_custom5 text,
   author_custom6 text,
   author_percentage int(3),
   author_incentive text,
   PRIMARY KEY  (`id`)
   ) DEFAULT CHARACTER SET utf8;";

	require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
	$create = $wpdb->query($sql);
	
	echo "<p>", __('Creazione TABELLA', 'author-advertising-pro'), " #1 - ";
	
	if ($create) {
	echo "<span style='color:green;'>OK</span></p>";
	} else {
	echo "<span style='color:red;'>ERROR</span></p>";
	}	
	
	//deprecated
	//dbDelta($sql);
	
   }
   
   //2
   //check google_ads db table
   $check_db2_table = $wpdb->get_var("show tables like '$table_name_ad_google'");
   
   if($check_db2_table != $table_name_ad_google){

   $sql2 = "CREATE TABLE ".$table_name_ad_google." (
   id mediumint(9) NOT NULL auto_increment,
   ad_active text,
   ad_type int(3) NOT NULL default '0',
   ad_type2 int(3) NOT NULL default '0',
   ad_format int(3) NOT NULL default '1',
   ad_style int(3) default '2',
   ad_links int(3) default '4',
   ad_client text,
   personal_ad_slot int(3),
   ad_slot text,
   ad_width text,
   ad_height text,
   ad_channel text,
   ad_color_border text,
   ad_color_bg text,
   ad_color_link text,
   ad_color_text text,
   ad_color_url text,
   ad_ui_features int(3) default '0',
   ad_generated_code text,
   ad_excluded_categories text,
   ad_included_categories text,
   ad_css text,
   ad_position_begin text,
   ad_position_end text,
   ad_tag text,
   ad_comment text,
   ad_position_center text,
   PRIMARY KEY  (`id`)
   ) DEFAULT CHARACTER SET utf8;";

	require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
	$create = $wpdb->query($sql2);
	
	echo "<p>", __('Creazione TABELLA', 'author-advertising-pro'), " #2 - ";
	
	if ($create) {
	echo "<span style='color:green;'>OK</span></p>";
	} else {
	echo "<span style='color:red;'>ERROR</span></p>";
	}	
	
	//deprecated
	//dbDelta($sql);
	
   }
   
   //3
   //check ad_rotator db table
   $check_db3_table = $wpdb->get_var("show tables like '$table_name_ad_rotator'");
   
   if($check_db3_table != $table_name_ad_rotator){

   $sql3 = "CREATE TABLE ".$table_name_ad_rotator." (
   id mediumint(9) NOT NULL auto_increment,
   ad_code text NOT NULL,
   ad_custom1 text,
   ad_custom2 text,
   PRIMARY KEY  (`id`)
   ) DEFAULT CHARACTER SET utf8;";

	require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
	$create = $wpdb->query($sql3);
	
	echo "<p>", __('Creazione TABELLA', 'author-advertising-pro'), " #3 - ";
	
	if ($create) {
	echo "<span style='color:green;'>OK</span></p>";
	} else {
	echo "<span style='color:red;'>ERROR</span></p>";
	}	
	//deprecated
	//dbDelta($sql);
	
   }
   
   
   //Preparazione valori di default
   
   $google_values['debug_mode'] = 0;
   $google_values['admin_username'] = "admin";
   $google_values['admin_xc'] = "25";
   $google_values['editor_xc'] = "0";
   $google_values['full_editor_xc_option'] = 0;
   $google_values['level_user'] = "edit_posts";
   $google_values['myadsense_title'] = __('Codici Adsense', 'author-advertising-pro');
   $google_values['myadsense_text'] = __('<p>In questa pagina puoi inserire il tuo codice personale Adsense "pub-id" ed i codici Ad_slot che avrai creato seguendo le <a href="http://www.your-site-name.ext/guides" target="_blank">nostre guide!</a></p>');
   $google_values['adslot1'] = "1";
   $google_values['adslot1_title'] = __('Esempio: Annuncio 336x280 solo testo', 'author-advertising-pro');
   $google_values['adslot2'] = "0";
   $google_values['adslot2_title'] = "";
   $google_values['adslot3'] = "0";
   $google_values['adslot3_title'] = "";
   $google_values['adslot4'] = "0";
   $google_values['adslot4_title'] = "";
   $google_values['adslot5'] = "0";
   $google_values['adslot5_title'] = "";
   $google_values['adslot6'] = "0";
   $google_values['adslot6_title'] = "";

   update_option("kd_author_advertising", $google_values);


   return $create;
}

?>