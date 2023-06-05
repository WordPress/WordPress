/**
 * External dependencies
 */
import { getValidBlockAttributes } from '@woocommerce/base-utils';

/**
 * HOC that filters given attributes by valid block attribute values, or uses defaults if undefined.
 *
 * @param {Object} blockAttributes Component being wrapped.
 */
export const withFilteredAttributes =
	( blockAttributes ) => ( OriginalComponent ) => {
		return ( ownProps ) => {
			const validBlockAttributes = getValidBlockAttributes(
				blockAttributes,
				ownProps
			);

			return (
				<OriginalComponent
					{ ...ownProps }
					{ ...validBlockAttributes }
				/>
			);
		};
	};
