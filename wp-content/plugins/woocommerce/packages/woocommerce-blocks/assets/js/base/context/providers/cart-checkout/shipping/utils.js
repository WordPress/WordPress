/**
 * Internal dependencies
 */
import { shippingErrorCodes } from './constants';

export const hasInvalidShippingAddress = ( errors ) => {
	return errors.some( ( error ) => {
		if (
			error.code &&
			Object.values( shippingErrorCodes ).includes( error.code )
		) {
			return true;
		}
		return false;
	} );
};
