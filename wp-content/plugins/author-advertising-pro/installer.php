<?php 


function kd_installer() {

global $wpdb;

include_once dirname( __FILE__ ) . '/install-function.php';
	
//Install Function
if (isset($_POST['install'])) {

$type_install = $_POST['install'];


if ($type_install == 'auto') {

   echo '<div id="icon-options-general" class="icon32"><br /></div>';
   echo "<h2>", _e('Author Advertising Pro - Installazione Automatica', 'author-advertising-pro'), "</h2><br/><br/>";
   
   echo "<div class='wrap' style=''><br/>";
   echo "<p>", _e('Inizializzazione procedura di installazione automatica...attendere prego.', 'author-advertising-pro'), "</p><br/>";
   
   
   //returned function value: true if table was created.
   $install = kd_install();
   
   if ($install) {
   echo "<p><span style='color:green;'>", _e('La creazione della tabella dedicata, &egrave; avvenuta con successo!', 'author-advertising-pro'), "</span><br/>";
   echo _e('Author Advertising Pro ora &egrave; pronto per funzionare.', 'author-advertising-pro'), "<br/><br/>";
   echo "<a href='". get_bloginfo('url') . "/wp-admin/admin.php?page=author-advertising-pro-admin'>", _e('Vai alla configurazione del plugin', 'author-advertising-pro'), "</a></p>";
   
   echo "</div>";
   
   } else { 
   
   $kd_need_install = kd_check_install();
   
   if ((!$kd_need_install) && ($_POST['install'] == 'auto')) {
   
   echo '<div id="icon-options-general" class="icon32"><br /></div>';
   echo "<h2>", _e('Author Advertising Pro - Installazione Automatica', 'author-advertising-pro'), "</h2><br/><br/>";
   
   echo "<div class='wrap' style=''><br/>";
   echo "<span style='color:green;'>",_e('La tabella esiste gi&agrave; all\'interno del database.', 'author-advertising-pro'), "</span><br/><br/>";
   echo "<a href='". get_bloginfo('url') . "/wp-admin/admin.php?page=author-advertising-pro-admin'>", _e('Vai alla configurazione del plugin', 'author-advertising-pro'), "</a>";
   
   
   echo "</div>";
   
   } else {

   echo '<div id="icon-options-general" class="icon32"><br /></div>';
   echo "<h2>", _e('Author Advertising Pro - Installazione Automatica', 'author-advertising-pro'), "</h2><br/><br/>";
   
   echo "<div class='wrap' style=''><br/>";
   echo "<span style='color:red;'>", _e('ERRORE: La procedura di installazione automatica non &egrave; andata a buon fine.', 'author-advertising-pro'), "</span><br/>";
      echo "<a href='". get_bloginfo('url') . "/wp-admin/admin.php?page=author-advertising-pro-admin'>", _e('Torna indietro', 'author-advertising-pro'), "</a> ",  _e('e prova con l\'installazione manuale (cos&igrave potrai visualizzare gli eventuali errori delle singole query MySQL).', 'author-advertising-pro');
	  
   
   echo "</div>";


}

}

} elseif ($type_install == 'manual') {

   echo '<div id="icon-options-general" class="icon32"><br /></div>';
   echo "<h2>", _e('Author Advertising Pro - Installazione Manuale', 'author-advertising-pro'), "</h2><br/><br/>";
   
   echo "<div class='wrap' style=''><br/>";
   
      echo "<a href='" . $_SERVER['http_referrer'] . "'>", _e('Torna indietro', 'author-advertising-pro'), "</a> ", _e('per selezionare la modalit&agrave; di installazione automatica.', 'author-advertising-pro');
   
echo "<p>", _e('Per procedere con l\'installazione manuale segui queste istruzioni:', 'author-advertising-pro'), "<br />";
   echo _e('1) Accedi al tuo pannello di controllo PhpMyAdmin del tuo database MySQL.', 'author-advertising-pro'), "<br />";
   echo _e('2) Clicca sulla \'linguetta\' SQL (troverai la scritta: Esegui la/e query SQL sul database...)', 'author-advertising-pro'), "<br />";
   echo _e('3) Quindi copia il codice sottostante ed incollalo nel campo di testo della Query MySQL da eseguire sul tuo database.', 'author-advertising-pro'),"<br />";
   echo _e('4) Clicca su ESEGUI.', 'author-advertising-pro'),"<br /></p>";
   

   //table name's
   $table_name = $base_prefix . "author_advertising";
   $table_name_ad_google = $base_prefix . "author_advertising_ad_google";
   $table_name_ad_rotator = $base_prefix . "author_advertising_ad_rotator";
   
   //query db 1
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
   
   //query db2
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
   PRIMARY KEY  (`id`)
   ) DEFAULT CHARACTER SET utf8;";
   
   //query db3
   $sql3 = "CREATE TABLE ".$table_name_ad_rotator." (
   id mediumint(9) NOT NULL auto_increment,
   ad_code text NOT NULL,
   ad_custom1 text,
   ad_custom2 text,
   PRIMARY KEY  (`id`)
   ) DEFAULT CHARACTER SET utf8;";
   
   echo "<p style='background-color:#ccc; border:1px solid #666; font-family:courier new, courier; padding:5px;'><strong>" , _e('CODICE Query SQL:', 'author-advertising-pro'), "</strong><br/>" . $sql . "<br/></p>";
   
   echo "<p>", _e('Ripeti l\'operazione (seguendo di nuovo i punti precedenti, dal n.1 al n.4) anche per la query sottostante', 'author-advertising-pro'), "</p>";
   
   echo "<p style='background-color:#ccc; border:1px solid #666; font-family:courier new, courier; padding:5px;'><strong>" , _e('CODICE Query SQL:', 'author-advertising-pro'), "</strong><br/>" . $sql2 . "<br/></p>";
   
   echo "<p>", _e('Ripeti l\'operazione (seguendo di nuovo i punti precedenti, dal n.1 al n.4) anche per la query sottostante', 'author-advertising-pro'), "</p>";
   
   echo "<p style='background-color:#ccc; border:1px solid #666; font-family:courier new, courier; padding:5px;'><strong>" , _e('CODICE Query SQL:', 'author-advertising-pro'), "</strong><br/>" . $sql3 . "<br/></p>";
   
   echo "<p>", _e('5) Fine. Esci da PhpMyAdmin e aggiorna questa pagina (F5).', 'author-advertising-pro'),"<br /></p>";
   echo "</div>";




} } else {

   echo '<div id="icon-options-general" class="icon32"><br /></div>';
   echo "<h2>", _e('Author Advertising Pro - Installazione', 'author-advertising-pro'), "</h2><br/><br/>";
   
   echo "<div class='wrap' style=''><br/>";
   echo _e('Per funzionare correttamente Author Advertising Pro ha bisogno di creare una nuova tabella all\'interno del database.', 'author-advertising-pro'), "<br/><br/>";
   echo _e('Per continuare, scegli tra la modalit&agrave; di installazione che preferisci:', 'author-advertising-pro'), "<br/><br/>";
   echo "<form name='input' action='". esc_attr($_SERVER['REQUEST_URI']) ."' method='post'>
   <select name='install'>
<option value='auto'>", __('Installazione Automatica', 'author-advertising-pro'), "</option>
<option value='manual'>", __('Installazione Manuale', 'author-advertising-pro'), "</option>
</select>
<input type='submit' value='", __('Vai', 'author-advertising-pro'), "' />
</form>";
   echo "</div>";
   	
	}
	
	}

?>