/**
 * External dependencies
 */
import { useEffect, useState } from '@wordpress/element';
import { useLocalStorageState } from '@woocommerce/base-hooks';

const initialDismissedNotices: string[] = [];

export const useCompatibilityNotice = (
	blockName: string
): [ boolean, () => void ] => {
	const [ dismissedNotices, setDismissedNotices ] = useLocalStorageState(
		`wc-blocks_dismissed_sidebar_compatibility_notices`,
		initialDismissedNotices
	);
	const [ isVisible, setIsVisible ] = useState( false );

	const isDismissed = dismissedNotices.includes( blockName );
	const dismissNotice = () => {
		const dismissedNoticesSet = new Set( dismissedNotices );
		dismissedNoticesSet.add( blockName );
		setDismissedNotices( [ ...dismissedNoticesSet ] );
	};

	// This ensures the modal is not loaded on first render. This is required so
	// Gutenberg doesn't steal the focus from the Guide and focuses the block.
	useEffect( () => {
		setIsVisible( ! isDismissed );
	}, [ isDismissed ] );

	return [ isVisible, dismissNotice ];
};
