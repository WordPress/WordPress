/**
 * External dependencies
 */
import { uniqueId } from 'lodash';

/**
 * Internal dependencies
 */
import {
	ACTION,
	ActionType,
	ActionCallbackType,
	EventObserversType,
} from './types';

export const actions = {
	addEventCallback: (
		eventType: string,
		callback: ActionCallbackType,
		priority = 10
	): ActionType => {
		return {
			id: uniqueId(),
			type: ACTION.ADD_EVENT_CALLBACK,
			eventType,
			callback,
			priority,
		};
	},
	removeEventCallback: ( eventType: string, id: string ): ActionType => {
		return {
			id,
			type: ACTION.REMOVE_EVENT_CALLBACK,
			eventType,
		};
	},
};

const initialState = {} as EventObserversType;

/**
 * Handles actions for emitters
 */
export const reducer = (
	state = initialState,
	{ type, eventType, id, callback, priority }: ActionType
): typeof initialState => {
	const newEvents = state.hasOwnProperty( eventType )
		? new Map( state[ eventType ] )
		: new Map();
	switch ( type ) {
		case ACTION.ADD_EVENT_CALLBACK:
			newEvents.set( id, { priority, callback } );
			return {
				...state,
				[ eventType ]: newEvents,
			};
		case ACTION.REMOVE_EVENT_CALLBACK:
			newEvents.delete( id );
			return {
				...state,
				[ eventType ]: newEvents,
			};
	}
};
