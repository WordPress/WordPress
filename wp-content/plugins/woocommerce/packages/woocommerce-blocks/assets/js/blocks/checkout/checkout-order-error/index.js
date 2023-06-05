/**
 * External dependencies
 */
import { __ } from '@wordpress/i18n';
import { CART_URL } from '@woocommerce/block-settings';
import { removeCart } from '@woocommerce/icons';
import { Icon } from '@wordpress/icons';
import { getSetting } from '@woocommerce/settings';
import { decodeEntities } from '@wordpress/html-entities';

/**
 * Internal dependencies
 */
import './style.scss';
import {
	PRODUCT_OUT_OF_STOCK,
	PRODUCT_NOT_PURCHASABLE,
	PRODUCT_NOT_ENOUGH_STOCK,
	PRODUCT_SOLD_INDIVIDUALLY,
	GENERIC_CART_ITEM_ERROR,
} from './constants';

const cartItemErrorCodes = [
	PRODUCT_OUT_OF_STOCK,
	PRODUCT_NOT_PURCHASABLE,
	PRODUCT_NOT_ENOUGH_STOCK,
	PRODUCT_SOLD_INDIVIDUALLY,
	GENERIC_CART_ITEM_ERROR,
];

const preloadedCheckoutData = getSetting( 'checkoutData', {} );

/**
 * When an order was not created for the checkout, for example, when an item
 * was out of stock, this component will be shown instead of the checkout form.
 *
 * The error message is derived by the hydrated API request passed to the
 * checkout block.
 */
const CheckoutOrderError = () => {
	const checkoutData = {
		code: '',
		message: '',
		...( preloadedCheckoutData || {} ),
	};

	const errorData = {
		code: checkoutData.code || 'unknown',
		message:
			decodeEntities( checkoutData.message ) ||
			__(
				'There was a problem checking out. Please try again. If the problem persists, please get in touch with us so we can assist.',
				'woocommerce'
			),
	};

	return (
		<div className="wc-block-checkout-error">
			<Icon
				className="wc-block-checkout-error__image"
				icon={ removeCart }
				size={ 100 }
			/>
			<ErrorTitle errorData={ errorData } />
			<ErrorMessage errorData={ errorData } />
			<ErrorButton errorData={ errorData } />
		</div>
	);
};

/**
 * Get the error message to display.
 *
 * @param {Object} props           Incoming props for the component.
 * @param {Object} props.errorData Object containing code and message.
 */
const ErrorTitle = ( { errorData } ) => {
	let heading = __( 'Checkout error', 'woocommerce' );

	if ( cartItemErrorCodes.includes( errorData.code ) ) {
		heading = __(
			'There is a problem with your cart',
			'woocommerce'
		);
	}

	return (
		<strong className="wc-block-checkout-error_title">{ heading }</strong>
	);
};

/**
 * Get the error message to display.
 *
 * @param {Object} props           Incoming props for the component.
 * @param {Object} props.errorData Object containing code and message.
 */
const ErrorMessage = ( { errorData } ) => {
	let message = errorData.message;

	if ( cartItemErrorCodes.includes( errorData.code ) ) {
		message =
			message +
			' ' +
			__(
				'Please edit your cart and try again.',
				'woocommerce'
			);
	}

	return <p className="wc-block-checkout-error__description">{ message }</p>;
};

/**
 * Get the CTA button to display.
 *
 * @param {Object} props           Incoming props for the component.
 * @param {Object} props.errorData Object containing code and message.
 */
const ErrorButton = ( { errorData } ) => {
	let buttonText = __( 'Retry', 'woocommerce' );
	let buttonUrl = 'javascript:window.location.reload(true)';

	if ( cartItemErrorCodes.includes( errorData.code ) ) {
		buttonText = __( 'Edit your cart', 'woocommerce' );
		buttonUrl = CART_URL;
	}

	return (
		<span className="wp-block-button">
			<a href={ buttonUrl } className="wp-block-button__link">
				{ buttonText }
			</a>
		</span>
	);
};

export default CheckoutOrderError;
