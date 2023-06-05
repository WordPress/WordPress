/**
 * External dependencies
 */
import { useBlockProps, InnerBlocks } from '@wordpress/block-editor';
import { Main } from '@woocommerce/base-components/sidebar-layout';
import { innerBlockAreas } from '@woocommerce/blocks-checkout';
import type { TemplateArray } from '@wordpress/blocks';
/**
 * Internal dependencies
 */
import {
	useForcedLayout,
	getAllowedBlocks,
} from '../../../cart-checkout-shared';

interface Props {
	clientId: string;
}

export const Edit = ( { clientId }: Props ): JSX.Element => {
	const blockProps = useBlockProps( { className: 'wc-block-cart__main' } );
	const allowedBlocks = getAllowedBlocks( innerBlockAreas.CART_ITEMS );
	const defaultTemplate = [
		[ 'woocommerce/cart-line-items-block', {}, [] ],
		[ 'woocommerce/cart-cross-sells-block', {}, [] ],
	] as unknown as TemplateArray;

	useForcedLayout( {
		clientId,
		registeredBlocks: allowedBlocks,
		defaultTemplate,
	} );
	return (
		<Main { ...blockProps }>
			<InnerBlocks
				allowedBlocks={ allowedBlocks }
				template={ defaultTemplate }
				templateLock={ false }
				renderAppender={ InnerBlocks.ButtonBlockAppender }
			/>
		</Main>
	);
};

export const Save = (): JSX.Element => {
	return (
		<div { ...useBlockProps.save() }>
			<InnerBlocks.Content />
		</div>
	);
};
