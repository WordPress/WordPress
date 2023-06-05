/**
 * External dependencies
 */
import TestRenderer, { act } from 'react-test-renderer';
import { createRegistry, RegistryProvider } from '@wordpress/data';

/**
 * Internal dependencies
 */
import { useCheckoutSubmit } from '../use-checkout-submit';
import {
	CHECKOUT_STORE_KEY,
	config as checkoutStoreConfig,
} from '../../../../data/checkout';
import {
	PAYMENT_STORE_KEY,
	config as paymentDataStoreConfig,
} from '../../../../data/payment';

jest.mock( '../../providers/cart-checkout/checkout-events', () => {
	const original = jest.requireActual(
		'../../providers/cart-checkout/checkout-events'
	);
	return {
		...original,
		useCheckoutEventsContext: () => {
			return { onSubmit: jest.fn() };
		},
	};
} );

describe( 'useCheckoutSubmit', () => {
	let registry, renderer;

	const getWrappedComponents = ( Component ) => (
		<RegistryProvider value={ registry }>
			<Component />
		</RegistryProvider>
	);

	const getTestComponent = () => () => {
		const data = useCheckoutSubmit();
		return <div { ...data } />;
	};

	beforeEach( () => {
		registry = createRegistry( {
			[ CHECKOUT_STORE_KEY ]: checkoutStoreConfig,
			[ PAYMENT_STORE_KEY ]: paymentDataStoreConfig,
		} );
		renderer = null;
	} );

	it( 'onSubmit calls the correct action in the checkout events context', () => {
		const TestComponent = getTestComponent();

		act( () => {
			renderer = TestRenderer.create(
				getWrappedComponents( TestComponent )
			);
		} );

		//eslint-disable-next-line testing-library/await-async-query
		const { onSubmit } = renderer.root.findByType( 'div' ).props;

		onSubmit();

		expect( onSubmit ).toHaveBeenCalledTimes( 1 );
	} );
} );
