/**
 * Internal dependencies
 */
import { actions } from './reducer';
import type { ActionType, ActionCallbackType } from './types';

export const emitterCallback =
	( type: string, observerDispatch: React.Dispatch< ActionType > ) =>
	( callback: ActionCallbackType, priority = 10 ): ( () => void ) => {
		const action = actions.addEventCallback( type, callback, priority );
		observerDispatch( action );
		return () => {
			observerDispatch( actions.removeEventCallback( type, action.id ) );
		};
	};
