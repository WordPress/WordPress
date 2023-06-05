/**
 * External dependencies
 */
import type { ChangeEventHandler } from 'react';
import type { ProductResponseItem } from '@woocommerce/types';

interface GenerateQueryProps {
	sortValue: string;
	currentPage: number;
	attributes: Attributes;
}

export type LayoutConfig = [ string, { children?: LayoutConfig } ][];

export type Attributes = {
	columns: number;
	rows: number;
	alignButtons?: string;
	align?: string;
	contentVisibility?: {
		orderBy: string;
	};
	orderby?: string;
	order?: string;
	layoutConfig?: LayoutConfig;
};

export type Query = {
	catalog_visibility: 'catalog';
	per_page: number;
	page: number;
	orderby?: string;
	order?: string;
};

export type TotalQuery = Pick< Query, 'catalog_visibility' >;

export type GenerateQuery = ( props: GenerateQueryProps ) => Query;

export type GetSortArgs = ( orderName: string ) =>
	| {
			orderby: string;
			order: string;
	  }
	| undefined;

export type AreQueryTotalsDifferent = (
	next: {
		totalQuery: TotalQuery;
		totalProducts: number;
	},
	current?: {
		totalQuery?: TotalQuery;
	}
) => boolean;

export interface ProductListProps {
	attributes: Attributes;
	currentPage: number;
	onPageChange: ( page: number ) => void;
	onSortChange: ChangeEventHandler;
	sortValue:
		| 'menu_order'
		| 'popularity'
		| 'rating'
		| 'date'
		| 'price'
		| 'price-desc';
	scrollToTop: ( opts: { focusableSelector: string } ) => void;
}

export interface ProductSortSelectProps {
	onChange: ChangeEventHandler;
	value: ProductListProps[ 'sortValue' ];
}

export interface ProductListContainerProps {
	attributes: Attributes;
}

export interface NoMatchingProductsProps {
	resetCallback: () => void;
}

export interface ProductListItemProps {
	product?: Partial< ProductResponseItem >;
	attributes: Attributes;
	instanceId: number;
}

export interface RenderProductLayoutProps {
	blockName: string;
	product: Partial< ProductResponseItem >;
	layoutConfig: LayoutConfig;
	componentId: number;
}
