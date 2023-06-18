/**
 * External dependencies
 */
import { __, _n, sprintf } from '@wordpress/i18n';
import { isEmpty } from '@woocommerce/types';
import PropTypes from 'prop-types';
import {
	SearchListControl,
	SearchListItem,
} from '@woocommerce/editor-components/search-list-control';
import { withInstanceId } from '@wordpress/compose';
import {
	withProductVariations,
	withSearchedProducts,
	withTransformSingleSelectToMultipleSelect,
} from '@woocommerce/block-hocs';
import ErrorMessage from '@woocommerce/editor-components/error-placeholder/error-message';
import classNames from 'classnames';
import ExpandableSearchListItem from '@woocommerce/editor-components/expandable-search-list-item/expandable-search-list-item.tsx';

/**
 * Internal dependencies
 */
import './style.scss';

const messages = {
	list: __( 'Products', 'woocommerce' ),
	noItems: __(
		"Your store doesn't have any products.",
		'woocommerce'
	),
	search: __(
		'Search for a product to display',
		'woocommerce'
	),
	updated: __(
		'Product search results updated.',
		'woocommerce'
	),
};

const ProductControl = ( {
	expandedProduct,
	error,
	instanceId,
	isCompact,
	isLoading,
	onChange,
	onSearch,
	products,
	renderItem,
	selected,
	showVariations,
	variations,
	variationsLoading,
} ) => {
	const renderItemWithVariations = ( args ) => {
		const { item, search, depth = 0, isSelected, onSelect } = args;
		const variationsCount =
			item.variations && Array.isArray( item.variations )
				? item.variations.length
				: 0;
		const classes = classNames(
			'woocommerce-search-product__item',
			'woocommerce-search-list__item',
			`depth-${ depth }`,
			'has-count',
			{
				'is-searching': search.length > 0,
				'is-skip-level': depth === 0 && item.parent !== 0,
				'is-variable': variationsCount > 0,
			}
		);

		// Top level items custom rendering based on SearchListItem.
		if ( ! item.breadcrumbs.length ) {
			return (
				<ExpandableSearchListItem
					{ ...args }
					className={ classNames( classes, {
						'is-selected': isSelected,
					} ) }
					isSelected={ isSelected }
					item={ item }
					onSelect={ () => {
						return () => {
							onSelect( item )();
						};
					} }
					isLoading={ isLoading || variationsLoading }
					countLabel={
						item.variations.length > 0
							? sprintf(
									/* translators: %1$d is the number of variations of a product product. */
									__(
										'%1$d variations',
										'woocommerce'
									),
									item.variations.length
							  )
							: null
					}
					name={ `products-${ instanceId }` }
					aria-label={ sprintf(
						/* translators: %1$s is the product name, %2$d is the number of variations of that product. */
						_n(
							'%1$s, has %2$d variation',
							'%1$s, has %2$d variations',
							item.variations.length,
							'woocommerce'
						),
						item.name,
						item.variations.length
					) }
				/>
			);
		}

		const itemArgs = isEmpty( item.variation )
			? args
			: {
					...args,
					item: {
						...args.item,
						name: item.variation,
					},
					'aria-label': `${ item.breadcrumbs[ 0 ] }: ${ item.variation }`,
			  };

		return (
			<SearchListItem
				{ ...itemArgs }
				className={ classes }
				name={ `variations-${ instanceId }` }
			/>
		);
	};

	const getRenderItemFunc = () => {
		if ( renderItem ) {
			return renderItem;
		} else if ( showVariations ) {
			return renderItemWithVariations;
		}
		return null;
	};

	if ( error ) {
		return <ErrorMessage error={ error } />;
	}

	const currentVariations =
		variations && variations[ expandedProduct ]
			? variations[ expandedProduct ]
			: [];
	const currentList = [ ...products, ...currentVariations ];

	return (
		<SearchListControl
			className="woocommerce-products"
			list={ currentList }
			isCompact={ isCompact }
			isLoading={ isLoading }
			isSingle
			selected={ currentList.filter( ( { id } ) =>
				selected.includes( id )
			) }
			onChange={ onChange }
			renderItem={ getRenderItemFunc() }
			onSearch={ onSearch }
			messages={ messages }
			isHierarchical
		/>
	);
};

ProductControl.propTypes = {
	/**
	 * Callback to update the selected products.
	 */
	onChange: PropTypes.func.isRequired,
	isCompact: PropTypes.bool,
	/**
	 * The ID of the currently expanded product.
	 */
	expandedProduct: PropTypes.number,
	/**
	 * Callback to search products by their name.
	 */
	onSearch: PropTypes.func,
	/**
	 * Query args to pass to getProducts.
	 */
	queryArgs: PropTypes.object,
	/**
	 * Callback to render each item in the selection list, allows any custom object-type rendering.
	 */
	renderItem: PropTypes.func,
	/**
	 * The ID of the currently selected item (product or variation).
	 */
	selected: PropTypes.arrayOf( PropTypes.number ),
	/**
	 * Whether to show variations in the list of items available.
	 */
	showVariations: PropTypes.bool,
};

ProductControl.defaultProps = {
	isCompact: false,
	expandedProduct: null,
	selected: [],
	showVariations: false,
};

export default withTransformSingleSelectToMultipleSelect(
	withSearchedProducts(
		withProductVariations( withInstanceId( ProductControl ) )
	)
);
