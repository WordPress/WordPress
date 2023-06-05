/**
 * External dependencies
 */
import { __, _n, sprintf } from '@wordpress/i18n';
import { Component } from '@wordpress/element';
import { debounce } from 'lodash';
import PropTypes from 'prop-types';
import {
	SearchListControl,
	SearchListItem,
} from '@woocommerce/editor-components/search-list-control';
import { SelectControl } from '@wordpress/components';
import { getSetting } from '@woocommerce/settings';
import classNames from 'classnames';

/**
 * Internal dependencies
 */
import { getProductTags } from '../utils';
import './style.scss';

/**
 * Component to handle searching and selecting product tags.
 */
class ProductTagControl extends Component {
	constructor() {
		super( ...arguments );
		this.state = {
			list: [],
			loading: true,
		};
		this.renderItem = this.renderItem.bind( this );
		this.debouncedOnSearch = debounce( this.onSearch.bind( this ), 400 );
	}

	componentDidMount() {
		const { selected } = this.props;

		getProductTags( { selected } )
			.then( ( list ) => {
				this.setState( { list, loading: false } );
			} )
			.catch( () => {
				this.setState( { list: [], loading: false } );
			} );
	}

	onSearch( search ) {
		const { selected } = this.props;
		this.setState( { loading: true } );

		getProductTags( { selected, search } )
			.then( ( list ) => {
				this.setState( { list, loading: false } );
			} )
			.catch( () => {
				this.setState( { list: [], loading: false } );
			} );
	}

	renderItem( args ) {
		const { item, search, depth = 0 } = args;

		const accessibleName = ! item.breadcrumbs.length
			? item.name
			: `${ item.breadcrumbs.join( ', ' ) }, ${ item.name }`;

		return (
			<SearchListItem
				className={ classNames(
					'woocommerce-product-tags__item',
					'has-count',
					{
						'is-searching': search.length > 0,
						'is-skip-level': depth === 0 && item.parent !== 0,
					}
				) }
				{ ...args }
				aria-label={ sprintf(
					/* translators: %1$d is the count of products, %2$s is the name of the tag. */
					_n(
						'%1$d product tagged as %2$s',
						'%1$d products tagged as %2$s',
						item.count,
						'woocommerce'
					),
					item.count,
					accessibleName
				) }
			/>
		);
	}

	render() {
		const { list, loading } = this.state;
		const { isCompact, onChange, onOperatorChange, operator, selected } =
			this.props;

		const messages = {
			clear: __(
				'Clear all product tags',
				'woocommerce'
			),
			list: __( 'Product Tags', 'woocommerce' ),
			noItems: __(
				'You have not set up any product tags on your store.',
				'woocommerce'
			),
			search: __(
				'Search for product tags',
				'woocommerce'
			),
			selected: ( n ) =>
				sprintf(
					/* translators: %d is the count of selected tags. */
					_n(
						'%d tag selected',
						'%d tags selected',
						n,
						'woocommerce'
					),
					n
				),
			updated: __(
				'Tag search results updated.',
				'woocommerce'
			),
		};

		const limitTags = getSetting( 'limitTags', false );

		return (
			<>
				<SearchListControl
					className="woocommerce-product-tags"
					list={ list }
					isLoading={ loading }
					selected={ selected
						.map( ( id ) =>
							list.find( ( listItem ) => listItem.id === id )
						)
						.filter( Boolean ) }
					onChange={ onChange }
					onSearch={ limitTags ? this.debouncedOnSearch : null }
					renderItem={ this.renderItem }
					messages={ messages }
					isCompact={ isCompact }
					isHierarchical
				/>
				{ !! onOperatorChange && (
					<div hidden={ selected.length < 2 }>
						<SelectControl
							className="woocommerce-product-tags__operator"
							label={ __(
								'Display products matching',
								'woocommerce'
							) }
							help={ __(
								'Pick at least two tags to use this setting.',
								'woocommerce'
							) }
							value={ operator }
							onChange={ onOperatorChange }
							options={ [
								{
									label: __(
										'Any selected tags',
										'woocommerce'
									),
									value: 'any',
								},
								{
									label: __(
										'All selected tags',
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
	}
}

ProductTagControl.propTypes = {
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
	 * The list of currently selected tags.
	 */
	selected: PropTypes.array.isRequired,
	isCompact: PropTypes.bool,
};

ProductTagControl.defaultProps = {
	isCompact: false,
	operator: 'any',
};

export default ProductTagControl;
