/**
 * External dependencies
 */
import { renderFrontend } from '@woocommerce/base-utils';

/**
 * Internal dependencies
 */
import Block from './block';

/**
 * Wrapper component to supply the notice provider.
 *
 * @param {*} props
 */
const AllProductsFrontend = ( props ) => {
	return <Block { ...props } />;
};

const getProps = ( el ) => ( {
	attributes: JSON.parse( el.dataset.attributes ),
} );

renderFrontend( {
	selector: '.wp-block-woocommerce-all-products',
	Block: AllProductsFrontend,
	getProps,
} );
