/**
 * External dependencies
 */
import { useEffect, useState } from '@wordpress/element';
import type { Dispatch, SetStateAction } from 'react';

export const useLocalStorageState = < T >(
	key: string,
	initialValue: T
): [ T, Dispatch< SetStateAction< T > > ] => {
	const [ state, setState ] = useState< T >( () => {
		const valueInLocalStorage = window.localStorage.getItem( key );
		if ( valueInLocalStorage ) {
			try {
				return JSON.parse( valueInLocalStorage );
			} catch {
				// eslint-disable-next-line no-console
				console.error(
					`Value for key '${ key }' could not be retrieved from localStorage because it can't be parsed.`
				);
			}
		}
		return initialValue;
	} );
	useEffect( () => {
		try {
			window.localStorage.setItem( key, JSON.stringify( state ) );
		} catch {
			// eslint-disable-next-line no-console
			console.error(
				`Value for key '${ key }' could not be saved in localStorage because it can't be converted into a string.`
			);
		}
	}, [ key, state ] );

	return [ state, setState ];
};
