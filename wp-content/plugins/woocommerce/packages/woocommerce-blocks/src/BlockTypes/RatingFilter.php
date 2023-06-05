<?php
namespace Automattic\WooCommerce\Blocks\BlockTypes;

/**
 * PriceFilter class.
 */
class RatingFilter extends AbstractBlock {

	/**
	 * Block name.
	 *
	 * @var string
	 */
	protected $block_name  = 'rating-filter';
	const RATING_QUERY_VAR = 'rating_filter';

}
