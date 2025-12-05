<?php

namespace Yoast\WP\SEO\Models;

use Yoast\WP\Lib\Model;

/**
 * Indexable Hierarchy model definition.
 *
 * @property int $indexable_id The ID of the indexable.
 * @property int $ancestor_id  The ID of the indexable's ancestor.
 * @property int $depth        The depth of the ancestry. 1 being a parent, 2 being a grandparent etc.
 * @property int $blog_id      Blog ID.
 */
class Indexable_Hierarchy extends Model {

	/**
	 * Which columns contain int values.
	 *
	 * @var array
	 */
	protected $int_columns = [
		'indexable_id',
		'ancestor_id',
		'depth',
		'blog_id',
	];
}
