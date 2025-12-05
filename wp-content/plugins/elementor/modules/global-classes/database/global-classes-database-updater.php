<?php

namespace Elementor\Modules\GlobalClasses\Database;

use Elementor\Core\Database\Base_Database_Updater;
use Elementor\Modules\GlobalClasses\Database\Migrations\Add_Capabilities;

class Global_Classes_Database_Updater extends Base_Database_Updater {
	const DB_VERSION = 1;
	const OPTION_NAME = 'elementor_global_classes_db_version';

	protected function get_migrations(): array {
		return [
			1 => new Add_Capabilities(),
		];
	}

	protected function get_db_version() {
		return static::DB_VERSION;
	}

	protected function get_db_version_option_name(): string {
		return static::OPTION_NAME;
	}
}
