/**
 * Internal dependencies
 */
import {
	getObserversByPriority,
	isErrorResponse,
	isFailResponse,
	ObserverResponse,
	responseTypes,
} from './utils';
import type { EventObserversType } from './types';
import { isObserverResponse } from '../../../types/type-guards/observers';

/**
 * Emits events on registered observers for the provided type and passes along
 * the provided data.
 *
 * This event emitter will silently catch promise errors, but doesn't care
 * otherwise if any errors are caused by observers. So events that do care
 * should use `emitEventWithAbort` instead.
 *
 * @param {Object} observers The registered observers to omit to.
 * @param {string} eventType The event type being emitted.
 * @param {*}      data      Data passed along to the observer when it is invoked.
 *
 * @return {Promise} A promise that resolves to true after all observers have executed.
 */
export const emitEvent = async (
	observers: EventObserversType,
	eventType: string,
	data: unknown
): Promise< unknown > => {
	const observersByType = getObserversByPriority( observers, eventType );
	const observerResponses = [];
	for ( const observer of observersByType ) {
		try {
			const observerResponse = await Promise.resolve(
				observer.callback( data )
			);
			if ( typeof observerResponse === 'object' ) {
				observerResponses.push( observerResponse );
			}
		} catch ( e ) {
			// we don't care about errors blocking execution, but will console.error for troubleshooting.
			// eslint-disable-next-line no-console
			console.error( e );
		}
	}
	return observerResponses.length ? observerResponses : true;
};

/**
 * Emits events on registered observers for the provided type and passes along
 * the provided data. This event emitter will abort if an observer throws an
 * error or if the response includes an object with an error type property.
 *
 * Any successful observer responses before abort will be included in the returned package.
 *
 * @param {Object} observers The registered observers to omit to.
 * @param {string} eventType The event type being emitted.
 * @param {*}      data      Data passed along to the observer when it is invoked.
 *
 * @return {Promise} Returns a promise that resolves to either boolean, or an array of responses
 *                   from registered observers that were invoked up to the point of an error.
 */
export const emitEventWithAbort = async (
	observers: EventObserversType,
	eventType: string,
	data: unknown
): Promise< ObserverResponse[] > => {
	const observerResponses: ObserverResponse[] = [];
	const observersByType = getObserversByPriority( observers, eventType );
	for ( const observer of observersByType ) {
		try {
			const response = await Promise.resolve( observer.callback( data ) );
			if ( ! isObserverResponse( response ) ) {
				continue;
			}
			if ( ! response.hasOwnProperty( 'type' ) ) {
				throw new Error(
					'Returned objects from event emitter observers must return an object with a type property'
				);
			}
			if ( isErrorResponse( response ) || isFailResponse( response ) ) {
				observerResponses.push( response );
				// early abort.
				return observerResponses;
			}
			// all potential abort conditions have been considered push the
			// response to the array.
			observerResponses.push( response );
		} catch ( e ) {
			// We don't handle thrown errors but just console.log for troubleshooting.
			// eslint-disable-next-line no-console
			console.error( e );
			observerResponses.push( { type: responseTypes.ERROR } );
			return observerResponses;
		}
	}
	return observerResponses;
};
