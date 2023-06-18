/**
 * External dependencies
 */
import { useState, useEffect, useMemo } from '@wordpress/element';
import { useDebounce } from 'use-debounce';
import { isEmpty, objectHasProp } from '@woocommerce/types';
import { sort } from 'fast-sort';
import { useShallowEqual } from '@woocommerce/base-hooks';

/**
 * Internal dependencies
 */
import { useQueryStateByContext, useQueryStateByKey } from '../use-query-state';
import { useCollection } from './use-collection';
import { useQueryStateContext } from '../../providers/query-state-context';

const buildCollectionDataQuery = (
	collectionDataQueryState: Record< string, unknown >
) => {
	const query = collectionDataQueryState;

	if (
		Array.isArray( collectionDataQueryState.calculate_attribute_counts )
	) {
		query.calculate_attribute_counts = sort(
			collectionDataQueryState.calculate_attribute_counts.map(
				( { taxonomy, queryType } ) => {
					return {
						taxonomy,
						query_type: queryType,
					};
				}
			)
		).asc( [ 'taxonomy', 'query_type' ] );
	}

	return query;
};

interface UseCollectionDataProps {
	queryAttribute?: {
		taxonomy: string;
		queryType: string;
	};
	queryPrices?: boolean;
	queryStock?: boolean;
	queryRating?: boolean;
	queryState: Record< string, unknown >;
	productIds?: number[];
	isEditor?: boolean;
}

export const useCollectionData = ( {
	queryAttribute,
	queryPrices,
	queryStock,
	queryRating,
	queryState,
	productIds,
	isEditor = false,
}: UseCollectionDataProps ) => {
	let context = useQueryStateContext();
	context = `${ context }-collection-data`;

	const [ collectionDataQueryState ] = useQueryStateByContext( context );
	const [ calculateAttributesQueryState, setCalculateAttributesQueryState ] =
		useQueryStateByKey( 'calculate_attribute_counts', [], context );
	const [ calculatePriceRangeQueryState, setCalculatePriceRangeQueryState ] =
		useQueryStateByKey( 'calculate_price_range', null, context );
	const [
		calculateStockStatusQueryState,
		setCalculateStockStatusQueryState,
	] = useQueryStateByKey( 'calculate_stock_status_counts', null, context );
	const [ calculateRatingQueryState, setCalculateRatingQueryState ] =
		useQueryStateByKey( 'calculate_rating_counts', null, context );

	const currentQueryAttribute = useShallowEqual( queryAttribute || {} );
	const currentQueryPrices = useShallowEqual( queryPrices );
	const currentQueryStock = useShallowEqual( queryStock );
	const currentQueryRating = useShallowEqual( queryRating );

	useEffect( () => {
		if (
			typeof currentQueryAttribute === 'object' &&
			Object.keys( currentQueryAttribute ).length
		) {
			const foundAttribute = calculateAttributesQueryState.find(
				( attribute ) => {
					return (
						objectHasProp( currentQueryAttribute, 'taxonomy' ) &&
						attribute.taxonomy === currentQueryAttribute.taxonomy
					);
				}
			);

			if ( ! foundAttribute ) {
				setCalculateAttributesQueryState( [
					...calculateAttributesQueryState,
					currentQueryAttribute,
				] );
			}
		}
	}, [
		currentQueryAttribute,
		calculateAttributesQueryState,
		setCalculateAttributesQueryState,
	] );

	useEffect( () => {
		if (
			calculatePriceRangeQueryState !== currentQueryPrices &&
			currentQueryPrices !== undefined
		) {
			setCalculatePriceRangeQueryState( currentQueryPrices );
		}
	}, [
		currentQueryPrices,
		setCalculatePriceRangeQueryState,
		calculatePriceRangeQueryState,
	] );

	useEffect( () => {
		if (
			calculateStockStatusQueryState !== currentQueryStock &&
			currentQueryStock !== undefined
		) {
			setCalculateStockStatusQueryState( currentQueryStock );
		}
	}, [
		currentQueryStock,
		setCalculateStockStatusQueryState,
		calculateStockStatusQueryState,
	] );

	useEffect( () => {
		if (
			calculateRatingQueryState !== currentQueryRating &&
			currentQueryRating !== undefined
		) {
			setCalculateRatingQueryState( currentQueryRating );
		}
	}, [
		currentQueryRating,
		setCalculateRatingQueryState,
		calculateRatingQueryState,
	] );

	// Defer the select query so all collection-data query vars can be gathered.
	const [ shouldSelect, setShouldSelect ] = useState( isEditor );
	const [ debouncedShouldSelect ] = useDebounce( shouldSelect, 200 );

	if ( ! shouldSelect ) {
		setShouldSelect( true );
	}

	const collectionDataQueryVars = useMemo( () => {
		return buildCollectionDataQuery( collectionDataQueryState );
	}, [ collectionDataQueryState ] );

	return useCollection( {
		namespace: '/wc/store/v1',
		resourceName: 'products/collection-data',
		query: {
			...queryState,
			page: undefined,
			per_page: undefined,
			orderby: undefined,
			order: undefined,
			...( ! isEmpty( productIds ) && { include: productIds } ),
			...collectionDataQueryVars,
		},
		shouldSelect: debouncedShouldSelect,
	} );
};
