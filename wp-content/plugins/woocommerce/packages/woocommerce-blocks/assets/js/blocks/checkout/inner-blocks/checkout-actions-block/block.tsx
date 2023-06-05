/**
 * External dependencies
 */
import classnames from 'classnames';
import { getSetting } from '@woocommerce/settings';
import {
	PlaceOrderButton,
	ReturnToCartButton,
} from '@woocommerce/base-components/cart-checkout';
import { useCheckoutSubmit } from '@woocommerce/base-context/hooks';
import { noticeContexts } from '@woocommerce/base-context';
import {
	StoreNoticesContainer,
	applyCheckoutFilter,
} from '@woocommerce/blocks-checkout';

/**
 * Internal dependencies
 */
import './style.scss';
import { defaultPlaceOrderButtonLabel } from './constants';

const Block = ( {
	cartPageId,
	showReturnToCart,
	className,
	placeOrderButtonLabel,
}: {
	cartPageId: number;
	showReturnToCart: boolean;
	className?: string;
	placeOrderButtonLabel: string;
} ): JSX.Element => {
	const { paymentMethodButtonLabel } = useCheckoutSubmit();
	const label = applyCheckoutFilter( {
		filterName: 'placeOrderButtonLabel',
		defaultValue:
			paymentMethodButtonLabel ||
			placeOrderButtonLabel ||
			defaultPlaceOrderButtonLabel,
	} );

	return (
		<div
			className={ classnames( 'wc-block-checkout__actions', className ) }
		>
			<StoreNoticesContainer
				context={ noticeContexts.CHECKOUT_ACTIONS }
			/>
			<div className="wc-block-checkout__actions_row">
				{ showReturnToCart && (
					<ReturnToCartButton
						link={ getSetting( 'page-' + cartPageId, false ) }
					/>
				) }
				<PlaceOrderButton label={ label } />
			</div>
		</div>
	);
};

export default Block;
