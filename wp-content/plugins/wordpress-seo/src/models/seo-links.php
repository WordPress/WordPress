<?php

namespace Yoast\WP\SEO\Models;

use Yoast\WP\Lib\Model;

/**
 * Table definition for the SEO Links table.
 *
 * @property int    $id
 * @property string $url
 * @property int    $post_id
 * @property int    $target_post_id
 * @property string $type
 * @property int    $indexable_id
 * @property int    $target_indexable_id
 * @property int    $height
 * @property int    $width
 * @property int    $size
 * @property string $language
 * @property string $region
 */
class SEO_Links extends Model {

	/**
	 * Indicates that the link is external.
	 *
	 * @var string
	 */
	public const TYPE_INTERNAL = 'internal';

	/**
	 * Indicates that the link is internal.
	 *
	 * @var string
	 */
	public const TYPE_EXTERNAL = 'external';

	/**
	 * Indicates the link is an internal image.
	 *
	 * @var string
	 */
	public const TYPE_INTERNAL_IMAGE = 'image-in';

	/**
	 * Indicates the link is an external image.
	 *
	 * @var string
	 */
	public const TYPE_EXTERNAL_IMAGE = 'image-ex';

	/**
	 * Holds the parsed URL. May not be set.
	 *
	 * @var array
	 */
	public $parsed_url;

	/**
	 * Which columns contain int values.
	 *
	 * @var array
	 */
	protected $int_columns = [
		'id',
		'post_id',
		'target_post_id',
		'indexable_id',
		'target_indexable_id',
		'height',
		'width',
		'size',
	];
}
