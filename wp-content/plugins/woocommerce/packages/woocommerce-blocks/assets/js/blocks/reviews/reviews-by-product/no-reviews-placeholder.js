/**
 * External dependencies
 */
import { __, sprintf } from '@wordpress/i18n';
import { Placeholder, Spinner } from '@wordpress/components';
import PropTypes from 'prop-types';
import ErrorPlaceholder from '@woocommerce/editor-components/error-placeholder';
import { Icon, commentContent } from '@wordpress/icons';
import { withProduct } from '@woocommerce/block-hocs';
import { decodeEntities } from '@wordpress/html-entities';

const NoReviewsPlaceholder = ( { error, getProduct, isLoading, product } ) => {
	const renderApiError = () => (
		<ErrorPlaceholder
			className="wc-block-featured-product-error"
			error={ error }
			isLoading={ isLoading }
			onRetry={ getProduct }
		/>
	);

	if ( error ) {
		return renderApiError();
	}

	const content =
		! product || isLoading ? (
			<Spinner />
		) : (
			sprintf(
				/* translators: %s is the product name. */
				__(
					"This block lists reviews for a selected product. %s doesn't have any reviews yet, but they will show up here when it does.",
					'woocommerce'
				),
				decodeEntities( product.name )
			)
		);

	return (
		<Placeholder
			className="wc-block-reviews-by-product"
			icon={
				<Icon
					icon={ commentContent }
					className="block-editor-block-icon"
				/>
			}
			label={ __( 'Reviews by Product', 'woocommerce' ) }
		>
			{ content }
		</Placeholder>
	);
};

NoReviewsPlaceholder.propTypes = {
	// from withProduct
	error: PropTypes.object,
	isLoading: PropTypes.bool,
	product: PropTypes.shape( {
		name: PropTypes.node,
		review_count: PropTypes.number,
	} ),
};

export default withProduct( NoReviewsPlaceholder );
