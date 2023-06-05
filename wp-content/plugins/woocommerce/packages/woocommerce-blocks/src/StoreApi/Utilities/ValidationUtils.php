<?php
namespace Automattic\WooCommerce\StoreApi\Utilities;

/**
 * ValidationUtils class.
 * Helper class which validates and update customer info.
 */
class ValidationUtils {
	/**
	 * Get list of states for a country.
	 *
	 * @param string $country Country code.
	 * @return array Array of state names indexed by state keys.
	 */
	public function get_states_for_country( $country ) {
		return $country ? array_filter( (array) \wc()->countries->get_states( $country ) ) : [];
	}

	/**
	 * Validate provided state against a countries list of defined states.
	 *
	 * If there are no defined states for a country, any given state is valid.
	 *
	 * @param string $state State name or code (sanitized).
	 * @param string $country Country code.
	 * @return boolean Valid or not valid.
	 */
	public function validate_state( $state, $country ) {
		$states = $this->get_states_for_country( $country );

		if ( count( $states ) && ! in_array( \wc_strtoupper( $state ), array_map( '\wc_strtoupper', array_keys( $states ) ), true ) ) {
			return false;
		}

		return true;
	}


	/**
	 * Format a state based on the country. If country has defined states, will return a valid upper case state code.
	 *
	 * @param string $state State name or code (sanitized).
	 * @param string $country Country code.
	 * @return string
	 */
	public function format_state( $state, $country ) {
		$states = $this->get_states_for_country( $country );

		if ( count( $states ) ) {
			$state        = \wc_strtoupper( $state );
			$state_values = array_map( '\wc_strtoupper', array_flip( array_map( '\wc_strtoupper', $states ) ) );

			if ( isset( $state_values[ $state ] ) ) {
				// Convert to state code if a state name was provided.
				return $state_values[ $state ];
			}
		}

		return $state;
	}
}
