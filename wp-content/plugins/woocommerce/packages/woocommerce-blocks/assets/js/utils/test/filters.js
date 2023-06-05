/**
 * Internal dependencies
 */
import { normalizeQueryParams } from '../filters';

describe( 'normalizeQueryParams', () => {
	test( 'does not change url if there is no query params', () => {
		const input = 'https://example.com';
		const expected = 'https://example.com';

		expect( normalizeQueryParams( input ) ).toBe( expected );
	} );

	test( 'does not change search term if there is no special character', () => {
		const input = 'https://example.com?foo=bar&s=asdf1234&baz=qux';
		const expected = 'https://example.com?foo=bar&s=asdf1234&baz=qux';

		expect( normalizeQueryParams( input ) ).toBe( expected );
	} );

	test( 'decodes single quote characters', () => {
		const input = 'https://example.com?foo=bar%27&s=asd%27f1234&baz=qux%27';
		const expected = "https://example.com?foo=bar'&s=asd'f1234&baz=qux'";

		expect( normalizeQueryParams( input ) ).toBe( expected );
	} );
} );
