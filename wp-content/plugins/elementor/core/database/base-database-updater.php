<?php
namespace Elementor\Core\Database;

use Elementor\Core\Utils\Collection;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

abstract class Base_Database_Updater {
	public function up( $force = false ) {
		$installed_version = $this->get_installed_version();

		if ( ! $force && $this->get_db_version() <= $installed_version ) {
			return;
		}

		$migrations = new Collection( $this->get_migrations() );

		if ( ! $force ) {
			$migrations = $migrations->filter( function ( $_, $version ) use ( $installed_version ) {
				return $version > $installed_version;
			} );
		}

		$migrations->map( function ( Base_Migration $migration, $version ) {
			$migration->up();

			$this->update_db_version_option( $version );
		} );

		$this->update_db_version_option( $this->get_db_version() );
	}

	public function register() {
		add_action( 'admin_init', function () {
			$this->up();
		} );
	}

	protected function update_db_version_option( $version ) {
		update_option( $this->get_db_version_option_name(), $version );
	}

	protected function get_installed_version() {
		return intval( get_option( $this->get_db_version_option_name() ) );
	}

	abstract protected function get_db_version();

	abstract protected function get_db_version_option_name(): string;

	abstract protected function get_migrations(): array;
}
