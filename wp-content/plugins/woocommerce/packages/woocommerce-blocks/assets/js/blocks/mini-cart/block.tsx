/**
 * External dependencies
 */
import { renderParentBlock } from '@woocommerce/atomic-utils';
import Drawer from '@woocommerce/base-components/drawer';
import { useStoreCart } from '@woocommerce/base-context/hooks';
import {
	getValidBlockAttributes,
	translateJQueryEventToNative,
} from '@woocommerce/base-utils';
import { getRegisteredBlockComponents } from '@woocommerce/blocks-registry';
import {
	formatPrice,
	getCurrencyFromPriceResponse,
} from '@woocommerce/price-format';
import { getSettingWithCoercion } from '@woocommerce/settings';
import {
	CartResponseTotals,
	isBoolean,
	isString,
	isCartResponseTotals,
	isNumber,
} from '@woocommerce/types';
import {
	unmountComponentAtNode,
	useCallback,
	useEffect,
	useRef,
	useState,
} from '@wordpress/element';
import { sprintf, _n } from '@wordpress/i18n';
import classnames from 'classnames';

/**
 * Internal dependencies
 */
import QuantityBadge from './quantity-badge';
import { MiniCartContentsBlock } from './mini-cart-contents/block';
import './style.scss';
import {
	blockName,
	attributes as miniCartContentsAttributes,
} from './mini-cart-contents/attributes';

interface Props {
	isInitiallyOpen?: boolean;
	colorClassNames?: string;
	style?: Record< string, Record< string, string > >;
	contents: string;
	addToCartBehaviour: string;
	hasHiddenPrice: boolean;
}

const MiniCartBlock = ( attributes: Props ): JSX.Element => {
	const {
		isInitiallyOpen = false,
		colorClassNames,
		style,
		contents = '',
		addToCartBehaviour = 'none',
		hasHiddenPrice = false,
	} = attributes;

	const {
		cartItemsCount: cartItemsCountFromApi,
		cartIsLoading,
		cartTotals: cartTotalsFromApi,
	} = useStoreCart();

	const isFirstLoadingCompleted = useRef( cartIsLoading );

	useEffect( () => {
		if ( isFirstLoadingCompleted.current && ! cartIsLoading ) {
			isFirstLoadingCompleted.current = false;
		}
	}, [ cartIsLoading, isFirstLoadingCompleted ] );

	const [ isOpen, setIsOpen ] = useState< boolean >( isInitiallyOpen );
	// We already rendered the HTML drawer placeholder, so we want to skip the
	// slide in animation.
	const [ skipSlideIn, setSkipSlideIn ] =
		useState< boolean >( isInitiallyOpen );
	const [ contentsNode, setContentsNode ] = useState< HTMLDivElement | null >(
		null
	);

	const contentsRef = useCallback( ( node ) => {
		setContentsNode( node );
	}, [] );

	useEffect( () => {
		const body = document.querySelector( 'body' );
		if ( body ) {
			if ( isOpen ) {
				Object.assign( body.style, { overflow: 'hidden' } );
			} else {
				Object.assign( body.style, { overflow: '' } );
			}
		}
	}, [ isOpen ] );

	useEffect( () => {
		if ( contentsNode instanceof Element ) {
			const container = contentsNode.querySelector(
				'.wp-block-woocommerce-mini-cart-contents'
			);
			if ( ! container ) {
				return;
			}
			if ( isOpen ) {
				renderParentBlock( {
					Block: MiniCartContentsBlock,
					blockName,
					getProps: ( el: Element ) => {
						return {
							attributes: getValidBlockAttributes(
								miniCartContentsAttributes,
								/* eslint-disable @typescript-eslint/no-explicit-any */
								( el instanceof HTMLElement
									? el.dataset
									: {} ) as any
							),
						};
					},
					selector: '.wp-block-woocommerce-mini-cart-contents',
					blockMap: getRegisteredBlockComponents( blockName ),
				} );
			}
		}

		return () => {
			if ( contentsNode instanceof Element && isOpen ) {
				const container = contentsNode.querySelector(
					'.wp-block-woocommerce-mini-cart-contents'
				);
				if ( container ) {
					unmountComponentAtNode( container );
				}
			}
		};
	}, [ isOpen, contentsNode ] );

	useEffect( () => {
		const openMiniCart = () => {
			if ( addToCartBehaviour === 'open_drawer' ) {
				setSkipSlideIn( false );
				setIsOpen( true );
			}
		};

		// Make it so we can read jQuery events triggered by WC Core elements.
		const removeJQueryAddedToCartEvent = translateJQueryEventToNative(
			'added_to_cart',
			'wc-blocks_added_to_cart'
		);

		document.body.addEventListener(
			'wc-blocks_added_to_cart',
			openMiniCart
		);

		return () => {
			removeJQueryAddedToCartEvent();

			document.body.removeEventListener(
				'wc-blocks_added_to_cart',
				openMiniCart
			);
		};
	}, [ addToCartBehaviour ] );

	const showIncludingTax = getSettingWithCoercion(
		'displayCartPricesIncludingTax',
		false,
		isBoolean
	);

	const preFetchedCartTotals =
		getSettingWithCoercion< CartResponseTotals | null >(
			'cartTotals',
			null,
			isCartResponseTotals
		);

	const preFetchedCartItemsCount = getSettingWithCoercion< number >(
		'cartItemsCount',
		0,
		isNumber
	);

	const taxLabel = getSettingWithCoercion( 'taxLabel', '', isString );

	const cartTotals =
		! isFirstLoadingCompleted.current || preFetchedCartTotals === null
			? cartTotalsFromApi
			: preFetchedCartTotals;

	const cartItemsCount = ! isFirstLoadingCompleted.current
		? cartItemsCountFromApi
		: preFetchedCartItemsCount;

	const subTotal = showIncludingTax
		? parseInt( cartTotals.total_items, 10 ) +
		  parseInt( cartTotals.total_items_tax, 10 )
		: parseInt( cartTotals.total_items, 10 );

	const ariaLabel = sprintf(
		/* translators: %1$d is the number of products in the cart. %2$s is the cart total */
		_n(
			'%1$d item in cart, total price of %2$s',
			'%1$d items in cart, total price of %2$s',
			cartItemsCount,
			'woo-gutenberg-products-block'
		),
		cartItemsCount,
		formatPrice( subTotal, getCurrencyFromPriceResponse( cartTotals ) )
	);

	const colorStyle = {
		backgroundColor: style?.color?.background,
		color: style?.color?.text,
	};

	return (
		<>
			<button
				className={ `wc-block-mini-cart__button ${ colorClassNames }` }
				style={ colorStyle }
				onClick={ () => {
					if ( ! isOpen ) {
						setIsOpen( true );
						setSkipSlideIn( false );
					}
				} }
				aria-label={ ariaLabel }
			>
				{ ! hasHiddenPrice && (
					<span className="wc-block-mini-cart__amount">
						{ formatPrice(
							subTotal,
							getCurrencyFromPriceResponse( cartTotals )
						) }
					</span>
				) }
				{ taxLabel !== '' && subTotal !== 0 && ! hasHiddenPrice && (
					<small className="wc-block-mini-cart__tax-label">
						{ taxLabel }
					</small>
				) }
				<QuantityBadge count={ cartItemsCount } />
			</button>
			<Drawer
				className={ classnames(
					'wc-block-mini-cart__drawer',
					'is-mobile',
					{
						'is-loading': cartIsLoading,
					}
				) }
				title=""
				isOpen={ isOpen }
				onClose={ () => {
					setIsOpen( false );
				} }
				slideIn={ ! skipSlideIn }
			>
				<div
					className="wc-block-mini-cart__template-part"
					ref={ contentsRef }
					dangerouslySetInnerHTML={ { __html: contents } }
				></div>
			</Drawer>
		</>
	);
};

export default MiniCartBlock;
