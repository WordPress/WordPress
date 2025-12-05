<?php
// phpcs:disable Yoast.NamingConventions.NamespaceName.TooLong -- Needed in the folder structure.
namespace Yoast\WP\SEO\Dashboard\Domain\Data_Provider;

/**
 * Object representation of the request parameters.
 */
abstract class Parameters {

	/**
	 * The start date.
	 *
	 * @var string
	 */
	private $start_date;

	/**
	 * The end date.
	 *
	 * @var string
	 */
	private $end_date;

	/**
	 * The amount of results.
	 *
	 * @var int
	 */
	private $limit = 0;

	/**
	 * The compare start date.
	 *
	 * @var string
	 */
	private $compare_start_date;

	/**
	 * The compare end date.
	 *
	 * @var string
	 */
	private $compare_end_date;

	/**
	 * Getter for the start date.
	 *
	 * @return string
	 */
	public function get_start_date(): string {
		return $this->start_date;
	}

	/**
	 * Getter for the end date.
	 * The date format should be Y-M-D.
	 *
	 * @return string
	 */
	public function get_end_date(): string {
		return $this->end_date;
	}

	/**
	 * Getter for the result limit.
	 *
	 * @return int
	 */
	public function get_limit(): int {
		return $this->limit;
	}

	/**
	 * Getter for the compare start date.
	 *
	 * @return string
	 */
	public function get_compare_start_date(): ?string {
		return $this->compare_start_date;
	}

	/**
	 * Getter for the compare end date.
	 * The date format should be Y-M-D.
	 *
	 * @return string
	 */
	public function get_compare_end_date(): ?string {
		return $this->compare_end_date;
	}

	/**
	 * The start date setter.
	 *
	 * @param string $start_date The start date.
	 *
	 * @return void
	 */
	public function set_start_date( string $start_date ): void {
		$this->start_date = $start_date;
	}

	/**
	 * The end date setter.
	 *
	 * @param string $end_date The end date.
	 *
	 * @return void
	 */
	public function set_end_date( string $end_date ): void {
		$this->end_date = $end_date;
	}

	/**
	 * The result limit.
	 *
	 * @param int $limit The result limit.
	 * @return void
	 */
	public function set_limit( int $limit ): void {
		$this->limit = $limit;
	}

	/**
	 * The compare start date setter.
	 *
	 * @param string $compare_start_date The compare start date.
	 *
	 * @return void
	 */
	public function set_compare_start_date( string $compare_start_date ): void {
		$this->compare_start_date = $compare_start_date;
	}

	/**
	 * The compare end date setter.
	 *
	 * @param string $compare_end_date The compare end date.
	 *
	 * @return void
	 */
	public function set_compare_end_date( string $compare_end_date ): void {
		$this->compare_end_date = $compare_end_date;
	}
}
