/**
 * External dependencies
 */
import { __ } from '@wordpress/i18n';
import { createInterpolateElement, useState } from '@wordpress/element';
import { ADMIN_URL } from '@woocommerce/settings';
import { CHECKOUT_PAGE_ID } from '@woocommerce/block-settings';
import {
	CheckboxControl,
	SelectControl,
	TextControl,
	ExternalLink,
	Notice,
} from '@wordpress/components';
import styled from '@emotion/styled';

/**
 * Internal dependencies
 */
import { SettingsCard, SettingsSection } from '../shared-components';
import { useSettingsContext } from './settings-context';

const GeneralSettingsDescription = () => (
	<>
		<h2>{ __( 'General', 'woo-gutenberg-products-block' ) }</h2>
		<p>
			{ __(
				'Enable or disable local pickup on your store, and define costs. Local pickup is only available from the block checkout.',
				'woo-gutenberg-products-block'
			) }
		</p>
		<ExternalLink
			href={ `${ ADMIN_URL }post.php?post=${ CHECKOUT_PAGE_ID }&action=edit` }
		>
			{ __( 'View checkout page', 'woo-gutenberg-products-block' ) }
		</ExternalLink>
	</>
);

const StyledNotice = styled( Notice )`
	margin-left: 0;
	margin-right: 0;
`;

const GeneralSettings = () => {
	const { settings, setSettingField, readOnlySettings } =
		useSettingsContext();
	const [ showCosts, setShowCosts ] = useState( !! settings.cost );

	return (
		<SettingsSection Description={ GeneralSettingsDescription }>
			<SettingsCard>
				{ readOnlySettings.hasLegacyPickup && (
					<StyledNotice status="warning" isDismissible={ false }>
						{ createInterpolateElement(
							__(
								'Enabling this will produce duplicate options at checkout. Remove the local pickup shipping method from your <a>shipping zones</a>.',
								'woo-gutenberg-products-block'
							),
							{
								a: (
									// eslint-disable-next-line jsx-a11y/anchor-has-content
									<a
										href={ `${ ADMIN_URL }admin.php?page=wc-settings&tab=shipping` }
										target="_blank"
										rel="noopener noreferrer"
									/>
								),
							}
						) }
					</StyledNotice>
				) }
				<CheckboxControl
					checked={ settings.enabled }
					name="local_pickup_enabled"
					onChange={ setSettingField( 'enabled' ) }
					label={ __(
						'Enable local pickup',
						'woo-gutenberg-products-block'
					) }
					help={ __(
						'When enabled, local pickup will appear as an option on the block based checkout.',
						'woo-gutenberg-products-block'
					) }
				/>
				<TextControl
					label={ __( 'Title', 'woo-gutenberg-products-block' ) }
					name="local_pickup_title"
					help={ __(
						'This is the shipping method title shown to customers.',
						'woo-gutenberg-products-block'
					) }
					placeholder={ __(
						'Local Pickup',
						'woo-gutenberg-products-block'
					) }
					value={ settings.title }
					onChange={ setSettingField( 'title' ) }
					disabled={ false }
					autoComplete="off"
					required={ true }
					onInvalid={ (
						event: React.InvalidEvent< HTMLInputElement >
					) => {
						event.target.setCustomValidity(
							__(
								'Local pickup title is required',
								'woo-gutenberg-products-block'
							)
						);
					} }
					onInput={ (
						event: React.ChangeEvent< HTMLInputElement >
					) => {
						event.target.setCustomValidity( '' );
					} }
				/>
				<CheckboxControl
					checked={ showCosts }
					onChange={ () => {
						setShowCosts( ! showCosts );
						setSettingField( 'cost' )( '' );
					} }
					label={ __(
						'Add a price for customers who choose local pickup',
						'woo-gutenberg-products-block'
					) }
					help={ __(
						'By default, the local pickup shipping method is free.',
						'woo-gutenberg-products-block'
					) }
				/>
				{ showCosts ? (
					<>
						<TextControl
							label={ __(
								'Cost',
								'woo-gutenberg-products-block'
							) }
							name="local_pickup_cost"
							help={ __(
								'Optional cost to charge for local pickup.',
								'woo-gutenberg-products-block'
							) }
							placeholder={ __(
								'Free',
								'woo-gutenberg-products-block'
							) }
							type="number"
							pattern="[0-9]+\.?[0-9]*"
							min={ 0 }
							value={ settings.cost }
							onChange={ setSettingField( 'cost' ) }
							disabled={ false }
							autoComplete="off"
						/>
						<SelectControl
							label={ __(
								'Taxes',
								'woo-gutenberg-products-block'
							) }
							name="local_pickup_tax_status"
							help={ __(
								'If a cost is defined, this controls if taxes are applied to that cost.',
								'woo-gutenberg-products-block'
							) }
							options={ [
								{
									label: __(
										'Taxable',
										'woo-gutenberg-products-block'
									),
									value: 'taxable',
								},
								{
									label: __(
										'Not taxable',
										'woo-gutenberg-products-block'
									),
									value: 'none',
								},
							] }
							value={ settings.tax_status }
							onChange={ setSettingField( 'tax_status' ) }
							disabled={ false }
						/>
					</>
				) : null }
			</SettingsCard>
		</SettingsSection>
	);
};

export default GeneralSettings;
