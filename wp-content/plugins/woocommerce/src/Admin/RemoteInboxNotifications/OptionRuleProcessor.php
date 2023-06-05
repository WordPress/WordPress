<?php
/**
 * Rule processor that performs a comparison operation against an option value.
 */

namespace Automattic\WooCommerce\Admin\RemoteInboxNotifications;

defined( 'ABSPATH' ) || exit;

/**
 * Rule processor that performs a comparison operation against an option value.
 */
class OptionRuleProcessor implements RuleProcessorInterface {
	/**
	 * Performs a comparison operation against the option value.
	 *
	 * @param object $rule         The specific rule being processed by this rule processor.
	 * @param object $stored_state Stored state.
	 *
	 * @return bool The result of the operation.
	 */
	public function process( $rule, $stored_state ) {
		$is_contains   = $rule->operation && strpos( $rule->operation, 'contains' ) !== false;
		$default_value = $is_contains ? array() : false;
		$default       = isset( $rule->default ) ? $rule->default : $default_value;
		$option_value  = get_option( $rule->option_name, $default );

		if ( $is_contains && ! is_array( $option_value ) ) {
			$logger = wc_get_logger();
			$logger->warning(
				sprintf(
					'ComparisonOperation "%s" option value "%s" is not an array, defaulting to empty array.',
					$rule->operation,
					$rule->option_name
				),
				array(
					'option_value' => $option_value,
					'rule'         => $rule,
				)
			);
			$option_value = array();
		}

		if ( isset( $rule->transformers ) && is_array( $rule->transformers ) ) {
			$option_value = TransformerService::apply( $option_value, $rule->transformers, $default );
		}

		return ComparisonOperation::compare(
			$option_value,
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
		if ( ! isset( $rule->option_name ) ) {
			return false;
		}

		if ( ! isset( $rule->value ) ) {
			return false;
		}

		if ( ! isset( $rule->operation ) ) {
			return false;
		}

		if ( isset( $rule->transformers ) && is_array( $rule->transformers ) ) {
			foreach ( $rule->transformers as $transform_args ) {
				$transformer = TransformerService::create_transformer( $transform_args->use );
				if ( ! $transformer->validate( $transform_args->arguments ) ) {
					return false;
				}
			}
		}

		return true;
	}
}
