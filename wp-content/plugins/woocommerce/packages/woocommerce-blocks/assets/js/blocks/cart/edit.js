/* tslint:disable */
/**
 * External dependencies
 */
import classnames from 'classnames';
import { __ } from '@wordpress/i18n';
import {
	useBlockProps,
	InnerBlocks,
	InspectorControls,
} from '@wordpress/block-editor';
import BlockErrorBoundary from '@woocommerce/base-components/block-error-boundary';
import { EditorProvider, CartProvider } from '@woocommerce/base-context';
import { previewCart } from '@woocommerce/resource-previews';
import { SlotFillProvider } from '@woocommerce/blocks-checkout';

/**
 * Internal dependencies
 */
import './inner-blocks';
import './editor.scss';
import {
	addClassToBody,
	useBlockPropsWithLocking,
	BlockSettings,
} from '../cart-checkout-shared';
import '../cart-checkout-shared/sidebar-notices';
import '../cart-checkout-shared/view-switcher';
import { CartBlockContext } from './context';

// This is adds a class to body to signal if the selected block is locked
addClassToBody();

// Array of allowed block names.
const ALLOWED_BLOCKS = [
	'woocommerce/filled-cart-block',
	'woocommerce/empty-cart-block',
];

export const Edit = ( { className, attributes, setAttributes } ) => {
	const { hasDarkControls, currentView, isPreview = false } = attributes;
	const defaultTemplate = [
		[ 'woocommerce/filled-cart-block', {}, [] ],
		[ 'woocommerce/empty-cart-block', {}, [] ],
	];
	const blockProps = useBlockPropsWithLocking( {
		className: classnames( className, 'wp-block-woocommerce-cart', {
			'is-editor-preview': isPreview,
		} ),
	} );

	return (
		<div { ...blockProps }>
			<InspectorControls>
				<BlockSettings
					attributes={ attributes }
					setAttributes={ setAttributes }
				/>
			</InspectorControls>
			<BlockErrorBoundary
				header={ __(
					'Cart Block Error',
					'woocommerce'
				) }
				text={ __(
					'There was an error whilst rendering the cart block. If this problem continues, try re-creating the block.',
					'woocommerce'
				) }
				showErrorMessage={ true }
				errorMessagePrefix={ __(
					'Error message:',
					'woocommerce'
				) }
			>
				<EditorProvider
					previewData={ { previewCart } }
					currentView={ currentView }
					isPreview={ isPreview }
				>
					<CartBlockContext.Provider
						value={ {
							hasDarkControls,
						} }
					>
						<SlotFillProvider>
							<CartProvider>
								<InnerBlocks
									allowedBlocks={ ALLOWED_BLOCKS }
									template={ defaultTemplate }
									templateLock="insert"
								/>
							</CartProvider>
						</SlotFillProvider>
					</CartBlockContext.Provider>
				</EditorProvider>
			</BlockErrorBoundary>
		</div>
	);
};

export const Save = () => {
	return (
		<div
			{ ...useBlockProps.save( {
				className: 'is-loading',
			} ) }
		>
			<InnerBlocks.Content />
		</div>
	);
};
