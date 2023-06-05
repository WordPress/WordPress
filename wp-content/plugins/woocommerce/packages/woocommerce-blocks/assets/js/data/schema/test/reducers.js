/**
 * External dependencies
 */
import deepFreeze from 'deep-freeze';

/**
 * Internal dependencies
 */
import { receiveRoutes } from '../reducers';
import { ACTION_TYPES as types } from '../action-types';

describe( 'receiveRoutes', () => {
	it( 'returns original state when action type is not a match', () => {
		expect( receiveRoutes( undefined, { type: 'invalid' } ) ).toEqual( {} );
	} );
	it( 'returns original state when the given endpoints already exists', () => {
		const routes = [
			'wc/blocks/products/attributes',
			'wc/blocks/products/attributes/(?P<attribute_id>[d]+)/terms/(?P<id>[d]+)',
		];
		const originalState = deepFreeze( {
			'wc/blocks': {
				'products/attributes': {
					'wc/blocks/products/attributes': [],
				},
				'products/attributes/terms': {
					'wc/blocks/products/attributes/{attribute_id}/terms/{id}': [
						'attribute_id',
						'id',
					],
				},
			},
		} );
		const newState = receiveRoutes( originalState, {
			type: types.RECEIVE_MODEL_ROUTES,
			namespace: 'wc/blocks',
			routes,
		} );
		expect( newState ).toBe( originalState );
	} );
	it( 'returns expected state when new route added', () => {
		const action = {
			type: types.RECEIVE_MODEL_ROUTES,
			namespace: 'wc/blocks',
			routes: [ 'wc/blocks/products/attributes' ],
		};
		const originalState = deepFreeze( {
			'wc/blocks': {
				'products/attributes/terms': {
					'wc/blocks/products/attributes/{attribute_id}/terms/{id}': [
						'attribute_id',
						'id',
					],
				},
			},
		} );
		const newState = receiveRoutes( originalState, action );
		expect( newState ).not.toBe( originalState );
		expect( newState ).toEqual( {
			'wc/blocks': {
				'products/attributes': {
					'wc/blocks/products/attributes': [],
				},
				'products/attributes/terms': {
					'wc/blocks/products/attributes/{attribute_id}/terms/{id}': [
						'attribute_id',
						'id',
					],
				},
			},
		} );
	} );
} );
