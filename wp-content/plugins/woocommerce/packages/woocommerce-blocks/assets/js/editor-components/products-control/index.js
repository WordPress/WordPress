/**
 * External dependencies
 */
import { __, _n, sprintf } from '@wordpress/i18n';
import { SearchListControl } from '@woocommerce/editor-components/search-list-control';
import PropTypes from 'prop-types';
import { withSearchedProducts } from '@woocommerce/block-hocs';
import ErrorMessage from '@woocommerce/editor-components/error-placeholder/error-message';
import { decodeEntities } from '@wordpress/html-entities';

/**
 * The products control exposes a custom selector for searching and selecting
 * products.
 *
 * @param {Object}   props           Component props.
 * @param {string}   props.error
 * @param {Function} props.onChange  Callback fired when the selected item changes
 * @param {Function} props.onSearch  Callback fired when a search is triggered
 * @param {Array}    props.selected  An array of selected products.
 * @param {Array}    props.products  An array of products to select from.
 * @param {boolean}  props.isLoading Whether or not the products are being loaded.
 * @param {boolean}  props.isCompact Whether or not the control should have compact styles.
 *
 * @return {Function} A functional component.
 */
const ProductsControl = ( {
	error,
	onChange,
	onSearch,
	selected,
	products,
	isLoading,
	isCompact,
} ) => {
	const messages = {
		clear: __( 'Clear all products', 'woocommerce' ),
		list: __( 'Products', 'woocommerce' ),
		noItems: __(
			"Your store doesn't have any products.",
			'woocommerce'
		),
		search: __(
			'Search for products to display',
			'woocommerce'
		),
		selected: ( n ) =>
			sprintf(
				/* translators: %d is the number of selected products. */
				_n(
					'%d product selected',
					'%d products selected',
					n,
					'woocommerce'
				),
				n
			),
		updated: __(
			'Product search results updated.',
			'woocommerce'
		),
	};

	if ( error ) {
		return <ErrorMessage error={ error } />;
	}

	return (
		<SearchListControl
			className="woocommerce-products"
			list={ products.map( ( product ) => {
				const formattedSku = product.sku
					? ' (' + product.sku + ')'
					: '';
				return {
					...product,
					name: `${ decodeEntities(
						product.name
					) }${ formattedSku }`,
				};
			} ) }
			isCompact={ isCompact }
			isLoading={ isLoading }
			selected={ products.filter( ( { id } ) =>
				selected.includes( id )
			) }
			onSearch={ onSearch }
			onChange={ onChange }
			messages={ messages }
		/>
	);
};

ProductsControl.propTypes = {
	onChange: PropTypes.func.isRequired,
	onSearch: PropTypes.func,
	selected: PropTypes.array,
	products: PropTypes.array,
	isCompact: PropTypes.bool,
	isLoading: PropTypes.bool,
};

ProductsControl.defaultProps = {
	selected: [],
	products: [],
	isCompact: false,
	isLoading: true,
};

export default withSearchedProducts( ProductsControl );
