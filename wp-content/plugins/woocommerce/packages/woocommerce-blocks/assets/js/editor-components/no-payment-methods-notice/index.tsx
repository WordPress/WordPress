/**
 * External dependencies
 */
import { __ } from '@wordpress/i18n';
import { Notice, ExternalLink } from '@wordpress/components';
import { ADMIN_URL } from '@woocommerce/settings';

/**
 * Internal dependencies
 */
import './editor.scss';

export function NoPaymentMethodsNotice() {
	const noticeContent = __(
		'Your store does not have any payment methods that support the Checkout block. Once you have configured a compatible payment method it will be displayed here.',
		'woo-gutenberg-products-block'
	);

	return (
		<Notice
			className="wc-blocks-no-payment-methods-notice"
			status={ 'warning' }
			spokenMessage={ noticeContent }
			isDismissible={ false }
		>
			<div className="wc-blocks-no-payment-methods-notice__content">
				{ noticeContent }{ ' ' }
				<ExternalLink
					href={ `${ ADMIN_URL }admin.php?page=wc-settings&tab=checkout` }
				>
					{ __(
						'Configure Payment Methods',
						'woo-gutenberg-products-block'
					) }
				</ExternalLink>
			</div>
		</Notice>
	);
}
