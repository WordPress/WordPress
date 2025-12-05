<?php

namespace Yoast\WP\SEO\Models;

use Yoast\WP\Lib\Model;

/**
 * Abstract class for indexable extensions.
 */
abstract class Indexable_Extension extends Model {

	/**
	 * Holds the Indexable instance.
	 *
	 * @var Indexable|null
	 */
	protected $indexable = null;

	/**
	 * Returns the indexable this extension belongs to.
	 *
	 * @return Indexable The indexable.
	 */
	public function indexable() {
		if ( $this->indexable === null ) {
			$this->indexable = $this->belongs_to( 'Indexable', 'indexable_id', 'id' )->find_one();
		}

		return $this->indexable;
	}
}
