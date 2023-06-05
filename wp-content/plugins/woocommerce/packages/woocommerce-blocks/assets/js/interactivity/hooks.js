import { h, options, createContext } from 'preact';
import { useRef } from 'preact/hooks';
import { rawStore as store } from './store';
import { componentPrefix } from './constants';

// Main context.
const context = createContext( {} );

// WordPress Directives.
const directiveMap = {};
export const directive = ( name, cb ) => {
	directiveMap[ name ] = cb;
};

// WordPress Components.
const componentMap = {};
export const component = ( name, Comp ) => {
	componentMap[ name ] = Comp;
};

// Resolve the path to some property of the store object.
const resolve = ( path, context ) => {
	let current = { ...store, context };
	path.split( '.' ).forEach( ( p ) => ( current = current[ p ] ) );
	return current;
};

// Generate the evaluate function.
const getEvaluate =
	( { ref } = {} ) =>
	( path, extraArgs = {} ) => {
		const value = resolve( path, extraArgs.context );
		return typeof value === 'function'
			? value( {
					state: store.state,
					...( ref !== undefined ? { ref } : {} ),
					...extraArgs,
			  } )
			: value;
	};

// Directive wrapper.
const Directive = ( { type, directives, props: originalProps } ) => {
	const ref = useRef( null );
	const element = h( type, { ...originalProps, ref, _wrapped: true } );
	const props = { ...originalProps, children: element };
	const evaluate = getEvaluate( { ref: ref.current } );
	const directiveArgs = { directives, props, element, context, evaluate };

	for ( const d in directives ) {
		const wrapper = directiveMap[ d ]?.( directiveArgs );
		if ( wrapper !== undefined ) props.children = wrapper;
	}

	return props.children;
};

// Preact Options Hook called each time a vnode is created.
const old = options.vnode;
options.vnode = ( vnode ) => {
	const type = vnode.type;
	const { directives } = vnode.props;

	if (
		typeof type === 'string' &&
		type.slice( 0, componentPrefix.length ) === componentPrefix
	) {
		vnode.props.children = h(
			componentMap[ type.slice( componentPrefix.length ) ],
			{ ...vnode.props, context, evaluate: getEvaluate() },
			vnode.props.children
		);
	} else if ( directives ) {
		const props = vnode.props;
		delete props.directives;
		if ( ! props._wrapped ) {
			vnode.props = { type: vnode.type, directives, props };
			vnode.type = Directive;
		} else {
			delete props._wrapped;
		}
	}

	if ( old ) old( vnode );
};
