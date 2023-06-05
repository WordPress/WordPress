import { hydrate, render } from 'preact';
import { toVdom, hydratedIslands } from './vdom';
import { createRootFragment } from './utils';
import { csnMetaTagItemprop, directivePrefix } from './constants';

// The root to render the vdom (document.body).
let rootFragment;

// The cache of visited and prefetched pages and stylesheets.
const pages = new Map();
const stylesheets = new Map();

// Helper to remove domain and hash from the URL. We are only interesting in
// caching the path and the query.
const cleanUrl = ( url ) => {
	const u = new URL( url, window.location );
	return u.pathname + u.search;
};

// Helper to check if a page can do client-side navigation.
export const canDoClientSideNavigation = ( dom ) =>
	dom
		.querySelector( `meta[itemprop='${ csnMetaTagItemprop }']` )
		?.getAttribute( 'content' ) === 'active';

// Fetch styles of a new page.
const fetchHead = async ( head ) => {
	const sheets = await Promise.all(
		[].map.call(
			head.querySelectorAll( "link[rel='stylesheet']" ),
			( link ) => {
				const href = link.getAttribute( 'href' );
				if ( ! stylesheets.has( href ) )
					stylesheets.set(
						href,
						fetch( href ).then( ( r ) => r.text() )
					);
				return stylesheets.get( href );
			}
		)
	);
	const stylesFromSheets = sheets.map( ( sheet ) => {
		const style = document.createElement( 'style' );
		style.textContent = sheet;
		return style;
	} );
	return [
		head.querySelector( 'title' ),
		...head.querySelectorAll( 'style' ),
		...stylesFromSheets,
	];
};

// Fetch a new page and convert it to a static virtual DOM.
const fetchPage = async ( url ) => {
	const html = await window.fetch( url ).then( ( r ) => r.text() );
	const dom = new window.DOMParser().parseFromString( html, 'text/html' );
	if ( ! canDoClientSideNavigation( dom.head ) ) return false;
	const head = await fetchHead( dom.head );
	return { head, body: toVdom( dom.body ) };
};

// Prefetch a page. We store the promise to avoid triggering a second fetch for
// a page if a fetching has already started.
export const prefetch = ( url ) => {
	url = cleanUrl( url );
	if ( ! pages.has( url ) ) {
		pages.set( url, fetchPage( url ) );
	}
};

// Navigate to a new page.
export const navigate = async ( href ) => {
	const url = cleanUrl( href );
	prefetch( url );
	const page = await pages.get( url );
	if ( page ) {
		document.head.replaceChildren( ...page.head );
		render( page.body, rootFragment );
		window.history.pushState( {}, '', href );
	} else {
		window.location.assign( href );
	}
};

// Listen to the back and forward buttons and restore the page if it's in the
// cache.
window.addEventListener( 'popstate', async () => {
	const url = cleanUrl( window.location ); // Remove hash.
	const page = pages.has( url ) && ( await pages.get( url ) );
	if ( page ) {
		document.head.replaceChildren( ...page.head );
		render( page.body, rootFragment );
	} else {
		window.location.reload();
	}
} );

// Initialize the router with the initial DOM.
export const init = async () => {
	if ( canDoClientSideNavigation( document.head ) ) {
		// Create the root fragment to hydrate everything.
		rootFragment = createRootFragment(
			document.documentElement,
			document.body
		);

		const body = toVdom( document.body );
		hydrate( body, rootFragment );

		const head = await fetchHead( document.head );
		pages.set(
			cleanUrl( window.location ),
			Promise.resolve( { body, head } )
		);
	} else {
		document
			.querySelectorAll( `[${ directivePrefix }island]` )
			.forEach( ( node ) => {
				if ( ! hydratedIslands.has( node ) ) {
					const fragment = createRootFragment(
						node.parentNode,
						node
					);
					const vdom = toVdom( node );
					hydrate( vdom, fragment );
				}
			} );
	}
};
