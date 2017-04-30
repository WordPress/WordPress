<?php

function kd_admin_users(){
   global $wpdb;
   
   $base_prefix = $wpdb->base_prefix;
   $table_name = $base_prefix . "author_advertising";
   $google_values = get_option('kd_author_advertising');
   echo "<div class=wrap>";
   $action = $_POST['action'];
   if($action == "delete"){
      $user_id = $_POST['user_id'];
      $wpdb->query("DELETE FROM $table_name WHERE author_id='$user_id'");
      }
   if($action == "edit"){
      $user_id = $_POST['user_id'];
   ?>
   
<script LANGUAGE="JavaScript">
<!--
function confirmSubmit()
{
var agree=confirm("<?php echo __('Sicuro di voler eliminare questo utente?', 'author-advertising-pro'); ?>");
if (agree)
	return true ;
else
	return false ;
}
// -->
</script>
   	  <?php echo kd_G_icon(); ?>
      <h2><?php echo _e('Author Advertising Pro - Modifica Utente', 'author-advertising-pro') ?></h2>
	  <br/>
	  
   <table class="form-table">
   <form method="post">
   <input type="hidden" name="action" value="edited">
   <input type="hidden" name="user_id" value="<?php echo $user_id; ?>">
   
   <tr valign="top"><th scope="row"><?php _e('User ID', 'author-advertising-pro') ?></th>
   <td><input type="text" name="user_id" disabled value="<?php echo $user_id;  ?>"></td>
   </tr>
   <tr valign="top"><th scope="row"><?php _e('Username', 'author-advertising-pro') ?></th>
   <td><input type="text" name="user_id" disabled value="<?php echo $wpdb->get_var("SELECT user_login FROM $wpdb->users WHERE ID='$user_id'");  ?>"></td>
   </tr>
   <tr valign="top"><th scope="row"><?php _e('Referral id', 'author-advertising-pro') ?></th>
   <td><?php 
		$selected = $wpdb->get_var("SELECT his_referral_id FROM $table_name WHERE author_id='$user_id'");
		//echo "sel=".$selected."   ";
		wp_dropdown_users(array('name' => 'his_referral_id', 'show' => 'user_login', 'selected' => $selected));
		?>
</td>
   </tr>
   <tr valign="top"><th scope="row"><?php _e('Rimuovi il Referral', 'author-advertising-pro') ?></th>
   <td>
   <input type="checkbox" name="delete_referral" value="NO"/>
</td>
   </tr>
   
   <tr valign="top"><th scope="row"><?php _e('Pub-id', 'author-advertising-pro') ?></th>
   <td><input type="text" name="edit_pubid" value="<?php echo $wpdb->get_var("SELECT author_advertising FROM $table_name WHERE author_ID='$user_id'"); //echo kd_get_google_id($user_id); ?>"></td>
   </tr>
   <tr valign="top"><th scope="row"><?php _e('AdSlot 1', 'author-advertising-pro') ?></th>
   <td><input type="text" name="edit_custom1" value="<?php echo $wpdb->get_var("SELECT author_custom1 FROM $table_name WHERE author_ID='$user_id'");  ?>"></td>
   </tr>
   <tr valign="top"><th scope="row"><?php _e('AdSlot 2', 'author-advertising-pro') ?></th>
   <td><input type="text" name="edit_custom2" value="<?php echo $wpdb->get_var("SELECT author_custom2 FROM $table_name WHERE author_ID='$user_id'");  ?>"></td>
   </tr>
      <tr valign="top"><th scope="row"><?php _e('AdSlot 3', 'author-advertising-pro') ?></th>
   <td><input type="text" name="edit_custom3" value="<?php echo $wpdb->get_var("SELECT author_custom3 FROM $table_name WHERE author_ID='$user_id'");  ?>"></td>
   </tr>
      <tr valign="top"><th scope="row"><?php _e('AdSlot 4', 'author-advertising-pro') ?></th>
   <td><input type="text" name="edit_custom4" value="<?php echo $wpdb->get_var("SELECT author_custom4 FROM $table_name WHERE author_ID='$user_id'");  ?>"></td>
   </tr>
      <tr valign="top"><th scope="row"><?php _e('AdSlot 5', 'author-advertising-pro') ?></th>
   <td><input type="text" name="edit_custom5" value="<?php echo $wpdb->get_var("SELECT author_custom5 FROM $table_name WHERE author_ID='$user_id'");  ?>"></td>
   </tr>
      <tr valign="top"><th scope="row"><?php _e('AdSlot 6', 'author-advertising-pro') ?></th>
   <td><input type="text" name="edit_custom6" value="<?php echo $wpdb->get_var("SELECT author_custom6 FROM $table_name WHERE author_ID='$user_id'");  ?>"></td>
   </tr>
   <tr valign="top"><th scope="row"><?php _e('Incentivo Autore (100%)', 'author-advertising-pro') ?></th>
   <td><input type="checkbox" name="edit_incentive" value="YES" <?php if($wpdb->get_var("SELECT author_incentive FROM $table_name WHERE author_ID='$user_id'") == "YES"){ echo " checked=\"checked\""; } ?> />
   
   <input type="hidden" name="edit_percentage" value="">
   
   </td>
   </tr>
   </table>
   <p class="submit"><input type="submit" name="submit" value="<?php _e('Salva', 'author-advertising-pro') ?>"></p></form>
   <div class="tablenav">


<br class="clear" />

</div>
<?php
      }

   if($action == "edited"){
   $user_id = $_POST['user_id'];
   $edited_id = stripslashes($_POST['edit_pubid']);
   	  $edited_id = ltrim($edited_id);
	  $edited_id = rtrim($edited_id);
   $edited_1 = stripslashes($_POST['edit_custom1']);
   	  $edited_1 = ltrim($edited_1);
	  $edited_1 = rtrim($edited_1);
   $edited_2 = stripslashes($_POST['edit_custom2']);
   	  $edited_2 = ltrim($edited_2);
	  $edited_2 = rtrim($edited_2);
   $edited_3 = stripslashes($_POST['edit_custom3']);
   	  $edited_3 = ltrim($edited_3);
	  $edited_3 = rtrim($edited_3);
   $edited_4 = stripslashes($_POST['edit_custom4']);
   	  $edited_4 = ltrim($edited_4);
	  $edited_4 = rtrim($edited_4);
   $edited_5 = stripslashes($_POST['edit_custom5']);
   	  $edited_5 = ltrim($edited_5);
	  $edited_5 = rtrim($edited_5);
   $edited_6 = stripslashes($_POST['edit_custom6']);
   	  $edited_6 = ltrim($edited_6);
	  $edited_6 = rtrim($edited_6);
	  
   $percentage = stripslashes($_POST['edit_percentage']);
   $incentive = $_POST['edit_incentive'];
   $delete_referral = $_POST['delete_referral'];
   
   if ($delete_referral) { $his_referral_id = "0"; } else { $his_referral_id = $_POST['his_referral_id']; }
   
   $wpdb->query("UPDATE $table_name SET author_advertising='$edited_id', his_referral_id=$his_referral_id, author_custom1='$edited_1',author_custom2='$edited_2', author_custom3='$edited_3', author_custom4='$edited_4', author_custom5='$edited_5', author_custom6='$edited_6',author_percentage='$percentage', author_incentive='$incentive' WHERE author_id='$user_id'");
}
   $userresults = $wpdb->get_results("SELECT ID, author_id, author_advertising, his_referral_id, author_custom1, author_custom2, author_custom3, author_custom4, author_custom5, author_custom6, author_percentage, author_incentive FROM $table_name ORDER BY author_id ASC");

?>

   	  <?php echo kd_G_icon(); ?>
      <h2><?php echo _e('Author Advertising Pro - Gestione Utenti', 'author-advertising-pro') ?></h2>
	  <br/>
	  <?php echo kd_menu_application(); ?>
	 
	  
<table class="widefat">
<thead>
<tr class="thead">
   <th><?php _e('UID', 'author-advertising-pro') ?></th>
   <th><?php _e('Username', 'author-advertising-pro') ?></th>
   <th><?php _e('Referral', 'author-advertising-pro') ?></th>
   <th><?php _e('Pub-id', 'author-advertising-pro') ?></th>
   <th><?php _e('AdSlot 1', 'author-advertising-pro') ?></th>
   <th><?php _e('AdSlot 2', 'author-advertising-pro') ?></th>
   <th><?php _e('AdSlot 3', 'author-advertising-pro') ?></th>
   <th><?php _e('AdSlot 4', 'author-advertising-pro') ?></th>
   <th><?php _e('AdSlot 5', 'author-advertising-pro') ?></th>
   <th><?php _e('AdSlot 6', 'author-advertising-pro') ?></th>
   <th><?php _e('Incentivo Autore','author-advertising-pro') ?></th>
   <th><?php _e('Azioni', 'author-advertising-pro') ?></th>
</tr>
</thead>
<tbody id="users" class="list:user user-list">
<?php foreach ($userresults as $userresult) { ?>
<tr id="<?php echo $userresult->author_id; ?>">
<td><?php echo $userresult->author_id; ?></td>
<td><?php $user_info = get_userdata($userresult->author_id); echo $user_info->user_login; ?></td>
<td><?php if ($userresult->his_referral_id <> "") { echo $wpdb->get_var("SELECT user_login FROM $wpdb->users WHERE ID='$userresult->his_referral_id'"); } ?></td>
<td><?php echo $userresult->author_advertising; ?></td>
<td><?php echo $userresult->author_custom1; ?></td>
<td><?php echo $userresult->author_custom2; ?></td>
<td><?php echo $userresult->author_custom3; ?></td>
<td><?php echo $userresult->author_custom4; ?></td>
<td><?php echo $userresult->author_custom5; ?></td>
<td><?php echo $userresult->author_custom6; ?></td>
<td><?php echo $userresult->author_incentive; ?></td>
<td><form method="post"><input type="hidden" name="action" value="delete"><input type="submit" class="button-secondary" name="submit" value="<?php _e('Elimina', 'author-advertising-pro') ?>" onClick="return confirmSubmit()"><input type="hidden" name="user_id" value="<?php echo $userresult->author_id; ?>"></form><form method="post"><input type="hidden" name="action" value="edit"><input type="hidden" name="user_id" value="<?php echo $userresult->author_id; ?>"><input type="submit" class="button-secondary" name="submit" value="<?php _e('Modifica', 'author-advertising-pro') ?>"></form>
</tr>
<?php } ?>
</tbody>
</table>
</div>
<?php } ?>