<?php

namespace Contactable\SWV;

class EnumRule extends Rule {

	const rule_name = 'enum';

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

		$acceptable_values = (array) $this->get_property( 'accept' );

		$acceptable_values = array_map(
			static function ( $val ) {
				$val = strval( $val );
				$val = wpcf7_normalize_newline( $val );
				return $val;
			},
			$acceptable_values
		);

		$acceptable_values = array_unique( $acceptable_values );

		$acceptable_values = array_filter( $acceptable_values,
			static function ( $val ) {
				return '' !== $val;
			}
		);

		foreach ( $input as $i ) {
			$i = wpcf7_normalize_newline( $i );

			if ( ! in_array( $i, $acceptable_values, true ) ) {
				return $this->create_error();
			}
		}

		return true;
	}

}
