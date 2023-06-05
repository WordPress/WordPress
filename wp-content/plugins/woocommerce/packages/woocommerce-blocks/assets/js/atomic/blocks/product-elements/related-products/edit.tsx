/**
 * External dependencies
 */
import {
	BLOCK_ATTRIBUTES,
	INNER_BLOCKS_TEMPLATE,
} from '@woocommerce/blocks/product-query/variations';
import {
	InnerBlocks,
	InspectorControls,
	useBlockProps,
} from '@wordpress/block-editor';
import { InnerBlockTemplate } from '@wordpress/blocks';
import { Notice } from '@wordpress/components';
import { __ } from '@wordpress/i18n';

/**
 * Internal dependencies
 */
import './editor.scss';

const Edit = () => {
	const TEMPLATE: InnerBlockTemplate[] = [
		[ 'core/query', BLOCK_ATTRIBUTES, INNER_BLOCKS_TEMPLATE ],
	];
	const blockProps = useBlockProps();

	return (
		<div { ...blockProps }>
			<InspectorControls>
				<Notice
					className={ 'wc-block-editor-related-products__notice' }
					status={ 'warning' }
					isDismissible={ false }
				>
					<p>
						{ __(
							'These products will vary depending on the main product in the page',
							'woo-gutenberg-products-block'
						) }
					</p>
				</Notice>
			</InspectorControls>
			<InnerBlocks template={ TEMPLATE } />
		</div>
	);
};

export default Edit;
