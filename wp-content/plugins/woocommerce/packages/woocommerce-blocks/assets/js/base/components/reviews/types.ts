/**
 * External dependencies
 */
import { EmptyObjectType } from '@woocommerce/types';

export type Review =
	| {
			date_created: string;
			date_created_gmt: string;
			formatted_date_created: string;
			product_name: string;
			product_permalink: string;
			review: string;
			reviewer: string;
			id: number;
			product_id: number;
			product_image: {
				alt: string;
				thumbnail: string;
				name: string;
				sizes: string;
				src: string;
				srcset: string;
			};
			reviewer_avatar_urls: { [ size: string ]: string };
			verified: boolean;
			rating: number;
	  }
	| EmptyObjectType;
