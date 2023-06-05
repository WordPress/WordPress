/**
 * External dependencies
 */
import { __ } from '@wordpress/i18n';
import { SHOP_URL } from '@woocommerce/block-settings';
import { cart } from '@woocommerce/icons';
import { Icon } from '@wordpress/icons';

/**
 * Internal dependencies
 */
import './style.scss';

const EmptyCart = () => {
	return (
		<div className="wc-block-checkout-empty">
			<Icon
				className="wc-block-checkout-empty__image"
				icon={ cart }
				size={ 100 }
			/>
			<strong className="wc-block-checkout-empty__title">
				{ __(
					'Your cart is currently empty!',
					'woocommerce'
				) }
			</strong>
			<p className="wc-block-checkout-empty__description">
				{ __(
					"Checkout is not available whilst your cart is empty—please take a look through our store and come back when you're ready to place an order.",
					'woocommerce'
				) }
			</p>
			{ SHOP_URL && (
				<span className="wp-block-button">
					<a href={ SHOP_URL } className="wp-block-button__link">
						{ __( 'Browse store', 'woocommerce' ) }
					</a>
				</span>
			) }
		</div>
	);
};

export default EmptyCart;
