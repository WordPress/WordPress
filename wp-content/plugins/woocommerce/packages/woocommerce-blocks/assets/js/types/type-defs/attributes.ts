export interface AttributeSetting {
	attribute_id: string;
	attribute_name: string;
	attribute_label: string;
	attribute_orderby: 'menu_order' | 'name' | 'name_num' | 'id';
	attribute_public: 0 | 1;
	attribute_type: string;
}

export interface AttributeObject {
	count: number;
	has_archives: boolean;
	id: number;
	label: string;
	name: string;
	order: 'menu_order' | 'name' | 'name_num' | 'id';
	parent: number;
	taxonomy: string;
	type: string;
}

export interface AttributeQuery {
	attribute: string;
	operator: 'in' | 'and';
	slug: string[];
}

export interface AttributeTerm {
	attr_slug: string;
	count: number;
	description: string;
	id: number;
	name: string;
	parent: number;
	slug: string;
}

export interface AttributeMetadata {
	taxonomy: string;
	termId: number;
}

export type AttributeWithTerms = AttributeObject & { terms: AttributeTerm[] };
