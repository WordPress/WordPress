/**
 * External dependencies
 */
import { createHigherOrderComponent } from '@wordpress/compose';
import {
	InspectorControls,
	store as blockEditorStore,
} from '@wordpress/block-editor';
import { addFilter, hasFilter } from '@wordpress/hooks';
import type { StoreDescriptor } from '@wordpress/data';
import { CartCheckoutSidebarCompatibilityNotice } from '@woocommerce/editor-components/sidebar-compatibility-notice';
import { NoPaymentMethodsNotice } from '@woocommerce/editor-components/no-payment-methods-notice';
import { PAYMENT_STORE_KEY } from '@woocommerce/block-data';
import { DefaultNotice } from '@woocommerce/editor-components/default-notice';
import { IncompatiblePaymentGatewaysNotice } from '@woocommerce/editor-components/incompatible-payment-gateways-notice';
import { useSelect } from '@wordpress/data';
import { CartCheckoutFeedbackPrompt } from '@woocommerce/editor-components/feedback-prompt';
import { useState } from '@wordpress/element';
declare module '@wordpress/editor' {
	let store: StoreDescriptor;
}

declare module '@wordpress/core-data' {
	let store: StoreDescriptor;
}

declare module '@wordpress/block-editor' {
	let store: StoreDescriptor;
}

const withSidebarNotices = createHigherOrderComponent(
	( BlockEdit ) => ( props ) => {
		const {
			clientId,
			name: blockName,
			isSelected: isBlockSelected,
		} = props;

		const [
			isIncompatiblePaymentGatewaysNoticeDismissed,
			setIsIncompatiblePaymentGatewaysNoticeDismissed,
		] = useState( true );

		const toggleIncompatiblePaymentGatewaysNoticeDismissedStatus = (
			isDismissed: boolean
		) => {
			setIsIncompatiblePaymentGatewaysNoticeDismissed( isDismissed );
		};

		const { isCart, isCheckout, isPaymentMethodsBlock, hasPaymentMethods } =
			useSelect( ( select ) => {
				const { getBlockParentsByBlockName, getBlockName } =
					select( blockEditorStore );
				const parent = getBlockParentsByBlockName( clientId, [
					'woocommerce/cart',
					'woocommerce/checkout',
				] ).map( getBlockName );
				const currentBlockName = getBlockName( clientId );
				return {
					isCart:
						parent.includes( 'woocommerce/cart' ) ||
						currentBlockName === 'woocommerce/cart',
					isCheckout:
						parent.includes( 'woocommerce/checkout' ) ||
						currentBlockName === 'woocommerce/checkout',
					isPaymentMethodsBlock:
						currentBlockName ===
						'woocommerce/checkout-payment-block',
					hasPaymentMethods:
						select(
							PAYMENT_STORE_KEY
						).paymentMethodsInitialized() &&
						Object.keys(
							select(
								PAYMENT_STORE_KEY
							).getAvailablePaymentMethods()
						).length > 0,
				};
			} );

		// Show sidebar notices only when a WooCommerce block is selected.
		if (
			! blockName.startsWith( 'woocommerce/' ) ||
			! isBlockSelected ||
			! ( isCart || isCheckout )
		) {
			return <BlockEdit key="edit" { ...props } />;
		}

		return (
			<>
				<InspectorControls>
					<IncompatiblePaymentGatewaysNotice
						toggleDismissedStatus={
							toggleIncompatiblePaymentGatewaysNoticeDismissedStatus
						}
						block={
							isCheckout
								? 'woocommerce/checkout'
								: 'woocommerce/cart'
						}
					/>

					{ isIncompatiblePaymentGatewaysNoticeDismissed ? (
						<>
							<DefaultNotice
								block={ isCheckout ? 'checkout' : 'cart' }
							/>
							<CartCheckoutSidebarCompatibilityNotice
								block={ isCheckout ? 'checkout' : 'cart' }
							/>
						</>
					) : null }

					{ isPaymentMethodsBlock && ! hasPaymentMethods && (
						<NoPaymentMethodsNotice />
					) }

					<CartCheckoutFeedbackPrompt />
				</InspectorControls>
				<BlockEdit key="edit" { ...props } />
			</>
		);
	},
	'withSidebarNotices'
);

if (
	! hasFilter(
		'editor.BlockEdit',
		'woocommerce/add/sidebar-compatibility-notice'
	)
) {
	addFilter(
		'editor.BlockEdit',
		'woocommerce/add/sidebar-compatibility-notice',
		withSidebarNotices,
		11
	);
}
