/**
 * External dependencies
 */
import type { Reducer } from 'redux';
import { pickBy } from 'lodash';
import isShallowEqual from '@wordpress/is-shallow-equal';
import { isString, FieldValidationStatus } from '@woocommerce/types';

/**
 * Internal dependencies
 */
import { ValidationAction } from './actions';
import { ACTION_TYPES as types } from './action-types';

const reducer: Reducer< Record< string, FieldValidationStatus > > = (
	state: Record< string, FieldValidationStatus > = {},
	action: Partial< ValidationAction >
) => {
	const newState = { ...state };
	switch ( action.type ) {
		case types.SET_VALIDATION_ERRORS:
			const newErrors = pickBy( action.errors, ( error, property ) => {
				if ( typeof error?.message !== 'string' ) {
					return false;
				}
				if ( state.hasOwnProperty( property ) ) {
					return ! isShallowEqual( state[ property ], error );
				}
				return true;
			} );
			if ( Object.values( newErrors ).length === 0 ) {
				return state;
			}
			return { ...state, ...action.errors };

		case types.CLEAR_VALIDATION_ERROR:
			if (
				! isString( action.error ) ||
				! newState.hasOwnProperty( action.error )
			) {
				return newState;
			}
			delete newState[ action.error ];
			return newState;
		case types.CLEAR_VALIDATION_ERRORS:
			const { errors } = action;
			if ( typeof errors === 'undefined' ) {
				return {};
			}
			if ( ! Array.isArray( errors ) ) {
				return newState;
			}
			errors.forEach( ( error ) => {
				if ( newState.hasOwnProperty( error ) ) {
					delete newState[ error ];
				}
			} );
			return newState;
		case types.HIDE_VALIDATION_ERROR:
			if (
				! isString( action.error ) ||
				! newState.hasOwnProperty( action.error )
			) {
				return newState;
			}
			newState[ action.error ].hidden = true;
			return newState;
		case types.SHOW_VALIDATION_ERROR:
			if (
				! isString( action.error ) ||
				! newState.hasOwnProperty( action.error )
			) {
				return newState;
			}
			newState[ action.error ].hidden = false;
			return newState;
		case types.SHOW_ALL_VALIDATION_ERRORS:
			Object.keys( newState ).forEach( ( property ) => {
				if ( newState[ property ].hidden ) {
					newState[ property ].hidden = false;
				}
			} );
			return { ...newState };

		default:
			return state;
	}
};

export type State = ReturnType< typeof reducer >;
export default reducer;
