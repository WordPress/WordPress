/**
 * External dependencies
 */
import type { FunctionKeys } from 'utility-types';

/**
 * Mapped types
 *
 * This module should only contain mapped types, operations useful in the type system
 * that do not produce any runtime code.
 *
 * Mapped types can be thought of as functions in the type system, they accept some type
 * argument and transform it to another type.
 *
 * @see https://www.typescriptlang.org/docs/handbook/advanced-types.html#mapped-types
 */

/* eslint-disable @typescript-eslint/no-explicit-any */
/* eslint-disable @typescript-eslint/no-shadow */

/**
 * Maps a "raw" selector object to the selectors available when registered on the @wordpress/data store.
 *
 * @template S Selector map, usually from `import * as selectors from './my-store/selectors';`
 */
// eslint-disable-next-line @typescript-eslint/ban-types
export type SelectFromMap< S extends object > = {
	[ selector in FunctionKeys< S > ]: (
		...args: TailParameters< S[ selector ] >
	) => ReturnType< S[ selector ] >;
};

/**
 * Maps a "raw" resolver object to the resolvers available on a @wordpress/data store.
 *
 * @template R Resolver map, usually from `import * as resolvers from './my-store/resolvers';`
 */
export type ResolveSelectFromMap< R extends object > = {
	[ resolver in FunctionKeys< R > ]: (
		...args: ReturnType< R[ resolver ] > extends Promise< any >
			? Parameters< R[ resolver ] >
			: TailParameters< R[ resolver ] >
	) => ReturnType< R[ resolver ] > extends Promise< any >
		? Promise< ReturnType< R[ resolver ] > >
		: void;
};

/**
 * Maps a "raw" actionCreators object to the actions available when registered on the @wordpress/data store.
 *
 * @template A Selector map, usually from `import * as actions from './my-store/actions';`
 */
export type DispatchFromMap<
	A extends Record< string, ( ...args: any[] ) => any >
> = {
	[ actionCreator in keyof A ]: (
		...args: Parameters< A[ actionCreator ] >
	) => // If the action creator is a function that returns a generator return GeneratorReturnType, if not, then check
	// if it's a function that returns a Promise, in other words: a thunk. https://developer.wordpress.org/block-editor/how-to-guides/thunks/
	// If it is, then return the return type of the thunk (which in most cases will be void, but sometimes it won't be).
	A[ actionCreator ] extends ( ...args: any[] ) => Generator
		? Promise< GeneratorReturnType< A[ actionCreator ] > >
		: A[ actionCreator ] extends Thunk
		? ThunkReturnType< A[ actionCreator ] >
		: void;
};

/**
 * A thunk is a function (action creator) that returns a function.
 */
type Thunk = ( ...args: any[] ) => ( ...args: any[] ) => any;
/**
 * The function returned by a thunk action creator can return a value, too.
 */
type ThunkReturnType< A extends Thunk > = ReturnType< ReturnType< A > >;

/**
 * Parameters type of a function, excluding the first parameter.
 *
 * This is useful for typing some @wordpres/data functions that make a leading
 * `state` argument implicit.
 */
// eslint-disable-next-line @typescript-eslint/ban-types
export type TailParameters< F extends Function > = F extends (
	head: any,
	...tail: infer T
) => any
	? T
	: never;

/**
 * Obtain the type finally returned by the generator when it's done iterating.
 */
export type GeneratorReturnType< T extends ( ...args: any[] ) => Generator > =
	T extends ( ...args: any ) => Generator< any, infer R, any > ? R : never;

/**
 * Usually we use ReturnType of all the action creators to deduce all the actions.
 * This works until one of the action creators is a generator and doesn't actually "Return" an action.
 * This type helper allows for actions to be both functions and generators
 */
export type ReturnOrGeneratorYieldUnion< T extends ( ...args: any ) => any > =
	T extends ( ...args: any ) => infer Return
		? Return extends Generator< infer T, infer U, any >
			? T | U
			: Return
		: never;
