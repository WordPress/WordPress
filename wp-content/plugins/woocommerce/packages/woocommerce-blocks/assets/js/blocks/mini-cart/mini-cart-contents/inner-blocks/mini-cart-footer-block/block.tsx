/**
 * External dependencies
 */
import { __ } from '@wordpress/i18n';
import { TotalsItem } from '@woocommerce/blocks-checkout';
import { getCurrencyFromPriceResponse } from '@woocommerce/price-format';
import {
	usePaymentMethods,
	useStoreCart,
} from '@woocommerce/base-context/hooks';
import PaymentMethodIcons from '@woocommerce/base-components/cart-checkout/payment-method-icons';
import { getIconsFromPaymentMethods } from '@woocommerce/base-utils';
import { getSetting } from '@woocommerce/settings';
import { PaymentEventsProvider } from '@woocommerce/base-context';
import classNames from 'classnames';
import { isObject } from '@woocommerce/types';

/**
 * Internal dependencies
 */
import CartButton from '../mini-cart-cart-button-block/block';
import CheckoutButton from '../mini-cart-checkout-button-block/block';

const PaymentMethodIconsElement = (): JSX.Element => {
	const { paymentMethods } = usePaymentMethods();
	return (
		<PaymentMethodIcons
			icons={ getIconsFromPaymentMethods( paymentMethods ) }
		/>
	);
};

interface Props {
	children: JSX.Element | JSX.Element[];
	className?: string;
	cartButtonLabel: string;
	checkoutButtonLabel: string;
}

/**
 * Checks if there are any children that are blocks.
 */
const hasChildren = ( children ): boolean => {
	return children.some( ( child ) => {
		if ( Array.isArray( child ) ) {
			return hasChildren( child );
		}
		return isObject( child ) && child.key !== null;
	} );
};

const Block = ( {
	children,
	className,
	cartButtonLabel,
	checkoutButtonLabel,
}: Props ): JSX.Element => {
	const { cartTotals } = useStoreCart();
	const subTotal = getSetting( 'displayCartPricesIncludingTax', false )
		? parseInt( cartTotals.total_items, 10 ) +
		  parseInt( cartTotals.total_items_tax, 10 )
		: parseInt( cartTotals.total_items, 10 );

	// The `Cart` and `Checkout` buttons were converted to inner blocks, but we still need to render the buttons
	// for themes that have the old `mini-cart.html` template. So we check if there are any inner blocks (buttons) and
	// if not, render the buttons.
	const hasButtons = hasChildren( children );

	return (
		<div
			className={ classNames( className, 'wc-block-mini-cart__footer' ) }
		>
			<TotalsItem
				className="wc-block-mini-cart__footer-subtotal"
				currency={ getCurrencyFromPriceResponse( cartTotals ) }
				label={ __( 'Subtotal', 'woo-gutenberg-products-block' ) }
				value={ subTotal }
				description={ __(
					'Shipping, taxes, and discounts calculated at checkout.',
					'woo-gutenberg-products-block'
				) }
			/>
			<div className="wc-block-mini-cart__footer-actions">
				{ hasButtons ? (
					children
				) : (
					<>
						<CartButton cartButtonLabel={ cartButtonLabel } />
						<CheckoutButton
							checkoutButtonLabel={ checkoutButtonLabel }
						/>
					</>
				) }
			</div>
			<PaymentEventsProvider>
				<PaymentMethodIconsElement />
			</PaymentEventsProvider>
		</div>
	);
};

export default Block;
