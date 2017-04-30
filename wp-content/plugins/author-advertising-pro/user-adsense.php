<?php

function kd_authoredit(){
   global  $wpdb, $user_ID;
   $wpdb->show_errors();
   $google_values = get_option('kd_author_advertising');
   
   $base_prefix = $wpdb->base_prefix;
   $table_name = $base_prefix . "author_advertising";
   
   if(isset($_POST['update_kd_googleauthor'])) {
      $user_advertising = $wpdb->escape($_POST['user_google']);
	  $user_advertising = ltrim($user_advertising);
	  $user_advertising = rtrim($user_advertising);
      $user_custom1 = $wpdb->escape($_POST['custom1']);
	  $user_custom1 = ltrim($user_custom1);
	  $user_custom1 = rtrim($user_custom1);
      $user_custom2 = $wpdb->escape($_POST['custom2']);
	  $user_custom2 = ltrim($user_custom2);
	  $user_custom2 = rtrim($user_custom2);
      $user_custom3 = $wpdb->escape($_POST['custom3']);
	  $user_custom3 = ltrim($user_custom3);
	  $user_custom3 = rtrim($user_custom3);
      $user_custom4 = $wpdb->escape($_POST['custom4']);
	  $user_custom4 = ltrim($user_custom4);
	  $user_custom4 = rtrim($user_custom4);
      $user_custom5 = $wpdb->escape($_POST['custom5']);
	  $user_custom5 = ltrim($user_custom5);
	  $user_custom5 = rtrim($user_custom5);
      $user_custom6 = $wpdb->escape($_POST['custom6']);
	  $user_custom6 = ltrim($user_custom6);
	  $user_custom6 = rtrim($user_custom6);
	  
      $google_id = $wpdb->get_var("SELECT author_advertising FROM $table_name WHERE author_id=$user_ID");
              if(!$google_id) {
                  $user_exists = $wpdb->get_var("SELECT author_id FROM $table_name WHERE author_id=$user_ID");
                  if(!$user_exists){ $wpdb->query("INSERT INTO $table_name (author_id, author_advertising, author_custom1, author_custom2, author_custom3, author_custom4, author_custom5, author_custom6) VALUES ('$user_ID', '$user_advertising', '$user_custom1', '$user_custom2', '$user_custom3', '$user_custom4', '$user_custom5', '$user_custom6')"); }}

         $wpdb->query("UPDATE $table_name SET author_advertising='$user_advertising', author_custom1='$user_custom1', author_custom2='$user_custom2', author_custom3='$user_custom3', author_custom4='$user_custom4', author_custom5='$user_custom5', author_custom6='$user_custom6' WHERE author_id='$user_ID'");
   }
   $user_details = $wpdb->get_row("SELECT * FROM $table_name WHERE author_id = '$user_ID'");
   $google_id = $user_details->author_advertising;
   $user_custom1 = $user_details->author_custom1;
   $user_custom2 = $user_details->author_custom2;
   $user_custom3 = $user_details->author_custom3;
   $user_custom4 = $user_details->author_custom4;
   $user_custom5 = $user_details->author_custom5;
   $user_custom6 = $user_details->author_custom6;
   ?>
   <div class="wrap">
   <form method="post">
      <h3><?php echo $google_values['myadsense_title']; ?></h3>
      <?php echo $google_values['myadsense_text']; ?>

   <?php 
   // setting images path
   $path = WP_PLUGIN_URL.'/'.str_replace(basename( __FILE__),"",plugin_basename(__FILE__));
   $ok_path = $path . "images/ok.png";
   $cancel_path = $path . "images/cancel.png";
   
   $pub_id_len = strlen($google_id);
   
     echo '<table class="form-table">';
	 echo '<tr valign="top"><th scope="row">', __('Il tuo pub-id Adsense', 'author-advertising-pro'), '</th>';
     echo '<td><input type="text" name="user_google" value="';
	 echo $google_id; 
	 echo '">&nbsp;&nbsp;&nbsp;';
   
   
   $pub_check = substr($google_id, 0, 4);
	if ($pub_check === 'pub-') { $pub_check = 1; } else { $pub_check = 0; }
   
   if($pub_id_len <> 20 && $pub_check == 0) {
   
         echo '<img src="' . $cancel_path .'" alt="Il codice inserito non sembra corretto" style="vertical-align:middle;margin-left:10px;" />&nbsp;&nbsp;&nbsp;', __('Manca il suffisso "pub-" all\'inizio del codice numerico', 'author-advertising-pro'), '</td></tr>';
   

   
	} elseif($pub_id_len == 20 && $pub_check == 0) {
   
         echo '<img src="' . $cancel_path .'" alt="Il codice inserito non sembra corretto" style="vertical-align:middle;margin-left:10px;" />&nbsp;&nbsp;&nbsp;', __('Manca il suffisso "pub-" all\'inizio del codice numerico', 'author-advertising-pro'), '</td></tr>';
	
	
	} elseif ($pub_check == 1 && $pub_id_len == 20) {
   
   echo '<img src="' . $ok_path .'" alt="Il codice inserito sembra corretto" style="vertical-align:middle;margin-left:10px;" />&nbsp;&nbsp;&nbsp;', __('Il codice pub-id inserito sembra corretto', 'author-advertising-pro'), '</td></tr>';
   
   } elseif($pub_id_len <> 20 && $pub_check == 1) {
   
   echo '<img src="' . $cancel_path .'" alt="Il codice inserito non sembra corretto" style="vertical-align:middle;margin-left:10px;" />&nbsp;&nbsp;&nbsp;', __('Il codice pub-id dovrebbe essere di 4 caratteri + 16 cifre (es.: pub-0123456789012345)!', 'author-advertising-pro'), '</td></tr>'; 
	  	 
		  
	  }
	  
   
	 echo '</td></tr>';
   
   
   //check pub id 1
   $pub1_len = strlen($user_custom1);

  
   if($google_values['adslot1'] == 1){ echo '<tr valign="top"><th scope="row">' . $google_values['adslot1_title'] . '</th><td><input type="text" name="custom1" value="' . $user_custom1 . '">&nbsp;&nbsp;&nbsp;';
   
   if ($pub1_len==10 && is_numeric ($user_custom1)) {
   
   echo '<img src="' . $ok_path .'" alt="', __('Il codice inserito sembra corretto', 'author-advertising-pro'), '" style="vertical-align:middle;" />&nbsp;&nbsp;&nbsp;', __('Il codice inserito sembra corretto', 'author-advertising-pro'), '</td></tr>';
   
   } else {
   
   echo '<img src="' . $cancel_path .'" alt="', __('Il codice inserito NON sembra corretto', 'author-advertising-pro'), '" style="vertical-align:middle;" />&nbsp;&nbsp;&nbsp;', __('Il codice inserito non sembra corretto (deve essere numerico, di 10 cifre, senza caratteri e senza spazi)', 'author-advertising-pro'), '</td></tr>'; }
   }

   //check pub id 2
   $pub2_len = strlen($user_custom2);
   
   if($google_values['adslot2'] == 1){ echo '<tr valign="top"><th scope="row">' . $google_values['adslot2_title'] . '</th><td><input type="text" name="custom2" value="' . $user_custom2 . '">&nbsp;&nbsp;&nbsp;';
   
   if($pub2_len == 10 && is_numeric ($user_custom2)) {
   
   echo '<img src="' . $ok_path .'" alt="', __('Il codice inserito sembra corretto', 'author-advertising-pro'), '" style="vertical-align:middle;" />&nbsp;&nbsp;&nbsp;', __('Il codice inserito sembra corretto', 'author-advertising-pro'), '</td></tr>';
   
   } else {
   
   echo '<img src="' . $cancel_path .'" alt="', __('Il codice inserito NON sembra corretto', 'author-advertising-pro'), '" style="vertical-align:middle;" />&nbsp;&nbsp;&nbsp;', __('Il codice inserito non sembra corretto (deve essere numerico, di 10 cifre, senza caratteri e senza spazi)', 'author-advertising-pro'), '</td></tr>'; }
   } 
   
   //check pub id 3
   $pub3_len = strlen($user_custom3);
   
   if($google_values['adslot3'] == 1){ echo '<tr valign="top"><th scope="row">' . $google_values['adslot3_title'] . '</th><td><input type="text" name="custom3" value="' . $user_custom3 . '">&nbsp;&nbsp;&nbsp;';
   
   if($pub3_len == 10 && is_numeric ($user_custom3)) {
   
   echo '<img src="' . $ok_path .'" alt="', __('Il codice inserito sembra corretto', 'author-advertising-pro'), '" style="vertical-align:middle;" />&nbsp;&nbsp;&nbsp;', __('Il codice inserito sembra corretto', 'author-advertising-pro'), '</td></tr>';
   
   } else {
   
   echo '<img src="' . $cancel_path .'" alt="', __('Il codice inserito NON sembra corretto', 'author-advertising-pro'), '" style="vertical-align:middle;" />&nbsp;&nbsp;&nbsp;', __('Il codice inserito non sembra corretto (deve essere numerico, di 10 cifre, senza caratteri e senza spazi)', 'author-advertising-pro'), '</td></tr>'; }
   }
   
 
   //check pub id 4
   $pub4_len = strlen($user_custom4);
   
   if($google_values['adslot4'] == 1){ echo '<tr valign="top"><th scope="row">' . $google_values['adslot4_title'] . '</th><td><input type="text" name="custom4" value="' . $user_custom4 . '">&nbsp;&nbsp;&nbsp;';
   
   if($pub4_len == 10 && is_numeric ($user_custom4)) {
   
   echo '<img src="' . $ok_path .'" alt="', __('Il codice inserito sembra corretto', 'author-advertising-pro'), '" style="vertical-align:middle;" />&nbsp;&nbsp;&nbsp;', __('Il codice inserito sembra corretto', 'author-advertising-pro'), '</td></tr>';
   
   } else {
   
   echo '<img src="' . $cancel_path .'" alt="', __('Il codice inserito NON sembra corretto', 'author-advertising-pro'), '" style="vertical-align:middle;" />&nbsp;&nbsp;&nbsp;', __('Il codice inserito non sembra corretto (deve essere numerico, di 10 cifre, senza caratteri e senza spazi)', 'author-advertising-pro'), '</td></tr>'; }
   } 
   
   
   //check pub id 5
   $pub5_len = strlen($user_custom5);
   
   if($google_values['adslot5'] == 1){ echo '<tr valign="top"><th scope="row">' . $google_values['adslot5_title'] . '</th><td><input type="text" name="custom5" value="' . $user_custom5 . '">&nbsp;&nbsp;&nbsp;';
   
   if($pub5_len == 10 && is_numeric ($user_custom5)) {
   
   echo '<img src="' . $ok_path .'" alt="', __('Il codice inserito sembra corretto', 'author-advertising-pro'), '" style="vertical-align:middle;" />&nbsp;&nbsp;&nbsp;', __('Il codice inserito sembra corretto', 'author-advertising-pro'), '</td></tr>';
   
   } else {
   
   echo '<img src="' . $cancel_path .'" alt="', __('Il codice inserito NON sembra corretto', 'author-advertising-pro'), '" style="vertical-align:middle;" />&nbsp;&nbsp;&nbsp;', __('Il codice inserito non sembra corretto (deve essere numerico, di 10 cifre, senza caratteri e senza spazi)', 'author-advertising-pro'), '</td></tr>'; }
   } 
   
   
   //check pub id 6
   $pub6_len = strlen($user_custom6);
   
   if($google_values['adslot6'] == 1){ echo '<tr valign="top"><th scope="row">' . $google_values['adslot6_title'] . '</th><td><input type="text" name="custom6" value="' . $user_custom6 . '">&nbsp;&nbsp;&nbsp;';
   
   if($pub6_len == 10 && is_numeric ($user_custom6)) {
   
   echo '<img src="' . $ok_path .'" alt="', __('Il codice inserito sembra corretto', 'author-advertising-pro'), '" style="vertical-align:middle;" />&nbsp;&nbsp;&nbsp;', __('Il codice inserito sembra corretto', 'author-advertising-pro'), '</td></tr>';
   
   } else {
   
   echo '<img src="' . $cancel_path .'" alt="', __('Il codice inserito NON sembra corretto', 'author-advertising-pro'), '" style="vertical-align:middle;" />&nbsp;&nbsp;&nbsp;', __('Il codice inserito non sembra corretto (deve essere numerico, di 10 cifre, senza caratteri e senza spazi)', 'author-advertising-pro'), '</td></tr>'; }
   } 
   
   ?>
   </table>

   <input type="hidden" name="update_kd_googleauthor" value="1">
   <p class="submit"><input type="submit" name="info_update" value="Save Changes" /></p>
   </form>
   </div>
<?php } ?>
<?php

function kd_authoredit_profile() {

echo "<div class=\"wrap\">";
echo "<h3>Codici Adsense</h3>";
echo "<p>Per inserire e modificare i tuoi codici Adsense <a href='" . get_bloginfo('url') . "/wp-admin/index.php?page=author-advertising-pro' >clicca qui!</a></p></div>";

}

add_action("edit_user_profile", "kd_authoredit_profile", 2, 1);  ?>