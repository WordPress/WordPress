/**
 * External dependencies
 */
import classNames from 'classnames';
import { __, sprintf } from '@wordpress/i18n';

/**
 * Internal dependencies
 */

const Rating = ( {
	className,
	rating,
	ratedProductsCount,
}: RatingProps ): JSX.Element => {
	const ratingClassName = classNames(
		'wc-block-components-product-rating',
		className
	);

	const starStyle = {
		width: ( rating / 5 ) * 100 + '%',
	};

	const ratingText = sprintf(
		/* translators: %f is referring to the average rating value */
		__( 'Rated %f out of 5', 'woo-gutenberg-products-block' ),
		rating
	);

	const ratingHTML = {
		__html: sprintf(
			/* translators: %s is the rating value wrapped in HTML strong tags. */
			__( 'Rated %s out of 5', 'woo-gutenberg-products-block' ),
			sprintf( '<strong class="rating">%f</strong>', rating )
		),
	};

	return (
		<div className={ ratingClassName }>
			<div
				className={ 'wc-block-components-product-rating__stars' }
				role="img"
				aria-label={ ratingText }
			>
				<span
					style={ starStyle }
					dangerouslySetInnerHTML={ ratingHTML }
				/>
			</div>
			{ ratedProductsCount !== null ? (
				<span className={ 'wc-block-components-product-rating-count' }>
					({ ratedProductsCount })
				</span>
			) : null }
		</div>
	);
};

export type RatingValues = 0 | 1 | 2 | 3 | 4 | 5;
interface RatingProps {
	className?: string;
	rating: RatingValues;
	ratedProductsCount?: number | null;
}

export default Rating;
