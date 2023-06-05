/**
 * Internal dependencies
 */
import type { emitterCallback } from '../../../event-emit';

export type ShippingErrorStatus = {
	isPristine: boolean;
	isValid: boolean;
	hasInvalidAddress: boolean;
	hasError: boolean;
};

export type ShippingErrorTypes = {
	// eslint-disable-next-line @typescript-eslint/naming-convention
	NONE: 'none';
	// eslint-disable-next-line @typescript-eslint/naming-convention
	INVALID_ADDRESS: 'invalid_address';
	// eslint-disable-next-line @typescript-eslint/naming-convention
	UNKNOWN: 'unknown_error';
};

export type ShippingDataContextType = {
	// A function for dispatching a shipping rate error status.
	dispatchErrorStatus: React.Dispatch< {
		type: string;
	} >;
	onShippingRateFail: ReturnType< typeof emitterCallback >;
	// Used to register a callback to be invoked when shipping rate is selected unsuccessfully
	onShippingRateSelectFail: ReturnType< typeof emitterCallback >;
	// Used to register a callback to be invoked when shipping rate is selected.
	onShippingRateSelectSuccess: ReturnType< typeof emitterCallback >;
	// Used to register a callback to be invoked when shipping rates are retrieved.
	onShippingRateSuccess: ReturnType< typeof emitterCallback >;
	// The current shipping error status.
	shippingErrorStatus: ShippingErrorStatus;
	// The error type constants for the shipping rate error status.
	shippingErrorTypes: ShippingErrorTypes;
};

export interface ShippingDataProviderProps {
	children: JSX.Element | JSX.Element[];
}
