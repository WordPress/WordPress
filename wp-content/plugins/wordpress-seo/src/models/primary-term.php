<?php

namespace Yoast\WP\SEO\Models;

use Yoast\WP\Lib\Model;

/**
 * Primary Term model definition.
 *
 * @property int    $id       Identifier.
 * @property int    $post_id  Post ID.
 * @property int    $term_id  Term ID.
 * @property string $taxonomy Taxonomy.
 * @property int    $blog_id  Blog ID.
 *
 * @property string $created_at
 * @property string $updated_at
 */
class Primary_Term extends Model {

	/**
	 * Whether nor this model uses timestamps.
	 *
	 * @var bool
	 */
	protected $uses_timestamps = true;

	/**
	 * Which columns contain int values.
	 *
	 * @var array
	 */
	protected $int_columns = [
		'id',
		'post_id',
		'term_id',
		'blog_id',
	];
}
