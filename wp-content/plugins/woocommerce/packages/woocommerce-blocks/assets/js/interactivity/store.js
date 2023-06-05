import { deepSignal } from 'deepsignal';

const isObject = ( item ) =>
	item && typeof item === 'object' && ! Array.isArray( item );

export const deepMerge = ( target, source ) => {
	if ( isObject( target ) && isObject( source ) ) {
		for ( const key in source ) {
			if ( isObject( source[ key ] ) ) {
				if ( ! target[ key ] ) Object.assign( target, { [ key ]: {} } );
				deepMerge( target[ key ], source[ key ] );
			} else {
				Object.assign( target, { [ key ]: source[ key ] } );
			}
		}
	}
};

const getSerializedState = () => {
	const storeTag = document.querySelector(
		`script[type="application/json"]#store`
	);
	if ( ! storeTag ) return {};
	try {
		const { state } = JSON.parse( storeTag.textContent );
		if ( isObject( state ) ) return state;
		throw Error( 'Parsed state is not an object' );
	} catch ( e ) {
		console.log( e );
	}
	return {};
};

const rawState = getSerializedState();
export const rawStore = { state: deepSignal( rawState ) };

if ( typeof window !== 'undefined' ) window.store = rawStore;

export const store = ( { state, ...block } ) => {
	deepMerge( rawStore, block );
	deepMerge( rawState, state );
};
