<?php
/**
 * Rule processor that passes when a store's payments volume exceeds a provided amount.
 */

namespace Automattic\WooCommerce\Admin\RemoteInboxNotifications;

defined( 'ABSPATH' ) || exit;

use Automattic\WooCommerce\Admin\API\Reports\Revenue\Query as RevenueQuery;
use Automattic\WooCommerce\Admin\API\Reports\TimeInterval;

/**
 * Rule processor that passes when a store's payments volume exceeds a provided amount.
 */
class TotalPaymentsVolumeProcessor implements RuleProcessorInterface {
	/**
	 * Compare against the store's total payments volume.
	 *
	 * @param object $rule         The rule being processed by this rule processor.
	 * @param object $stored_state Stored state.
	 *
	 * @return bool The result of the operation.
	 */
	public function process( $rule, $stored_state ) {
		$dates      = TimeInterval::get_timeframe_dates( $rule->timeframe );
		$reports_revenue = new RevenueQuery(
			array(
				'before' => $dates['end'],
				'after'  => $dates['start'],
				'interval' => 'year',
				'fields' => array( 'total_sales' ),
			)
		);
		$report_data    = $reports_revenue->get_data();
		$value          = $report_data->totals->total_sales;

		return ComparisonOperation::compare(
			$value,
			$rule->value,
			$rule->operation
		);
	}

	/**
	 * Validates the rule.
	 *
	 * @param object $rule The rule to validate.
	 *
	 * @return bool Pass/fail.
	 */
	public function validate( $rule ) {
		$allowed_timeframes = array(
			'last_week',
			'last_month',
			'last_quarter',
			'last_6_months',
			'last_year',
		);

		if ( ! isset( $rule->timeframe ) || ! in_array( $rule->timeframe, $allowed_timeframes, true ) ) {
			return false;
		}

		if ( ! isset( $rule->value ) ) {
			return false;
		}

		if ( ! isset( $rule->operation ) ) {
			return false;
		}

		return true;
	}
}
