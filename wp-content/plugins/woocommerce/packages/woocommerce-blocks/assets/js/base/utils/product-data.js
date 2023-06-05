/**
 * Check a product object to see if it can be purchased.
 *
 * @param {Object} product Product object.
 * @return {boolean} True if purchasable.
 */
export const productIsPurchasable = ( product ) => {
	return product.is_purchasable || false;
};

/**
 * Check if the product is supported by the blocks add to cart form.
 *
 * @param {Object} product Product object.
 * @return {boolean} True if supported.
 */
export const productSupportsAddToCartForm = ( product ) => {
	/**
	 * @todo Define supported product types for add to cart form.
	 *
	 * When introducing the form-element registration system, include a method of defining if a
	 * product type has support.
	 *
	 * If, as an example, we went with an inner block system for the add to cart form, we could allow
	 * a type to be registered along with it's default Block template. Registered types would then be
	 * picked up here, as well as the core types which would be defined elsewhere.
	 */
	const supportedTypes = [ 'simple', 'variable' ];

	return supportedTypes.includes( product.type || 'simple' );
};
