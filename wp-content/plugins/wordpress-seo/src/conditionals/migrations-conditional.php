<?php

namespace Yoast\WP\SEO\Conditionals;

use Yoast\WP\SEO\Config\Migration_Status;

/**
 * Class for integrations that depend on having all migrations run.
 */
class Migrations_Conditional implements Conditional {

	/**
	 * The migration status.
	 *
	 * @var Migration_Status
	 */
	protected $migration_status;

	/**
	 * Migrations_Conditional constructor.
	 *
	 * @param Migration_Status $migration_status The migration status object.
	 */
	public function __construct( Migration_Status $migration_status ) {
		$this->migration_status = $migration_status;
	}

	/**
	 * Returns `true` when all database migrations have been run.
	 *
	 * @return bool `true` when all database migrations have been run.
	 */
	public function is_met() {
		return $this->migration_status->is_version( 'free', \WPSEO_VERSION );
	}
}
