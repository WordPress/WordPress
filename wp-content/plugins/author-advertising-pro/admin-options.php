<?php

function kd_admin_options_percentage_dropdown($html_name, $selected, $plugin_is_active) {

	$google_values = get_option("kd_author_advertising");
	
	if ($html_name == "admin_xc") { $max = 100; }
	if ($html_name == "editor_xc") { $max = 100;}
	
   $max = $max - $google_values['admin_xc'] - $google_values['editor_xc'];

   if ($selected <> 0) {
   
   if ($max < 100) { $max = $selected + $max; }
   $i = $max;
   while ($i >= 0) :
   
   if ($i == $selected) { $value = 'selected'; } else { $value = ''; }
   
	$percentage_dropdown = $percentage_dropdown."<option value='".$i."' ".$value.">".$i."</option>";
	$i--;
   endwhile;
   
   } else {
   
   $i = 0;
   while ($i<=$max) :
      
   $percentage_dropdown = $percentage_dropdown."<option value='".$i."' ".$value.">".$i."</option>";
   $i++;
   
   endwhile;
   }
   
   $percentage_dropdown = "<select onChange='submit();' name='".$html_name."'>".$percentage_dropdown."</select>&nbsp;%";
   
   return $percentage_dropdown;
   
   }


function kd_clean_url($blog_url) {

/////////////////////////////////////////////////////////////////
$blog_url = str_ireplace('http://', '', $blog_url);
$blog_url = str_ireplace('http://www.', '', $blog_url);
$blog_url = str_ireplace('https://', '', $blog_url);
$blog_url = str_ireplace('https://www.', '', $blog_url);

$find_slash_pos = strpos($blog_url, "/");
if ($find_slash_pos > 3) {
$url_len = strlen($blog_url);
$char = $find_slash_pos - $url_len;
$clean_blog_url = substr($blog_url, 0, $char);

$blog_url = $clean_blog_url; }

return $blog_url;
}


function kd_admin_options(){
   global $wpdb;
   
   //check if Plugin need to install his db table.
   $kd_need_install = kd_check_install();
   
   //check if Plugin need to update db table column for a new version.
   $kd_need_update = kd_check_update();
   
   if ($kd_need_install) {
   
   kd_installer();
   
   } elseif ($kd_need_update) {
   
   kd_updater();
   
   } else {
   
   
   $base_prefix = $wpdb->base_prefix;
   $table_name = $base_prefix . "author_advertising";
   $wp_roles = new WP_Roles;
   $existingroles = $wp_roles->get_names();
   $current_roles = $wp_roles->roles;
   
   if(isset($_POST['update_kd_google'])) {
   $google_values['debug_mode'] = $_POST['debug_mode'];
   $google_values['admin_user_id'] = $_POST['admin_user_id'];
   $google_values['admin_xc'] = $_POST['admin_xc'];
   $google_values['editor_xc'] = $_POST['editor_xc'];
   $google_values['full_editor_xc_option'] = $_POST['full_editor_xc_option'];
   foreach($current_roles as $key => $role){
       $role = urlencode($role["name"]);
       if(isset($_POST[$role]) == $role){ $wp_roles->add_cap($key, 'author_advertising' ); }
       else { $wp_roles->remove_cap( $key, 'author_advertising' ); }
   }
   $current_roles = $wp_roles->roles;
   $google_values['level_user'] = $_POST['level_user'];
   $google_values['myadsense_title'] = stripslashes($_POST['myadsense_title']);
   $google_values['myadsense_text'] = stripslashes($_POST['myadsense_text']);
   $google_values['adslot1'] = $_POST['adslot1'];
   $google_values['adslot1_title'] = stripslashes($_POST['adslot1_title']);
   $google_values['adslot2'] = $_POST['adslot2'];
   $google_values['adslot2_title'] = stripslashes($_POST['adslot2_title']);
   $google_values['adslot3'] = $_POST['adslot3'];
   $google_values['adslot3_title'] = stripslashes($_POST['adslot3_title']);
   $google_values['adslot4'] = $_POST['adslot4'];
   $google_values['adslot4_title'] = stripslashes($_POST['adslot4_title']);
   $google_values['adslot5'] = $_POST['adslot5'];
   $google_values['adslot5_title'] = stripslashes($_POST['adslot5_title']);
   $google_values['adslot6'] = $_POST['adslot6'];
   $google_values['adslot6_title'] = stripslashes($_POST['adslot6_title']);
   $google_values['ad_google_rotator_mode'] = $_POST['ad_google_rotator_mode'];
   
   
   
   update_option("kd_author_advertising", $google_values);
   
   /*if (isset($_POST['activation_code'])) {
   
   $activation['code'] = $_POST['activation_code'];
   //echo $activation['code'];
   update_option('kd_activation_code', $activation);
   
   }*/
   
   $kd_referral_cfg = get_option('kd_referral_cfg');
   
   if ($kd_referral_cfg['kd_referral_percentage'] > $google_values['admin_xc']) {
   $kd_referral_cfg['kd_referral_percentage'] = $google_values['admin_xc'];
   update_option('kd_referral_cfg',$kd_referral_cfg);
   echo "<br/><br/><p style='color:red;font-size:16px;'><b>", _e('Poich&egrave; la percentuale dell\'admin &egrave; stata diminutita rispetto alla percentuale del Referral, di conseguenza &egrave; stato corretto anche il valore percentuale del Referral. Verifica le impostazioni del Referral Mode se vuoi modificare il suo valore.', 'author-advertising-pro'), "</b></p><br/><br/>";
   }
   
   }

//Reset Function
if($_POST['action'] == "resetall") { update_option("kd_author_advertising", ""); }
//End Reset Function

	
?>
<script LANGUAGE="JavaScript">
<!--
function confirmSubmit()
{
var agree=confirm("<?php echo __('Il Reset comporta la perdita permanente delle impostazioni di base del plugin. (I dati dei singoli utenti e delle altre funzioni non subiranno variazioni', 'author-advertising-pro'); ?>");
if (agree)
	return true ;
else
	return false ;
}
// -->
</script>

<?php 

$google_values = get_option('kd_author_advertising');
$plugin_version_db = get_option('kd_db_version');
$language = get_bloginfo('language');

?>

	  <?php echo kd_G_icon(); ?>
      <h2><?php echo _e('Author Advertising Pro - Impostazioni', 'author-advertising-pro') ?></h2>
      
	  <br/>

	  
	  <!-- STATUS -->
	  <p><?php echo "<span style='font-weight:bold;'>", _e('Lingua in uso:', 'author-advertising-pro'), "</span> ", $language; ?></p>
			
		
			
	  <?php $plugin_is_active = true; ?>
	  
	  <?php  if ($google_values['debug_mode'] == "1") {
	  
	  echo '<p style="color: red; font-weight: bold;">MODALITA\' DI DEBUG ATTIVA!</p>';
	  
	  }
	  
	  ?>
	  
	  <?php
	  
	  $admin_xc = $google_values['admin_xc'];
	  $editor_xc = $google_values['editor_xc'];
	  $total_user = 100 - $admin_xc - $editor_xc;
	  ?>
	  <?php echo kd_menu_application(); ?>
	  <br/>
	  <b><?php _e('Al momento le impression degli annunci Google sul tuo sito vengono suddivise con queste percentuali:', 'author-advertising-pro') ?></b><br/><br/>
	  
	  <?php $kd_referral_cfg = get_option('kd_referral_cfg'); ?>
	  <b><?php _e('Amministratore:', 'author-advertising-pro') ?></b> <?php echo $google_values['admin_xc']; ?> %<?php if ($kd_referral_cfg['kd_referral_mode_active'] == 1) { $admin_less_referral = $google_values['admin_xc'] - $kd_referral_cfg['kd_referral_percentage']; 
	  echo " - ( <i><small>", $admin_less_referral, "% ", _e(' con Referral mode attiva e <u>solo quando</u> si visualizzano gli articoli di autori sub-affiliati ad altri utenti.', 'author-advertising-pro'), " )</small></i>"; } ?>
	  <br />
	  <b><?php _e('Editori:', 'author-advertising-pro') ?></b> <?php echo $google_values['editor_xc']; ?> %<br />
	  <b><?php _e('Autori:', 'author-advertising-pro') ?></b> <?php echo $total_user ?> %<br />
	  
	  <?php
	  
	  if ($kd_referral_cfg['kd_referral_mode_active'] == 1) {
	  echo "<b>", _e('Referral:', 'author-advertising-pro'), "</b> ", $kd_referral_cfg['kd_referral_percentage'], " %<br />";
	  }
	  ?>
	  
	  
	  
	   <form method="post">  
<table class="form-table">

   <input type="hidden" name="update_kd_google" value="1">
   <p class="submit"><input type="submit" name="info_update" value="<?php _e('Salva le modifiche', 'author-advertising-pro') ?>" /></p>
   
   <?php
     //echo "<tr valign='top'><th scope='row'>", _e('Codice Installazione', 'author-advertising-pro'), "</th><td><input type='text' name='activation_code' size='25' /></td><td>", _e('Se l\'attivazione automatica non avviene correttamente, inserisci il tuo codice installazione (ne ricevi una copia via mail dopo la registrazione)', 'author-advertising-pro'), "</td></tr>";
   ?>

      <tr valign="top"><th scope="row"><?php _e('Modalit&agrave; di funzionamento:', 'author-advertising-pro') ?></th>
   <td>
   <?php
   
         		Switch ($google_values['ad_google_rotator_mode']) {
		
				case 'single_pub_id':
				$checked_0 = "checked='checked'";
				break;
				
				case 'multi_pub_id':
				$checked_1 = "checked='checked'";
				break;
		}
   

   echo "<label><input type='radio' name='ad_google_rotator_mode' value='single_pub_id' $checked_0 /> ", _e('Un solo Pub-id', 'author-advertising-pro'), "</label><br/>";
   echo "<label><input type='radio' name='ad_google_rotator_mode' value='multi_pub_id' $checked_1 /> ", _e('Multi Pub-id', 'author-advertising-pro'), "</label><br/>";
   
   
   ?>
   </td>
   <td><?php echo _e('Seleziona il metodo di funzionamento del Plugin. Come stabilito recentemente dal <a target="_blank" href="http://adsense.blogspot.com/2008/07/sharing-your-ad-space.html" >regolamento di Google Adsense</a> nel tuo sito puoi far apparire uno o pi&ugrave; Pub-id per ogni singola pagina. Leggi la guida per maggiori informazioni.', 'author-advertising-pro'); ?>
   </td>
   </tr>
   
   <tr valign="top"><th scope="row"><?php _e('Amministratore', 'author-advertising-pro') ?></th>
   <td>
   <?php wp_dropdown_users(array('name' => 'admin_user_id', 'selected' => $google_values['admin_user_id'])); ?>
   </td>
   <td><?php _e('Seleziona l\' amministratore del sito. Se un autore non specifica il proprio pub-id nel suo profilo verranno visualizzati gli annunci adsense con il pub-id dell\'amministratore all\'interno del suo articolo.', 'author-advertising-pro') ?>
   </td>
   </tr>

   <tr valign="top"><th scope="row"><?php _e('Percentuale di visualizzazione Ads Admin', 'author-advertising-pro') ?></th>
   <td><?php echo kd_admin_options_percentage_dropdown("admin_xc", $google_values['admin_xc'], $plugin_is_active); ?>
   </td>
   <td>
   <?php _e('Inserisci la percentuale di visualizzazioni che desideri ottenere come Amministratore. Impostando 20, i tuoi ads verranno visualizzati 20 volte su 100, mentre l\'Ads dell\'autore 80 su 100.', 'author-advertising-pro') ?>
   </td>
   </tr>
   
   <tr valign="top"><th scope="row"><?php _e('Percentuale di visualizzazione Ads Editori', 'author-advertising-pro') ?></th>
   <td><?php 
   
   if ($plugin_is_active) { echo kd_admin_options_percentage_dropdown("editor_xc", $google_values['editor_xc'], $plugin_is_active); }
   
   else { 
   echo "<input type='hidden' name='editor_xc' value='0' />";
   echo _e('Registra il plugin per attivare questa funzione','author-advertising-pro'); }
   
   
   
   ?>
   </td>
   <td>
   <?php _e('Inserisci la percentuale di visualizzazioni che desideri offrire ai moderatori/editori degli articoli. Impostando 20% admin e 10% editori, i tuoi ads verranno visualizzati 20 volte su 100, quello dell\'editore 10 volte su 100, mentre l\'Ads dell\'autore 70 su 100.', 'author-advertising-pro') ?>
   </td>
   </tr>
   <tr>
   <td><?php _e('Incentivo Editore', 'author-advertising-pro'); ?></td>
   <td><label for="full_editor_xc_option">
   
   <?php if ($plugin_is_active) { echo "<input type='checkbox' name='full_editor_xc_option' value='YES'";
   if($google_values['full_editor_xc_option']=="YES") echo " checked=\"checked\"";
   echo " />";
   } else { echo _e('Registra il plugin per attivare questa funzione','author-advertising-pro'); }
   ?>
   </label>
   </td>
   <td><?php _e('Attivando questa opzione, verr&agrave; dato il 100% delle impression agli articoli scritti dagli utenti con ruolo di Editore.', 'author-advertising-pro') ?></td>
   </tr>
   
   <tr valign="top"><th scope="row"><?php _e('Ruoli permessi', 'author-advertising-pro') ?></th>
   <td width="15%">
   <?php
        foreach($current_roles as $key => $role){
            echo '<input type="checkbox" name="' .  urlencode($role["name"]) . '" value="' . urlencode($role["name"]) . '"';
            if($role["capabilities"]['author_advertising'] == 1){ echo " checked"; }
            echo '> ' . $role["name"] . '<br/>';  
        }
   ?>
   </td>
   <td>
      <?php echo kd_info_icon(); ?>
	  <small><?php _e('<b>Nota bene:</b> Qui puoi abilitare i ruoli degli utenti che potranno visualizzare il link per inserire i propri dati di Adsense in Bacheca.<br/>A causa delle impostazioni di Wordpress, una volta selezionate le caselle ed aver salvato la configurazione le modifiche saranno effettive per tutti gli utenti, <b>tuttavia dovrai aggiornare nuovamente la pagina (F5) per visualizzare subito le voci nel men&ugrave;.</b>', 'author-advertising-pro') ?></small></td>
   </tr>

 </table>
 
 <br/>
 <hr/>

   <h3><?php _e('Opzioni utente in Bacheca', 'author-advertising-pro') ?></h3>
   <p><?php _e('Qui puoi modificare il testo che gli utenti visualizzaranno nelle opzioni Author Advertising della propria Bacheca, ovvero la pagina che permette agli iscritti di inserire il loro codice pub-id, ad-slot ecc...', 'author-advertising-pro') ?></p>

   <table class="form-table">
   <tr valign="top"><th scope="row"><?php _e('Titolo del link', 'author-advertising-pro') ?></th>
   <td><input type="text" name="myadsense_title" value="<?php echo $google_values['myadsense_title']; ?>" size="25"><br/><?php _e('es.: \'I miei codici Adsense\'', 'author-advertising-pro') ?></td>
   </tr>
   <tr valign="top"><th scope="row"><?php _e('Contenuto della pagina', 'author-advertising-pro') ?></th>
   <td><textarea rows="10" cols="50" name="myadsense_text"><?php echo $google_values['myadsense_text']; ?></textarea><br /><?php _e('Il testo di introduzione che dovrebbe guidare gli utenti ad inserire i loro codici nei campi corrispondenti', 'author-advertising-pro') ?></td>
   </tr>

   <tr valign="top"><th scope="row"><?php _e('Ad_slot utenti', 'author-advertising-pro') ?></th>
   <td><label for="adslot1"><input name="adslot1" type="checkbox" id="adslot1" value="1" <?php if($google_values['adslot1']=="1") echo "checked=\"checked \""; ?>/> <?php _e('Ad_Slot 1 Attivo', 'author-advertising-pro') ?></label><br/>
   
   <label for="adslot1_title"><?php _e('Descrizione Ad_Slot 1:', 'author-advertising-pro') ?> <input type="text" name="adslot1_title" value="<?php echo $google_values['adslot1_title']; ?>" size="25"></label><br/><br/>
   
   <label for="adslot2"><input name="adslot2" type="checkbox" id="adslot2" value="1" <?php if($google_values['adslot2']=="1") echo "checked=\"checked \""; ?>/> <?php _e('Ad_slot 2 Attivo', 'author-advertising-pro') ?></label><br/>
   
   <label for="adslot2_title"><?php _e('Descrizione Ad_Slot 2:', 'author-advertising-pro') ?> <input type="text" name="adslot2_title" value="<?php echo $google_values['adslot2_title']; ?>" size="25"></label><br/><br/>
   
   <label for="adslot3"><input name="adslot3" type="checkbox" id="adslot3" value="1" <?php if($google_values['adslot3']=="1") echo "checked=\"checked \""; ?>/> <?php _e('Ad_Slot 3 Attivo', 'author-advertising-pro') ?></label><br/>
   
   <label for="adslot3_title"><?php _e('Descrizione Ad_Slot 3:', 'author-advertising-pro') ?> <input type="text" name="adslot3_title" value="<?php echo $google_values['adslot3_title']; ?>" size="25"></label><br/><br/>
   
   <label for="adslot4"><input name="adslot4" type="checkbox" id="adslot4" value="1" <?php if($google_values['adslot4']=="1") echo "checked=\"checked \""; ?>/> <?php _e('Ad_slot 4 Attivo', 'author-advertising-pro') ?></label><br/>
   
   <label for="adslot4_title"><?php _e('Descrizione Ad_Slot 4:', 'author-advertising-pro') ?> <input type="text" name="adslot4_title" value="<?php echo $google_values['adslot4_title']; ?>" size="25"></label><br/><br/>
   
   <label for="adslot5"><input name="adslot5" type="checkbox" id="adslot5" value="1" <?php if($google_values['adslot5']=="1") echo "checked=\"checked \""; ?>/> <?php _e('Ad_Slot 5 Attivo', 'author-advertising-pro') ?></label><br/>
   
   <label for="adslot5_title"><?php _e('Descrizione Ad_Slot 5:', 'author-advertising-pro') ?> <input type="text" name="adslot5_title" value="<?php echo $google_values['adslot5_title']; ?>" size="25"></label><br/><br/>
   
   <label for="adslot6"><input name="adslot6" type="checkbox" id="adslot6" value="1" <?php if($google_values['adslot6']=="1") echo "checked=\"checked \""; ?>/> <?php _e('Ad_slot 6 Attivo', 'author-advertising-pro') ?></label><br/>
   
   <label for="adslot6_title"><?php _e('Descrizione Ad_Slot 6:', 'author-advertising-pro') ?> <input type="text" name="adslot6_title" value="<?php echo $google_values['adslot6_title']; ?>" size="25"></label><br/>

   </td>
   </table>

   <table class="form-table">
   
    <tr valign="top"  style="border: 1px solid blue;margin-bottom: 15px;background-color: #eaeaea;"><th scope="row"><?php _e('Attiva modalita\' di debug', 'author-advertising-pro'); ?></th>

	<td><input name="debug_mode" type="checkbox" id="debug_mode" value="1" <?php if($google_values['debug_mode']=="1") echo "checked=\"checked \""; ?>/> 
	<br />
	<?php echo _e('Se selezionato visualizzerai sotto all\'annuncio le informazioni relative al pub-id ed ai codici dell\'annuncio stesso.<br/>Solo gli utenti admin potranno visualizzare il debug sul sito.<br/>E\' una funzione utile per risolvere eventuali problemi senza dover aprire il sorgente HTML della pagina.', 'author-advertising-pro') ?>
	<br /></td>
   </tr>
   
   </table>

   <input type="hidden" name="update_kd_google" value="1">
   <p class="submit"><input type="submit" name="info_update" value="<?php _e('Salva le modifiche', 'author-advertising-pro') ?>" /></p>
   </form>
<form method="post">
    <input type="hidden" name="action" value="resetall">
    <p class="submit"><input type="submit" name="settodefault" value="<?php _e('Reset Impostazioni', 'author-advertising-pro') ?>" onClick="return confirmSubmit()"/></p>
</form>

   <div class="wrap">

   </div>
<?php
}
}

?>