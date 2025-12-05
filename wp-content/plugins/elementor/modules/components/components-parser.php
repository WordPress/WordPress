<?php

namespace Elementor\Modules\Components;

use Elementor\Core\Utils\Api\Parse_Result;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class Components_Parser {
	const MIN_NAME_LENGTH = 2;
	const MAX_NAME_LENGTH = 50;

	public static function make() {
		return new static();
	}

	public function parse_name( $name, $existing_components_names ): Parse_Result {
		$result = Parse_Result::make();

		$sanitized = trim( sanitize_text_field( $name ) );

		if ( strlen( $sanitized ) < self::MIN_NAME_LENGTH ) {
			$result->errors()->add( 'name', 'component_name_too_short_min_' . self::MIN_NAME_LENGTH );
		}

		if ( strlen( $sanitized ) > self::MAX_NAME_LENGTH ) {
			$result->errors()->add( 'name', 'component_name_too_long_max_' . self::MAX_NAME_LENGTH );
		}

		if ( in_array( $sanitized, $existing_components_names, true ) ) {
			$result->errors()->add( 'name', 'duplicated_component_name' );
		}

		return $result->wrap( $sanitized );
	}
}
