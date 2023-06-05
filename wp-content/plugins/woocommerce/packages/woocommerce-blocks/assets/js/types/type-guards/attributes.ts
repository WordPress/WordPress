/**
 * Internal dependencies
 */
import type { AttributeTerm, AttributeQuery } from '../type-defs';
import { objectHasProp } from './object';

export const isAttributeTerm = ( term: unknown ): term is AttributeTerm => {
	return (
		objectHasProp( term, 'count' ) &&
		objectHasProp( term, 'description' ) &&
		objectHasProp( term, 'id' ) &&
		objectHasProp( term, 'name' ) &&
		objectHasProp( term, 'parent' ) &&
		objectHasProp( term, 'slug' ) &&
		typeof term.count === 'number' &&
		typeof term.description === 'string' &&
		typeof term.id === 'number' &&
		typeof term.name === 'string' &&
		typeof term.parent === 'number' &&
		typeof term.slug === 'string'
	);
};

export const isAttributeTermCollection = (
	terms: unknown
): terms is AttributeTerm[] => {
	return Array.isArray( terms ) && terms.every( isAttributeTerm );
};

export const isAttributeQuery = ( query: unknown ): query is AttributeQuery => {
	return (
		objectHasProp( query, 'attribute' ) &&
		objectHasProp( query, 'operator' ) &&
		objectHasProp( query, 'slug' ) &&
		typeof query.attribute === 'string' &&
		typeof query.operator === 'string' &&
		Array.isArray( query.slug ) &&
		query.slug.every( ( slug ) => typeof slug === 'string' )
	);
};

export const isAttributeQueryCollection = (
	queries: unknown
): queries is AttributeQuery[] => {
	return Array.isArray( queries ) && queries.every( isAttributeQuery );
};
