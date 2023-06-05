/**
 * External dependencies
 */
import { useSelect } from '@wordpress/data';
import { useEffect, useState } from '@wordpress/element';
import { useLocalStorageState } from '@woocommerce/base-hooks';

/**
 * Internal dependencies
 */
import { STORE_KEY as PAYMENT_STORE_KEY } from '../../data/payment/constants';

type StoredIncompatibleGateway = { [ k: string ]: string[] };
const initialDismissedNotices: React.SetStateAction<
	StoredIncompatibleGateway[]
> = [];

const areEqual = ( array1: string[], array2: string[] ) => {
	if ( array1.length !== array2.length ) {
		return false;
	}

	const uniqueCollectionValues = new Set( [ ...array1, ...array2 ] );

	return uniqueCollectionValues.size === array1.length;
};

export const useIncompatiblePaymentGatewaysNotice = (
	blockName: string
): [ boolean, () => void, { [ k: string ]: string }, number ] => {
	const [ dismissedNotices, setDismissedNotices ] = useLocalStorageState<
		StoredIncompatibleGateway[]
	>(
		`wc-blocks_dismissed_incompatible_payment_gateways_notices`,
		initialDismissedNotices
	);
	const [ isVisible, setIsVisible ] = useState( false );

	const { incompatiblePaymentMethods } = useSelect( ( select ) => {
		const { getIncompatiblePaymentMethods } = select( PAYMENT_STORE_KEY );
		return {
			incompatiblePaymentMethods: getIncompatiblePaymentMethods(),
		};
	}, [] );
	const incompatiblePaymentMethodsIDs = Object.keys(
		incompatiblePaymentMethods
	);
	const numberOfIncompatiblePaymentMethods =
		incompatiblePaymentMethodsIDs.length;

	const isDismissedNoticeUpToDate = dismissedNotices.some(
		( notice ) =>
			Object.keys( notice ).includes( blockName ) &&
			areEqual(
				notice[ blockName as keyof object ],
				incompatiblePaymentMethodsIDs
			)
	);

	const shouldBeDismissed =
		numberOfIncompatiblePaymentMethods === 0 || isDismissedNoticeUpToDate;
	const dismissNotice = () => {
		const dismissedNoticesSet = new Set( dismissedNotices );
		dismissedNoticesSet.add( {
			[ blockName ]: incompatiblePaymentMethodsIDs,
		} );
		setDismissedNotices( [ ...dismissedNoticesSet ] );
	};

	// This ensures the modal is not loaded on first render. This is required so
	// Gutenberg doesn't steal the focus from the Guide and focuses the block.
	useEffect( () => {
		setIsVisible( ! shouldBeDismissed );

		if ( ! shouldBeDismissed && ! isDismissedNoticeUpToDate ) {
			setDismissedNotices( ( previousDismissedNotices ) =>
				previousDismissedNotices.reduce(
					( acc: StoredIncompatibleGateway[], curr ) => {
						if ( Object.keys( curr ).includes( blockName ) ) {
							return acc;
						}
						acc.push( curr );

						return acc;
					},
					[]
				)
			);
		}
	}, [
		shouldBeDismissed,
		isDismissedNoticeUpToDate,
		setDismissedNotices,
		blockName,
	] );

	return [
		isVisible,
		dismissNotice,
		incompatiblePaymentMethods,
		numberOfIncompatiblePaymentMethods,
	];
};
