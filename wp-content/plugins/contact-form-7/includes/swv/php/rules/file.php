<?php

namespace Contactable\SWV;

class FileRule extends Rule {

	const rule_name = 'file';

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
		$input = $this->get_default_upload()->name ?? '';
		$input = wpcf7_array_flatten( $input );
		$input = wpcf7_exclude_blank( $input );

		$acceptable_filetypes = array();

		foreach ( (array) $this->get_property( 'accept' ) as $accept ) {
			if ( preg_match( '/^\.[a-z0-9]+$/i', $accept ) ) {
				$acceptable_filetypes[] = strtolower( $accept );
			} else {
				foreach ( wpcf7_convert_mime_to_ext( $accept ) as $ext ) {
					$acceptable_filetypes[] = sprintf(
						'.%s',
						strtolower( trim( $ext, ' .' ) )
					);
				}
			}
		}

		$acceptable_filetypes = array_unique( $acceptable_filetypes );

		foreach ( $input as $i ) {
			$last_period_pos = strrpos( $i, '.' );

			if ( false === $last_period_pos ) { // no period
				return $this->create_error();
			}

			$suffix = strtolower( substr( $i, $last_period_pos ) );

			if ( ! in_array( $suffix, $acceptable_filetypes, true ) ) {
				return $this->create_error();
			}
		}

		return true;
	}

}
