/**
 * External dependencies
 */
import apiFetch from '@wordpress/api-fetch';
import classNames from 'classnames';
import { getSetting } from '@woocommerce/settings';

export const getSortArgs = ( sortValue ) => {
	const reviewRatingsEnabled = getSetting( 'reviewRatingsEnabled', true );

	if ( reviewRatingsEnabled ) {
		if ( sortValue === 'lowest-rating' ) {
			return {
				order: 'asc',
				orderby: 'rating',
			};
		}
		if ( sortValue === 'highest-rating' ) {
			return {
				order: 'desc',
				orderby: 'rating',
			};
		}
	}

	return {
		order: 'desc',
		orderby: 'date_gmt',
	};
};

export const getReviews = ( args ) => {
	return apiFetch( {
		path:
			'/wc/store/v1/products/reviews?' +
			Object.entries( args )
				.map( ( arg ) => arg.join( '=' ) )
				.join( '&' ),
		parse: false,
	} ).then( ( response ) => {
		return response.json().then( ( reviews ) => {
			const totalReviews = parseInt(
				response.headers.get( 'x-wp-total' ),
				10
			);
			return { reviews, totalReviews };
		} );
	} );
};

export const getBlockClassName = ( attributes ) => {
	const {
		className,
		categoryIds,
		productId,
		showReviewDate,
		showReviewerName,
		showReviewContent,
		showProductName,
		showReviewImage,
		showReviewRating,
	} = attributes;

	let blockClassName = 'wc-block-all-reviews';

	if ( productId ) {
		blockClassName = 'wc-block-reviews-by-product';
	}

	if ( Array.isArray( categoryIds ) ) {
		blockClassName = 'wc-block-reviews-by-category';
	}

	return classNames( blockClassName, className, {
		'has-image': showReviewImage,
		'has-name': showReviewerName,
		'has-date': showReviewDate,
		'has-rating': showReviewRating,
		'has-content': showReviewContent,
		'has-product-name': showProductName,
	} );
};

export const getDataAttrs = ( attributes ) => {
	const {
		categoryIds,
		imageType,
		orderby,
		productId,
		reviewsOnPageLoad,
		reviewsOnLoadMore,
		showLoadMore,
		showOrderby,
	} = attributes;

	const data = {
		'data-image-type': imageType,
		'data-orderby': orderby,
		'data-reviews-on-page-load': reviewsOnPageLoad,
		'data-reviews-on-load-more': reviewsOnLoadMore,
		'data-show-load-more': showLoadMore,
		'data-show-orderby': showOrderby,
	};

	if ( productId ) {
		data[ 'data-product-id' ] = productId;
	}

	if ( Array.isArray( categoryIds ) ) {
		data[ 'data-category-ids' ] = categoryIds.join( ',' );
	}

	return data;
};
