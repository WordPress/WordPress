/**
 * External dependencies
 */
import { __ } from '@wordpress/i18n';
import {
	useBlockProps,
	RichText,
	InspectorControls,
} from '@wordpress/block-editor';
import { CheckboxControl } from '@woocommerce/blocks-checkout';
import {
	PanelBody,
	ToggleControl,
	Notice,
	ExternalLink,
} from '@wordpress/components';
import { PRIVACY_URL, TERMS_URL } from '@woocommerce/block-settings';
import { ADMIN_URL } from '@woocommerce/settings';

/**
 * Internal dependencies
 */
import './editor.scss';
import { termsConsentDefaultText, termsCheckboxDefaultText } from './constants';

export const Edit = ( {
	attributes: { checkbox, text },
	setAttributes,
}: {
	attributes: { text: string; checkbox: boolean };
	setAttributes: ( attributes: Record< string, unknown > ) => void;
} ): JSX.Element => {
	const blockProps = useBlockProps();

	const defaultText = checkbox
		? termsCheckboxDefaultText
		: termsConsentDefaultText;

	const currentText = text || defaultText;
	return (
		<div { ...blockProps }>
			<InspectorControls>
				{ /* Show this notice if a terms page or a privacy page is not setup. */ }
				{ ( ! TERMS_URL || ! PRIVACY_URL ) && (
					<Notice
						className="wc-block-checkout__terms_notice"
						status="warning"
						isDismissible={ false }
					>
						{ __(
							"Link to your store's Terms and Conditions and Privacy Policy pages by creating pages for them.",
							'woo-gutenberg-products-block'
						) }
						<br />
						{ ! TERMS_URL && (
							<>
								<br />
								<ExternalLink
									href={ `${ ADMIN_URL }admin.php?page=wc-settings&tab=advanced` }
								>
									{ __(
										'Setup a Terms and Conditions page',
										'woo-gutenberg-products-block'
									) }
								</ExternalLink>
							</>
						) }
						{ ! PRIVACY_URL && (
							<>
								<br />
								<ExternalLink
									href={ `${ ADMIN_URL }options-privacy.php` }
								>
									{ __(
										'Setup a Privacy Policy page',
										'woo-gutenberg-products-block'
									) }
								</ExternalLink>
							</>
						) }
					</Notice>
				) }
				{ /* Show this notice if we have both a terms and privacy pages, but they're not present in the text. */ }
				{ TERMS_URL &&
					PRIVACY_URL &&
					! (
						currentText.includes( TERMS_URL ) &&
						currentText.includes( PRIVACY_URL )
					) && (
						<Notice
							className="wc-block-checkout__terms_notice"
							status="warning"
							isDismissible={ false }
							actions={
								termsConsentDefaultText !== text
									? [
											{
												label: __(
													'Restore default text',
													'woo-gutenberg-products-block'
												),
												onClick: () =>
													setAttributes( {
														text: '',
													} ),
											},
									  ]
									: []
							}
						>
							<p>
								{ __(
									'Ensure you add links to your policy pages in this section.',
									'woo-gutenberg-products-block'
								) }
							</p>
						</Notice>
					) }
				<PanelBody
					title={ __(
						'Display options',
						'woo-gutenberg-products-block'
					) }
				>
					<ToggleControl
						label={ __(
							'Require checkbox',
							'woo-gutenberg-products-block'
						) }
						checked={ checkbox }
						onChange={ () =>
							setAttributes( {
								checkbox: ! checkbox,
							} )
						}
					/>
				</PanelBody>
			</InspectorControls>
			<div className="wc-block-checkout__terms">
				{ checkbox ? (
					<>
						<CheckboxControl
							id="terms-condition"
							checked={ false }
						/>
						<RichText
							value={ currentText }
							onChange={ ( value ) =>
								setAttributes( { text: value } )
							}
						/>
					</>
				) : (
					<RichText
						tagName="span"
						value={ currentText }
						onChange={ ( value ) =>
							setAttributes( { text: value } )
						}
					/>
				) }
			</div>
		</div>
	);
};

export const Save = (): JSX.Element => {
	return <div { ...useBlockProps.save() } />;
};
