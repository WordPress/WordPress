export interface Attributes {
	columns: number;
	rows: number;
	alignButtons: boolean;
	contentVisibility: {
		image: boolean;
		title: boolean;
		price: boolean;
		rating: boolean;
		button: boolean;
	};
	categories: Array< number >;
	catOperator: string;
	isPreview: boolean;
	stockStatus: Array< string >;
	editMode: boolean;
	orderby:
		| 'date'
		| 'popularity'
		| 'price_asc'
		| 'price_desc'
		| 'rating'
		| 'title'
		| 'menu_order';
}

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
	setAttributes: ( attributes: Partial< Attributes > ) => void;
	// from withSpokenMessages
	debouncedSpeak: ( message: string ) => void;
}
