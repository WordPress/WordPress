/**
 * External dependencies
 */
import { COLLECTIONS_STORE_KEY as storeKey } from '@woocommerce/block-data';
import { useSelect } from '@wordpress/data';
import { useShallowEqual } from '@woocommerce/base-hooks';

/**
 * Internal dependencies
 */
import { useCollectionOptions } from '.';

/**
 * This is a custom hook that is wired up to the `wc/store/collections` data
 * store. Given a header key and a collections option object, this will ensure a
 * component is kept up to date with the collection header value matching that
 * query in the store state.
 *
 * @param {string} headerKey              Used to indicate which header value to
 *                                        return for the given collection query.
 *                                        Example: `'x-wp-total'`
 * @param {Object} options                An object declaring the various
 *                                        collection arguments.
 * @param {string} options.namespace      The namespace for the collection.
 *                                        Example: `'/wc/blocks'`
 * @param {string} options.resourceName   The name of the resource for the
 *                                        collection. Example:
 *                                        `'products/attributes'`
 * @param {Array}  options.resourceValues An array of values (in correct order)
 *                                        that are substituted in the route
 *                                        placeholders for the collection route.
 *                                        Example: `[10, 20]`
 * @param {Object} options.query          An object of key value pairs for the
 *                                        query to execute on the collection
 *                                        (optional). Example:
 *                                        `{ order: 'ASC', order_by: 'price' }`
 *
 * @return {Object} This hook will return an object with two properties:
 *                  - value     Whatever value is attached to the specified
 *                              header.
 *                  - isLoading A boolean indicating whether the header is
 *                              loading (true) or not.
 */

export const useCollectionHeader = (
	headerKey: string,
	options: Omit< useCollectionOptions, 'shouldSelect' >
): {
	value: unknown;
	isLoading: boolean;
} => {
	const {
		namespace,
		resourceName,
		resourceValues = [],
		query = {},
	} = options;
	if ( ! namespace || ! resourceName ) {
		throw new Error(
			'The options object must have valid values for the namespace and ' +
				'the resource name properties.'
		);
	}
	// ensure we feed the previous reference if it's equivalent
	const currentQuery = useShallowEqual( query );
	const currentResourceValues = useShallowEqual( resourceValues );
	const { value, isLoading = true } = useSelect(
		( select ) => {
			const store = select( storeKey );
			// filter out query if it is undefined.
			const args = [
				headerKey,
				namespace,
				resourceName,
				currentQuery,
				currentResourceValues,
			];
			return {
				value: store.getCollectionHeader( ...args ),
				isLoading: store.hasFinishedResolution(
					'getCollectionHeader',
					args
				),
			};
		},
		[
			headerKey,
			namespace,
			resourceName,
			currentResourceValues,
			currentQuery,
		]
	);
	return {
		value,
		isLoading,
	};
};
