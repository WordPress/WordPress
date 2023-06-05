/**
 * External dependencies
 */
import { __ } from '@wordpress/i18n';
import { useEffect, useState } from '@wordpress/element';
import ProductAttributeTermControl from '@woocommerce/editor-components/product-attribute-term-control';
import {
	ExternalLink,
	// eslint-disable-next-line @wordpress/no-unsafe-wp-apis
	__experimentalToolsPanelItem as ToolsPanelItem,
} from '@wordpress/components';

/**
 * Internal dependencies
 */
import { ProductQueryBlock } from '../types';
import { setQueryAttribute } from '../utils';
import { EDIT_ATTRIBUTES_URL } from '../constants';

export const AttributesFilter = ( props: ProductQueryBlock ) => {
	const { query } = props.attributes;
	const [ selected, setSelected ] = useState< { id: number }[] >( [] );

	useEffect( () => {
		if ( query.__woocommerceAttributes ) {
			setSelected(
				query.__woocommerceAttributes.map( ( { termId: id } ) => ( {
					id,
				} ) )
			);
		}
	}, [ query.__woocommerceAttributes ] );

	return (
		<ToolsPanelItem
			label={ __( 'Product Attributes', 'woo-gutenberg-products-block' ) }
			hasValue={ () => query.__woocommerceAttributes?.length }
		>
			<ProductAttributeTermControl
				messages={ {
					search: __( 'Attributes', 'woo-gutenberg-products-block' ),
				} }
				selected={ selected }
				onChange={ ( attributes ) => {
					const __woocommerceAttributes = attributes.map(
						// eslint-disable-next-line @typescript-eslint/naming-convention
						( { id, value } ) => ( {
							termId: id,
							taxonomy: value,
						} )
					);

					setQueryAttribute( props, {
						__woocommerceAttributes,
					} );
				} }
				operator={ 'any' }
				isCompact={ true }
				type={ 'token' }
			/>
			<ExternalLink
				className="woocommerce-product-query-panel__external-link"
				href={ EDIT_ATTRIBUTES_URL }
			>
				{ __( 'Manage attributes', 'woo-gutenberg-products-block' ) }
			</ExternalLink>
		</ToolsPanelItem>
	);
};
