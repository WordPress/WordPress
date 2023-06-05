/**
 * External dependencies
 */
import {
	usePaymentMethodInterface,
	useStoreEvents,
} from '@woocommerce/base-context/hooks';
import { cloneElement, useCallback } from '@wordpress/element';
import { useEditorContext } from '@woocommerce/base-context';
import classNames from 'classnames';
import RadioControlAccordion from '@woocommerce/base-components/radio-control-accordion';
import { useDispatch, useSelect } from '@wordpress/data';
import { getPaymentMethods } from '@woocommerce/blocks-registry';

/**
 * Internal dependencies
 */
import PaymentMethodCard from './payment-method-card';
import { noticeContexts } from '../../../base/context/event-emit';
import { STORE_KEY as PAYMENT_STORE_KEY } from '../../../data/payment/constants';

/**
 * Component used to render all non-saved payment method options.
 *
 * @return {*} The rendered component.
 */
const PaymentMethodOptions = () => {
	const {
		activeSavedToken,
		activePaymentMethod,
		isExpressPaymentMethodActive,
		savedPaymentMethods,
		availablePaymentMethods,
	} = useSelect( ( select ) => {
		const store = select( PAYMENT_STORE_KEY );
		return {
			activeSavedToken: store.getActiveSavedToken(),
			activePaymentMethod: store.getActivePaymentMethod(),
			isExpressPaymentMethodActive: store.isExpressPaymentMethodActive(),
			savedPaymentMethods: store.getSavedPaymentMethods(),
			availablePaymentMethods: store.getAvailablePaymentMethods(),
		};
	} );
	const { __internalSetActivePaymentMethod } =
		useDispatch( PAYMENT_STORE_KEY );
	const paymentMethods = getPaymentMethods();
	const { ...paymentMethodInterface } = usePaymentMethodInterface();
	const { removeNotice } = useDispatch( 'core/notices' );
	const { dispatchCheckoutEvent } = useStoreEvents();
	const { isEditor } = useEditorContext();

	const options = Object.keys( availablePaymentMethods ).map( ( name ) => {
		const { edit, content, label, supports } = paymentMethods[ name ];
		const component = isEditor ? edit : content;
		return {
			value: name,
			label:
				typeof label === 'string'
					? label
					: cloneElement( label, {
							components: paymentMethodInterface.components,
					  } ),
			name: `wc-saved-payment-method-token-${ name }`,
			content: (
				<PaymentMethodCard showSaveOption={ supports.showSaveOption }>
					{ cloneElement( component, {
						__internalSetActivePaymentMethod,
						...paymentMethodInterface,
					} ) }
				</PaymentMethodCard>
			),
		};
	} );

	const onChange = useCallback(
		( value ) => {
			__internalSetActivePaymentMethod( value );
			removeNotice( 'wc-payment-error', noticeContexts.PAYMENTS );
			dispatchCheckoutEvent( 'set-active-payment-method', {
				value,
			} );
		},
		[
			dispatchCheckoutEvent,
			removeNotice,
			__internalSetActivePaymentMethod,
		]
	);

	const isSinglePaymentMethod =
		Object.keys( savedPaymentMethods ).length === 0 &&
		Object.keys( paymentMethods ).length === 1;

	const singleOptionClass = classNames( {
		'disable-radio-control': isSinglePaymentMethod,
	} );
	return isExpressPaymentMethodActive ? null : (
		<RadioControlAccordion
			id={ 'wc-payment-method-options' }
			className={ singleOptionClass }
			selected={ activeSavedToken ? null : activePaymentMethod }
			onChange={ onChange }
			options={ options }
		/>
	);
};

export default PaymentMethodOptions;
