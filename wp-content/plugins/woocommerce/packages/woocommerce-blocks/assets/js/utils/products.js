/**
 * Get the src of the first image attached to a product (the featured image).
 *
 * @param {Object} product        The product object to get the images from.
 * @param {Array}  product.images The array of images, destructured from the product object.
 * @return {string} The full URL to the image.
 */
export function getImageSrcFromProduct( product ) {
	if ( ! product || ! product.images || ! product.images.length ) {
		return '';
	}

	return product.images[ 0 ].src || '';
}

/**
 * Get the ID of the first image attached to a product (the featured image).
 *
 * @param {Object} product        The product object to get the images from.
 * @param {Array}  product.images The array of images, destructured from the product object.
 * @return {number} The ID of the image.
 */
export function getImageIdFromProduct( product ) {
	if ( ! product || ! product.images || ! product.images.length ) {
		return 0;
	}

	return product.images[ 0 ].id || 0;
}
