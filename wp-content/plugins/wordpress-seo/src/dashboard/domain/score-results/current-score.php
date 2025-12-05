<?php
// phpcs:disable Yoast.NamingConventions.NamespaceName.TooLong -- Needed in the folder structure.
namespace Yoast\WP\SEO\Dashboard\Domain\Score_Results;

/**
 * This class describes a current score.
 */
class Current_Score {

	/**
	 * The name of the current score.
	 *
	 * @var string
	 */
	private $name;

	/**
	 * The amount of the current score.
	 *
	 * @var string
	 */
	private $amount;

	/**
	 * The ids of the current score.
	 *
	 * @var string|null
	 */
	private $ids;

	/**
	 * The links of the current score.
	 *
	 * @var array<string, string>|null
	 */
	private $links;

	/**
	 * The constructor.
	 *
	 * @param string                     $name   The name of the current score.
	 * @param int                        $amount The amount of the current score.
	 * @param string|null                $ids    The ids of the current score.
	 * @param array<string, string>|null $links  The links of the current score.
	 */
	public function __construct( string $name, int $amount, ?string $ids = null, ?array $links = null ) {
		$this->name   = $name;
		$this->amount = $amount;
		$this->ids    = $ids;
		$this->links  = $links;
	}

	/**
	 * Gets name of the current score.
	 *
	 * @return string The name of the current score.
	 */
	public function get_name(): string {
		return $this->name;
	}

	/**
	 * Gets the amount of the current score.
	 *
	 * @return int The amount of the current score.
	 */
	public function get_amount(): int {
		return $this->amount;
	}

	/**
	 * Gets the ids of the current score.
	 *
	 * @return string|null The ids of the current score.
	 */
	public function get_ids(): ?string {
		return $this->ids;
	}

	/**
	 * Gets the links of the current score in the expected key value representation.
	 *
	 * @return array<string, string> The links of the current score in the expected key value representation.
	 */
	public function get_links_to_array(): ?array {
		$links = [];

		if ( $this->links === null ) {
			return $links;
		}

		foreach ( $this->links as $key => $link ) {
			if ( $link === null ) {
				continue;
			}
			$links[ $key ] = $link;
		}
		return $links;
	}
}
