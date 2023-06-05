/**
 * Get a class name for an icon.
 *
 * @param {string} id Icon ID.
 */
const getIconClassName = ( id: string ): string => {
	return `wc-block-components-payment-method-icon wc-block-components-payment-method-icon--${ id }`;
};

interface PaymentMethodIconProps {
	id: string;
	src?: string | null;
	alt?: string;
}
/**
 * Return an element for an icon.
 *
 * @param {Object}      props     Incoming props for component.
 * @param {string}      props.id  Id for component.
 * @param {string|null} props.src Optional src value for icon.
 * @param {string}      props.alt Optional alt value for icon.
 */
const PaymentMethodIcon = ( {
	id,
	src = null,
	alt = '',
}: PaymentMethodIconProps ): JSX.Element | null => {
	if ( ! src ) {
		return null;
	}
	return <img className={ getIconClassName( id ) } src={ src } alt={ alt } />;
};

export default PaymentMethodIcon;
