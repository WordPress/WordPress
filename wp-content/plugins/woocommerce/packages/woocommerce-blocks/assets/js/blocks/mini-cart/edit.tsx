/**
 * External dependencies
 */
import { InspectorControls, useBlockProps } from '@wordpress/block-editor';
import { formatPrice } from '@woocommerce/price-format';
import {
	PanelBody,
	ExternalLink,
	ToggleControl,
	BaseControl,
	__experimentalToggleGroupControlOption as ToggleGroupControlOption,
	__experimentalToggleGroupControl as ToggleGroupControl,
} from '@wordpress/components';
import { getSetting } from '@woocommerce/settings';
import { __ } from '@wordpress/i18n';
import Noninteractive from '@woocommerce/base-components/noninteractive';
import type { ReactElement } from 'react';
import { useSelect } from '@wordpress/data';

/**
 * Internal dependencies
 */
import QuantityBadge from './quantity-badge';

interface Attributes {
	addToCartBehaviour: string;
	hasHiddenPrice: boolean;
	cartAndCheckoutRenderStyle: boolean;
}

interface Props {
	attributes: Attributes;
	setAttributes: ( attributes: Record< string, unknown > ) => void;
}

const Edit = ( { attributes, setAttributes }: Props ): ReactElement => {
	const { addToCartBehaviour, hasHiddenPrice, cartAndCheckoutRenderStyle } =
		attributes;
	const blockProps = useBlockProps( {
		className: `wc-block-mini-cart`,
	} );

	const isSiteEditor = useSelect( 'core/edit-site' ) !== undefined;

	const templatePartEditUri = getSetting(
		'templatePartEditUri',
		''
	) as string;

	const productCount = 0;
	const productTotal = 0;

	return (
		<div { ...blockProps }>
			<InspectorControls>
				<PanelBody
					title={ __(
						'Mini Cart Settings',
						'woo-gutenberg-products-block'
					) }
				>
					<BaseControl
						id="wc-block-mini-cart__add-to-cart-behaviour-toggle"
						label={ __(
							'Add-to-Cart behaviour',
							'woo-gutenberg-products-block'
						) }
					>
						<ToggleControl
							label={ __(
								'Open cart in a drawer',
								'woo-gutenberg-products-block'
							) }
							onChange={ ( value ) => {
								setAttributes( {
									addToCartBehaviour: value
										? 'open_drawer'
										: 'none',
								} );
							} }
							help={ __(
								'Select what happens when a customer adds a product to the cart.',
								'woo-gutenberg-products-block'
							) }
							checked={ addToCartBehaviour === 'open_drawer' }
						/>
					</BaseControl>
					<ToggleControl
						label={ __(
							'Hide Cart Price',
							'woo-gutenberg-products-block'
						) }
						help={ __(
							'Toggles the visibility of the Mini Cart price.',
							'woo-gutenberg-products-block'
						) }
						checked={ hasHiddenPrice }
						onChange={ () =>
							setAttributes( {
								hasHiddenPrice: ! hasHiddenPrice,
							} )
						}
					/>
					{ isSiteEditor && (
						<ToggleGroupControl
							className="wc-block-mini-cart__render-in-cart-and-checkout-toggle"
							label={ __(
								'Mini Cart in cart and checkout pages',
								'woo-gutenberg-products-block'
							) }
							value={ cartAndCheckoutRenderStyle }
							onChange={ ( value ) => {
								setAttributes( {
									cartAndCheckoutRenderStyle: value,
								} );
							} }
							help={ __(
								'Select how the Mini Cart behaves in the Cart and Checkout pages. This might affect the header layout.',
								'woo-gutenberg-products-block'
							) }
						>
							<ToggleGroupControlOption
								value={ 'hidden' }
								label={ __(
									'Hide',
									'woo-gutenberg-products-block'
								) }
							/>
							<ToggleGroupControlOption
								value={ 'removed' }
								label={ __(
									'Remove',
									'woo-gutenberg-products-block'
								) }
							/>
						</ToggleGroupControl>
					) }
				</PanelBody>
				{ templatePartEditUri && (
					<PanelBody
						title={ __(
							'Template settings',
							'woo-gutenberg-products-block'
						) }
					>
						<p>
							{ __(
								'Edit the appearance of the Mini Cart.',
								'woo-gutenberg-products-block'
							) }
						</p>
						<ExternalLink href={ templatePartEditUri }>
							{ __(
								'Edit Mini Cart template part',
								'woo-gutenberg-products-block'
							) }
						</ExternalLink>
					</PanelBody>
				) }
			</InspectorControls>
			<Noninteractive>
				<button className="wc-block-mini-cart__button">
					{ ! hasHiddenPrice && (
						<span className="wc-block-mini-cart__amount">
							{ formatPrice( productTotal ) }
						</span>
					) }
					<QuantityBadge count={ productCount } />
				</button>
			</Noninteractive>
		</div>
	);
};

export default Edit;
