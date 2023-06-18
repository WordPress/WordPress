/**
 * External dependencies
 */
import { __, _n, sprintf } from '@wordpress/i18n';
import classnames from 'classnames';
import {
	useInnerBlockLayoutContext,
	useProductDataContext,
} from '@woocommerce/shared-context';
import { useStyleProps } from '@woocommerce/base-hooks';
import { withProductDataContext } from '@woocommerce/shared-hocs';
import { isNumber, ProductResponseItem } from '@woocommerce/types';

/**
 * Internal dependencies
 */
import './style.scss';

type RatingProps = {
	reviews: number;
	rating: number;
	parentClassName?: string;
};

type AddReviewProps = {
	href?: string;
};

const getAverageRating = (
	product: Omit< ProductResponseItem, 'average_rating' > & {
		average_rating: string;
	}
) => {
	const rating = parseFloat( product.average_rating );

	return Number.isFinite( rating ) && rating > 0 ? rating : 0;
};

const getReviewsHref = ( product: ProductResponseItem ) => {
	const { permalink } = product;
	return `${ permalink }#reviews`;
};

const getRatingCount = ( product: ProductResponseItem ) => {
	const count = isNumber( product.review_count )
		? product.review_count
		: parseInt( product.review_count, 10 );

	return Number.isFinite( count ) && count > 0 ? count : 0;
};

const Rating = ( props: RatingProps ): JSX.Element => {
	const { rating, reviews, parentClassName } = props;

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
			/* translators: %1$s is referring to the average rating value, %2$s is referring to the number of ratings */
			_n(
				'Rated %1$s out of 5 based on %2$s customer rating',
				'Rated %1$s out of 5 based on %2$s customer ratings',
				reviews,
				'woo-gutenberg-products-block'
			),
			sprintf( '<strong class="rating">%f</strong>', rating ),
			sprintf( '<span class="rating">%d</span>', reviews )
		),
	};
	return (
		<div
			className={ classnames(
				'wc-block-components-product-rating__stars',
				`${ parentClassName }__product-rating__stars`
			) }
			role="img"
			aria-label={ ratingText }
		>
			<span style={ starStyle } dangerouslySetInnerHTML={ ratingHTML } />
		</div>
	);
};

const AddReview = ( props: AddReviewProps ): JSX.Element | null => {
	const { href } = props;
	const label = __( 'Add review', 'woo-gutenberg-products-block' );

	return href ? (
		<a className="wc-block-components-product-rating__link" href={ href }>
			{ label }
		</a>
	) : null;
};

const ReviewsCount = ( props: { reviews: number } ): JSX.Element => {
	const { reviews } = props;

	const reviewsCount = sprintf(
		/* translators: %s is referring to the total of reviews for a product */
		_n(
			'(%s customer review)',
			'(%s customer reviews)',
			reviews,
			'woo-gutenberg-products-block'
		),
		reviews
	);

	return (
		<span className="wc-block-components-product-rating__reviews_count">
			{ reviewsCount }
		</span>
	);
};

interface ProductRatingProps {
	className?: string;
	textAlign?: string;
	isDescendentOfSingleProductBlock: boolean;
	isDescendentOfQueryLoop: boolean;
	postId: number;
	productId: number;
}

export const Block = ( props: ProductRatingProps ): JSX.Element | null => {
	const { textAlign, isDescendentOfSingleProductBlock } = props;
	const styleProps = useStyleProps( props );
	const { parentClassName } = useInnerBlockLayoutContext();
	const { product } = useProductDataContext();
	const rating = getAverageRating( product );
	const reviews = getRatingCount( product );
	const href = getReviewsHref( product );

	const className = classnames(
		styleProps.className,
		'wc-block-components-product-rating',
		{
			[ `${ parentClassName }__product-rating` ]: parentClassName,
			[ `has-text-align-${ textAlign }` ]: textAlign,
		}
	);

	const content = reviews ? (
		<Rating
			rating={ rating }
			reviews={ reviews }
			parentClassName={ parentClassName }
		/>
	) : (
		<AddReview href={ href } />
	);

	return (
		<div className={ className } style={ styleProps.style }>
			<div className="wc-block-components-product-rating__container">
				{ content }
				{ reviews && isDescendentOfSingleProductBlock ? (
					<ReviewsCount reviews={ reviews } />
				) : null }
			</div>
		</div>
	);
};

export default withProductDataContext( Block );
