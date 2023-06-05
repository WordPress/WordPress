/**
 * External dependencies
 */
import { __ } from '@wordpress/i18n';
import { createBlock } from '@wordpress/blocks';
import {
	BlockControls,
	InnerBlocks,
	InspectorControls,
} from '@wordpress/block-editor';
import { withDispatch, withSelect } from '@wordpress/data';
import {
	PanelBody,
	withSpokenMessages,
	Placeholder,
	Button,
	ToolbarGroup,
	Disabled,
	Tip,
} from '@wordpress/components';
import { Component } from '@wordpress/element';
import { compose } from '@wordpress/compose';
import PropTypes from 'prop-types';
import { Icon, grid } from '@wordpress/icons';
import GridLayoutControl from '@woocommerce/editor-components/grid-layout-control';
import {
	InnerBlockLayoutContextProvider,
	ProductDataContextProvider,
} from '@woocommerce/shared-context';
import { getBlockMap } from '@woocommerce/atomic-utils';
import { previewProducts } from '@woocommerce/resource-previews';
import { getSetting } from '@woocommerce/settings';
import { blocksConfig } from '@woocommerce/block-settings';

/**
 * Internal dependencies
 */
import { getBlockClassName } from '../utils';
import {
	renderHiddenContentPlaceholder,
	renderNoProductsPlaceholder,
} from '../edit-utils';
import {
	DEFAULT_PRODUCT_LIST_LAYOUT,
	getProductLayoutConfig,
} from '../base-utils';
import { getSharedContentControls, getSharedListControls } from '../edit';
import Block from './block';
import './editor.scss';

/**
 * Component to handle edit mode of "All Products".
 */
class Editor extends Component {
	static propTypes = {
		/**
		 * The attributes for this block.
		 */
		attributes: PropTypes.object.isRequired,
		/**
		 * A callback to update attributes.
		 */
		setAttributes: PropTypes.func.isRequired,
		/**
		 * From withSpokenMessages.
		 */
		debouncedSpeak: PropTypes.func.isRequired,
	};

	state = {
		isEditing: false,
		innerBlocks: [],
	};

	blockMap = getBlockMap( 'woocommerce/all-products' );

	componentDidMount = () => {
		const { block } = this.props;
		this.setState( { innerBlocks: block.innerBlocks } );
	};

	getTitle = () => {
		return __( 'All Products', 'woocommerce' );
	};

	getIcon = () => {
		return <Icon icon={ grid } />;
	};

	togglePreview = () => {
		const { debouncedSpeak } = this.props;

		this.setState( { isEditing: ! this.state.isEditing } );

		if ( ! this.state.isEditing ) {
			debouncedSpeak(
				__(
					'Showing All Products block preview.',
					'woocommerce'
				)
			);
		}
	};

	getInspectorControls = () => {
		const { attributes, setAttributes } = this.props;
		const { columns, rows, alignButtons } = attributes;

		return (
			<InspectorControls key="inspector">
				<PanelBody
					title={ __(
						'Layout Settings',
						'woocommerce'
					) }
					initialOpen
				>
					<GridLayoutControl
						columns={ columns }
						rows={ rows }
						alignButtons={ alignButtons }
						setAttributes={ setAttributes }
						minColumns={ getSetting( 'min_columns', 1 ) }
						maxColumns={ getSetting( 'max_columns', 6 ) }
						minRows={ getSetting( 'min_rows', 1 ) }
						maxRows={ getSetting( 'max_rows', 6 ) }
					/>
				</PanelBody>
				<PanelBody
					title={ __(
						'Content Settings',
						'woocommerce'
					) }
				>
					{ getSharedContentControls( attributes, setAttributes ) }
					{ getSharedListControls( attributes, setAttributes ) }
				</PanelBody>
			</InspectorControls>
		);
	};

	getBlockControls = () => {
		const { isEditing } = this.state;

		return (
			<BlockControls>
				<ToolbarGroup
					controls={ [
						{
							icon: 'edit',
							title: __(
								'Edit the layout of each product',
								'woocommerce'
							),
							onClick: () => this.togglePreview(),
							isActive: isEditing,
						},
					] }
				/>
			</BlockControls>
		);
	};

	renderEditMode = () => {
		const onDone = () => {
			const { block, setAttributes } = this.props;
			setAttributes( {
				layoutConfig: getProductLayoutConfig( block.innerBlocks ),
			} );
			this.setState( { innerBlocks: block.innerBlocks } );
			this.togglePreview();
		};

		const onCancel = () => {
			const { block, replaceInnerBlocks } = this.props;
			const { innerBlocks } = this.state;
			replaceInnerBlocks( block.clientId, innerBlocks, false );
			this.togglePreview();
		};

		const onReset = () => {
			const { block, replaceInnerBlocks } = this.props;
			const newBlocks = [];
			DEFAULT_PRODUCT_LIST_LAYOUT.map( ( [ name, attributes ] ) => {
				newBlocks.push( createBlock( name, attributes ) );
				return true;
			} );
			replaceInnerBlocks( block.clientId, newBlocks, false );
			this.setState( { innerBlocks: block.innerBlocks } );
		};

		const InnerBlockProps = {
			template: this.props.attributes.layoutConfig,
			templateLock: false,
			allowedBlocks: Object.keys( this.blockMap ),
		};

		if ( this.props.attributes.layoutConfig.length !== 0 ) {
			InnerBlockProps.renderAppender = false;
		}

		return (
			<Placeholder icon={ this.getIcon() } label={ this.getTitle() }>
				{ __(
					'Display all products from your store as a grid.',
					'woocommerce'
				) }
				<div className="wc-block-all-products-grid-item-template">
					<Tip>
						{ __(
							'Edit the blocks inside the example below to change the content displayed for all products within the product grid.',
							'woocommerce'
						) }
					</Tip>
					<InnerBlockLayoutContextProvider
						parentName="woocommerce/all-products"
						parentClassName="wc-block-grid"
					>
						<div className="wc-block-grid wc-block-layout has-1-columns">
							<ul className="wc-block-grid__products">
								<li className="wc-block-grid__product">
									<ProductDataContextProvider
										product={ previewProducts[ 0 ] }
									>
										<InnerBlocks { ...InnerBlockProps } />
									</ProductDataContextProvider>
								</li>
							</ul>
						</div>
					</InnerBlockLayoutContextProvider>
					<div className="wc-block-all-products__actions">
						<Button
							className="wc-block-all-products__done-button"
							isPrimary
							onClick={ onDone }
						>
							{ __( 'Done', 'woocommerce' ) }
						</Button>
						<Button
							className="wc-block-all-products__cancel-button"
							isTertiary
							onClick={ onCancel }
						>
							{ __( 'Cancel', 'woocommerce' ) }
						</Button>
						<Button
							className="wc-block-all-products__reset-button"
							icon={ <Icon icon={ grid } /> }
							label={ __(
								'Reset layout to default',
								'woocommerce'
							) }
							onClick={ onReset }
						>
							{ __(
								'Reset Layout',
								'woocommerce'
							) }
						</Button>
					</div>
				</div>
			</Placeholder>
		);
	};

	renderViewMode = () => {
		const { attributes } = this.props;
		const { layoutConfig } = attributes;
		const hasContent = layoutConfig && layoutConfig.length !== 0;
		const blockTitle = this.getTitle();
		const blockIcon = this.getIcon();

		if ( ! hasContent ) {
			return renderHiddenContentPlaceholder( blockTitle, blockIcon );
		}

		return (
			<Disabled>
				<Block attributes={ attributes } />
			</Disabled>
		);
	};

	render = () => {
		const { attributes } = this.props;
		const { isEditing } = this.state;
		const blockTitle = this.getTitle();
		const blockIcon = this.getIcon();

		if ( blocksConfig.productCount === 0 ) {
			return renderNoProductsPlaceholder( blockTitle, blockIcon );
		}

		return (
			<div
				className={ getBlockClassName(
					'wc-block-all-products',
					attributes
				) }
			>
				{ this.getBlockControls() }
				{ this.getInspectorControls() }
				{ isEditing ? this.renderEditMode() : this.renderViewMode() }
			</div>
		);
	};
}

export default compose(
	withSpokenMessages,
	withSelect( ( select, { clientId } ) => {
		const { getBlock } = select( 'core/block-editor' );
		return {
			block: getBlock( clientId ),
		};
	} ),
	withDispatch( ( dispatch ) => {
		const { replaceInnerBlocks } = dispatch( 'core/block-editor' );
		return {
			replaceInnerBlocks,
		};
	} )
)( Editor );
