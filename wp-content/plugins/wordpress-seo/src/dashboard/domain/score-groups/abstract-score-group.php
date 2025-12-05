<?php
// phpcs:disable Yoast.NamingConventions.NamespaceName.TooLong -- Needed in the folder structure.
namespace Yoast\WP\SEO\Dashboard\Domain\Score_Groups;

/**
 * Abstract class for a score group.
 */
abstract class Abstract_Score_Group implements Score_Groups_Interface {

	/**
	 * The name of the score group.
	 *
	 * @var string
	 */
	private $name;

	/**
	 * The key of the score group that is used when filtering on the posts page.
	 *
	 * @var string
	 */
	private $filter_key;

	/**
	 * The value of the score group that is used when filtering on the posts page.
	 *
	 * @var string
	 */
	private $filter_value;

	/**
	 * The min score of the score group.
	 *
	 * @var int
	 */
	private $min_score;

	/**
	 * The max score of the score group.
	 *
	 * @var int
	 */
	private $max_score;

	/**
	 * The position of the score group.
	 *
	 * @var int
	 */
	private $position;
}
