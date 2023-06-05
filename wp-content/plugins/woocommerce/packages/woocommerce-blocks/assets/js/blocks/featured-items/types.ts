/**
 * External dependencies
 */
import type { Block, BlockEditProps } from '@wordpress/blocks';
import { isNumber } from 'lodash';

export type EditorBlock< T > = Block< T > & BlockEditProps< T >;

export interface Coordinates {
	x: number;
	y: number;
}

export interface GenericBlockUIConfig {
	icon: JSX.Element;
	label: string;
}

export type ImageFit = 'cover' | 'none';

export interface ImageObject {
	id: number;
	src: string;
}

export function isImageObject( obj: unknown ): obj is ImageObject {
	if ( ! obj ) return false;

	return (
		isNumber( ( obj as ImageObject ).id ) &&
		typeof ( obj as ImageObject ).src === 'string'
	);
}
