/**
 * External dependencies
 */
import { useEffect } from '@wordpress/element';

/**
 * Internal dependencies
 */
import NoticeBanner, { NoticeBannerProps } from '../notice-banner';
import { SNACKBAR_TIMEOUT } from './constants';

const Snackbar = ( {
	onRemove = () => void 0,
	children,
	listRef,
	...notice
}: {
	// A ref to the list that contains the snackbar.
	listRef?: React.MutableRefObject< HTMLDivElement | null >;
} & NoticeBannerProps ) => {
	// Only set up the timeout dismiss if we're not explicitly dismissing.
	useEffect( () => {
		const timeoutHandle = setTimeout( () => {
			onRemove();
		}, SNACKBAR_TIMEOUT );

		return () => clearTimeout( timeoutHandle );
	}, [ onRemove ] );

	return (
		<NoticeBanner
			{ ...notice }
			onRemove={ () => {
				// Prevent focus loss by moving it to the list element.
				if ( listRef && listRef.current ) {
					listRef.current.focus();
				}
				onRemove();
			} }
		>
			{ children }
		</NoticeBanner>
	);
};

export default Snackbar;
