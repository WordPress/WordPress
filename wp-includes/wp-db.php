<?php
//  WordPress DB Class

//  ORIGINAL CODE FROM:
//  Justin Vincent (justin@visunet.ie)
//	http://php.justinvincent.com

define('EZSQL_VERSION', 'WP1.25');
define('OBJECT', 'OBJECT', true);
define('ARRAY_A', 'ARRAY_A', false);
define('ARRAY_N', 'ARRAY_N', false);
if (!defined('SAVEQUERIES'))
	define('SAVEQUERIES', false);

class wpdb {

	var $show_errors = true;
	var $num_queries = 0;	
	var $last_query;
	var $col_info;

	// ==================================================================
	//	DB Constructor - connects to the server and selects a database

	function wpdb($dbuser, $dbpassword, $dbname, $dbhost) {
		$this->dbh = @mysql_connect($dbhost,$dbuser,$dbpassword);
		if (!$this->dbh) {
			die("<div>
			<p><strong>Error establishing a database connection!</strong> This probably means that the connection information in your <code>wp-config.php</code> file is incorrect. Double check it and try again.</p>
			<ul>
			<li>Are you sure you have the correct user/password?</li>
			<li>Are you sure that you have typed the correct hostname?</li>
			<li>Are you sure that the database server is running?</li>
			</ul>
			<p><a href='http://wordpress.org/support/'>WordPress Support Forums</a></p>
			</div>");
		}

		$this->select($dbname);
	}

	// ==================================================================
	//	Select a DB (if another one needs to be selected)

	function select($db) {
		if (!@mysql_select_db($db,$this->dbh)) {
			die("
			<p>We're having a little trouble selecting the proper database for WordPress.</p>
			<ul>
			<li>Are you sure it exists?</li>
			<li>Your database name is currently specified as <code>" . DB_NAME ."</code>. Is this correct?</li>
			<li>On some systems the name of your database is prefixed with your username, so it would be like username_wordpress. Could that be the problem?</li>
			</ul>
			<p><a href='http://wordpress.org/support/'>WordPress Support Forums</a></p>");
		}
	}

	// ====================================================================
	//	Format a string correctly for safe insert under all PHP conditions
	
	function escape($str) {
		return mysql_escape_string(stripslashes($str));				
	}

	// ==================================================================
	//	Print SQL/DB error.

	function print_error($str = '') {
		global $EZSQL_ERROR;
		if (!$str) $str = mysql_error();
		$EZSQL_ERROR[] = 
		array ('query' => $this->last_query, 'error_str' => $str);

		// Is error output turned on or not..
		if ( $this->show_errors ) {
			// If there is an error then take note of it
			print "<div id='error'>
			<p><strong>Database error:</strong> [$str]<br />
			<code>$this->last_query</code></p>
			</div>";
		} else {
			return false;	
		}
	}

	// ==================================================================
	//	Turn error handling on or off..

	function show_errors() {
		$this->show_errors = true;
	}
	
	function hide_errors() {
		$this->show_errors = false;
	}

	// ==================================================================
	//	Kill cached query results

	function flush() {
		$this->last_result = null;
		$this->col_info = null;
		$this->last_query = null;
	}

	// ==================================================================
	//	Basic Query	- see docs for more detail

	function query($query) {
		// initialise return
		$return_val = 0;
		$this->flush();

		// Log how the function was called
		$this->func_call = "\$db->query(\"$query\")";

		// Keep track of the last query for debug..
		$this->last_query = $query;

		// Perform the query via std mysql_query function..
		$this->result = @mysql_query($query,$this->dbh);
		++$this->num_queries;
		if (SAVEQUERIES) {
			$this->savedqueries[] = $query;
		}

		// If there is an error then take note of it..
		if ( mysql_error() ) {
			$this->print_error();
			return false;
		}

		if ( preg_match("/^\\s*(insert|delete|update|replace) /i",$query) ) {
			$this->rows_affected = mysql_affected_rows();
			// Take note of the insert_id
			if ( preg_match("/^\\s*(insert|replace) /i",$query) ) {
				$this->insert_id = mysql_insert_id($this->dbh);	
			}
			// Return number of rows affected
			$return_val = $this->rows_affected;
		} else {
			$i = 0;
			while ($i < @mysql_num_fields($this->result)) {
				$this->col_info[$i] = @mysql_fetch_field($this->result);
				$i++;
			}
			$num_rows = 0;
			while ( $row = @mysql_fetch_object($this->result) ) {
				$this->last_result[$num_rows] = $row;
				$num_rows++;
			}

			@mysql_free_result($this->result);

			// Log number of rows the query returned
			$this->num_rows = $num_rows;
			
			// Return number of rows selected
			$return_val = $this->num_rows;
		}

		// If debug ALL queries
		$this->trace || $this->debug_all ? $this->debug() : null ;

		return $return_val;
	}

	// ==================================================================
	//	Get one variable from the DB - see docs for more detail

	function get_var($query=null, $x = 0, $y = 0) {
		$this->func_call = "\$db->get_var(\"$query\",$x,$y)";
		if ( $query )
			$this->query($query);

		// Extract var out of cached results based x,y vals
		if ( $this->last_result[$y] ) {
			$values = array_values(get_object_vars($this->last_result[$y]));
		}

		// If there is a value return it else return null
		return (isset($values[$x]) && $values[$x]!=='') ? $values[$x] : null;
	}

	// ==================================================================
	//	Get one row from the DB - see docs for more detail

	function get_row($query = null, $output = OBJECT, $y = 0) {
		$this->func_call = "\$db->get_row(\"$query\",$output,$y)";
		if ( $query )
			$this->query($query);

		if ( $output == OBJECT ) {
			return $this->last_result[$y] ? $this->last_result[$y] : null;
		} elseif ( $output == ARRAY_A ) {
			return $this->last_result[$y] ? get_object_vars($this->last_result[$y]) : null;
		} elseif ( $output == ARRAY_N ) {
			return $this->last_result[$y] ? array_values(get_object_vars($this->last_result[$y])) : null;
		} else {
			$this->print_error(" \$db->get_row(string query, output type, int offset) -- Output type must be one of: OBJECT, ARRAY_A, ARRAY_N");
		}
	}

	// ==================================================================
	//	Function to get 1 column from the cached result set based in X index
	// se docs for usage and info

	function get_col($query = null , $x = 0) {
		if ( $query )
			$this->query($query);

		// Extract the column values
		for ( $i=0; $i < count($this->last_result); $i++ ) {
			$new_array[$i] = $this->get_var(null, $x, $i);
		}
		return $new_array;
	}

	// ==================================================================
	// Return the the query as a result set - see docs for more details

	function get_results($query = null, $output = OBJECT) {
		$this->func_call = "\$db->get_results(\"$query\", $output)";

		if ( $query )
			$this->query($query);

		// Send back array of objects. Each row is an object
		if ( $output == OBJECT ) {
			return $this->last_result;
		} elseif ( $output == ARRAY_A || $output == ARRAY_N ) {
			if ( $this->last_result ) {
				$i = 0;
				foreach( $this->last_result as $row ) {
					$new_array[$i] = (array) $row;
					if ( $output == ARRAY_N ) {
						$new_array[$i] = array_values($new_array[$i]);
					}
					$i++;
				}
				return $new_array;
			} else {
				return null;
			}
		}
	}


	// ==================================================================
	// Function to get column meta data info pertaining to the last query
	// see docs for more info and usage

	function get_col_info($info_type = 'name', $col_offset = -1) {
		if ( $this->col_info ) {
			if ( $col_offset == -1 ) {
				$i = 0;
				foreach($this->col_info as $col ) {
					$new_array[$i] = $col->{$info_type};
					$i++;
				}
				return $new_array;
			} else {
				return $this->col_info[$col_offset]->{$info_type};
			}
		}
	}
}

$wpdb = new wpdb(DB_USER, DB_PASSWORD, DB_NAME, DB_HOST);

?>