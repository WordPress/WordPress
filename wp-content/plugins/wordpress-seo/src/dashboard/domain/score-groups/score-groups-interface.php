<?php
// phpcs:disable Yoast.NamingConventions.NamespaceName.TooLong -- Needed in the folder structure.
namespace Yoast\WP\SEO\Dashboard\Domain\Score_Groups;

/**
 * This interface describes a score group implementation.
 */
interface Score_Groups_Interface {

	/**
	 * Gets the name of the score group.
	 *
	 * @return string
	 */
	public function get_name(): string;

	/**
	 * Gets the key of the score group that is used when filtering on the posts page.
	 *
	 * @return string
	 */
	public function get_filter_key(): string;

	/**
	 * Gets the value of the score group that is used when filtering on the posts page.
	 *
	 * @return string
	 */
	public function get_filter_value(): string;

	/**
	 * Gets the minimum score of the score group.
	 *
	 * @return int|null
	 */
	public function get_min_score(): ?int;

	/**
	 * Gets the maximum score of the score group.
	 *
	 * @return int|null
	 */
	public function get_max_score(): ?int;

	/**
	 * Gets the position of the score group.
	 *
	 * @return int
	 */
	public function get_position(): int;
}
