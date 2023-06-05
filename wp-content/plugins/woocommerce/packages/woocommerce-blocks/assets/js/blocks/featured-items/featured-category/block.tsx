/**
 * External dependencies
 */
import { withCategory } from '@woocommerce/block-hocs';
import { withSpokenMessages } from '@wordpress/components';
import { compose } from '@wordpress/compose';
import { __ } from '@wordpress/i18n';
import { folderStarred } from '@woocommerce/icons';

/**
 * Internal dependencies
 */
import { withBlockControls } from '../block-controls';
import { withImageEditor } from '../image-editor';
import { withInspectorControls } from '../inspector-controls';
import { withApiError } from '../with-api-error';
import { withEditMode } from '../with-edit-mode';
import { withEditingImage } from '../with-editing-image';
import { withFeaturedItem } from '../with-featured-item';
import { withUpdateButtonAttributes } from '../with-update-button-attributes';

const GENERIC_CONFIG = {
	icon: folderStarred,
	label: __( 'Featured Category', 'woo-gutenberg-products-block' ),
};

const BLOCK_CONTROL_CONFIG = {
	...GENERIC_CONFIG,
	cropLabel: __( 'Edit category image', 'woo-gutenberg-products-block' ),
	editLabel: __( 'Edit selected category', 'woo-gutenberg-products-block' ),
};

const CONTENT_CONFIG = {
	...GENERIC_CONFIG,
	emptyMessage: __(
		'No product category is selected.',
		'woo-gutenberg-products-block'
	),
};

const EDIT_MODE_CONFIG = {
	...GENERIC_CONFIG,
	description: __(
		'Visually highlight a product category and encourage prompt action.',
		'woo-gutenberg-products-block'
	),
	editLabel: __(
		'Showing Featured Product block preview.',
		'woo-gutenberg-products-block'
	),
};

export default compose( [
	withCategory,
	withSpokenMessages,
	withUpdateButtonAttributes,
	withEditingImage,
	withEditMode( EDIT_MODE_CONFIG ),
	withFeaturedItem( CONTENT_CONFIG ),
	withApiError,
	withImageEditor,
	withInspectorControls,
	withBlockControls( BLOCK_CONTROL_CONFIG ),
] )( () => <></> );
