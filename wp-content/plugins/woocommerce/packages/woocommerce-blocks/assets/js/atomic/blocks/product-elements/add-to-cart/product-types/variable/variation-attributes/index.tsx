/**
 * External dependencies
 */
import { ProductResponseItem } from '@woocommerce/types';

/**
 * Internal dependencies
 */
import './style.scss';
import AttributePicker from './attribute-picker';
import { getAttributes, getVariationAttributes } from './utils';

interface Props {
	dispatchers: { setRequestParams: () => void };
	product: ProductResponseItem;
}

/**
 * VariationAttributes component.
 */
const VariationAttributes = ( { dispatchers, product }: Props ) => {
	const attributes = getAttributes( product.attributes );
	const variationAttributes = getVariationAttributes( product.variations );
	if (
		Object.keys( attributes ).length === 0 ||
		Object.keys( variationAttributes ).length === 0
	) {
		return null;
	}

	return (
		<AttributePicker
			attributes={ attributes }
			variationAttributes={ variationAttributes }
			setRequestParams={ dispatchers.setRequestParams }
		/>
	);
};

export default VariationAttributes;
