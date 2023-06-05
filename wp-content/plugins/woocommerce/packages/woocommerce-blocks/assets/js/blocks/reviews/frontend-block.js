/**
 * External dependencies
 */
import { __ } from '@wordpress/i18n';
import PropTypes from 'prop-types';
import { getSetting } from '@woocommerce/settings';
import LoadMoreButton from '@woocommerce/base-components/load-more-button';
import {
	ReviewList,
	ReviewSortSelect,
} from '@woocommerce/base-components/reviews';
import withReviews from '@woocommerce/base-hocs/with-reviews';

/**
 * Block rendered in the frontend.
 *
 * @param {Object}                                             props                 Incoming props for the component.
 * @param {Object}                                             props.attributes      Incoming block attributes.
 * @param {function(any):any}                                  props.onAppendReviews Function called when appending review.
 * @param {function(any):any}                                  props.onChangeOrderby
 * @param {Array}                                              props.reviews
 * @param {'most-recent' | 'highest-rating' | 'lowest-rating'} props.sortSelectValue
 * @param {number}                                             props.totalReviews
 */
const FrontendBlock = ( {
	attributes,
	onAppendReviews,
	onChangeOrderby,
	reviews,
	sortSelectValue,
	totalReviews,
} ) => {
	if ( reviews.length === 0 ) {
		return null;
	}

	const reviewRatingsEnabled = getSetting( 'reviewRatingsEnabled', true );

	return (
		<>
			{ attributes.showOrderby !== 'false' && reviewRatingsEnabled && (
				<ReviewSortSelect
					value={ sortSelectValue }
					onChange={ onChangeOrderby }
					readOnly
				/>
			) }
			<ReviewList attributes={ attributes } reviews={ reviews } />
			{ attributes.showLoadMore !== 'false' &&
				totalReviews > reviews.length && (
					<LoadMoreButton
						onClick={ onAppendReviews }
						screenReaderLabel={ __(
							'Load more reviews',
							'woocommerce'
						) }
					/>
				) }
		</>
	);
};

FrontendBlock.propTypes = {
	/**
	 * The attributes for this block.
	 */
	attributes: PropTypes.object.isRequired,
	onAppendReviews: PropTypes.func,
	onChangeArgs: PropTypes.func,
	// from withReviewsattributes
	reviews: PropTypes.array,
	totalReviews: PropTypes.number,
};

export default withReviews( FrontendBlock );
