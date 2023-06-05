/**
 * External dependencies
 */
import { __, _n, sprintf } from '@wordpress/i18n';
import PropTypes from 'prop-types';
import {
	SearchListControl,
	SearchListItem,
} from '@woocommerce/editor-components/search-list-control';
import { SelectControl } from '@wordpress/components';
import { withCategories } from '@woocommerce/block-hocs';
import ErrorMessage from '@woocommerce/editor-components/error-placeholder/error-message';
import classNames from 'classnames';

/**
 * Internal dependencies
 */
import './style.scss';

/**
 * @param {Object}    props
 * @param {string=}   props.categories
 * @param {boolean=}  props.isLoading
 * @param {string=}   props.error
 * @param {Function}  props.onChange
 * @param {Function=} props.onOperatorChange
 * @param {string=}   props.operator
 * @param {number[]}  props.selected
 * @param {boolean=}  props.isCompact
 * @param {boolean=}  props.isSingle
 * @param {boolean=}  props.showReviewCount
 */
const ProductCategoryControl = ( {
	categories,
	error,
	isLoading,
	onChange,
	onOperatorChange,
	operator,
	selected,
	isCompact,
	isSingle,
	showReviewCount,
} ) => {
	const renderItem = ( args ) => {
		const { item, search, depth = 0 } = args;

		const accessibleName = ! item.breadcrumbs.length
			? item.name
			: `${ item.breadcrumbs.join( ', ' ) }, ${ item.name }`;

		const listItemAriaLabel = showReviewCount
			? sprintf(
					/* translators: %1$s is the item name, %2$d is the count of reviews for the item. */
					_n(
						'%1$s, has %2$d review',
						'%1$s, has %2$d reviews',
						item.review_count,
						'woocommerce'
					),
					accessibleName,
					item.review_count
			  )
			: sprintf(
					/* translators: %1$s is the item name, %2$d is the count of products for the item. */
					_n(
						'%1$s, has %2$d product',
						'%1$s, has %2$d products',
						item.count,
						'woocommerce'
					),
					accessibleName,
					item.count
			  );

		const listItemCountLabel = showReviewCount
			? sprintf(
					/* translators: %d is the count of reviews. */
					_n(
						'%d review',
						'%d reviews',
						item.review_count,
						'woocommerce'
					),
					item.review_count
			  )
			: sprintf(
					/* translators: %d is the count of products. */
					_n(
						'%d product',
						'%d products',
						item.count,
						'woocommerce'
					),
					item.count
			  );
		return (
			<SearchListItem
				className={ classNames(
					'woocommerce-product-categories__item',
					'has-count',
					{
						'is-searching': search.length > 0,
						'is-skip-level': depth === 0 && item.parent !== 0,
					}
				) }
				{ ...args }
				countLabel={ listItemCountLabel }
				aria-label={ listItemAriaLabel }
			/>
		);
	};

	const messages = {
		clear: __(
			'Clear all product categories',
			'woocommerce'
		),
		list: __( 'Product Categories', 'woocommerce' ),
		noItems: __(
			"Your store doesn't have any product categories.",
			'woocommerce'
		),
		search: __(
			'Search for product categories',
			'woocommerce'
		),
		selected: ( n ) =>
			sprintf(
				/* translators: %d is the count of selected categories. */
				_n(
					'%d category selected',
					'%d categories selected',
					n,
					'woocommerce'
				),
				n
			),
		updated: __(
			'Category search results updated.',
			'woocommerce'
		),
	};

	if ( error ) {
		return <ErrorMessage error={ error } />;
	}

	return (
		<>
			<SearchListControl
				className="woocommerce-product-categories"
				list={ categories }
				isLoading={ isLoading }
				selected={ selected
					.map( ( id ) =>
						categories.find( ( category ) => category.id === id )
					)
					.filter( Boolean ) }
				onChange={ onChange }
				renderItem={ renderItem }
				messages={ messages }
				isCompact={ isCompact }
				isHierarchical
				isSingle={ isSingle }
			/>
			{ !! onOperatorChange && (
				<div hidden={ selected.length < 2 }>
					<SelectControl
						className="woocommerce-product-categories__operator"
						label={ __(
							'Display products matching',
							'woocommerce'
						) }
						help={ __(
							'Pick at least two categories to use this setting.',
							'woocommerce'
						) }
						value={ operator }
						onChange={ onOperatorChange }
						options={ [
							{
								label: __(
									'Any selected categories',
									'woocommerce'
								),
								value: 'any',
							},
							{
								label: __(
									'All selected categories',
									'woocommerce'
								),
								value: 'all',
							},
						] }
					/>
				</div>
			) }
		</>
	);
};

ProductCategoryControl.propTypes = {
	/**
	 * Callback to update the selected product categories.
	 */
	onChange: PropTypes.func.isRequired,
	/**
	 * Callback to update the category operator. If not passed in, setting is not used.
	 */
	onOperatorChange: PropTypes.func,
	/**
	 * Setting for whether products should match all or any selected categories.
	 */
	operator: PropTypes.oneOf( [ 'all', 'any' ] ),
	/**
	 * The list of currently selected category IDs.
	 */
	selected: PropTypes.array.isRequired,
	isCompact: PropTypes.bool,
	/**
	 * Allow only a single selection. Defaults to false.
	 */
	isSingle: PropTypes.bool,
};

ProductCategoryControl.defaultProps = {
	operator: 'any',
	isCompact: false,
	isSingle: false,
};

export default withCategories( ProductCategoryControl );
