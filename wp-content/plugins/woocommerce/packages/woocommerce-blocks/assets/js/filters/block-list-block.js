/**
 * External dependencies
 */
import { Component } from '@wordpress/element';
import { createHigherOrderComponent } from '@wordpress/compose';
import { getBlockType } from '@wordpress/blocks';
import { addFilter } from '@wordpress/hooks';

/**
 * withDefaultAttributes HOC for editor.BlockListBlock.
 *
 * @param  object BlockListBlock The BlockListBlock element.
 */
const withDefaultAttributes = createHigherOrderComponent(
	( BlockListBlock ) => {
		class WrappedComponent extends Component {
			mounted = false;

			componentDidMount() {
				const { block, setAttributes } = this.props;

				if ( block.name.startsWith( 'woocommerce/' ) ) {
					setAttributes( this.getAttributesWithDefaults() );
				}
			}

			componentDidUpdate() {
				if (
					this.props.block.name.startsWith( 'woocommerce/' ) &&
					! this.mounted
				) {
					this.mounted = true;
				}
			}

			getAttributesWithDefaults() {
				const blockType = getBlockType( this.props.block.name );
				let attributes = this.props.attributes;

				if (
					! this.mounted &&
					this.props.block.name.startsWith( 'woocommerce/' ) &&
					typeof blockType.attributes !== 'undefined' &&
					typeof blockType.defaults !== 'undefined'
				) {
					attributes = Object.assign(
						{},
						this.props.attributes || {}
					);
					Object.keys( blockType.attributes ).map( ( key ) => {
						if (
							typeof attributes[ key ] === 'undefined' &&
							typeof blockType.defaults[ key ] !== 'undefined'
						) {
							attributes[ key ] = blockType.defaults[ key ];
						}
						return key;
					} );
				}
				return attributes;
			}

			render() {
				return (
					<BlockListBlock
						{ ...this.props }
						attributes={ this.getAttributesWithDefaults() }
					/>
				);
			}
		}
		return WrappedComponent;
	},
	'withDefaultAttributes'
);

/**
 * Hook into `editor.BlockListBlock` to set default attributes (if blocks
 * define them separately) when a block is inserted.
 *
 * This is a workaround for Gutenberg which does not save "default" attributes
 * to the post, which means if defaults change, all existing blocks change too.
 *
 * See https://github.com/WordPress/gutenberg/issues/7342
 *
 * To use this, the block name needs a `woocommerce/` prefix, and as well
 * as defining `attributes` during block registration, you must also declare an
 * array called `defaults`. Defaults should be omitted from `attributes`.
 */
addFilter(
	'editor.BlockListBlock',
	'woocommerce-blocks/block-list-block',
	withDefaultAttributes
);
