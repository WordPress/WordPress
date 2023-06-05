/**
 * External dependencies
 */
import { __ } from '@wordpress/i18n';
import PropTypes from 'prop-types';
import { debounce } from 'lodash';
import { Placeholder } from '@wordpress/components';
import { useBlockProps } from '@wordpress/block-editor';

/**
 * Internal dependencies
 */
import EditorBlock from './editor-block.js';
import { getBlockClassName, getSortArgs } from './utils.js';

const EditorContainerBlock = ( {
	attributes,
	icon,
	name,
	noReviewsPlaceholder,
} ) => {
	const {
		categoryIds,
		productId,
		reviewsOnPageLoad,
		showProductName,
		showReviewDate,
		showReviewerName,
		showReviewContent,
		showReviewImage,
		showReviewRating,
	} = attributes;
	const { order, orderby } = getSortArgs( attributes.orderby );
	const isAllContentHidden =
		! showReviewContent &&
		! showReviewRating &&
		! showReviewDate &&
		! showReviewerName &&
		! showReviewImage &&
		! showProductName;

	const blockProps = useBlockProps( {
		className: getBlockClassName( attributes ),
	} );

	if ( isAllContentHidden ) {
		return (
			<Placeholder icon={ icon } label={ name }>
				{ __(
					'The content for this block is hidden due to block settings.',
					'woocommerce'
				) }
			</Placeholder>
		);
	}

	return (
		<div { ...blockProps }>
			<EditorBlock
				attributes={ attributes }
				categoryIds={ categoryIds }
				delayFunction={ ( callback ) => debounce( callback, 400 ) }
				noReviewsPlaceholder={ noReviewsPlaceholder }
				orderby={ orderby }
				order={ order }
				productId={ productId }
				reviewsToDisplay={ reviewsOnPageLoad }
			/>
		</div>
	);
};

EditorContainerBlock.propTypes = {
	attributes: PropTypes.object.isRequired,
	icon: PropTypes.node.isRequired,
	name: PropTypes.string.isRequired,
	noReviewsPlaceholder: PropTypes.element.isRequired,
	className: PropTypes.string,
};

export default EditorContainerBlock;
