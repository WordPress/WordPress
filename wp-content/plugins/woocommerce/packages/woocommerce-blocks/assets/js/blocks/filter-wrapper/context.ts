/**
 * External dependencies
 */
import { createContext, useContext } from '@wordpress/element';

/**
 * Context consumed by inner blocks.
 */
export type FilterContextProps = {
	wrapper?: React.RefObject< HTMLDivElement >;
};

export const FilterBlockContext: React.Context< FilterContextProps > =
	createContext< FilterContextProps >( {} );

export const useFilterBlockContext = (): FilterContextProps => {
	return useContext( FilterBlockContext );
};

export const useSetWraperVisibility = () => {
	const { wrapper } = useFilterBlockContext();
	return ( isVisible: boolean ) => {
		if ( ! wrapper ) {
			return;
		}
		if ( wrapper.current ) {
			wrapper.current.hidden = isVisible ? false : true;
		}
	};
};
