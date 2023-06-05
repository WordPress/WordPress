/**
 * External dependencies
 */
import { createBlock, registerBlockType } from '@wordpress/blocks';
import { Icon, listView } from '@wordpress/icons';

/**
 * Internal dependencies
 */
import './editor.scss';
import metadata from './block.json';
import './style.scss';
import { Edit } from './edit';

registerBlockType( metadata, {
	icon: {
		src: (
			<Icon
				icon={ listView }
				className="wc-block-editor-components-block-icon"
			/>
		),
	},
	transforms: {
		from: [
			{
				type: 'block',
				blocks: [ 'core/legacy-widget' ],
				// We can't transform if raw instance isn't shown in the REST API.
				isMatch: ( { idBase, instance } ) =>
					idBase === 'woocommerce_product_categories' &&
					!! instance?.raw,
				transform: ( { instance } ) =>
					createBlock( 'woocommerce/product-categories', {
						hasCount: !! instance.raw.count,
						hasEmpty: ! instance.raw.hide_empty,
						isDropdown: !! instance.raw.dropdown,
						isHierarchical: !! instance.raw.hierarchical,
					} ),
			},
		],
	},

	deprecated: [
		{
			// Deprecate HTML save method in favor of dynamic rendering.
			attributes: {
				hasCount: {
					type: 'boolean',
					default: true,
					source: 'attribute',
					selector: 'div',
					attribute: 'data-has-count',
				},
				hasEmpty: {
					type: 'boolean',
					default: false,
					source: 'attribute',
					selector: 'div',
					attribute: 'data-has-empty',
				},
				isDropdown: {
					type: 'boolean',
					default: false,
					source: 'attribute',
					selector: 'div',
					attribute: 'data-is-dropdown',
				},
				isHierarchical: {
					type: 'boolean',
					default: true,
					source: 'attribute',
					selector: 'div',
					attribute: 'data-is-hierarchical',
				},
			},
			migrate( attributes ) {
				return attributes;
			},
			save( props ) {
				const { hasCount, hasEmpty, isDropdown, isHierarchical } =
					props.attributes;
				const data = {};
				if ( hasCount ) {
					data[ 'data-has-count' ] = true;
				}
				if ( hasEmpty ) {
					data[ 'data-has-empty' ] = true;
				}
				if ( isDropdown ) {
					data[ 'data-is-dropdown' ] = true;
				}
				if ( isHierarchical ) {
					data[ 'data-is-hierarchical' ] = true;
				}
				return (
					<div className="is-loading" { ...data }>
						{ isDropdown ? (
							<span
								aria-hidden
								className="wc-block-product-categories__placeholder"
							/>
						) : (
							<ul aria-hidden>
								<li>
									<span className="wc-block-product-categories__placeholder" />
								</li>
								<li>
									<span className="wc-block-product-categories__placeholder" />
								</li>
								<li>
									<span className="wc-block-product-categories__placeholder" />
								</li>
							</ul>
						) }
					</div>
				);
			},
		},
	],

	edit: Edit,

	/**
	 * Save nothing; rendered by server.
	 */
	save() {
		return null;
	},
} );
