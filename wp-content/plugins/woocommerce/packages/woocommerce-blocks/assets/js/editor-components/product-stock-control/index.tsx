/**
 * External dependencies
 */
import { __, sprintf } from '@wordpress/i18n';
import { getSetting } from '@woocommerce/settings';
import { useCallback, useState, useEffect } from '@wordpress/element';
import { ToggleControl } from '@wordpress/components';

export interface ProductStockControlProps {
	value: Array< string >;
	setAttributes: ( attributes: Record< string, unknown > ) => void;
}

// Look up whether or not out of stock items should be hidden globally.
const hideOutOfStockItems = getSetting( 'hideOutOfStockItems', false );

// Get the stock status options.
const allStockStatusOptions = getSetting( 'stockStatusOptions', {} );

/**
 * A pre-configured SelectControl for product stock settings.
 */
const ProductStockControl = ( {
	value,
	setAttributes,
}: ProductStockControlProps ): JSX.Element => {
	// Determine whether or not to use the out of stock status.
	const { outofstock, ...otherStockStatusOptions } = allStockStatusOptions;
	const stockStatusOptions = hideOutOfStockItems
		? otherStockStatusOptions
		: allStockStatusOptions;

	/**
	 * Valid options must be in an array of [ 'value' : 'mystatus', 'label' : 'My label' ] format.
	 * stockStatusOptions are returned as [ 'mystatus' : 'My label' ].
	 * Formatting is corrected here.
	 */
	const displayOptions = Object.entries( stockStatusOptions )
		.map( ( [ slug, name ] ) => ( { value: slug, label: name } ) )
		.filter( ( status ) => !! status.label );

	// Set the initial state to the default or saved value.
	const [ checkedOptions, setChecked ] = useState( value );

	/**
	 * Set attributes when checked items change.
	 * Note: The blank stock status prevents all results returning when all options are unchecked.
	 */
	useEffect( () => {
		setAttributes( {
			stockStatus: [ '', ...checkedOptions ],
		} );
	}, [ checkedOptions, setAttributes ] );

	/**
	 * When a checkbox in the list changes, update state.
	 */
	const onChange = useCallback(
		( checkedValue: string ) => {
			const previouslyChecked = checkedOptions.includes( checkedValue );

			const newChecked = checkedOptions.filter(
				( filteredValue ) => filteredValue !== checkedValue
			);

			if ( ! previouslyChecked ) {
				newChecked.push( checkedValue );
				newChecked.sort();
			}

			setChecked( newChecked );
		},
		[ checkedOptions ]
	);

	return (
		<>
			{ displayOptions.map( ( option ) => {
				const helpText = checkedOptions.includes( option.value )
					? /* translators: %s stock status. */ __(
							'Stock status "%s" visible.',
							'woo-gutenberg-products-block'
					  )
					: /* translators: %s stock status. */ __(
							'Stock status "%s" hidden.',
							'woo-gutenberg-products-block'
					  );
				return (
					<ToggleControl
						label={ option.label }
						key={ option.value }
						help={ sprintf( helpText, option.label ) }
						checked={ checkedOptions.includes( option.value ) }
						onChange={ () => onChange( option.value ) }
					/>
				);
			} ) }
		</>
	);
};

export default ProductStockControl;
