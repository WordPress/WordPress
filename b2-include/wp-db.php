<?php
	// ==================================================================
	//  Author: Justin Vincent (justin@visunet.ie)
	//	Web: 	http://php.justinvincent.com
	//	Name: 	ezSQL
	// 	Desc: 	Class to make it very easy to deal with mySQL database connections.
	//  WordPress is using this class to make the code cleaner and faster.
	//  We highly recommend it.
	//  We have modified the HTML it returns slightly.

	define('EZSQL_DB_USER', "");			// <-- mysql db user
	define('EZSQL_DB_PASSWORD', "");		// <-- mysql db password
	define('EZSQL_DB_NAME', "mysql");		// <-- mysql db pname
	define('EZSQL_DB_HOST', 'localhost');	// <-- mysql server host

	define('EZSQL_VERSION', '1.21');
	define('OBJECT', 'OBJECT', true);
	define('ARRAY_A', 'ARRAY_A', true);
	define('ARRAY_N', 'ARRAY_N', true);

	//	The Main Class, renamed to avoid conflicts.

	class wpdb {

		var $debug_called;
		var $vardump_called;
		var $show_errors = true;

		// ==================================================================
		//	DB Constructor - connects to the server and selects a database

		function wpdb($dbuser, $dbpassword, $dbname, $dbhost)
		{

			$this->dbh = @mysql_connect($dbhost,$dbuser,$dbpassword);

			if ( ! $this->dbh )
			{
				$this->print_error("<ol id='error'>
				<li><strong>Error establishing a database connection!</strong></li>
				<li>Are you sure you have the correct user/password?</li>
				<li>Are you sure that you have typed the correct hostname?</li>
				<li>Are you sure that the database server is running?</li>
				</ol>");
			}


			$this->select($dbname);

		}

		// ==================================================================
		//	Select a DB (if another one needs to be selected)

		function select($db)
		{
			if ( !@mysql_select_db($db,$this->dbh))
			{
				$this->print_error("<ol id='error'>
				<li><strong>Error selecting database <u>$db</u>!</strong></li>
				<li>Are you sure it exists?</li>
				<li>Are you sure there is a valid database connection?</li>
				</ol>");
			}
		}

		// ====================================================================
		//	Format a string correctly for safe insert under all PHP conditions
		
		function escape($str)
		{
			return mysql_escape_string(stripslashes($str));				
		}

		// ==================================================================
		//	Print SQL/DB error.

		function print_error($str = '')
		{
			
			// All errors go to the global error array $EZSQL_ERROR..
			global $EZSQL_ERROR;

			// If no special error string then use mysql default..
			if ( !$str ) $str = mysql_error();
			
			// Log this error to the global array..
			$EZSQL_ERROR[] = array 
							(
								'query' => $this->last_query,
								'error_str'  => $str
							);

			// Is error output turned on or not..
			if ( $this->show_errors )
			{
				// If there is an error then take note of it
				print "<ol id='error'>
				<li><strong>SQL/DB Error --</strong></li>
				<li>[<font color=000077>$str</font>]</li>
				</ol>";
			}
			else
			{
				return false;	
			}

		}

		// ==================================================================
		//	Turn error handling on or off..

		function show_errors()
		{
			$this->show_errors = true;
		}
		
		function hide_errors()
		{
			$this->show_errors = false;
		}

		// ==================================================================
		//	Kill cached query results

		function flush()
		{

			// Get rid of these
			$this->last_result = null;
			$this->col_info = null;
			$this->last_query = null;

		}

		// ==================================================================
		//	Basic Query	- see docs for more detail

		function query($query)
		{

			// Flush cached values..
			$this->flush();

			// Log how the function was called
			$this->func_call = "\$db->query(\"$query\")";

			// Keep track of the last query for debug..
			$this->last_query = $query;

			// Perform the query via std mysql_query function..
			$this->result = mysql_query($query, $this->dbh);

			// If there was an insert, delete or update see how many rows were affected
			// (Also, If there there was an insert take note of the insert_id
			$query_type = array('insert','delete','update','replace');

			// loop through the above array
			foreach ( $query_type as $word )
			{
				// This is true if the query starts with insert, delete or update
				if ( preg_match("/^\\s*$word /i",$query) )
				{
					$this->rows_affected = mysql_affected_rows();
					
					// This gets the insert ID
					if ( $word == 'insert' || $word == 'replace' )
					{
						$this->insert_id = mysql_insert_id($this->dbh);
					}
					
					$this->result = false;
				}
				
			}
   
			if ( mysql_error() )
			{

				// If there is an error then take note of it..
				$this->print_error();

			}
			else
			{

				// In other words if this was a select statement..
				if ( $this->result )
				{

					// =======================================================
					// Take note of column info

					$i=0;
					while ($i < @mysql_num_fields($this->result))
					{
						$this->col_info[$i] = @mysql_fetch_field($this->result);
						$i++;
					}

					// =======================================================
					// Store Query Results

					$i=0;
					while ( $row = @mysql_fetch_object($this->result) )
					{

						// Store relults as an objects within main array
						$this->last_result[$i] = $row;

						$i++;
					}

					// Log number of rows the query returned
					$this->num_rows = $i;

					@mysql_free_result($this->result);


					// If there were results then return true for $db->query
					if ( $i )
					{
						return true;
					}
					else
					{
						return false;
					}

				}
				else
				{
					// Update insert etc. was good..
					return true;
				}
			}
		}

		// ==================================================================
		//	Get one variable from the DB - see docs for more detail

		function get_var($query=null, $x=0, $y=0)
		{

			// Log how the function was called
			$this->func_call = "\$db->get_var(\"$query\",$x,$y)";

			// If there is a query then perform it if not then use cached results..
			if ( $query )
			{
				$this->query($query);
			}

			// Extract var out of cached results based x,y vals
			if ( $this->last_result[$y] )
			{
				$values = array_values(get_object_vars($this->last_result[$y]));
			}

			// If there is a value return it else return null
			return (isset($values[$x]) && $values[$x]!=='')?$values[$x]:null;
		}

		// ==================================================================
		//	Get one row from the DB - see docs for more detail

		function get_row($query=null, $output=OBJECT, $y=0)
		{

			// Log how the function was called
			$this->func_call = "\$db->get_row(\"$query\",$output,$y)";

			// If there is a query then perform it if not then use cached results..
			if ( $query )
			{
				$this->query($query);
			}

			// If the output is an object then return object using the row offset..
			if ( $output == OBJECT )
			{
				return $this->last_result[$y]?$this->last_result[$y]:null;
			}
			// If the output is an associative array then return row as such..
			elseif ( $output == ARRAY_A )
			{
				return $this->last_result[$y]?get_object_vars($this->last_result[$y]):null;
			}
			// If the output is an numerical array then return row as such..
			elseif ( $output == ARRAY_N )
			{
				return $this->last_result[$y]?array_values(get_object_vars($this->last_result[$y])):null;
			}
			// If invalid output type was specified..
			else
			{
				$this->print_error(" \$db->get_row(string query, output type, int offset) -- Output type must be one of: OBJECT, ARRAY_A, ARRAY_N");
			}

		}

		// ==================================================================
		//	Function to get 1 column from the cached result set based in X index
		// se docs for usage and info

		function get_col($query=null,$x=0)
		{

			// If there is a query then perform it if not then use cached results..
			if ( $query )
			{
				$this->query($query);
			}

			// Extract the column values
			for ( $i=0; $i < count($this->last_result); $i++ )
			{
				$new_array[$i] = $this->get_var(null,$x,$i);
			}

			return $new_array;
		}

		// ==================================================================
		// Return the the query as a result set - see docs for more details

		function get_results($query=null, $output = OBJECT)
		{

			// Log how the function was called
			$this->func_call = "\$db->get_results(\"$query\", $output)";

			// If there is a query then perform it if not then use cached results..
			if ( $query )
			{
				$this->query($query);
			}

			// Send back array of objects. Each row is an object
			if ( $output == OBJECT )
			{
				return $this->last_result;
			}
			elseif ( $output == ARRAY_A || $output == ARRAY_N )
			{
				if ( $this->last_result )
				{
					$i=0;
					foreach( $this->last_result as $row )
					{

						$new_array[$i] = get_object_vars($row);

						if ( $output == ARRAY_N )
						{
							$new_array[$i] = array_values($new_array[$i]);
						}

						$i++;
					}

					return $new_array;
				}
				else
				{
					return null;
				}
			}
		}


		// ==================================================================
		// Function to get column meta data info pertaining to the last query
		// see docs for more info and usage

		function get_col_info($info_type='name', $col_offset=-1)
		{

			if ( $this->col_info )
			{
				if ( $col_offset == -1 )
				{
					$i=0;
					foreach($this->col_info as $col )
					{
						$new_array[$i] = $col->{$info_type};
						$i++;
					}
					return $new_array;
				}
				else
				{
					return $this->col_info[$col_offset]->{$info_type};
				}

			}

		}

	}

$wpdb = new wpdb(EZSQL_DB_USER, EZSQL_DB_PASSWORD, EZSQL_DB_NAME, EZSQL_DB_HOST);

?>