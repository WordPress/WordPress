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
	attributes: {
		className?: string;
		lock: {
			move: boolean;
			remove: boolean;
		};
	};
} ): JSX.Element | null => {
	const { paymentMethods, isInitialized } = useExpressPaymentMethods();
	const hasExpressPaymentMethods = Object.keys( paymentMethods ).length > 0;
	const blockProps = useBlockProps( {
		className: classnames(
			{
				'wp-block-woocommerce-checkout-express-payment-block--has-express-payment-methods':
					hasExpressPaymentMethods,
			},
			attributes?.className
		),
		attributes,
	} );

	if ( ! isInitialized || ! hasExpressPaymentMethods ) {
		return null;
	}

	return (
		<div { ...blockProps }>
			<Block />
		</div>
	);
};

export const Save = (): JSX.Element => {
	return <div { ...useBlockProps.save() } />;
};
