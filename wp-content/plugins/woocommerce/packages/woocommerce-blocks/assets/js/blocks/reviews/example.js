/**
 * External dependencies
 */
import { previewReviews } from '@woocommerce/resource-previews';

export const example = {
	attributes: {
		editMode: false,
		imageType: 'reviewer',
		orderby: 'most-recent',
		reviewsOnLoadMore: 10,
		reviewsOnPageLoad: 10,
		showLoadMore: true,
		showOrderby: true,
		showReviewDate: true,
		showReviewerName: true,
		showReviewImage: true,
		showReviewRating: true,
		showReviewContent: true,
		previewReviews,
	},
};
