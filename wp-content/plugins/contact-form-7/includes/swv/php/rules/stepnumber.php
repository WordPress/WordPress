<?php

namespace Contactable\SWV;

class StepNumberRule extends Rule {

	const rule_name = 'stepnumber';

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

		$base = floatval( $this->get_property( 'base' ) );
		$interval = floatval( $this->get_property( 'interval' ) );

		if ( ! ( 0 < $interval ) ) {
			return true;
		}

		foreach ( $input as $i ) {
			$remainder = fmod( floatval( $i ) - $base, $interval );

			if (
				0.0 === round( abs( $remainder ), 6 ) or
				0.0 === round( abs( $remainder - $interval ), 6 )
			) {
				continue;
			}

			return $this->create_error();
		}

		return true;
	}

}
