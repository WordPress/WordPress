/**
 * External dependencies
 */
import { usePrevious } from '@woocommerce/base-hooks';
import {
	useQueryStateByKey,
	useQueryStateByContext,
	useCollectionData,
} from '@woocommerce/base-context/hooks';
import { useCallback, useState, useEffect } from '@wordpress/element';
import PriceSlider from '@woocommerce/base-components/price-slider';
import FilterTitlePlaceholder from '@woocommerce/base-components/filter-placeholder';
import { useDebouncedCallback } from 'use-debounce';
import PropTypes from 'prop-types';
import { getCurrencyFromPriceResponse } from '@woocommerce/price-format';
import { getSettingWithCoercion } from '@woocommerce/settings';
import { addQueryArgs, removeQueryArgs } from '@wordpress/url';
import { changeUrl, getUrlParameter } from '@woocommerce/utils';
import {
	CurrencyResponse,
	isBoolean,
	isString,
	objectHasProp,
} from '@woocommerce/types';

/**
 * Internal dependencies
 */
import usePriceConstraints from './use-price-constraints';
import './style.scss';
import { Attributes } from './types';
import { useSetWraperVisibility } from '../filter-wrapper/context';

/**
 * Formats filter values into a string for the URL parameters needed for filtering PHP templates.
 *
 * @param {string} url    Current page URL.
 * @param {Object} params Parameters and their constraints.
 *
 * @return {string} New URL with query parameters in it.
 */
function formatParams(
	url: string,
	params: Record< string, string | number >
) {
	const paramObject: Record< string, string > = {};

	for ( const [ key, value ] of Object.entries( params ) ) {
		if ( value ) {
			paramObject[ key ] = value.toString();
		} else {
			delete paramObject[ key ];
		}
	}

	// Clean the URL before we add our new query parameters to it.
	const cleanUrl = removeQueryArgs( url, ...Object.keys( params ) );

	return addQueryArgs( cleanUrl, paramObject );
}

/**
 * Formats price values taking into account precision
 *
 * @param {string|void} value
 * @param {number}      minorUnit
 *
 * @return {number} Formatted price.
 */

function formatPrice( value: unknown, minorUnit: number ) {
	return Number( value ) * 10 ** minorUnit;
}

/**
 * Component displaying a price filter.
 *
 * @param {Object}  props            Component props.
 * @param {Object}  props.attributes Incoming block attributes.
 * @param {boolean} props.isEditor   Whether the component is being rendered in the editor.
 */
const PriceFilterBlock = ( {
	attributes,
	isEditor = false,
}: {
	attributes: Attributes;
	isEditor: boolean;
} ) => {
	const setWrapperVisibility = useSetWraperVisibility();
	const hasFilterableProducts = getSettingWithCoercion(
		'has_filterable_products',
		false,
		isBoolean
	);

	const filteringForPhpTemplate = getSettingWithCoercion(
		'is_rendering_php_template',
		false,
		isBoolean
	);

	const productIds = isEditor
		? []
		: getSettingWithCoercion( 'product_ids', [], Array.isArray );

	const [ hasSetFilterDefaultsFromUrl, setHasSetFilterDefaultsFromUrl ] =
		useState( false );

	const minPriceParam = getUrlParameter( 'min_price' );
	const maxPriceParam = getUrlParameter( 'max_price' );
	const [ queryState ] = useQueryStateByContext();
	const { results, isLoading } = useCollectionData( {
		queryPrices: true,
		queryState,
		productIds,
		isEditor,
	} );

	const currency = getCurrencyFromPriceResponse(
		objectHasProp( results, 'price_range' )
			? ( results.price_range as CurrencyResponse )
			: undefined
	);

	const [ minPriceQuery, setMinPriceQuery ] =
		useQueryStateByKey( 'min_price' );
	const [ maxPriceQuery, setMaxPriceQuery ] =
		useQueryStateByKey( 'max_price' );

	const [ minPrice, setMinPrice ] = useState(
		formatPrice( minPriceParam, currency.minorUnit ) || null
	);
	const [ maxPrice, setMaxPrice ] = useState(
		formatPrice( maxPriceParam, currency.minorUnit ) || null
	);

	const { minConstraint, maxConstraint } = usePriceConstraints( {
		minPrice:
			objectHasProp( results, 'price_range' ) &&
			objectHasProp( results.price_range, 'min_price' ) &&
			isString( results.price_range.min_price )
				? results.price_range.min_price
				: undefined,
		maxPrice:
			objectHasProp( results, 'price_range' ) &&
			objectHasProp( results.price_range, 'max_price' ) &&
			isString( results.price_range.max_price )
				? results.price_range.max_price
				: undefined,
		minorUnit: currency.minorUnit,
	} );

	/**
	 * Try get the min and/or max price from the URL.
	 */
	useEffect( () => {
		if ( ! hasSetFilterDefaultsFromUrl ) {
			setMinPriceQuery(
				formatPrice( minPriceParam, currency.minorUnit )
			);
			setMaxPriceQuery(
				formatPrice( maxPriceParam, currency.minorUnit )
			);

			setHasSetFilterDefaultsFromUrl( true );
		}
	}, [
		currency.minorUnit,
		hasSetFilterDefaultsFromUrl,
		maxPriceParam,
		minPriceParam,
		setMaxPriceQuery,
		setMinPriceQuery,
	] );

	const [ isUpdating, setIsUpdating ] = useState( isLoading );

	// Updates the query based on slider values.
	const onSubmit = useCallback(
		( newMinPrice, newMaxPrice ) => {
			const finalMaxPrice =
				newMaxPrice >= Number( maxConstraint )
					? undefined
					: newMaxPrice;
			const finalMinPrice =
				newMinPrice <= Number( minConstraint )
					? undefined
					: newMinPrice;

			if ( window ) {
				const newUrl = formatParams( window.location.href, {
					min_price: finalMinPrice / 10 ** currency.minorUnit,
					max_price: finalMaxPrice / 10 ** currency.minorUnit,
				} );

				// If the params have changed, lets update the filter URL.
				if ( window.location.href !== newUrl ) {
					changeUrl( newUrl );
				}
			}

			setMinPriceQuery( finalMinPrice );
			setMaxPriceQuery( finalMaxPrice );
		},
		[
			minConstraint,
			maxConstraint,
			setMinPriceQuery,
			setMaxPriceQuery,
			currency.minorUnit,
		]
	);

	// Updates the query after a short delay.
	const debouncedUpdateQuery = useDebouncedCallback( onSubmit, 500 );

	// Callback when slider or input fields are changed.
	const onChange = useCallback(
		( prices ) => {
			setIsUpdating( true );
			if ( prices[ 0 ] !== minPrice ) {
				setMinPrice( prices[ 0 ] );
			}
			if ( prices[ 1 ] !== maxPrice ) {
				setMaxPrice( prices[ 1 ] );
			}

			if (
				filteringForPhpTemplate &&
				hasSetFilterDefaultsFromUrl &&
				! attributes.showFilterButton
			) {
				debouncedUpdateQuery( prices[ 0 ], prices[ 1 ] );
			}
		},
		[
			minPrice,
			maxPrice,
			setMinPrice,
			setMaxPrice,
			filteringForPhpTemplate,
			hasSetFilterDefaultsFromUrl,
			debouncedUpdateQuery,
			attributes.showFilterButton,
		]
	);

	// Track price STATE changes - if state changes, update the query.
	useEffect( () => {
		if ( ! attributes.showFilterButton && ! filteringForPhpTemplate ) {
			debouncedUpdateQuery( minPrice, maxPrice );
		}
	}, [
		minPrice,
		maxPrice,
		attributes.showFilterButton,
		debouncedUpdateQuery,
		filteringForPhpTemplate,
	] );

	// Track price query/price constraint changes so the slider reflects current filters.
	const previousMinPriceQuery = usePrevious( minPriceQuery );
	const previousMaxPriceQuery = usePrevious( maxPriceQuery );
	const previousMinConstraint = usePrevious( minConstraint );
	const previousMaxConstraint = usePrevious( maxConstraint );
	useEffect( () => {
		if (
			! Number.isFinite( minPrice ) ||
			( minPriceQuery !== previousMinPriceQuery && // minPrice from query changed
				minPriceQuery !== minPrice ) || // minPrice from query doesn't match the UI min price
			( minConstraint !== previousMinConstraint && // minPrice from query changed
				minConstraint !== minPrice ) // minPrice from query doesn't match the UI min price
		) {
			setMinPrice(
				Number.isFinite( minPriceQuery ) ? minPriceQuery : minConstraint
			);
		}
		if (
			! Number.isFinite( maxPrice ) ||
			( maxPriceQuery !== previousMaxPriceQuery && // maxPrice from query changed
				maxPriceQuery !== maxPrice ) || // maxPrice from query doesn't match the UI max price
			( maxConstraint !== previousMaxConstraint && // maxPrice from query changed
				maxConstraint !== maxPrice ) // maxPrice from query doesn't match the UI max price
		) {
			setMaxPrice(
				Number.isFinite( maxPriceQuery ) ? maxPriceQuery : maxConstraint
			);
		}
	}, [
		minPrice,
		maxPrice,
		minPriceQuery,
		maxPriceQuery,
		minConstraint,
		maxConstraint,
		previousMinConstraint,
		previousMaxConstraint,
		previousMinPriceQuery,
		previousMaxPriceQuery,
	] );

	if ( ! hasFilterableProducts ) {
		setWrapperVisibility( false );
		return null;
	}

	if (
		! isLoading &&
		( minConstraint === null ||
			maxConstraint === null ||
			minConstraint === maxConstraint )
	) {
		setWrapperVisibility( false );
		return null;
	}

	const TagName =
		`h${ attributes.headingLevel }` as keyof JSX.IntrinsicElements;

	setWrapperVisibility( true );

	if ( ! isLoading && isUpdating ) {
		setIsUpdating( false );
	}

	const heading = (
		<TagName className="wc-block-price-filter__title">
			{ attributes.heading }
		</TagName>
	);

	const filterHeading =
		isLoading && isUpdating ? (
			<FilterTitlePlaceholder>{ heading }</FilterTitlePlaceholder>
		) : (
			heading
		);

	return (
		<>
			{ ! isEditor && attributes.heading && filterHeading }
			<div className="wc-block-price-slider">
				<PriceSlider
					minConstraint={ minConstraint }
					maxConstraint={ maxConstraint }
					minPrice={ minPrice }
					maxPrice={ maxPrice }
					currency={ currency }
					showInputFields={ attributes.showInputFields }
					inlineInput={ attributes.inlineInput }
					showFilterButton={ attributes.showFilterButton }
					onChange={ onChange }
					onSubmit={ () => onSubmit( minPrice, maxPrice ) }
					isLoading={ isLoading }
					isUpdating={ isUpdating }
					isEditor={ isEditor }
				/>
			</div>
		</>
	);
};

PriceFilterBlock.propTypes = {
	/**
	 * The attributes for this block.
	 */
	attributes: PropTypes.object.isRequired,
	/**
	 * Whether it's in the editor or frontend display.
	 */
	isEditor: PropTypes.bool,
};

export default PriceFilterBlock;
