/**
 * External dependencies
 */
import { __ } from '@wordpress/i18n';
import classnames from 'classnames';
import { createInterpolateElement, useEffect } from '@wordpress/element';
import { useStoreCart } from '@woocommerce/base-context/hooks';
import { CheckoutProvider, noticeContexts } from '@woocommerce/base-context';
import BlockErrorBoundary from '@woocommerce/base-components/block-error-boundary';
import { SidebarLayout } from '@woocommerce/base-components/sidebar-layout';
import { CURRENT_USER_IS_ADMIN, getSetting } from '@woocommerce/settings';
import {
	SlotFillProvider,
	StoreNoticesContainer,
} from '@woocommerce/blocks-checkout';
import withScrollToTop from '@woocommerce/base-hocs/with-scroll-to-top';
import { useDispatch, useSelect } from '@wordpress/data';
import {
	CHECKOUT_STORE_KEY,
	VALIDATION_STORE_KEY,
} from '@woocommerce/block-data';

/**
 * Internal dependencies
 */
import './styles/style.scss';
import EmptyCart from './empty-cart';
import CheckoutOrderError from './checkout-order-error';
import { LOGIN_TO_CHECKOUT_URL, isLoginRequired, reloadPage } from './utils';
import type { Attributes } from './types';
import { CheckoutBlockContext } from './context';

const MustLoginPrompt = () => {
	return (
		<div className="wc-block-must-login-prompt">
			{ __(
				'You must be logged in to checkout.',
				'woo-gutenberg-products-block'
			) }{ ' ' }
			<a href={ LOGIN_TO_CHECKOUT_URL }>
				{ __(
					'Click here to log in.',
					'woo-gutenberg-products-block'
				) }
			</a>
		</div>
	);
};

const Checkout = ( {
	attributes,
	children,
}: {
	attributes: Attributes;
	children: React.ReactChildren;
} ): JSX.Element => {
	const { hasOrder, customerId } = useSelect( ( select ) => {
		const store = select( CHECKOUT_STORE_KEY );
		return {
			hasOrder: store.hasOrder(),
			customerId: store.getCustomerId(),
		};
	} );
	const { cartItems, cartIsLoading } = useStoreCart();

	const {
		showCompanyField,
		requireCompanyField,
		showApartmentField,
		showPhoneField,
		requirePhoneField,
	} = attributes;

	if ( ! cartIsLoading && cartItems.length === 0 ) {
		return <EmptyCart />;
	}

	if ( ! hasOrder ) {
		return <CheckoutOrderError />;
	}

	/**
	 * If checkout requires an account (guest checkout is turned off), render
	 * a notice and prevent access to the checkout, unless we explicitly allow
	 * account creation during the checkout flow.
	 */
	if (
		isLoginRequired( customerId ) &&
		! getSetting( 'checkoutAllowsSignup', false )
	) {
		return <MustLoginPrompt />;
	}

	return (
		<CheckoutBlockContext.Provider
			value={
				{
					showCompanyField,
					requireCompanyField,
					showApartmentField,
					showPhoneField,
					requirePhoneField,
				} as Attributes
			}
		>
			{ children }
		</CheckoutBlockContext.Provider>
	);
};

const ScrollOnError = ( {
	scrollToTop,
}: {
	scrollToTop: ( props: Record< string, unknown > ) => void;
} ): null => {
	const { hasError: checkoutHasError, isIdle: checkoutIsIdle } = useSelect(
		( select ) => {
			const store = select( CHECKOUT_STORE_KEY );
			return {
				isIdle: store.isIdle(),
				hasError: store.hasError(),
			};
		}
	);
	const { hasValidationErrors } = useSelect( ( select ) => {
		const store = select( VALIDATION_STORE_KEY );
		return {
			hasValidationErrors: store.hasValidationErrors(),
		};
	} );
	const { showAllValidationErrors } = useDispatch( VALIDATION_STORE_KEY );

	const hasErrorsToDisplay =
		checkoutIsIdle && checkoutHasError && hasValidationErrors;

	useEffect( () => {
		let scrollToTopTimeout: number;
		if ( hasErrorsToDisplay ) {
			showAllValidationErrors();
			// Scroll after a short timeout to allow a re-render. This will allow focusableSelector to match updated components.
			scrollToTopTimeout = window.setTimeout( () => {
				scrollToTop( {
					focusableSelector: 'input:invalid, .has-error input',
				} );
			}, 50 );
		}
		return () => {
			clearTimeout( scrollToTopTimeout );
		};
	}, [ hasErrorsToDisplay, scrollToTop, showAllValidationErrors ] );

	return null;
};

const Block = ( {
	attributes,
	children,
	scrollToTop,
}: {
	attributes: Attributes;
	children: React.ReactChildren;
	scrollToTop: ( props: Record< string, unknown > ) => void;
} ): JSX.Element => {
	return (
		<BlockErrorBoundary
			header={ __(
				'Something went wrong. Please contact us for assistance.',
				'woo-gutenberg-products-block'
			) }
			text={ createInterpolateElement(
				__(
					'The checkout has encountered an unexpected error. <button>Try reloading the page</button>. If the error persists, please get in touch with us so we can assist.',
					'woo-gutenberg-products-block'
				),
				{
					button: (
						<button
							className="wc-block-link-button"
							onClick={ reloadPage }
						/>
					),
				}
			) }
			showErrorMessage={ CURRENT_USER_IS_ADMIN }
		>
			<StoreNoticesContainer
				context={ [ noticeContexts.CHECKOUT, noticeContexts.CART ] }
			/>
			{ /* SlotFillProvider need to be defined before CheckoutProvider so fills have the SlotFill context ready when they mount. */ }
			<SlotFillProvider>
				<CheckoutProvider>
					<SidebarLayout
						className={ classnames( 'wc-block-checkout', {
							'has-dark-controls': attributes.hasDarkControls,
						} ) }
					>
						<Checkout attributes={ attributes }>
							{ children }
						</Checkout>
						<ScrollOnError scrollToTop={ scrollToTop } />
					</SidebarLayout>
				</CheckoutProvider>
			</SlotFillProvider>
		</BlockErrorBoundary>
	);
};

export default withScrollToTop( Block );
