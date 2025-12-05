<?php

namespace Yoast\WP\SEO\Actions\Indexing;

use Yoast\WP\SEO\Helpers\Indexing_Helper;

/**
 * Indexing action to call when the indexing is completed.
 */
class Indexing_Complete_Action {

	/**
	 * The indexing helper.
	 *
	 * @var Indexing_Helper
	 */
	protected $indexing_helper;

	/**
	 * Indexing_Complete_Action constructor.
	 *
	 * @param Indexing_Helper $indexing_helper The indexing helper.
	 */
	public function __construct( Indexing_Helper $indexing_helper ) {
		$this->indexing_helper = $indexing_helper;
	}

	/**
	 * Wraps up the indexing process.
	 *
	 * @return void
	 */
	public function complete() {
		$this->indexing_helper->complete();
	}
}
