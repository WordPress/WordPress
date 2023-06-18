/**
 * External dependencies
 */
import classnames from 'classnames';
import { __ } from '@wordpress/i18n';
import { CartResponseItem } from '@woocommerce/types';
import { createRef, useEffect, useRef } from '@wordpress/element';
import type { RefObject } from 'react';

/**
 * Internal dependencies
 */
import CartLineItemRow from './cart-line-item-row';
import './style.scss';

const placeholderRows = [ ...Array( 3 ) ].map( ( _x, i ) => (
	<CartLineItemRow lineItem={ {} } key={ i } />
) );

interface CartLineItemsTableProps {
	lineItems: CartResponseItem[];
	isLoading: boolean;
	className?: string;
}

const setRefs = ( lineItems: CartResponseItem[] ) => {
	const refs = {} as Record< string, RefObject< HTMLTableRowElement > >;
	lineItems.forEach( ( { key } ) => {
		refs[ key ] = createRef();
	} );
	return refs;
};

const CartLineItemsTable = ( {
	lineItems = [],
	isLoading = false,
	className,
}: CartLineItemsTableProps ): JSX.Element => {
	const tableRef = useRef< HTMLTableElement | null >( null );
	const rowRefs = useRef( setRefs( lineItems ) );
	useEffect( () => {
		rowRefs.current = setRefs( lineItems );
	}, [ lineItems ] );

	const onRemoveRow = ( nextItemKey: string | null ) => () => {
		if (
			rowRefs?.current &&
			nextItemKey &&
			rowRefs.current[ nextItemKey ].current instanceof HTMLElement
		) {
			( rowRefs.current[ nextItemKey ].current as HTMLElement ).focus();
		} else if ( tableRef.current instanceof HTMLElement ) {
			tableRef.current.focus();
		}
	};

	const products = isLoading
		? placeholderRows
		: lineItems.map( ( lineItem, i ) => {
				const nextItemKey =
					lineItems.length > i + 1 ? lineItems[ i + 1 ].key : null;
				return (
					<CartLineItemRow
						key={ lineItem.key }
						lineItem={ lineItem }
						onRemove={ onRemoveRow( nextItemKey ) }
						ref={ rowRefs.current[ lineItem.key ] }
						tabIndex={ -1 }
					/>
				);
		  } );

	return (
		<table
			className={ classnames( 'wc-block-cart-items', className ) }
			ref={ tableRef }
			tabIndex={ -1 }
		>
			<thead>
				<tr className="wc-block-cart-items__header">
					<th className="wc-block-cart-items__header-image">
						<span>
							{ __( 'Product', 'woo-gutenberg-products-block' ) }
						</span>
					</th>
					<th className="wc-block-cart-items__header-product">
						<span>
							{ __( 'Details', 'woo-gutenberg-products-block' ) }
						</span>
					</th>
					<th className="wc-block-cart-items__header-total">
						<span>
							{ __( 'Total', 'woo-gutenberg-products-block' ) }
						</span>
					</th>
				</tr>
			</thead>
			<tbody>{ products }</tbody>
		</table>
	);
};

export default CartLineItemsTable;
