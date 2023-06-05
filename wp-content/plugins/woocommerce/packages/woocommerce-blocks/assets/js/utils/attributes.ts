/**
 * External dependencies
 */
import { SearchListItem } from '@woocommerce/editor-components/search-list-control/types';
import { getSetting } from '@woocommerce/settings';
import {
	AttributeObject,
	AttributeSetting,
	AttributeTerm,
	AttributeWithTerms,
	isAttributeTerm,
} from '@woocommerce/types';
import { dispatch, select } from '@wordpress/data';

const ATTRIBUTES = getSetting< AttributeSetting[] >( 'attributes', [] );

/**
 * Format an attribute from the settings into an object with standardized keys.
 *
 * @param {Object} attribute The attribute object.
 */
const attributeSettingToObject = ( attribute: AttributeSetting ) => {
	if ( ! attribute || ! attribute.attribute_name ) {
		return null;
	}
	return {
		id: parseInt( attribute.attribute_id, 10 ),
		name: attribute.attribute_name,
		taxonomy: 'pa_' + attribute.attribute_name,
		label: attribute.attribute_label,
	};
};

/**
 * Format all attribute settings into objects.
 */
const attributeObjects = ATTRIBUTES.reduce(
	( acc: Partial< AttributeObject >[], current ) => {
		const attributeObject = attributeSettingToObject( current );

		if ( attributeObject && attributeObject.id ) {
			acc.push( attributeObject );
		}

		return acc;
	},
	[]
);

/**
 * Converts an Attribute object into a shape compatible with the `SearchListControl`
 */
export const convertAttributeObjectToSearchItem = (
	attribute: AttributeObject | AttributeTerm | AttributeWithTerms
): SearchListItem => {
	const { count, id, name, parent } = attribute;

	return {
		count,
		id,
		name,
		parent,
		breadcrumbs: [],
		children: [],
		value: isAttributeTerm( attribute ) ? attribute.attr_slug : '',
	};
};

/**
 * Get attribute data by taxonomy.
 *
 * @param {number} attributeId The attribute ID.
 * @return {Object|undefined} The attribute object if it exists.
 */
export const getAttributeFromID = ( attributeId: number ) => {
	if ( ! attributeId ) {
		return;
	}
	return attributeObjects.find( ( attribute ) => {
		return attribute.id === attributeId;
	} );
};

/**
 * Get attribute data by taxonomy.
 *
 * @param {string} taxonomy The attribute taxonomy name e.g. pa_color.
 * @return {Object|undefined} The attribute object if it exists.
 */
export const getAttributeFromTaxonomy = ( taxonomy: string ) => {
	if ( ! taxonomy ) {
		return;
	}
	return attributeObjects.find( ( attribute ) => {
		return attribute.taxonomy === taxonomy;
	} );
};

/**
 * Get the taxonomy of an attribute by Attribute ID.
 *
 * @param {number} attributeId The attribute ID.
 * @return {string} The taxonomy name.
 */
export const getTaxonomyFromAttributeId = ( attributeId: number ) => {
	if ( ! attributeId ) {
		return null;
	}
	const attribute = getAttributeFromID( attributeId );
	return attribute ? attribute.taxonomy : null;
};

/**
 * Updates an attribute in a sibling block. Useful if two settings control the same attribute, but you don't want to
 * have this attribute exist on a parent block.
 */
export const updateAttributeInSiblingBlock = (
	clientId: string,
	attribute: string,
	newValue: unknown,
	siblingBlockName: string
) => {
	const store = select( 'core/block-editor' );
	const actions = dispatch( 'core/block-editor' );
	const parentBlocks = store.getBlockParents( clientId );

	let shippingMethodsBlockClientId = '';

	// Loop through parent block's children until we find woocommerce/checkout-shipping-methods-block.
	// Also set this attribute in the woocommerce/checkout-shipping-methods-block.
	parentBlocks.forEach( ( parent ) => {
		const childBlock = store
			.getBlock( parent )
			.innerBlocks.find( ( child ) => child.name === siblingBlockName );
		if ( ! childBlock ) {
			return;
		}
		shippingMethodsBlockClientId = childBlock.clientId;
	} );
	actions.updateBlockAttributes( shippingMethodsBlockClientId, {
		[ attribute ]: newValue,
	} );
};
