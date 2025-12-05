<?php

namespace Yoast\WP\SEO\Config;

/**
 * Migration_Status class.
 *
 * Used to validate whether or not migrations have been run and whether or not they should be run again.
 */
class Migration_Status {

	/**
	 * The migration option key.
	 *
	 * @var string
	 */
	public const MIGRATION_OPTION_KEY = 'yoast_migrations_';

	/**
	 * The migration options.
	 *
	 * @var array
	 */
	protected $migration_options = [];

	/**
	 * Checks if a given migration should be run.
	 *
	 * @param string $name    The name of the migration.
	 * @param string $version The current version.
	 *
	 * @return bool Whether or not the migration should be run.
	 */
	public function should_run_migration( $name, $version = \WPSEO_VERSION ) {
		$migration_status = $this->get_migration_status( $name );

		// Check if we've attempted to run this migration in the past 10 minutes. If so, it may still be running.
		if ( \array_key_exists( 'lock', $migration_status ) ) {
			$timestamp = \strtotime( '-10 minutes' );

			return $timestamp > $migration_status['lock'];
		}

		// Is the migration version less than the current version.
		return \version_compare( $migration_status['version'], $version, '<' );
	}

	/**
	 * Checks whether or not the given migration is at least the given version, defaults to checking for the latest version.
	 *
	 * @param string $name    The name of the migration.
	 * @param string $version The version to check, defaults to the latest version.
	 *
	 * @return bool Whether or not the requested migration is at least the requested version.
	 */
	public function is_version( $name, $version = \WPSEO_VERSION ) {
		$migration_status = $this->get_migration_status( $name );

		return \version_compare( $version, $migration_status['version'], '<=' );
	}

	/**
	 * Gets the error of a given migration if it exists.
	 *
	 * @param string $name The name of the migration.
	 *
	 * @return bool|array False if there is no error, otherwise the error.
	 */
	public function get_error( $name ) {
		$migration_status = $this->get_migration_status( $name );

		if ( ! isset( $migration_status['error'] ) ) {
			return false;
		}

		return $migration_status['error'];
	}

	/**
	 * Sets an error for the migration.
	 *
	 * @param string $name    The name of the migration.
	 * @param string $message Message explaining the reason for the error.
	 * @param string $version The current version.
	 *
	 * @return void
	 */
	public function set_error( $name, $message, $version = \WPSEO_VERSION ) {
		$migration_status = $this->get_migration_status( $name );

		$migration_status['error'] = [
			'time'    => \strtotime( 'now' ),
			'version' => $version,
			'message' => $message,
		];

		$this->set_migration_status( $name, $migration_status );
	}

	/**
	 * Updates the migration version to the latest version.
	 *
	 * @param string $name    The name of the migration.
	 * @param string $version The current version.
	 *
	 * @return void
	 */
	public function set_success( $name, $version = \WPSEO_VERSION ) {
		$migration_status = $this->get_migration_status( $name );
		unset( $migration_status['lock'] );
		unset( $migration_status['error'] );
		$migration_status['version'] = $version;
		$this->set_migration_status( $name, $migration_status );
	}

	/**
	 * Locks the migration status.
	 *
	 * @param string $name The name of the migration.
	 *
	 * @return bool Whether or not the migration was succesfully locked.
	 */
	public function lock_migration( $name ) {
		$migration_status         = $this->get_migration_status( $name );
		$migration_status['lock'] = \strtotime( 'now' );

		return $this->set_migration_status( $name, $migration_status );
	}

	/**
	 * Retrieves the migration option.
	 *
	 * @param string $name The name of the migration.
	 *
	 * @return bool|array The status of the migration, false if no status exists.
	 */
	protected function get_migration_status( $name ) {
		$current_blog_id = \get_current_blog_id();
		if ( ! isset( $this->migration_options[ $current_blog_id ][ $name ] ) ) {
			$migration_status = \get_option( self::MIGRATION_OPTION_KEY . $name );

			if ( ! \is_array( $migration_status ) || ! isset( $migration_status['version'] ) ) {
				$migration_status = [ 'version' => '0.0' ];
			}

			if ( ! isset( $this->migration_options[ $current_blog_id ] ) ) {
				$this->migration_options[ $current_blog_id ] = [];
			}
			$this->migration_options[ $current_blog_id ][ $name ] = $migration_status;
		}

		return $this->migration_options[ $current_blog_id ][ $name ];
	}

	/**
	 * Retrieves the migration option.
	 *
	 * @param string $name             The name of the migration.
	 * @param array  $migration_status The migration status.
	 *
	 * @return bool True if the status was succesfully updated, false otherwise.
	 */
	protected function set_migration_status( $name, $migration_status ) {
		if ( ! \is_array( $migration_status ) || ! isset( $migration_status['version'] ) ) {
			return false;
		}
		$current_blog_id = \get_current_blog_id();

		if ( ! isset( $this->migration_options[ $current_blog_id ] ) ) {
			$this->migration_options[ $current_blog_id ] = [];
		}
		$this->migration_options[ $current_blog_id ][ $name ] = $migration_status;

		return \update_option( self::MIGRATION_OPTION_KEY . $name, $migration_status );
	}
}
