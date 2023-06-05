/**
 * External dependencies
 */
import { __ } from '@wordpress/i18n';
import { clamp } from 'lodash';
import PropTypes from 'prop-types';
import { RangeControl, ToggleControl } from '@wordpress/components';

/**
 * A combination of range controls for product grid layout settings.
 *
 * @param {Object}            props               Incoming props for the component.
 * @param {number}            props.columns
 * @param {number}            props.rows
 * @param {function(any):any} props.setAttributes Setter for block attributes.
 * @param {boolean}           props.alignButtons
 * @param {number}            props.minColumns
 * @param {number}            props.maxColumns
 * @param {number}            props.minRows
 * @param {number}            props.maxRows
 */
const GridLayoutControl = ( {
	columns,
	rows,
	setAttributes,
	alignButtons,
	minColumns = 1,
	maxColumns = 6,
	minRows = 1,
	maxRows = 6,
} ) => {
	return (
		<>
			<RangeControl
				label={ __( 'Columns', 'woocommerce' ) }
				value={ columns }
				onChange={ ( value ) => {
					const newValue = clamp( value, minColumns, maxColumns );
					setAttributes( {
						columns: Number.isNaN( newValue ) ? '' : newValue,
					} );
				} }
				min={ minColumns }
				max={ maxColumns }
			/>
			<RangeControl
				label={ __( 'Rows', 'woocommerce' ) }
				value={ rows }
				onChange={ ( value ) => {
					const newValue = clamp( value, minRows, maxRows );
					setAttributes( {
						rows: Number.isNaN( newValue ) ? '' : newValue,
					} );
				} }
				min={ minRows }
				max={ maxRows }
			/>
			<ToggleControl
				label={ __(
					'Align the last block to the bottom',
					'woocommerce'
				) }
				help={
					alignButtons
						? __(
								'Align the last block to the bottom.',
								'woocommerce'
						  )
						: __(
								'The last inner block will follow other content.',
								'woocommerce'
						  )
				}
				checked={ alignButtons }
				onChange={ () =>
					setAttributes( { alignButtons: ! alignButtons } )
				}
			/>
		</>
	);
};

GridLayoutControl.propTypes = {
	// The current columns count.
	columns: PropTypes.oneOfType( [ PropTypes.number, PropTypes.string ] )
		.isRequired,
	// The current rows count.
	rows: PropTypes.oneOfType( [ PropTypes.number, PropTypes.string ] )
		.isRequired,
	// Whether or not buttons are aligned horizontally across items.
	alignButtons: PropTypes.bool.isRequired,
	// Callback to update the layout settings.
	setAttributes: PropTypes.func.isRequired,
	// Min and max constraints.
	minColumns: PropTypes.number,
	maxColumns: PropTypes.number,
	minRows: PropTypes.number,
	maxRows: PropTypes.number,
};

export default GridLayoutControl;
