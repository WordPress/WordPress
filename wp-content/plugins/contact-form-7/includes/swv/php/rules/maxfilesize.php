<?php

namespace Contactable\SWV;

class MaxFileSizeRule extends Rule {

	const rule_name = 'maxfilesize';

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

		if ( $threshold < array_sum( $input ) ) {
			return $this->create_error();
		}

		return true;
	}

}
