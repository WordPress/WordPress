/**
 * External dependencies
 */
import classnames from 'classnames';
import { __ } from '@wordpress/i18n';
import { FormStep } from '@woocommerce/base-components/cart-checkout';
import { useShippingData } from '@woocommerce/base-context/hooks';
import { useDispatch, useSelect } from '@wordpress/data';
import { CHECKOUT_STORE_KEY } from '@woocommerce/block-data';

/**
 * Internal dependencies
 */
import CheckoutOrderNotes from '../../order-notes';

const Block = ( { className }: { className?: string } ): JSX.Element => {
	const { needsShipping } = useShippingData();
	const { isProcessing: checkoutIsProcessing, orderNotes } = useSelect(
		( select ) => {
			const store = select( CHECKOUT_STORE_KEY );
			return {
				isProcessing: store.isProcessing(),
				orderNotes: store.getOrderNotes(),
			};
		}
	);
	const { __internalSetOrderNotes } = useDispatch( CHECKOUT_STORE_KEY );

	return (
		<FormStep
			id="order-notes"
			showStepNumber={ false }
			className={ classnames(
				'wc-block-checkout__order-notes',
				className
			) }
			disabled={ checkoutIsProcessing }
		>
			<CheckoutOrderNotes
				disabled={ checkoutIsProcessing }
				onChange={ __internalSetOrderNotes }
				placeholder={
					needsShipping
						? __(
								'Notes about your order, e.g. special notes for delivery.',
								'woo-gutenberg-products-block'
						  )
						: __(
								'Notes about your order.',
								'woo-gutenberg-products-block'
						  )
				}
				value={ orderNotes }
			/>
		</FormStep>
	);
};

export default Block;
