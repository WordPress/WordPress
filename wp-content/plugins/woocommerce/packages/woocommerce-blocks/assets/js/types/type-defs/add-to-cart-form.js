/**
 * @typedef {Object} AddToCartFormDispatchActions
 *
 * @property {function():void}         resetForm          Dispatches an action that resets the form to a
 *                                                        pristine state.
 * @property {function():void}         submitForm         Dispatches an action that tells the form to submit.
 * @property {function(number):void}   setQuantity        Dispatches an action that sets the quantity to
 *                                                        the given value.
 * @property {function(Object):void}   setRequestParams   Dispatches an action that sets params for the
 *                                                        add to cart request (key value pairs).
 * @property {function(boolean=):void} setHasError        Dispatches an action that sets the status to
 *                                                        having an error.
 * @property {function(Object):void}   setAfterProcessing Dispatches an action that sets the status to
 *                                                        after processing and also sets the response
 *                                                        data accordingly.
 */

/**
 * @typedef {Object} AddToCartFormEventRegistration
 *
 * @property {function(function():boolean|Object,number=):function():void} onAddToCartAfterProcessingWithSuccess Used to register a callback that will fire after form has been processed and there are no errors.
 * @property {function(function():boolean|Object,number=):function():void} onAddToCartAfterProcessingWithError   Used to register a callback that will fire when the form has been processed and has an error.
 * @property {function(function():boolean|Object,number=):function():void} onAddToCartBeforeProcessing           Used to register a callback that will fire when the form has been submitted before being sent off to the server.
 */

/**
 * @typedef {Object} AddToCartFormStatusConstants
 *
 * @property {string} PRISTINE          Form is in it's initialized state.
 * @property {string} IDLE              When form state has changed but there is no
 *                                      activity happening.
 * @property {string} DISABLED          If the form cannot be submitted due to missing
 *                                      constraints, this status is assigned.
 * @property {string} BEFORE_PROCESSING This is the state before form processing
 *                                      begins after the add to cart button has been
 *                                      pressed. Validation occurs at point point.
 * @property {string} PROCESSING        After BEFORE_PROCESSING status emitters have
 *                                      finished successfully.
 * @property {string} AFTER_PROCESSING  After server side processing is completed
 *                                      this status is set.
 */

export {};
