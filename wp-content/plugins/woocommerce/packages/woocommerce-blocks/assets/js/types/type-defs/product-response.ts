/**
 * Internal dependencies
 */
import type { CurrencyResponse } from './currency';

export interface ProductResponseItemPrices extends CurrencyResponse {
	price: string;
	regular_price: string;
	sale_price: string;
	price_range: null | { min_amount: string; max_amount: string };
}

export interface ProductResponseItemBaseData {
	value: string;
	display?: string;
	hidden?: boolean;
	className?: string;
}

export type ProductResponseItemData = ProductResponseItemBaseData &
	( { key: string; name?: never } | { key?: never; name: string } );

export interface ProductResponseImageItem {
	id: number;
	src: string;
	thumbnail: string;
	srcset: string;
	sizes: string;
	name: string;
	alt: string;
}

export interface ProductResponseTermItem {
	default?: boolean;
	id: number;
	name: string;
	slug: string;
	link?: string;
}

export interface ProductResponseAttributeItem {
	id: number;
	name: string;
	taxonomy: string;
	has_variations: boolean;
	terms: Array< ProductResponseTermItem >;
}

export interface ProductResponseVariationsItem {
	id: number;
	attributes: Array< ProductResponseVariationAttributeItem >;
}

export interface ProductResponseVariationAttributeItem {
	name: string;
	value: string;
}

export interface ProductResponseItem {
	id: number;
	name: string;
	parent: number;
	type: string;
	variation: string;
	permalink: string;
	sku: string;
	short_description: string;
	description: string;
	on_sale: boolean;
	prices: ProductResponseItemPrices;
	price_html: string;
	average_rating: string;
	review_count: number;
	images: Array< ProductResponseImageItem >;
	categories: Array< ProductResponseTermItem >;
	tags: Array< ProductResponseTermItem >;
	attributes: Array< ProductResponseAttributeItem >;
	variations: Array< ProductResponseVariationsItem >;
	has_options: boolean;
	is_purchasable: boolean;
	is_in_stock: boolean;
	is_on_backorder: boolean;
	low_stock_remaining: null | number;
	sold_individually: boolean;
	add_to_cart: {
		text: string;
		description: string;
		url: string;
		minimum: number;
		maximum: number;
		multiple_of: number;
	};
}
