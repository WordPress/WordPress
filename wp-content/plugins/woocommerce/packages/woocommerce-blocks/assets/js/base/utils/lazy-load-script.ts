/**
 * External dependencies
 */
import { isString } from '@woocommerce/types';

interface LazyLoadScriptParams {
	handle: string;
	src: string;
	version?: string;
	after?: string;
	before?: string;
	translations?: string;
}

interface AppendScriptAttributesParam {
	id: string;
	innerHTML?: string;
	onerror?: OnErrorEventHandlerNonNull;
	onload?: () => void;
	src?: string;
}

declare global {
	interface Window {
		wc: {
			wcBlocksRegistry: Record< string, unknown >;
		};
	}
}

/**
 * In WP, registered scripts are loaded into the page with an element like this:
 * `<script src='...' id='[SCRIPT_ID]'></script>`
 * This function checks whether an element matching that selector exists.
 * Useful to know if a script has already been appended to the page.
 */
const isScriptTagInDOM = ( scriptId: string, src = '' ): boolean => {
	// If the store is using a plugin to concatenate scripts, we might have some
	// cases where we don't detect whether a script has already been loaded.
	// Because of that, we add an extra protection to the wc-blocks-registry-js
	// script, to avoid ending up with two registries.
	if ( scriptId === 'wc-blocks-registry-js' ) {
		if ( typeof window?.wc?.wcBlocksRegistry === 'object' ) {
			return true;
		}
	}

	const srcParts = src.split( '?' );
	if ( srcParts?.length > 1 ) {
		src = srcParts[ 0 ];
	}
	const selector = src
		? `script#${ scriptId }, script[src*="${ src }"]`
		: `script#${ scriptId }`;
	const scriptElements = document.querySelectorAll( selector );

	return scriptElements.length > 0;
};

/**
 * Appends a script element to the document body if a script with the same id
 * doesn't exist.
 */
const appendScript = ( attributes: AppendScriptAttributesParam ): void => {
	// Abort if id is not valid or a script with the same id exists.
	if (
		! isString( attributes.id ) ||
		isScriptTagInDOM( attributes.id, attributes?.src )
	) {
		return;
	}
	const scriptElement = document.createElement( 'script' );
	for ( const attr in attributes ) {
		// We could technically be iterating over inherited members here, so
		// if this is the case we should skip it.
		if ( ! attributes.hasOwnProperty( attr ) ) {
			continue;
		}
		const key = attr as keyof AppendScriptAttributesParam;

		// Skip the keys that aren't strings, because TS can't be sure which
		// key in the scriptElement object we're assigning to.
		if ( key === 'onload' || key === 'onerror' ) {
			continue;
		}

		// This assignment stops TS complaining about the value maybe being
		// undefined following the isString check below.
		const value = attributes[ key ];
		if ( isString( value ) ) {
			scriptElement[ key ] = value;
		}
	}

	// Now that we've assigned all the strings, we can explicitly assign to the
	// function keys.
	if ( typeof attributes.onload === 'function' ) {
		scriptElement.onload = attributes.onload;
	}
	if ( typeof attributes.onerror === 'function' ) {
		scriptElement.onerror = attributes.onerror;
	}
	document.body.appendChild( scriptElement );
};

/**
 * Appends a `<script>` tag to the document body based on the src and handle
 * parameters. In addition, it appends additional script tags to load the code
 * needed for translations and any before and after inline scripts. See these
 * documentation pages for more information:
 *
 * https://developer.wordpress.org/reference/functions/wp_set_script_translations/
 * https://developer.wordpress.org/reference/functions/wp_add_inline_script/
 */
const lazyLoadScript = ( {
	handle,
	src,
	version,
	after,
	before,
	translations,
}: LazyLoadScriptParams ): Promise< void > => {
	return new Promise( ( resolve, reject ) => {
		if ( isScriptTagInDOM( `${ handle }-js`, src ) ) {
			resolve();
		}

		if ( translations ) {
			appendScript( {
				id: `${ handle }-js-translations`,
				innerHTML: translations,
			} );
		}
		if ( before ) {
			appendScript( {
				id: `${ handle }-js-before`,
				innerHTML: before,
			} );
		}

		const onload = () => {
			if ( after ) {
				appendScript( {
					id: `${ handle }-js-after`,
					innerHTML: after,
				} );
			}
			resolve();
		};

		appendScript( {
			id: `${ handle }-js`,
			onerror: reject,
			onload,
			src: version ? `${ src }?ver=${ version }` : src,
		} );
	} );
};

export default lazyLoadScript;
