/**
 * External dependencies
 */
import { renderFrontend } from '@woocommerce/base-utils';

/**
 * Internal dependencies
 */
import FrontendContainerBlock from './frontend-container-block.js';

const selector = `
	.wp-block-woocommerce-all-reviews,
	.wp-block-woocommerce-reviews-by-product,
	.wp-block-woocommerce-reviews-by-category
`;

const getProps = ( el ) => {
	return {
		attributes: {
			showReviewDate: el.classList.contains( 'has-date' ),
			showReviewerName: el.classList.contains( 'has-name' ),
			showReviewImage: el.classList.contains( 'has-image' ),
			showReviewRating: el.classList.contains( 'has-rating' ),
			showReviewContent: el.classList.contains( 'has-content' ),
			showProductName: el.classList.contains( 'has-product-name' ),
		},
	};
};

// @ts-ignore
// Current typing does not work with non-functional components
renderFrontend( { selector, Block: FrontendContainerBlock, getProps } );
