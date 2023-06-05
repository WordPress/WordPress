/**
 * External dependencies
 */
import { useBlockProps } from '@wordpress/block-editor';

/**
 * Internal dependencies
 */
import Block from './block';
import withProductSelector from '../shared/with-product-selector';
import {
	BLOCK_TITLE as label,
	BLOCK_ICON as icon,
	BLOCK_DESCRIPTION as description,
} from './constants';
import './editor.scss';
import type { BlockAttributes } from './types';

interface Props {
	attributes: BlockAttributes;
}

const Edit = ( { attributes }: Props ): JSX.Element => {
	const blockProps = useBlockProps();
	return (
		<div { ...blockProps }>
			<Block { ...attributes } />
		</div>
	);
};

// @todo: Refactor this to remove the HOC 'withProductSelector()' component as users will not see this block in the inserter. Therefore, we can export the Edit component by default. The HOC 'withProductSelector()' component should also be removed from other `product-elements` components. See also https://github.com/woocommerce/woocommerce-blocks/pull/7566#pullrequestreview-1168635469.
export default withProductSelector( { icon, label, description } )( Edit );
