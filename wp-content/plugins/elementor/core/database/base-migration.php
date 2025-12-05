<?php

namespace Elementor\Core\Database;

abstract class Base_Migration {
	/**
	 * Runs when upgrading the database
	 *
	 * @return void
	 */
	abstract public function up();
}
