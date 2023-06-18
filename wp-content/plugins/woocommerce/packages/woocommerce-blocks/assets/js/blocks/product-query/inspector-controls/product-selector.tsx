/**
 * External dependencies
 */
import { getProducts } from '@woocommerce/editor-components/utils';
import { ProductResponseItem } from '@woocommerce/types';
import { objectOmit } from '@woocommerce/utils';
import { useState, useEffect } from '@wordpress/element';
import { __ } from '@wordpress/i18n';
import {
	FormTokenField,
	// eslint-disable-next-line @wordpress/no-unsafe-wp-apis
	__experimentalToolsPanelItem as ToolsPanelItem,
} from '@wordpress/components';

/**
 * Internal dependencies
 */
import { ProductQueryBlock } from '../types';
import { setQueryAttribute } from '../utils';

function useProductsList() {
	const [ productsList, setProductsList ] = useState< ProductResponseItem[] >(
		[]
	);

	useEffect( () => {
		getProducts( { selected: [] } ).then( ( results ) => {
			setProductsList( results as ProductResponseItem[] );
		} );
	}, [] );

	return productsList;
}

export const ProductSelector = ( props: ProductQueryBlock ) => {
	const { query } = props.attributes;

	const productsList = useProductsList();

	const onTokenChange = ( values: FormTokenField.Value[] ) => {
		const ids = values
			.map(
				( nameOrId ) =>
					productsList.find(
						( product ) =>
							product.name === nameOrId ||
							product.id === Number( nameOrId )
					)?.id
			)
			.filter( Boolean )
			.map( String );

		if ( ! ids.length && props.attributes.query.include ) {
			const prunedQuery = objectOmit( props.attributes.query, 'include' );

			setQueryAttribute(
				{
					...props,
					attributes: {
						...props.attributes,
						query: prunedQuery,
					},
				},
				{}
			);
		} else {
			setQueryAttribute( props, {
				include: ids,
			} );
		}
	};

	return (
		<ToolsPanelItem
			label={ __(
				'Hand-picked Products',
				'woo-gutenberg-products-block'
			) }
			hasValue={ () => query.include?.length }
		>
			<FormTokenField
				disabled={ ! productsList.length }
				displayTransform={ ( token: string ) =>
					Number.isNaN( Number( token ) )
						? token
						: productsList.find(
								( product ) => product.id === Number( token )
						  )?.name || ''
				}
				label={ __(
					'Pick some products',
					'woo-gutenberg-products-block'
				) }
				onChange={ onTokenChange }
				suggestions={ productsList.map( ( product ) => product.name ) }
				validateInput={ ( value: string ) =>
					productsList.find( ( product ) => product.name === value )
				}
				value={
					! productsList.length
						? [ __( 'Loading…', 'woo-gutenberg-products-block' ) ]
						: query?.include || []
				}
				__experimentalExpandOnFocus={ true }
			/>
		</ToolsPanelItem>
	);
};
