/**
 * External dependencies
 */
import { __, sprintf } from '@wordpress/i18n';
import { useDispatch, useSelect } from '@wordpress/data';
import { CART_STORE_KEY, VALIDATION_STORE_KEY } from '@woocommerce/block-data';
import { decodeEntities } from '@wordpress/html-entities';
import type { StoreCartCoupon } from '@woocommerce/types';
import { applyCheckoutFilter } from '@woocommerce/blocks-checkout';

/**
 * Internal dependencies
 */
import { useStoreCart } from './use-store-cart';

/**
 * This is a custom hook for loading the Store API /cart/coupons endpoint and an
 * action for adding a coupon _to_ the cart.
 * See also: https://github.com/woocommerce/woocommerce-gutenberg-products-block/tree/trunk/src/RestApi/StoreApi
 */
export const useStoreCartCoupons = ( context = '' ): StoreCartCoupon => {
	const { cartCoupons, cartIsLoading } = useStoreCart();
	const { createErrorNotice } = useDispatch( 'core/notices' );
	const { createNotice } = useDispatch( 'core/notices' );
	const { setValidationErrors } = useDispatch( VALIDATION_STORE_KEY );

	const {
		isApplyingCoupon,
		isRemovingCoupon,
	}: Pick< StoreCartCoupon, 'isApplyingCoupon' | 'isRemovingCoupon' > =
		useSelect(
			( select ) => {
				const store = select( CART_STORE_KEY );

				return {
					isApplyingCoupon: store.isApplyingCoupon(),
					isRemovingCoupon: store.isRemovingCoupon(),
				};
			},
			[ createErrorNotice, createNotice ]
		);

	const { applyCoupon, removeCoupon } = useDispatch( CART_STORE_KEY );

	const applyCouponWithNotices = ( couponCode: string ) => {
		return applyCoupon( couponCode )
			.then( () => {
				if (
					applyCheckoutFilter( {
						filterName: 'showApplyCouponNotice',
						defaultValue: true,
						arg: { couponCode, context },
					} )
				) {
					createNotice(
						'info',
						sprintf(
							/* translators: %s coupon code. */
							__(
								'Coupon code "%s" has been applied to your cart.',
								'woo-gutenberg-products-block'
							),
							couponCode
						),
						{
							id: 'coupon-form',
							type: 'snackbar',
							context,
						}
					);
				}
				return Promise.resolve( true );
			} )
			.catch( ( error ) => {
				setValidationErrors( {
					coupon: {
						message: decodeEntities( error.message ),
						hidden: false,
					},
				} );
				return Promise.resolve( false );
			} );
	};

	const removeCouponWithNotices = ( couponCode: string ) => {
		return removeCoupon( couponCode )
			.then( () => {
				if (
					applyCheckoutFilter( {
						filterName: 'showRemoveCouponNotice',
						defaultValue: true,
						arg: { couponCode, context },
					} )
				) {
					createNotice(
						'info',
						sprintf(
							/* translators: %s coupon code. */
							__(
								'Coupon code "%s" has been removed from your cart.',
								'woo-gutenberg-products-block'
							),
							couponCode
						),
						{
							id: 'coupon-form',
							type: 'snackbar',
							context,
						}
					);
				}
				return Promise.resolve( true );
			} )
			.catch( ( error ) => {
				createErrorNotice( error.message, {
					id: 'coupon-form',
					context,
				} );
				return Promise.resolve( false );
			} );
	};

	return {
		appliedCoupons: cartCoupons,
		isLoading: cartIsLoading,
		applyCoupon: applyCouponWithNotices,
		removeCoupon: removeCouponWithNotices,
		isApplyingCoupon,
		isRemovingCoupon,
	};
};
