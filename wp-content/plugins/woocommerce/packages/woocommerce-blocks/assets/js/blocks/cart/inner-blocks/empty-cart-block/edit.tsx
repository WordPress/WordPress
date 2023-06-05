/**
 * External dependencies
 */
import { __, sprintf } from '@wordpress/i18n';
import { useBlockProps, InnerBlocks } from '@wordpress/block-editor';
import { innerBlockAreas } from '@woocommerce/blocks-checkout';
import type { TemplateArray } from '@wordpress/blocks';
import { useEditorContext } from '@woocommerce/base-context';
import { SHOP_URL } from '@woocommerce/block-settings';

/**
 * Internal dependencies
 */
import {
	useForcedLayout,
	getAllowedBlocks,
} from '../../../cart-checkout-shared';
import './style.scss';

const browseStoreTemplate = SHOP_URL
	? [
			'core/paragraph',
			{
				align: 'center',
				content: sprintf(
					/* translators: %s is the link to the store product directory. */
					__(
						'<a href="%s">Browse store</a>',
						'woo-gutenberg-products-block'
					),
					SHOP_URL
				),
				dropCap: false,
			},
	  ]
	: null;

const defaultTemplate = [
	[
		'core/heading',
		{
			textAlign: 'center',
			content: __(
				'Your cart is currently empty!',
				'woo-gutenberg-products-block'
			),
			level: 2,
			className: 'with-empty-cart-icon wc-block-cart__empty-cart__title',
		},
	],
	browseStoreTemplate,
	[
		'core/separator',
		{
			className: 'is-style-dots',
		},
	],
	[
		'core/heading',
		{
			textAlign: 'center',
			content: __( 'New in store', 'woo-gutenberg-products-block' ),
			level: 2,
		},
	],
	[
		'woocommerce/product-new',
		{
			columns: 3,
			rows: 1,
		},
	],
].filter( Boolean ) as unknown as TemplateArray;

export const Edit = ( { clientId }: { clientId: string } ): JSX.Element => {
	const blockProps = useBlockProps();
	const { currentView } = useEditorContext();
	const allowedBlocks = getAllowedBlocks( innerBlockAreas.EMPTY_CART );

	useForcedLayout( {
		clientId,
		registeredBlocks: allowedBlocks,
		defaultTemplate,
	} );

	return (
		<div
			{ ...blockProps }
			hidden={ currentView !== 'woocommerce/empty-cart-block' }
		>
			<InnerBlocks
				template={ defaultTemplate }
				templateLock={ false }
				renderAppender={ InnerBlocks.ButtonBlockAppender }
			/>
		</div>
	);
};

export const Save = (): JSX.Element => {
	return (
		<div { ...useBlockProps.save() }>
			<InnerBlocks.Content />
		</div>
	);
};
