<?php
/**
 * Matomo - free/libre analytics platform
 *
 * @link https://matomo.org
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 * @package matomo
 */

namespace Piwik\Db\Adapter;

use Zend_Db_Statement;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // if accessed directly
}
/**
 * We want a real data, not something coming from cache
 * phpcs:disable WordPress.DB.DirectDatabaseQuery.NoCaching
 *
 * This is a report error, so silent the possible errors
 * phpcs:disable WordPress.PHP.NoSilencedErrors.Discouraged
 *
 * We cannot use parameters of statements as this is the table names we build
 * phpcs:disable WordPress.DB.DirectDatabaseQuery.DirectQuery
 * phpcs:disable WordPress.DB.PreparedSQL.NotPrepared
 */
class WordPressDbStatement extends Zend_Db_Statement {
	private $result;
	private $sql;

	public function __construct( $adapter, $sql, $result ) {
		$this->result   = $result;
		$this->_adapter = $adapter;
		$this->sql      = $sql;
	}

	public function closeCursor() {
		// not needed
	}

	public function columnCount() {
		return 0;
	}

	public function errorCode() {
		// not needed
	}

	public function errorInfo() {
		// not needed
	}

	public function fetch( $style = null, $cursor = null, $offset = null ) {
		if ( is_array( $this->result ) && ! empty( $this->result ) ) {
			return array_shift( $this->result );
		}

		return $this->result;
	}

	public function nextRowset() {
		// not needed
	}

	public function rowCount() {
		if ( is_array( $this->result ) ) {
			return count( $this->result );
		}

		return $this->result;
	}
}
