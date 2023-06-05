/**
 * External dependencies
 */
import { __, sprintf } from '@wordpress/i18n';
import { speak } from '@wordpress/a11y';
import { Icon, chevronDown } from '@wordpress/icons';
import Rating, {
	RatingValues,
} from '@woocommerce/base-components/product-rating';
import {
	usePrevious,
	useShallowEqual,
	useBorderProps,
} from '@woocommerce/base-hooks';
import {
	useQueryStateByKey,
	useQueryStateByContext,
	useCollectionData,
} from '@woocommerce/base-context/hooks';
import { getSettingWithCoercion } from '@woocommerce/settings';
import { isBoolean, isObject, objectHasProp } from '@woocommerce/types';
import isShallowEqual from '@wordpress/is-shallow-equal';
import { useState, useCallback, useMemo, useEffect } from '@wordpress/element';
import CheckboxList from '@woocommerce/base-components/checkbox-list';
import FilterSubmitButton from '@woocommerce/base-components/filter-submit-button';
import FilterResetButton from '@woocommerce/base-components/filter-reset-button';
import FormTokenField from '@woocommerce/base-components/form-token-field';
import { addQueryArgs, removeQueryArgs } from '@wordpress/url';
import { changeUrl, normalizeQueryParams } from '@woocommerce/utils';
import classnames from 'classnames';
import { difference } from 'lodash';
import type { ReactElement } from 'react';

/**
 * Internal dependencies
 */
import { previewOptions } from './preview';
import './style.scss';
import { Attributes } from './types';
import { formatSlug, getActiveFilters, generateUniqueId } from './utils';
import { useSetWraperVisibility } from '../filter-wrapper/context';

export const QUERY_PARAM_KEY = 'rating_filter';

const translations = {
	ratingAdded: ( rating: string ): string =>
		sprintf(
			/* translators: %s is referring to the average rating value */
			__(
				'Rated %s out of 5 filter added.',
				'woo-gutenberg-products-block'
			),
			rating
		),
	ratingRemoved: ( rating: string ): string =>
		sprintf(
			/* translators: %s is referring to the average rating value */
			__(
				'Rated %s out of 5 filter added.',
				'woo-gutenberg-products-block'
			),
			rating
		),
};

/**
 * Component displaying a rating filter.
 */
const RatingFilterBlock = ( {
	attributes: blockAttributes,
	isEditor,
	noRatingsNotice = null,
}: {
	attributes: Attributes;
	isEditor: boolean;
	noRatingsNotice?: ReactElement | null;
} ) => {
	const setWrapperVisibility = useSetWraperVisibility();

	const filteringForPhpTemplate = getSettingWithCoercion(
		'is_rendering_php_template',
		false,
		isBoolean
	);
	const [ hasSetFilterDefaultsFromUrl, setHasSetFilterDefaultsFromUrl ] =
		useState( false );

	const [ queryState ] = useQueryStateByContext();

	const { results: filteredCounts, isLoading: filteredCountsLoading } =
		useCollectionData( {
			queryRating: true,
			queryState,
			isEditor,
		} );

	const [ displayedOptions, setDisplayedOptions ] = useState(
		blockAttributes.isPreview ? previewOptions : []
	);

	const isLoading =
		! blockAttributes.isPreview &&
		filteredCountsLoading &&
		displayedOptions.length === 0;

	const isDisabled = ! blockAttributes.isPreview && filteredCountsLoading;

	const initialFilters = useMemo(
		() => getActiveFilters( 'rating_filter' ),
		[]
	);

	const [ checked, setChecked ] = useState( initialFilters );

	const [ productRatingsQuery, setProductRatingsQuery ] = useQueryStateByKey(
		'rating',
		initialFilters
	);

	/*
		FormTokenField forces the dropdown to reopen on reset, so we create a unique ID to use as the components key.
		This will force the component to remount on reset when we change this value.
		More info: https://github.com/woocommerce/woocommerce-blocks/pull/6920#issuecomment-1222402482
	 */
	const [ remountKey, setRemountKey ] = useState( generateUniqueId() );

	const borderProps = useBorderProps( blockAttributes );
	const [ displayNoProductRatingsNotice, setDisplayNoProductRatingsNotice ] =
		useState( false );

	/**
	 * Used to redirect the page when filters are changed so templates using the Classic Template block can filter.
	 *
	 * @param {Array} checkedRatings Array of checked ratings.
	 */
	const updateFilterUrl = ( checkedRatings: string[] ) => {
		if ( ! window ) {
			return;
		}

		if ( checkedRatings.length === 0 ) {
			const url = removeQueryArgs(
				window.location.href,
				QUERY_PARAM_KEY
			);

			if ( url !== normalizeQueryParams( window.location.href ) ) {
				changeUrl( url );
			}

			return;
		}

		const newUrl = addQueryArgs( window.location.href, {
			[ QUERY_PARAM_KEY ]: checkedRatings.join( ',' ),
		} );

		if ( newUrl === normalizeQueryParams( window.location.href ) ) {
			return;
		}

		changeUrl( newUrl );
	};

	const multiple = blockAttributes.selectType !== 'single';

	const showChevron = multiple
		? ! isLoading && checked.length < displayedOptions.length
		: ! isLoading && checked.length === 0;

	const onSubmit = useCallback(
		( checkedOptions ) => {
			if ( isEditor ) {
				return;
			}
			if ( checkedOptions && ! filteringForPhpTemplate ) {
				setProductRatingsQuery( checkedOptions );
			}

			updateFilterUrl( checkedOptions );
		},
		[ isEditor, setProductRatingsQuery, filteringForPhpTemplate ]
	);

	// Track checked STATE changes - if state changes, update the query.
	useEffect( () => {
		if ( ! blockAttributes.showFilterButton ) {
			onSubmit( checked );
		}
	}, [ blockAttributes.showFilterButton, checked, onSubmit ] );

	const checkedQuery = useMemo( () => {
		return productRatingsQuery;
	}, [ productRatingsQuery ] );

	const currentCheckedQuery = useShallowEqual( checkedQuery );
	const previousCheckedQuery = usePrevious( currentCheckedQuery );
	// Track Rating query changes so the block reflects current filters.
	useEffect( () => {
		if (
			! isShallowEqual( previousCheckedQuery, currentCheckedQuery ) && // Checked query changed.
			! isShallowEqual( checked, currentCheckedQuery ) // Checked query doesn't match the UI.
		) {
			setChecked( currentCheckedQuery );
		}
	}, [ checked, currentCheckedQuery, previousCheckedQuery ] );

	/**
	 * Try get the rating filter from the URL.
	 */
	useEffect( () => {
		if ( ! hasSetFilterDefaultsFromUrl ) {
			setProductRatingsQuery( initialFilters );
			setHasSetFilterDefaultsFromUrl( true );
		}
	}, [
		setProductRatingsQuery,
		hasSetFilterDefaultsFromUrl,
		setHasSetFilterDefaultsFromUrl,
		initialFilters,
	] );

	/**
	 * Compare intersection of all ratings and filtered counts to get a list of options to display.
	 */
	useEffect( () => {
		/**
		 * Checks if a status slug is in the query state.
		 *
		 * @param {string} queryStatus The status slug to check.
		 */

		if ( filteredCountsLoading || blockAttributes.isPreview ) {
			return;
		}

		const orderedRatings =
			! filteredCountsLoading &&
			objectHasProp( filteredCounts, 'rating_counts' ) &&
			Array.isArray( filteredCounts.rating_counts )
				? [ ...filteredCounts.rating_counts ].reverse()
				: [];

		if ( isEditor && orderedRatings.length === 0 ) {
			setDisplayedOptions( previewOptions );
			setDisplayNoProductRatingsNotice( true );
			return;
		}

		const newOptions = orderedRatings
			.filter(
				( item ) => isObject( item ) && Object.keys( item ).length > 0
			)
			.map( ( item ) => {
				return {
					label: (
						<Rating
							key={ item?.rating }
							rating={ item?.rating }
							ratedProductsCount={
								blockAttributes.showCounts ? item?.count : null
							}
						/>
					),
					value: item?.rating?.toString(),
				};
			} );

		setDisplayedOptions( newOptions );
		setRemountKey( generateUniqueId() );
	}, [
		blockAttributes.showCounts,
		blockAttributes.isPreview,
		filteredCounts,
		filteredCountsLoading,
		productRatingsQuery,
		isEditor,
	] );

	/**
	 * When a checkbox in the list changes, update state.
	 */
	const onClick = useCallback(
		( checkedValue: string ) => {
			const previouslyChecked = checked.includes( checkedValue );

			if ( ! multiple ) {
				const newChecked = previouslyChecked ? [] : [ checkedValue ];
				speak(
					previouslyChecked
						? translations.ratingRemoved( checkedValue )
						: translations.ratingAdded( checkedValue )
				);
				setChecked( newChecked );
				return;
			}

			if ( previouslyChecked ) {
				const newChecked = checked.filter(
					( value ) => value !== checkedValue
				);
				speak( translations.ratingRemoved( checkedValue ) );
				setChecked( newChecked );
				return;
			}

			const newChecked = [ ...checked, checkedValue ].sort(
				( a, b ) => Number( b ) - Number( a )
			);
			speak( translations.ratingAdded( checkedValue ) );
			setChecked( newChecked );
		},
		[ checked, multiple ]
	);

	if ( ! filteredCountsLoading && displayedOptions.length === 0 ) {
		setWrapperVisibility( false );
		return null;
	}

	const hasFilterableProducts = getSettingWithCoercion(
		'has_filterable_products',
		false,
		isBoolean
	);

	if ( ! hasFilterableProducts ) {
		setWrapperVisibility( false );
		return null;
	}

	setWrapperVisibility( true );

	return (
		<>
			{ displayNoProductRatingsNotice && noRatingsNotice }
			<div
				className={ classnames(
					'wc-block-rating-filter',
					`style-${ blockAttributes.displayStyle }`,
					{
						'is-loading': isLoading,
					}
				) }
			>
				{ blockAttributes.displayStyle === 'dropdown' ? (
					<>
						<FormTokenField
							key={ remountKey }
							className={ classnames( borderProps.className, {
								'single-selection': ! multiple,
								'is-loading': isLoading,
							} ) }
							style={ {
								...borderProps.style,
								borderStyle: 'none',
							} }
							suggestions={ displayedOptions
								.filter(
									( option ) =>
										! checked.includes( option.value )
								)
								.map( ( option ) => option.value ) }
							disabled={ isLoading }
							placeholder={ __(
								'Select Rating',
								'woo-gutenberg-products-block'
							) }
							onChange={ ( tokens: string[] ) => {
								if ( ! multiple && tokens.length > 1 ) {
									tokens = [ tokens[ tokens.length - 1 ] ];
								}

								tokens = tokens.map( ( token ) => {
									const displayOption = displayedOptions.find(
										( option ) => option.value === token
									);

									return displayOption
										? displayOption.value
										: token;
								} );

								const added = difference( tokens, checked );

								if ( added.length === 1 ) {
									return onClick( added[ 0 ] );
								}

								const removed = difference( checked, tokens );
								if ( removed.length === 1 ) {
									onClick( removed[ 0 ] );
								}
							} }
							value={ checked }
							// eslint-disable-next-line @typescript-eslint/ban-ts-comment
							// @ts-ignore - FormTokenField doesn't accept custom components, forcing it here to display component
							displayTransform={ ( value ) => {
								const resultWithZeroCount = {
									value,
									label: (
										<Rating
											key={
												Number( value ) as RatingValues
											}
											rating={
												Number( value ) as RatingValues
											}
											ratedProductsCount={ 0 }
										/>
									),
								};
								const resultWithNonZeroCount =
									displayedOptions.find(
										( option ) => option.value === value
									);

								const displayedResult =
									resultWithNonZeroCount ||
									resultWithZeroCount;

								const { label, value: rawValue } =
									displayedResult;

								// A label - JSX component - is extended with faked string methods to allow using JSX element as an option in FormTokenField
								const extendedLabel = Object.assign(
									{},
									label,
									{
										toLocaleLowerCase: () => rawValue,
										substring: (
											start: number,
											end: number
										) =>
											start === 0 && end === 1
												? label
												: '',
									}
								);
								return extendedLabel;
							} }
							saveTransform={ formatSlug }
							messages={ {
								added: __(
									'Rating filter added.',
									'woo-gutenberg-products-block'
								),
								removed: __(
									'Rating filter removed.',
									'woo-gutenberg-products-block'
								),
								remove: __(
									'Remove rating filter.',
									'woo-gutenberg-products-block'
								),
								__experimentalInvalid: __(
									'Invalid rating filter.',
									'woo-gutenberg-products-block'
								),
							} }
						/>
						{ showChevron && (
							<Icon icon={ chevronDown } size={ 30 } />
						) }
					</>
				) : (
					<CheckboxList
						className={ 'wc-block-rating-filter-list' }
						options={ displayedOptions }
						checked={ checked }
						onChange={ ( item ) => {
							onClick( item.toString() );
						} }
						isLoading={ isLoading }
						isDisabled={ isDisabled }
					/>
				) }
			</div>
			{
				<div className="wc-block-rating-filter__actions">
					{ ( checked.length > 0 || isEditor ) && ! isLoading && (
						<FilterResetButton
							onClick={ () => {
								setChecked( [] );
								setProductRatingsQuery( [] );
								onSubmit( [] );
							} }
							screenReaderLabel={ __(
								'Reset rating filter',
								'woo-gutenberg-products-block'
							) }
						/>
					) }
					{ blockAttributes.showFilterButton && (
						<FilterSubmitButton
							className="wc-block-rating-filter__button"
							isLoading={ isLoading }
							disabled={ isLoading || isDisabled }
							onClick={ () => onSubmit( checked ) }
						/>
					) }
				</div>
			}
		</>
	);
};

export default RatingFilterBlock;
