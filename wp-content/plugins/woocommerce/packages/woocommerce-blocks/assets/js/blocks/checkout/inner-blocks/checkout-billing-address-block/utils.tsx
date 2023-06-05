/**
 * Internal dependencies
 */
import {
	DEFAULT_TITLE,
	DEFAULT_DESCRIPTION,
	DEFAULT_FORCED_BILLING_DESCRIPTION,
	DEFAULT_FORCED_BILLING_TITLE,
} from './constants';

export const getBillingAddresssBlockTitle = (
	title: string,
	forcedBillingAddress: boolean
): string => {
	if ( forcedBillingAddress ) {
		// Returns default forced billing title when forced billing address is enabled and there is no title set.
		return title === DEFAULT_TITLE ? DEFAULT_FORCED_BILLING_TITLE : title;
	}
	// Returns default title when forced billing address is disabled and there is no title set.
	return title === DEFAULT_FORCED_BILLING_TITLE ? DEFAULT_TITLE : title;
};

export const getBillingAddresssBlockDescription = (
	description: string,
	forcedBillingAddress: boolean
): string => {
	if ( forcedBillingAddress ) {
		// Returns default forced billing description when forced billing address is enabled and there is no description set.
		return description === DEFAULT_DESCRIPTION
			? DEFAULT_FORCED_BILLING_DESCRIPTION
			: description;
	}

	// Returns default description when forced billing address is disabled and there is no description set.
	return description === DEFAULT_FORCED_BILLING_DESCRIPTION
		? DEFAULT_DESCRIPTION
		: description;
};
