/**
 * External dependencies
 */
import { Cart } from '@woocommerce/type-defs/cart';

export interface ShippingData {
	needsShipping: Cart[ 'needsShipping' ];
	hasCalculatedShipping: Cart[ 'hasCalculatedShipping' ];
	shippingRates: Cart[ 'shippingRates' ];
	isLoadingRates: boolean;
	selectedRates: Record< string, string | unknown >;
	// Returns a function that accepts a shipping rate ID and a package ID.
	selectShippingRate: (
		newShippingRateId: string,
		packageId?: string | number | undefined
	) => void;
	// Only true when ALL packages support local pickup. If true, we can show the collection/delivery toggle
	isCollectable: boolean;
	// True when a rate is currently being selected and persisted to the server.
	isSelectingRate: boolean;
	// True when the user has chosen a local pickup method.
	hasSelectedLocalPickup: boolean;
}
