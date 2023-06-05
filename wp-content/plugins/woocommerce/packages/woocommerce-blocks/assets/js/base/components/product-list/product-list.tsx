/**
 * External dependencies
 */
import { __, _n, sprintf } from '@wordpress/i18n';
import { isEqual } from 'lodash';
import classnames from 'classnames';
import Pagination from '@woocommerce/base-components/pagination';
import { useEffect } from '@wordpress/element';
import { usePrevious } from '@woocommerce/base-hooks';
import {
	useStoreEvents,
	useStoreProducts,
	useSynchronizedQueryState,
	useQueryStateByKey,
} from '@woocommerce/base-context/hooks';
import withScrollToTop from '@woocommerce/base-hocs/with-scroll-to-top';
import { useInnerBlockLayoutContext } from '@woocommerce/shared-context';
import { speak } from '@wordpress/a11y';

/**
 * Internal dependencies
 */
import NoProducts from './no-products';
import NoMatchingProducts from './no-matching-products';
import ProductSortSelect from './product-sort-select';
import ProductListItem from './product-list-item';
import {
	GenerateQuery,
	GetSortArgs,
	Query,
	AreQueryTotalsDifferent,
	ProductListProps,
	TotalQuery,
} from './types';
import './style.scss';

const getSortArgs: GetSortArgs = ( orderName ) => {
	switch ( orderName ) {
		case 'menu_order':
		case 'popularity':
		case 'rating':
		case 'price':
			return {
				orderby: orderName,
				order: 'asc',
			};
		case 'price-desc':
			return {
				orderby: 'price',
				order: 'desc',
			};
		case 'date':
			return {
				orderby: 'date',
				order: 'desc',
			};
	}
};

const generateQuery: GenerateQuery = ( {
	sortValue,
	currentPage,
	attributes,
} ) => {
	const { columns, rows } = attributes;

	return {
		...getSortArgs( sortValue ),
		catalog_visibility: 'catalog',
		per_page: columns * rows,
		page: currentPage,
	};
};

/**
 * Given a query state, returns the same query without the attributes related to
 * pagination and sorting.
 *
 * @param {Object} query Query to extract the attributes from.
 *
 * @return {Object} Same query without pagination and sorting attributes.
 */

const extractPaginationAndSortAttributes = ( query: Query ): TotalQuery => {
	/* eslint-disable-next-line no-unused-vars */
	const { order, orderby, page, per_page: perPage, ...totalQuery } = query;
	return totalQuery || {};
};

const announceLoadingCompletion = ( totalProducts: number ): void => {
	if ( ! Number.isFinite( totalProducts ) ) {
		return;
	}

	if ( totalProducts === 0 ) {
		speak( __( 'No products found', 'woo-gutenberg-products-block' ) );
	} else {
		speak(
			sprintf(
				/* translators: %s is an integer higher than 0 (1, 2, 3...) */
				_n(
					'%d product found',
					'%d products found',
					totalProducts,
					'woo-gutenberg-products-block'
				),
				totalProducts
			)
		);
	}
};

const areQueryTotalsDifferent: AreQueryTotalsDifferent = (
	{ totalQuery: nextQuery, totalProducts: nextProducts },
	{ totalQuery: currentQuery } = {}
) => ! isEqual( nextQuery, currentQuery ) && Number.isFinite( nextProducts );

const ProductList = ( {
	attributes,
	currentPage,
	onPageChange,
	onSortChange,
	sortValue,
	scrollToTop,
}: ProductListProps ): JSX.Element => {
	// These are possible filters.
	const [ productAttributes, setProductAttributes ] = useQueryStateByKey(
		'attributes',
		[]
	);
	const [ productStockStatus, setProductStockStatus ] = useQueryStateByKey(
		'stock_status',
		[]
	);
	const [ productRating, setProductRating ] = useQueryStateByKey(
		'rating',
		[]
	);

	const [ minPrice, setMinPrice ] = useQueryStateByKey( 'min_price' );
	const [ maxPrice, setMaxPrice ] = useQueryStateByKey( 'max_price' );

	const [ queryState ] = useSynchronizedQueryState(
		generateQuery( {
			attributes,
			sortValue,
			currentPage,
		} )
	);
	const { products, totalProducts, productsLoading } =
		useStoreProducts( queryState );
	const { parentClassName, parentName } = useInnerBlockLayoutContext();
	const totalQuery = extractPaginationAndSortAttributes( queryState );
	const { dispatchStoreEvent } = useStoreEvents();

	// Only update previous query totals if the query is different and the total number of products is a finite number.
	const previousQueryTotals = usePrevious(
		{ totalQuery, totalProducts },
		areQueryTotalsDifferent
	);

	// If the product list changes, trigger an event.
	useEffect( () => {
		dispatchStoreEvent( 'product-list-render', {
			products,
			listName: parentName,
		} );
	}, [ products, parentName, dispatchStoreEvent ] );

	// If query state (excluding pagination/sorting attributes) changed, reset pagination to the first page.
	useEffect( () => {
		if ( isEqual( totalQuery, previousQueryTotals?.totalQuery ) ) {
			return;
		}
		onPageChange( 1 );

		// Make sure there was a previous query, so we don't announce it on page load.
		if ( previousQueryTotals?.totalQuery ) {
			announceLoadingCompletion( totalProducts );
		}
	}, [
		previousQueryTotals?.totalQuery,
		totalProducts,
		onPageChange,
		totalQuery,
	] );

	const onPaginationChange = ( newPage: number ) => {
		scrollToTop( { focusableSelector: 'a, button' } );
		onPageChange( newPage );
	};

	const getClassnames = () => {
		const { columns, rows, alignButtons, align } = attributes;
		const alignClass = typeof align !== 'undefined' ? 'align' + align : '';

		return classnames(
			parentClassName,
			alignClass,
			'has-' + columns + '-columns',
			{
				'has-multiple-rows': rows > 1,
				'has-aligned-buttons': alignButtons,
			}
		);
	};

	const { contentVisibility } = attributes;
	const perPage = attributes.columns * attributes.rows;
	const totalPages =
		! Number.isFinite( totalProducts ) &&
		Number.isFinite( previousQueryTotals?.totalProducts ) &&
		isEqual( totalQuery, previousQueryTotals?.totalQuery )
			? Math.ceil( ( previousQueryTotals?.totalProducts || 0 ) / perPage )
			: Math.ceil( totalProducts / perPage );
	const listProducts = products.length
		? products
		: Array.from( { length: perPage } );
	const hasProducts = products.length !== 0 || productsLoading;
	const hasFilters =
		productAttributes.length > 0 ||
		productStockStatus.length > 0 ||
		productRating.length > 0 ||
		Number.isFinite( minPrice ) ||
		Number.isFinite( maxPrice );

	return (
		<div className={ getClassnames() }>
			{ contentVisibility?.orderBy && hasProducts && (
				<ProductSortSelect
					onChange={ onSortChange }
					value={ sortValue }
				/>
			) }
			{ ! hasProducts && hasFilters && (
				<NoMatchingProducts
					resetCallback={ () => {
						setProductAttributes( [] );
						setProductStockStatus( [] );
						setProductRating( [] );
						setMinPrice( null );
						setMaxPrice( null );
					} }
				/>
			) }
			{ ! hasProducts && ! hasFilters && <NoProducts /> }
			{ hasProducts && (
				<ul
					className={ classnames( `${ parentClassName }__products`, {
						'is-loading-products': productsLoading,
					} ) }
				>
					{ listProducts.map( ( product = {}, i: number ) => (
						<ProductListItem
							key={ product.id || i }
							attributes={ attributes }
							product={ product }
						/>
					) ) }
				</ul>
			) }
			{ totalPages > 1 && (
				<Pagination
					currentPage={ currentPage }
					onPageChange={ onPaginationChange }
					totalPages={ totalPages }
				/>
			) }
		</div>
	);
};

export default withScrollToTop( ProductList );
