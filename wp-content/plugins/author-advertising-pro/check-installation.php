<?php

function kd_check_install() {

global $wpdb;

$debug = false;


		//Recupero nomi tabelle
		$table_name_array = kd_table_names();
		
		//set indice conteggio ciclo
		$i = 1;
		
		//start check with no errors
		$errors = 0;
		
		//conto il numero di db nell'array
		$db_count = count($table_name_array);
		
		//popolo l'array che contiene le query per verificare l'esistenza di ciascun db
		while ($i <= $db_count) :
	
			$table_name = $table_name_array[$i];
			
			//debug
			if ($debug) { echo "<p>Cerco la tabella " . $table_name . " con ID= " . $i . " - nel database</p>"; }
			
			$check_db_table[$i] = $wpdb->get_var("show tables like '$table_name'");
			
			//debug
			if ($debug) { echo "<p>eseguo la query con risultato " . $check_db_table[$i] . "</p>"; }
	
				if (!isset($check_db_table[$i])) {
				$errors++;
				}
		$i++;
		endwhile;
	
	
	if ($errors >= 1) { $kd_need_install = true; } else { $kd_need_install = false; }
	
	//debug errors
	if ($debug) { echo "Database non trovati (errori): " . $errors; }
	
	return $kd_need_install;
	
	}
	
	
function kd_check_update() {

global $wpdb;

$debug = false;

	//table name's
	$table_name_array = kd_table_names();
	
	//conta db
	$db_count = count($table_name_array);
	
	//set first index_db value
	$i_db = 1;
	
	//set to zero error values
	$errors = 0;
	
	//debug
		if ($debug) { echo "<p>Ci sono " . $db_count . " tabelle sul database</p>"; }
	
	
	while ($i_db <= $db_count) :
	
	
		//start update tables
		//update function 
		$table_columns = kd_db_columns($i_db);
		$table_code_add_columns = kd_db_add_columns_code($i_db);
		
		$i_max = count($table_columns);
		$check_values = count($table_code_add_columns);
		
			//debug
			if ($debug) { echo "<p>Ci sono " . $i_max . " colonne nella tabella corrente: " . $i_db . "</p>"; }
	
			//controlla se il numero di valori negli array corrispondono
			if ($i_max == $check_values) {
		
				//set/reset first index_db_columns value
				$i_col = 1;
	
				//check if  table column exist
				while ($i_col <= $i_max) :

				$current_table_name = $table_name_array[$i_db];
				$current_column = $table_columns[$i_col];
		
				$check_column_exist[$i_col] = $wpdb->query("show columns from $current_table_name like '$current_column'");
		
				//debug
					if ($debug) { echo "<p>Cerco in " . $current_table_name . " la colonna corrente:" . $current_column . ". con id= " . $i_col ." Esiste = " . $check_column_exist[$i_col]; }
		
					if($check_column_exist[$i_col] === 0) {
					$errors++; 
					}
				$i_col++;
				endwhile;
		
			} else { echo _e('Error code: #01_array', 'author-advertising-pro'); }
		
		$i_db++;
	endwhile;
   
   
///////////////////////////////////////////////////
	
	//debug
	//show total errors occurred
		if ($debug) { echo "<p>Totale Errori rilevati:   " . $errors . "</p>"; }
	
	if($errors >= 1) {
	
	$kd_need_update = true; } else { $kd_need_update = false; }
	
	return $kd_need_update;
	
	}
	
	
?>