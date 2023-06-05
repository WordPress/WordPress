/**
 * External dependencies
 */
import { useState, useEffect, useMemo } from '@wordpress/element';
import { useShallowEqual } from '@woocommerce/base-hooks';
import type { SelectControl } from '@wordpress/components';
import { Dictionary, ProductResponseAttributeItem } from '@woocommerce/types';

/**
 * Internal dependencies
 */
import AttributeSelectControl from './attribute-select-control';
import {
	getVariationMatchingSelectedAttributes,
	getActiveSelectControlOptions,
	getDefaultAttributes,
} from './utils';
import { AttributesMap, VariationParam } from '../types';

interface Props {
	attributes: Record< string, ProductResponseAttributeItem >;
	setRequestParams: ( param: VariationParam ) => void;
	variationAttributes: AttributesMap;
}

/**
 * AttributePicker component.
 */
const AttributePicker = ( {
	attributes,
	variationAttributes,
	setRequestParams,
}: Props ) => {
	const currentAttributes = useShallowEqual( attributes );
	const currentVariationAttributes = useShallowEqual( variationAttributes );
	const [ variationId, setVariationId ] = useState( 0 );
	const [ selectedAttributes, setSelectedAttributes ] =
		useState< Dictionary >( {} );
	const [ hasSetDefaults, setHasSetDefaults ] = useState( false );

	// Get options for each attribute picker.
	const filteredAttributeOptions = useMemo( () => {
		return getActiveSelectControlOptions(
			currentAttributes,
			currentVariationAttributes,
			selectedAttributes
		);
	}, [ selectedAttributes, currentAttributes, currentVariationAttributes ] );

	// Set default attributes as selected.
	useEffect( () => {
		if ( ! hasSetDefaults ) {
			const defaultAttributes = getDefaultAttributes( attributes );
			if ( defaultAttributes ) {
				setSelectedAttributes( {
					...defaultAttributes,
				} );
			}
			setHasSetDefaults( true );
		}
	}, [ selectedAttributes, attributes, hasSetDefaults ] );

	// Select variations when selections are change.
	useEffect( () => {
		const hasSelectedAllAttributes =
			Object.values( selectedAttributes ).filter(
				( selected ) => selected !== ''
			).length === Object.keys( currentAttributes ).length;

		if ( hasSelectedAllAttributes ) {
			setVariationId(
				getVariationMatchingSelectedAttributes(
					currentAttributes,
					currentVariationAttributes,
					selectedAttributes
				)
			);
		} else if ( variationId > 0 ) {
			// Unset variation when form is incomplete.
			setVariationId( 0 );
		}
	}, [
		selectedAttributes,
		variationId,
		currentAttributes,
		currentVariationAttributes,
	] );

	// Set requests params as variation ID and data changes.
	useEffect( () => {
		setRequestParams( {
			id: variationId,
			variation: Object.keys( selectedAttributes ).map(
				( attributeName ) => {
					return {
						attribute: attributeName,
						value: selectedAttributes[ attributeName ],
					};
				}
			),
		} );
	}, [ setRequestParams, variationId, selectedAttributes ] );

	return (
		<div className="wc-block-components-product-add-to-cart-attribute-picker">
			{ Object.keys( currentAttributes ).map( ( attributeName ) => (
				<AttributeSelectControl
					key={ attributeName }
					attributeName={ attributeName }
					options={
						filteredAttributeOptions[ attributeName ].filter(
							Boolean
						) as SelectControl.Option[]
					}
					value={ selectedAttributes[ attributeName ] }
					onChange={ ( selected ) => {
						setSelectedAttributes( {
							...selectedAttributes,
							[ attributeName ]: selected,
						} );
					} }
				/>
			) ) }
		</div>
	);
};

export default AttributePicker;
