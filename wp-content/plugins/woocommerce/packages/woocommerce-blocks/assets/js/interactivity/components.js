import { useMemo, useContext } from 'preact/hooks';
import { deepSignal } from 'deepsignal';
import { component } from './hooks';

export default () => {
	// <wp-context>
	const Context = ( { children, data, context: { Provider } } ) => {
		const signals = useMemo(
			() => deepSignal( JSON.parse( data ) ),
			[ data ]
		);
		return <Provider value={ signals }>{ children }</Provider>;
	};
	component( 'context', Context );

	// <wp-show>
	const Show = ( { children, when, evaluate, context } ) => {
		const contextValue = useContext( context );
		if ( evaluate( when, { context: contextValue } ) ) {
			return children;
		} else {
			return <template>{ children }</template>;
		}
	};
	component( 'show', Show );
};
