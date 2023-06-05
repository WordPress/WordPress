/**
 * External dependencies
 */
import { ApiErrorResponse, isApiErrorResponse } from '@woocommerce/types';
import { createNotice } from '@woocommerce/base-utils';
import { decodeEntities } from '@wordpress/html-entities';
import { dispatch } from '@wordpress/data';

/**
 * This function is used to notify the user of cart item errors/conflicts
 */
export const notifyCartErrors = (
	errors: ApiErrorResponse[] | null = null,
	oldErrors: ApiErrorResponse[] | null = null
) => {
	if ( oldErrors ) {
		oldErrors.forEach( ( error ) => {
			dispatch( 'core/notices' ).removeNotice( error.code, 'wc/cart' );
		} );
	}

	if ( errors !== null ) {
		errors.forEach( ( error ) => {
			if ( isApiErrorResponse( error ) ) {
				createNotice( 'error', decodeEntities( error.message ), {
					id: error.code,
					context: 'wc/cart',
					isDismissible: true,
				} );
			}
		} );
	}
};
