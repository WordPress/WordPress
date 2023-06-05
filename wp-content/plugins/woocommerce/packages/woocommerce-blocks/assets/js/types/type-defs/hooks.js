/**
 * @typedef {import('./cart').CartData} CartData
 * @typedef {import('./shipping').ShippingAddress} CartShippingAddress
 * @typedef {import('./contexts').StoreNoticeObject} StoreNoticeObject
 * @typedef {import('@woocommerce/type-defs/billing').BillingData} CartBillingAddress
 */

/**
 * @typedef {Object} StoreCart
 *
 * @property {Array}                cartCoupons               An array of coupons applied
 *                                                            to the cart.
 * @property {Array}                cartItems                 An array of items in the
 *                                                            cart.
 * @property {Array}                cartFees                  An array of fees in the
 *                                                            cart.
 * @property {number}               cartItemsCount            The number of items in the
 *                                                            cart.
 * @property {number}               cartItemsWeight           The weight of all items in
 *                                                            the cart.
 * @property {boolean}              cartNeedsPayment          True when the cart will
 *                                                            require payment.
 * @property {boolean}              cartNeedsShipping         True when the cart will
 *                                                            require shipping.
 * @property {Array}                cartItemErrors            Item validation errors.
 * @property {Object}               cartTotals                Cart and line total
 *                                                            amounts.
 * @property {boolean}              cartIsLoading             True when cart data is
 *                                                            being loaded.
 * @property {Array}                cartErrors                An array of errors thrown
 *                                                            by the cart.
 * @property {CartBillingAddress}   billingAddress            Billing address for the
 *                                                            cart.
 * @property {CartShippingAddress}  shippingAddress           Shipping address for the
 *                                                            cart.
 * @property {Array}                shippingRates             array of selected shipping
 *                                                            rates.
 * @property {Object}               extensions                Values provided by  *                                                      extensions.
 * @property {boolean}              shippingRatesLoading      Whether or not the
 *                                                            shipping rates are
 *                                                            being loaded.
 * @property {boolean}              cartHasCalculatedShipping Whether or not the cart has calculated shipping yet.
 * @property {Array}                paymentRequirements       List of features required from payment gateways.
 * @property {function(Object):any} receiveCart               Dispatcher to receive
 *                                                            updated cart.
 */

/**
 * @typedef {Object} StoreCartCoupon
 *
 * @property {Array}    appliedCoupons   Collection of applied coupons from the
 *                                       API.
 * @property {boolean}  isLoading        True when coupon data is being loaded.
 * @property {Function} applyCoupon      Callback for applying a coupon by code.
 * @property {Function} removeCoupon     Callback for removing a coupon by code.
 * @property {boolean}  isApplyingCoupon True when a coupon is being applied.
 * @property {boolean}  isRemovingCoupon True when a coupon is being removed.
 */

/**
 * @typedef {Object} StoreCartItemAddToCart
 *
 * @property {number}   cartQuantity  The quantity of the item in the
 *                                    cart.
 * @property {boolean}  addingToCart  Whether the cart item is still
 *                                    being added or not.
 * @property {boolean}  cartIsLoading Whether the cart is being loaded.
 * @property {Function} addToCart     Callback for adding a cart item.
 */

/**
 * @typedef {Object} CheckoutNotices
 *
 * @property {StoreNoticeObject[]} checkoutNotices       Array of notices in the
 *                                                       checkout context.
 * @property {StoreNoticeObject[]} expressPaymentNotices Array of notices in the
 *                                                       express payment context.
 * @property {StoreNoticeObject[]} paymentNotices        Array of notices in the
 *                                                       payment context.
 */

/**
 * @typedef {Object} EmitResponseTypes
 *
 * @property {string} SUCCESS To indicate a success response.
 * @property {string} FAIL    To indicate a failed response.
 * @property {string} ERROR   To indicate an error response.
 */

/**
 * @typedef {Object} NoticeContexts
 *
 * @property {string} PAYMENTS         Notices for the payments step.
 * @property {string} EXPRESS_PAYMENTS Notices for the express payments step.
 */

/* eslint-disable jsdoc/valid-types */
// Enum format below triggers the above rule even though VSCode interprets it fine.
/**
 * @typedef {NoticeContexts['PAYMENTS']|NoticeContexts['EXPRESS_PAYMENTS']} NoticeContextsEnum
 */

/**
 * @typedef {Object} EmitSuccessResponse
 *
 * @property {EmitResponseTypes['SUCCESS']} type          Should have the value of
 *                                                        EmitResponseTypes.SUCCESS.
 * @property {string}                       [redirectUrl] If the redirect url should be changed set
 *                                                        this. Note, this is ignored for some
 *                                                        emitters.
 * @property {Object}                       [meta]        Additional data returned for the success
 *                                                        response. This varies between context
 *                                                        emitters.
 */

/**
 * @typedef {Object} EmitFailResponse
 *
 * @property {EmitResponseTypes['FAIL']} type             Should have the value of
 *                                                        EmitResponseTypes.FAIL
 * @property {string}                    message          A message to trigger a notice for.
 * @property {NoticeContextsEnum}        [messageContext] What context to display any message in.
 * @property {Object}                    [meta]           Additional data returned for the fail
 *                                                        response. This varies between context
 *                                                        emitters.
 */

/**
 * @typedef {Object} EmitErrorResponse
 *
 * @property {EmitResponseTypes['ERROR']} type               Should have the value of
 *                                                           EmitResponseTypes.ERROR
 * @property {string}                     message            A message to trigger a notice for.
 * @property {boolean}                    retry              If false, then it means an
 *                                                           irrecoverable error so don't allow for
 *                                                           shopper to retry checkout (which may
 *                                                           mean either a different payment or
 *                                                           fixing validation errors).
 * @property {Object}                     [validationErrors] If provided, will be set as validation
 *                                                           errors in the validation context.
 * @property {NoticeContextsEnum}         [messageContext]   What context to display any message in.
 * @property {Object}                     [meta]             Additional data returned for the fail
 *                                                           response. This varies between context
 *                                                           emitters.
 */
/* eslint-enable jsdoc/valid-types */

/**
 * @typedef {Object} EmitResponseApi
 *
 * @property {EmitResponseTypes}        responseTypes     An object of various response types that can
 *                                                        be used in returned response objects.
 * @property {NoticeContexts}           noticeContexts    An object of various notice contexts that can
 *                                                        be used for targeting where a notice appears.
 * @property {function(Object):boolean} shouldRetry       Returns whether the user is allowed to retry
 *                                                        the payment after a failed one.
 * @property {function(Object):boolean} isSuccessResponse Returns whether the given response is of a
 *                                                        success response type.
 * @property {function(Object):boolean} isErrorResponse   Returns whether the given response is of an
 *                                                        error response type.
 * @property {function(Object):boolean} isFailResponse    Returns whether the given response is of a
 *                                                        fail response type.
 */

export {};
