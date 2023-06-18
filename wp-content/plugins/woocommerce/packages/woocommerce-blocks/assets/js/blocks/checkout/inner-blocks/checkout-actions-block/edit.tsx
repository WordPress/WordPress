/**
 * External dependencies
 */
import { useRef } from '@wordpress/element';
import { useSelect } from '@wordpress/data';
import { __ } from '@wordpress/i18n';
import { InspectorControls, useBlockProps } from '@wordpress/block-editor';
import PageSelector from '@woocommerce/editor-components/page-selector';
import { PanelBody, ToggleControl } from '@wordpress/components';
import { CHECKOUT_PAGE_ID } from '@woocommerce/block-settings';
import { getSetting } from '@woocommerce/settings';
import { ReturnToCartButton } from '@woocommerce/base-components/cart-checkout';
import EditableButton from '@woocommerce/editor-components/editable-button';
import Noninteractive from '@woocommerce/base-components/noninteractive';

/**
 * Internal dependencies
 */
import { defaultPlaceOrderButtonLabel } from './constants';

export const Edit = ( {
	attributes,
	setAttributes,
}: {
	attributes: {
		showReturnToCart: boolean;
		cartPageId: number;
		placeOrderButtonLabel: string;
	};
	setAttributes: ( attributes: Record< string, unknown > ) => void;
} ): JSX.Element => {
	const blockProps = useBlockProps();
	const {
		cartPageId = 0,
		showReturnToCart = true,
		placeOrderButtonLabel,
	} = attributes;
	const { current: savedCartPageId } = useRef( cartPageId );
	const currentPostId = useSelect(
		( select ) => {
			if ( ! savedCartPageId ) {
				const store = select( 'core/editor' );
				return store.getCurrentPostId();
			}
			return savedCartPageId;
		},
		[ savedCartPageId ]
	);

	return (
		<div { ...blockProps }>
			<InspectorControls>
				<PanelBody
					title={ __(
						'Account options',
						'woo-gutenberg-products-block'
					) }
				>
					<ToggleControl
						label={ __(
							'Show a "Return to Cart" link',
							'woo-gutenberg-products-block'
						) }
						checked={ showReturnToCart }
						onChange={ () =>
							setAttributes( {
								showReturnToCart: ! showReturnToCart,
							} )
						}
					/>
				</PanelBody>
				{ showReturnToCart &&
					! (
						currentPostId === CHECKOUT_PAGE_ID &&
						savedCartPageId === 0
					) && (
						<PageSelector
							pageId={ cartPageId }
							setPageId={ ( id: number ) =>
								setAttributes( { cartPageId: id } )
							}
							labels={ {
								title: __(
									'Return to Cart button',
									'woo-gutenberg-products-block'
								),
								default: __(
									'WooCommerce Cart Page',
									'woo-gutenberg-products-block'
								),
							} }
						/>
					) }
			</InspectorControls>
			<div className="wc-block-checkout__actions">
				<div className="wc-block-checkout__actions_row">
					<Noninteractive>
						{ showReturnToCart && (
							<ReturnToCartButton
								link={ getSetting(
									'page-' + cartPageId,
									false
								) }
							/>
						) }
					</Noninteractive>
					<EditableButton
						className="wc-block-cart__submit-button wc-block-components-checkout-place-order-button"
						value={ placeOrderButtonLabel }
						placeholder={ defaultPlaceOrderButtonLabel }
						onChange={ ( content ) => {
							setAttributes( {
								placeOrderButtonLabel: content,
							} );
						} }
					/>
				</div>
			</div>
		</div>
	);
};

export const Save = (): JSX.Element => {
	return <div { ...useBlockProps.save() } />;
};
