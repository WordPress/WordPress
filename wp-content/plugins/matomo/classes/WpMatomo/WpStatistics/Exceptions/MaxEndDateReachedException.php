<?php
namespace WpMatomo\WpStatistics\Exceptions;

/**
 * @package WpMatomo
 * @subpackage WpStatisticsImport
 */
class MaxEndDateReachedException extends \RuntimeException {

	public function __construct() {
		parent::__construct( 'Max end date reached.' );
	}
}
