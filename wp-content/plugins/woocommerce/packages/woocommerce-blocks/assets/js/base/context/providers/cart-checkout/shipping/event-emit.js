/**
 * Internal dependencies
 */
import { emitterCallback, reducer, emitEvent } from '../../../event-emit';

const EMIT_TYPES = {
	SHIPPING_RATES_SUCCESS: 'shipping_rates_success',
	SHIPPING_RATES_FAIL: 'shipping_rates_fail',
	SHIPPING_RATE_SELECT_SUCCESS: 'shipping_rate_select_success',
	SHIPPING_RATE_SELECT_FAIL: 'shipping_rate_select_fail',
};

/**
 * Receives a reducer dispatcher and returns an object with the onSuccess and
 * onFail callback registration points for the shipping option emit events.
 *
 * Calling the event registration function with the callback will register it
 * for the event emitter and will return a dispatcher for removing the
 * registered callback (useful for implementation in `useEffect`).
 *
 * @param {Function} dispatcher A reducer dispatcher
 * @return {Object} An object with `onSuccess` and `onFail` emitter registration.
 */
const emitterObservers = ( dispatcher ) => ( {
	onSuccess: emitterCallback( EMIT_TYPES.SHIPPING_RATES_SUCCESS, dispatcher ),
	onFail: emitterCallback( EMIT_TYPES.SHIPPING_RATES_FAIL, dispatcher ),
	onSelectSuccess: emitterCallback(
		EMIT_TYPES.SHIPPING_RATE_SELECT_SUCCESS,
		dispatcher
	),
	onSelectFail: emitterCallback(
		EMIT_TYPES.SHIPPING_RATE_SELECT_FAIL,
		dispatcher
	),
} );

export { EMIT_TYPES, emitterObservers, reducer, emitEvent };
