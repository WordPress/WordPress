/**
 * External dependencies
 */
import { __ } from '@wordpress/i18n';
import { store as editorStore } from '@wordpress/editor';
import triggerFetch from '@wordpress/api-fetch';
import { store as coreStore } from '@wordpress/core-data';
import { Notice, Button } from '@wordpress/components';
import { useSelect, useDispatch } from '@wordpress/data';
import { CHECKOUT_PAGE_ID, CART_PAGE_ID } from '@woocommerce/block-settings';
import { useCallback, useState } from '@wordpress/element';
/**
 * Internal dependencies
 */
import './editor.scss';

export function DefaultNotice( { block }: { block: string } ) {
	// To avoid having the same logic twice, we're going to handle both pages here.
	const ORIGINAL_PAGE_ID =
		block === 'checkout' ? CHECKOUT_PAGE_ID : CART_PAGE_ID;
	const settingName =
		block === 'checkout'
			? 'woocommerce_checkout_page_id'
			: 'woocommerce_cart_page_id';

	const noticeContent =
		block === 'checkout'
			? __(
					'If you would like to use this block as your default checkout, update your page settings',
					'woo-gutenberg-products-block'
			  )
			: __(
					'If you would like to use this block as your default cart, update your page settings',
					'woo-gutenberg-products-block'
			  );

	// Everything below works the same for Cart/Checkout
	const { saveEntityRecord } = useDispatch( coreStore );
	const { editPost, savePost } = useDispatch( editorStore );
	const { slug, isLoadingPage, postPublished, currentPostId } = useSelect(
		( select ) => {
			const { getEntityRecord, isResolving } = select( coreStore );
			const { isCurrentPostPublished, getCurrentPostId } =
				select( editorStore );
			return {
				slug:
					getEntityRecord( 'postType', 'page', ORIGINAL_PAGE_ID )
						?.slug || block,
				isLoadingPage: isResolving( 'getEntityRecord', [
					'postType',
					'page',
					ORIGINAL_PAGE_ID,
				] ),
				postPublished: isCurrentPostPublished(),
				currentPostId: getCurrentPostId(),
			};
		},
		[]
	);
	const [ settingStatus, setStatus ] = useState( 'pristine' );
	const updatePage = useCallback( () => {
		setStatus( 'updating' );
		Promise.resolve()
			.then( () =>
				triggerFetch( {
					path: `/wc/v3/settings/advanced/${ settingName }`,
					method: 'GET',
				} )
			)
			.catch( ( error ) => {
				if ( error.code === 'rest_setting_setting_invalid' ) {
					setStatus( 'error' );
				}
			} )
			.then( () => {
				if ( ! postPublished ) {
					editPost( { status: 'publish' } );
					return savePost();
				}
			} )
			.then( () =>
				// Make this page ID the default cart/checkout.
				triggerFetch( {
					path: `/wc/v3/settings/advanced/${ settingName }`,
					method: 'POST',
					data: {
						value: currentPostId.toString(),
					},
				} )
			)
			// Append `-2` to the original link so we can use it here.
			.then( () => {
				if ( ORIGINAL_PAGE_ID !== 0 ) {
					return saveEntityRecord( 'postType', 'page', {
						id: ORIGINAL_PAGE_ID,
						slug: `${ slug }-2`,
					} );
				}
			} )
			// Use the original link for this page.
			.then( () => editPost( { slug } ) )
			// Save page.
			.then( () => savePost() )
			.then( () => setStatus( 'updated' ) );
	}, [
		postPublished,
		editPost,
		savePost,
		settingName,
		currentPostId,
		ORIGINAL_PAGE_ID,
		saveEntityRecord,
		slug,
	] );
	if ( currentPostId === ORIGINAL_PAGE_ID || settingStatus === 'dismissed' ) {
		return null;
	}
	return (
		<Notice
			className="wc-default-page-notice"
			status={ settingStatus === 'updated' ? 'success' : 'warning' }
			onRemove={ () => setStatus( 'dismissed' ) }
			spokenMessage={
				settingStatus === 'updated'
					? __(
							'Page settings updated',
							'woo-gutenberg-products-block'
					  )
					: noticeContent
			}
		>
			{ settingStatus === 'updated' ? (
				__( 'Page settings updated', 'woo-gutenberg-products-block' )
			) : (
				<>
					<p>{ noticeContent }</p>
					<Button
						onClick={ updatePage }
						variant="secondary"
						isBusy={ settingStatus === 'updating' }
						disabled={ isLoadingPage }
						isSmall={ true }
					>
						{ __(
							'Update your page settings',
							'woo-gutenberg-products-block'
						) }
					</Button>
				</>
			) }
		</Notice>
	);
}
