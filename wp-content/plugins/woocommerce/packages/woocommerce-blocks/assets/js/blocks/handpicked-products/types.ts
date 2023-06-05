/**
 * External dependencies
 */
import type { BlockAlignment } from '@wordpress/blocks';

export interface Attributes {
	align?: BlockAlignment;
	columns: number;
	editMode: boolean;
	contentVisibility: {
		image: boolean;
		title: boolean;
		price: boolean;
		rating: boolean;
		button: boolean;
	};
	orderby:
		| 'date'
		| 'popularity'
		| 'price_asc'
		| 'price_desc'
		| 'rating'
		| 'title'
		| 'menu_order';
	products: Array< number >;
	alignButtons: boolean;
	isPreview: boolean;
}
/**
 * Component to handle edit mode of "Hand-picked Products".
 */
export interface Props {
	/**
	 * The attributes for this block
	 */
	attributes: Attributes;
	/**
	 * The register block name.
	 */
	name: string;
	/**
	 * A callback to update attributes
	 */
	setAttributes: ( attributes: Record< string, unknown > ) => void;
	// from withSpokenMessages
	debouncedSpeak: ( message: string ) => void;
}
