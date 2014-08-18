<?

//REFERRAL MODE

function kd_referral_install()
{
    global $wpdb;
	//set the options

	$newoptions['kd_referral_mode_active'] = '0';
	$newoptions['kd_referral_percentage'] = '0';	
	$newoptions['kd_referral_override'] = '1';
	$newoptions['kd_referral_expire'] = '30';
	$newoptions['kd_referral_trkpar'] = 'ref_uid';
	$newoptions['kd_referral_usrpage'] = '1';
	$newoptions['kd_referral_showid'] = '1';
	add_option('kd_referral_cfg', $newoptions);

}

register_activation_hook(__FILE__,'kd_referral_install');

// process incomming referral
function kd_referral_getreferral()
{
	global $wpdb;
	$kd_referral_cfg = array(
		'kd_referral_mode_active'		=> '',
		'kd_referral_percentage'			=> '',
		'kd_referral_override'			=> '',
		'kd_referral_expire'			=> '',
		'kd_referral_redir'			=> '',
		'kd_referral_trkpar'			=> '',
		'kd_referral_usrpage'			=> '',
		'kd_referral_land'				=> '',
		'kd_referral_showid'			=> ''
	);
	$kd_referral_cfg = get_option('kd_referral_cfg');
	
	if(!($kd_referral_cfg['kd_referral_trkpar'])){
		$kd_referral_cfg['kd_referral_trkpar'] = 'ref_uid';
	}

	foreach ($_GET as $key => $value) {
		if ($key == $kd_referral_cfg['kd_referral_trkpar']) {
			$referral_id = $value;
    	}
	}
	
	if(isset($referral_id)) {
		if (!$kd_referral_cfg['kd_referral_override']){
			// check if cookie already exists
			if(isset($_COOKIE['kdref'])){
				return;
			}
		}
		if($kd_referral_cfg['kd_referral_expire']){
			$exp = time()+60*60*24*$kd_referral_cfg['kd_referral_expire'];
		}
		$wp_root = get_option('home');
		$htp 		= "http://";
		$htps		= "https://";
		$kd_referral_domain = str_replace($htp, ".", $wp_root);
		$kd_referral_domain = str_replace($htps, ".", $kd_referral_domain);
		$kd_referral_domain = explode("/",$kd_referral_domain);
		// set cookie
		setcookie('kdref', $referral_id, $exp, '/', $kd_referral_domain[0]);
		if($kd_referral_cfg['kd_referral_land']) {
			header("Location: ".$kd_referral_cfg['kd_referral_land']);
			exit(0);
		}
	}

	
}
	
add_action("init", "kd_referral_getreferral");

function kd_referral_signupform()
{
	global $wpdb;
	$kd_referral_cfg = array(
		'kd_referral_mode_active'		=> '',
		'kd_referral_percentage'			=> '',
		'kd_referral_override'			=> '',
		'kd_referral_expire'			=> '',
		'kd_referral_redir'			=> '',
		'kd_referral_trkpar'			=> '',
		'kd_referral_usrpage'			=> '',
		'kd_referral_showid'			=> ''
		);
	$kd_referral_cfg = get_option('kd_referral_cfg');

	// check if we have a cookie
	if(isset($_COOKIE['kdref'])){
		$form_referral = $_COOKIE['kdref'];
		if ($kd_referral_cfg['kd_referral_showid']){
			echo'<p>
			<label>', _e('Referral ID','author-advertising-pro'), '<br />
			<input type="text" name="kd_referral_referral" id="user_login" class="input" value="'.$form_referral.'" readonly="readonly" size="20" tabindex="30" /></label>
			</p>';
		} else {
			echo'<input type="hidden" name="kd_referral_referral" id="user_login" class="input" value="'.$form_referral.'" readonly="readonly" size="20" tabindex="30" />';
		}
	} else {
	
	if (!isset($_POST['kd_referral_referral'])) {
	$form_referral = $_GET[$kd_referral_cfg['kd_referral_trkpar']];
	} else {
	$form_referral = $_POST['kd_referral_referral'];
	}
	
	$ref_exist = username_exists($form_referral);
	if (!$ref_exist) { $form_referral = ''; }
	
		echo '<p>
			<label>Referral ID<br />
			<input type="text" name="kd_referral_referral" id="user_login" class="input" value="'.$form_referral.'" readonly="readonly" size="20" tabindex="30" /></label>
		</p>';
		
	}
}

add_action("register_form", "kd_referral_signupform"); 

function kd_referral_register($userid)
{
	global $wpdb;
	
	$table_users = $wpdb->users;
	$reffered = $_POST['kd_referral_referral'];
	$reffered_id = $wpdb->get_var("SELECT ID FROM $table_users WHERE user_login='$reffered' LIMIT 1");
	//$reffered = $wpdb->escape($_COOKIE['kd_referral']);
	
	
	$table = $wpdb->base_prefix."author_advertising";
    $wpdb->query("INSERT INTO $table(author_id, his_referral_id) VALUES('$userid', '$reffered_id')");
}

add_action("user_register", "kd_referral_register"); 



function kd_referral_redirect($redirect_to, $requested_redirect_to, $user)
{
	if ( !isset ( $user->user_login ) ) {
		return $redirect_to;
	}
	
	if($user->user_level){
		if($user->user_level > 7){
			return $requested_redirect_to;
		}
	}
	
	$kd_referral_cfg = array(
		'kd_referral_mode_active'		=> '',
		'kd_referral_percentage'			=> '',
		'kd_referral_override'			=> '',
		'kd_referral_expire'			=> '',
		'kd_referral_redir'			=> '',
		'kd_referral_trkpar'			=> '',
		'kd_referral_usrpage'			=> '',
		'kd_referral_showid'			=> ''
		);
	$kd_referral_cfg = get_option('kd_referral_cfg');
	
	if ($kd_referral_cfg['kd_referral_redir']){
		return $kd_referral_cfg['kd_referral_redir'];
	} else {
		return $requested_redirect_to;
	}
}

add_filter("login_redirect", "kd_referral_redirect", 10, 3);

// Add new column to the user list
function kd_referral_addcolumn( $columns ) {
	// This requires WP 2.8+
	$columns['kd_referral_refbycol'] = __('referred by', 'user-locker');
	$columns['kd_referral_refcountcol'] = __('referred', 'user-locker');

	return $columns;
}

add_filter("manage_users_columns", "kd_referral_addcolumn");
		
// Add column content for each user on user list
function kd_referral_fillcolumn( $value, $column_name, $user_id ) {
	global $wpdb;
	if ( $column_name == 'kd_referral_refbycol' ) {
		// get referral name
		$user_info = get_userdata($user_id);
		
    	$table = $wpdb->prefix."author_advertising";
		$referral = $wpdb->get_var("SELECT his_referral_id FROM $table WHERE author_id=$user_id");
	
	$table_users = $wpdb->users;
	$referral = $wpdb->get_var("SELECT user_login FROM $table_users WHERE ID='$referral' LIMIT 1");
	
		if($referral){
			return $referral;
		}else{
			return "-";
		}
	}
	
	if ( $column_name == 'kd_referral_refcountcol' ) {
		// count referrals by this user
		$user_info = get_userdata($user_id);
    	$table = $wpdb->prefix."author_advertising";
		$ref_count = $wpdb->get_var("SELECT COUNT(*) FROM $table WHERE his_referral_id = '$user_info->ID'");
		if($ref_count){
			return $ref_count;
		}else{
			return "-";
		}
	}

	return $value;
}

add_filter("manage_users_custom_column", "kd_referral_fillcolumn", 10, 3 );

function kd_referral_userpage($user_id)
{
	global $wpdb;
	$kd_referral_cfg = array(
		'kd_referral_override'			=> '',
		'kd_referral_percentage'			=> '',
		'kd_referral_expire'			=> '',
		'kd_referral_redir'			=> '',
		'kd_referral_trkpar'			=> '',
		'kd_referral_usrpage'			=> '',
		'kd_referral_showid'			=> ''
		);
	$kd_referral_cfg = get_option('kd_referral_cfg');
	
	if(!($kd_referral_cfg['kd_referral_trkpar'])){
		$kd_referral_cfg['kd_referral_trkpar'] = 'ref_uid';
	}

	if ($kd_referral_cfg['kd_referral_usrpage']){
		$affurl = get_option('siteurl').'/wp-login.php?action=register&'.$kd_referral_cfg['kd_referral_trkpar'].'='.$user_id->user_login;
		// count referrals by this user
		$table = $wpdb->prefix."author_advertising";
		$ref_count = $wpdb->get_var("SELECT COUNT(*) FROM $table WHERE his_referral_id = '$user_id->ID'");
		if(!$ref_count){
			$ref_count = "0";
		}
		// get referral name
		$table_users = $wpdb->users;
		$get_his_referral_id = $wpdb->get_var("SELECT his_referral_id FROM $table WHERE author_id = '$user_id->ID'");
		$referral = $wpdb->get_var("SELECT user_login FROM $table_users WHERE ID='$get_his_referral_id' LIMIT 1");
	
	
		if(!$referral){
			$referral = '-';
		}	
		
		echo "<h3>", _e('Referral', 'author-advertising-pro'), "</h3>
			<table class=\"form-table\">
				<tr>
					<th>", _e('Ti sei iscritto tramite', 'author-advertising-pro'), "</th>
					<td>$referral</td>
				</tr>
				<tr>
					<th>", _e('Numero di utenti che si sono iscritti con il tuo Link', 'author-advertising-pro'), "</th>
					<td>$ref_count</td></tr>";
					
					if ($ref_count) {
					echo "<tr><th>", _e('Lista degli utenti', 'author-advertising-pro'), "</th><th>", _e('Nome Utente', 'author-advertising-pro'), "</th><th>", _e('Numero Articoli Pubblicati', 'author-advertising-pro'), "</th></tr>";
					$ref_results = $wpdb->get_results("SELECT author_id FROM $table WHERE his_referral_id = '$user_id->ID'");
		
					foreach ($ref_results as $ref_results) {
					$ref_id = $ref_results->author_id;
					$ref_name = $wpdb->get_var("SELECT user_login FROM $table_users WHERE ID='$ref_id'");
					$ref_posts =  count_user_posts( $ref_id );
					
					echo "<tr><td></td><td>$ref_name</td>";
					echo "<td>$ref_posts</td><td></td></tr>";
					}
					}
					
					
			echo "</td></tr>
				<tr>
					<b>", _e('Il tuo Link-Referral', 'author-advertising-pro'), ":</b><br/>
					<p><a href='$affurl' target='_blank'>$affurl</a><br/>
					<span class=\"description\" style=\"color:grey;\" >", _e('Invita persone ad iscriversi tramite il tuo link! Potrai guadagnare una percentuale sulle impression Adsense visualizzate tramite i loro articoli.<br/>Le impression vengono ripartite sottraendo la percentuale che guadagnerai da quella che invece andrebbe allo staff. Gli utenti che si iscrivono tramite il tuo link guadagnano sempre la stessa percentuale fissa di impression per ogni articolo scritto, anche se si registrano senza referral.', 'author-advertising-pro'), "</span></p>
				</tr>
			</table>";
	}	
}

add_action("edit_user_profile", "kd_referral_userpage", 1, 1); 
add_action("profile_personal_options", "kd_referral_userpage", 1, 1); 

function kd_referral_custom_userpage($user_id, $tag_html1, $tag_html2)
{
	global $wpdb;
	$table_users = $wpdb->users;
	$table = $wpdb->prefix."author_advertising";
	
	$kd_referral_cfg = array(
		'kd_referral_override'			=> '',
		'kd_referral_percentage'			=> '',
		'kd_referral_expire'			=> '',
		'kd_referral_redir'			=> '',
		'kd_referral_trkpar'			=> '',
		'kd_referral_usrpage'			=> '',
		'kd_referral_showid'			=> ''
		);
	$kd_referral_cfg = get_option('kd_referral_cfg');
	
	$user_login = $wpdb->get_var("SELECT user_login FROM $table_users WHERE id = '$user_id'");
	
	if(!($kd_referral_cfg['kd_referral_trkpar'])){
		$kd_referral_cfg['kd_referral_trkpar'] = 'ref_uid';
	}

	if ($kd_referral_cfg['kd_referral_usrpage']){
		$affurl = get_option('siteurl').'/wp-login.php?action=register&'.$kd_referral_cfg['kd_referral_trkpar'].'='.$user_login;
		// count referrals by this user
		$ref_count = $wpdb->get_var("SELECT COUNT(*) FROM $table WHERE his_referral_id = '$user_id'");
		if(!$ref_count){
			$ref_count = "0";
		}
		// get referral name
		
		$get_his_referral_id = $wpdb->get_var("SELECT his_referral_id FROM $table WHERE author_id = '$user_id'");
		$referral = $wpdb->get_var("SELECT user_login FROM $table_users WHERE ID='$get_his_referral_id' LIMIT 1");
	
	
		if(!$referral){
			$referral = '-';
		}	
		
		echo $tag_html1, _e('Referral', 'author-advertising-pro'),$tag_html2,"
			<table class=\"form-table\">
				<tr>
					<th>", _e('Ti sei iscritto tramite', 'author-advertising-pro'), "</th>
					<td>$referral</td>
				</tr>
				<tr>
					<th>", _e('Numero di utenti che si sono iscritti con il tuo Link', 'author-advertising-pro'), "</th>
					<td>$ref_count</td></tr>";
					
					if ($ref_count) {
					echo "<tr><th>", _e('Lista degli utenti', 'author-advertising-pro'), "</th><th>", _e('Nome Utente', 'author-advertising-pro'), "</th><th>", _e('Numero Articoli Pubblicati', 'author-advertising-pro'), "</th></tr>";
					$ref_results = $wpdb->get_results("SELECT author_id FROM $table WHERE his_referral_id = '$user_id'");
		
					foreach ($ref_results as $ref_results) {
					$ref_id = $ref_results->author_id;
					$ref_name = $wpdb->get_var("SELECT user_login FROM $table_users WHERE ID='$ref_id'");
					$ref_posts =  count_user_posts( $ref_id );
					
					echo "<tr><td></td><td>$ref_name</td>";
					echo "<td>$ref_posts</td><td></td></tr>";
					}
					}
					
					
			echo "</td></tr>
				<tr>
					<b>", _e('Il tuo Link-Referral', 'author-advertising-pro'), ":</b><br/>
					<p><a href='$affurl' target='_blank'>$affurl</a><br/>
					<span class=\"description\" style=\"color:grey;\" >", _e('Invita persone ad iscriversi tramite il tuo link! Potrai guadagnare una percentuale sulle impression Adsense visualizzate tramite i loro articoli.<br/>Le impression vengono ripartite sottraendo la percentuale che guadagnerai da quella che invece andrebbe allo staff. Gli utenti che si iscrivono tramite il tuo link guadagnano sempre la stessa percentuale fissa di impression per ogni articolo scritto, anche se si registrano senza referral.', 'author-advertising-pro'), "</span></p>
				</tr>
			</table><hr/><br/>";
	}	
}


function kd_referral_custom_userpage_parse($content) {

global $current_user;
      get_currentuserinfo();
	  
	  $user_id = $current_user->ID;
	  $tag = "[REFERRAL_CUSTOM_USERPAGE]";
	  
	  if ($user_id >= 1) {
	  
	  if(strpos($content, $tag)){
	  
	  $custom_page = kd_referral_custom_userpage($user_id);

		$content = str_replace($tag, $custom_page, $content); 
		
	}

} else {
	if(strpos($content, $tag)){
	$err = __('Devi eseguire il login per visualizzare questa pagina', 'author-advertising-pro');
$content = str_replace($tag, $err, $content); 
}
}
return $content;
}

add_filter('the_content', 'kd_referral_custom_userpage_parse');

function kd_referral_options() {

$kd_referral_cfg = array(
	'kd_referral_mode_active'		=> '',
	'kd_referral_percentage'			=> '',
	'kd_referral_override'			=> '',
	'kd_referral_expire'			=> '',
	'kd_referral_redir'			=> '',
	'kd_referral_trkpar'			=> '',
	'kd_referral_usrpage'			=> '',
	'kd_referral_land'				=> '',
	'kd_referral_showid'			=> ''
);
$ol_flash = '';
if(isset($_POST['kd_referral_submit'])) {
	
	$kd_referral_cfg['kd_referral_mode_active'] = $_POST['kd_referral_mode_active'];
	$kd_referral_cfg['kd_referral_percentage'] = $_POST['kd_referral_percentage'];
	$kd_referral_cfg['kd_referral_override'] = $_POST['kd_referral_override'];
	$kd_referral_cfg['kd_referral_expire'] = $_POST['kd_referral_expire'];
	$kd_referral_cfg['kd_referral_redir'] = $_POST['kd_referral_redir'];
	$kd_referral_cfg['kd_referral_trkpar'] = $_POST['kd_referral_trkpar'];
	$kd_referral_cfg['kd_referral_usrpage'] = $_POST['kd_referral_usrpage'];
	$kd_referral_cfg['kd_referral_land'] = $_POST['kd_referral_land'];
	$kd_referral_cfg['kd_referral_showid'] = $_POST['kd_referral_showid'];
	update_option('kd_referral_cfg',$kd_referral_cfg);
	$ol_flash = "Your settings have been saved.";
}
if ($ol_flash != '') echo '<div id="message"class="updated fade"><p>' . $ol_flash . '</p></div>';

$kd_referral_cfg = get_option('kd_referral_cfg');
$affurl = get_option('siteurl').'/?ref_uid=';

//HTML OPTIONS

echo kd_G_icon();
echo "<h2>", _e('Author Advertising Pro - Referral Mode', 'author-advertising-pro'), "</h2><br/><br/>";
   
echo "<div class='wrap' style=''>";

echo kd_menu_application();

   echo "<div>", kd_help_icon(), "<div style='width:70%;text-align:justify;'>";
   
   echo "<p><b>", _e('In questa sezione potrai attivare e configurare la funzione Referral.', 'author-advertising-pro'), "</b></p>";
   echo "<p>", _e('<b>Come funziona?</b> La funzione Referral mode permette ai tuoi utenti/autori di invitare altre persone ad iscriversi a questo sito. Funziona in modo molto semplice: ogni utente trover&agrave; nel proprio <a href="/wp-admin/profile.php" target="blank">profilo</a> un link personale, da copiare ed inviare agli amici. In questo modo gli utenti che si iscriveranno tramite quel link diventeranno sub-affiliati dell\'utente che li ha invitati. <br/><br/><i>Quest\'ultimo, se desideri incentivare gli inviti, otterr&agrave; quindi una percentuale (che decidi tu) sulle impression generate dagli articoli scritti dagli amici invitati!</i><br/><br/><b>Ad esempio:</b> Se Luca invita due amici, Diego e Silvia, e questi scrivono 3 articoli ciascuno, il Pub_id di Luca verr&agrave; visualizzato in questi 6 articoli secondo la percentuale che imposterai qui sotto!<br/><br/>Tale percentuale viene sottratta all\'admin, infatti come puoi vedere, per evitare errori di calcolo, puoi impostare una percentuale massima, uguale o inferiore a quella che hai salvato nelle Impostazioni generali del plugin.', 'author-advertising-pro'), "</p>";
   
   echo "</div></div>";



echo "<br/><hr/><table class='form-table'>
<form method='post'>
<tr valign='top'>
<th scope='row'>", _e('Attiva Referral Mode', 'author-advertising-pro'), "</th>
	<td><input name='kd_referral_mode_active' type='checkbox' value='1'";
	if($kd_referral_cfg['kd_referral_mode_active']){echo"checked";} 
	echo " ></td>
	<td>", _e('Seleziona se desideri attivare la funzione Referral Mode.', 'author-advertising-pro'), "</td>
</tr>";



////dropdown percentage
	$google_values = get_option('kd_author_advertising');
   
   $max = $google_values['admin_xc'];
   $selected = $kd_referral_cfg['kd_referral_percentage'];
   if ($selected <> 0) {
   
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
   
   $percentage_dropdown = "<select onChange='submit();' name='kd_referral_percentage'>".$percentage_dropdown."</select>&nbsp;&nbsp;%";
   	
echo "<tr valign='top'>
<th scope='row'>", _e('Impression Referral', 'author-advertising-pro'), "</th>
	<td>";
	echo $percentage_dropdown;
	echo "</td>
	<td>", _e('Seleziona la percentuale di condivisione per gli utenti che fanno iscrivere nuovi autori.', 'author-advertising-pro'), "</td>
</tr>";


echo "<tr valign='top'>
<th scope='row'>", _e('Annulla Cookie', 'author-advertising-pro'), "</th>
	<td><input name='kd_referral_override' type='checkbox' value='1'";
	if($kd_referral_cfg['kd_referral_override']){echo"checked";} 
	echo " ></td>
	<td>", _e('Seleziona se desideri che il cookie gi&agrave esistente non venga sostituito da nuovi link-referral.', 'author-advertising-pro'), "</td>
</tr>
<tr valign='top'>
<th scope='row'>", _e('Validit&agrave del cookie', 'author-advertising-pro'), "</th>
	<td><input name='kd_referral_expire' type='text' value='" . $kd_referral_cfg['kd_referral_expire'] . "' size='10' maxlength='10'></td>
	<td>", _e('Numero di giorni di validit&agrave del cookie. Imposta 0 se vuoi che venga eliminato alla chiusura del browser.', 'author-advertising-pro'), "</td>
</tr>
<tr>
<tr valign='top'>
<th scope='row'>", _e('Redirect URL', 'author-advertising-pro'), "</th>
	<td><input name='kd_referral_redir' type='text' value='" . $kd_referral_cfg['kd_referral_redir'] . "' size='30' maxlength='100'></td>
	<td>", _e('URL per il redirect degli utenti che eseguono il login (tranne gli utenti con ruolo di amministratore).', 'author-advertising-pro'), "</td>
</tr>
<tr>
<tr valign='top'>
<th scope='row'>", _e('Landingpage URL', 'author-advertising-pro'), " </th>
	<td><input name='kd_referral_land' type='text' value='" . $kd_referral_cfg['kd_referral_land'] . "' size='30' maxlength='100'></td>
	<td>", _e('Puoi inserire un URL dove redirezionare un utente che arriva sul sito da un referral-link.', 'author-advertising-pro'), "</td>
</tr>

<tr valign='top'>
<th scope='row'>", _e('Visualizza il campo referral', 'author-advertising-pro'), "</th>
	<td><input name='kd_referral_showid' type='checkbox' value='1'";
	if($kd_referral_cfg['kd_referral_showid']){echo'checked';} 
	
	echo "></td>
	<td>", _e('Seleziona per mostrare il campo referral nella pagina di registrazione.', 'author-advertising-pro'), "</td>
</tr>
<tr valign='top'>
<th scope='row'>", _e('Visualizza informazioni sul Referral nella pagina profilo degli utenti.', 'author-advertising-pro'), "</th>
	<td><input name='kd_referral_usrpage' type='checkbox' value='1'";
	
	if($kd_referral_cfg['kd_referral_usrpage']){echo'checked';} 
	echo " ></td>
	<td>", _e('Visualizza informazioni come l\'utente tramite il quale ci si &egrave registrati, il numero di utenti che si sono iscritti tramite il nostro link, ed il numero di articoli scritti da ciascuno di essi.', 'author-advertising-pro'), "</td>
</tr>
<th scope='row'>", _e('Shortcode', 'author-advertising-pro'), "</th>
	<td>[REFERRAL_CUSTOM_USERPAGE]</td>
	<td>", _e('Copia e incolla questo TAG nel contenuto di una pagina o di un articolo per visualizzare le informazioni sul referral.', 'author-advertising-pro'), "</td>
</tr>
<th scope='row'>", _e('Codice Template', 'author-advertising-pro'), "</th>
	<td>";
	echo '<textarea rows="4" cols="30" readonly>&lt;?php global $current_user; get_currentuserinfo(); echo kd_referral_custom_userpage($current_user->ID); ?&gt;</textarea>';
	echo "</td>
	<td>", _e('Copia e incolla questo script nel contenuto php di una pagina (page.php) o di un articolo(single.php) per visualizzare le informazioni sul referral.', 'author-advertising-pro'), "</td>
</tr>
<tr>
<tr valign='top'>
<th scope='row'></th>
	<td><p class='submit'><input type='submit' name='Submit' value='", _e('Salva', 'author-advertising-pro'), "' /></p></td>
</tr>
<input name='kd_referral_submit' type='hidden' value='1'>
</form>
</table>
</div>";

}

?>