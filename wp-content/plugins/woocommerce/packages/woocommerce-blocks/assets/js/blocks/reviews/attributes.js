export default {
	/**
	 * Toggle for edit mode in the block preview.
	 */
	editMode: {
		type: 'boolean',
		default: true,
	},

	/**
	 * Whether to display the reviewer or product image.
	 */
	imageType: {
		type: 'string',
		default: 'reviewer',
	},

	/**
	 * Order to use for the reviews listing.
	 */
	orderby: {
		type: 'string',
		default: 'most-recent',
	},

	/**
	 * Number of reviews to add when clicking on load more.
	 */
	reviewsOnLoadMore: {
		type: 'number',
		default: 10,
	},

	/**
	 * Number of reviews to display on page load.
	 */
	reviewsOnPageLoad: {
		type: 'number',
		default: 10,
	},

	/**
	 * Show the load more button.
	 */
	showLoadMore: {
		type: 'boolean',
		default: true,
	},

	/**
	 * Show the order by selector.
	 */
	showOrderby: {
		type: 'boolean',
		default: true,
	},

	/**
	 * Show the review date.
	 */
	showReviewDate: {
		type: 'boolean',
		default: true,
	},

	/**
	 * Show the reviewer name.
	 */
	showReviewerName: {
		type: 'boolean',
		default: true,
	},

	/**
	 * Show the review image..
	 */
	showReviewImage: {
		type: 'boolean',
		default: true,
	},

	/**
	 * Show the product rating.
	 */
	showReviewRating: {
		type: 'boolean',
		default: true,
	},

	/**
	 * Show the product content.
	 */
	showReviewContent: {
		type: 'boolean',
		default: true,
	},

	previewReviews: {
		type: 'array',
		default: null,
	},
};
