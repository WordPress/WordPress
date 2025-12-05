<?php

namespace Yoast\WP\SEO\Actions\Indexing;

use Yoast\WP\SEO\Helpers\Indexable_Helper;

/**
 * Indexing action to call when the indexable indexing process is completed.
 */
class Indexable_Indexing_Complete_Action {

	/**
	 * The options helper.
	 *
	 * @var Indexable_Helper
	 */
	protected $indexable_helper;

	/**
	 * Indexable_Indexing_Complete_Action constructor.
	 *
	 * @param Indexable_Helper $indexable_helper The indexable helper.
	 */
	public function __construct( Indexable_Helper $indexable_helper ) {
		$this->indexable_helper = $indexable_helper;
	}

	/**
	 * Wraps up the indexing process.
	 *
	 * @return void
	 */
	public function complete() {
		$this->indexable_helper->finish_indexing();
	}
}
