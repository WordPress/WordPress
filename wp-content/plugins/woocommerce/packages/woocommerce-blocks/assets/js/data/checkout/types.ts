/**
 * External dependencies
 */
import type { Notice } from '@wordpress/notices/';
import { DataRegistry } from '@wordpress/data';
import { FieldValidationStatus } from '@woocommerce/types';

/**
 * Internal dependencies
 */
import type { EventObserversType } from '../../base/context/event-emit/types';
import type { CheckoutState } from './default-state';
import type { PaymentState } from '../payment/default-state';
import type { DispatchFromMap, SelectFromMap } from '../mapped-types';
import * as selectors from './selectors';
import * as actions from './actions';

export type CheckoutAfterProcessingWithErrorEventData = {
	redirectUrl: CheckoutState[ 'redirectUrl' ];
	orderId: CheckoutState[ 'orderId' ];
	customerId: CheckoutState[ 'customerId' ];
	orderNotes: CheckoutState[ 'orderNotes' ];
	processingResponse: PaymentState[ 'paymentResult' ];
};
export type CheckoutAndPaymentNotices = {
	checkoutNotices: Notice[];
	paymentNotices: Notice[];
	expressPaymentNotices: Notice[];
};

/**
 * Type for emitAfterProcessingEventsType() thunk
 */
export type emitAfterProcessingEventsType = ( {
	observers,
	notices,
}: {
	observers: EventObserversType;
	notices: CheckoutAndPaymentNotices;
} ) => ( {
	select,
	dispatch,
	registry,
}: {
	select: SelectFromMap< typeof selectors >;
	dispatch: DispatchFromMap< typeof actions >;
	registry: DataRegistry;
} ) => void;

/**
 * Type for emitValidateEventType() thunk
 */
export type emitValidateEventType = ( {
	observers,
	setValidationErrors,
}: {
	observers: EventObserversType;
	setValidationErrors: (
		errors: Record< string, FieldValidationStatus >
	) => void;
} ) => ( {
	dispatch,
	registry,
}: {
	dispatch: DispatchFromMap< typeof actions >;
	registry: DataRegistry;
} ) => void;
