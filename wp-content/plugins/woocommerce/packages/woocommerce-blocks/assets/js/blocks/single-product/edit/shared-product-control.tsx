/**
 * External dependencies
 */
import ProductControl from '@woocommerce/editor-components/product-control';

/**
 * Internal dependencies
 */
import { Attributes } from '../types';

interface SharedProductControlProps {
	attributes: Attributes;
	setAttributes: ( attributes: Attributes ) => void;
}

const SharedProductControl = ( {
	attributes,
	setAttributes,
}: SharedProductControlProps ) => (
	<ProductControl
		selected={ attributes.productId || 0 }
		showVariations
		onChange={ ( value = [] ) => {
			const id = value[ 0 ] ? value[ 0 ].id : 0;
			setAttributes( {
				productId: id,
			} );
		} }
	/>
);

export default SharedProductControl;
