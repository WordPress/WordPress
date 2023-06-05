<?php
/**
 * Compare two operands using the specified operation.
 */

namespace Automattic\WooCommerce\Admin\RemoteInboxNotifications;

defined( 'ABSPATH' ) || exit;

/**
 * Compare two operands using the specified operation.
 */
class ComparisonOperation {
	/**
	 * Compare two operands using the specified operation.
	 *
	 * @param object $left_operand  The left hand operand.
	 * @param object $right_operand The right hand operand.
	 * @param string $operation     The operation used to compare the operands.
	 */
	public static function compare( $left_operand, $right_operand, $operation ) {
		switch ( $operation ) {
			case '=':
				return $left_operand === $right_operand;
			case '<':
				return $left_operand < $right_operand;
			case '<=':
				return $left_operand <= $right_operand;
			case '>':
				return $left_operand > $right_operand;
			case '>=':
				return $left_operand >= $right_operand;
			case '!=':
				return $left_operand !== $right_operand;
			case 'contains':
				return in_array( $right_operand, $left_operand, true );
			case '!contains':
				return ! in_array( $right_operand, $left_operand, true );
		}

		return false;
	}
}
