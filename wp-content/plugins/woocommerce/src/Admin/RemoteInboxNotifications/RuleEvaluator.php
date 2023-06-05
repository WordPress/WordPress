<?php
/**
 * Evaluate the given rules as an AND operation - return false early if a
 * rule evaluates to false.
 */

namespace Automattic\WooCommerce\Admin\RemoteInboxNotifications;

defined( 'ABSPATH' ) || exit;

/**
 * Evaluate the given rules as an AND operation - return false early if a
 * rule evaluates to false.
 */
class RuleEvaluator {
	/**
	 * Constructor.
	 *
	 * @param GetRuleProcessor $get_rule_processor The GetRuleProcessor to use.
	 */
	public function __construct( $get_rule_processor = null ) {
		$this->get_rule_processor = null === $get_rule_processor
			? new GetRuleProcessor()
			: $get_rule_processor;
	}

	/**
	 * Evaluate the given rules as an AND operation - return false early if a
	 * rule evaluates to false.
	 *
	 * @param array|object $rules The rule or rules being processed.
	 * @param object|null  $stored_state Stored state.
	 * @param array        $logger_args Arguments for the event logger. `slug` is required.
	 *
	 * @throws \InvalidArgumentException Thrown when $logger_args is missing slug.
	 *
	 * @return bool The result of the operation.
	 */
	public function evaluate( $rules, $stored_state = null, $logger_args = array() ) {

		if ( is_bool( $rules ) ) {
			return $rules;
		}

		if ( ! is_array( $rules ) ) {
			$rules = array( $rules );
		}

		if ( 0 === count( $rules ) ) {
			return false;
		}

		$evaluation_logger = null;

		if ( count( $logger_args ) ) {
			if ( ! array_key_exists( 'slug', $logger_args ) ) {
				throw new \InvalidArgumentException( 'Missing required field: slug in $logger_args.' );
			}

			array_key_exists( 'source', $logger_args ) ? $source = $logger_args['source'] : $source = null;

			$evaluation_logger = new EvaluationLogger( $logger_args['slug'], $source );
		}

		foreach ( $rules as $rule ) {
			if ( ! is_object( $rule ) ) {
				return false;
			}

			$processor        = $this->get_rule_processor->get_processor( $rule->type );
			$processor_result = $processor->process( $rule, $stored_state );
			$evaluation_logger && $evaluation_logger->add_result( $rule->type, $processor_result );

			if ( ! $processor_result ) {
				$evaluation_logger && $evaluation_logger->log();
				return false;
			}
		}

		$evaluation_logger && $evaluation_logger->log();

		return true;
	}
}
