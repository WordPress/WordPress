/**
 * External dependencies
 */
import classnames from 'classnames';
import { __ } from '@wordpress/i18n';
import { InspectorControls, useBlockProps } from '@wordpress/block-editor';
import { PanelBody, ExternalLink } from '@wordpress/components';
import { ADMIN_URL } from '@woocommerce/settings';
import { innerBlockAreas } from '@woocommerce/blocks-checkout';
import Noninteractive from '@woocommerce/base-components/noninteractive';

/**
 * Internal dependencies
 */
import {
	FormStepBlock,
	AdditionalFields,
	AdditionalFieldsContent,
} from '../../form-step';
import Block from './block';

export const Edit = ( {
	attributes,
	setAttributes,
}: {
	attributes: {
		title: string;
		description: string;
		showStepNumber: boolean;
		className: string;
	};
	setAttributes: ( attributes: Record< string, unknown > ) => void;
} ): JSX.Element => {
	return (
		<FormStepBlock
			attributes={ attributes }
			setAttributes={ setAttributes }
			className={ classnames(
				'wc-block-checkout__contact-fields',
				attributes?.className
			) }
		>
			<InspectorControls>
				<PanelBody
					title={ __( 'Account', 'woo-gutenberg-products-block' ) }
				>
					<p className="wc-block-checkout__controls-text">
						{ __(
							'Account creation and guest checkout settings can be managed in the WooCommerce settings.',
							'woo-gutenberg-products-block'
						) }
					</p>
					<ExternalLink
						href={ `${ ADMIN_URL }admin.php?page=wc-settings&tab=account` }
					>
						{ __(
							'Manage account settings',
							'woo-gutenberg-products-block'
						) }
					</ExternalLink>
				</PanelBody>
			</InspectorControls>
			<Noninteractive>
				<Block />
			</Noninteractive>
			<AdditionalFields block={ innerBlockAreas.CONTACT_INFORMATION } />
		</FormStepBlock>
	);
};

export const Save = (): JSX.Element => {
	return (
		<div { ...useBlockProps.save() }>
			<AdditionalFieldsContent />
		</div>
	);
};
