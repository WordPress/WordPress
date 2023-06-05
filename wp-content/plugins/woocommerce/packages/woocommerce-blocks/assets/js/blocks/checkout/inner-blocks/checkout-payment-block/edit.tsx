/**
 * External dependencies
 */
import classnames from 'classnames';
import { __ } from '@wordpress/i18n';
import { InspectorControls, useBlockProps } from '@wordpress/block-editor';
import { PanelBody, ExternalLink } from '@wordpress/components';
import { ADMIN_URL, getSetting } from '@woocommerce/settings';
import ExternalLinkCard from '@woocommerce/editor-components/external-link-card';
import { innerBlockAreas } from '@woocommerce/blocks-checkout';
import Noninteractive from '@woocommerce/base-components/noninteractive';
import { GlobalPaymentMethod } from '@woocommerce/types';
import { useSelect } from '@wordpress/data';
import { PAYMENT_STORE_KEY } from '@woocommerce/block-data';

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
	const globalPaymentMethods = getSetting< GlobalPaymentMethod[] >(
		'globalPaymentMethods'
	);

	const { incompatiblePaymentMethods } = useSelect( ( select ) => {
		const { getIncompatiblePaymentMethods } = select( PAYMENT_STORE_KEY );
		return {
			incompatiblePaymentMethods: getIncompatiblePaymentMethods(),
		};
	}, [] );
	const incompatiblePaymentMethodMessage = __(
		'Incompatible with block-based checkout',
		'woo-gutenberg-products-block'
	);

	return (
		<FormStepBlock
			attributes={ attributes }
			setAttributes={ setAttributes }
			className={ classnames(
				'wc-block-checkout__payment-method',
				attributes?.className
			) }
		>
			<InspectorControls>
				{ globalPaymentMethods.length > 0 && (
					<PanelBody
						title={ __(
							'Methods',
							'woo-gutenberg-products-block'
						) }
					>
						<p className="wc-block-checkout__controls-text">
							{ __(
								'You currently have the following payment integrations active.',
								'woo-gutenberg-products-block'
							) }
						</p>
						{ globalPaymentMethods.map( ( method ) => {
							const isIncompatible =
								!! incompatiblePaymentMethods[ method.id ];

							return (
								<ExternalLinkCard
									key={ method.id }
									href={ `${ ADMIN_URL }admin.php?page=wc-settings&tab=checkout&section=${ method.id }` }
									title={ method.title }
									description={ method.description }
									{ ...( isIncompatible
										? {
												warning:
													incompatiblePaymentMethodMessage,
										  }
										: {} ) }
								/>
							);
						} ) }
						<ExternalLink
							href={ `${ ADMIN_URL }admin.php?page=wc-settings&tab=checkout` }
						>
							{ __(
								'Manage payment methods',
								'woo-gutenberg-products-block'
							) }
						</ExternalLink>
					</PanelBody>
				) }
			</InspectorControls>
			<Noninteractive>
				<Block />
			</Noninteractive>
			<AdditionalFields block={ innerBlockAreas.PAYMENT_METHODS } />
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
