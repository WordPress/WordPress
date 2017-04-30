<?php

function kd_admin_values() {

   $kd_admin_mode = get_option("kd_admin_mode");
   
   if ($kd_admin_mode[0] = 'multi') {
   
   $n_admin = $kd_admin_mode[1] * 2;
   
   $i=1;
   $c=2;
   while ($i <= $n_admin) :
   
		$admin_values[$i] = $kd_admin_mode[$c];
   $i++;
   $c++;
   endwhile;
   
   
   } else { $admin_values = 'false';}
   
   return $admin_values;
   
   //how to read $admin_values array.
   
   /*show values_debug-function
   $admin_values = kd_admin_values();
   $elements = count($admin_values);
   
   if (isset($elements)) {
   $i=1;
   while ($i <= $elements) :
   echo $admin_values[$i], "<br />";
   $i++;
   endwhile;
   } */


}



function kd_percentage_dropdown($count, $selected, $n_admin) {
   //create %_dropdown
   $percentage_dropdown1 = "";
   
   $kd_admin_mode = get_option("kd_admin_mode");
   
   /*$max = 100;
   $i = 1;
   $id_value = 6;
   while ($i <= $n_admin) :
   
   $max = $max - $kd_admin_mode[$id_value];
   
   $i++;
   $id_value++;
   endwhile;*/
   
   
   $max = 100 - $kd_admin_mode[6] - $kd_admin_mode[7] - $kd_admin_mode[8] - $kd_admin_mode[9];

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
   
   $percentage_dropdown = "<select onChange='submit();' name='percentage_admin_".$count."'>".$percentage_dropdown."</select>&nbsp;% impression";
   
   return $percentage_dropdown;
   
   }
   

function kd_admin_mode() {
//Admin mode

   //check if Plugin need to install his db table.
   $kd_need_install = kd_check_install();
   
   //check if Plugin need to update db table column for a new version.
   $kd_need_update = kd_check_update();
   
   if ($kd_need_install) {
   
   kd_installer();
   
   } elseif ($kd_need_update) {
   
   kd_updater();
   
   } else {


if (isset($_POST['kd_admin_mode_save'])) {

$kd_admin_mode = get_option("kd_admin_mode");
$n_admin = $kd_admin_mode[1];


if ($n_admin > $_POST['n_admin']) {

$kd_admin_mode[1] = $_POST['n_admin'];
$kd_admin_mode[6] = "0";
$kd_admin_mode[7] = "0";
$kd_admin_mode[8] = "0";
$kd_admin_mode[9] = "0";

} else {

$kd_admin_mode[1] = $_POST['n_admin'];
$kd_admin_mode[6] = $_POST['percentage_admin_1'];
$kd_admin_mode[7] = $_POST['percentage_admin_2'];
$kd_admin_mode[8] = $_POST['percentage_admin_3'];
$kd_admin_mode[9] = $_POST['percentage_admin_4'];

}

$kd_admin_mode[0] = $_POST['mode'];
$kd_admin_mode[2] = $_POST['admin_1'];
$kd_admin_mode[3] = $_POST['admin_2'];
$kd_admin_mode[4] = $_POST['admin_3'];
$kd_admin_mode[5] = $_POST['admin_4'];


update_option("kd_admin_mode", $kd_admin_mode);


}


$kd_admin_mode = get_option("kd_admin_mode");


   echo kd_G_icon();
   echo "<h2>", _e('Author Advertising Pro - Admin Mode', 'author-advertising-pro'), "</h2><br/>";
   
   echo "<div class='wrap' style=''><br/>";
   
   echo kd_menu_application();
   
   echo "<div>", kd_help_icon(), "<div style='width:70%;text-align:justify;'>";
   
   echo "<h3>", _e('Scegli la modalit&agrave; di funzionamento di Auhtor Advertising Pro.', 'author-advertising-pro'), "</h3>";
   echo "<p><i>", _e('Questa funzione ti permette di scegliere se vuoi suddividere la percentuale di impression dell\'Amministratore con altri utenti specifici (ad esempio dei soci): Essi non dovranno per forza avere il ruolo di amministratore nel sito, sar&agrave; sufficiente che siano registrati e che abbiano inserito i propri dati adsense nel loro pannello di controllo.', 'author-advertising-pro'), "</i></p></div></div><br />";
   
   echo "<div style='float:left;margin: 10px 20px 0 74px;height: 50px;clear:both;'>";
   echo "<h4>", _e('Modalit&agrave; singolo/multi Admin.', 'author-advertising-pro'), "</h4>";
   echo "<form name='admin_mode' action='". esc_attr($_SERVER['REQUEST_URI']) ."' method='post'>";
   
   //clear values
	$single_checked = '';
	$multi_checked = '';

	//set the values
	if ($kd_admin_mode[0] == 'single') { $single_checked = 'checked'; $multi_checked = '';} elseif ($kd_admin_mode[0] == 'multi') { $multi_checked = 'checked'; }
	
	//show the Option Radio showing saved values
   echo "<input onChange='submit();' type='radio' name='mode' value='single'", $single_checked," /> ", _e('Un solo Admin', 'author-advertising-pro'), "<br />
<input onChange='submit();' type='radio' name='mode' value='multi'", $multi_checked," /> ", _e('Multi Admin', 'author-advertising-pro'), "<br /><br /></div>
<div style='float:left;height:auto;margin: 70px 0 0 -50px;'>
<input type='hidden' name='n_admin' value='$kd_admin_mode[1]' />
<input type='hidden' name='admin_1' value='$kd_admin_mode[2]' />
<input type='hidden' name='admin_2' value='$kd_admin_mode[3]' />
<input type='hidden' name='admin_3' value='$kd_admin_mode[4]' />
<input type='hidden' name='admin_4' value='$kd_admin_mode[5]' />
<input type='hidden' name='percentage_admin_1' value='$kd_admin_mode[6]' />
<input type='hidden' name='percentage_admin_2' value='$kd_admin_mode[7]' />
<input type='hidden' name='percentage_admin_3' value='$kd_admin_mode[8]' />
<input type='hidden' name='percentage_admin_4' value='$kd_admin_mode[9]' />
<input type='hidden' name='kd_admin_mode_save' value='1' />
</div>";
   
   
   if (!empty($multi_checked)) {
   echo "<div style='float:left;margin: 40px 0 0 74px;clear:both;'>";
   
   echo "<div style='float:left;margin: 20px 0 0 0;'>";
   echo "<h4>", _e('Scegli il numero di utenti con i quali condividere le impression.', 'author-advertising-pro'), "</h4>";
   echo _e('Numero di Admin', 'author-advertising-pro'), "&nbsp;&nbsp;";
   
   echo "<select onChange='submit();' name='n_admin'>";
   
   $i=2;
   while ($i <= 4) :
   $selected = '';
   if ($i == $kd_admin_mode[1]) { $selected = "selected='$i'"; } else { $selected = ""; }
   
   echo "<option value='$i' $selected>$i</option><br />";
   $i++;
   endwhile;
   
   echo "</select>";
      echo "<p><i>", kd_info_icon(), _e('Ogni volta che <b>diminuisci</b> il numero di admin selezionati, le percentuali si resettano automaticamente.', 'author-advertising-pro'), "</i></p>";
   echo "</div>";
     
   echo "<div style='float:left;margin: 20px 0 20px 0;clear:both;'>";
   echo "<h4>", _e('Scegli gli utenti dalla lista ed imposta, per ciascuno di essi, la percentuale di impression da condividere.', 'author-advertising-pro'), "</h4>";
	//create user_dropdown
	$a = 1;
	$n_admin = $kd_admin_mode[1];
	$id = 2;
	$id2 = 6;
	while ($a<=$n_admin) :
	$name = 'admin_'.$a;
	$selected = $kd_admin_mode[$id];
	$percentage_selected = $kd_admin_mode[$id2];
	$percent_count = $percent_count + $kd_admin_mode[$id2];
	echo __('Admin', 'author-advertising-pro'), " ", $a, ":&nbsp;&nbsp;&nbsp;&nbsp;";
	wp_dropdown_users(array('name' => $name, 'selected' => $selected));
	
	echo "&nbsp;&nbsp;&nbsp;&nbsp;";
	echo kd_percentage_dropdown($a, $percentage_selected, $n_admin);
	echo "<br />";
	$a++;
	$id++;
	$id2++;
   endwhile;
   
   


echo "<br />";

if ($percent_count < 100) { $color = 'red'; } else { $color = 'green'; }
   echo "<p><b>", _e('Percentuale totale:', 'author-advertising-pro'), " <span style='color:$color;'>", $percent_count, "</span>/100</b><br />";
   echo kd_info_icon(), "<i>", _e('Ricordati che devi impostare le percentuali di ogni utente in modo che il totale sia 100/100, altrimenti avrai perdite di impression.', 'author-advertising-pro'), "</i></p>";

echo "<input type='hidden' name='kd_admin_mode_save' value='1' />
<input type='submit' value='", __('Salva', 'author-advertising-pro'), "' />
</form></div>";



echo "</div>";

}


   echo "</div>";
   

   
   
   
   }
   
   }
   
 ?>