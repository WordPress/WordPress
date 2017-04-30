<?php

function kd_table_names() {

global $wpdb;

	$base_prefix = $wpdb->base_prefix;
	
	$table_name_array[1] = $base_prefix . "author_advertising";
	$table_name_array[2] = $base_prefix . "author_advertising_ad_google";
    $table_name_array[3] = $base_prefix . "author_advertising_ad_rotator";
	
	return $table_name_array;
	
}

/////////////////////////////////

function kd_db_columns($table_num) {

//db_columns

Switch ($table_num) {

	case 1:

	$db_columns = array(1 => 'id' , 'author_id', 'his_referral_id' , 'author_advertising' , 'author_custom1' , 'author_custom2' , 'author_custom3' , 'author_custom4' , 'author_custom5' , 'author_custom6' , 'author_percentage' , 'author_incentive' );
	break;
	
	case 2:
	
	$db_columns = array(1=> 'id' , 'ad_active', 'ad_type' , 'ad_type2' , 'ad_style' , 'ad_links' , 'ad_format' , 'ad_client' , 'personal_ad_slot', 'ad_slot' , 'ad_width' , 'ad_height' , 'ad_channel' , 'ad_color_border' , 'ad_color_bg' , 'ad_color_link' , 'ad_color_text' , 'ad_color_url' , 'ad_ui_features' , 'ad_generated_code' , 'ad_excluded_categories' , 'ad_included_categories' , 'ad_css' , 'ad_position_begin' , 'ad_position_end' , 'ad_tag' , 'ad_comment', 'ad_position_center');
	break;

	
	case 3:
	
	$db_columns = array(1=> 'id' , 'ad_code' , 'ad_custom1' , 'ad_custom2' );
	break;
	
}


return $db_columns;


}
   

/////////////////////////////////////////////////////////////////////
   
function kd_db_add_columns_code($table_num) {

//db_add_columns_code
Switch ($table_num) {

	case 1:
		
	$db_add_columns = array(1 => 'id mediumint(9) NOT NULL auto_increment' , 'author_id int(11) NOT NULL default \'0\'', 'his_referral_id int(11) default \'0\'' , 'author_advertising text NOT NULL' , 'author_custom1 text' , 'author_custom2 text' , 'author_custom3 text' , 'author_custom4 text' , 'author_custom5 text' , 'author_custom6 text' , 'author_percentage int(3)' , 'author_incentive text' );
	break;
	
   
	case 2:
	
	$db_add_columns = array(1 => 'id mediumint(9) NOT NULL auto_increment' , 'ad_active text' , 'ad_type int(3) NOT NULL default \'0\'' , 'ad_type2 int(3) NOT NULL default \'0\'' , 'ad_style int(3) default \'2\'' , 'ad_links int(3) default \'4\'' , 'ad_format int(3) NOT NULL default \'1\'' , 'ad_client text' , 'personal_ad_slot int(3)', 'ad_slot text' , 'ad_width text' , 'ad_height text' , 'ad_channel text' , 'ad_color_border text' , 'ad_color_bg text' , 'ad_color_link text' , 'ad_color_text text' , 'ad_color_url text' , 'ad_ui_features int(3) default \'0\'' , 'ad_generated_code text' , 'ad_excluded_categories text' , 'ad_included_categories text' , 'ad_css text' , 'ad_position_begin text' , 'ad_position_end text' , 'ad_tag text' , 'ad_comment text', 'ad_position_center text');
	
	break;
   
	case 3:
	
	$db_add_columns = array(1 => 'id mediumint(9) NOT NULL auto_increment' , 'ad_code text NOT NULL' , 'ad_custom1 text' , 'ad_custom2 text' );
	break;
	
}
		



return $db_add_columns;


}

//////////////////////

?>