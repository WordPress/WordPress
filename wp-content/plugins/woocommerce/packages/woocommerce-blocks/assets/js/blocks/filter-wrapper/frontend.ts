/**
 * External dependencies
 */
import { renderParentBlock } from '@woocommerce/atomic-utils';
import { getRegisteredBlockComponents } from '@woocommerce/blocks-registry';

/**
 * Internal dependencies
 */
import Block from './block';

renderParentBlock( {
	blockName: 'woocommerce/filter-wrapper',
	selector: '.wp-block-woocommerce-filter-wrapper',
	Block,
	blockMap: getRegisteredBlockComponents( 'woocommerce/filter-wrapper' ),
} );
