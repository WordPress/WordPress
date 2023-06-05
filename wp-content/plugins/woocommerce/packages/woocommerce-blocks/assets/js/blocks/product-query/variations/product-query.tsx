/**
 * External dependencies
 */
import {
	registerBlockVariation,
	unregisterBlockVariation,
} from '@wordpress/blocks';
import { Icon } from '@wordpress/components';
import { __ } from '@wordpress/i18n';
import { stacks } from '@woocommerce/icons';
import { isWpVersion } from '@woocommerce/settings';
import { select, subscribe } from '@wordpress/data';
import { QueryBlockAttributes } from '@woocommerce/blocks/product-query/types';

/**
 * Internal dependencies
 */
import {
	DEFAULT_ALLOWED_CONTROLS,
	INNER_BLOCKS_TEMPLATE,
	QUERY_DEFAULT_ATTRIBUTES,
	QUERY_LOOP_ID,
} from '../constants';

export const VARIATION_NAME = 'woocommerce/product-query';

const ARCHIVE_PRODUCT_TEMPLATES = [
	'woocommerce/woocommerce//archive-product',
	'woocommerce/woocommerce//taxonomy-product_cat',
	'woocommerce/woocommerce//taxonomy-product_tag',
	'woocommerce/woocommerce//taxonomy-product_attribute',
	'woocommerce/woocommerce//product-search-results',
];

const registerProductsBlock = ( attributes: QueryBlockAttributes ) => {
	registerBlockVariation( QUERY_LOOP_ID, {
		description: __(
			'A block that displays a selection of products in your store.',
			'woo-gutenberg-products-block'
		),
		name: VARIATION_NAME,
		/* translators: “Products“ is the name of the block. */
		title: __( 'Products (Beta)', 'woo-gutenberg-products-block' ),
		isActive: ( blockAttributes ) =>
			blockAttributes.namespace === VARIATION_NAME,
		icon: (
			<Icon
				icon={ stacks }
				className="wc-block-editor-components-block-icon wc-block-editor-components-block-icon--stacks"
			/>
		),
		attributes: {
			...attributes,
			namespace: VARIATION_NAME,
		},
		// Gutenberg doesn't support this type yet, discussion here:
		// https://github.com/WordPress/gutenberg/pull/43632
		// eslint-disable-next-line @typescript-eslint/ban-ts-comment
		// @ts-ignore
		allowedControls: DEFAULT_ALLOWED_CONTROLS,
		innerBlocks: INNER_BLOCKS_TEMPLATE,
		scope: [ 'inserter' ],
	} );
};

if ( isWpVersion( '6.1', '>=' ) ) {
	const store = select( 'core/edit-site' );

	if ( store ) {
		let currentTemplateId: string | undefined;

		subscribe( () => {
			const previousTemplateId = currentTemplateId;

			currentTemplateId = store?.getEditedPostId();

			if ( previousTemplateId === currentTemplateId ) {
				return;
			}

			const queryAttributes = {
				...QUERY_DEFAULT_ATTRIBUTES,
				query: {
					...QUERY_DEFAULT_ATTRIBUTES.query,
					inherit:
						ARCHIVE_PRODUCT_TEMPLATES.includes( currentTemplateId ),
				},
			};

			unregisterBlockVariation( QUERY_LOOP_ID, VARIATION_NAME );

			registerProductsBlock( queryAttributes );
		} );
	} else {
		registerProductsBlock( QUERY_DEFAULT_ATTRIBUTES );
	}
}
