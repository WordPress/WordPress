<?php
/**
 * Matomo - free/libre analytics platform
 *
 * @link https://matomo.org
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 * @package matomo
 */

namespace Piwik\Db\Adapter;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // if accessed directly
}

require_once 'WordPressDbStatement.php';
require_once 'WordPressTracker.php';

class WordPress extends Mysqli {

	// needed to be compatbile with mysqli class when `getConnection()` is called and we cannot return the
	// actual connection but return an instance of this.
	public $error = '';

	private $old_suppress_errors_value = null;

	/**
	 * Return default port.
	 *
	 * @return int
	 */
	public static function getDefaultPort() {
		return 3306;
	}

	/**
	 * Returns true if this adapter supports blobs as fields
	 *
	 * @return bool
	 */
	public function hasBlobDataType() {
		return true;
	}

	/**
	 * Returns true if this adapter supports bulk loading
	 *
	 * @return bool
	 */
	public function hasBulkLoader() {
		return false;
	}


	public static function isEnabled() {
		return true;
	}

	public function getConnection() {
		return $this;
	}

	/**
	 * Is the connection character set equal to utf8?
	 *
	 * @return bool
	 */
	public function isConnectionUTF8() {
		$value = $this->fetchOne( 'SELECT @@character_set_client;' );

		return ! empty( $value ) && strpos(strtolower( $value ), 'utf8') === 0;
	}

	public function checkClientVersion() {
		// not implemented as we don't need to check that
	}

	public function getClientVersion() {
		$value = $this->fetchOne( 'SELECT @@version;' );

		return $value;
	}

	public function closeConnection() {
		// we do not want to disconnect WordPress DB ever as it breaks eg the tests where it loses all
		// temporary tables... also we should leave it up to WordPress whether it wants to close db or not
		// global $wpdb;
		// $wpdb->close();
		// if ($this->_connection) {
		// parent::closeConnection();
		// }
	}

	public function lastInsertId( $tableName = null, $primaryKey = null ) {
		global $wpdb;

		if ( empty( $wpdb->insert_id ) ) {
			return $this->fetchOne( 'SELECT LAST_INSERT_ID()' );
		}

		return $wpdb->insert_id;
	}

	public function listTables() {
		global $wpdb;
		$sql = 'SHOW TABLES';

		$tables = $wpdb->get_results( $sql, ARRAY_N );
		$result = [];
		foreach ($tables as $table) {
			$result[] = $table[0];
		}
		return $result;
	}

	public function describeTable($tableName, $schemaName = null)
	{
		global $wpdb;

		if ($schemaName) {
			$sql = 'DESCRIBE ' . $this->quoteIdentifier("$schemaName.$tableName", true);
		} else {
			$sql = 'DESCRIBE ' . $this->quoteIdentifier($tableName, true);
		}

		$result = $wpdb->get_results( $sql, ARRAY_A );

		$desc = array();

		$row_defaults = array(
			'Length'          => null,
			'Scale'           => null,
			'Precision'       => null,
			'Unsigned'        => null,
			'Primary'         => false,
			'PrimaryPosition' => null,
			'Identity'        => false
		);
		$i = 1;
		$p = 1;
		foreach ($result as $key => $row) {
			$row = array_merge($row_defaults, $row);
			if (preg_match('/unsigned/', $row['Type'])) {
				$row['Unsigned'] = true;
			}
			if (preg_match('/^((?:var)?char)\((\d+)\)/', $row['Type'], $matches)) {
				$row['Type'] = $matches[1];
				$row['Length'] = $matches[2];
			} else if (preg_match('/^decimal\((\d+),(\d+)\)/', $row['Type'], $matches)) {
				$row['Type'] = 'decimal';
				$row['Precision'] = $matches[1];
				$row['Scale'] = $matches[2];
			} else if (preg_match('/^float\((\d+),(\d+)\)/', $row['Type'], $matches)) {
				$row['Type'] = 'float';
				$row['Precision'] = $matches[1];
				$row['Scale'] = $matches[2];
			} else if (preg_match('/^((?:big|medium|small|tiny)?int)\((\d+)\)/', $row['Type'], $matches)) {
				$row['Type'] = $matches[1];
				/**
				 * The optional argument of a MySQL int type is not precision
				 * or length; it is only a hint for display width.
				 */
			}
			if (strtoupper($row['Key']) == 'PRI') {
				$row['Primary'] = true;
				$row['PrimaryPosition'] = $p;
				if ($row['Extra'] == 'auto_increment') {
					$row['Identity'] = true;
				} else {
					$row['Identity'] = false;
				}
				++$p;
			}
			$desc[$this->foldCase($row['Field'])] = array(
				'SCHEMA_NAME'      => null, // @todo
				'TABLE_NAME'       => $this->foldCase($tableName),
				'COLUMN_NAME'      => $this->foldCase($row['Field']),
				'COLUMN_POSITION'  => $i,
				'DATA_TYPE'        => $row['Type'],
				'DEFAULT'          => $row['Default'],
				'NULLABLE'         => (bool) ($row['Null'] == 'YES'),
				'LENGTH'           => $row['Length'],
				'SCALE'            => $row['Scale'],
				'PRECISION'        => $row['Precision'],
				'UNSIGNED'         => $row['Unsigned'],
				'PRIMARY'          => $row['Primary'],
				'PRIMARY_POSITION' => $row['PrimaryPosition'],
				'IDENTITY'         => $row['Identity']
			);
			++$i;
		}
		return $desc;
	}

	public function getServerVersion() {
		global $wpdb;

		return $wpdb->db_version();
	}

	private function getErrorNumberFromMessage( $message ) {
		if ( preg_match( '/(?:\[|\s)([0-9]{4})(?:\]|\s)/', $message, $match ) ) {
			return $match[1];
		}
	}

	/**
	 * Test error number
	 *
	 * @param \Exception $e
	 * @param string     $errno
	 *
	 * @return bool
	 */
	public function isErrNo( $e, $errno ) {
		$errorCode = $this->getErrorNumberFromMessage($e->getMessage());
		return !empty($errorCode) && $errorCode == $errno;
	}

	public function rowCount( $queryResult ) {
		return $queryResult->rowCount();
	}

	private function prepareWp( $sql, $bind = array() ) {
		global $wpdb;

		// make sure CREATE TABLE statements includ IF NOT EXISTS
		if ( strpos( $sql, 'CREATE TABLE' ) !== false
			&& strpos( $sql, 'CREATE TABLE IF NOT EXISTS' ) === false
		) {
			$sql = preg_replace( '/CREATE TABLE (?!IF NOT EXISTS)/', 'CREATE TABLE IF NOT EXISTS ', $sql );
		}

		$sql = str_replace( '%', '%%', $sql ); // eg when "value like 'done%'"

		if ( is_array( $bind ) && empty( $bind ) ) {
			return $sql;
		}
		if ( ! is_array( $bind ) ) {
			$bind = array( $bind );
		}

		$null_placeholder = '_#__###NULL###_' . rand(1, PHP_INT_MAX) . ' __#_';
		// random number not really needed but may prevent random issues that someone could somehow inject easily something

		$has_replaced_null = false;

		foreach ($bind as $index => $val) {
			if (is_object($val) && method_exists($val, '__toString')) {
				$bind[$index] = $val->__toString();
			}
			if (is_null($val)) {
				$bind[$index] = $null_placeholder;
				$has_replaced_null = true;
			} elseif (is_string($val) && strpos($val, $null_placeholder) !== false) {
				throw new \Exception('unexpected bind param'); // preventing random injections or something
			}
		}

		$sql = $this->replace_placeholders( $sql );

		$query = $wpdb->prepare( $sql, $bind );

		if ($has_replaced_null) {
			$query = str_replace("'$null_placeholder'", 'NULL', $query);
		}

		return $query;
	}

	public function query( $sql, $bind = array() ) {
		global $wpdb;

		$test_sql = trim( $sql );
		if ( strpos( $test_sql, '/*' ) === 0 ) {
			// remove eg "/* trigger = CronArchive */"
			$startPos = strpos( $test_sql, '*/' );
			$test_sql = substr( $test_sql, $startPos + strlen( '*/' ) );
			$test_sql = trim( $test_sql );
		}

		if (
			preg_match( '/^\s*(select)\s/i', $test_sql )
			|| preg_match( '/\sfor select\s/i', $test_sql )
		) {
			// WordPress does not fetch any result when doing a query w/ a select... it's only supposed to be used for things like
			// insert / update / drop ...
			$result = $this->fetchAll( $sql, $bind );
		} else {
			$prepare = $this->prepareWp( $sql, $bind );

			$this->before_execute_query( $wpdb, $sql );

			$result = $wpdb->query( $prepare );

			$this->after_execute_query( $wpdb, $sql );
		}

		return new WordPressDbStatement( $this, $sql, $result );
	}

	public function exec( $sqlQuery ) {
		global $wpdb;

		$this->before_execute_query( $wpdb, $sqlQuery );

		$exec = $wpdb->query( $sqlQuery );
		$this->after_execute_query( $wpdb, $sqlQuery );

		return $exec;
	}

	public function fetch( $query, $parameters = array() ) {
		return $this->fetchRow( $query, $parameters );
	}

	public function fetchCol( $sql, $bind = array() ) {
		global $wpdb;
		$prepare = $this->prepareWp( $sql, $bind );

		$this->before_execute_query( $wpdb, $sql );

		$col = $wpdb->get_col( $prepare );

		$this->after_execute_query( $wpdb, $sql );

		return $col;
	}

	public function fetchAssoc( $sql, $bind = array() ) {
		global $wpdb;
		$prepare = $this->prepareWp( $sql, $bind );

		$this->before_execute_query( $wpdb, $sql );

		$assoc = $wpdb->get_results( $prepare, ARRAY_A );

		$this->after_execute_query( $wpdb, $sql );

		return $assoc;
	}

	/**
	 * @param \wpdb $wpdb
	 *
	 * @throws \Zend_Db_Statement_Exception
	 */
	private function before_execute_query( $wpdb, $sql ) {
		if ( ! $wpdb->suppress_errors ) {
			if ( defined( 'MATOMO_SUPPRESS_DB_ERRORS' ) ) {
				// allow users to always suppress or never suppress
				if ( MATOMO_SUPPRESS_DB_ERRORS === true ) {
					$this->old_suppress_errors_value = $wpdb->suppress_errors( true );
				}

				return;
			}

			if ( defined( 'WP_DEBUG' )
			     && WP_DEBUG
			     && defined( 'WP_DEBUG_DISPLAY' )
			     && WP_DEBUG_DISPLAY
			     && ! is_admin() ) {
				// prevent showing some notices in frontend eg if cronjob runs there

				$is_likely_dedicated_cron = defined( 'DOING_CRON' ) && DOING_CRON && defined( 'DISABLE_WP_CRON' ) && DISABLE_WP_CRON;
				if ( ! $is_likely_dedicated_cron ) {
					// if cron is triggered through wp-cron.php, then we should maybe not suppress!
					$this->old_suppress_errors_value = $wpdb->suppress_errors( true );

					return;
				}
			}

			if ( ( stripos( $sql, '/* WP IGNORE ERROR */' ) !== false  )
			     || stripos( $sql, 'SELECT @@TX_ISOLATION' ) !== false
			     || stripos( $sql, 'SELECT @@transaction_isolation' ) !== false ) {
				// prevent notices for queries that are expected to fail
				// SELECT 1 FROM wp_matomo_logtmpsegment1cc77bce7a13181081e44ea6ffc0a9fd LIMIT 1 => runs to detect if temp table exists or not and regularly the query fails which is expected
				// SELECT @@TX_ISOLATION => not available in all mysql versions
				// SELECT @@transaction_isolation => not available in all mysql versions
				// we show notices only in admin...
				$this->old_suppress_errors_value = $wpdb->suppress_errors( true );

				return;
			}
		}
	}

	/**
	 * @param \wpdb $wpdb
	 *
	 * @throws \Zend_Db_Statement_Exception
	 */
	private function after_execute_query( $wpdb, $sql ) {
		$lastError = $wpdb->last_error;

		if ( $lastError && !$this->getErrorNumberFromMessage($lastError) ) {
			// see #174 mysqli message usually doesn't include the error code so we need to add it for isErrNo to work
			// we want to execute this while errors are suppressed
			$row = $wpdb->get_row('SHOW ERRORS', ARRAY_A);
			if (!empty($row['Code'])) {
				$lastError = '['.$row['Code'].'] ' . $lastError;
			}
		}

		if ( isset( $this->old_suppress_errors_value ) ) {
			$wpdb->suppress_errors( $this->old_suppress_errors_value );
			$this->old_suppress_errors_value = null;
		}

		if ( $lastError ) {
			$message = 'WP DB Error: ' . $lastError;
			if ( $sql ) {
				$message .= ' SQL: ' . $sql;
			}
			throw new \Zend_Db_Statement_Exception( $message );
		}
	}

	public function fetchAll( $sql, $bind = array(), $fetchMode = null ) {
		global $wpdb;
		$prepare = $this->prepareWp( $sql, $bind );

		$this->before_execute_query( $wpdb, $sql );

		$results = $wpdb->get_results( $prepare, ARRAY_A );

		$this->after_execute_query( $wpdb, $sql );

		return $results;
	}

	public function fetchOne( $sql, $bind = array() ) {
		global $wpdb;
		$prepare = $this->prepareWp( $sql, $bind );

		$this->before_execute_query( $wpdb, $sql );

		$value = $wpdb->get_var( $prepare );

		$this->after_execute_query( $wpdb, $sql );

		if ( $value === null ) {
			return false; // make sure to behave same way as matomo
		}

		return $value;
	}

	public function fetchRow( $sql, $bind = array(), $fetchMode = null ) {
		global $wpdb;
		$prepare = $this->prepareWp( $sql, $bind );

		$this->before_execute_query( $wpdb, $sql );

		$row = $wpdb->get_row( $prepare, ARRAY_A );

		$this->after_execute_query( $wpdb, $sql );

		return $row;
	}

	public function insert( $table, array $bind ) {
		global $wpdb;

		$this->before_execute_query( $wpdb, '' );

		$insert = $wpdb->insert( $table, $bind );

		$this->after_execute_query( $wpdb, '' );

		return $insert;
	}

	public function update( $table, array $bind, $where = '' ) {
		global $wpdb;

		$fields = array();
		foreach ( $bind as $field => $val ) {
			// wpdb's prepare doesn't seem to handle null values correctly. they are set to `''`
			// in some cases, so we handle this explicitly here.
			if ($val === null) {
				unset($bind[$field]);
				$fields[] = "`$field` = NULL";
			} else {
				$fields[] = "`$field` = %s";
			}
		}
		$fields = implode( ', ', $fields );

		$sql = "UPDATE `$table` SET $fields " . ( ( $where ) ? " WHERE $where" : '' );

		if ( empty( $bind ) ) {
			$prepared = $sql;
		} else {
			$prepared = $wpdb->prepare( $sql, $bind );
		}

		$this->before_execute_query( $wpdb, '' );

		$update = $wpdb->query( $prepared );

		$this->after_execute_query( $wpdb, '' );

		return $update;
	}

	// public for tests
	public function replace_placeholders( $sql ) {
		$replaced = '';

		$i = 0;
		while ( $i < strlen( $sql ) ) {
			if ( $this->is_string_literal_start( $sql[$i] ) ) {
				$quote = $sql[$i];

				$segment_end = $i + 1;
				while ( $segment_end < strlen( $sql ) ) {
					if ( $sql[ $segment_end ] === $quote ) {
						// '' or ""
						$is_double_quote = $segment_end + 1 < strlen( $sql )
							&& $sql[ $segment_end + 1 ] === $quote;

						if ( $is_double_quote ) {
							++$segment_end;
						} else {
							break; // not double quote, end of string literal
						}
					}

					++$segment_end;
				}

				++$segment_end; // advance past end quote

				$replaced .= substr( $sql, $i, $segment_end - $i );
			} else {
				// advance until string literal or end of string
				$segment_end = $i + 1;
				while ( $segment_end < strlen( $sql ) && ! $this->is_string_literal_start( $sql[$segment_end] ) ) {
					++$segment_end;
				}

				$segment = substr( $sql, $i, $segment_end - $i );
				$segment = str_replace( '?', '%s', $segment );

				$replaced .= $segment;
			}

			$i = $segment_end;
		}

		return $replaced;
	}

	private function is_string_literal_start( $s ) {
		return $s === '\'' || $s === '"';
	}
}
