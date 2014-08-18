<?php
function kd_admin_adv() {

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
   echo "<h2>", _e('Author Advertising Pro - Gestione Ad Rotator', 'author-advertising-pro'), "</h2><br/>";
   
   echo "<div class='wrap' style=''><br/>";
   
   echo kd_menu_application();
   
   echo "<div>", kd_help_icon(), "<div style='width:70%;text-align:justify;'>";
   
   echo "<p><b>", _e('In questa sezione potrai gestire, creare o eliminare gli annunci di altre campagne NON-ADSENSE (Zanox, Tradedoubler ecc...).</b><br/><br/>Copia e Incolla direttamente qui sotto il codice completo dell\'annuncio fornito dal tuo Advertiser. ', 'author-advertising-pro'), "</p>";
   echo "<p>", _e('Scegli il tipo di annuncio che desideri, poi compila tutti i campi necessari a farlo funzionare correttamente sul sito;<br/>In questa nuova versione di Author Advertising Pro, potrai decidere direttamente dove posizionare gli annunci all\'interno delle pagine senza dover mettere mano al codice del tuo template.', 'author-advertising-pro'), "</p>";
   
   echo "</div></div>";
   
   /*
   
//GET VALUES
 global $wpdb;

   $base_prefix = $wpdb->base_prefix;
   $table_name_ad_google = $base_prefix . "author_advertising_ad_rotator";
   
   $ad_active = $wpdb->get_var( $wpdb->prepare( "SELECT ad_active FROM $table_name_ad_google WHERE id='$selected_id'" ) );
   $ad_type = $wpdb->get_var( $wpdb->prepare( "SELECT ad_type FROM $table_name_ad_google WHERE id='$selected_id'" ) );
   $ad_type2 = $wpdb->get_var( $wpdb->prepare( "SELECT ad_type2 FROM $table_name_ad_google WHERE id='$selected_id'" ) );
   $ad_format = $wpdb->get_var( $wpdb->prepare( "SELECT ad_format FROM $table_name_ad_google WHERE id='$selected_id'" ) );
   
   
*/

echo "<p><br/><b>Function is Coming soon in next 5.1.1 version...about 4 April 2012 :)</b></p>";


}


}


?>