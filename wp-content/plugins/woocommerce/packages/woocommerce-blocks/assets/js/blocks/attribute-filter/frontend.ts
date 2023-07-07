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
		isEditor: false,
		attributes: parseAttributes( el.dataset ),
	};
};

renderFrontend( {
	selector: '.wp-block-woocommerce-attribute-filter',
	Block,
	getProps,
} );
