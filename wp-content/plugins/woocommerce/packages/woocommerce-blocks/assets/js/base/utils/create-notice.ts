/**
 * External dependencies
 */
import { __ } from '@wordpress/i18n';
import type { Options as NoticeOptions } from '@wordpress/notices';
import { select, dispatch } from '@wordpress/data';

/**
 * Internal dependencies
 */
import { noticeContexts } from '../context/event-emit/utils';

export const DEFAULT_ERROR_MESSAGE = __(
	'Something went wrong. Please contact us to get assistance.',
	'woo-gutenberg-products-block'
);

/**
 * Returns a list of all notice contexts defined by Blocks.
 *
 * Contexts are defined in enum format, but this returns an array of strings instead.
 */
export const getNoticeContexts = () => {
	return Object.values( noticeContexts );
};

/**
 * Wrapper for @wordpress/notices createNotice.
 */
export const createNotice = (
	status: 'error' | 'warning' | 'info' | 'success',
	message: string,
	options: Partial< NoticeOptions >
) => {
	const noticeContext = options?.context;
	const suppressNotices =
		select( 'wc/store/payment' ).isExpressPaymentMethodActive();

	if ( suppressNotices || noticeContext === undefined ) {
		return;
	}

	dispatch( 'core/notices' ).createNotice( status, message, {
		isDismissible: true,
		...options,
		context: noticeContext,
	} );
};

/**
 * Remove notices from all contexts.
 *
 * @todo Remove this when supported in Gutenberg.
 * @see https://github.com/WordPress/gutenberg/pull/44059
 */
export const removeAllNotices = () => {
	const containers = select(
		'wc/store/store-notices'
	).getRegisteredContainers();
	const { removeNotice } = dispatch( 'core/notices' );
	const { getNotices } = select( 'core/notices' );

	containers.forEach( ( container ) => {
		getNotices( container ).forEach( ( notice ) => {
			removeNotice( notice.id, container );
		} );
	} );
};

export const removeNoticesWithContext = ( context: string ) => {
	const { removeNotice } = dispatch( 'core/notices' );
	const { getNotices } = select( 'core/notices' );

	getNotices( context ).forEach( ( notice ) => {
		removeNotice( notice.id, context );
	} );
};
