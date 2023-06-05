/**
 * External dependencies
 */
import classnames from 'classnames';
import { useBlockProps, InnerBlocks } from '@wordpress/block-editor';
import { Main } from '@woocommerce/base-components/sidebar-layout';
import { innerBlockAreas } from '@woocommerce/blocks-checkout';
import type { TemplateArray } from '@wordpress/blocks';

/**
 * Internal dependencies
 */
import { useCheckoutBlockControlsContext } from '../../context';
import {
	useForcedLayout,
	getAllowedBlocks,
} from '../../../cart-checkout-shared';
import './style.scss';

export const Edit = ( {
	clientId,
	attributes,
}: {
	clientId: string;
	attributes: {
		className?: string;
		isPreview?: boolean;
	};
} ): JSX.Element => {
	const blockProps = useBlockProps( {
		className: classnames(
			'wc-block-checkout__main',
			attributes?.className
		),
	} );
	const allowedBlocks = getAllowedBlocks( innerBlockAreas.CHECKOUT_FIELDS );

	const { addressFieldControls: Controls } =
		useCheckoutBlockControlsContext();

	const defaultTemplate = [
		[ 'woocommerce/checkout-express-payment-block', {}, [] ],
		[ 'woocommerce/checkout-contact-information-block', {}, [] ],
		[ 'woocommerce/checkout-shipping-method-block', {}, [] ],
		[ 'woocommerce/checkout-pickup-options-block', {}, [] ],
		[ 'woocommerce/checkout-shipping-address-block', {}, [] ],
		[ 'woocommerce/checkout-billing-address-block', {}, [] ],
		[ 'woocommerce/checkout-shipping-methods-block', {}, [] ],
		[ 'woocommerce/checkout-payment-block', {}, [] ],
		[ 'woocommerce/checkout-order-note-block', {}, [] ],
		[ 'woocommerce/checkout-terms-block', {}, [] ],
		[ 'woocommerce/checkout-actions-block', {}, [] ],
	].filter( Boolean ) as unknown as TemplateArray;

	useForcedLayout( {
		clientId,
		registeredBlocks: allowedBlocks,
		defaultTemplate,
	} );

	return (
		<Main { ...blockProps }>
			<Controls />
			<form className="wc-block-components-form wc-block-checkout__form">
				<InnerBlocks
					allowedBlocks={ allowedBlocks }
					templateLock={ false }
					template={ defaultTemplate }
					renderAppender={ InnerBlocks.ButtonBlockAppender }
				/>
			</form>
		</Main>
	);
};

export const Save = (): JSX.Element => {
	return (
		<div { ...useBlockProps.save() }>
			<InnerBlocks.Content />
		</div>
	);
};
