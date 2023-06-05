/**
 * External dependencies
 */
import { isString } from '@woocommerce/types';

/**
 * Internal dependencies
 */
import metadata from './block.json';

export const parseAttributes = ( data: Record< string, unknown > ) => {
	return {
		heading: isString( data?.heading ) ? data.heading : '',
		headingLevel:
			( isString( data?.headingLevel ) &&
				parseInt( data.headingLevel, 10 ) ) ||
			metadata.attributes.headingLevel.default,
		showFilterButton: data?.showFilterButton === 'true',
		showInputFields: data?.showInputFields !== 'false',
		inlineInput: data?.inlineInput === 'true',
	};
};
