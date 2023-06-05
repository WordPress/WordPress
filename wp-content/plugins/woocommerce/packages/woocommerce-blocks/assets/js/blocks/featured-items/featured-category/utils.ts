/**
 * External dependencies
 */
import { WP_REST_API_Category } from 'wp-types';

/**
 * Internal dependencies
 */
import { isImageObject } from '../types';

/**
 * Get the src from a category object, unless null (no image).
 */
export function getCategoryImageSrc( category: WP_REST_API_Category ) {
	if ( category && isImageObject( category.image ) ) {
		return category.image.src;
	}
	return '';
}

/**
 * Get the attachment ID from a category object, unless null (no image).
 */
export function getCategoryImageId( category: WP_REST_API_Category ) {
	if ( category && isImageObject( category.image ) ) {
		return category.image.id;
	}
	return 0;
}
