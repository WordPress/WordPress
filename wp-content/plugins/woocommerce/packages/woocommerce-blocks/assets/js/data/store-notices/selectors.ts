/**
 * Internal dependencies
 */
import { StoreNoticesState } from './default-state';

export const getRegisteredContainers = (
	state: StoreNoticesState
): StoreNoticesState[ 'containers' ] => state.containers;
