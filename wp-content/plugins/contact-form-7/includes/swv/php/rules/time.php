<?php

namespace Contactable\SWV;

class TimeRule extends Rule {

	const rule_name = 'time';

	public function matches( $context ) {
		if ( false === parent::matches( $context ) ) {
			return false;
		}

		if ( empty( $context['text'] ) ) {
			return false;
		}

		return true;
	}

	public function validate( $context ) {
		$input = $this->get_default_input();
		$input = wpcf7_array_flatten( $input );
		$input = wpcf7_strip_whitespaces( $input );
		$input = wpcf7_exclude_blank( $input );

		foreach ( $input as $i ) {
			if ( ! wpcf7_is_time( $i ) ) {
				return $this->create_error();
			}
		}

		return true;
	}

}
