/**
 * External dependencies
 */
import { __ } from '@wordpress/i18n';
import classnames from 'classnames';
import {
	InnerBlocks,
	useBlockProps,
	InspectorControls,
} from '@wordpress/block-editor';
import { SidebarLayout } from '@woocommerce/base-components/sidebar-layout';
import { CheckoutProvider, EditorProvider } from '@woocommerce/base-context';
import {
	previewCart,
	previewSavedPaymentMethods,
} from '@woocommerce/resource-previews';
import {
	PanelBody,
	ToggleControl,
	CheckboxControl,
} from '@wordpress/components';
import { SlotFillProvider } from '@woocommerce/blocks-checkout';
import type { TemplateArray } from '@wordpress/blocks';

/**
 * Internal dependencies
 */
import './inner-blocks';
import './styles/editor.scss';
import {
	addClassToBody,
	BlockSettings,
	useBlockPropsWithLocking,
} from '../cart-checkout-shared';
import '../cart-checkout-shared/sidebar-notices';
import { CheckoutBlockContext, CheckoutBlockControlsContext } from './context';
import type { Attributes } from './types';

// This is adds a class to body to signal if the selected block is locked
addClassToBody();

// Array of allowed block names.
const ALLOWED_BLOCKS: string[] = [
	'woocommerce/checkout-fields-block',
	'woocommerce/checkout-totals-block',
];

export const Edit = ( {
	attributes,
	setAttributes,
}: {
	attributes: Attributes;
	setAttributes: ( attributes: Record< string, unknown > ) => undefined;
} ): JSX.Element => {
	const {
		showCompanyField,
		requireCompanyField,
		showApartmentField,
		showPhoneField,
		requirePhoneField,
		showOrderNotes,
		showPolicyLinks,
		showReturnToCart,
		showRateAfterTaxName,
		cartPageId,
		isPreview = false,
	} = attributes;

	const defaultTemplate = [
		[ 'woocommerce/checkout-fields-block', {}, [] ],
		[ 'woocommerce/checkout-totals-block', {}, [] ],
	] as TemplateArray;

	const toggleAttribute = ( key: keyof Attributes ): void => {
		const newAttributes = {} as Partial< Attributes >;
		newAttributes[ key ] = ! ( attributes[ key ] as boolean );
		setAttributes( newAttributes );
	};

	const addressFieldControls = (): JSX.Element => (
		<InspectorControls>
			<PanelBody
				title={ __( 'Address Fields', 'woo-gutenberg-products-block' ) }
			>
				<p className="wc-block-checkout__controls-text">
					{ __(
						'Show or hide fields in the checkout address forms.',
						'woo-gutenberg-products-block'
					) }
				</p>
				<ToggleControl
					label={ __( 'Company', 'woo-gutenberg-products-block' ) }
					checked={ showCompanyField }
					onChange={ () => toggleAttribute( 'showCompanyField' ) }
				/>
				{ showCompanyField && (
					<CheckboxControl
						label={ __(
							'Require company name?',
							'woo-gutenberg-products-block'
						) }
						checked={ requireCompanyField }
						onChange={ () =>
							toggleAttribute( 'requireCompanyField' )
						}
						className="components-base-control--nested"
					/>
				) }
				<ToggleControl
					label={ __(
						'Apartment, suite, etc.',
						'woo-gutenberg-products-block'
					) }
					checked={ showApartmentField }
					onChange={ () => toggleAttribute( 'showApartmentField' ) }
				/>
				<ToggleControl
					label={ __( 'Phone', 'woo-gutenberg-products-block' ) }
					checked={ showPhoneField }
					onChange={ () => toggleAttribute( 'showPhoneField' ) }
				/>
				{ showPhoneField && (
					<CheckboxControl
						label={ __(
							'Require phone number?',
							'woo-gutenberg-products-block'
						) }
						checked={ requirePhoneField }
						onChange={ () =>
							toggleAttribute( 'requirePhoneField' )
						}
						className="components-base-control--nested"
					/>
				) }
			</PanelBody>
		</InspectorControls>
	);
	const blockProps = useBlockPropsWithLocking();
	return (
		<div { ...blockProps }>
			<InspectorControls>
				<BlockSettings
					attributes={ attributes }
					setAttributes={ setAttributes }
				/>
			</InspectorControls>
			<EditorProvider
				isPreview={ isPreview }
				previewData={ { previewCart, previewSavedPaymentMethods } }
			>
				<SlotFillProvider>
					<CheckoutProvider>
						<SidebarLayout
							className={ classnames( 'wc-block-checkout', {
								'has-dark-controls': attributes.hasDarkControls,
							} ) }
						>
							<CheckoutBlockControlsContext.Provider
								value={ { addressFieldControls } }
							>
								<CheckoutBlockContext.Provider
									value={ {
										showCompanyField,
										requireCompanyField,
										showApartmentField,
										showPhoneField,
										requirePhoneField,
										showOrderNotes,
										showPolicyLinks,
										showReturnToCart,
										cartPageId,
										showRateAfterTaxName,
									} }
								>
									<InnerBlocks
										allowedBlocks={ ALLOWED_BLOCKS }
										template={ defaultTemplate }
										templateLock="insert"
									/>
								</CheckoutBlockContext.Provider>
							</CheckoutBlockControlsContext.Provider>
						</SidebarLayout>
					</CheckoutProvider>
				</SlotFillProvider>
			</EditorProvider>
		</div>
	);
};

export const Save = (): JSX.Element => {
	return (
		<div
			{ ...useBlockProps.save( {
				className: 'wc-block-checkout is-loading',
			} ) }
		>
			<InnerBlocks.Content />
		</div>
	);
};
