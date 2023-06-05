/**
 * External dependencies
 */
import { __, sprintf } from '@wordpress/i18n';
import { CURRENT_USER_IS_ADMIN } from '@woocommerce/settings';
import deprecated from '@wordpress/deprecated';
import isShallowEqual from '@wordpress/is-shallow-equal';
import type { ComparableObject } from '@wordpress/is-shallow-equal';
import { isNull, isObject, objectHasProp } from '@woocommerce/types';

/**
 * A function that always return true.
 * We need to have a single instance of this function so it doesn't
 * invalidate our memo comparison.
 */
const returnTrue = (): true => true;

type CheckoutFilterFunction< U = unknown > = < T >(
	value: T | U,
	extensions: Record< string, unknown >,
	args?: CheckoutFilterArguments
) => T | U;

type CheckoutFilterArguments =
	| ( Record< string, unknown > & {
			context?: string;
	  } )
	| null;

let checkoutFilters: Record<
	string,
	Record< string, CheckoutFilterFunction >
> = {};

let cachedValues: Record< string, unknown > = {};

/**
 * Register filters for a specific extension.
 */
export const registerCheckoutFilters = (
	namespace: string,
	filters: Record< string, CheckoutFilterFunction >
): void => {
	/**
	 * Let the user know couponName is no longer available as a filter.
	 *
	 * See https://github.com/woocommerce/woocommerce-gutenberg-products-block/pull/4312
	 */
	if ( Object.keys( filters ).includes( 'couponName' ) ) {
		deprecated( 'couponName', {
			alternative: 'coupons',
			plugin: 'WooCommerce Blocks',
			link: 'https://github.com/woocommerce/woocommerce-gutenberg-products-block/blob/bb921d21f42e21f38df2b1c87b48e07aa4cb0538/docs/extensibility/available-filters.md#coupons',
		} );
	}
	// Clear cached values when registering new filters because otherwise we get outdated results when applying them.
	cachedValues = {};
	checkoutFilters = {
		...checkoutFilters,
		[ namespace ]: filters,
	};
};

/**
 * Backward compatibility for __experimentalRegisterCheckoutFilters, this has been graduated to stable now.
 * Remove after July 2023.
 */
export const __experimentalRegisterCheckoutFilters = (
	namespace: string,
	filters: Record< string, CheckoutFilterFunction >
) => {
	deprecated( '__experimentalRegisterCheckoutFilters', {
		alternative: 'registerCheckoutFilters',
		plugin: 'WooCommerce Blocks',
		link: 'https://github.com/woocommerce/woocommerce-blocks/pull/8346',
		since: '9.6.0',
		hint: '__experimentalRegisterCheckoutFilters has graduated to stable and this experimental function will be removed.',
	} );
	registerCheckoutFilters( namespace, filters );
};

/**
 * Get all filters with a specific name.
 *
 * @param {string} filterName Name of the filter to search for.
 * @return {Function[]} Array of functions that are registered for that filter
 *                      name.
 */
const getCheckoutFilters = ( filterName: string ): CheckoutFilterFunction[] => {
	const namespaces = Object.keys( checkoutFilters );
	const filters = namespaces
		.map( ( namespace ) => checkoutFilters[ namespace ][ filterName ] )
		.filter( Boolean );
	return filters;
};

const cachedFilterRuns: Record<
	string,
	{
		arg?: CheckoutFilterArguments;
		extensions?: Record< string, unknown > | null;
		defaultValue: unknown;
	} & Record< string, unknown >
> = {};

const updatePreviousFilterRun = < T >(
	filterName: string,
	arg: CheckoutFilterArguments,
	extensions: Record< string, unknown > | null,
	defaultValue: T
): void => {
	cachedFilterRuns[ filterName ] = {
		arg,
		extensions,
		defaultValue,
	};
};

/**
 * A function that checks the shallow equality of an object's members.
 */
const checkMembersShallowEqual = <
	T extends Record< string, unknown > | null,
	U extends Record< string, unknown > | null
>(
	a: T,
	b: U
) => {
	// For the case when extensions is null across runs.
	if ( isNull( a ) && isNull( b ) ) {
		return true;
	}

	return (
		isObject( a ) &&
		isObject( b ) &&
		Object.keys( a ).length === Object.keys( b ).length &&
		Object.keys( a ).every( ( aKey ) => {
			return (
				objectHasProp( b, aKey ) &&
				isShallowEqual(
					a[ aKey ] as ComparableObject,
					b[ aKey ] as ComparableObject
				)
			);
		} )
	);
};

/**
 * A function that checks the arg and extensions that were passed the last time a specific filter ran.
 * If they are shallowly equal, then return the cached value and prevent third party code running. If they are
 * different then the third party filters are run and the result is cached.
 */
const shouldReRunFilters = < T >(
	filterName: string,
	arg: CheckoutFilterArguments,
	extensions: Record< string, unknown > | null,
	defaultValue: T
): boolean => {
	const previousFilterRun = cachedFilterRuns[ filterName ];

	if ( ! previousFilterRun ) {
		// This is the first time the filter is running so let it continue;
		updatePreviousFilterRun( filterName, arg, extensions, defaultValue );
		return true;
	}
	const {
		arg: previousArg = {} as Record< string, unknown >,
		extensions: previousExtensions = {} as Record< string, unknown >,
		defaultValue: previousDefaultValue = null,
	} = previousFilterRun;

	// Check length of arg and previousArg, and that all keys are present in both arg and previousArg
	const argIsEqual = checkMembersShallowEqual( arg, previousArg );
	if ( ! argIsEqual ) {
		updatePreviousFilterRun( filterName, arg, extensions, defaultValue );
		return true;
	}

	// Check length of arg and previousArg, and that all keys are present in both arg and previousArg
	const defaultValueIsEqual = defaultValue === previousDefaultValue;
	if ( ! defaultValueIsEqual ) {
		updatePreviousFilterRun( filterName, arg, extensions, defaultValue );
		return true;
	}

	const extensionsIsEqual = checkMembersShallowEqual(
		extensions,
		previousExtensions
	);
	if ( ! extensionsIsEqual ) {
		updatePreviousFilterRun( filterName, arg, extensions, defaultValue );
		return true;
	}
	return false;
};

/**
 * Apply a filter.
 */
export const applyCheckoutFilter = < T >( {
	filterName,
	defaultValue,
	extensions = null,
	arg = null,
	validation = returnTrue,
}: {
	/** Name of the filter to apply. */
	filterName: string;
	/** Default value to filter. */
	defaultValue: T;
	/** Values extend to REST API response. */
	extensions?: Record< string, unknown > | null;
	/** Object containing arguments for the filter function. */
	arg?: CheckoutFilterArguments;
	/** Function that needs to return true when the filtered value is passed in order for the filter to be applied. */
	validation?: ( value: T ) => true | Error;
} ): T => {
	if (
		! shouldReRunFilters( filterName, arg, extensions, defaultValue ) &&
		cachedValues[ filterName ] !== undefined
	) {
		return cachedValues[ filterName ] as T;
	}
	const filters = getCheckoutFilters( filterName );
	let value = defaultValue;
	filters.forEach( ( filter ) => {
		try {
			const newValue = filter( value, extensions || {}, arg ) as T;
			if ( typeof newValue !== typeof value ) {
				throw new Error(
					sprintf(
						/* translators: %1$s is the type of the variable passed to the filter function, %2$s is the type of the value returned by the filter function. */
						__(
							'The type returned by checkout filters must be the same as the type they receive. The function received %1$s but returned %2$s.',
							'woo-gutenberg-products-block'
						),
						typeof value,
						typeof newValue
					)
				);
			}
			value = validation( newValue ) ? newValue : value;
		} catch ( e ) {
			if ( CURRENT_USER_IS_ADMIN ) {
				throw e;
			} else {
				// eslint-disable-next-line no-console
				console.error( e );
			}
		}
	} );
	cachedValues[ filterName ] = value;
	return value;
};

/**
 * Backward compatibility for __experimentalApplyCheckoutFilter, this has been graduated to stable now.
 * Remove after July 2023.
 */
export const __experimentalApplyCheckoutFilter = < T >( {
	filterName,
	defaultValue,
	extensions = null,
	arg = null,
	validation = returnTrue,
}: {
	/** Name of the filter to apply. */
	filterName: string;
	/** Default value to filter. */
	defaultValue: T;
	/** Values extend to REST API response. */
	extensions?: Record< string, unknown > | null;
	/** Object containing arguments for the filter function. */
	arg?: CheckoutFilterArguments;
	/** Function that needs to return true when the filtered value is passed in order for the filter to be applied. */
	validation?: ( value: T ) => true | Error;
} ): T => {
	deprecated( '__experimentalApplyCheckoutFilter', {
		alternative: 'applyCheckoutFilter',
		plugin: 'WooCommerce Blocks',
		link: 'https://github.com/woocommerce/woocommerce-blocks/pull/8346',
		since: '9.6.0',
		hint: '__experimentalApplyCheckoutFilter has graduated to stable and this experimental function will be removed.',
	} );
	return applyCheckoutFilter( {
		filterName,
		defaultValue,
		extensions,
		arg,
		validation,
	} );
};
