/**
 * External dependencies
 */
import { previewCategories } from '@woocommerce/resource-previews';
import type { Block } from '@wordpress/blocks';

type ExampleBlock = Block[ 'example' ] & {
	attributes: {
		categoryId: 'preview' | number;
		previewCategory: typeof previewCategories[ number ];
		editMode: false;
	};
};

export const example: ExampleBlock = {
	attributes: {
		categoryId: 'preview',
		previewCategory: previewCategories[ 0 ],
		editMode: false,
	},
} as const;
