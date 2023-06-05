/**
 * External dependencies
 */
import classnames from 'classnames';
import { withFilteredAttributes } from '@woocommerce/shared-hocs';
import { FormStep } from '@woocommerce/base-components/cart-checkout';
import { useDispatch, useSelect } from '@wordpress/data';
import { CHECKOUT_STORE_KEY } from '@woocommerce/block-data';
import { useShippingData } from '@woocommerce/base-context/hooks';
import { LOCAL_PICKUP_ENABLED } from '@woocommerce/block-settings';

/**
 * Internal dependencies
 */
import Block from './block';
import attributes from './attributes';

const FrontendBlock = ( {
	title,
	description,
	showStepNumber,
	children,
	className,
	showPrice,
	showIcon,
	shippingText,
	localPickupText,
	shippingCostRequiresAddress,
}: {
	title: string;
	description: string;
	showStepNumber: boolean;
	shippingCostRequiresAddress: boolean;
	children: JSX.Element;
	className?: string;
	showPrice: boolean;
	showIcon: boolean;
	shippingText: string;
	localPickupText: string;
} ) => {
	const { checkoutIsProcessing, prefersCollection } = useSelect(
		( select ) => {
			const checkoutStore = select( CHECKOUT_STORE_KEY );
			return {
				checkoutIsProcessing: checkoutStore.isProcessing(),
				prefersCollection: checkoutStore.prefersCollection(),
			};
		}
	);
	const { setPrefersCollection } = useDispatch( CHECKOUT_STORE_KEY );
	const {
		shippingRates,
		needsShipping,
		hasCalculatedShipping,
		isCollectable,
	} = useShippingData();

	if (
		! needsShipping ||
		! hasCalculatedShipping ||
		! shippingRates ||
		! isCollectable ||
		! LOCAL_PICKUP_ENABLED
	) {
		return null;
	}

	const onChange = ( method: string ) => {
		if ( method === 'pickup' ) {
			setPrefersCollection( true );
		} else {
			setPrefersCollection( false );
		}
	};

	return (
		<FormStep
			id="shipping-method"
			disabled={ checkoutIsProcessing }
			className={ classnames(
				'wc-block-checkout__shipping-method',
				className
			) }
			title={ title }
			description={ description }
			showStepNumber={ showStepNumber }
		>
			<Block
				checked={ prefersCollection ? 'pickup' : 'shipping' }
				onChange={ onChange }
				showPrice={ showPrice }
				showIcon={ showIcon }
				localPickupText={ localPickupText }
				shippingText={ shippingText }
				shippingCostRequiresAddress={ shippingCostRequiresAddress }
			/>
			{ children }
		</FormStep>
	);
};

export default withFilteredAttributes( attributes )( FrontendBlock );
