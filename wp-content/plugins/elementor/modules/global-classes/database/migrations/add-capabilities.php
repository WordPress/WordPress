<?php

namespace Elementor\Modules\GlobalClasses\Database\Migrations;

use Elementor\Core\Database\Base_Migration;

class Add_Capabilities extends Base_Migration {
	const UPDATE_CLASS = 'elementor_global_classes_update_class';
	const REMOVE_CSS_CLASS = 'elementor_global_classes_remove_class';
	const APPLY_CSS_CLASS = 'elementor_global_classes_apply_class';

	public function up() {
		$capabilities = [
			self::UPDATE_CLASS              => [ 'administrator' ],
			self::REMOVE_CSS_CLASS          => [ 'administrator', 'editor', 'author', 'contributor', 'shop_manager' ],
			self::APPLY_CSS_CLASS           => [ 'administrator', 'editor', 'author', 'contributor', 'shop_manager' ],
		];

		foreach ( $capabilities as $capability => $roles ) {
			foreach ( $roles as $role_name ) {
				$role = get_role( $role_name );

				if ( $role ) {
					$role->add_cap( $capability );
				}
			}
		}
	}
}
