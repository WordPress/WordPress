/**
 * External dependencies
 */
import { __ } from '@wordpress/i18n';
import { CART_URL } from '@woocommerce/block-settings';
import { Icon, arrowLeft } from '@wordpress/icons';

/**
 * Internal dependencies
 */
import './style.scss';

interface ReturnToCartButtonProps {
	link?: string | undefined;
}

const ReturnToCartButton = ( {
	link,
}: ReturnToCartButtonProps ): JSX.Element | null => {
	const cartLink = link || CART_URL;
	if ( ! cartLink ) {
		return null;
	}
	return (
		<a
			href={ cartLink }
			className="wc-block-components-checkout-return-to-cart-button"
		>
			<Icon icon={ arrowLeft } />
			{ __( 'Return to Cart', 'woo-gutenberg-products-block' ) }
		</a>
	);
};

export default ReturnToCartButton;
