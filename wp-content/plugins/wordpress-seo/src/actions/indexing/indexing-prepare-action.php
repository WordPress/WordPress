<?php

namespace Yoast\WP\SEO\Actions\Indexing;

use Yoast\WP\SEO\Helpers\Indexing_Helper;

/**
 * Class Indexing_Prepare_Action.
 *
 * Action for preparing the indexing routine.
 */
class Indexing_Prepare_Action {

	/**
	 * The indexing helper.
	 *
	 * @var Indexing_Helper
	 */
	protected $indexing_helper;

	/**
	 * Action for preparing the indexing routine.
	 *
	 * @param Indexing_Helper $indexing_helper The indexing helper.
	 */
	public function __construct( Indexing_Helper $indexing_helper ) {
		$this->indexing_helper = $indexing_helper;
	}

	/**
	 * Prepares the indexing routine.
	 *
	 * @return void
	 */
	public function prepare() {
		$this->indexing_helper->prepare();
	}
}
