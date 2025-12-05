<?php

namespace Elementor\Modules\AtomicWidgets\Database\Migrations;

use Elementor\Core\Database\Base_Migration;

class Add_Capabilities extends Base_Migration {
	const ACCESS_STYLES_TAB = 'elementor_atomic_widgets_access_styles_tab';
	const EDIT_LOCAL_CSS_CLASS = 'elementor_atomic_widgets_edit_local_css_class';

	public function up() {
		$capabilities = [
			self::ACCESS_STYLES_TAB    => [ 'administrator', 'editor', 'author', 'contributor', 'shop_manager' ],
			self::EDIT_LOCAL_CSS_CLASS => [ 'administrator', 'editor', 'author', 'contributor', 'shop_manager' ],
		];

		foreach ( $capabilities as $cap => $roles ) {
			foreach ( $roles as $role_name ) {
				$role = get_role( $role_name );

				if ( $role ) {
					$role->add_cap( $cap );
				}
			}
		}
	}
}
