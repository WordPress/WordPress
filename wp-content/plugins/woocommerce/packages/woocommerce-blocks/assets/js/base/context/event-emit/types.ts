export enum ACTION {
	ADD_EVENT_CALLBACK = 'add_event_callback',
	REMOVE_EVENT_CALLBACK = 'remove_event_callback',
}

export type ActionCallbackType = ( ...args: unknown[] ) => unknown;

export type ActionType = {
	type: ACTION;
	eventType: string;
	id: string;
	callback?: ActionCallbackType;
	priority?: number;
};

export type ObserverType = { priority: number; callback: ActionCallbackType };
export type ObserversType = Map< string, ObserverType >;
export type EventObserversType = Record< string, ObserversType >;
