/**
 * External dependencies
 */
import { getBlockMap } from '@woocommerce/atomic-utils';
import { Suspense } from '@wordpress/element';
import { ProductResponseItem } from '@woocommerce/types';

/**
 * Internal dependencies
 */
import { LayoutConfig } from '../types';

/**
 * Maps a layout config into atomic components.
 *
 * @param {string}   blockName    Name of the parent block. Used to get extension children.
 * @param {Object}   product      Product object to pass to atomic components.
 * @param {Object[]} layoutConfig Object with component data.
 * @param {number}   componentId  Parent component ID needed for key generation.
 */
export const renderProductLayout = (
	blockName: string,
	product: Partial< ProductResponseItem >,
	layoutConfig: LayoutConfig | undefined,
	componentId: number
): ( JSX.Element | null )[] | undefined => {
	if ( ! layoutConfig ) {
		return;
	}

	const blockMap = getBlockMap( blockName );
	return layoutConfig.map( ( [ name, props = {} ], index ) => {
		let children = [] as ( JSX.Element | null )[] | undefined;

		if ( !! props.children && props.children.length > 0 ) {
			// props.children here refers to the children stored in the block attributes. which
			// has the same shape as `layoutConfig`, not React children, which has a different shape */
			children = renderProductLayout(
				blockName,
				product,
				props.children,
				componentId
			);
		}

		const LayoutComponent = blockMap[ name ] as React.ComponentType< {
			product: Partial< ProductResponseItem >;
		} >;

		if ( ! LayoutComponent ) {
			return null;
		}

		const productID = product.id || 0;
		const keyParts = [ 'layout', name, index, componentId, productID ];

		return (
			<Suspense
				key={ keyParts.join( '_' ) }
				fallback={ <div className="wc-block-placeholder" /> }
			>
				<LayoutComponent
					{ ...props }
					children={ children }
					product={ product }
				/>
			</Suspense>
		);
	} );
};
