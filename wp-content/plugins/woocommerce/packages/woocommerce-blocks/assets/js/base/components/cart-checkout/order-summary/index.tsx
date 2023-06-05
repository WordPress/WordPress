/**
 * External dependencies
 */
import { __ } from '@wordpress/i18n';
import { useContainerWidthContext } from '@woocommerce/base-context';
import { Panel } from '@woocommerce/blocks-checkout';
import type { CartItem } from '@woocommerce/types';

/**
 * Internal dependencies
 */
import OrderSummaryItem from './order-summary-item';
import './style.scss';

interface OrderSummaryProps {
	cartItems: CartItem[];
}

const OrderSummary = ( {
	cartItems = [],
}: OrderSummaryProps ): null | JSX.Element => {
	const { isLarge, hasContainerWidth } = useContainerWidthContext();

	if ( ! hasContainerWidth ) {
		return null;
	}

	return (
		<Panel
			className="wc-block-components-order-summary"
			initialOpen={ isLarge }
			hasBorder={ false }
			title={
				<span className="wc-block-components-order-summary__button-text">
					{ __( 'Order summary', 'woo-gutenberg-products-block' ) }
				</span>
			}
			titleTag="h2"
		>
			<div className="wc-block-components-order-summary__content">
				{ cartItems.map( ( cartItem ) => {
					return (
						<OrderSummaryItem
							key={ cartItem.key }
							cartItem={ cartItem }
						/>
					);
				} ) }
			</div>
		</Panel>
	);
};

export default OrderSummary;
