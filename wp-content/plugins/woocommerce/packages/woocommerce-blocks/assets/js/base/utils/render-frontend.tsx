/**
 * External dependencies
 */
import { render, Suspense } from '@wordpress/element';
import BlockErrorBoundary from '@woocommerce/base-components/block-error-boundary';

// Some blocks take care of rendering their inner blocks automatically. For
// example, the empty cart. In those cases, we don't want to trigger the render
// function of inner components on load. Instead, the wrapper block can trigger
// the event `wc-blocks_render_blocks_frontend` to render its inner blocks.
const selectorsToSkipOnLoad = [ '.wp-block-woocommerce-cart' ];

type BlockProps<
	TProps extends Record< string, unknown >,
	TAttribute extends Record< string, unknown >
> = TProps & {
	attributes?: TAttribute;
};

type BlockType<
	TProps extends Record< string, unknown >,
	TAttribute extends Record< string, unknown >
> = ( props: BlockProps< TProps, TAttribute > ) => JSX.Element | null;

export type GetPropsFn<
	TProps extends Record< string, unknown >,
	TAttributes extends Record< string, unknown >
> = ( el: HTMLElement, i: number ) => BlockProps< TProps, TAttributes >;

interface RenderBlockParams<
	TProps extends Record< string, unknown >,
	TAttributes extends Record< string, unknown >
> {
	// React component to use as a replacement.
	Block: BlockType< TProps, TAttributes > | null;
	// Container to replace with the Block component.
	container: HTMLElement;
	// Attributes object for the block.
	attributes: TAttributes;
	// Props object for the block.
	props: BlockProps< TProps, TAttributes >;
	// Props object for the error boundary.
	errorBoundaryProps?: Record< string, unknown >;
}

/**
 * Renders a block component in a single `container` node.
 */
export const renderBlock = <
	TProps extends Record< string, unknown >,
	TAttributes extends Record< string, unknown >
>( {
	Block,
	container,
	attributes = {} as TAttributes,
	props = {} as BlockProps< TProps, TAttributes >,
	errorBoundaryProps = {},
}: RenderBlockParams< TProps, TAttributes > ): void => {
	render(
		<BlockErrorBoundary { ...errorBoundaryProps }>
			<Suspense fallback={ <div className="wc-block-placeholder" /> }>
				{ Block && <Block { ...props } attributes={ attributes } /> }
			</Suspense>
		</BlockErrorBoundary>,
		container,
		() => {
			if ( container.classList ) {
				container.classList.remove( 'is-loading' );
			}
		}
	);
};

interface RenderBlockInContainersParams<
	TProps extends Record< string, unknown >,
	TAttributes extends Record< string, unknown >
> {
	// React component to use as a replacement.
	Block: BlockType< TProps, TAttributes > | null;
	// Containers to replace with the Block component.
	containers: NodeListOf< Element >;
	// Function to generate the props object for the block.
	getProps?: GetPropsFn< TProps, TAttributes >;
	// Function to generate the props object for the error boundary.
	getErrorBoundaryProps?: (
		el: HTMLElement,
		i: number
	) => Record< string, unknown >;
}

/**
 * Renders a block component in each `containers` node.
 */
const renderBlockInContainers = <
	TProps extends Record< string, unknown >,
	TAttributes extends Record< string, unknown >
>( {
	Block,
	containers,
	getProps = () => ( {} as BlockProps< TProps, TAttributes > ),
	getErrorBoundaryProps = () => ( {} ),
}: RenderBlockInContainersParams< TProps, TAttributes > ): void => {
	if ( containers.length === 0 ) {
		return;
	}

	// Use Array.forEach for IE11 compatibility.
	Array.prototype.forEach.call( containers, ( el, i ) => {
		const props = getProps( el, i );

		const errorBoundaryProps = getErrorBoundaryProps( el, i );
		const attributes = {
			...el.dataset,
			...( props.attributes || {} ),
		};

		renderBlock( {
			Block,
			container: el,
			props,
			attributes,
			errorBoundaryProps,
		} );
	} );
};

// Given an element and a list of wrappers, check if the element is inside at
// least one of the wrappers.
const isElementInsideWrappers = (
	el: Element,
	wrappers: NodeListOf< Element >
): boolean => {
	return Array.prototype.some.call(
		wrappers,
		( wrapper ) => wrapper.contains( el ) && ! wrapper.isSameNode( el )
	);
};

interface RenderBlockOutsideWrappersParams<
	TProps extends Record< string, unknown >,
	TAttributes extends Record< string, unknown >
> extends RenderFrontendParams< TProps, TAttributes > {
	// All elements matched by the selector which are inside the wrapper will be ignored.
	wrappers?: NodeListOf< Element >;
}

/**
 * Renders the block frontend in the elements matched by the selector which are
 * outside the wrapper elements.
 */
const renderBlockOutsideWrappers = <
	TProps extends Record< string, unknown >,
	TAttributes extends Record< string, unknown >
>( {
	Block,
	getProps,
	getErrorBoundaryProps,
	selector,
	wrappers,
}: RenderBlockOutsideWrappersParams< TProps, TAttributes > ): void => {
	const containers = document.body.querySelectorAll( selector );
	// Filter out blocks inside the wrappers.
	if ( wrappers && wrappers.length > 0 ) {
		Array.prototype.filter.call( containers, ( el ) => {
			return ! isElementInsideWrappers( el, wrappers );
		} );
	}
	renderBlockInContainers( {
		Block,
		containers,
		getProps,
		getErrorBoundaryProps,
	} );
};

interface RenderBlockInsideWrapperParams<
	TProps extends Record< string, unknown >,
	TAttributes extends Record< string, unknown >
> extends RenderFrontendParams< TProps, TAttributes > {
	// Wrapper element to query the selector inside.
	wrapper: HTMLElement;
}

/**
 * Renders the block frontend in the elements matched by the selector inside the
 * wrapper element.
 */
const renderBlockInsideWrapper = <
	TProps extends Record< string, unknown >,
	TAttributes extends Record< string, unknown >
>( {
	Block,
	getProps,
	getErrorBoundaryProps,
	selector,
	wrapper,
}: RenderBlockInsideWrapperParams< TProps, TAttributes > ): void => {
	const containers = wrapper.querySelectorAll( selector );
	renderBlockInContainers( {
		Block,
		containers,
		getProps,
		getErrorBoundaryProps,
	} );
};

interface RenderFrontendParams<
	TProps extends Record< string, unknown >,
	TAttributes extends Record< string, unknown >
> {
	// React component to use as a replacement.
	Block: BlockType< TProps, TAttributes > | null;
	// CSS selector to match the elements to replace.
	selector: string;
	// Function to generate the props object for the block.
	getProps?: GetPropsFn< TProps, TAttributes >;
	// Function to generate the props object for the error boundary.
	getErrorBoundaryProps?: (
		el: HTMLElement,
		i: number
	) => Record< string, unknown >;
}

/**
 * Renders the block frontend on page load. If the block is contained inside a
 * wrapper element that should be excluded from initial load, it adds the
 * appropriate event listeners to render the block when the
 * `wc-blocks_render_blocks_frontend` event is triggered.
 */
export const renderFrontend = <
	TProps extends Record< string, unknown >,
	TAttributes extends Record< string, unknown >
>(
	props:
		| RenderBlockOutsideWrappersParams< TProps, TAttributes >
		| RenderBlockInsideWrapperParams< TProps, TAttributes >
): void => {
	const wrappersToSkipOnLoad = document.body.querySelectorAll(
		selectorsToSkipOnLoad.join( ',' )
	);

	const { Block, getProps, getErrorBoundaryProps, selector } = props;

	renderBlockOutsideWrappers( {
		Block,
		getProps,
		getErrorBoundaryProps,
		selector,
		wrappers: wrappersToSkipOnLoad,
	} );
	// For each wrapper, add an event listener to render the inner blocks when
	// `wc-blocks_render_blocks_frontend` event is triggered.
	Array.prototype.forEach.call( wrappersToSkipOnLoad, ( wrapper ) => {
		wrapper.addEventListener( 'wc-blocks_render_blocks_frontend', () => {
			renderBlockInsideWrapper( { ...props, wrapper } );
		} );
	} );
};

export default renderFrontend;
