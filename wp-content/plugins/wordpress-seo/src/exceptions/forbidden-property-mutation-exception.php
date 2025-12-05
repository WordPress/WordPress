<?php

namespace Yoast\WP\SEO\Exceptions;

use RuntimeException;

/**
 * Exception for attempting a mutation on properties that are made readonly through magic getters and setters.
 */
class Forbidden_Property_Mutation_Exception extends RuntimeException {

	/**
	 * Creates a Forbidden_Property_Mutation_Exception exception when an attempt is made
	 * to assign a value to an immutable property.
	 *
	 * @param string $property_name The name of the immutable property.
	 *
	 * @return Forbidden_Property_Mutation_Exception The exception.
	 */
	public static function cannot_set_because_property_is_immutable( $property_name ) {
		return new self( \sprintf( 'Setting property $%s is not supported.', $property_name ) );
	}

	/**
	 * Creates a Forbidden_Property_Mutation_Exception exception when an attempt is made to unset an immutable property.
	 *
	 * @param string $property_name The name of the immutable property.
	 *
	 * @return Forbidden_Property_Mutation_Exception The exception.
	 */
	public static function cannot_unset_because_property_is_immutable( $property_name ) {
		return new self( \sprintf( 'Unsetting property $%s is not supported.', $property_name ) );
	}
}
