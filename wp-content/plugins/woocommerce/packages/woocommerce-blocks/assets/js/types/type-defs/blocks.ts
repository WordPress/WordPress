/**
 * External dependencies
 */
import type { BlockEditProps, BlockInstance } from '@wordpress/blocks';
import type { LazyExoticComponent } from 'react';

export type EditorBlock< T > = BlockInstance< T > & BlockEditProps< T >;

export type RegisteredBlockComponent =
	| LazyExoticComponent< React.ComponentType< unknown > >
	| ( () => JSX.Element | null )
	| null;
