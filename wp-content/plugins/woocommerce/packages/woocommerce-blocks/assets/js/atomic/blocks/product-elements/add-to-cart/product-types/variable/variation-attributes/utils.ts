/**
 * External dependencies
 */
import { decodeEntities } from '@wordpress/html-entities';
import {
	Dictionary,
	isObject,
	ProductResponseAttributeItem,
	ProductResponseTermItem,
	ProductResponseVariationsItem,
} from '@woocommerce/types';
import { keyBy } from '@woocommerce/base-utils';

/**
 * Internal dependencies
 */
import { AttributesMap } from '../types';

/**
 * Key an array of attributes by name,
 */
export const getAttributes = (
	attributes?: ProductResponseAttributeItem[] | null
) => {
	return attributes
		? keyBy(
				Object.values( attributes ).filter(
					( { has_variations: hasVariations } ) => hasVariations
				),
				'name'
		  )
		: {};
};

/**
 * Format variations from the API into a map of just the attribute names and values.
 *
 * Note, each item is keyed by the variation ID with an id: prefix. This is to prevent the object
 * being reordered when iterated.
 */
export const getVariationAttributes = (
	/**
	 * List of Variation objects and attributes keyed by variation ID.
	 */
	variations?: ProductResponseVariationsItem[] | null
) => {
	if ( ! variations ) {
		return {};
	}

	const attributesMap: AttributesMap = {};

	variations.forEach( ( { id, attributes } ) => {
		attributesMap[ `id:${ id }` ] = {
			id,
			attributes: attributes.reduce( ( acc, { name, value } ) => {
				acc[ name ] = value;
				return acc;
			}, {} as Dictionary ),
		};
	} );

	return attributesMap;
};

/**
 * Given a list of variations and a list of attribute values, return variations which match.
 *
 * Allows an attribute to be excluded by name. This is used to filter displayed options for
 * individual attribute selects.
 *
 * @return List of matching variation IDs.
 */
export const getVariationsMatchingSelectedAttributes = (
	/**
	 * List of attribute names and terms.
	 *
	 * As returned from {@link getAttributes()}.
	 */
	attributes: Record< string, ProductResponseAttributeItem >,
	/**
	 * Attributes for each variation keyed by variation ID.
	 *
	 * As returned from {@link getVariationAttributes()}.
	 */
	variationAttributes: AttributesMap,
	/**
	 * Attribute Name Value pairs of current selections by the user.
	 */
	selectedAttributes: Record< string, string | null >
) => {
	const variationIds = Object.values( variationAttributes ).map(
		( { id } ) => id
	);

	// If nothing is selected yet, just return all variations.
	if (
		Object.values( selectedAttributes ).every( ( value ) => value === '' )
	) {
		return variationIds;
	}

	const attributeNames = Object.keys( attributes );

	return variationIds.filter( ( variationId ) =>
		attributeNames.every( ( attributeName ) => {
			const selectedAttribute = selectedAttributes[ attributeName ] || '';
			const variationAttribute =
				variationAttributes[ 'id:' + variationId ].attributes[
					attributeName
				];

			// If there is no selected attribute, consider this a match.
			if ( selectedAttribute === '' ) {
				return true;
			}
			// If the variation attributes for this attribute are set to null, it matches all values.
			if ( variationAttribute === null ) {
				return true;
			}
			// Otherwise, only match if the selected values are the same.
			return variationAttribute === selectedAttribute;
		} )
	);
};

/**
 * Given a list of variations and a list of attribute values, returns the first matched variation ID.
 *
 * @return Variation ID.
 */
export const getVariationMatchingSelectedAttributes = (
	/**
	 * List of attribute names and terms.
	 *
	 * As returned from {@link getAttributes()}.
	 */
	attributes: Record< string, ProductResponseAttributeItem >,
	/**
	 * Attributes for each variation keyed by variation ID.
	 *
	 * As returned from {@link getVariationAttributes()}.
	 */
	variationAttributes: AttributesMap,
	/**
	 * Attribute Name Value pairs of current selections by the user.
	 */
	selectedAttributes: Dictionary
) => {
	const matchingVariationIds = getVariationsMatchingSelectedAttributes(
		attributes,
		variationAttributes,
		selectedAttributes
	);
	return matchingVariationIds[ 0 ] || 0;
};

/**
 * Given a list of terms, filter them and return valid options for the select boxes.
 *
 * @see getActiveSelectControlOptions
 *
 * @return Value/Label pairs of select box options.
 */
const getValidSelectControlOptions = (
	/**
	 * List of attribute term objects.
	 */
	attributeTerms: ProductResponseTermItem[],
	/**
	 * Valid values if selections have been made already.
	 */
	validAttributeTerms: Array< string | null > | null = null
) => {
	return Object.values( attributeTerms )
		.map( ( { name, slug } ) => {
			if (
				validAttributeTerms === null ||
				validAttributeTerms.includes( null ) ||
				validAttributeTerms.includes( slug )
			) {
				return {
					value: slug,
					label: decodeEntities( name ),
				};
			}
			return null;
		} )
		.filter( Boolean );
};

/**
 * Given a list of terms, filter them and return active options for the select boxes. This factors in
 * which options should be hidden due to current selections.
 *
 * @return Select box options.
 */
export const getActiveSelectControlOptions = (
	/**
	 * List of attribute names and terms.
	 *
	 * As returned from {@link getAttributes()}.
	 */
	attributes: Record< string, ProductResponseAttributeItem >,
	/**
	 * Attributes for each variation keyed by variation ID.
	 *
	 * As returned from {@link getVariationAttributes()}.
	 */
	variationAttributes: AttributesMap,
	/**
	 * Attribute Name Value pairs of current selections by the user.
	 */
	selectedAttributes: Dictionary
) => {
	const options: Record<
		string,
		Array< { label: string; value: string } | null >
	> = {};
	const attributeNames = Object.keys( attributes );
	const hasSelectedAttributes =
		Object.values( selectedAttributes ).filter( Boolean ).length > 0;

	attributeNames.forEach( ( attributeName ) => {
		const currentAttribute = attributes[ attributeName ];
		const selectedAttributesExcludingCurrentAttribute = {
			...selectedAttributes,
			[ attributeName ]: null,
		};
		// This finds matching variations for selected attributes apart from this one. This will be
		// used to get valid attribute terms of the current attribute narrowed down by those matching
		// variation IDs. For example, if I had Large Blue Shirts and Medium Red Shirts, I want to only
		// show Red shirts if Medium is selected.
		const matchingVariationIds = hasSelectedAttributes
			? getVariationsMatchingSelectedAttributes(
					attributes,
					variationAttributes,
					selectedAttributesExcludingCurrentAttribute
			  )
			: null;
		// Uses the above matching variation IDs to get the attributes from just those variations.
		const validAttributeTerms =
			matchingVariationIds !== null
				? matchingVariationIds.map(
						( varId ) =>
							variationAttributes[ 'id:' + varId ].attributes[
								attributeName
							]
				  )
				: null;
		// Intersects attributes with valid attributes.
		options[ attributeName ] = getValidSelectControlOptions(
			currentAttribute.terms,
			validAttributeTerms
		);
	} );

	return options;
};

/**
 * Return the default values of the given attributes in a format ready to be set in state.
 *
 * @return Default attributes.
 */
export const getDefaultAttributes = (
	/**
	 * List of attribute names and terms.
	 *
	 * As returned from {@link getAttributes()}.
	 */
	attributes: Record< string, ProductResponseAttributeItem >
) => {
	if ( ! isObject( attributes ) ) {
		return {};
	}

	const attributeNames = Object.keys( attributes );

	if ( attributeNames.length === 0 ) {
		return {};
	}

	const attributesEntries = Object.values( attributes );

	return attributesEntries.reduce( ( acc, curr ) => {
		const defaultValues = curr.terms.filter( ( term ) => term.default );

		if ( defaultValues.length > 0 ) {
			acc[ curr.name ] = defaultValues[ 0 ]?.slug;
		}

		return acc;
	}, {} as Dictionary );
};
