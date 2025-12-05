<?php

namespace Contactable\SWV;

class MinFileSizeRule extends Rule {

	const rule_name = 'minfilesize';

	public function matches( $context ) {
		if ( false === parent::matches( $context ) ) {
			return false;
		}

		if ( empty( $context['file'] ) ) {
			return false;
		}

		return true;
	}

	public function validate( $context ) {
		$input = $this->get_default_upload()->size ?? '';
		$input = wpcf7_array_flatten( $input );
		$input = wpcf7_exclude_blank( $input );

		if ( empty( $input ) ) {
			return true;
		}

		$threshold = $this->get_property( 'threshold' );

		if ( array_sum( $input ) < $threshold ) {
			return $this->create_error();
		}

		return true;
	}

}
