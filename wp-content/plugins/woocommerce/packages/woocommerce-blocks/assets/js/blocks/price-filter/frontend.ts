/**
 * External dependencies
 */
import { renderFrontend } from '@woocommerce/base-utils';

/**
 * Internal dependencies
 */
import Block from './block';
import { parseAttributes } from './utils';

const getProps = ( el: HTMLElement ) => {
	return {
		attributes: parseAttributes( el.dataset ),
		isEditor: false,
	};
};

renderFrontend( {
	selector: '.wp-block-woocommerce-price-filter',
	Block,
	getProps,
} );
