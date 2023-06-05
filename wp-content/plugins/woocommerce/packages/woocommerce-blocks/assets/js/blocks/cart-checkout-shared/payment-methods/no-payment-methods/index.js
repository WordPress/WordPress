/**
 * External dependencies
 */
import { __ } from '@wordpress/i18n';
import NoticeBanner from '@woocommerce/base-components/notice-banner';

/**
 * Internal dependencies
 */
import './style.scss';

/**
 * Render content when no payment methods are found depending on context.
 */
const NoPaymentMethods = () => {
	return (
		<NoticeBanner
			isDismissible={ false }
			className="wc-block-checkout__no-payment-methods-notice"
			status="error"
		>
			{ __(
				'There are no payment methods available. This may be an error on our side. Please contact us if you need any help placing your order.',
				'woocommerce'
			) }
		</NoticeBanner>
	);
};

export default NoPaymentMethods;
