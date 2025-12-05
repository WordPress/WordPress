<?php

namespace Contactable\SWV;

class AllRule extends CompositeRule {

	const rule_name = 'all';

	public function matches( $context ) {
		if ( false === parent::matches( $context ) ) {
			return false;
		}

		return true;
	}

	public function validate( $context ) {
		foreach ( $this->rules() as $rule ) {
			if ( $rule->matches( $context ) ) {
				$result = $rule->validate( $context );

				if ( is_wp_error( $result ) ) {
					if ( $result->get_error_message() ) {
						return $result;
					} else {
						return $this->create_error();
					}
				}
			}
		}

		return true;
	}

}
