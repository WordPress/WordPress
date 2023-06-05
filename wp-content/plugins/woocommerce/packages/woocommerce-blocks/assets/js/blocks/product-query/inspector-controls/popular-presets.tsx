/**
 * External dependencies
 */
import { CustomSelectControl, PanelBody } from '@wordpress/components';
import { __ } from '@wordpress/i18n';

/**
 * Internal dependencies
 */
import { ProductQueryBlock, ProductQueryBlockQuery } from '../types';
import { setQueryAttribute } from '../utils';

const PRESETS = [
	{
		key: 'title/asc',
		name: __( 'Sorted by title', 'woo-gutenberg-products-block' ),
	},
	{ key: 'date/desc', name: __( 'Newest', 'woo-gutenberg-products-block' ) },
	{
		key: 'popularity/desc',
		name: __( 'Best Selling', 'woo-gutenberg-products-block' ),
	},
	{
		key: 'rating/desc',
		name: __( 'Top Rated', 'woo-gutenberg-products-block' ),
	},
];

export function PopularPresets( props: ProductQueryBlock ) {
	const { query } = props.attributes;

	return (
		<PanelBody
			className="woocommerce-product-query-panel__sort"
			title={ __( 'Popular Filters', 'woo-gutenberg-products-block' ) }
			initialOpen={ true }
		>
			<p>
				{ __(
					'Arrange products by popular pre-sets.',
					'woo-gutenberg-products-block'
				) }
			</p>
			<CustomSelectControl
				hideLabelFromVision={ true }
				label={ __(
					'Choose among these pre-sets',
					'woo-gutenberg-products-block'
				) }
				onChange={ ( option ) => {
					if ( ! option.selectedItem?.key ) return;

					const [ orderBy, order ] = option.selectedItem?.key?.split(
						'/'
					) as [
						ProductQueryBlockQuery[ 'orderBy' ],
						ProductQueryBlockQuery[ 'order' ]
					];

					setQueryAttribute( props, { order, orderBy } );
				} }
				options={ PRESETS }
				value={ PRESETS.find(
					( option ) =>
						option.key === `${ query.orderBy }/${ query.order }`
				) }
			/>
		</PanelBody>
	);
}
