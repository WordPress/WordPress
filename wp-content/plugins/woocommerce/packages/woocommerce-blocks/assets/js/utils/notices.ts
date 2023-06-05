/**
 * External dependencies
 */
import { dispatch, select } from '@wordpress/data';
import type { Notice } from '@wordpress/notices';

export const hasNoticesOfType = (
	type: 'default' | 'snackbar',
	context?: string | undefined
): boolean => {
	const notices: Notice[] = select( 'core/notices' ).getNotices( context );
	return notices.some( ( notice: Notice ) => notice.type === type );
};

// Note, if context is blank, the default context is used.
export const removeNoticesByStatus = (
	status: string,
	context?: string | undefined
): void => {
	const notices = select( 'core/notices' ).getNotices( context );
	const { removeNotice } = dispatch( 'core/notices' );
	const noticesOfType = notices.filter(
		( notice ) => notice.status === status
	);
	noticesOfType.forEach( ( notice ) => removeNotice( notice.id, context ) );
};
