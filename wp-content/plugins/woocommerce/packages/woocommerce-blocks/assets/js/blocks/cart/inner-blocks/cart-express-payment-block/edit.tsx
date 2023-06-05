/**
 * External dependencies
 */
import { useBlockProps } from '@wordpress/block-editor';
import { useExpressPaymentMethods } from '@woocommerce/base-context/hooks';
import classnames from 'classnames';

/**
 * Internal dependencies
 */
import Block from './block';
import './editor.scss';

export const Edit = ( {
	attributes,
}: {
	attributes: { className: string };
} ): JSX.Element | null => {
	const { paymentMethods, isInitialized } = useExpressPaymentMethods();
	const hasExpressPaymentMethods = Object.keys( paymentMethods ).length > 0;
	const blockProps = useBlockProps( {
		className: classnames( {
			'wp-block-woocommerce-cart-express-payment-block--has-express-payment-methods':
				hasExpressPaymentMethods,
		} ),
	} );
	const { className } = attributes;

	if ( ! isInitialized || ! hasExpressPaymentMethods ) {
		return null;
	}

	return (
		<div { ...blockProps }>
			<Block className={ className } />
		</div>
	);
};

export const Save = (): JSX.Element => {
	return <div { ...useBlockProps.save() } />;
};
