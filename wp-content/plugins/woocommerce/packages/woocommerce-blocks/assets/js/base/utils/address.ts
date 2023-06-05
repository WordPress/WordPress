/**
 * External dependencies
 */
import prepareAddressFields from '@woocommerce/base-components/cart-checkout/address-form/prepare-address-fields';
import { isEmail } from '@wordpress/url';
import type {
	CartResponseBillingAddress,
	CartResponseShippingAddress,
} from '@woocommerce/types';
import {
	defaultAddressFields,
	ShippingAddress,
	BillingAddress,
	getSetting,
} from '@woocommerce/settings';
import { decodeEntities } from '@wordpress/html-entities';

/**
 * Compare two addresses and see if they are the same.
 */
export const isSameAddress = < T extends ShippingAddress | BillingAddress >(
	address1: T,
	address2: T
): boolean => {
	return Object.keys( defaultAddressFields ).every(
		( field: string ) =>
			address1[ field as keyof T ] === address2[ field as keyof T ]
	);
};

/**
 * pluckAddress takes a full address object and returns relevant fields for calculating
 * shipping, so we can track when one of them change to update rates.
 *
 * @param {Object} address          An object containing all address information
 * @param {string} address.country  The country.
 * @param {string} address.state    The state.
 * @param {string} address.city     The city.
 * @param {string} address.postcode The postal code.
 *
 * @return {Object} pluckedAddress  An object containing shipping address that are needed to fetch an address.
 */
export const pluckAddress = ( {
	country = '',
	state = '',
	city = '',
	postcode = '',
}: CartResponseBillingAddress | CartResponseShippingAddress ): {
	country: string;
	state: string;
	city: string;
	postcode: string;
} => ( {
	country: country.trim(),
	state: state.trim(),
	city: city.trim(),
	postcode: postcode ? postcode.replace( ' ', '' ).toUpperCase() : '',
} );

/**
 * pluckEmail takes a full address object and returns only the email address, if set and valid. Otherwise returns an empty string.
 *
 * @param {Object} address       An object containing all address information
 * @param {string} address.email The email address.
 * @return {string} The email address.
 */
export const pluckEmail = ( {
	email = '',
}: CartResponseBillingAddress ): string =>
	isEmail( email ) ? email.trim() : '';

/**
 * Type-guard.
 */
const isValidAddressKey = (
	key: string,
	address: CartResponseBillingAddress | CartResponseShippingAddress
): key is keyof typeof address => {
	return key in address;
};

/**
 * Sets fields to an empty string in an address if they are hidden by the settings in countryLocale.
 *
 * @param {Object} address The address to empty fields from.
 * @return {Object} The address with hidden fields values removed.
 */
export const emptyHiddenAddressFields = <
	T extends CartResponseBillingAddress | CartResponseShippingAddress
>(
	address: T
): T => {
	const fields = Object.keys( defaultAddressFields );
	const addressFields = prepareAddressFields( fields, {}, address.country );
	const newAddress = Object.assign( {}, address ) as T;

	addressFields.forEach( ( { key = '', hidden = false } ) => {
		if ( hidden && isValidAddressKey( key, address ) ) {
			newAddress[ key ] = '';
		}
	} );

	return newAddress;
};

/*
 * Formats a shipping address for display.
 *
 * @param {Object} address The address to format.
 * @return {string | null} The formatted address or null if no address is provided.
 */
export const formatShippingAddress = (
	address: ShippingAddress | BillingAddress
): string | null => {
	// We bail early if we don't have an address.
	if ( Object.values( address ).length === 0 ) {
		return null;
	}
	const shippingCountries = getSetting< Record< string, string > >(
		'shippingCountries',
		{}
	);
	const shippingStates = getSetting< Record< string, string > >(
		'shippingStates',
		{}
	);
	const formattedCountry =
		typeof shippingCountries[ address.country ] === 'string'
			? decodeEntities( shippingCountries[ address.country ] )
			: '';

	const formattedState =
		typeof shippingStates[ address.country ] === 'object' &&
		typeof shippingStates[ address.country ][ address.state ] === 'string'
			? decodeEntities(
					shippingStates[ address.country ][ address.state ]
			  )
			: address.state;

	const addressParts = [];

	addressParts.push( address.postcode.toUpperCase() );
	addressParts.push( address.city );
	addressParts.push( formattedState );
	addressParts.push( formattedCountry );

	const formattedLocation = addressParts.filter( Boolean ).join( ', ' );

	if ( ! formattedLocation ) {
		return null;
	}

	return formattedLocation;
};

/**
 * Returns true if the address has a city and country.
 */
export const isAddressComplete = (
	address: ShippingAddress | BillingAddress
): boolean => {
	return !! address.city && !! address.country;
};
