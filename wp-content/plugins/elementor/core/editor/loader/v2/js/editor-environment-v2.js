if ( ! window.elementorV2?.env ) {
	throw new Error( 'The "@elementor/env" package was not loaded.' );
}

window.elementorV2.env.initEnv( window.elementorEditorV2Env );
