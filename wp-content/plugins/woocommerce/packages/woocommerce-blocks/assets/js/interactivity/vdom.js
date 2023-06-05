import { h } from 'preact';
import { directivePrefix as p } from './constants';

const ignoreAttr = `${ p }ignore`;
const islandAttr = `${ p }island`;
const directiveParser = new RegExp( `${ p }([^:]+):?(.*)$` );

export const hydratedIslands = new WeakSet();

// Recursive function that transfoms a DOM tree into vDOM.
export function toVdom( node ) {
	const props = {};
	const { attributes, childNodes } = node;
	const directives = {};
	let hasDirectives = false;
	let ignore = false;
	let island = false;

	if ( node.nodeType === 3 ) return node.data;
	if ( node.nodeType === 4 ) {
		node.replaceWith( new Text( node.nodeValue ) );
		return node.nodeValue;
	}

	for ( let i = 0; i < attributes.length; i++ ) {
		const n = attributes[ i ].name;
		if ( n[ p.length ] && n.slice( 0, p.length ) === p ) {
			if ( n === ignoreAttr ) {
				ignore = true;
			} else if ( n === islandAttr ) {
				island = true;
			} else {
				hasDirectives = true;
				let val = attributes[ i ].value;
				try {
					val = JSON.parse( val );
				} catch ( e ) {}
				const [ , prefix, suffix ] = directiveParser.exec( n );
				directives[ prefix ] = directives[ prefix ] || {};
				directives[ prefix ][ suffix || 'default' ] = val;
			}
		} else if ( n === 'ref' ) {
			continue;
		} else {
			props[ n ] = attributes[ i ].value;
		}
	}

	if ( ignore && ! island )
		return h( node.localName, {
			dangerouslySetInnerHTML: { __html: node.innerHTML },
		} );
	if ( island ) hydratedIslands.add( node );

	if ( hasDirectives ) props.directives = directives;

	const children = [];
	for ( let i = 0; i < childNodes.length; i++ ) {
		const child = childNodes[ i ];
		if ( child.nodeType === 8 || child.nodeType === 7 ) {
			child.remove();
			i--;
		} else {
			children.push( toVdom( child ) );
		}
	}

	return h( node.localName, props, children );
}
