/**
 * External dependencies
 */
import PropTypes from 'prop-types';
import classnames from 'classnames';
import {
	AddToCartFormContextProvider,
	useAddToCartFormContext,
} from '@woocommerce/base-context';
import { useProductDataContext } from '@woocommerce/shared-context';
import { isEmpty } from 'lodash';
import { withProductDataContext } from '@woocommerce/shared-hocs';

/**
 * Internal dependencies
 */
import './style.scss';
import { AddToCartButton } from './shared';
import {
	SimpleProductForm,
	VariableProductForm,
	ExternalProductForm,
	GroupedProductForm,
} from './product-types';

interface Props {
	/**
	 * CSS Class name for the component.
	 */
	className?: string;
	/**
	 * Whether or not to show form elements.
	 */
	showFormElements?: boolean;
}

/**
 * Renders the add to cart form using useAddToCartFormContext.
 */
const AddToCartForm = () => {
	const { showFormElements, productType } = useAddToCartFormContext();

	if ( showFormElements ) {
		if ( productType === 'variable' ) {
			return <VariableProductForm />;
		}
		if ( productType === 'grouped' ) {
			return <GroupedProductForm />;
		}
		if ( productType === 'external' ) {
			return <ExternalProductForm />;
		}
		if ( productType === 'simple' || productType === 'variation' ) {
			return <SimpleProductForm />;
		}
		return null;
	}

	return <AddToCartButton />;
};

/**
 * Product Add to Form Block Component.
 */
const Block = ( { className, showFormElements }: Props ) => {
	const { product } = useProductDataContext();
	const componentClass = classnames(
		className,
		'wc-block-components-product-add-to-cart',
		{
			'wc-block-components-product-add-to-cart--placeholder':
				isEmpty( product ),
		}
	);

	return (
		<AddToCartFormContextProvider
			product={ product }
			showFormElements={ showFormElements }
		>
			<div className={ componentClass }>
				<AddToCartForm />
			</div>
		</AddToCartFormContextProvider>
	);
};

Block.propTypes = {
	className: PropTypes.string,
};

export default withProductDataContext( Block );
