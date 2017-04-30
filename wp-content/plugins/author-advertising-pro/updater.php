<?php

function kd_updater() {

global $wpdb;

$debug = false;

	$kd_need_update = kd_check_update();
	
	echo "<div class='wrap' style=''>";
   	echo kd_G_icon();
	
		if($kd_need_update && (!isset($_POST['update']))){
	
	echo "<h2>", _e('Author Advertising Pro - Aggiornamento', 'author-advertising-pro'), "</h2><br/>";
	echo "<p>", _e('Per funzionare correttamente Author Advertising Pro ha bisogno di aggiornare la tabella correntemente utilizzata nel database.', 'author-advertising-pro'), "</p>";
   echo "<p><span style='color:red;'>", _e('****** I dati che si trovano nelle tabelle non subiranno modifiche o variazioni. Procedendo NON perderai la tua configurazione attuale. ******', 'author-advertising-pro'), "</span></p>";
 
	echo "<p>", _e('Prima di continuare &egrave; consigliato effettuare il backup del database attuale. Per procedere clicca sul pulsante OK.', 'author-advertising-pro'), "</p>"; 
   
	echo "<form action='" . esc_attr($_SERVER['REQUEST_URI']) . "' method='POST' name='kd_update'>";

					

		echo "<input type='hidden' name='update' value='kd_update' /><button type='submit'>OK</button></form>";
		
	} elseif (isset($_POST['update'])) {
	
	/////// UPDATE ////////
   
	echo "<h2>", _e('Author Advertising Pro - Aggiornamento in corso', 'author-advertising-pro'), "</h2><br/>";
	
	
	//table name's
	$table_name_array = kd_table_names();
	
	$i_db = 1;
	$db_count = count($table_name_array);
	
	
	while ($i_db <= $db_count) :
	
	//set index_column value to 1
	$i_col = 1;
	
	//start update tables
	//update function 
	$table_columns = kd_db_columns($i_db);
	$table_code_add_columns = kd_db_add_columns_code($i_db);
	
	$i_max = count($table_columns);
	$check_values = count($table_code_add_columns);
	
		//controlla se il numero di valori negli array corrispondono
		if ($i_max == $check_values) {
		
		echo "<p>", _e('UPDATE TABLE', 'author-advertising-pro'), " ", $i_db, "</p>";
	
	
			//check if new table column exist
			while ($i_col <= $i_max) :
	
			$current_table_name = $table_name_array[$i_db];
			$current_column = $table_columns[$i_col];
			$check_table_exist[$i_col] = $wpdb->query("show columns from $current_table_name like '$current_column'");
	
				if ($check_table_exist[$i_col] === 0) {
	
				$current_table_name = $table_name_array[$i_db];
				$current_code_add_column = $table_code_add_columns[$i_col];
	
				//prepare query
				$query = "alter table " . $current_table_name . " add column " . $current_code_add_column;
		
				$update = $wpdb->query($query);
				
					if ($update) { echo "<span style='color:green;'>", _e('Aggiornamento #', 'author-advertising-pro') , $i_col, " ", _e('eseguito con successo.', 'author-advertising-pro'), "</span><br/><br/>"; }
					
					else { echo "<span style='color:red;'>", _e('ERRORE: Aggiornamento #', 'author-advertising-pro'), $i_col, " ", _e('non eseguito.', 'author-advertising-pro'), "</span><br/><br/>"; }
		
				} else { echo _e('Aggiornamento #', 'author-advertising-pro'), $i_col, " ", _e('non necessario.', 'author-advertising-pro'), "<br/><br/>"; }
		
			$i_col++;
			
				if ($debug) { echo $query . " n. " . $i . "<br />"; }
				
			endwhile;
		
		} else { echo _e('Error code: #01_array', 'author-advertising-pro'); }
	
	$i_db++;
	endwhile;
	
   
   
   echo _e('Aggiornamento completato, se hai ricevuto degli errori consulta la guida o visita il', 'author-advertising-pro')," <a href='http://support.author-adv-pro.net' target='_blank'>Forum</a>", "<br/><br/>";

		
    }  
	
	   echo "</div>";	
	   
	}
	
?>