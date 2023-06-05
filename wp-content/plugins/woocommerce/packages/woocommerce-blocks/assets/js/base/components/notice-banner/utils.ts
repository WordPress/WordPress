/**
 * External dependencies
 */
import { info, megaphone, check } from '@wordpress/icons';

/**
 * Get the default politeness level for a given status. This is based on how severe the status is.
 */
export const getDefaultPoliteness = ( status: string ) => {
	switch ( status ) {
		case 'success':
		case 'warning':
		case 'info':
		case 'default':
			return 'polite';

		case 'error':
		default:
			return 'assertive';
	}
};

/**
 * Gets the icon for the notice from the status. Note; we spin the warning status 180 degrees to make it look like an exclamation mark.
 */
export const getStatusIcon = ( status: string ): JSX.Element => {
	switch ( status ) {
		case 'success':
			return check;
		case 'warning':
		case 'info':
		case 'error':
			return info;
		default:
			return megaphone;
	}
};
