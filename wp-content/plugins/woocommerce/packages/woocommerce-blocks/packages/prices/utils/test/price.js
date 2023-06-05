/**
 * Internal dependencies
 */
import { formatPrice, getCurrency } from '../price';

describe( 'The function formatPrice()', () => {
	test.each`
		value               | prefix    | suffix   | thousandSeparator | decimalSeparator | minorUnit | expected
		${ 1020 }           | ${ '€' }  | ${ '' }  | ${ ',' }          | ${ '.' }         | ${ 2 }    | ${ '€10.20' }
		${ 1000 }           | ${ '€' }  | ${ '' }  | ${ ',' }          | ${ '.' }         | ${ 2 }    | ${ '€10.00' }
		${ 1000 }           | ${ '' }   | ${ '€' } | ${ ',' }          | ${ '.' }         | ${ 2 }    | ${ '10.00€' }
		${ 1000 }           | ${ '' }   | ${ '$' } | ${ ',' }          | ${ '.' }         | ${ 2 }    | ${ '10.00$' }
		${ '1000' }         | ${ '€' }  | ${ '' }  | ${ ',' }          | ${ '.' }         | ${ 2 }    | ${ '€10.00' }
		${ 0 }              | ${ '€' }  | ${ '' }  | ${ ',' }          | ${ '.' }         | ${ 2 }    | ${ '€0.00' }
		${ '' }             | ${ '€' }  | ${ '' }  | ${ ',' }          | ${ '.' }         | ${ 2 }    | ${ '' }
		${ null }           | ${ '€' }  | ${ '' }  | ${ ',' }          | ${ '.' }         | ${ 2 }    | ${ '' }
		${ undefined }      | ${ '€' }  | ${ '' }  | ${ ',' }          | ${ '.' }         | ${ 2 }    | ${ '' }
		${ 100000 }         | ${ '€' }  | ${ '' }  | ${ ',' }          | ${ '.' }         | ${ 2 }    | ${ '€1,000.00' }
		${ 1000000 }        | ${ '€' }  | ${ '' }  | ${ ',' }          | ${ '.' }         | ${ 2 }    | ${ '€10,000.00' }
		${ 1000000000 }     | ${ '€' }  | ${ '' }  | ${ ',' }          | ${ '.' }         | ${ 2 }    | ${ '€10,000,000.00' }
		${ 10000000000 }    | ${ '€' }  | ${ '' }  | ${ ',' }          | ${ '.' }         | ${ 3 }    | ${ '€10,000,000.000' }
		${ 10000000000000 } | ${ '€ ' } | ${ '' }  | ${ ',' }          | ${ '.' }         | ${ 6 }    | ${ '€ 10,000,000.000000' }
		${ 10000000 }       | ${ '€ ' } | ${ '' }  | ${ ',' }          | ${ '.' }         | ${ 0 }    | ${ '€ 10,000,000' }
		${ 1000000099 }     | ${ '$' }  | ${ '' }  | ${ ',' }          | ${ '.' }         | ${ 2 }    | ${ '$10,000,000.99' }
		${ 1000000099 }     | ${ '$' }  | ${ '' }  | ${ '.' }          | ${ ',' }         | ${ 2 }    | ${ '$10.000.000,99' }
	`(
		'correctly formats price given "$value", "$prefix" prefix, "$suffix" suffix, "$thousandSeparator" thousandSeparator, "$decimalSeparator" decimalSeparator, and "$minorUnit" minorUnit as "$expected"',
		( {
			value,
			prefix,
			suffix,
			expected,
			thousandSeparator,
			decimalSeparator,
			minorUnit,
		} ) => {
			const formattedPrice = formatPrice(
				value,
				getCurrency( {
					prefix,
					suffix,
					thousandSeparator,
					decimalSeparator,
					minorUnit,
				} )
			);

			expect( formattedPrice ).toEqual( expected );
		}
	);

	test.each`
		value          | expected
		${ 1000 }      | ${ '$10.00' }
		${ 0 }         | ${ '$0.00' }
		${ '' }        | ${ '' }
		${ null }      | ${ '' }
		${ undefined } | ${ '' }
	`(
		'correctly formats price given "$value" only as "$expected"',
		( { value, expected } ) => {
			const formattedPrice = formatPrice( value );

			expect( formattedPrice ).toEqual( expected );
		}
	);
} );
