/**
 * External dependencies
 */
import { isBoolean, isString } from '@woocommerce/types';

/**
 * Internal dependencies
 */
import { ADMIN_URL } from '../default-constants';
import { getSettingWithCoercion } from '..';

describe( 'getSettingWithCoercion', () => {
	it( 'returns provided default for non available setting', () => {
		expect(
			getSettingWithCoercion( 'nada', 'really nada', isString )
		).toBe( 'really nada' );
	} );
	it( 'returns provided default value when the typeguard returns false', () => {
		expect(
			getSettingWithCoercion( 'currentUserIsAdmin', '', isString )
		).toBe( '' );
	} );
	it( 'returns expected value for existing setting', () => {
		expect(
			getSettingWithCoercion( 'adminUrl', 'not this', isString )
		).toEqual( ADMIN_URL );
	} );
	it( "returns expected value for existing setting even if it's a falsy value", () => {
		expect(
			getSettingWithCoercion( 'currentUserIsAdmin', true, isBoolean )
		).toBe( false );
	} );
} );
