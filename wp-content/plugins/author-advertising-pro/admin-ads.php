<?php
/*
File Version: 5.1.4
Last Update 13.12.2012
*/

?>
<?php

function kd_admin_google_ads_size_values($ad_format, $ad_type, $ad_links) {

		Switch ($ad_format) {
	
			case 1:
			//
			$ad_format_values['width'] = '300'; //width
			$ad_format_values['height'] = '250'; //height
			break;
			
			case 2:
			//
			$ad_format_values['width'] = '336'; //width
			$ad_format_values['height'] = '280'; //height
			break;
			
			case 3:
			//
			$ad_format_values['width'] = '728'; //width
			$ad_format_values['height'] = '90'; //height
			break;
			
			case 4:
			//
			$ad_format_values['width'] = '160'; //width
			$ad_format_values['height'] = '600'; //height
			break;
			
			case 5:
			//
			$ad_format_values['width'] = '468'; //width
			$ad_format_values['height'] = '60'; //height
			break;
			
			case 6:
			//
			$ad_format_values['width'] = '234'; //width
			$ad_format_values['height'] = '60'; //height
			break;
			
			case 7:
			//
			$ad_format_values['width'] = '120'; //width
			$ad_format_values['height'] = '600'; //height
			break;
			
			case 8:
			//
			$ad_format_values['width'] = '120'; //width
			$ad_format_values['height'] = '240'; //height
			break;
			
			case 9:
			//
			$ad_format_values['width'] = '250'; //width
			$ad_format_values['height'] = '250'; //height
			break;
			
			case 10:
			//
			$ad_format_values['width'] = '200'; //width
			$ad_format_values['height'] = '200'; //height
			break;
			
			case 11:
			//
			$ad_format_values['width'] = '180'; //width
			$ad_format_values['height'] = '150'; //height
			break;
			
			case 12:
			//
			$ad_format_values['width'] = '125'; //width
			$ad_format_values['height'] = '125'; //height
			break;
			
			case 19:
			//
			$ad_format_values['width'] = '300'; //width
			$ad_format_values['height'] = '600'; //height
			break;
			
			case 20:
			//
			$ad_format_values['width'] = '970'; //width
			$ad_format_values['height'] = '90'; //height
			break;

			case 21:
			//
			$ad_format_values['width'] = '320'; //width
			$ad_format_values['height'] = '50'; //height
			break;
			
			case 22:
			//
			$ad_format_values['width'] = '320'; //width
			$ad_format_values['height'] = '100'; //height
			break;			
			
			//LINK UNIT
			case 13:
			//
			$ad_format_values['width'] = '728'; //width
			$ad_format_values['height'] = '15'; //height
			break;
			
			case 14:
			//
			$ad_format_values['width'] = '468'; //width
			$ad_format_values['height'] = '15'; //height
			break;
			
			case 15:
			//
			$ad_format_values['width'] = '200'; //width
			$ad_format_values['height'] = '90'; //height
			break;
			
			case 16:
			//
			$ad_format_values['width'] = '180'; //width
			$ad_format_values['height'] = '90'; //height
			break;
			
			case 17:
			//
			$ad_format_values['width'] = '160'; //width
			$ad_format_values['height'] = '90'; //height
			break;
			
			case 18:
			//
			$ad_format_values['width'] = '120'; //width
			$ad_format_values['height'] = '90'; //height
			break;
			

			
}



if ($ad_type == 2) {

$ad_format_values['format_text'] = $ad_format_values['width'] . "x" . $ad_format_values['height'] . "_as";

} elseif ($ad_type == 1) {

	if ($ad_links == 4) { 
	
	$ad_format_values['format_text'] = $ad_format_values['width'] . "x" . $ad_format_values['height'] . "_0ads_al";
	
	} elseif ($ad_links == 5) {
	
	$ad_format_values['format_text'] = $ad_format_values['width'] . "x" . $ad_format_values['height'] . "_0ads_al_s";

	}
	
}

return $ad_format_values;
						
}





function kd_admin_googleads_option_size_banner($selected) {

//option banner code

echo '<div style="width:auto;float:left;clear:both;margin: 5px 0 0 20px;border-top: 1px solid #ddd;"><p><b>', _e('Dimensioni dell\'Annuncio:', 'author-advertising-pro'), '</b><br/></p></div>';

		$i = 1;
				while ($i <= 22) : //last= 22
				
					if ($selected == $i) {
					$selected_value[$i] = "selected='selected'";
					}
				$i++;
				endwhile;

echo '
<div id="formatContainer" style="padding-bottom: 1em; visibility: visible; margin: 5px 0 0 20px;float:left;clear:both; ">
	<select name="ad_format" id="text_img_format" onchange=\'submit();\'>
		<optgroup label="', _e('Consigliato', 'author-advertising-pro'), '">
			<option value="1" ', $selected_value[1], ' >', _e('Rettangolo medio', 'author-advertising-pro'), ' 300 x 250</option>
			<option value="2" ', $selected_value[2], ' >', _e('Rettangolo grande', 'author-advertising-pro'), ' 336 x 280</option>
			<option value="3" ', $selected_value[3], ' >', _e('Leaderboard', 'author-advertising-pro'), ' 728 x 90</option>
			<option value="4" ', $selected_value[4], ' >', _e('Skyscraper largo', 'author-advertising-pro'), ' 160 x 600</option>
			<option value="21" ', $selected_value[21], ' >', _e('Banner mobile', 'author-advertising-pro'), ' 320 x 50</option>
		</optgroup>
		
		<optgroup label="', _e('Altro: orizzontale', 'author-advertising-pro'), '">
		<option value="20" ', $selected_value[20], ' >', _e('Leaderboard grande', 'author-advertising-pro'), ' 970 x 90</option>
		<option value="22" ', $selected_value[22], ' >', _e('Banner grande per dispositivi mobili', 'author-advertising-pro'), ' 320 x 100</option>
			<option value="5" ', $selected_value[5], ' >', _e('Banner', 'author-advertising-pro'), ' 468 x 60</option>
			<option value="6" ', $selected_value[6], ' >234 x 60 ', _e('Mezzo banner', 'author-advertising-pro'), '</option>
		</optgroup>
		
		<optgroup label="', _e('Altro: verticale', 'author-advertising-pro'), '">
			<option value="7" ', $selected_value[7], ' >', _e('Skyscraper', 'author-advertising-pro'), ' 120 x 600</option>
			<option value="8" ', $selected_value[8], ' >', _e('Banner verticale', 'author-advertising-pro'), ' 120 x 240</option>
			<option value="19" ', $selected_value[19], ' >', _e('Skyscraper grande', 'author-advertising-pro'), ' 300 x 600</option>
		</optgroup>
		
		<optgroup label="', _e('Altro: quadrato', 'author-advertising-pro'), '">
			<option value="9" ', $selected_value[9], ' >', _e('Quadrato', 'author-advertising-pro'), ' 250 x 250</option>
			<option value="10" ', $selected_value[10], ' >', _e('Quadrato piccolo', 'author-advertising-pro'), ' 200 x 200</option>
			<option value="11" ', $selected_value[11], ' >', _e('Rettangolo piccolo', 'author-advertising-pro'), ' 180 x 150</option>
			<option value="12" ', $selected_value[12], ' >', _e('Button', 'author-advertising-pro'), ' 125 x 125</option>
		</optgroup>
	</select>

</div>';

}




function kd_admin_googleads_option_size_link($selected) {

echo '<div style="width:auto;float:left;clear:both;margin: 5px 0 0 20px;border-top: 1px solid #ddd;"><p><b>', _e('Dimensioni dell\'Annuncio:', 'author-advertising-pro'), '</b><br/></p></div>';

		$i = 13;
				while ($i <= 18) :
				
					if ($selected == $i) {
					$selected_value[$i] = "selected='selected'";
					}
				$i++;
				endwhile;

echo '
<div id="formatContainer" style="padding-bottom: 1em; visibility: visible; margin: 5px 0 0 20px;float:left;clear:both; ">
	<select name="ad_format" id="text_img_format" onchange="">
		<optgroup label="', _e('Unit&agrave; di Link', 'author-advertising-pro'), '">
			<option value="13" ', $selected_value[13], ' >728 x 15</option>
			<option value="14" ', $selected_value[14], ' >468 x 15</option>
			<option value="15" ', $selected_value[15], ' >200 x 90</option>
			<option value="16" ', $selected_value[16], ' >180 x 90</option>
			<option value="17" ', $selected_value[17], ' >160 x 90</option>
			<option value="18" ', $selected_value[18], ' >120 x 90</option>
		</optgroup>
	</select>
</div>';

}


function kd_admin_googleads_color_selector($text, $input_name, $value) {

echo '<script type="text/javascript" src="'. plugin_dir_url( __FILE__ ) . '/jscolor/jscolor.js"></script>';

echo '<div id="selector_color_container" style="float:left;clear:both;margin: 15px 0 0 67px;">';
echo '<div style="height:auto;width:70px;float:left;margin: 2px 0 0 0;">' . $text . '</div>';

echo '<div style="float:left;margin: 0 0 5px 5px;">';
echo "#&nbsp;<input type='text' id='$input_name' value='$value' name='$input_name' />";
echo "</div>";
	  
echo "<input type='text' readonly='readonly' class=\"color {valueElement:'$input_name'}\" style=\"height:23px;width:23px;margin: 1px 0 0 3px;\" />";

echo '</div>';

} //end color_selector



function kd_admin_googleads_prepare_new() {

//prepare a new id into ads table
//and redirect to manage page passing the new id

}



function kd_admin_googleads() {

   //check if Plugin need to install his db table.
   $kd_need_install = kd_check_install();
   
   //check if Plugin need to update db table column for a new version.
   $kd_need_update = kd_check_update();
   
   if ($kd_need_install) {
   
   kd_installer();
   
   } elseif ($kd_need_update) {
   
   kd_updater();
   
   } else {

   echo kd_G_icon();
   echo "<h2>", _e('Author Advertising Pro - Gestione Annunci Google', 'author-advertising-pro'), "</h2><br/>";
   
   echo "<div class='wrap' style=''><br/>";
   
   echo kd_menu_application();
   
   echo "<div>", kd_help_icon(), "<div style='width:70%;text-align:justify;'>";
   
   echo "<p><b>", _e('In questa sezione potrai gestire, creare o eliminare i tuoi annunci Adsense', 'author-advertising-pro'), "</b></p>";
   echo "<p>", _e('Scegli il tipo di annuncio che desideri, poi compila tutti i campi necessari a farlo funzionare correttamente sul sito;<br/>In questa nuova versione di Author Advertising Pro, potrai decidere direttamente dove posizionare gli annunci all\'interno delle pagine senza dover mettere mano al codice del tuo template.', 'author-advertising-pro'), "</p>";
   
   echo "</div></div>";
   
   
   
//GET VALUES
 global $wpdb;

   $base_prefix = $wpdb->base_prefix;
   $table_name_ad_google = $base_prefix . "author_advertising_ad_google";
   
      //DELETE ADS ID FUNCTION
   
   if (isset($_POST['delete_ads'])) {
   
   $delete_id = $_POST['delete_id'];
   
   $sql= "DELETE FROM `$table_name_ad_google` WHERE `id` = $delete_id";
 
   $delete = $wpdb->query($sql);
   
   if ($delete) {
   
   echo "<div style='float:left;clear:both;margin: 20px 0 0 75px;'><p><span style='color:green;'>", _e('Annuncio eliminato correttamente.', 'author-advertising-pro'), "</span></p></div>";
   
   } else {
   
   echo "<div style='float:left;clear:both;margin: 20px 0 0 75px;'><p><span style='color:red;'>", _e('Errore durante l\'eliminazione dell\'annuncio.', 'author-advertising-pro'), "</span></p></div>";
   
   }
   
   //Conto il numero di ads presenti nella tabella
   $ads_count = $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(id) FROM $table_name_ad_google" ) );
   
   if ($ads_count == 0) {
   $wpdb->query("ALTER TABLE $table_name_ad_google AUTO_INCREMENT = 1");
      echo "<div style='float:left;clear:both;margin: 20px 0 0 75px;'><p><span style='color:blue;'>", _e('Tutti gli annunci sono stati eliminati, il contatore degli ID &egrave; stato quindi resettato e ripartir&agrave; da 1', 'author-advertising-pro'), " :)</span></p></div>";
   }
   
   }
   
   //CREATE NEW ADS FUNCTION
   if (isset($_POST['new_ads'])) {
   
		$query = $wpdb->insert( 
	$table_name_ad_google, 
	array( 
		'ad_active' => 'on',
		'ad_type' => 1, 
		'ad_type2' => 2,
		'ad_format' => 2,
	) );
	
	if ($query) {
	echo "<div style='float:left;clear:both;margin: 20px 0 0 75px;'><p><span style='color:green;'>", _e('E\' stato creato un nuovo ID annuncio! Ora puoi modificarlo impostando i campi sottostanti!', 'author-advertising-pro'), "</span></p></div>";
	
	$ads_count = $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(id) FROM $table_name_ad_google" ) );
	$new_ad_created = $ads_count;
	
	} else {
	echo "<div style='float:left;clear:both;margin: 20px 0 0 75px;'><p><span style='color:red;'>", _e('Errore durante la creazione del nuovo ID annuncio', 'author-advertising-pro'), "</span></p></div>";
	
	
	}
	
	}
	
	
   //Conto il numero di ads presenti nella tabella
   $ads_count = $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(id) FROM $table_name_ad_google", null ) );


   echo "<div style='height:auto;width:100%;float:left;clear:both;margin: 15px 0 0 73px;'>";

if ($ads_count == 0) {

	echo "<p>", _e('Non ci sono annunci salvati nel database', 'author-advertising-pro'), "</p>";
	
    echo "<form name='no_ads' method='POST' action='". esc_attr($_SERVER['REQUEST_URI']) ."''>
			<input type='hidden' name='new_ads' value='1' />
			<input type='submit' value='", _e('Crea un nuovo annuncio', 'author-advertising-pro'), "' />";
    echo "</form>";
	
	echo "<form name='no_ads' method='POST' action='". esc_attr($_SERVER['REQUEST_URI']) ."''>
			<input type='hidden' name='show_old_ads' value='1' />
			<input type='submit' value='", _e('Mostrami gli annunci che avevo nella precedente versione.', 'author-advertising-pro'), "' />";
    echo "</form>";
	
    echo "</div>";
	
	} else {
   
   echo "<form name='no_ads' method='POST' action='". esc_attr($_SERVER['REQUEST_URI']) ."''>
			<input type='hidden' name='new_ads' value='1' />
			<input type='submit' value='", _e('Crea un nuovo annuncio', 'author-advertising-pro'), "' />";
   echo "</form>";
   echo "</div>";
   

   
//Conto il numero di ads presenti nella tabella
$ads_count = $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(id) FROM $table_name_ad_google", null ) );
//Seleziona il primo id salvato nella tabella
$first_id = $wpdb->get_var( $wpdb->prepare( "SELECT id FROM $table_name_ad_google LIMIT 1", null ) );
   
   //GET VALUES FROM ID
if (isset($new_ad_created)) { 

$selected_id = $new_ad_created;

} elseif (isset($_POST['ads_id'])) {

$selected_id = $_POST['ads_id'];

} else { 

$selected_id = $first_id;

}

if (isset($_POST['save_ad_values'])) {
//GET POST VALUES
$ad_type = $_POST['ad_type']; 
$ad_type2 = $_POST['ad_type2']; 
$ad_format = $_POST['ad_format']; 

//FIX AD_CLIENT

$ad_client = ltrim($_POST['ad_client']);
$ad_client = rtrim($ad_client);
if (!empty($ad_client)) { 

	$ad_pub_id = $ad_client; 
	$ad_client = $ad_client;
	} else { 
	$ad_pub_id = "%pubid%";
	$ad_client = "";
	
}
//END FIX

$personal_ad_slot = $_POST['personal_ad_slot'];
$ad_slot = $_POST['ad_slot'];
$ad_channel = $_POST['ad_channel']; 

//FIX COLOR VALUES
if (isset($_POST['ad_color_border'])) { $ad_color_border = $_POST['ad_color_border']; } else { $ad_color_border = "FFFFFF"; }
if (isset($_POST['ad_color_bg'])) { $ad_color_bg = $_POST['ad_color_bg']; } else { $ad_color_bg = "FFFFFF"; }
if (isset($_POST['ad_color_link'])) { $ad_color_link = $_POST['ad_color_link']; } else { $ad_color_link = "FFFFFF"; }
if (isset($_POST['ad_color_text'])) { $ad_color_text = $_POST['ad_color_text']; } else { $ad_color_text = "FFFFFF"; }
if (isset($_POST['ad_color_url'])) { $ad_color_url = $_POST['ad_color_url']; } else { $ad_color_url = "FFFFFF"; }
//END FIX

$ad_ui_features = $_POST['ad_ui_features']; 
//$ad_generated_code = $_POST['']; 
$ad_excluded_categories = $_POST['ad_excluded_categories']; 
$ad_included_categories = $_POST['ad_included_categories']; 
$ad_css = $_POST['ad_css']; 
$ad_active = $_POST['ad_active']; 
$ad_links = $_POST['ad_links']; 
$ad_style = $_POST['ad_style']; 
$ad_position_begin = $_POST['ad_position_begin']; 
$ad_position_end = $_POST['ad_position_end']; 
$ad_tag = $_POST['ad_tag'];
$ad_comment = $_POST['ad_comment'];
$ad_position_center = $_POST['ad_position_center']; 

///PREPARE THE FORMAT VALUES TO GENERATE THE FINAL CODE
$ad_format_values = kd_admin_google_ads_size_values($ad_format, $ad_type, $ad_links);
$ad_width = $ad_format_values['width']; 
$ad_height = $ad_format_values['height']; 
$ad_format_text = $ad_format_values['format_text'];

if (strlen($ad_slot) <= 9 && $personal_ad_slot == 1) { $ad_slot = ""; } //fix 5.1.0

//CHECK AD STYLE (TEXT/IMAGE)
Switch ($ad_style) {

	case 0:
	//image
	$ad_style_format = "image";
	break;
	
	case 1:
	//text
	$ad_style_format = "text";
	break;
	
	case 2:
	//text_image
	$ad_style_format = "text_image";
	break;
	
}

//CHECK UI FEATURES
Switch ($ad_ui_features) {

	case 0:
	$ad_ui_value = "rc:0";
	break;
	
	case 6:
	$ad_ui_value = "rc:6";
	break;
	
	case 10:
	$ad_ui_value = "rc:10";
	break;
}



//NOW GENERATE THE FINAL CODE


if ($ad_type2 == 2) { //IF OLD CODE

	
	if ($ad_type == 2) { //IF ADS 

$code = "<script type=\"text/javascript\"><!--
google_ad_client = \"ca-$ad_pub_id\";
/** $ad_comment **/
google_ad_width = \"$ad_width\";
google_ad_height = \"$ad_height\";
google_ad_format = \"$ad_format_text\";
google_ad_type = \"$ad_style_format\";
google_ad_channel = \"$ad_channel\";
google_color_border = \"$ad_color_border\";
google_color_bg = \"$ad_color_bg\";
google_color_link = \"$ad_color_link\";
google_color_text = \"$ad_color_text\";
google_color_url = \"$ad_color_url\";
google_ui_features = \"$ad_ui_value\";
//-->
</script>
<script type=\"text/javascript\" src=\"http://pagead2.googlesyndication.com/pagead/show_ads.js\">
</script>";

	} elseif ($ad_type == 1) { //IF LINK_UNIT
	
$code = "<script type=\"text/javascript\"><!--
google_ad_client = \"ca-$ad_pub_id\";
/** $ad_comment **/
google_ad_width = $ad_width;
google_ad_height = $ad_height;
google_ad_format = \"$ad_format_text\";
google_ad_channel =\"$ad_channel\";
google_color_border = \"$ad_color_border\";
google_color_bg = \"$ad_color_bg\";
google_color_link = \"$ad_color_link\";
//--></script>
<script type=\"text/javascript\" src=\"http://pagead2.googlesyndication.com/pagead/show_ads.js\">
</script>";

	}
	

} elseif ($ad_type2 == 1 && $personal_ad_slot == 0) {


	$ad_slot_id = $ad_slot;
	
	switch ($ad_slot) {
	
		case 1:
		$ad_slot = "%ad_slot1%";
		break;
		
		case 2:
		$ad_slot = "%ad_slot2%";
		break;

		case 3:
		$ad_slot = "%ad_slot3%";
		break;

		case 4:
		$ad_slot = "%ad_slot4%";
		break;

		case 5:
		$ad_slot = "%ad_slot5%";
		break;

		case 6:
		$ad_slot = "%ad_slot6%";
		break;

		//fix 5.1.0
		case "":
		$ad_slot = "%ad_slot1%";
		break;
		
	}

$code = "<script type=\"text/javascript\"><!--
google_ad_client = \"ca-$ad_pub_id\";
/** $ad_comment *$ad_slot_id*/
google_ad_slot = \"$ad_slot\";
google_ad_width = $ad_width;
google_ad_height = $ad_height;
//-->
</script>
<script type=\"text/javascript\" src=\"http://pagead2.googlesyndication.com/pagead/show_ads.js\">
</script>";

} elseif ($ad_type2 == 1 && $personal_ad_slot == 1) {

$code = "<script type=\"text/javascript\"><!--
google_ad_client = \"ca-$ad_pub_id\";
/** $ad_comment *$ad_slot_id*/
google_ad_slot = \"$ad_slot\";
google_ad_width = $ad_width;
google_ad_height = $ad_height;
//-->
</script>
<script type=\"text/javascript\" src=\"http://pagead2.googlesyndication.com/pagead/show_ads.js\">
</script>";

}

//NOW SAVE THE VALUES

$data = array('ad_type' => $ad_type,
'ad_type2' => $ad_type2,
'ad_format' => $ad_format,
'ad_client' => $ad_client,
'personal_ad_slot' => $personal_ad_slot,
'ad_slot' => $ad_slot,
'ad_channel' => $ad_channel,
'ad_width' => $ad_width,
'ad_height' => $ad_height,
'ad_color_border' => $ad_color_border,
'ad_color_bg' => $ad_color_bg,
'ad_color_link' => $ad_color_link,
'ad_color_text' => $ad_color_text,
'ad_color_url' => $ad_color_url,
'ad_ui_features' => $ad_ui_features,
'ad_excluded_categories' => $ad_excluded_categories,
'ad_included_categories' => $ad_included_categories,
'ad_css' => $ad_css,
'ad_active' => $ad_active,
'ad_links' => $ad_links,
'ad_style' => $ad_style,
'ad_generated_code' => $code,
'ad_position_begin' => $ad_position_begin,
'ad_position_end' => $ad_position_end,
'ad_tag' => $ad_tag,
'ad_comment' => $ad_comment,
'ad_position_center' => $ad_position_center);

$where = array('id' => $selected_id);

$wpdb->update( $table_name_ad_google, $data, $where );

}

//////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////

//GETTING SETTING VALUES BY SAVED ADS
$ad_active = $wpdb->get_var( $wpdb->prepare( "SELECT ad_active FROM $table_name_ad_google WHERE id='$selected_id'", null ) );
$ad_type = $wpdb->get_var( $wpdb->prepare( "SELECT ad_type FROM $table_name_ad_google WHERE id='$selected_id'", null ) );
$ad_type2 = $wpdb->get_var( $wpdb->prepare( "SELECT ad_type2 FROM $table_name_ad_google WHERE id='$selected_id'", null ) );
$ad_format = $wpdb->get_var( $wpdb->prepare( "SELECT ad_format FROM $table_name_ad_google WHERE id='$selected_id'", null ) );
$ad_client = $wpdb->get_var( $wpdb->prepare( "SELECT ad_client FROM $table_name_ad_google WHERE id='$selected_id'", null ) );
$personal_ad_slot = $wpdb->get_var( $wpdb->prepare( "SELECT personal_ad_slot FROM $table_name_ad_google WHERE id='$selected_id'", null) );
$ad_slot = $wpdb->get_var( $wpdb->prepare( "SELECT ad_slot FROM $table_name_ad_google WHERE id='$selected_id'", null ) );
$ad_width = $wpdb->get_var( $wpdb->prepare( "SELECT ad_width FROM $table_name_ad_google WHERE id='$selected_id'", null ) );
$ad_height = $wpdb->get_var( $wpdb->prepare( "SELECT ad_height FROM $table_name_ad_google WHERE id='$selected_id'", null ) );
$ad_channel = $wpdb->get_var( $wpdb->prepare( "SELECT ad_channel FROM $table_name_ad_google WHERE id='$selected_id'", null ) );
$ad_color_border = $wpdb->get_var( $wpdb->prepare( "SELECT ad_color_border FROM $table_name_ad_google WHERE id='$selected_id'", null ) );
$ad_color_bg = $wpdb->get_var( $wpdb->prepare( "SELECT ad_color_bg FROM $table_name_ad_google WHERE id='$selected_id'", null ) );
$ad_color_link = $wpdb->get_var( $wpdb->prepare( "SELECT ad_color_link FROM $table_name_ad_google WHERE id='$selected_id'", null ) );
$ad_color_text = $wpdb->get_var( $wpdb->prepare( "SELECT ad_color_text FROM $table_name_ad_google WHERE id='$selected_id'", null ) );
$ad_color_url = $wpdb->get_var( $wpdb->prepare( "SELECT ad_color_url FROM $table_name_ad_google WHERE id='$selected_id'", null ) );
$ad_ui_features = $wpdb->get_var( $wpdb->prepare( "SELECT ad_ui_features FROM $table_name_ad_google WHERE id='$selected_id'", null ) );
$ad_generated_code = $wpdb->get_var( $wpdb->prepare( "SELECT ad_generated_code FROM $table_name_ad_google WHERE id='$selected_id'", null ) );
$ad_excluded_categories = $wpdb->get_var( $wpdb->prepare( "SELECT ad_excluded_categories FROM $table_name_ad_google WHERE id='$selected_id'", null ) );
$ad_included_categories = $wpdb->get_var( $wpdb->prepare( "SELECT ad_included_categories FROM $table_name_ad_google WHERE id='$selected_id'", null ) );
$ad_css = $wpdb->get_var( $wpdb->prepare( "SELECT ad_css FROM $table_name_ad_google WHERE id='$selected_id'", null ) );
$ad_links = $wpdb->get_var( $wpdb->prepare( "SELECT ad_links FROM $table_name_ad_google WHERE id='$selected_id'", null ) );
$ad_style = $wpdb->get_var( $wpdb->prepare( "SELECT ad_style FROM $table_name_ad_google WHERE id='$selected_id'", null ) );
$ad_position_begin = $wpdb->get_var( $wpdb->prepare( "SELECT ad_position_begin FROM $table_name_ad_google WHERE id='$selected_id'", null ) );
$ad_position_end = $wpdb->get_var( $wpdb->prepare( "SELECT ad_position_end FROM $table_name_ad_google WHERE id='$selected_id'", null ) );
$ad_tag = $wpdb->get_var( $wpdb->prepare( "SELECT ad_tag FROM $table_name_ad_google WHERE id='$selected_id'", null ) );
$ad_comment = $wpdb->get_var( $wpdb->prepare( "SELECT ad_comment FROM $table_name_ad_google WHERE id='$selected_id'", null ) );
$ad_position_center = $wpdb->get_var( $wpdb->prepare( "SELECT ad_position_center FROM $table_name_ad_google WHERE id='$selected_id'", null ) );
//END GETTING VALUES

      
   echo "<div style='height:auto;width:100%;float:left;clear:both;margin: 15px 0 0 20px;'>";
   echo "<div style='height:auto;width:100%;float:left;clear:both;margin: 10px 0 10px 20px;'>";
   echo "<form name='id_select_form' action='". esc_attr($_SERVER['REQUEST_URI']) ."' method='POST'>";
   echo "<p><b>", _e('Scegli l\'ID dell\'annuncio che vuoi modificare:', 'author-advertising-pro'), "</b></p>";
   echo _e('ID Annuncio:', 'author-advertising-pro'), "  ";

   echo "<select name=\"ads_id\" onchange=\"submit();\" >";
   
   echo '<optgroup label="', _e('Seleziona ID Annuncio', 'author-advertising-pro'), '">';
  
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
	echo "</form>";
   
   echo "</div>";
   
   //FINE BLOCCO SELECT ID
   
   
   //INIZIO BLOCCO FORM VALUES ADS
   echo "<form name='ads_values' action='". esc_attr($_SERVER['REQUEST_URI']) ."' method='POST'>";
   echo "<input type='hidden' name='ads_id' value='$selected_id' />";
   echo "<input type='hidden' name='save_ad_values' value='save_this' />";
   
   echo "<div style='height:auto;width:auto;float:left;margin: 10px 0 0 20px;'>";
   echo "<p><b>", _e('Spunta la casella se desideri che l\'annuncio sia attivo:', 'author-advertising-pro'), "</b></p>";
   
		//CHECK AD_ACTIVE_VALUE
   		if (isset($ad_active)) { $chk_active = "checked=\"yes\""; } else { $checked_active = ""; }

   echo "<label>", _e('Annuncio Attivo:', 'author-advertising-pro'), "&nbsp;<input type='checkbox' name='ad_active' $chk_active /></label>";
   echo "</div>";
   
   echo "<div style='height:auto;width:auto;float:left;clear:both;margin: 10px 0 30px 20px;'>";
   
   //AD TYPE 1 ADS/UNIT_LINK
   echo "<p><b>", _e('Scegli il tipo di annuncio Adsense (Link/Banner):', 'author-advertising-pro'), "</b></p>";

   echo "<select name='ad_type' onchange='submit();'>";
   
		Switch ($ad_type) {
		
				case 0:
				$selected1 = "";
				$selected2 = "";
				break;
				
				case 1:
				$selected1 = "selected='selected'";
				$selected2 = "";
				break;

				case 2:
				$selected1 = "";
				$selected2 = "selected='selected'";
				break;
		}
		
   echo "<option name='ad_type' value='1' $selected1 />", _e('Unit&agrave; di Link', 'author-advertising-pro'), "</option>";
   echo "<option name='ad_type' value='2' $selected2 />", _e('Annuncio Illustrato/Testo', 'author-advertising-pro'), "</option>";
   echo '</select>';
   
   echo "</div>";
   
   echo "<div style='height:auto;width:auto;float:left;margin: 10px 0 30px 20px;border-left: 1px solid #ddd;padding-left: 20px;'>";
	
   //AD TYPE 2  NEW/OLD ADSENSE CODE
   echo "<p><b>", _e('Scegli il tipo di annuncio che desideri creare:', 'author-advertising-pro'), "</b></p>";

   echo "<select name='ad_type2' onchange='submit();'>";
   
		Switch ($ad_type2) {
		
				case 0:
				$selected1 = "";
				$selected2 = "";
				break;
				
				case 1:
				$selected1 = "selected='selected'";
				$selected2 = "";
				break;

				case 2:
				$selected1 = "";
				$selected2 = "selected='selected'";
				break;
		}
		
   echo "<option name='ad_type2' value='1' $selected1 />", _e('Nuovo codice Adsense (ad_slot)', 'author-advertising-pro'), "</option>";
   echo "<option name='ad_type2' value='2' $selected2 />", _e('Vecchio codice Adsense', 'author-advertising-pro'), "</option>";
   echo "</select>";
   echo "</div>";
   

   //ADS SIZE //DIMENSIONI DELL'ANNUNCIO
   if ($ad_type == 2) {
echo kd_admin_googleads_option_size_banner($ad_format);

} elseif ($ad_type == 1) {
echo kd_admin_googleads_option_size_link($ad_format);

}

if ($ad_type2 == 1) {
//AD_SLOT
echo "<div style='height:auto;width:auto;clear:both;float:left;margin: 15px 0 0 20px;border-top: 1px solid #ddd;'>";
echo "<p><b>", _e('Codice AD_SLOT (formato annuncio):', 'author-advertising-pro'), "</b></p><br/>";

   echo "<select name='personal_ad_slot' onchange='submit();'>";
   
		Switch ($personal_ad_slot) {
		
				case 0:
				$selected0 = "selected='selected'";
				$selected1 = "";
				break;
				
				case 1:
				$selected0 = "";
				$selected1 = "selected='selected'";
				break;

		}
		
   echo "<option name='personal_ad_slot' value='0' $selected0 />", _e('Usa il codice AD_SLOT relativo al pub-id dell\'Autore', 'author-advertising-pro'), "</option>";
   echo "<option name='personal_ad_slot' value='1' $selected1 />", _e('Usa un codice AD_SLOT personale', 'author-advertising-pro'), "</option>";
   echo "</select>";


if ($personal_ad_slot == 1) {
echo "<br/><br/>", _e('Ad_slot:', 'author-advertising-pro'), "<input type='text' name='ad_slot' value='$ad_slot' />";

if (strlen($ad_slot) <= 9 || strlen($ad_slot) > 10) { echo "<span style='color:red;'>  ", _e('Inserisci il codice Ad_Slot di 10 cifre, altrimenti l\'annuncio non funzioner&agrave;!','author-advertising-pro'), "</span>"; } elseif (strlen($ad_slot) == 10 && is_numeric($ad_slot)) { echo "<span style='color:green;'>  ", _e('Codice Ad_Slot formalmente valido!','author-advertising-pro'), "</span>"; }
elseif (!is_numeric($ad_slot)) { echo "<span style='color:red;'>  ", _e('Codice Ad_Slot non valido: Deve contenere solo numeri!','author-advertising-pro'), "</span>"; }

} else {
	
   echo "<br/><br/>";
   echo "<select name='ad_slot' onchange='submit();'>";
   
		Switch ($ad_slot) {
		
				case "%ad_slot1%":
				$selected1 = "selected='selected'";
				$selected2 = "";
				$selected3 = "";
				$selected4 = "";
				$selected5 = "";
				$selected6 = "";
				break;
				
				case "%ad_slot2%":
				$selected1 = "";
				$selected2 = "selected='selected'";
				$selected3 = "";
				$selected4 = "";
				$selected5 = "";
				$selected6 = "";
				break;
		
				case "%ad_slot3%":
				$selected1 = "";
				$selected2 = "";
				$selected3 = "selected='selected'";
				$selected4 = "";
				$selected5 = "";
				$selected6 = "";
				break;
				
				case "%ad_slot4%":
				$selected1 = "";
				$selected2 = "";
				$selected3 = "";
				$selected4 = "selected='selected'";
				$selected5 = "";
				$selected6 = "";
				break;
		
				case "%ad_slot5%":
				$selected1 = "";
				$selected2 = "";
				$selected3 = "";
				$selected4 = "";
				$selected5 = "selected='selected'";
				$selected6 = "";
				break;
				
				case "%ad_slot6%":
				$selected1 = "";
				$selected2 = "";
				$selected3 = "";
				$selected4 = "";
				$selected5 = "";
				$selected6 = "selected='selected'";
				break;


		}
		
   $google_values = get_option('kd_author_advertising');
   
   if($google_values['adslot1']=="1") echo "<option name='ad_slot' value='1' $selected1 />AD_SLOT 1 (".$google_values['adslot1_title'].") </option>";
   if($google_values['adslot2']=="1") echo "<option name='ad_slot' value='2' $selected2 />AD_SLOT 2 (".$google_values['adslot2_title'].") </option>";
   if($google_values['adslot3']=="1") echo "<option name='ad_slot' value='3' $selected3 />AD_SLOT 3 (".$google_values['adslot3_title'].") </option>";
   if($google_values['adslot4']=="1") echo "<option name='ad_slot' value='4' $selected4 />AD_SLOT 4 (".$google_values['adslot4_title'].") </option>";
   if($google_values['adslot5']=="1") echo "<option name='ad_slot' value='5' $selected5 />AD_SLOT 5 (".$google_values['adslot5_title'].") </option>";
   if($google_values['adslot6']=="1") echo "<option name='ad_slot' value='6' $selected6 />AD_SLOT 6 (".$google_values['adslot6_title'].") </option>";
   echo "</select>";
   echo "<br/>
		<p><small>", _e('Puoi selezionare solo gli Ad_Slot che hai attivato nelle Impostazioni generali del plugin.', 'author-advertising-pro'), "</small></p>";
   
}

echo "</div>";

}


if (($ad_type == 0 || $ad_type == 2) && ($ad_type2 == 0 || $ad_type2 == 2)) {
   echo "<div style='height:auto;width:auto;clear:both;float:left;margin: 15px 0 40px 20px;border-top: 1px solid #ddd;padding-left: 0px;'>";
   echo "<p><b>", _e('Scegli lo stile dell\'annuncio Adsense:', 'author-advertising-pro'), "</b></p>";
   
      		Switch ($ad_style) {
		
				case 0:
				$checked_0 = "checked='checked'";
				break;
				
				case 1:
				$checked_1 = "checked='checked'";
				break;

				case 2:
				$checked_2 = "checked='checked'";
				break;
		}

		$ad_width = (int)$ad_width;
		if ($ad_width == 234 || $ad_width == 120 || $ad_width == 180 || $ad_width == 125) { //fix 5.1.0
		$only_image = true;
		}
		
		if (!$only_image) {
   echo "<label><input type='radio' name='ad_style' value='0' $checked_0 />    ", _e('Solo Immagini', 'author-advertising-pro'), "</label><br/>";
   }
   
   if ($only_image) { $checked_1 = "checked='checked'";}
   echo "<label><input type='radio' name='ad_style' value='1' $checked_1 />    ", _e('Solo Testo', 'author-advertising-pro'), "</label><br/>";
   
   if (!$only_image) {
   echo "<label><input type='radio' name='ad_style' value='2' $checked_2 />    ", _e('Immagini e Testo', 'author-advertising-pro'), "</label><br/>";
   }
   
   echo "</div>";
}
   
if (($ad_type == 0 || $ad_type == 1) && ($ad_type2 == 0 || $ad_type2 == 2)) {
   echo "<div style='clear:both;height:auto;width:auto;float:left;margin: 15px 0 40px 20px;border-top: 1px solid #ddd;padding-left: 20px;'>";
   echo "<p><b>", _e('Numero di links:', 'author-advertising-pro'), "</b></p>";
   
      		Switch ($ad_links) {
		
				case 4:
				$checked_4 = "checked='checked'";
				break;
				
				case 5:
				$checked_5 = "checked='checked'";
				break;
		}
   

   echo "<label><input type='radio' name='ad_links' value='4' $checked_4 /> 4</label><br/>";
   echo "<label><input type='radio' name='ad_links' value='5' $checked_5 /> 5</label><br/>";
   echo "</div>";
}
   
if (($ad_type == 0 || $ad_type == 2) && ($ad_type2 == 0 || $ad_type2 == 2)) {
   echo "<div style='height:auto;width:auto;float:left;margin: 15px 0 40px 20px;border-top: 1px solid #ddd;padding-left: 20px;'>";
   echo "<p><b>", _e('Seleziona lo stile degli angoli dell\'annuncio:', 'author-advertising-pro'), "</b></p>";
   
   		Switch ($ad_ui_features) {
		
				case 0:
				$checked_0 = "checked='checked'";
				break;
				
				case 6:
				$checked_6 = "checked='checked'";
				break;

				case 10:
				$checked_10 = "checked='checked'";
				break;
		}

   echo "<label><input type='radio' name='ad_ui_features' value='0' $checked_0 /> ", __('Nessuno (squadrato)', 'author-advertising-pro'), "</label><br/>";
   echo "<label><input type='radio' name='ad_ui_features' value='6' $checked_6 /> ", __('Leggermente arrotondati', 'author-advertising-pro'), "</label><br/>";
   echo "<label><input type='radio' name='ad_ui_features' value='10' $checked_10 /> ", __('Molto arrotondati', 'author-advertising-pro'), "</label><br/>";
   echo "</div>";
   
}

if ($ad_type2 == 2) {
echo '<div style="width:auto;float:left;clear:both;margin: 15px 0 0 0;"><p><b>', _e('Seleziona i colori dell\'annuncio:', 'author-advertising-pro'), '</b><br/>', kd_info_icon(), '<small>', _e('Inserisci un valore esadecimale o clicca sulla casella del colore per selezionarne uno.', 'author-advertising-pro'), '</small></p></div>';

//default value
$value = $ad_color_border;
$text= __('Bordo', 'author-advertising-pro');
$input_name ='ad_color_border';
echo kd_admin_googleads_color_selector($text,$input_name,$value);

if ($ad_type == 2) {
//default value
$value = $ad_color_link;
$text= __('Titolo', 'author-advertising-pro');
$input_name ='ad_color_link';
echo kd_admin_googleads_color_selector($text,$input_name,$value);
}

//default value
$value = $ad_color_bg;
$text= __('Sfondo', 'author-advertising-pro');
$input_name ='ad_color_bg';
echo kd_admin_googleads_color_selector($text,$input_name,$value);

if ($ad_type == 2) {
//default value
$value = $ad_color_text;
$text= __('Testo', 'author-advertising-pro');
$input_name ='ad_color_text';
echo kd_admin_googleads_color_selector($text,$input_name,$value);
}

//default value

if ($ad_type == 2) {
$value = $ad_color_url;
$text= __('URL', 'author-advertising-pro');
$input_name ='ad_color_url';
echo kd_admin_googleads_color_selector($text,$input_name,$value);
 
} elseif ($ad_type == 1) {

//default value
$value = $ad_color_link;
$text= __('Link', 'author-advertising-pro');
$input_name ='ad_color_link';
echo kd_admin_googleads_color_selector($text,$input_name,$value);

}

}


//AD_CLIENT
if ($personal_ad_slot == 1 || $ad_type2 == 2) {

echo "<div style='height:auto;width:auto;clear:both;float:left;margin: 15px 0 0 20px;'>";
echo "<p><b>", _e('Personalizza Pub-id:', 'author-advertising-pro'), "</b></p>";

$pub_id_len = strlen($ad_client);
$pub_check = substr($ad_client, 0, 4);
if ($pub_check === 'pub-') { $pub_check = 1; } else { $pub_check = 0; }


echo "<div style='height:100px;float:left;'><p>", kd_info_icon(), "</p></div><div style='float:left;'><p><small>", _e('Inserisci un codice pub-id personale se desideri fare in modo che questo annuncio visualizzi sempre uno specifico pub-id.<br/><b>Lascialo vuoto se desideri che appaia il codice pub-id dell\'autore!</b>', 'author-advertising-pro'), "</small></p></div>";

echo "<div style='height:auto;width:auto;clear:both;float:left;margin: -25px 0 20px 20px;'>";
echo _e('Codice Pub-id:', 'author-advertising-pro'), "<input type='text' name='ad_client' value='$ad_client' />";

   // setting images path
   $path = WP_PLUGIN_URL.'/'.str_replace(basename( __FILE__),"",plugin_basename(__FILE__));
   $ok_path = $path . "images/ok.png";
   $cancel_path = $path . "images/cancel.png";

   if (isset($ad_client)) {
   if($pub_id_len <> 20 && $pub_check == 0) {
   
         echo '<img src="' . $cancel_path .'" alt="Il codice inserito non sembra corretto" style="vertical-align:middle;margin-left:10px;" />&nbsp;&nbsp;&nbsp;', _e('Manca il suffisso "pub-" all\'inizio del codice numerico','author-advertising-pro'), '</td></tr>';
   

   
	} elseif($pub_id_len == 20 && $pub_check == 0) {
   
         echo '<img src="' . $cancel_path .'" alt="Il codice inserito non sembra corretto" style="vertical-align:middle;margin-left:10px;" />&nbsp;&nbsp;&nbsp;', _e('Manca il suffisso "pub-" all\'inizio del codice numerico','author-advertising-pro'), '</td></tr>';
	
	
	} elseif ($pub_check == 1 && $pub_id_len == 20) {
   
   echo '<img src="' . $ok_path .'" alt="Il codice inserito sembra corretto" style="vertical-align:middle;margin-left:10px;" />&nbsp;&nbsp;&nbsp;', _e('Il codice pub-id inserito sembra corretto','author-advertising-pro'), '</td></tr>';
   
   } elseif($pub_id_len <> 20 && $pub_check == 1) {
   
   echo '<img src="' . $cancel_path .'" alt="Il codice inserito non sembra corretto" style="vertical-align:middle;margin-left:10px;" />&nbsp;&nbsp;&nbsp;', _e('Il codice pub-id dovrebbe essere di 4 caratteri + 16 cifre (es.: pub-0123456789012345)!','author-advertising-pro'), '</td></tr>'; 
	  	 
		  
	  }
	  
	  } elseif ($personal_ad_slot == 1 && $pub_id_len == 0) { 
	echo '<img src="' . $cancel_path .'" alt="Il codice inserito non sembra corretto" style="vertical-align:middle;margin-left:10px;" />&nbsp;&nbsp;&nbsp;', _e('Inserire il codice pub-id utente!','author-advertising-pro'), '</td></tr>'; 
	}


echo "</div>";

}
//END AD_CLIENT

if ($ad_type2 == 2) {
//AD_CHANNEL
echo "<div style='height:auto;width:auto;clear:both;float:left;margin: 15px 0 0 20px;'>";
echo "<p><b>", _e('Canale di monitoraggio per questo annuncio:', 'author-advertising-pro'), "</b></p>";
echo _e('Nome canale:', 'author-advertising-pro'), "<input type='text' name='ad_channel' value='$ad_channel' />";
echo "</div>";

}

//AD_COMMENT
echo "<div style='height:auto;width:auto;clear:both;float:left;margin: 15px 0 0 20px;'>";
echo "<p><b>", _e('Commento per questo annuncio:', 'author-advertising-pro'), "</b></p>";
echo _e('Testo del commento:', 'author-advertising-pro'), "<span style='color:green;'>&nbsp;/*&nbsp;<input type='text' name='ad_comment' value='$ad_comment' />&nbsp;*/&nbsp;</span>";
echo "</div>";

//POSIZIONAMENTO AUTOMATICO
echo "<div style='height:auto;width:40%;clear:both;float:left;margin: 15px 0 0 20px;'>";

echo "<p><b>", _e('Posizionamento Automatico:', 'author-advertising-pro'), "</b></p>";
echo "<div style='height:80px;float:left;'>", kd_info_icon(), "</div><div style='float:left;'><p><small>", _e('Ti permette di scegliere dove inserire questo annuncio, automaticamente, senza dover mettere mano al codice del template.', 'author-advertising-pro'), "</small></p></div><div style='float:left;'>";
		
		//CHECK AD_POSITION_VALUE
   		if ($ad_position_begin == 'on') { $checked_position_begin = "checked='checked'"; } else { $checked_position_begin = ""; }
		if ($ad_position_end == 'on') { $checked_position_end = "checked='checked'"; } else { $checked_position_end = ""; }
		if ($ad_position_center == 'on') { $checked_position_center = "checked='checked'"; $advice_color = "red;"; } else { $checked_position_center = ""; $advice_color = "black;"; }

echo "<label>", _e('All\'inizio dell\'articolo:', 'author-advertising-pro'), " <input type='checkbox' name='ad_position_begin' $checked_position_begin /></label></br>";
echo "<label>", _e('Alla fine dell\'articolo:', 'author-advertising-pro'), " <input type='checkbox' name='ad_position_end' $checked_position_end /></label><br/>";
echo "<label>", _e('Al centro dell\'articolo:', 'author-advertising-pro'), " <input type='checkbox' name='ad_position_center' $checked_position_center /></label><br/>";
echo "<small><span style='color:$advice_color'>", _e('Attenzione: Il posizionamento automatico al centro viene ignorato se nell\'articolo inserisci anche il TAG manuale o se non sono presenti ALMENO 2 paragrafi (Es.: &lsaquo;p&rsaquo;Primo paragrafo&lsaquo;/p&rsaquo;&lsaquo;p&rsaquo;Secondo paragrafo&lsaquo;/p&rsaquo;)','author-advertising-pro'), " ", $ad_tag, "</span></small>";

echo "</div>";
echo "</div>";

//POSIZIONAMENTO MANUALE
echo "<div style='height:auto;width:auto;float:left;margin: 15px 0 0 20px;border-left: 1px solid #ddd;padding-left:20px;'>";

echo "<p><b>", _e('Posizionamento Manuale:', 'author-advertising-pro'), "</b></p>";
echo "<div style='height:100px;float:left;'><p>", kd_info_icon(), "</p></div><div style='float:left;'><p><small>", _e('Scegli il TAG, ovvero la parola che inserita nell\'articolo, verr&agrave sostituita con questo annuncio.<br/>Oppure copia il codice template ed inseriscilo dove preferisci nel codice php del tuo tema.', 'author-advertising-pro'), "</small></p></div>";
echo "<label>", _e('TAG:', 'author-advertising-pro');

if (empty($ad_tag)) {

	if ($selected_id == 1) {
	
	$sel_id = "";
	
	} else {
	
	$sel_id = $selected_id;
	}

echo " <input type='text' name='ad_tag' value='[ADSENSE$sel_id]'/></label></br>";
} else {
echo " <input type='text' name='ad_tag' value='$ad_tag'/></label></br>";
}

//provvisiorio
$ad_template_tag = "<?php if (function_exists('kd_template_ad')) { kd_template_ad($selected_id); } ?>";

echo "<p><b><small>", _e('PHP Template tag:', 'author-advertising-pro'), "</small></b></p><textarea style='margin: 0 0 0 23px;resize:none;' rows='2' cols='70' name='ad_template_tag' readonly='readonly' >$ad_template_tag</textarea>";
echo "</div>";


//CATEGORIE ABILITATE
echo "<div style='height:auto;width:auto;clear:both;float:left;margin: 15px 0 0 20px;'>";
//echo wp_category_checklist($selected_cats = false);
//wp_dropdown_categories('show_count=0&hide_empty=0&hierarchical=1 Archives');

echo "<p><b>", _e('Seleziona le categorie abilitate alla visualizzazione di questo annuncio:', 'author-advertising-pro'), "</b></p>";

//blocco info
echo "<div style='height:auto;width:100%;float:left;margin: -10px 0 20px 2px;'>";
echo "<div style='height:50px;float:left;'><p>", kd_info_icon(), "</p></div><div style='float:left;'><p><small>", _e('Inserisci l\' ID della categoria degli articoli che vuoi includere/escludere - se pi&ugrave; di una separati da virgola (es.: 1,34,23,28). - <br/>Se escludi l\'id di una categoria, e lo stesso id lo inserisci tra le categorie abilitate, l\'annuncio non verr&agrave; visualizzato!', 'author-advertising-pro'), "<br/>", _e('Lascia vuota la casella se non desideri utilizzare la relativa funzione. Per le modalit&agrave; di funzionamento di questa opzione consulta la <a href="http://author-adv-pro.org/guides?lang=it" target="_blank">guida</a>.', 'author-advertising-pro'), "</small></p></div>";
echo "</div>";


echo "<p>", _e('Disabilita per queste categorie:', 'author-advertising-pro'), " <input type='text' name='ad_excluded_categories' value='$ad_excluded_categories' /></p>";
echo "<p>", _e('Abilita per queste categorie:', 'author-advertising-pro'), " <input type='text' name='ad_included_categories' value='$ad_included_categories' /></p>";
echo "</div>";

//Customize
echo "<div style='height:auto;width:auto;clear:both;float:left;margin: 15px 0 0 20px;'>";
echo "<p><b>", _e('Personalizza Annuncio:', 'author-advertising-pro'), "</b></p>";

//blocco info
echo "<div style='height:50px;float:left;clear:both;'><p>", kd_info_icon(), "</p></div><div style='float:left;'><p><small>", _e('Gli annunci vengono visualizzati sul sito all\'interno di un DIV, utilizza questo campo per scrivere delle regole CSS personalizzate.', 'author-advertising-pro'), "<br/>", _e('(es.: "float:left;height:280px;width:336px;overflow:hidden;")', 'author-advertising-pro'), "</small></p></div>";

echo "<div style='height:auto;width:100%;clear:both;float:left;margin: 5px 0 0 0;'>";
echo "<p>", _e('Css personalizzato:', 'author-advertising-pro'), " <input type='text' name='ad_css' value='$ad_css' /></p>";
echo "</div>";

echo "</div>";

	if (!empty($ad_generated_code)) {
   //SHOW ADS CODE
   echo "<div style='float:left;clear:both;margin: 15px 0 15px 75px;'><p><b>", _e('Codice Adsense generato per l\'annuncio', 'author-advertising-pro'), " ID.$selected_id :<br/></b></p><textarea style='resize:none;' readonly='readonly' cols='100' rows='10'>$ad_generated_code</textarea></div>";
   }


echo "</div></div>";

echo "<div style='margin: 30px 0 30px 60px;float:left;'><input type='submit' value='", __('Salva questo annuncio', 'author-advertising-pro'), "' />";
echo "</form>";
echo "</div>";
echo "<div style='float:left;margin: 30px 0 30px 20px;'><form name='delete' action='". esc_attr($_SERVER['REQUEST_URI']) ."' method='POST'><input type='hidden' name='delete_ads' value='delete_ads' /><input type='hidden' name='delete_id' value='$selected_id' /><input type='submit' name='delete_ads_send' OnClick=\"return confirm('", _e('Sei sicuro di voler eliminare questo annuncio defenitivamente?', 'author-advertising-pro'), "');\" value='", __('Elimina questo annuncio', 'author-advertising-pro'), "' /></form></div>

	<div style='float:left;margin: 30px 0 30px 20px;'><form name='no_ads' method='POST' action='". esc_attr($_SERVER['REQUEST_URI']) ."''>
			<input type='hidden' name='show_old_ads' value='1' />";
    echo "</form></div>";


}

}

}


?>