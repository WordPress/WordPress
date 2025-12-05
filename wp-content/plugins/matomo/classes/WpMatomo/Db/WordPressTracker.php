<?php
/**
 * Matomo - free/libre analytics platform
 *
 * @link https://matomo.org
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 * @package matomo
 */

namespace Piwik\Tracker\Db;

use Piwik\Db\Adapter\WordPressDbStatement;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // if accessed directly
}

class WordPress extends Mysqli {

	private $old_suppress_errors_value = null;

	public function disconnect() {
		// we do not want to disconnect WordPress DB ever as it breaks eg the tests where it loses all
		// temporary tables... also we should leave it up to WordPress whether it wants to close db or not
		// global $wpdb;
		// $wpdb->close();
		// if ($this->connection) {
		// parent::disconnect();
		// }
	}

	public function connect() {
		// do not connect to DB
	}

	public function lastInsertId( $tableName = null, $primaryKey = null ) {
		global $wpdb;

		if ( empty( $wpdb->insert_id ) ) {
			return $this->fetchOne( 'SELECT LAST_INSERT_ID()' );
		}

		return $wpdb->insert_id;
	}

	/**
	 * @param \wpdb $wpdb
	 *
	 * @throws \Zend_Db_Statement_Exception
	 */
	private function after_execute_query( $wpdb ) {
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
			throw new \Zend_Db_Statement_Exception( $lastError );
		}
	}

	/**
	 * @param \wpdb $wpdb
	 * @param $sql
	 */
	private function before_execute_query( $wpdb, $sql ) {
		if ( ! $wpdb->suppress_errors
		     && defined( 'WP_DEBUG' )
		     && WP_DEBUG
		     && defined( 'WP_DEBUG_DISPLAY' )
		     && WP_DEBUG_DISPLAY ) {
			// we want to prevent showing these notices
			if ( defined( 'MATOMO_SUPPRESS_DB_ERRORS' ) ) {
				if ( MATOMO_SUPPRESS_DB_ERRORS === true ) {
					$this->old_suppress_errors_value = $wpdb->suppress_errors( true );
				}

				// any other value than false and we will not supproess
				return;
			}

			$this->old_suppress_errors_value = $wpdb->suppress_errors( true );
			return;
		}

		if ( ! $wpdb->suppress_errors ) {
			if ( ( stripos( $sql, '/* WP IGNORE ERROR */' ) !== false  )
			     || stripos( $sql, 'SELECT @@TX_ISOLATION' ) !== false
			     || stripos( $sql, 'SELECT @@transaction_isolation' ) !== false ) {
				// see {@link WordPress::before_execute_query() }
				$this->old_suppress_errors_value = $wpdb->suppress_errors( true );

				return;
			}
		}
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

		// fix some queries
		$sql = str_replace( '%', '%%', $sql ); // eg when "value like 'done%'"

		if ( is_array( $bind ) && empty( $bind ) ) {
			return $sql;
		}
		if ( ! is_array( $bind ) ) {
			$bind = array( $bind );
		}

		$has_replaced_null = false;
		$null_placeholder = '_#__###NULL###_' . rand(1, PHP_INT_MAX) . ' __#_';
		// random number not really needed but may prevent random issues that someone could somehow inject easily something

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

		$sql = str_replace( '?', '%s', $sql );

		$query = $wpdb->prepare( $sql, $bind );

		if ($has_replaced_null) {
			$query = str_replace("'$null_placeholder'", 'NULL', $query);
		}

		return $query;
	}

	public function query( $query, $parameters = array() ) {
		global $wpdb;

		$test_query = trim( $query );
		if ( strpos( $test_query, '/*' ) === 0 ) {
			// remove eg "/* trigger = CronArchive */"
			$startPos   = strpos( $test_query, '*/' );
			$test_query = substr( $test_query, $startPos + strlen( '*/' ) );
			$test_query = trim( $test_query );
		}

		if ( preg_match( '/^\s*(select)\s/i', $test_query ) ) {
			// WordPress does not fetch any result when doing a select... it's only supposed to be used for things like
			// insert / update / drop ...
			$result = $this->fetchAll( $query, $parameters );
		} else {
			$query = $this->prepareWp( $query, $parameters );
			$this->before_execute_query( $wpdb, $query );
			$result = $wpdb->query( $query );
			$this->after_execute_query( $wpdb );
		}

		return new WordPressDbStatement( $this, $query, $result );
	}

	public function beginTransaction() {
		global $wpdb;
		if ( ! $this->activeTransaction === false ) {
			return;
		}

		$wpdb->query( 'START TRANSACTION' );
		$this->activeTransaction = uniqid();

		return $this->activeTransaction;
	}

	/**
	 * Commit Transaction
	 *
	 * @param $xid
	 *
	 * @throws DbException
	 * @internal param TransactionID $string from beginTransaction
	 */
	public function commit( $xid ) {
		global $wpdb;

		if ( $this->activeTransaction != $xid || $this->activeTransaction === false ) {
			return;
		}

		$this->activeTransaction = false;

		$wpdb->query( 'COMMIT' );
	}

	/**
	 * Rollback Transaction
	 *
	 * @param $xid
	 *
	 * @throws DbException
	 * @internal param TransactionID $string from beginTransaction
	 */
	public function rollBack( $xid ) {
		global $wpdb;

		if ( $this->activeTransaction != $xid || $this->activeTransaction === false ) {
			return;
		}

		$this->activeTransaction = false;

		$wpdb->query( 'ROLLBACK' );
	}

	public function fetch( $query, $parameters = array() ) {
		global $wpdb;
		$prepare = $this->prepareWp( $query, $parameters );

		$this->before_execute_query( $wpdb, $query );

		$row = $wpdb->get_row( $prepare, ARRAY_A );

		$this->after_execute_query( $wpdb );

		return $row;
	}

	public function fetchAll( $query, $parameters = array() ) {
		global $wpdb;
		$prepare = $this->prepareWp( $query, $parameters );

		$this->before_execute_query( $wpdb, $query );

		$results = $wpdb->get_results( $prepare, ARRAY_A );

		$this->after_execute_query( $wpdb );

		return $results;
	}


}
