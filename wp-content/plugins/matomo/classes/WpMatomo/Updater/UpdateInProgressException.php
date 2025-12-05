<?php
/**
 * Matomo - free/libre analytics platform
 *
 * @link https://matomo.org
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 * @package matomo
 */

namespace WpMatomo\Updater;

use Exception;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // if accessed directly
}

/**
 * phpcs:disable Generic.CodeAnalysis.UselessOverridingMethod.Found
 */
class UpdateInProgressException extends Exception {
	public function __construct( $message = 'Matomo upgrade is already in progress', $code = 0, $previous = null ) {
		parent::__construct( $message, $code, $previous );
	}
}
