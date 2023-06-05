/**
 * External dependencies
 */
import classnames from 'classnames';
import { __ } from '@wordpress/i18n';
import { InspectorControls, useBlockProps } from '@wordpress/block-editor';
import { PanelBody, ExternalLink, ToggleControl } from '@wordpress/components';
import { ADMIN_URL, getSetting } from '@woocommerce/settings';
import ExternalLinkCard from '@woocommerce/editor-components/external-link-card';
import { innerBlockAreas } from '@woocommerce/blocks-checkout';
import { useCheckoutAddress } from '@woocommerce/base-context/hooks';
import Noninteractive from '@woocommerce/base-components/noninteractive';
import { Attributes } from '@woocommerce/blocks/checkout/types';
import { updateAttributeInSiblingBlock } from '@woocommerce/utils';

/**
 * Internal dependencies
 */
import {
	FormStepBlock,
	AdditionalFields,
	AdditionalFieldsContent,
} from '../../form-step';
import NoShippingPlaceholder from './no-shipping-placeholder';
import Block from './block';
import './editor.scss';

type shippingAdminLink = {
	id: number;
	title: string;
	description: string;
};

export const Edit = ( {
	attributes,
	setAttributes,
	clientId,
}: {
	clientId: string;
	attributes: {
		title: string;
		description: string;
		showStepNumber: boolean;
		className: string;
		shippingCostRequiresAddress: boolean;
	};
	setAttributes: ( attributes: Record< string, unknown > ) => void;
} ): JSX.Element | null => {
	const globalShippingMethods = getSetting(
		'globalShippingMethods'
	) as shippingAdminLink[];
	const activeShippingZones = getSetting(
		'activeShippingZones'
	) as shippingAdminLink[];

	const { showShippingMethods } = useCheckoutAddress();

	if ( ! showShippingMethods ) {
		return null;
	}

	const toggleAttribute = ( key: keyof Attributes ): void => {
		const newAttributes = {} as Partial< Attributes >;
		newAttributes[ key ] = ! ( attributes[ key ] as boolean );
		setAttributes( newAttributes );
	};

	return (
		<FormStepBlock
			attributes={ attributes }
			setAttributes={ setAttributes }
			className={ classnames(
				'wc-block-checkout__shipping-option',
				attributes?.className
			) }
		>
			<InspectorControls>
				<PanelBody
					title={ __(
						'Calculations',
						'woo-gutenberg-products-block'
					) }
				>
					<ToggleControl
						label={ __(
							'Hide shipping costs until an address is entered',
							'woo-gutenberg-products-block'
						) }
						checked={ attributes.shippingCostRequiresAddress }
						onChange={ ( selected ) => {
							updateAttributeInSiblingBlock(
								clientId,
								'shippingCostRequiresAddress',
								selected,
								'woocommerce/checkout-shipping-method-block'
							);
							toggleAttribute( 'shippingCostRequiresAddress' );
						} }
					/>
				</PanelBody>
				{ globalShippingMethods.length > 0 && (
					<PanelBody
						title={ __(
							'Methods',
							'woo-gutenberg-products-block'
						) }
					>
						<p className="wc-block-checkout__controls-text">
							{ __(
								'The following shipping integrations are active on your store.',
								'woo-gutenberg-products-block'
							) }
						</p>
						{ globalShippingMethods.map( ( method ) => {
							return (
								<ExternalLinkCard
									key={ method.id }
									href={ `${ ADMIN_URL }admin.php?page=wc-settings&tab=shipping&section=${ method.id }` }
									title={ method.title }
									description={ method.description }
								/>
							);
						} ) }
						<ExternalLink
							href={ `${ ADMIN_URL }admin.php?page=wc-settings&tab=shipping` }
						>
							{ __(
								'Manage shipping methods',
								'woo-gutenberg-products-block'
							) }
						</ExternalLink>
					</PanelBody>
				) }
				{ activeShippingZones.length && (
					<PanelBody
						title={ __( 'Zones', 'woo-gutenberg-products-block' ) }
					>
						<p className="wc-block-checkout__controls-text">
							{ __(
								'You currently have the following shipping zones active.',
								'woo-gutenberg-products-block'
							) }
						</p>
						{ activeShippingZones.map( ( zone ) => {
							return (
								<ExternalLinkCard
									key={ zone.id }
									href={ `${ ADMIN_URL }admin.php?page=wc-settings&tab=shipping&zone_id=${ zone.id }` }
									title={ zone.title }
									description={ zone.description }
								/>
							);
						} ) }
						<ExternalLink
							href={ `${ ADMIN_URL }admin.php?page=wc-settings&tab=shipping` }
						>
							{ __(
								'Manage shipping zones',
								'woo-gutenberg-products-block'
							) }
						</ExternalLink>
					</PanelBody>
				) }
			</InspectorControls>
			<Noninteractive>
				<Block
					noShippingPlaceholder={ <NoShippingPlaceholder /> }
					shippingCostRequiresAddress={
						attributes.shippingCostRequiresAddress
					}
				/>
			</Noninteractive>
			<AdditionalFields block={ innerBlockAreas.SHIPPING_METHODS } />
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
