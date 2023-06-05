/**
 * External dependencies
 */
import { useEffect, useMemo, useState } from '@wordpress/element';
import { WP_REST_API_Category } from 'wp-types';
import { ProductResponseItem } from '@woocommerce/types';
import { useDispatch, useSelect } from '@wordpress/data';
import type { ComponentType } from 'react';

/**
 * Internal dependencies
 */
import { EditorBlock } from './types';

interface WithUpdateButtonRequiredAttributes {
	editMode: boolean;
}

interface WithUpdateButtonAttributes< T > {
	attributes: WithUpdateButtonRequiredAttributes &
		EditorBlock< T >[ 'attributes' ];
}

interface WithUpdateButtonCategoryProps< T >
	extends WithUpdateButtonAttributes< T > {
	category: WP_REST_API_Category;
	product: never;
}

interface WithUpdateButtonProductProps< T >
	extends WithUpdateButtonAttributes< T > {
	category: never;
	product: ProductResponseItem;
}

type WithUpdateButtonProps< T extends EditorBlock< T > > =
	| ( T & WithUpdateButtonCategoryProps< T > )
	| ( T & WithUpdateButtonProductProps< T > );

export const withUpdateButtonAttributes =
	< T extends EditorBlock< T > >( Component: ComponentType< T > ) =>
	( props: WithUpdateButtonProps< T > ) => {
		const [ doUrlUpdate, setDoUrlUpdate ] = useState( false );
		const { attributes, category, clientId, product } = props;
		const item = category || product;

		const { editMode } = attributes;
		const permalink =
			( item as WP_REST_API_Category )?.link ||
			( item as ProductResponseItem )?.permalink;

		const block = useSelect( ( select ) => {
			return select( 'core/block-editor' ).getBlock( clientId );
		} );
		const innerBlock = block?.innerBlocks[ 0 ]?.innerBlocks[ 0 ];
		const buttonBlockId = innerBlock?.clientId || '';
		const currentButtonAttributes = useMemo(
			() => innerBlock?.attributes || {},
			[ innerBlock ]
		);
		const { url } = currentButtonAttributes;

		const { updateBlockAttributes } = useDispatch( 'core/block-editor' );

		useEffect( () => {
			if (
				doUrlUpdate &&
				buttonBlockId &&
				! editMode &&
				permalink &&
				url &&
				permalink !== url
			) {
				updateBlockAttributes( buttonBlockId, {
					url: permalink,
				} );
				setDoUrlUpdate( false );
			}
		}, [
			buttonBlockId,
			doUrlUpdate,
			editMode,
			permalink,
			updateBlockAttributes,
			url,
		] );

		const triggerUrlUpdate = () => setDoUrlUpdate( true );

		return <Component { ...props } triggerUrlUpdate={ triggerUrlUpdate } />;
	};
