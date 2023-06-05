/**
 * External dependencies
 */
import { useCheckoutSubmit } from '@woocommerce/base-context/hooks';
import { Icon, check } from '@wordpress/icons';
import Button from '@woocommerce/base-components/button';

interface PlaceOrderButton {
	label: string;
}

const PlaceOrderButton = ( { label }: PlaceOrderButton ): JSX.Element => {
	const {
		onSubmit,
		isCalculating,
		isDisabled,
		waitingForProcessing,
		waitingForRedirect,
	} = useCheckoutSubmit();

	return (
		<Button
			className="wc-block-components-checkout-place-order-button"
			onClick={ onSubmit }
			disabled={
				isCalculating ||
				isDisabled ||
				waitingForProcessing ||
				waitingForRedirect
			}
			showSpinner={ waitingForProcessing }
		>
			{ waitingForRedirect ? <Icon icon={ check } /> : label }
		</Button>
	);
};

export default PlaceOrderButton;
