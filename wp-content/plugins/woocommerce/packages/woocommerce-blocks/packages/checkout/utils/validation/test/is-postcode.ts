/**
 * Internal dependencies
 */
import isPostcode from '../is-postcode';
import type { IsPostcodeProps } from '../is-postcode';

describe( 'isPostcode', () => {
	const cases = [
		// Austrian postcodes
		[ true, '1000', 'AT' ],
		[ true, '9999', 'AT' ],
		[ false, '0000', 'AT' ],
		[ false, '10000', 'AT' ],

		// Bosnian postcodes
		[ true, '71000', 'BA' ],
		[ true, '78256', 'BA' ],
		[ true, '89240', 'BA' ],
		[ false, '61000', 'BA' ],
		[ false, '7850', 'BA' ],

		// Belgian postcodes
		[ true, '1111', 'BE' ],
		[ false, '111', 'BE' ],
		[ false, '11111', 'BE' ],

		// Brazilian postcodes
		[ true, '99999-999', 'BR' ],
		[ true, '99999999', 'BR' ],
		[ false, '99999 999', 'BR' ],
		[ false, '99999-ABC', 'BR' ],

		// Canadian postcodes
		[ true, 'A9A 9A9', 'CA' ],
		[ true, 'A9A9A9', 'CA' ],
		[ true, 'a9a9a9', 'CA' ],
		[ false, 'D0A 9A9', 'CA' ],
		[ false, '99999', 'CA' ],
		[ false, 'ABC999', 'CA' ],
		[ false, '0A0A0A', 'CA' ],

		// Swiss postcodes
		[ true, '9999', 'CH' ],
		[ false, '99999', 'CH' ],
		[ false, 'ABCDE', 'CH' ],

		// Czech postcodes
		[ true, '160 00', 'CZ' ],
		[ true, '16000', 'CZ' ],
		[ false, '1600', 'CZ' ],

		// German postcodes
		[ true, '01234', 'DE' ],
		[ true, '12345', 'DE' ],
		[ false, '12 345', 'DE' ],
		[ false, '1234', 'DE' ],

		// Spanish postcodes
		[ true, '03000', 'ES' ],
		[ true, '08000', 'ES' ],
		[ false, '08 000', 'ES' ],
		[ false, '1234', 'ES' ],

		// French postcodes
		[ true, '01000', 'FR' ],
		[ true, '99999', 'FR' ],
		[ true, '01 000', 'FR' ],
		[ false, '1234', 'FR' ],

		// British postcodes
		[ true, 'AA9A 9AA', 'GB' ],
		[ true, 'A9A 9AA', 'GB' ],
		[ true, 'A9 9AA', 'GB' ],
		[ true, 'A99 9AA', 'GB' ],
		[ true, 'AA99 9AA', 'GB' ],
		[ true, 'BFPO 801', 'GB' ],
		[ false, '99999', 'GB' ],
		[ false, '9999 999', 'GB' ],
		[ false, '999 999', 'GB' ],
		[ false, '99 999', 'GB' ],
		[ false, '9A A9A', 'GB' ],

		// Hungarian postcodes
		[ true, '1234', 'HU' ],
		[ false, '123', 'HU' ],
		[ false, '12345', 'HU' ],

		// Irish postcodes
		[ true, 'A65F4E2', 'IE' ],
		[ true, 'A65 F4E2', 'IE' ],
		[ true, 'A65-F4E2', 'IE' ],
		[ false, 'B23F854', 'IE' ],

		// Indian postcodes
		[ true, '110001', 'IN' ],
		[ true, '110 001', 'IN' ],
		[ false, '11 0001', 'IN' ],
		[ false, '1100 01', 'IN' ],

		// Italian postcodes
		[ true, '99999', 'IT' ],
		[ false, '9999', 'IT' ],
		[ false, 'ABC 999', 'IT' ],
		[ false, 'ABC-999', 'IT' ],
		[ false, 'ABC_123', 'IT' ],

		// Japanese postcodes
		[ true, '1340088', 'JP' ],
		[ true, '134-0088', 'JP' ],
		[ false, '1340-088', 'JP' ],
		[ false, '12345', 'JP' ],
		[ false, '0123', 'JP' ],

		// Lichtenstein postcodes
		[ true, '9485', 'LI' ],
		[ true, '9486', 'LI' ],
		[ true, '9499', 'LI' ],
		[ false, '9585', 'LI' ],
		[ false, '9385', 'LI' ],
		[ false, '9475', 'LI' ],

		// Dutch postcodes
		[ true, '3852GC', 'NL' ],
		[ true, '3852 GC', 'NL' ],
		[ true, '3852 gc', 'NL' ],
		[ false, '3852SA', 'NL' ],
		[ false, '3852 SA', 'NL' ],
		[ false, '3852 sa', 'NL' ],

		// Polish postcodes
		[ true, '00-001', 'PL' ],
		[ true, '99-440', 'PL' ],
		[ false, '000-01', 'PL' ],
		[ false, '994-40', 'PL' ],
		[ false, '00001', 'PL' ],
		[ false, '99440', 'PL' ],

		// Puerto Rican postcodes
		[ true, '00901', 'PR' ],
		[ true, '00617', 'PR' ],
		[ true, '00602-1211', 'PR' ],
		[ false, '1234', 'PR' ],
		[ false, '0060-21211', 'PR' ],

		// Portuguese postcodes
		[ true, '1234-567', 'PT' ],
		[ true, '2345-678', 'PT' ],
		[ false, '123-4567', 'PT' ],
		[ false, '234-5678', 'PT' ],

		// Slovenian postcodes
		[ true, '1234', 'SI' ],
		[ true, '1000', 'SI' ],
		[ true, '9876', 'SI' ],
		[ false, '12345', 'SI' ],
		[ false, '0123', 'SI' ],

		// Slovak postcodes
		[ true, '010 01', 'SK' ],
		[ true, '01001', 'SK' ],
		[ false, '01 001', 'SK' ],
		[ false, '1234', 'SK' ],
		[ false, '123456', 'SK' ],

		// United States postcodes
		[ true, '90210', 'US' ],
		[ true, '99577-0727', 'US' ],
		[ false, 'ABCDE', 'US' ],
		[ false, 'ABCDE-9999', 'US' ],
	];

	test.each( cases )( '%s: %s for %s', ( result, postcode, country ) =>
		expect( isPostcode( { postcode, country } as IsPostcodeProps ) ).toBe(
			result
		)
	);
} );
