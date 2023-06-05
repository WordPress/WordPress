/**
 * External dependencies
 */
import classnames from 'classnames';
import { checkPayment } from '@woocommerce/icons';
import {
	Icon,
	institution as bank,
	currencyDollar as bill,
	payment as card,
} from '@wordpress/icons';
import { isString, objectHasProp } from '@woocommerce/types';
import { useCallback } from '@wordpress/element';

/**
 * Internal dependencies
 */
import './style.scss';

interface NamedIcons {
	bank: JSX.Element;
	bill: JSX.Element;
	card: JSX.Element;
	checkPayment: JSX.Element;
}

const namedIcons: NamedIcons = {
	bank,
	bill,
	card,
	checkPayment,
};

interface PaymentMethodLabelProps {
	icon: '' | keyof NamedIcons | SVGElement;
	text: string;
}

/**
 * Exposed to payment methods for the label shown on checkout. Allows icons to be added as well as
 * text.
 *
 * @param {Object} props      Component props.
 * @param {*}      props.icon Show an icon beside the text if provided. Can be a string to use a named
 *                            icon, or an SVG element.
 * @param {string} props.text Text shown next to icon.
 */
export const PaymentMethodLabel = ( {
	icon = '',
	text = '',
}: PaymentMethodLabelProps ): JSX.Element => {
	const hasIcon = !! icon;
	const hasNamedIcon = useCallback(
		(
			iconToCheck: '' | keyof NamedIcons | SVGElement
		): iconToCheck is keyof NamedIcons =>
			hasIcon &&
			isString( iconToCheck ) &&
			objectHasProp( namedIcons, iconToCheck ),
		[ hasIcon ]
	);
	const className = classnames( 'wc-block-components-payment-method-label', {
		'wc-block-components-payment-method-label--with-icon': hasIcon,
	} );

	return (
		<span className={ className }>
			{ hasNamedIcon( icon ) ? (
				<Icon icon={ namedIcons[ icon ] } />
			) : (
				icon
			) }
			{ text }
		</span>
	);
};

export default PaymentMethodLabel;
