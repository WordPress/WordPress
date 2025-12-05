<?php
// phpcs:disable Yoast.NamingConventions.NamespaceName.TooLong -- Needed in the folder structure.
namespace Yoast\WP\SEO\Dashboard\Domain\Traffic;

use Yoast\WP\SEO\Dashboard\Domain\Data_Provider\Data_Interface;

/**
 * Domain object that represents a single Traffic record.
 */
class Traffic_Data implements Data_Interface {

	/**
	 * The sessions, if any.
	 *
	 * @var int|null
	 */
	private $sessions;

	/**
	 * The total users, if any.
	 *
	 * @var int|null
	 */
	private $total_users;

	/**
	 * The array representation of this domain object.
	 *
	 * @return array<string, int>
	 */
	public function to_array(): array {
		$result = [];

		if ( $this->sessions !== null ) {
			$result['sessions'] = $this->sessions;
		}

		if ( $this->total_users !== null ) {
			$result['total_users'] = $this->total_users;
		}

		return $result;
	}

	/**
	 * Sets the sessions.
	 *
	 * @param int $sessions The sessions.
	 *
	 * @return void
	 */
	public function set_sessions( int $sessions ): void {
		$this->sessions = $sessions;
	}

	/**
	 * Sets the total users.
	 *
	 * @param int $total_users The total users.
	 *
	 * @return void
	 */
	public function set_total_users( int $total_users ): void {
		$this->total_users = $total_users;
	}
}
