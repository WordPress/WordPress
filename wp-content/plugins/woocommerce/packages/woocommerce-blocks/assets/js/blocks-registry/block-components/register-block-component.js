/**
 * External dependencies
 */
import deprecated from '@wordpress/deprecated';

/**
 * Internal dependencies
 */
import { registeredBlockComponents } from './registered-block-components-init';

/**
 * Register a Block Component.
 *
 * WooCommerce Blocks allows React Components to be used on the frontend of the store in place of
 * Blocks instead of just serving static content.
 *
 * Registering a Block Component allows you to define which React Component should be used in place
 * of a registered Block. The Component, when rendered, will be passed all Block Attributes.
 *
 * @param {Object}   options           Options to use when registering the block.
 * @param {Function} options.component React component that will be rendered, or the return value from  React.lazy if
 *                                     dynamically imported.
 * @param {string}   options.blockName Name of the block that this component belongs to.
 * @param {string}   [options.context] To make this component available only under a certain context
 *                                     (named parent Block) define it here. If left blank, the
 *                                     Component will be available for all contexts.
 */
export function registerBlockComponent( options ) {
	if ( ! options.context ) {
		options.context = 'any';
	}
	assertOption( options, 'context', 'string' );
	assertOption( options, 'blockName', 'string' );
	assertBlockComponent( options, 'component' );

	const { context, blockName, component } = options;

	if ( ! registeredBlockComponents[ context ] ) {
		registeredBlockComponents[ context ] = {};
	}

	registeredBlockComponents[ context ][ blockName ] = component;
}

/**
 * Asserts that an option is a valid react element or lazy callback. Otherwise, throws an error.
 *
 * @throws Will throw an error if the type of the option doesn't match the expected type.
 * @param {Object} options    Object containing the option to validate.
 * @param {string} optionName Name of the option to validate.
 */
const assertBlockComponent = ( options, optionName ) => {
	if ( options[ optionName ] ) {
		if ( typeof options[ optionName ] === 'function' ) {
			return;
		}
		if (
			options[ optionName ].$$typeof &&
			options[ optionName ].$$typeof === Symbol.for( 'react.lazy' )
		) {
			return;
		}
	}
	throw new Error(
		`Incorrect value for the ${ optionName } argument when registering a block component. Component must be a valid React Element or Lazy callback.`
	);
};

/**
 * Asserts that an option is of the given type. Otherwise, throws an error.
 *
 * @throws Will throw an error if the type of the option doesn't match the expected type.
 * @param {Object} options      Object containing the option to validate.
 * @param {string} optionName   Name of the option to validate.
 * @param {string} expectedType Type expected for the option.
 */
const assertOption = ( options, optionName, expectedType ) => {
	const actualType = typeof options[ optionName ];
	if ( actualType !== expectedType ) {
		throw new Error(
			`Incorrect value for the ${ optionName } argument when registering a block component. It was a ${ actualType }, but must be a ${ expectedType }.`
		);
	}
};

/**
 * Alias of registerBlockComponent kept for backwards compatibility.
 *
 * @param {Object}   options           Options to use when registering the block.
 * @param {string}   options.main      Name of the parent block.
 * @param {string}   options.blockName Name of the child block being registered.
 * @param {Function} options.component React component used to render the child block.
 */
export function registerInnerBlock( options ) {
	deprecated( 'registerInnerBlock', {
		version: '2.8.0',
		alternative: 'registerBlockComponent',
		plugin: 'WooCommerce Blocks',
		hint: '"main" has been replaced with "context" and is now optional.',
	} );
	assertOption( options, 'main', 'string' );
	registerBlockComponent( {
		...options,
		context: options.main,
	} );
}
