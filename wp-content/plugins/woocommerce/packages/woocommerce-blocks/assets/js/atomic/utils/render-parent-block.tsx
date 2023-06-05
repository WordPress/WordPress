/**
 * External dependencies
 */
import { renderFrontend } from '@woocommerce/base-utils';
import { CURRENT_USER_IS_ADMIN } from '@woocommerce/settings';
import {
	Fragment,
	Suspense,
	isValidElement,
	cloneElement,
} from '@wordpress/element';
import parse from 'html-react-parser';
import {
	getRegisteredBlocks,
	hasInnerBlocks,
} from '@woocommerce/blocks-checkout';
import BlockErrorBoundary from '@woocommerce/base-components/block-error-boundary';

/**
 * This file contains logic used on the frontend to convert DOM elements (saved by the block editor) to React
 * Components. These components are registered using registerBlockComponent() and registerCheckoutBlock() and map 1:1
 * to a block by name.
 *
 * Blocks using this system will have their blockName stored as a data attribute, for example:
 * 		<div data-block-name="woocommerce/product-title"></div>
 *
 * This block name is then read, and using the map, dynamically converted to a real React Component.
 *
 * @see registerBlockComponent
 * @see registerCheckoutBlock
 */

/**
 * Gets a component from the block map for a given block name, or returns null if a component is not registered.
 */
const getBlockComponentFromMap = (
	block: string,
	blockMap: Record< string, React.ReactNode >
): React.ElementType | null => {
	return block && blockMap[ block ]
		? ( blockMap[ block ] as React.ElementType )
		: null;
};

/**
 * Render forced blocks which are missing from the template.
 *
 * Forced blocks are registered in registerCheckoutBlock. If a block is forced, it will be inserted in the editor
 * automatically, however, until that happens they may be missing from the frontend. To fix this, we look up what blocks
 * are registered as forced, and then append them here if they are missing.
 *
 * @see registerCheckoutBlock
 */
const renderForcedBlocks = (
	block: string,
	blockMap: Record< string, React.ReactNode >,
	// Current children from the parent (siblings of the forced block)
	blockChildren: NodeListOf< ChildNode > | null,
	// Wrapper for inner components.
	blockWrapper?: React.ElementType
) => {
	if ( ! hasInnerBlocks( block ) ) {
		return null;
	}

	const currentBlocks = blockChildren
		? ( Array.from( blockChildren )
				.map( ( node: Node ) =>
					node instanceof HTMLElement
						? node?.dataset.blockName || null
						: null
				)
				.filter( Boolean ) as string[] )
		: [];

	const forcedBlocks = getRegisteredBlocks( block ).filter(
		( { blockName, force } ) =>
			force === true && ! currentBlocks.includes( blockName )
	);

	// This will wrap inner blocks with the provided wrapper. If no wrapper is provided, we default to Fragment.
	const InnerBlockComponentWrapper = blockWrapper ? blockWrapper : Fragment;

	return (
		<>
			{ forcedBlocks.map(
				(
					{ blockName, component },
					index: number
				): JSX.Element | null => {
					const ForcedComponent = component
						? component
						: getBlockComponentFromMap( blockName, blockMap );
					return ForcedComponent ? (
						<BlockErrorBoundary
							key={ `${ blockName }_blockerror` }
							text={ `Unexpected error in: ${ blockName }` }
							showErrorBlock={ CURRENT_USER_IS_ADMIN as boolean }
						>
							<InnerBlockComponentWrapper>
								<ForcedComponent
									key={ `${ blockName }_forced_${ index }` }
								/>
							</InnerBlockComponentWrapper>
						</BlockErrorBoundary>
					) : null;
				}
			) }
		</>
	);
};

interface renderInnerBlocksProps {
	// Block (parent) being rendered. Used for inner block component mapping.
	block: string;
	// Map of block names to block components for children.
	blockMap: Record< string, React.ReactNode >;
	// Wrapper for inner components.
	blockWrapper?: React.ElementType | undefined;
	// Elements from the DOM being converted to components.
	children: HTMLCollection | NodeList;
	// Depth within the DOM hierarchy.
	depth?: number;
}

/**
 * Recursively replace block markup in the DOM with React Components.
 */
const renderInnerBlocks = ( {
	// This is the parent block we're working within (see renderParentBlock)
	block,
	// This is the map of blockNames->components
	blockMap,
	// Component which inner blocks are wrapped with.
	blockWrapper,
	// The children from the DOM we're currently iterating over.
	children,
	// Current depth of the children. Used to ensure keys are unique.
	depth = 1,
}: renderInnerBlocksProps ): ( string | JSX.Element | null )[] | null => {
	if ( ! children || children.length === 0 ) {
		return null;
	}
	return Array.from( children ).map( ( node: Node, index: number ) => {
		/**
		 * This will grab the blockName from the data- attributes stored in block markup. Without a blockName, we cannot
		 * convert the HTMLElement to a React component.
		 */
		const { blockName = '', ...componentProps } = {
			...( node instanceof HTMLElement ? node.dataset : {} ),
			className: node instanceof Element ? node?.className : '',
		};
		const componentKey = `${ block }_${ depth }_${ index }`;
		const InnerBlockComponent = getBlockComponentFromMap(
			blockName,
			blockMap
		);

		/**
		 * If the component cannot be found, or blockName is missing, return the original element. This also ensures
		 * that children within the element are processed also, since it may be an element containing block markup.
		 *
		 * Note we use childNodes rather than children so that text nodes are also rendered.
		 */
		if ( ! InnerBlockComponent ) {
			const parsedElement = parse(
				( node instanceof Element && node?.outerHTML ) ||
					node?.textContent ||
					''
			);

			// Returns text nodes without manipulation.
			if ( typeof parsedElement === 'string' && !! parsedElement ) {
				return parsedElement;
			}

			// Do not render invalid elements.
			if ( ! isValidElement( parsedElement ) ) {
				return null;
			}

			const renderedChildren = node.childNodes.length
				? renderInnerBlocks( {
						block,
						blockMap,
						children: node.childNodes,
						depth: depth + 1,
						blockWrapper,
				  } )
				: undefined;

			// We pass props here rather than componentProps to avoid the data attributes being renamed.
			return renderedChildren
				? cloneElement(
						parsedElement,
						{
							key: componentKey,
							...( parsedElement?.props || {} ),
						},
						renderedChildren
				  )
				: cloneElement( parsedElement, {
						key: componentKey,
						...( parsedElement?.props || {} ),
				  } );
		}

		// This will wrap inner blocks with the provided wrapper. If no wrapper is provided, we default to Fragment.
		const InnerBlockComponentWrapper = blockWrapper
			? blockWrapper
			: Fragment;

		return (
			<Suspense
				key={ `${ block }_${ depth }_${ index }_suspense` }
				fallback={ <div className="wc-block-placeholder" /> }
			>
				{ /* Prevent third party components from breaking the entire checkout */ }
				<BlockErrorBoundary
					text={ `Unexpected error in: ${ blockName }` }
					showErrorBlock={ CURRENT_USER_IS_ADMIN as boolean }
				>
					<InnerBlockComponentWrapper>
						<InnerBlockComponent
							key={ componentKey }
							{ ...componentProps }
						>
							{
								/**
								 * Within this Inner Block Component we also need to recursively render it's children. This
								 * is done here with a depth+1. The same block map and parent is used, but we pass new
								 * children from this element.
								 */
								renderInnerBlocks( {
									block,
									blockMap,
									children: node.childNodes,
									depth: depth + 1,
									blockWrapper,
								} )
							}
							{
								/**
								 * In addition to the inner blocks, we may also need to render FORCED blocks which have not
								 * yet been added to the inner block template. We do this by comparing the current children
								 * to the list of registered forced blocks.
								 *
								 * @see registerCheckoutBlock
								 */
								renderForcedBlocks(
									blockName,
									blockMap,
									node.childNodes,
									blockWrapper
								)
							}
						</InnerBlockComponent>
					</InnerBlockComponentWrapper>
				</BlockErrorBoundary>
			</Suspense>
		);
	} );
};

/**
 * Render a parent block on the frontend.
 *
 * This is the main entry point used on the frontend to convert Block Markup (with inner blocks) in the DOM to React
 * Components.
 *
 * This uses renderFrontend(). The difference is, renderFrontend renders a single block, but renderParentBlock() also
 * handles inner blocks by recursively running over children from the DOM.
 *
 * @see renderInnerBlocks
 * @see renderFrontend
 */
export const renderParentBlock = ( {
	Block,
	selector,
	blockName,
	getProps = () => ( {} ),
	blockMap,
	blockWrapper,
}: {
	// Parent Block Name. Used for inner block component mapping.
	blockName: string;
	// Map of block names to block components for children.
	blockMap: Record< string, React.ReactNode >;
	// Wrapper for inner components.
	blockWrapper?: React.ElementType;
	// React component to use as a replacement.
	Block: React.FunctionComponent;
	// CSS selector to match the elements to replace.
	selector: string;
	// Function to generate the props object for the block.
	getProps: ( el: Element, i: number ) => Record< string, unknown >;
} ): void => {
	/**
	 * In addition to getProps, we need to render and return the children. This adds children to props.
	 */
	const getPropsWithChildren = ( element: Element, i: number ) => {
		const children = renderInnerBlocks( {
			block: blockName,
			blockMap,
			children: element.children || [],
			blockWrapper,
		} );
		return { ...getProps( element, i ), children };
	};
	/**
	 * The only difference between using renderParentBlock and renderFrontend is that here we provide children.
	 */
	renderFrontend( {
		Block,
		selector,
		getProps: getPropsWithChildren,
	} );
};
