<?php

function kd_aa_widget($args, $number=1){


//GET VALUES
 global $wpdb;

   $base_prefix = $wpdb->base_prefix;
   $table_name_ad_google = $base_prefix . "author_advertising_ad_google";


extract($args);
$options = get_option('kd_aa_widget');
$title = $options[$number]['title'];
$google_values = get_option('kd_author_advertising');
$selected_id = get_option("kd_aa_widget_id");
if($selected_id >= 1 ){ 


$widget_position = get_option("kd_aa_widget_pos");

			$widget_display = true;

			if ( is_home() && $widget_position['home'] == "on" ) { $widget_display = false; }
			if ( is_archive() && $widget_position['archive'] == "on" ) { $widget_display = false; }
			if ( is_category() && $widget_position['category'] == "on" ) { $widget_display = false; } 
			if ( is_page() && $widget_position['page'] == "on" ) { $widget_display = false; } 
			if ( is_single() && $widget_position['post'] == "on" ) { $widget_display = false; } 

			
			if ($widget_display) { $widget_content = kd_template_ad($selected_id); }

}
   //echo $before_widget . $before_title . $title . $after_title . $widget_content . $after_widget;
   echo $widget_content; // fix 5.1.0
}

function kd_aa_widget_control($number) {
   $options = $newoptions = get_option('kd_aa_widget');
   if ( !is_array($options) )
      $options = $newoptions = array();
   if ( $_POST["kd_aa_widget-$number"] ) {
      $newoptions[$number]['title'] = strip_tags(stripslashes($_POST["kd_aa_widget-$number"]));
      }
   if ( $options != $newoptions ) {
      $options = $newoptions;
      update_option('kd_aa_widget', $options);
   }
   $title = attribute_escape($options[$number]['title']);
?>
<p><label for="advertising_widget-title"><?php _e('Titolo:'); ?> <input style="width: 250px;" id="kd_aa_widget-<?php echo $number; ?>" name="kd_aa_widget-<?php echo $number; ?>" type="text" value="<?php echo $title; ?>" /></label></p>
<input type="hidden" id="kd_aa_widget-submit-<?php echo "$number"; ?>" name="kd_aa_widget-submit-<?php echo "$number"; ?>" value="1" />
<?php
}

function kd_aa_widget_setup() {
if (function_exists('register_sidebar_widget')){
   $options = $newoptions = get_option('kd_aa_widget');
   if ( isset($_POST['kd_aa_widget-submit']) ) {
      $number = (int) $_POST['kd_aa_widget-number'];
      if ( $number > 3 ) $number = 3;
      if ( $number < 1 ) $number = 1;
      $newoptions['number'] = $number;
   }
   if ( $options != $newoptions ) {
      $options = $newoptions;
      update_option('kd_aa_widget', $options);
      kd_aa_widget_register($options['number']);
   }
kd_aa_widget_register();
}
}

function kd_aa_widget_page() {
   $options = $newoptions = get_option('kd_aa_widget');
?>
   <div class="wrap">
      <form method="POST">
         <h2><?php _e('Author Advertising Pro Widgets'); ?></h2>
         <p style="line-height: 30px;"><?php _e('Di quanti Widget hai bisogno?'); ?>
         <select id="kd_aa_widget-number" name="kd_aa_widget-number" value="<?php echo $options['number']; ?>">
<?php for ( $i = 1; $i < 4; ++$i ) echo "<option value='$i' ".($options['number']==$i ? "selected='selected'" : '').">$i</option>"; ?>
         </select>
         <span class="submit"><input type="submit" name="kd_aa_widget-submit" id="kd_aa_widget-submit" value="<?php echo attribute_escape(__('Salva')); ?>" /></span></p>
      </form>
   </div>
<?php
}

function kd_aa_widget_register() {
   $options = get_option('kd_aa_widget');
   $number = $options['number'];
   if ( $number < 1 ) $number = 1;
   if ( $number > 3 ) $number = 3;
   $class = array('classname' => 'kd_aa_widget');
   for ($i = 1; $i <= 3; $i++) {
      $name = sprintf(__('Author Advertising Pro %d'), $i);
      $id = "advertising-$i"; // Never never never translate an id
      wp_register_sidebar_widget($id, $name, $i <= $number ? 'kd_aa_widget' : /* unregister */ '', $class, $i);
      wp_register_widget_control($id, $name, $i <= $number ? 'kd_aa_widget_control' : /* unregister */ '', $dims, $i);
   }
   add_action('sidebar_admin_setup', 'kd_aa_widget_setup');
   add_action('sidebar_admin_page', 'kd_aa_widget_page');
}

function kd_aa_widget_admin() {

//GET VALUES
 global $wpdb;

   $base_prefix = $wpdb->base_prefix;
   $table_name_ad_google = $base_prefix . "author_advertising_ad_google";



   //check if Plugin need to install his db table.
   $kd_need_install = kd_check_install();
   
   //check if Plugin need to update db table column for a new version.
   $kd_need_update = kd_check_update();
   
   if ($kd_need_install) {
   
   kd_installer();
   
   } elseif ($kd_need_update) {
   
   kd_updater();
   
   } else {
   
		if (isset($_POST['ads_id'])) {
		
			$widget_position['home'] = $_POST['widget_in_home'];
			$widget_position['archive'] = $_POST['widget_in_archive'];
			$widget_position['category'] = $_POST['widget_in_category'];
			$widget_position['page'] = $_POST['widget_in_page'];
			$widget_position['post'] = $_POST['widget_in_post'];
		
			update_option("kd_aa_widget_pos", $widget_position);
			update_option("kd_aa_widget_id", $_POST['ads_id']);
			
			}

   echo kd_G_icon();
   echo "<h2>", _e('Author Advertising Pro - Widget', 'author-advertising-pro'), "</h2><br/>";
   
   echo "<div class='wrap' style=''><br/>";
   
   echo kd_menu_application();
   
   echo "<div>", kd_help_icon(), "<div style='width:70%;text-align:justify;'>";
   
   echo "<p><b>", _e('In questa sezione potrai selezionare l\'id dell\'annuncio Adsense che desideri utilizzare come widget.', 'author-advertising-pro'), "</b></p>";
   
   echo "</div></div><hr/>";

   echo "<div style='height:auto;width:100%;float:left;clear:both;margin: 15px 0 0 20px;'>";
   echo "<div style='height:auto;width:100%;float:left;clear:both;margin: 10px 0 10px 20px;'>";
   echo "<form name='id_select_form' action='". esc_attr($_SERVER['REQUEST_URI']) ."' method='POST'>";
   echo "<p><b>", _e('Scegli l\'ID dell\'annuncio Adsense che vuoi usare come Widget:', 'author-advertising-pro'), "</b></p>";
   echo _e('ID Annuncio:', 'author-advertising-pro'), "  ";

   echo "<select name=\"ads_id\" onchange=\"submit();\" >";
   
   echo '<optgroup label="', _e('Seleziona ID Annuncio', 'author-advertising-pro'), '">';
  
  
  $selected_id = get_option("kd_aa_widget_id");
//select ads id's  stored into db table
$sql  = "SELECT id FROM $table_name_ad_google";
$result = mysql_query($sql);

while($row = mysql_fetch_array($result))
{
  $id = $row['id'];
	if ($selected_id == $id) { $selected_value = "selected='selected'"; } else { $selected_value = ""; }
  
  echo "<option value='$id' $selected_value >$id</option>";
  
} 
   
    echo "</optgroup></select>";
	
			//CHECK WIDGET_POSITION_VALUE
			
			$widget_position = get_option("kd_aa_widget_pos");
			
   		if ($widget_position['home'] == 'on') { $checked_widget_home = "checked='checked'"; } else { $checked_widget_home = ""; }
		if ($widget_position['archive'] == 'on') { $checked_widget_archive = "checked='checked'"; } else { $checked_widget_archive = ""; }
		if ($widget_position['category'] == 'on') { $checked_widget_category = "checked='checked'"; } else { $checked_widget_category = ""; }
		if ($widget_position['page'] == 'on') { $checked_widget_page = "checked='checked'"; } else { $checked_widget_page = ""; }
		if ($widget_position['post'] == 'on') { $checked_widget_post = "checked='checked'"; } else { $checked_widget_post = ""; }
	
	echo "<br/><br/>";
	echo "<label>", _e('Nascondi in Home:', 'author-advertising-pro'), " <input type='checkbox' name='widget_in_home' $checked_widget_home /></label><br/>";
	echo "<label>", _e('Nascondi in Archivi:', 'author-advertising-pro'), " <input type='checkbox' name='widget_in_archive' $checked_widget_archive /></label><br/>";
	echo "<label>", _e('Nascondi in Categorie:', 'author-advertising-pro'), " <input type='checkbox' name='widget_in_category' $checked_widget_category /></label><br/>";
	echo "<label>", _e('Nascondi in Pagine:', 'author-advertising-pro'), " <input type='checkbox' name='widget_in_page' $checked_widget_page /></label><br/>";
	echo "<label>", _e('Nascondi in Articoli:', 'author-advertising-pro'), " <input type='checkbox' name='widget_in_post' $checked_widget_post /></label><br/>";
	
	echo "<p class='submit'><input type='submit' name='save' value='", _e('Salva le modifiche', 'author-advertising-pro'), "' /></p>";
	
	echo "</form>";
   
   echo "</div>";
   
   //FINE BLOCCO SELECT ID 
   

}
}
?>