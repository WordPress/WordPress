/**
 * External dependencies
 */
import { COLLECTIONS_STORE_KEY as storeKey } from '@woocommerce/block-data';
import { useSelect } from '@wordpress/data';
import { useRef } from '@wordpress/element';
import { useShallowEqual, useThrowError } from '@woocommerce/base-hooks';
import { isError } from '@woocommerce/types';

/**
 * This is a custom hook that is wired up to the `wc/store/collections` data
 * store. Given a collections option object, this will ensure a component is
 * kept up to date with the collection matching that query in the store state.
 *
 * @throws {Object} Throws an exception object if there was a problem with the
 * 					API request, to be picked up by BlockErrorBoundry.
 *
 * @param {Object}  options                  An object declaring the various
 *                                           collection arguments.
 * @param {string}  options.namespace        The namespace for the collection.
 *                                           Example: `'/wc/blocks'`
 * @param {string}  options.resourceName     The name of the resource for the
 *                                           collection. Example:
 *                                           `'products/attributes'`
 * @param {Array}   [options.resourceValues] An array of values (in correct order)
 *                                           that are substituted in the route
 *                                           placeholders for the collection route.
 *                                           Example: `[10, 20]`
 * @param {Object}  [options.query]          An object of key value pairs for the
 *                                           query to execute on the collection
 *                                           Example:
 *                                           `{ order: 'ASC', order_by: 'price' }`
 * @param {boolean} [options.shouldSelect]   If false, the previous results will be
 *                                           returned and internal selects will not
 *                                           fire.
 *
 * @return {Object} This hook will return an object with two properties:
 *                  - results   An array of collection items returned.
 *                  - isLoading A boolean indicating whether the collection is
 *                              loading (true) or not.
 */

export interface useCollectionOptions {
	namespace: string;
	resourceName: string;
	resourceValues?: number[];
	query?: Record< string, unknown >;
	shouldSelect?: boolean;
	isEditor?: boolean;
}

export const useCollection = (
	options: useCollectionOptions
): {
	results: unknown;
	isLoading: boolean;
} => {
	const {
		namespace,
		resourceName,
		resourceValues = [],
		query = {},
		shouldSelect = true,
	} = options;
	if ( ! namespace || ! resourceName ) {
		throw new Error(
			'The options object must have valid values for the namespace and ' +
				'the resource properties.'
		);
	}
	const currentResults = useRef< { results: unknown; isLoading: boolean } >( {
		results: [],
		isLoading: true,
	} );
	// ensure we feed the previous reference if it's equivalent
	const currentQuery = useShallowEqual( query );
	const currentResourceValues = useShallowEqual( resourceValues );
	const throwError = useThrowError();
	const results = useSelect(
		( select ) => {
			if ( ! shouldSelect ) {
				return null;
			}

			const store = select( storeKey );
			const args = [
				namespace,
				resourceName,
				currentQuery,
				currentResourceValues,
			];
			const error = store.getCollectionError( ...args );

			if ( error ) {
				if ( isError( error ) ) {
					throwError( error );
				} else {
					throw new Error(
						'TypeError: `error` object is not an instance of Error constructor'
					);
				}
			}

			return {
				results: store.getCollection< T >( ...args ),
				isLoading: ! store.hasFinishedResolution(
					'getCollection',
					args
				),
			};
		},
		[
			namespace,
			resourceName,
			currentResourceValues,
			currentQuery,
			shouldSelect,
		]
	);
	// if selector was not bailed, then update current results. Otherwise return
	// previous results
	if ( results !== null ) {
		currentResults.current = results;
	}
	return currentResults.current;
};
