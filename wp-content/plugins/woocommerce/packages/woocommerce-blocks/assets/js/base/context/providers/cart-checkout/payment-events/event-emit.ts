/**
 * External dependencies
 */
import { useMemo } from '@wordpress/element';

/**
 * Internal dependencies
 */
import {
	reducer,
	emitEvent,
	emitEventWithAbort,
	emitterCallback,
	ActionType,
} from '../../../event-emit';

const EMIT_TYPES = {
	PAYMENT_SETUP: 'payment_setup',
};

type EventEmittersType = Record< string, ReturnType< typeof emitterCallback > >;

/**
 * Receives a reducer dispatcher and returns an object with the
 * various event emitters for the payment processing events.
 *
 * Calling the event registration function with the callback will register it
 * for the event emitter and will return a dispatcher for removing the
 * registered callback (useful for implementation in `useEffect`).
 *
 * @param {Function} observerDispatch The emitter reducer dispatcher.
 * @return {Object} An object with the various payment event emitter registration functions
 */
const useEventEmitters = (
	observerDispatch: React.Dispatch< ActionType >
): EventEmittersType => {
	const eventEmitters = useMemo(
		() => ( {
			onPaymentSetup: emitterCallback(
				EMIT_TYPES.PAYMENT_SETUP,
				observerDispatch
			),
		} ),
		[ observerDispatch ]
	);
	return eventEmitters;
};

export { EMIT_TYPES, useEventEmitters, reducer, emitEvent, emitEventWithAbort };
