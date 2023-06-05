/**
 * External dependencies
 */
import { __ } from '@wordpress/i18n';
import { PanelBody, RangeControl } from '@wordpress/components';
import { useBlockProps, InspectorControls } from '@wordpress/block-editor';
import { getSetting } from '@woocommerce/settings';
import Noninteractive from '@woocommerce/base-components/noninteractive';

/**
 * Internal dependencies
 */
import Block from './block';
import './editor.scss';

interface Attributes {
	className?: string;
	columns: number;
}

interface Props {
	attributes: Attributes;
	setAttributes: ( attributes: Record< string, unknown > ) => void;
}

export const Edit = ( { attributes, setAttributes }: Props ): JSX.Element => {
	const { className, columns } = attributes;
	const blockProps = useBlockProps();

	return (
		<div { ...blockProps }>
			<InspectorControls>
				<PanelBody
					title={ __( 'Settings', 'woo-gutenberg-products-block' ) }
				>
					<RangeControl
						label={ __(
							'Cross-Sells products to show',
							'woo-gutenberg-products-block'
						) }
						value={ columns }
						onChange={ ( value ) =>
							setAttributes( { columns: value } )
						}
						min={ getSetting( 'min_columns', 1 ) }
						max={ getSetting( 'max_columns', 6 ) }
					/>
				</PanelBody>
			</InspectorControls>
			<Noninteractive>
				<Block columns={ columns } className={ className } />
			</Noninteractive>
		</div>
	);
};

export const Save = (): JSX.Element => {
	return <div { ...useBlockProps.save() } />;
};
