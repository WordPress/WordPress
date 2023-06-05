/**
 * External dependencies
 */
import { useDispatch, useSelect } from '@wordpress/data';
import { CART_STORE_KEY as storeKey } from '@woocommerce/block-data';
import type { BillingAddress, ShippingAddress } from '@woocommerce/settings';

export interface CustomerDataType {
	isInitialized: boolean;
	billingAddress: BillingAddress;
	shippingAddress: ShippingAddress;
	setBillingAddress: ( data: Partial< BillingAddress > ) => void;
	setShippingAddress: ( data: Partial< ShippingAddress > ) => void;
}

/**
 * This is a custom hook for syncing customer address data (billing and shipping) with the server.
 */
export const useCustomerData = (): CustomerDataType => {
	const { customerData, isInitialized } = useSelect( ( select ) => {
		const store = select( storeKey );
		return {
			customerData: store.getCustomerData(),
			isInitialized: store.hasFinishedResolution( 'getCartData' ),
		};
	} );
	const { setShippingAddress, setBillingAddress } = useDispatch( storeKey );

	return {
		isInitialized,
		billingAddress: customerData.billingAddress,
		shippingAddress: customerData.shippingAddress,
		setBillingAddress,
		setShippingAddress,
	};
};
