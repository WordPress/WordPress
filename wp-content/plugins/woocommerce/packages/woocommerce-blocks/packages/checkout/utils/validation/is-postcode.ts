/**
 * External dependencies
 */
import { POSTCODE_REGEXES } from 'postcode-validator/lib/cjs/postcode-regexes.js';

const CUSTOM_REGEXES = new Map< string, RegExp >( [
	[ 'BA', /^([7-8]{1})([0-9]{4})$/ ],
	[
		'GB',
		/^([A-Z]){1}([0-9]{1,2}|[A-Z][0-9][A-Z]|[A-Z][0-9]{2}|[A-Z][0-9]|[0-9][A-Z]){1}([ ])?([0-9][A-Z]{2}){1}|BFPO(?:\s)?([0-9]{1,4})$|BFPO(c\/o[0-9]{1,3})$/i,
	],
	[ 'IN', /^[1-9]{1}[0-9]{2}\s{0,1}[0-9]{3}$/ ],
	[ 'JP', /^([0-9]{3})([-]?)([0-9]{4})$/ ],
	[ 'LI', /^(94[8-9][0-9])$/ ],
	[ 'NL', /^([1-9][0-9]{3})(\s?)(?!SA|SD|SS)[A-Z]{2}$/i ],
	[ 'SI', /^([1-9][0-9]{3})$/ ],
] );

const DEFAULT_REGEXES = new Map< string, RegExp >( [
	...POSTCODE_REGEXES,
	...CUSTOM_REGEXES,
] );

export interface IsPostcodeProps {
	postcode: string;
	country: string;
}

const isPostcode = ( { postcode, country }: IsPostcodeProps ): boolean => {
	// If the country is not in the list of regexes, trying to test it would result in an error, so we skip and assume
	// that it is valid.
	const postcodeTest = DEFAULT_REGEXES.get( country )?.test( postcode );
	return typeof postcodeTest !== 'undefined' ? postcodeTest : true;
};

export default isPostcode;
