<?php
/**
 * Rule processor that negates the rules in the rule's operand.
 */

namespace Automattic\WooCommerce\Admin\RemoteInboxNotifications;

defined( 'ABSPATH' ) || exit;

/**
 * Rule processor that negates the rules in the rule's operand.
 */
class NotRuleProcessor implements RuleProcessorInterface {
	/**
	 * Constructor.
	 *
	 * @param RuleEvaluator $rule_evaluator The rule evaluator to use.
	 */
	public function __construct( $rule_evaluator = null ) {
		$this->rule_evaluator = null === $rule_evaluator
			? new RuleEvaluator()
			: $rule_evaluator;
	}

	/**
	 * Evaluates the rules in the operand and negates the result.
	 *
	 * @param object $rule         The specific rule being processed by this rule processor.
	 * @param object $stored_state Stored state.
	 *
	 * @return bool The result of the operation.
	 */
	public function process( $rule, $stored_state ) {
		$evaluated_operand = $this->rule_evaluator->evaluate(
			$rule->operand,
			$stored_state
		);

		return ! $evaluated_operand;
	}

	/**
	 * Validates the rule.
	 *
	 * @param object $rule The rule to validate.
	 *
	 * @return bool Pass/fail.
	 */
	public function validate( $rule ) {
		if ( ! isset( $rule->operand ) ) {
			return false;
		}

		return true;
	}
}
